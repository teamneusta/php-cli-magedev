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

use TeamNeusta\Magedev\Runtime\Runtime;

/**
 * Class: RuntimeTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    protected $runtime;

    public function setUp()
    {
        $output = $this->getMockForAbstractClass('Symfony\Component\Console\Output\OutputInterface');
        $input = $this->getMockForAbstractClass('Symfony\Component\Console\Input\InputInterface');
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'foo'
            ])
            ->getMock();
        $this->runtime = $this->getMockBuilder(Runtime::class)
            ->setConstructorArgs([$input, $output, $questionHelper])
            ->setMethods([
                'loadPlugins'
            ])
            ->getMock();
    }

    public function testGetOutput()
    {
        self::assertInstanceOf("\Symfony\Component\Console\Output\OutputInterface", $this->runtime->getOutput());
    }
}
