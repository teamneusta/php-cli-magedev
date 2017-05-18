<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license BSD-3-Clause License
 */

namespace TeamNeusta\Magedev\Services;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShellService.
 */
class ShellService
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $cmd = '';

    /**
     * @var string
     */
    protected $wd = null;

    /**
     * __construct.
     *
     * @param Context         $context
     * @param OutputInterface $output
     */
    public function __construct(
        OutputInterface $output
    ) {
        $this->output = $output;
    }

    /**
     * wd.
     *
     * @param string $dir
     */
    public function wd($dir)
    {
        $this->wd = $dir;

        return $this;
    }

    /**
     * bash.
     *
     * @param string $cmd
     */
    public function bash($cmd)
    {
        if ($this->wd) {
            $cmd = 'cd '.$this->wd.' && '.$cmd;
        }

        return $this->execute($cmd);
    }

    /**
     * execute.
     *
     * @param string $cmd
     */
    public function execute($cmd, $interactive = true)
    {
        $cmd = $this->cmd.$cmd;
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('execute: '.$cmd);
        }

        return $this->nativeExecute($cmd, $interactive);
    }

    /**
     * nativeExecute.
     *
     * @param string $cmd
     */
    public function nativeExecute($cmd, $interactive = true)
    {
        if ($interactive) {
            $returnVar = 0;
            passthru($cmd, $returnVar);
            if ($returnVar != 0) {
                throw new \Exception('last command '.$cmd.' failed. cannot proceed');
            }
        } else {
            return exec($cmd);
        }
    }
}
