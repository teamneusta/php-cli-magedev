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

namespace TeamNeusta\Magedev\Docker\Image;

use Docker\API\Model\BuildInfo;
use Docker\API\Model\CreateImageInfo;
use Docker\Context\ContextBuilder;
use Docker\Docker;
use Docker\Manager\ImageManager;

/**
 * Class DockerImage
 */
abstract class DockerImage
{
    /**
     * @var \TeamNeusta\Magedev\Docker\Context
     */
    protected $context;

    /**
     * @var \Docker\Context\ContextBuilder
     */
    protected $contextBuilder;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Docker\Context $context
     */
    public function __construct(
        \TeamNeusta\Magedev\Docker\Context $context
    ) {
        $this->context = $context;
        $this->contextBuilder = new ContextBuilder();
    }

    /**
     * getBuildName
     * @return string
     */
    public abstract function getBuildName();

    /**
     * configure
     */
    public abstract function configure();

    /**
     * exists
     * @return bool
     */
    public function exists()
    {
        $images = $this->context->getImageManager()->findAll();
        foreach ($images as $image) {
            foreach ($image->getRepoTags() as $tags) {
                $nameParts = explode(":", $tags);
                $name = $nameParts[0];
                $version = $nameParts[1];
                if ($name === $this->getBuildName()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * build
     */
    public function build()
    {
        $name = $this->getBuildName();
        if (!$this->exists()) {
            $contextBuilder = $context = $this->getContextBuilder();
            foreach ($this->context->getEnvVars() as $key => $value) {
                $contextBuilder->env($key, $value);
            }
            $this->configure();
            $context = $contextBuilder->getContext();
            $buildStream = $this->context->getImageManager()->build($context->read(), [
                't' => $this->getBuildName(),
                'rm' => true,
                'nocache' => false
            ], ImageManager::FETCH_STREAM);

            $buildStream->onFrame(function (BuildInfo $buildInfo) {
                $status = $buildInfo->getStream();
                $progress = $buildInfo->getProgress();
                if ($status != "") {
                    echo $status . "\n";
                } elseif ($progress != "") {
                    echo $progress . "\n";
                }
            });
            $buildStream->wait();
        }
    }

    /**
     * pull
     */
    public function pull()
    {
        $name = $this->getBuildName();
        if (!$this->exists()) {
            $buildStream = $this->context->getImageManager()->create(
                null,
                [
                  'fromImage' => $name
                ],
                ImageManager::FETCH_STREAM);
            $buildStream->onFrame(function (CreateImageInfo $info) {
                echo $info->getProgress() . "\n";
            });
            $buildStream->wait();
        }
    }

    /**
     * destroy
     */
    public function destroy()
    {
        // TODO
    }

    /**
     * getContextBuilder
     * @return \Docker\Context\ContextBuilder
     */
    public function getContextBuilder()
    {
        return $this->contextBuilder;
    }
}
