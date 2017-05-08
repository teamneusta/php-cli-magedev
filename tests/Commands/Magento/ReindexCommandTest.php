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

namespace TeamNeusta\Magedev\Test\Commands\Magento;

use TeamNeusta\Magedev\Test\TestHelper\CommandMockHelper;
use TeamNeusta\Magedev\Commands\Magento\ReindexCommand;

/**
 * Class: ReindexCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ReindexCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $helper = new CommandMockHelper();
        $command = $helper->getCommand(ReindexCommand::class);
        $helper->getShell()
             ->expects(self::exactly(1))
             ->method('nativeExecute')
             ->withConsecutive(
                 ["docker exec --user=www-data -it magedev-neusta-magedev-main bash -c \"cd Source/ && bin/magento indexer:reindex\""]
             );
        $helper->executeCommand($command);
    }

    public function testReindexCallForMagento1()
    {
        $magedevConfig = [
            "magento_version" => "1",
            "source_folder" => ".",
            "domain" => "test.domain.de"
        ];
        $helper = new CommandMockHelper($magedevConfig);
        $command = $helper->getCommand(ReindexCommand::class);
        $helper->getShell()
             ->expects(self::exactly(1))
             ->method('nativeExecute')
             ->withConsecutive(
                 ["docker exec --user=www-data -it magedev-neusta-magedev-main bash -c \"cd . && shell/magerun index:reindex:all\""]
             );
        $helper->getFileHelper()
            ->method('fileExists')
            ->willReturn(true);
        $helper->executeCommand($command);
    }
}
