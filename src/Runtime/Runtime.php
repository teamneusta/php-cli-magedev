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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Runtime
 */
class Runtime
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected $questionHelper;

    /**
     * __construct
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->dispatcher = new EventDispatcher();
        $this->questionHelper = $questionHelper;

        if ($input == null) {
            throw new \Exception("InputInterface cannot be null");
        }
        if ($output == null) {
            throw new \Exception("OutputInterface cannot be null");
        }
        $this->loadPlugins();
    }

    /**
     * getInput
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * getOutput
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * getConfig
     * @return \TeamNeusta\Magedev\Runtime\Config
     */
    public function getConfig()
    {
        return new \TeamNeusta\Magedev\Runtime\Config($this);
    }

    /**
     * getHelper
     *
     * @param string $name
     * @return \TeamNeusta\Magedev\Runtime\Helper\AbstractHelper
     */
    public function getHelper($name)
    {
        $class = "\TeamNeusta\Magedev\Runtime\Helper\\" . $name;
        if (!class_exists($class)) {
            throw new \Exception("Requested helper " . $name . " was not found");

        }
        return new $class($this);
    }

    /**
     * loadPlugins
     */
    protected function loadPlugins()
    {
        // TODO: how to make this more generic?
        if ($this->getConfig()->optionExists("plugins")) {
            $plugins = $this->getConfig()->get("plugins");
            /* $plugins = ["StopServices", "MountSharefolder"]; */
            foreach ($plugins as $plugin) {
                $class = "\TeamNeusta\Magedev\Plugins\Neusta\\" . $plugin;
                if (!class_exists($class)) {
                    throw new \Exception("Plugin ".$plugin." could not be found.");
                }
                new $class($this);
            }
        }
    }

    /**
     * getDispatcher
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * getQuestionHelper
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }
}
