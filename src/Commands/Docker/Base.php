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

namespace TeamNeusta\Magedev\Commands\Docker;

use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Services\DockerService;

/**
 * Class: Base.
 *
 * @see AbstractCommand
 */
class Base extends AbstractCommand
{
    /**
     * @var \TeamNeusta\Magedev\Services\DockerService
     */
    protected $dockerService;

    /**
     * __construct.
     *
     * @param DockerService $dockerService
     */
    public function __construct(DockerService $dockerService)
    {
        parent::__construct();
        $this->dockerService = $dockerService;
    }
}
