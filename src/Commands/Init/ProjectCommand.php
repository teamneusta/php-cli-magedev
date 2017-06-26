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

/**
 * Class: ProjectCommand.
 *
 * @see AbstractCommand
 */
class ProjectCommand extends AbstractCommand
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config
    ) {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('init:project');
        $this->setDescription('setup project');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->find('init:permissions')->execute($input, $output);
        $this->getApplication()->find('init:composer')->execute($input, $output);
        $this->getApplication()->find('init:npm')->execute($input, $output);
        $this->getApplication()->find('magento:install-magerun')->execute($input, $output);
        $this->getApplication()->find('magento:align-config')->execute($input, $output);
        $this->getApplication()->find('db:import')->execute($input, $output);
        $this->getApplication()->find('media:import')->execute($input, $output);
        $this->getApplication()->find('magento:set-base-url')->execute($input, $output);
        $this->getApplication()->find('magento:upgrade')->execute($input, $output);
        $this->getApplication()->find('magento:admin:default')->execute($input, $output);
        $this->getApplication()->find('magento:customer:default')->execute($input, $output);
        $this->getApplication()->find('init:permissions')->execute($input, $output);
        $this->getApplication()->find('config:reset')->execute($input, $output);
        $this->getApplication()->find('magento:refresh')->execute($input, $output);
        $this->getApplication()->find('magento:cache:clean')->execute($input, $output);
        $this->getApplication()->find('magento:reindex')->execute($input, $output);
        $this->getApplication()->find('init:add-host-entry')->execute($input, $output);

        $output->writeln('project installed');

        if ($this->config->optionExists('domain')) {
            $domain = $this->config->get('domain');
            $output->writeln('visit: http://'.$domain);
        }
    }
}
