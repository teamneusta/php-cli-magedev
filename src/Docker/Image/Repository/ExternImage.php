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

namespace TeamNeusta\Magedev\Docker\Image\Repository;

use TeamNeusta\Magedev\Docker\Image\AbstractImage;

/**
 * Class: ExternImage.
 *
 * @see DockerImage
 */
class ExternImage extends AbstractImage
{
    /**
     * @var string
     */
    protected $buildName;

    /**
     * setBuildName.
     *
     * @param string $buildName
     */
    public function setBuildName($buildName)
    {
        $this->buildName = $buildName;

        return $this;
    }

    /**
     * getBuildName.
     *
     * @return string
     */
    public function getBuildName()
    {
        return $this->buildName;
    }

    /**
     * configure.
     */
    public function configure()
    {
        // nothing to do
    }
}
