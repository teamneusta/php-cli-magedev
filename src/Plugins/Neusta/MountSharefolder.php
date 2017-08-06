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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use TeamNeusta\Magedev\Runtime\Helper\FileHelper;

/**
 * Class MountSharefolder.
 */
class MountSharefolder
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
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Services\ShellService
     */
    protected $shellService;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected $questionHelper;

    /**
     * __construct.
     *
     * @param \Pimple\Container $c
     */
    public function __construct(\Pimple\Container $c)
    {
        $this->input = $c['console.input'];
        $this->output = $c['console.output'];
        $this->config = $c['runtime.config'];
        $this->shellService = $c['services.shell'];
        $this->fileHelper = $c['runtime.helper.filehelper'];
        $this->questionHelper = $c['console.questionhelper'];
        $c['runtime.eventdispatcher']->addListener('before:db:import', array($this, 'checkShareFolder'));
        $c['runtime.eventdispatcher']->addListener('before:media:import', array($this, 'checkShareFolder'));
        $c['runtime.eventdispatcher']->addListener('before:init:project', array($this, 'checkShareFolder'));
    }

    /**
     * checkShareFolder.
     */
    public function checkShareFolder()
    {
        foreach (['media_archive', 'dump_file'] as $fileType) {
            if ($this->config->optionExists($fileType)) {
                $this->checkPath($this->config->get($fileType));
            }
        }
    }

    /**
     * checkPath.
     *
     * @param string $path
     */
    public function checkPath($path)
    {
        if (!file_exists($this->fileHelper->expandPath($path))) {
            if ($this->isFileInShareFolderPath($path)) {
                $this->tryMount($path);
            }
        }
    }

    /**
     * fileInShareFolderPath.
     *
     * @return bool
     */
    public function isFileInShareFolderPath($path)
    {
        $shareFolderPrefix = '~/smb/share/';

        return substr($path, 0, strlen($shareFolderPrefix)) === $shareFolderPrefix;
    }

    /**
     * tryMount.
     */
    public function tryMount($path)
    {
        $question = new ConfirmationQuestion('File '.$path.' could not found, but it seems the path is inside shared folder, should I try to mount it? [y]', false);
        if ($this->questionHelper->ask($this->input, $this->output, $question)) {
            $mountScript = '/etc/smbmount';
            if (!file_exists($mountScript)) {
                throw new \Exception('Mountscript '.$mountScript.' not found');
            }
            $this->shellService->execute('sudo /etc/smbmount');
        }
    }
}
