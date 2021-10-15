<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class NullJoinProcessor implements ProcessorInterface
{
    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        return $select;
    }
}
