<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;

class FilterFieldExtractor extends AbstractFieldExtractor implements FieldExtractorInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array{string, string}[]
     */
    public function getFields(SearchCriteriaInterface $searchCriteria): array
    {
        $filterGroups      = $searchCriteria->getFilterGroups() ?? [];
        $filterGroupTables = array_map([$this, 'getFilterGroupTables'], $filterGroups);

        return array_merge([], ...$filterGroupTables);
    }

    /**
     * @param FilterGroup $filterGroup
     *
     * @return array{string, string}[]
     *
     * @SuppressWarnings(PMD.UnusedPrivateMethod)
     */
    private function getFilterGroupTables(FilterGroup $filterGroup): array
    {
        $filters = $filterGroup->getFilters() ?? [];
        $tables  = array_map([$this, 'getFilterTable'], $filters);

        return array_filter($tables);
    }

    /**
     * @param Filter $filter
     *
     * @return array{string, string}|null
     *
     * @SuppressWarnings(PMD.UnusedPrivateMethod)
     */
    private function getFilterTable(Filter $filter): ?array
    {
        return $this->getFieldTable($filter->getField());
    }
}
