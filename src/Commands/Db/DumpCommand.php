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

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: DumpCommand
 *
 * @see AbstractCommand
 */
class DumpCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct
     *
     * @param DockerService $dockerService
     */
    public function __construct(DockerService $dockerService)
    {
        parent::__construct();
        $this->dockerService = $dockerService;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("db:dump");
        $this->setDescription("dump db");
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dumpFile = "dump.sql"; //TODO: make this configurable
        $dbName = "magento";

        $this->dockerService->execute("mysqldump ".$dbName." > ".$dumpFile);

        parent::execute($input, $output);
    }
}
