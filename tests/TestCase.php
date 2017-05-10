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

namespace TeamNeusta\Magedev\Test;
use \Mockery as m;

/**
 * Class: StartCommandTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
