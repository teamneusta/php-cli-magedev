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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Runtime\Config;
use TeamNeusta\Magedev\Runtime\Helper\MagerunHelper;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: DefaultCustomerCommand.
 *
 * @see AbstractCommand
 */
class DefaultCustomerCommand extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper
     */
    protected $magerunHelper;

    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config               $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper $magerunHelper
     * @param \TeamNeusta\Magedev\Services\DockerService       $dockerService
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\MagerunHelper $magerunHelper,
        \TeamNeusta\Magedev\Services\DockerService $dockerService
    ) {
        $this->config = $config;
        $this->magerunHelper = $magerunHelper;
        $this->dockerService = $dockerService;
        parent::__construct();
    }

    /**
     * configure.
     */
    protected function configure()
    {
        $this->setName('magento:customer:default');
        $this->setDescription('creates a default customer magento@neusta.de/magento@neusta.de');
    }

    /**
     * execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersion = $this->config->getMagentoVersion();
        $userSettings = $this->config->get('users');
        $customer = $userSettings['customer'];

        if (!$this->customerExists($customer['email'])) {
            $this->magerunHelper->magerunCommand('customer:create '.$customer['email'].' '.$customer['password'].' '.$customer['firstname'].' '.$customer['lastname'].' default');
        } else {
            // change pw only works for magento1 for now
            if ($magentoVersion == '1') {
                $this->magerunHelper->magerunCommand('customer:change-password '.$customer['email'].' '.$customer['password'].' default');
            }
        }
    }

    /**
     * customerExists.
     *
     * check if a given customer exists
     *
     * @param string $email
     */
    public function customerExists($email)
    {
        $result = $this->dockerService->execute(
            "mysql --execute \"select * from customer_entity where email = '".$email."';\"",
            [
                'interactive' => false,
            ]
        );

        return $result != '';
    }
}
