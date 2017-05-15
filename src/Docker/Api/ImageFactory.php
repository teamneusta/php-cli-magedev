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

/**
 * Class ImageFactory
 */
class ImageFactory
{
    /**
     * @var \Docker\Manager\ImageManager
     */
    protected $imageManager;

    /**
     * __construct
     *
     * @param \Docker\Manager\ImageManager $imageManager
     * @param \TeamNeusta\Magedev\Docker\Image\AbstractImage $image
     */
    public function __construct(
        \Docker\Manager\ImageManager $imageManager
    ) {
        $this->imageManager = $imageManager;
    }

    /**
     * create
     *
     * @param \TeamNeusta\Magedev\Docker\Image\AbstractImage $image
     */
    public function create(\TeamNeusta\Magedev\Docker\Image\AbstractImage $image)
    {
        return new Image($this->imageManager, $image);
    }
}
