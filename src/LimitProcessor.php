<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class LimitProcessor implements ProcessorInterface
{
    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        $pageSize    = $searchCriteria->getPageSize();
        $currentPage = $searchCriteria->getCurrentPage();

        if ($pageSize !== null) {
            $select->limit($pageSize, $currentPage * $pageSize);
        }

        return $select;
    }
}
