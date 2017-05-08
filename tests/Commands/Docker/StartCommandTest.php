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

namespace TeamNeusta\Magedev\Test\Commands\Docker;

use TeamNeusta\Magedev\Test\TestHelper\CommandMockHelper;
use TeamNeusta\Magedev\Commands\Docker\StartCommand;

/**
 * Class: StartCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class StartCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $helper = new CommandMockHelper();
        $command = $helper->getCommand(StartCommand::class);
        $helper->getDocker()->expects(self::once())->method('startContainers');
        $helper->executeCommand($command);
    }
}
