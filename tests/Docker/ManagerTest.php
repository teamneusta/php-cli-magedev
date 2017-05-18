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

namespace TeamNeusta\Magedev\Test\Docker;

use Mockery as m;
use TeamNeusta\Magedev\Docker\Manager;
use TeamNeusta\Magedev\Docker\Api\ContainerFactory as ContainerApiFactory;
use TeamNeusta\Magedev\Docker\Api\Container as ContainerApi;
use TeamNeusta\Magedev\Docker\Api\ImageFactory as ImageApiFactory;
use TeamNeusta\Magedev\Docker\Api\Image as ImageApi;
use TeamNeusta\Magedev\Docker\Image\Factory as ImageFactory;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class: FactoryTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ManagerTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testStartContainer()
    {
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput');
        $output->shouldReceive('writeln')->with('<info>starting container magedev-project-main</info>')->times(1);

        $containerApi = m::mock(ContainerApi::class);
        $containerApi->shouldReceive('start')->times(1);
        $containerApiFactory = m::mock(ContainerApiFactory::class);
        $containerApiFactory->shouldReceive('create')->andReturn($containerApi);

        $imageApi = m::mock(ImageApi::class);
        $imageApiFactory = m::mock(ImageApiFactory::class);

        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\Repository\Main");
        $container->shouldReceive('getBuildName')->andReturn('magedev-project-main');
        $manager = new Manager($output, $containerApiFactory, $imageApiFactory);
        $manager->addContainer($container);
        $manager->startContainers();
    }

    public function testStopContainers()
    {
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput');

        $containerApi = m::mock(ContainerApi::class);
        $containerApi->shouldReceive('stop')->times(1);
        $containerApiFactory = m::mock(ContainerApiFactory::class);
        $containerApiFactory->shouldReceive('create')->andReturn($containerApi);

        $imageApi = m::mock(ImageApi::class);
        $imageApiFactory = m::mock(ImageApiFactory::class);

        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\Repository\Main");
        $manager = new Manager($output, $containerApiFactory, $imageApiFactory);
        $manager->addContainer($container);
        $manager->stopContainers();
    }

    public function testDestroyContainers()
    {
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput');

        $containerApi = m::mock(ContainerApi::class);
        $containerApi->shouldReceive('destroy')->times(1);
        $containerApiFactory = m::mock(ContainerApiFactory::class);
        $containerApiFactory->shouldReceive('create')->andReturn($containerApi);

        $imageApi = m::mock(ImageApi::class);
        $imageApiFactory = m::mock(ImageApiFactory::class);

        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\Repository\Main");
        $manager = new Manager($output, $containerApiFactory, $imageApiFactory);
        $manager->addContainer($container);
        $manager->destroyContainers();
    }

    public function testRebuildContainers()
    {
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput');

        $containerApi = m::mock(ContainerApi::class);
        $containerApi->shouldReceive('destroy')->times(1);
        $containerApi->shouldReceive('build')->times(1);
        $containerApiFactory = m::mock(ContainerApiFactory::class);
        $containerApiFactory->shouldReceive('create')->andReturn($containerApi);

        $imageApi = m::mock(ImageApi::class);
        $imageApi->shouldReceive('destroy')->times(1);
        $imageApiFactory = m::mock(ImageApiFactory::class);
        $imageApiFactory->shouldReceive('create')->andReturn($imageApi);

        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('env_vars')->andReturn([]);
        $fileHelper = m::mock(FileHelper::class);
        $contextBuilder = m::mock("Docker\Context\ContextBuilder[__destruct,add]");
        $contextBuilder->shouldReceive('__destruct')->andReturn(null);
        $imageApiFactory = m::mock("\TeamNeusta\Magedev\Docker\Api\ImageFactory");
        $imageApiFactory->shouldReceive('create')->andReturn($imageApi);

        $nameBuilder = m::mock("\TeamNeusta\Magedev\Docker\Helper\NameBuilder");
        $imageFactory = new ImageFactory($config, $fileHelper, $imageApiFactory, $nameBuilder);
        $image = $imageFactory->create('Php7');

        $container = m::mock("\TeamNeusta\Magedev\Docker\Container\Repository\Main");
        $container->shouldReceive('getImage')->andReturn($image);

        $manager = new Manager($output, $containerApiFactory, $imageApiFactory);
        $manager->addContainer($container);
        $manager->rebuildContainers();
    }
}
