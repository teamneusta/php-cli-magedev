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

namespace TeamNeusta\Magedev\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use TeamNeusta\Magedev\Runtime\Runtime;

/**
 * Class: AbstractCommand
 *
 * @see BaseCommand
 * @abstract
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var Runtime
     */
    public static $runtime;

    /**
     * @var callable
     */
    protected $commandCallback = null;

    /**
     * onExecute
     *
     * @param callable $callback
     */
    public function onExecute(callable $callback)
    {
        $this->commandCallback = $callback;
    }

    /**
     * configure
     * @codeCoverageIgnore
     */
    protected function configure()
    {
        $this->commandConfig = $this->getCommandConfig();

        $this->ensureConfigFieldIsSet($this->commandConfig, 'name');
        $this->ensureConfigFieldIsSet($this->commandConfig, 'description');
        $this->ensureConfigFieldIsSet($this->commandConfig, 'command');

        $this->setName($this->commandConfig['name']);
        $this->setDescription($this->commandConfig['description']);
    }

    /**
     * Ensures, that a field is set in an array and throws an exception if not.
     *
     * @param array  $array Lookup-source.
     * @param string $field Field to look for.
     *
     * @throws \Exception Field was not found.
     */
    protected function ensureConfigFieldIsSet(array $array, $field)
    {
        if (!isset($array[$field])) {
            throw new \Exception(sprintf("%s not set", $field));
        }
    }

    /**
     * execute
     * @codeCoverageIgnore
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        self::$runtime = $this->createRuntime($input, $output);
        $this->executeCommand();
        $output->writeln("finished ... have a nice day :)");
    }

    /**
     * executeCommand
     */
    public function executeCommand()
    {
        self::$runtime->getDispatcher()->dispatch("before:" . $this->getName(), new GenericEvent()); if ($this->commandCallback) {
            call_user_func_array($this->commandCallback, [self::$runtime]);
        }
    }

    /**
     * @param AbstractCommand $command
     */
    public function executeSubcommand($command)
    {
        // init command to avoid this error:
        // Cannot retrieve helper "question" because there is no HelperSet defined. Did you forget to add your command to the
        // application or to set the application on the command using the setApplication() method  You can also set the
        // HelperSet directly using the setHelperSet() method.
        // TODO: use DI here

        $command->setApplication($this->getApplication());
        $command->executeCommand();
    }

    public function createRuntime(InputInterface $input, OutputInterface $output)
    {
        return new Runtime($input, $output, $this->getHelper('question'));
    }
}
