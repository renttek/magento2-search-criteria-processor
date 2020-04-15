<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;

class FilterFieldExtractor extends AbstractFieldExtractor implements FieldExtractorInterface
{
    public function getFields(SearchCriteriaInterface $searchCriteria): array
    {
        $filterGroups      = $searchCriteria->getFilterGroups() ?? [];
        $filterGroupTables = array_map([$this, 'getFilterGroupTables'], $filterGroups);

        return array_merge([], ...$filterGroupTables);
    }

    /**
     * @SuppressWarnings(PMD)
     */
    private function getFilterGroupTables(FilterGroup $filterGroup): array
    {
        $filters = $filterGroup->getFilters() ?? [];
        $tables  = array_map([$this, 'getFilterTable'], $filters);

        return array_filter($tables);
    }

    /**
     * @SuppressWarnings(PMD)
     */
    private function getFilterTable(Filter $filter): ?array
    {
        return $this->getFieldTable($filter->getField());
    }
}
