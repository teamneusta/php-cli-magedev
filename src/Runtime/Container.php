<?php

$c = new \Pimple\Container();

$c['console.input'] = function ($c) {
    return new \Symfony\Component\Console\Input\ArgvInput();
};

$c['console.output'] = function ($c) {
    return new \Symfony\Component\Console\Output\ConsoleOutput();
};

$c['runtime.helper.filehelper'] = function ($c) {
    return new TeamNeusta\Magedev\Runtime\Helper\FileHelper();
};

$c['runtime.config'] = function ($c) {
    return new \TeamNeusta\Magedev\Runtime\Config(
        $c['console.input'],
        $c['runtime.helper.filehelper']
    );
};

$c['services.docker'] = function ($c) {
    return new \TeamNeusta\Magedev\Services\DockerService(
        $c['runtime.config'],
        $c['console.output'],
        $c['services.shell'],
        $c['runtime.helper.filehelper']
    );
};

$c['services.shell'] = function ($c) {
    return new \TeamNeusta\Magedev\Services\ShellService(
        $c['console.output']
    );
};


$c['commands'] = function($c) {
    return [
        /* $this->add(new \Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand()); */

        // Db
        new \TeamNeusta\Magedev\Commands\Db\CleanupCommand(
            $c['runtime.config'],
            $c['runtime.helper.filehelper'],
            $c['services.shell'],
            $c['services.docker']
        ),
        new \TeamNeusta\Magedev\Commands\Db\DumpCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Db\ImportCommand(
            $c['runtime.config'],
            $c['services.shell'],
            $c['services.docker']
        ),

        /* // Media */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Media\ImportCommand); */

        /* // Config */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Config\ResetCommand); */

        new \TeamNeusta\Magedev\Commands\Docker\BuildCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\DestroyCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\MysqlCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\ReinitCommand(),
        new \TeamNeusta\Magedev\Commands\Docker\RestartCommand(),
        new \TeamNeusta\Magedev\Commands\Docker\SshCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\StartCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\StopCommand($c['services.docker']),

        /* // Grunt */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Grunt\RefreshCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Grunt\WatchCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Grunt\KillCommand); */

        /* // Init */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Init\ComposerCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Init\NpmCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Init\PermissionsCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Init\ProjectCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Init\AddHostEntryCommand); */

        /* // Magento */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\CacheCleanCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\CommandCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\RefreshCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\ReindexCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\SetBaseUrlCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\AlignConfigCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\UpgradeCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\InstallCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\InstallMagerunCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\DefaultAdminUserCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Magento\DefaultCustomerCommand); */

        /* // Tests */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Tests\DebugCommand); */
        /* $this->add(new \TeamNeusta\Magedev\Commands\Tests\RunCommand); */

        /* $this->add(new \TeamNeusta\Magedev\Commands\UpdateCommand); */
    ];
};

$c['application'] = function($c) {
    $application = new \TeamNeusta\Magedev\Runtime\Application();
    $application->addCommands($c['commands']);
    return $application;
};

return $c;
