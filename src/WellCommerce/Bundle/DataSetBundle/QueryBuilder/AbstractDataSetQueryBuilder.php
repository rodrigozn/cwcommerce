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

namespace WellCommerce\Bundle\DataSetBundle\QueryBuilder;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Tools\Pagination\Paginator;
use WellCommerce\Bundle\DataSetBundle\Column\ColumnCollection;
use WellCommerce\Bundle\DataSetBundle\Conditions\ConditionInterface;
use WellCommerce\Bundle\DataSetBundle\Conditions\ConditionsCollection;
use WellCommerce\Bundle\CoreBundle\Doctrine\ORM\DataSetAwareRepositoryInterface;

/**
 * Class DataSetQueryBuilder
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
abstract class AbstractDataSetQueryBuilder
{
    /**
     * @var DataSetAwareRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var ColumnCollection
     */
    protected $columns;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $orderBy;

    /**
     * @var string
     */
    protected $orderDir;

    /**
     * Constructor
     *
     * @param DataSetAwareRepositoryInterface $repository
     */
    public function __construct(DataSetAwareRepositoryInterface $repository)
    {
        $this->repository   = $repository;
        $this->queryBuilder = $this->getQueryBuilder();
    }

    /**
     * Returns dataset identifier from repository
     *
     * @return string
     */
    protected function getIdentifier()
    {
        return $this->repository->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->repository->getDataSetQueryBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->queryBuilder->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBy($sort, $order)
    {
        $column         = $this->columns->get($sort);
        $this->orderBy  = ($column->isAggregated()) ? $column->getAlias() : $column->getSource();
        $this->orderDir = $this->normalizeOrderDir($order);
    }

    /**
     * Normalizes order direction
     *
     * @param $order
     *
     * @return string
     */
    private function normalizeOrderDir($order)
    {
        $order = strtolower($order);

        return in_array($order, ['asc', 'desc']) ? $order : 'asc';
    }

    /**
     * {@inheritdoc}
     */
    public function setPagination($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit  = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns(ColumnCollection $columns)
    {
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function setConditions(ConditionsCollection $conditions = null)
    {
        if (null !== $conditions) {
            $paramIteration = 0;
            foreach ($conditions->all() as $condition) {
                $this->addConditionToQuery($condition, $paramIteration++);
            }
        }
    }

    /**
     * Adds conditions as where clauses to query
     *
     * @param ConditionInterface $condition
     * @param int                $paramIteration
     */
    private function addConditionToQuery(ConditionInterface $condition, $paramIteration)
    {
        $column     = $this->columns->get($condition->getIdentifier());
        $source     = $column->getSource();
        $alias      = $column->getAlias();
        $operator   = $condition->getOperator();
        $identifier = sprintf('%s_%s', $condition->getIdentifier(), $paramIteration);

        if ($column->isAggregated()) {
            $expression = $this->queryBuilder->expr()->{$operator}($alias, ':' . $identifier);
            $this->queryBuilder->andHaving($expression);
        } else {
            $expression = $this->queryBuilder->expr()->{$operator}($source, ':' . $identifier);
            $this->queryBuilder->andWhere($expression);
        }

        $this->queryBuilder->setParameter($identifier, $condition->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalRows()
    {
        $paginator = new Paginator($this->getQuery(), true);
        $paginator->setUseOutputWalkers(false);

        return $paginator->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        $this->queryBuilder->select($this->columns->getColumnsSelectClause());
        $this->queryBuilder->setFirstResult($this->offset);
        $this->queryBuilder->setMaxResults($this->limit);
        $this->queryBuilder->addOrderBy(new Expr\OrderBy($this->orderBy, $this->orderDir));

        $query = $this->queryBuilder->getQuery();

        $query->useResultCache(true, 3600, $this->getIdentifier());

        return $query->getArrayResult();
    }
}
