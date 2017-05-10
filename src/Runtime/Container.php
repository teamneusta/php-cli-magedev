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

$c['runtime.helper.magerunhelper'] = function ($c) {
    return new TeamNeusta\Magedev\Runtime\Helper\MagerunHelper(
        $c['runtime.config'],
        $c['runtime.helper.filehelper'],
        $c['services.docker']
    );
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
        new \Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand(),

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
        new \TeamNeusta\Magedev\Commands\Media\ImportCommand($c['runtime.config'], $c['services.shell']),

        /* // Config */
        new \TeamNeusta\Magedev\Commands\Config\ResetCommand(
            $c['runtime.config'],
            $c['runtime.helper.filehelper'],
            $c['services.docker']
        ),

        new \TeamNeusta\Magedev\Commands\Docker\BuildCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\DestroyCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\MysqlCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\ReinitCommand(),
        new \TeamNeusta\Magedev\Commands\Docker\RestartCommand(),
        new \TeamNeusta\Magedev\Commands\Docker\SshCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\StartCommand($c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Docker\StopCommand($c['services.docker']),

        /* // Grunt */
        new \TeamNeusta\Magedev\Commands\Grunt\RefreshCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Grunt\WatchCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Grunt\KillCommand($c['runtime.config'], $c['services.docker']),

        /* // Init */
        new \TeamNeusta\Magedev\Commands\Init\AddHostEntryCommand($c['runtime.config'], $c['console.output'], $c['services.shell']),
        new \TeamNeusta\Magedev\Commands\Init\ComposerCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Init\NpmCommand($c['runtime.config'], $c['services.docker'], $c['services.shell']),
        new \TeamNeusta\Magedev\Commands\Init\PermissionsCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Init\ProjectCommand($c['runtime.config'], $c['console.output']),

        /* // Magento */
        new \TeamNeusta\Magedev\Commands\Magento\AlignConfigCommand($c['runtime.config']),
        new \TeamNeusta\Magedev\Commands\Magento\CacheCleanCommand($c['runtime.config'], $c['runtime.helper.magerunhelper'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\CommandCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\DefaultAdminUserCommand($c['runtime.config'], $c['runtime.helper.magerunhelper'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\DefaultCustomerCommand($c['runtime.config'], $c['runtime.helper.magerunhelper'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\InstallCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\InstallMagerunCommand($c['runtime.config'], $c['services.shell']),
        new \TeamNeusta\Magedev\Commands\Magento\RefreshCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\ReindexCommand($c['runtime.config'], $c['runtime.helper.magerunhelper'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\SetBaseUrlCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Magento\UpgradeCommand($c['runtime.config'], $c['runtime.helper.magerunhelper'], $c['services.docker']),

        /* // Tests */
        new \TeamNeusta\Magedev\Commands\Tests\DebugCommand($c['runtime.config'], $c['services.docker']),
        new \TeamNeusta\Magedev\Commands\Tests\RunCommand($c['runtime.config'], $c['services.docker']),

        new \TeamNeusta\Magedev\Commands\UpdateCommand()
    ];
};

$c['application'] = function($c) {
    $application = new \TeamNeusta\Magedev\Runtime\Application();
    $application->addCommands($c['commands']);
    return $application;
};

return $c;
