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

namespace TeamNeusta\Magedev\Runtime;

/**
 * Class: Application
 */
class Application extends \Symfony\Component\Console\Application
{
    protected static $_logo = <<<LOGO
     __  __    _    ____ _____ ____  _______     __
    |  \/  |  / \  / ___| ____|  _ \| ____\ \   / /
    | |\/| | / _ \| |  _|  _| | | | |  _|  \ \ / /
    | |  | |/ ___ \ |_| | |___| |_| | |___  \ V /
    |_|  |_/_/   \_\____|_____|____/|_____|  \_/
LOGO;

    /**
     * __construct
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct('magedev', '@package_version@');
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getHelp()
    {
        return static::$_logo . "\n\n" . parent::getHelp();
    }
}
