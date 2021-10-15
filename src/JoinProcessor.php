<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FieldExtractorInterface;
use Renttek\SearchCriteriaProcessor\Join\JoinInterface;
use RuntimeException;

class JoinProcessor implements ProcessorInterface
{
    private FieldExtractorInterface $fieldExtractor;

    /**
     * @var JoinInterface[]
     */
    private array $joins;

    /**
     * @param FieldExtractorInterface $fieldExtractor
     * @param JoinInterface[]         $joins
     */
    public function __construct(FieldExtractorInterface $fieldExtractor, array $joins)
    {
        $this->fieldExtractor = $fieldExtractor;
        $this->setJoins(...array_values($joins));
    }

    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        $columnsTables  = $this->getTablesFromSelectedColumns($select);
        $criteriaTables = $this->getTablesFromSearchCriteria($searchCriteria);

        $tables = array_merge_recursive($columnsTables, $criteriaTables);
        foreach ($tables as $table => $fields) {
            $join = $this->getMatchingJoin($table);
            if (!$join instanceof JoinInterface) {
                throw new RuntimeException(sprintf('No matching Join found for table %s', $table));
            }

            $join->addJoinToSelect($select, $fields);
        }

        return $select;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array<string, array<string>>
     */
    private function getTablesFromSearchCriteria(SearchCriteriaInterface $searchCriteria): array
    {
        $fields = $this->fieldExtractor->getFields($searchCriteria);

        return groupFieldsByTables($fields);
    }

    /**
     * @return array<string, array<string>>
     */
    private function getTablesFromSelectedColumns(Select $select): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $columns = $select->getPart(Select::COLUMNS);
        if ($columns === null) {
            return [];
        }

        $mainTableName = $this->getMainTableName($select);

        return array_filter(
            groupFieldsByTables($columns),
            static fn (string $table) => $table !== $mainTableName,
            ARRAY_FILTER_USE_KEY
        );
    }

    private function getMainTableName(Select $select): string
    {
        $from = $select->getPart(Select::FROM);
        return (string)array_key_first($from);
    }

    private function getMatchingJoin(string $table): ?JoinInterface
    {
        foreach ($this->joins as $join) {
            if ($join->supportsTable($table)) {
                return $join;
            }
        }

        return null;
    }

    private function setJoins(JoinInterface ...$joins): void
    {
        $this->joins = $joins;
    }
}
