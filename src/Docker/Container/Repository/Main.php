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

/**
 * Class: Main.
 *
 * @see AbstractContainer
 */
class Main extends AbstractContainer
{
    /**
     * getName.
     */
    public function getName()
    {
        return 'main';
    }

    /**
     * getImage.
     */
    public function getImage()
    {
        return $this->imageFactory->create('Main');
    }

    /**
     * getConfig.
     */
    public function getConfig()
    {
        $homePath = $this->config->get('home_path');
        $projectPath = $this->config->get('project_path');

        $binds = [
            $projectPath.':/var/www/html:rw',
            $homePath.'/.composer:/var/www/.composer:rw', // TODO: check for existence?
            $homePath.'/.ssh:/var/www/.ssh:rw',
        ];

        if ($this->config->optionExists('modules_path')) {
            $modulesPath = $this->config->get('modules_path');
            $binds[] = $modulesPath . ':' . $modulesPath;
        }

        // TODO: make this configurable ?
        $this->setBinds($binds);

        $config = parent::getConfig();

        return $config;
    }
}
