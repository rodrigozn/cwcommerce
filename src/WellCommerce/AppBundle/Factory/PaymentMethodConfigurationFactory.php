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

namespace WellCommerce\AppBundle\Factory;

use WellCommerce\CoreBundle\Factory\AbstractFactory;
use WellCommerce\AppBundle\Entity\PaymentMethodConfiguration;

/**
 * Class PaymentMethodConfigurationFactory
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class PaymentMethodConfigurationFactory extends AbstractFactory
{
    /**
     * @return \WellCommerce\AppBundle\Entity\PaymentMethodConfigurationInterface
     */
    public function create()
    {
        $paymentMethodConfiguration = new PaymentMethodConfiguration();

        return $paymentMethodConfiguration;
    }
}