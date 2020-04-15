<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class FilterProcessor implements ProcessorInterface
{
    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        $whereParts = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $whereParts[] = $this->getFilterGroupSql($select, $filterGroup);
        }

        if (count($whereParts) > 0) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $select->setPart(Select::WHERE, [implode(' AND ', $whereParts)]);
        }

        return $select;
    }

    private function getFilterGroupSql(Select $select, FilterGroup $filterGroup): string
    {
        $connection = $select->getConnection();
        $groupParts = [];

        foreach ($filterGroup->getFilters() ?? [] as $filter) {
            $value = $filter->getValue();
            $value = !is_string($value) ? (int)$value : $value;

            $groupParts[] = $connection->prepareSqlCondition(
                $filter->getField(),
                [$filter->getConditionType() => $value]
            );
        }

        return sprintf('(%s)', implode(' OR ', $groupParts));
    }
}
