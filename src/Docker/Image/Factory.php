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

/**
 * Class: Factory
 *
 * @see DockerImage
 */
class Factory
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * @var \Docker\Context\ContextBuilder
     */
    protected $contextBuilder;

    /**
     * @var \TeamNeusta\Magedev\Runtime\Helper\FileHelper
     */
    protected $fileHelper;

    /**
     * __construct
     *
     * @param \TeamNeusta\Magedev\Runtime\Config  $config
     * @param \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper
     * @param \TeamNeusta\Magedev\Docker\Context $context
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config,
        \TeamNeusta\Magedev\Runtime\Helper\FileHelper $fileHelper,
        \Docker\Context\ContextBuilder $contextBuilder
    ) {
        $this->config = $config;
        $this->fileHelper = $fileHelper;
        $this->contextBuilder = $contextBuilder;
    }

    public function create($className)
    {
        $className = "\\TeamNeusta\\Magedev\\Docker\\Image\\Repository\\" . $className;
        return new $className(
            $this->config,
            $this,
            $this->fileHelper,
            $this->contextBuilder
        );
    }
}
