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

namespace TeamNeusta\Magedev\Plugins\Neusta;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use TeamNeusta\Magedev\Runtime\Runtime;

/**
 * Class StopServices
 */
class StopServices
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Runtime
     */
    protected $runtime;

    /**
     * __construct
     *
     * @param mixed $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
        $this->runtime->getDispatcher()->addListener('before:docker:start', array($this, 'stopServices'));
    }

    /**
     * stopServices
     */
    public function stopServices()
    {
        $this->runtime->getOutput()->writeln("let me check if you have local services like apache or mysql running...");

        $apacheRunning = $this->runtime->getHelper('ProcessHelper')->isProcessRunning("apache2");
        $mysqlRunning = $this->runtime->getHelper('ProcessHelper')->isProcessRunning("mysqld");
        $redisRunning = $this->runtime->getHelper('ProcessHelper')->isProcessRunning("redis-server");

        if ($apacheRunning || $mysqlRunning || $redisRunning) {
            $questionHelper = $this->runtime->getQuestionHelper();
            $question = new ConfirmationQuestion("you have a local apache/mysql running. Should I stop it for you? [y]", false);

            if (!$questionHelper->ask($this->runtime->getInput(), $this->runtime->getOutput(), $question)) {
                throw new \Exception("could not proceed");
            }

            if ($apacheRunning) {
                $this->runtime->getShell()->execute("sudo service apache2 stop");
            }
            if ($mysqlRunning) {
                $this->runtime->getShell()->execute("sudo service mysql stop");
            }
            if ($redisRunning) {
                $this->runtime->getShell()->execute("sudo service redis-server stop");
            }
        }
    }
}
