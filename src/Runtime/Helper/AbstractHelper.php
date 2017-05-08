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

namespace TeamNeusta\Magedev\Runtime\Helper;

use TeamNeusta\Magedev\Runtime\Runtime;

/**
 * Class AbstractHelper
 */
class AbstractHelper
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Runtime
     */
    protected $runtime;

    /**
     * __construct
     *
     * @param Runtime $runtime
     */
    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }
}
