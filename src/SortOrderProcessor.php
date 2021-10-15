<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DB\Select;

class SortOrderProcessor implements ProcessorInterface
{
    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        $sortOrders = $searchCriteria->getSortOrders() ?? [];
        $sortOrders = filter($sortOrders, static fn (SortOrder $sortOrder) => $sortOrder->getField() !== null);
        $sortOrders = map(
            $sortOrders,
            static fn (SortOrder $sortOrder) => "{$sortOrder->getField()} {$sortOrder->getDirection()}"
        );

        return $select->order($sortOrders);
    }
}
