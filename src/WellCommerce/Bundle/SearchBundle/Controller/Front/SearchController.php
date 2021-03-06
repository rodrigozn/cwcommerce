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

namespace WellCommerce\Bundle\SearchBundle\Controller\Front;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use WellCommerce\Bundle\CoreBundle\Controller\Front\AbstractFrontController;
use WellCommerce\Bundle\SearchBundle\Manager\IndexManager;
use WellCommerce\Bundle\SearchBundle\Manager\SearchManagerInterface;
use WellCommerce\Bundle\SearchBundle\Query\SearchQuery;
use WellCommerce\Component\Breadcrumb\Model\Breadcrumb;
use WellCommerce\Component\DataSet\Conditions\ConditionsCollection;

/**
 * Class SearchController
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class SearchController extends AbstractFrontController
{
    public function indexAction(string $phrase) : Response
    {
        $this->search($phrase);

        $this->getBreadcrumbProvider()->add(new Breadcrumb([
            'label' => $this->trans('search.heading.index')
        ]));

        $this->getBreadcrumbProvider()->add(new Breadcrumb([
            'label' => $phrase
        ]));

        return $this->displayTemplate('index', [
            'phrase' => $phrase,
        ]);
    }
    
    public function viewAction(string $phrase) : JsonResponse
    {
        if (strlen($phrase) < $this->container->getParameter('search_term_min_length')) {
            $liveSearchContent = '';
        } else {
            $this->search($phrase);

            $dataset    = $this->get('search.dataset.front');
            $conditions = new ConditionsCollection();
            $conditions = $this->get('layered_navigation.helper')->addLayeredNavigationConditions($conditions);
            
            $products = $dataset->getResult('array', [
                'limit'      => 20,
                'page'       => 1,
                'order_by'   => 'score',
                'order_dir'  => 'asc',
                'conditions' => $conditions,
            ]);

            $liveSearchContent = $this->renderView('WellCommerceSearchBundle:Front/Search:view.html.twig', [
                'dataset' => $products,
            ]);
        }
        
        return $this->jsonResponse([
            'liveSearchContent' => $liveSearchContent
        ]);
    }

    protected function search(string $phrase) : array
    {
        return $this->getIndexManager()->search(new SearchQuery($phrase));
    }

    protected function getIndexManager() : IndexManager
    {
        return $this->get('product.index.manager');
    }
}
