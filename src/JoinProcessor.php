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
        $tables = $this->getTablesFromSearchCriteria($searchCriteria);

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
