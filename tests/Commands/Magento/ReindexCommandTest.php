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

use Mockery as m;
use TeamNeusta\Magedev\Commands\Magento\ReindexCommand;

/**
 * Class: ReindexCommandTest.
 *
 * @see \PHPUnit_Framework_TestCase
 */
class ReindexCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config', [
            'getMagentoVersion' => 2,
        ]);

        $magerunHelper = m::mock('\TeamNeusta\Magedev\Runtime\Helper\MagerunHelper');
        $dockerService = m::mock(
            '\TeamNeusta\Magedev\Services\DockerService',
            ['execute' => 'docker exec --user=www-data -it magedev-neusta-magedev-main bash -c "cd Source/ && bin/magento indexer:reindex"']
        );

        $command = new ReindexCommand($config, $magerunHelper, $dockerService);
        $command->execute($input, $output);
    }

    public function testReindexCallForMagento1()
    {
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\ConsoleOutput[]', ['writeln']);

        $config = m::mock('\TeamNeusta\Magedev\Runtime\Config', [
            'getMagentoVersion' => 1,
        ]);

        $magerunHelper = m::mock(
            '\TeamNeusta\Magedev\Runtime\Helper\MagerunHelper',
            ['magerunCommand' => 'index:reindex:all']
        );
        $dockerService = m::mock('\TeamNeusta\Magedev\Services\DockerService');

        $command = new ReindexCommand($config, $magerunHelper, $dockerService);
        $command->execute($input, $output);
    }
}
