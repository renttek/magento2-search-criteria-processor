<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class SortOrderProcessor implements ProcessorInterface
{
    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        $sort = [];

        foreach ($searchCriteria->getSortOrders() ?? [] as $sortOrder) {
            $sort[] = "{$sortOrder->getField()} {$sortOrder->getDirection()}";
        }

        return $select->order($sort);
    }
}
