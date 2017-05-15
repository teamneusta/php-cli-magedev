<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license MIT License
 */

namespace TeamNeusta\Magedev\Test\Docker\Image\Repository;

use \Mockery as m;
use Docker\Manager\ImageManager;
use TeamNeusta\Magedev\Docker\Image\AbstractImage;
use TeamNeusta\Magedev\Docker\Api\Image;
use TeamNeusta\Magedev\Docker\Image\Repository\Php7;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;

/**
 * Class: ImageTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ImageTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExists()
    {
        $images = [
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["redis:2.8"]
            ]),
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["magedev-varnish4:latest"]
            ]),
        ];
        $imageManager = m::mock(ImageManager::class);
        $imageManager->shouldReceive("findAll")->andReturn($images);
        $image = m::mock(AbstractImage::class);
        $image->shouldReceive("getBuildName")->andReturn("magedev-varnish4");
        $imageApi = new Image($imageManager, $image);
        self::assertTrue($imageApi->exists());
    }

    public function testNotExists()
    {
        $images = [
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["redis:2.8"]
            ]),
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["magedev-varnish4:latest"]
            ]),
        ];
        $imageManager = m::mock(ImageManager::class);
        $imageManager->shouldReceive("findAll")->andReturn($images);
        $image = m::mock(AbstractImage::class);
        $image->shouldReceive("getBuildName")->andReturn("magedev-main");
        $imageApi = new Image($imageManager, $image);
        self::assertFalse($imageApi->exists());
    }

    public function testBuild()
    {
        $images = [
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["redis:2.8"]
            ]),
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["magedev-varnish4:latest"]
            ]),
        ];
        $buildStream = m::mock("\Docker\Stream\BuildStram");
        $buildStream->shouldReceive("onFrame");
        $buildStream->shouldReceive("wait");

        $imageManager = m::mock(ImageManager::class);
        $imageManager->shouldReceive("findAll")->andReturn($images);
        $imageManager->shouldReceive("build")->with(m::any(), [
            't' => "magedev-php7",
            'rm' => true,
            'nocache' => false
        ], m::any())->andReturn($buildStream);

        $config = m::mock(Config::class);
        $config->shouldReceive("optionExists");
        $config->shouldReceive("get")->with("env_vars")->andReturn([]);

        $imageFactory = m::mock(ImageFactory::class);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add,run]");
        $contextBuilder->shouldReceive("run");
        $contextBuilder->shouldReceive("__destruct");

        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");

        $image = new Php7(
            $config,
            $imageFactory,
            $fileHelper,
            $contextBuilder,
            $imageApiFactory,
            $nameBuilder
        );
        $imageApi = new Image($imageManager, $image);
        $imageApi->build();
    }

    public function testPull()
    {
        $images = [
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["redis:2.8"]
            ]),
            m::mock("\Docker\API\Model\Image", [
                "getRepoTags" => ["magedev-varnish4:latest"]
            ]),
        ];
        $buildStream = m::mock("\Docker\Stream\BuildStram");
        $buildStream->shouldReceive("onFrame");
        $buildStream->shouldReceive("wait");

        $imageManager = m::mock(ImageManager::class);
        $imageManager->shouldReceive("findAll")->andReturn($images);
        $imageManager->shouldReceive("create")->with(m::any(), [
            'fromImage' => 'nginx:latest'
        ], m::any())->andReturn($buildStream);

        $image = m::mock(AbstractImage::class);
        $image->shouldReceive("getBuildName")->andReturn("nginx:latest");
        $imageApi = new Image($imageManager, $image);
        $imageApi->pull();
    }

}
