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

/**
 * Class: CacheCleanCommand
 *
 * @see AbstractCommand
 */
class CommandCommand extends AbstractCommand
{
    const ARGUMENT_MAGENTO_COMMAND = 'magento_command';
    const ARGUMENT_MAGENTO_COMMAND_ARGS = 'magento_command_args';

    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:command")
            ->setDescription("Execute command with bin/magento (magento 2 only)");

        $this->addArgument(
            self::ARGUMENT_MAGENTO_COMMAND,
            InputArgument::REQUIRED,
            'Command to be executed by bin/magento'
        );
        $this->addArgument(
            self::ARGUMENT_MAGENTO_COMMAND_ARGS,
            InputArgument::IS_ARRAY,
            'parameters for bin/magento'
        );

        $this->onExecute([$this, 'onExecuteCallback']);
    }

    /**
     * onExecuteCallback
     *
     * @param Runtime $runtime
     */
    public function onExecuteCallback($runtime)
    {
        $command = $this->input->getArgument(self::ARGUMENT_MAGENTO_COMMAND);
        $arguments = $this->input->getArgument(self::ARGUMENT_MAGENTO_COMMAND_ARGS);

        $shellCommand = sprintf(
            "bin/magento %s %s",
            $command,
            implode(' ', $arguments)
        );

        if ($context->getMagentoVersion() == "2") {
            $runtime->getDocker()
                ->execute(
                    $shellCommand
                );
        } else {
            $this->output->writeln('<error>This command works for magento 2 only!</error>');
        }
    }

}
