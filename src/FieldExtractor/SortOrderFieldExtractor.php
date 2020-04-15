<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;

class SortOrderFieldExtractor extends AbstractFieldExtractor implements FieldExtractorInterface
{
    public function getFields(SearchCriteriaInterface $searchCriteria): array
    {
        $sortOrders = $searchCriteria->getSortOrders() ?? [];
        $tables = array_map([$this, 'getSortOrderTable'], $sortOrders);

        return array_filter($tables);
    }

    /**
     * @SuppressWarnings(PMD)
     */
    private function getSortOrderTable(SortOrder $sortOrder): ?array
    {
        return $this->getFieldTable($sortOrder->getField());
    }
}
