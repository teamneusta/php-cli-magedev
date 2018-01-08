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

namespace TeamNeusta\Magedev\Commands\Magento;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: SetBaseUrlCommand.
 *
 * @see AbstractCommand
 */
class SetBaseUrlCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config         $config
     * @param \TeamNeusta\Magedev\Services\DockerService $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('magento:set-base-url');
        $this->setDescription('change base url of magento');
        $this->addArgument('domain', InputArgument::OPTIONAL, 'domain without http and slashes');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->optionExists('domains')) {
            $domains = $this->config->get('domains');
            foreach ($domains as $domain => $scopeId) {
                $baseUrl = 'http://'.$domain.'/';
                $this->updateBaseUrl($baseUrl, $scopeId);
            }

            return;
        } else {
            $baseUrl = '';
            if ($this->config->optionExists('base_url')) {
                $baseUrl = $this->config->get('base_url');
            }

            if (empty($baseUrl)) {
                $domain = $this->config->get('domain');
                $baseUrl = 'http://'.$domain.'/';
            }

            if (empty($baseUrl)) {
                throw new \Exception('could not determine base_url');
            }
            $this->updateBaseUrl($baseUrl);
            $this->deleteBaseLinkUrls();
        }
        $this->getApplication()->find('magento:cache:clean')->execute($input, $output);
    }

    /**
     * updateBaseUrl.
     *
     * @param string $baseUrl
     * @param int    $scopeId
     */
    public function updateBaseUrl($baseUrl, $scopeId = 0)
    {
        $cmd = "mysql --execute=\"update core_config_data set value='".$baseUrl."' where (path='web/unsecure/base_url' OR path='web/secure/base_url') AND scope_id=".$scopeId.';"';
        $this->dockerService->execute($cmd);
    }

    /**
     * deleteBaseLinkUrls
     */
    public function deleteBaseLinkUrls()
    {
        $cmd = "mysql --execute=\"delete from core_config_data where (path='web/unsecure/base_link_url' OR path='web/secure/base_link_url');\"";
        $this->dockerService->execute($cmd);
    }
}
