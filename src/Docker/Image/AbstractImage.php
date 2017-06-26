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

use Docker\Context\ContextBuilder;
use TeamNeusta\Magedev\Docker\Api\ImageFactory;

/**
 * Class AbstractImage.
 */
abstract class AbstractImage
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \TeamNeusta\Magedev\Docker\Image\Factory
     */
    protected $imageFactory;

    /**
     * @var \Docker\Context\ContextBuilder
     */
    protected $contextBuilder;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * @var \TeamNeusta\Magedev\Docker\Api\ImageFactory
     */
    protected $imageApiFactory;

    /**
     * @var \TeamNeusta\Magedev\Docker\Helper\NameBuilder
     */
    protected $nameBuilder;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config            $config
     * @param \TeamNeusta\Magedev\Docker\Image\Factory      $imageFactory
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     * @param \TeamNeusta\Magedev\Docker\Context            $context
     * @param \TeamNeusta\Magedev\Docker\Api\ImageFactory   $imageApi
     * @param \TeamNeusta\Magedev\Docker\Helper\NameBuilder $nameBuilder
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Docker\Image\Factory $imageFactory,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \Docker\Context\ContextBuilder $contextBuilder,
        \TeamNeusta\Magedev\Docker\Api\ImageFactory $imageApiFactory,
        \TeamNeusta\Magedev\Docker\Helper\NameBuilder $nameBuilder
    ) {
        $this->config = $config;
        $this->imageFactory = $imageFactory;
        $this->fileHelper = $fileHelper;
        $this->contextBuilder = $contextBuilder;
        $this->imageApiFactory = $imageApiFactory;
        $this->nameBuilder = $nameBuilder;

        foreach ($this->config->get('env_vars') as $key => $value) {
            $this->env($key, $value);
        }
    }

    /**
     * configure.
     */
    abstract public function configure();

    /**
     * getBuildName.
     *
     * @return string
     */
    public function getBuildName()
    {
        return 'magedev-'.$this->getName();
    }

    /**
     * getName.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * name.
     *
     * @param mixed $name
     */
    public function name($name)
    {
        $this->name = $name;
    }

    /**
     * from.
     *
     * @param string | AbstractImage $image
     */
    public function from($image)
    {
        if ($image instanceof self) {
            $this->imageApiFactory->create($image)->build();
            $this->contextBuilder->from($image->getBuildName());

            return;
        }

        if (is_string($image)) {
            $imageName = $image;
            $this->contextBuilder->from($imageName);
        }
    }

    /**
     * add.
     *
     * @param string $path
     * @param mixed  $content
     */
    public function add($path, $content)
    {
        $this->contextBuilder->add($path, $content);
    }

    /**
     * addFile.
     *
     * @param string $srcPath
     * @param string $dstPath
     */
    public function addFile($srcPath, $dstPath)
    {
        $this->add($dstPath, $this->fileHelper->read($srcPath));
    }

    /**
     * run.
     *
     * @param string $cmd
     */
    public function run($cmd)
    {
        $this->contextBuilder->run($cmd);
    }

    /**
     * expose.
     *
     * @param int $port
     */
    public function expose($port)
    {
        $this->contextBuilder->expose($port);
    }

    /**
     * cmd.
     *
     * @param string $cmd
     */
    public function cmd($cmd)
    {
        $this->contextBuilder->command($cmd);
    }

    /**
     * env.
     *
     * @param string $key
     * @param string $value
     */
    public function env($key, $value)
    {
        $this->contextBuilder->env($key, $value);
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
