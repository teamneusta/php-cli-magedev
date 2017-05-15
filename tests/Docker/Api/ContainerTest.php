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
use Docker\Manager\ContainerManager;
use TeamNeusta\Magedev\Docker\Api\Container;

/**
 * Class: ContainerTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ContainerTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testExists()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php7"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with(['all' => true])
            ->andReturn($containers);

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        self::assertTrue($containerApi->exists());
    }

    public function testNotExists()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php5"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with(['all' => true])
            ->andReturn($containers);

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        self::assertFalse($containerApi->exists());
    }

    public function testIsRunning()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php7"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with()
            ->andReturn($containers);

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        self::assertTrue($containerApi->isRunning());
    }

    public function testNotIsRunning()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php5"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with()
            ->andReturn($containers);

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        self::assertFalse($containerApi->isRunning());
    }

    public function testStop()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php7"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with()
            ->andReturn($containers);
        $containerManager->shouldReceive("stop")->with("magedev-project-php7");

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        $containerApi->stop();
    }

    public function testStart()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php7"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with(m::any())
            ->andReturn($containers);
        $containerManager->shouldReceive("findAll")
            ->with()
            ->andReturn([]);
        $containerManager->shouldReceive("start")->with("magedev-project-php7");

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        $containerApi->start();
    }

    public function testBuild()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php5"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with(m::any())
            ->andReturn($containers);
        $containerManager->shouldReceive("findAll")
            ->with()
            ->andReturn([]);
        $containerManager->shouldReceive("create")->with(
            m::any(),
            ['name' => "magedev-project-php7"]
        );

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageFactory->shouldReceive("create")->with("ExternImage")
            ->andReturn(m::mock("\TeamNeusta\Magedev\Docker\Image\Repository\ExternImage")->makePartial());
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $imageApi = m::mock("\Magedev\TeamNeusta\Docker\Api\Image");
        $imageApi->shouldReceive("pull")->times(1);
        $imageApi->shouldReceive("exists")->andReturn(true);
        $imageApiFactory->shouldReceive("create")->andReturn($imageApi);

        $containerConfig = m::mock("\Docker\API\Model\ContainerConfig");
        $containerConfig->shouldReceive("setImage")->andReturn("magedev-project-php7");

        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $container->shouldReceive("getConfig")->andReturn($containerConfig);
        $container->shouldReceive("getImage")->andReturn("some-php7-image");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        $containerApi->build();
    }

    public function testDestroy()
    {
        $containers = [
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/magedev-project-php7"]
            ]),
            m::mock("\Docker\API\Model\Container", [
                "getNames" => ["/some-other-stuff"]
            ])
        ];
        $containerManager = m::mock(ContainerManager::class);
        $containerManager->shouldReceive("findAll")
            ->with(['all' => true])
            ->andReturn($containers);
        $containerManager->shouldReceive("findAll")
            ->andReturn([]);
        $containerManager->shouldReceive("remove")
            ->with("magedev-project-php7")
            ->times(1);

        $imageFactory = m::mock("\TeamNeusta\Magedev\Docker\Image\Factory");
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\AbstractContainer");
        $container->shouldReceive("getBuildName")->andReturn("magedev-project-php7");
        $containerApi = new Container($containerManager, $imageFactory, $imageApiFactory, $container);
        $containerApi->destroy();
    }
}
