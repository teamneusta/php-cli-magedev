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
use TeamNeusta\Magedev\Commands\Magento\RefreshCommand;

/**
 * Class: RefreshCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class RefreshCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $helper = new CommandMockHelper();
        $command = $helper->getCommand(RefreshCommand::class);
        $projectName = basename(getcwd());
        $helper->getShell()
             ->expects(self::exactly(7))
             ->method('nativeExecute')
             ->withConsecutive(
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf var/generation/*\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf var/di/*\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf var/cache/*\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf var/view_preprocessed/*\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf pub/static/_requirejs\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf pub/static/adminhtml\""],
                 ["docker exec --user=www-data -it magedev-".$projectName."-main bash -c \"cd Source/ && rm -rf pub/static/frontend\""]
             );
        $helper->executeCommand($command);
    }
}
