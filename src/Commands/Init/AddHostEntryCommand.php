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

namespace TeamNeusta\Magedev\Commands\Init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\ShellService;

/**
 * Class: AddHostEntryCommand
 *
 * @see AbstractCommand
 */
class AddHostEntryCommand extends AbstractCommand
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \TeamNeusta\Magedev\Services\ShellService $shellService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \Symfony\Component\Console\Output\OutputInterface $output,
        \TeamNeusta\Magedev\Services\ShellService $shellService
    ) {
        $this->config = $config;
        $this->output = $output;
        $this->shellService = $shellService;
        parent::__construct();
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('init:add-host-entry');
        $this->setDescription('adds domain to your /etc/hosts');
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->optionExists("domains")) {
            $domains = $this->config->get("domains");
            foreach ($domains as $domain => $scopeId) {
                $this->addHostEntryIfRequired($runtime, $domain);
            }
        } else {
            $domain = $this->config->get("domain");
            $this->addHostEntryIfRequired($runtime, $domain);
        }
    }


    /**
     * addHostEntryIfRequired
     *
     * @param Runtime $runtime
     * @param string $domain
     */
    public function addHostEntryIfRequired($domain)
    {
        $this->output->writeln("checking your /etc/hosts for presence of ".$domain);

        ob_start();
        $res = system("grep \"".$domain."\" /etc/hosts");
        ob_clean();
        ob_end_flush();
        if (!$res) {
            $this->output->writeln("adding ".$domain." to your /etc/hosts");
            $this->shellService->execute("sudo sh -c 'echo \"127.0.0.1 ".$domain."\" >> /etc/hosts'");
        }
    }
}
