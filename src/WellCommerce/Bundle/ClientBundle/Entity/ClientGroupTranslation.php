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

namespace WellCommerce\Bundle\ClientBundle\Entity;

use Knp\DoctrineBehaviors\Model\Translatable\Translation;
use WellCommerce\Bundle\LocaleBundle\Entity\LocaleAwareInterface;

/**
 * Class ClientGroupTranslation
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class ClientGroupTranslation implements LocaleAwareInterface
{
    use Translation;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCopyingSensitiveProperties() : array
    {
        return [
            'name',
        ];
    }
}
