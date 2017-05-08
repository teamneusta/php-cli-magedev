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
 * Class MountSharefolder
 */
class MountSharefolder
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Runtime $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
        $this->fileHelper = $runtime->getHelper("FileHelper");
        $this->runtime->getDispatcher()->addListener('before:db:import', array($this, 'checkShareFolder'));
        $this->runtime->getDispatcher()->addListener('before:media:import', array($this, 'checkShareFolder'));
    }

    /**
     * checkShareFolder
     */
    public function checkShareFolder()
    {
        foreach (['media_archive', 'dump_file'] as $fileType) {
            if ($this->runtime->getConfig()->optionExists($fileType)) {
                $this->checkPath($this->runtime->getConfig()->get($fileType));
            }
        }
    }

    /**
     * checkPath
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
     * fileInShareFolderPath
     * @return bool
     */
    public function isFileInShareFolderPath($path)
    {
        $shareFolderPrefix = "~/smb/share/";
        return substr($path, 0, strlen($shareFolderPrefix)) === $shareFolderPrefix;
    }

    /**
     * tryMount
     */
    public function tryMount($path)
    {
        $question = new ConfirmationQuestion("File ".$path." could not found, but it seems the path is inside shared folder, should I try to mount it? [y]", false);
        $questionHelper = $this->runtime->getQuestionHelper();

        if ($questionHelper->ask($this->runtime->getInput(), $this->runtime->getOutput(), $question)) {
            $mountScript = "/etc/smbmount";
            if (!file_exists($mountScript)) {
                throw new \Exception("Mountscript ".$mountScript." not found");
            }
            $this->runtime->getShell()->execute("sudo /etc/smbmount");
        }
    }
}
