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

namespace TeamNeusta\Magedev\Docker\Helper;

/**
 * Class NameBuilder.
 */
class NameBuilder
{
    /**
     * @var \TeamNeusta\Magedev\Runtime\Config
     */
    protected $config;

    /**
     * __construct.
     *
     * @param \TeamNeusta\Magedev\Runtime\Config $config
     */
    public function __construct(
        \TeamNeusta\Magedev\Runtime\Config $config
    ) {
        $this->config = $config;
    }
    /**
     * buildName.
     *
     * @param string $containerName
     */
    public function buildName($name)
    {
        $projectName = $this->config->get('project_name');
        if ($projectName !== '') {
            $projectName = '-'.$projectName;
        }

        return strtolower('magedev'.$projectName.'-'.$name);
    }
}
