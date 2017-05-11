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

namespace TeamNeusta\Magedev\Test\Runtime;

use \Mockery as m;
use TeamNeusta\Magedev\Runtime\Config;

/**
 * Class: ConfigTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ConfigTest extends \TeamNeusta\Magedev\Test\TestCase
{
    public function testLoadFailsIfNoConfigInCwd()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $fileHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\FileHelper');
        $fileHelper->shouldReceive('findPath');
        $fileHelper->shouldReceive('fileExists');
        $config = new Config($input, $fileHelper);
        try {
            $config->getMagentoVersion();
            $this->fail("Excepted load of config would fail");
        } catch (\Exception $e) {
            $projectConfigFile = getcwd() . "/magedev.json";
            $this->assertEquals("it seems this is not a magento project I can handle: ".$projectConfigFile." file was not found",$e->getMessage());
        }
    }

    public function testShouldLoadMagentoVersionInConfig()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive('getArgument');
        $fileContent = json_encode(
            ['magento_version' => 2]
        );
        $fileHelper = m::mock(
            '\TeamNeusta\Magedev\Runtime\Helper\FileHelper',
            [
                "findPath" => "/home/user/path/magedev.json",
                "fileExists" => true,
                "read" => $fileContent
            ]
        )->makePartial();
        $config = new Config($input, $fileHelper);
        self::assertSame(2, $config->getMagentoVersion());
    }

    public function testOptionExists()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive('getArgument');
        $fileContent = json_encode(
            ['some_option' => 'abc']
        );
        $fileHelper = m::mock(
            '\TeamNeusta\Magedev\Runtime\Helper\FileHelper',
            [
                "findPath" => "/home/user/path/magedev.json",
                "fileExists" => true,
                "read" => $fileContent
            ]
        )->makePartial();
        $config = new Config($input, $fileHelper);
        self::assertTrue($config->optionExists('some_option'));
        self::assertFalse($config->optionExists('other_stuff'));
    }

    public function testWrongMagentoVersion()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive('getArgument');
        $fileContent = json_encode(
            ['magento_version' => 100]
        );
        $fileHelper = m::mock(
            '\TeamNeusta\Magedev\Runtime\Helper\FileHelper',
            [
                "findPath" => "/home/user/path/magedev.json",
                "fileExists" => true,
                "read" => $fileContent
            ]
        )->makePartial();
        $config = new Config($input, $fileHelper);
        try {
            $config->getMagentoVersion();
            $this->fail("Magento version 100 should fail here");
        } catch (\Exception $e) {
            $this->assertEquals("supplied magento version 100 not available",$e->getMessage());


        }
    }

}
