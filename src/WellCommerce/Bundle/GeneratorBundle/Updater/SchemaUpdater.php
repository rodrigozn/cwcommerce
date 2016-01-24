<?php
/*
 * WellCommerce Open-Source E-Commerce Platform
 * 
 * This file is part of the WellCommerce package.
 *
 * (c) Adam Piotrowski <adam@wellcommerce.org>
 * 
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace WellCommerce\Bundle\GeneratorBundle\Updater;

use WellCommerce\Bundle\CoreBundle\DependencyInjection\AbstractContainerAware;

/**
 * Class SchemaUpdater
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class SchemaUpdater extends AbstractContainerAware
{
    public function execute()
    {
        $kernel      = $this->container->get('kernel');
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'doctrine:schema:update', '--force' => true];
        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}