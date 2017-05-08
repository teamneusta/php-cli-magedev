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

namespace TeamNeusta\Magedev\Runtime;

/**
 * Class: Application
 */
class Application extends \Symfony\Component\Console\Application
{
    protected static $_logo = <<<LOGO
     __  __    _    ____ _____ ____  _______     __
    |  \/  |  / \  / ___| ____|  _ \| ____\ \   / /
    | |\/| | / _ \| |  _|  _| | | | |  _|  \ \ / /
    | |  | |/ ___ \ |_| | |___| |_| | |___  \ V /
    |_|  |_/_/   \_\____|_____|____/|_____|  \_/
LOGO;

    /**
     * __construct
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct('magedev', '1.0.0');

        $this->add(new \Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand());

        // Db
        $this->add(new \TeamNeusta\Magedev\Commands\Db\ImportCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Db\DumpCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Db\CleanupCommand);

        // Media
        $this->add(new \TeamNeusta\Magedev\Commands\Media\ImportCommand);

        // Config
        $this->add(new \TeamNeusta\Magedev\Commands\Config\ResetCommand);

        // Docker
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\BuildCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\MysqlCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\SshCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\StartCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\StopCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\RestartCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\DestroyCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Docker\ReinitCommand);

        // Grunt
        $this->add(new \TeamNeusta\Magedev\Commands\Grunt\RefreshCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Grunt\WatchCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Grunt\KillCommand);

        // Init
        $this->add(new \TeamNeusta\Magedev\Commands\Init\ComposerCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Init\NpmCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Init\PermissionsCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Init\ProjectCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Init\AddHostEntryCommand);

        // Magento
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\CacheCleanCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\CommandCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\RefreshCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\ReindexCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\SetBaseUrlCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\AlignConfigCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\UpgradeCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\InstallCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\InstallMagerunCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\DefaultAdminUserCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Magento\DefaultCustomerCommand);

        // Tests
        $this->add(new \TeamNeusta\Magedev\Commands\Tests\DebugCommand);
        $this->add(new \TeamNeusta\Magedev\Commands\Tests\RunCommand);
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getHelp()
    {
        return static::$_logo . "\n\n" . parent::getHelp();
    }
}
