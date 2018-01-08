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

namespace TeamNeusta\Magedev\Docker\Api;

use Docker\API\Model\BuildInfo;
use Docker\API\Model\CreateImageInfo;
use Docker\Context\ContextBuilder;
use Docker\Manager\ImageManager;

/**
 * Class Image.
 */
class Image
{
    /**
     * @var \Docker\Manager\ImageManager
     */
    protected $imageManager;

    /**
     * @var \TeamNeusta\Magedev\Docker\Image\AbstractImage
     */
    protected $image;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * __construct.
     *
     * @param \Docker\Manager\ImageManager                      $imageManager
     * @param \TeamNeusta\Magedev\Docker\Image\AbstractImage    $image
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(
        \Docker\Manager\ImageManager $imageManager,
        \TeamNeusta\Magedev\Docker\Image\AbstractImage $image,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->imageManager = $imageManager;
        $this->image = $image;
        $this->output = $output;
    }

    /**
     * exists.
     *
     * @return bool
     */
    public function exists()
    {
        $images = $this->imageManager->findAll();
        foreach ($images as $image) {
            if (!$image->getRepoTags()) {
                continue;
            }
            foreach ($image->getRepoTags() as $tags) {
                $nameParts = explode(':', $tags);
                $name = $nameParts[0];
                $version = $nameParts[1];
                if ($name === $this->image->getBuildName()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * build.
     */
    public function build()
    {
        $this->image->configure();
        $name = $this->image->getBuildName();
        if (!$this->exists()) {
            $contextBuilder = $this->image->getContextBuilder();
            $context = $contextBuilder->getContext();

            $buildName = $this->image->getBuildName();
            $this->output->writeln("<fg=green>Building image " . $buildName . "...</>");

            $buildStream = $this->imageManager->build($context->read(), [
                't' => $buildName,
                'rm' => true,
                'nocache' => true,
            ], ImageManager::FETCH_STREAM);

            $buildStream->onFrame(function (BuildInfo $buildInfo) {
                $status = $buildInfo->getStream();
                $progress = $buildInfo->getProgress();
                if ($status != '') {
                    echo $status."\n";
                } elseif ($progress != '') {
                    echo $progress."\n";
                }
            });
            $buildStream->wait();
        }
    }

    /**
     * pull.
     */
    public function pull()
    {
        $name = $this->image->getBuildName();
        if (!$this->exists()) {
            $this->output->writeln("<fg=green>Pulling image " . $name . "...</>");
            $buildStream = $this->imageManager->create(
                null,
                [
                  'fromImage' => $name,
                ],
                ImageManager::FETCH_STREAM);
            $buildStream->onFrame(function (CreateImageInfo $info) {
                echo $info->getProgress()."\n";
            });
            $buildStream->wait();
        }
    }

    /**
     * destroy.
     */
    public function destroy()
    {
        if ($this->exists()) {
            $name = $this->image->getBuildName();
            $this->output->writeln("<fg=red>Deleting image " . $name . "...</>");
            $this->imageManager->remove($name);
        }
    }

    /**
     * getContextBuilder.
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function getContextBuilder()
    {
        return $this->contextBuilder;
    }
}
