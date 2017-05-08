<?php
/**
 * This file is part of the teamneusta/php-cli-magedev project.
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 */

/**
 * Class UpdateManifest
 */
class UpdateManifest extends Task
{
    private $_baseUrl = 'http://teamneusta.github.io/php-cli-magedev/releases/';

    /**
     * @var VersionManager
     */
    protected $versionManager;

    /**
     * Basic information to be provided/filled for release;
     *
     * @var array
     */
    private $_baseData = [
        "name" => "magedev.phar",
        "sha1" => "",
        "url" => "",
        "version" => ""
    ];
    /**
     * @var string
     */
    private $baseDir = null;

    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @var string
     */
    private $downloadPath;

    /**
     * UpdateManifest constructor.
     *
     * @codeCoverageIgnore
     *
     * @param VersionManager|null $versionManager
     */
    public function __construct(VersionManager $versionManager = null)
    {
        if ($versionManager == null) {
            $versionManager = new VersionManager();
        }

        $this->versionManager = $versionManager;
    }

    /**
     * The setter for the attribute "basedir"
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * The setter for the attribute "basedir"
     */
    public function setManifestPath($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * @param $downloadPath
     */
    public function setDownloadPath($downloadPath)
    {
        $this->downloadPath = $downloadPath;
    }

    /**
     * The main entry point method.
     */
    public function main()
    {
        $sha1 = sha1_file($this->baseDir . 'magedev.phar');

        $targetFileName = $this->getTargetFileName();

        echo "copy " . $this->baseDir . "magedev.phar" . " > " . $this->baseDir . $this->downloadPath . DIRECTORY_SEPARATOR . $targetFileName;
        copy($this->baseDir . 'magedev.phar',
            $this->baseDir . $this->downloadPath . DIRECTORY_SEPARATOR . $targetFileName);

        $releaseData = [
            'sha1' => $sha1,
            'url' => $this->_baseUrl . $targetFileName,
            'version' => $this->getVersion()
        ];

        $releaseData = array_merge($this->_baseData, $releaseData);

        $manifest = $this->addReleaseData($releaseData);
        $manifestEncoded = json_encode($manifest);

        var_dump($manifestEncoded);

        file_put_contents($this->manifestPath, $manifestEncoded);
    }

    private function getTargetFileName()
    {
        $version = $this->getVersion();
        $projectName = $this->getProject()->getName();

        $fileName = $projectName . "-" . $version . '.phar';

        return $fileName;
    }

    /**
     * @return mixed
     */
    private function getVersion()
    {
        return $this->versionManager->getVersion();
    }

    /**
     * @param $releaseData
     * @return array|mixed|string
     */
    private function addReleaseData($releaseData)
    {
        $manifest = @file_get_contents($this->manifestPath);

        if (strlen($manifest) > 0) {
            $manifest = json_decode($manifest, true);
        } else {
            $manifest = [];
        }

        $sha = $releaseData['sha1'];
        $version = $releaseData['version'];

        $manifest = array_filter($manifest, function($item) use ($sha,$version){
            $shaToCompare = $item['sha1'] ?? null;
            $versionToCompare = $item['version'] ?? null;

            return !($shaToCompare === $sha || $versionToCompare === $version);
        });

        $manifest[] = $releaseData;
        return array_values($manifest);
    }
}

/**
 * Class VersionManager
 *
 * @codeCoverageIgnore
 */
class VersionManager
{
    public function getVersion()
    {
        exec("git tag", $latestTag);
        return array_pop($latestTag);
    }
}
