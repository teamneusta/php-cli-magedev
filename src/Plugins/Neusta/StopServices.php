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

/**
 * Class StopServices.
 */
class StopServices
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * @var \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected $questionHelper;

    /**
     * __construct.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param ShellService    $shellService
     * @param QuestionHelper  $questionHelper
     */
    public function __construct(\Pimple\Container $c)
    {
        $this->input = $c['console.input'];
        $this->output = $c['console.output'];
        $this->shellService = $c['services.shell'];
        $this->questionHelper = $c['console.questionhelper'];
        $c['runtime.eventdispatcher']->addListener('before:docker:start', array($this, 'stopServices'));
    }

    /**
     * stopServices.
     */
    public function stopServices()
    {
        $this->output->writeln('let me check if you have local services like apache or mysql running...');

        $apacheRunning = $this->isProcessRunning('apache2');
        $nginxRunning = $this->isProcessRunning("nginx");
        $mysqlRunning = $this->isProcessRunning('mysqld');
        $redisRunning = $this->isProcessRunning('redis-server');

        if ($apacheRunning || $mysqlRunning || $redisRunning || $nginxRunning) {
            $question = new ConfirmationQuestion('you have a local apache/mysql running. Should I stop it for you? [y]', false);

            if (!$this->questionHelper->ask($this->input, $this->output, $question)) {
                throw new \Exception('could not proceed');
            }

            if ($apacheRunning) {
                $this->shellService->execute('sudo service apache2 stop');
            }
            if ($nginxRunning) {
                $this->shellService->execute("sudo service nginx stop");
            }
            if ($mysqlRunning) {
                $this->shellService->execute('sudo service mysql stop');
            }
            if ($redisRunning) {
                $this->shellService->execute('sudo service redis-server stop');
            }
        }
    }

    /**
     * isProcessRunning.
     *
     * @param string $processName
     *
     * @return bool
     */
    public function isProcessRunning($processName)
    {
        $pid = [];
        exec('sudo pidof -c '.$processName, $pid);

        return !empty($pid);
    }
}
