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

namespace TeamNeusta\Magedev\Docker\Container\Repository;

use TeamNeusta\Magedev\Docker\Container\AbstractContainer;

class Mailcatcher extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'mailcatcher';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        return 'schickling/mailcatcher';
    }
}
