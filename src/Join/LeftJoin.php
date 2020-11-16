<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Join;

use Magento\Framework\DB\Select;

class LeftJoin implements JoinInterface
{
    private string $mainTableField;
    private string $foreignTableName;
    private string $foreignTableField;

    public function __construct(string $mainTableField, string $foreignTableName, string $foreignTableField)
    {
        $this->mainTableField    = $mainTableField;
        $this->foreignTableName  = $foreignTableName;
        $this->foreignTableField = $foreignTableField;
    }

    public function supportsTable(string $table): bool
    {
        return $this->foreignTableName === $table;
    }

    public function addJoinToSelect(Select $select, array $fields): void
    {
        $condition = $this->getJoinConditionString($select);
        $select->joinLeft($this->foreignTableName, $condition, $fields);
    }

    protected function getJoinConditionString(Select $select): string
    {
        return sprintf(
            '%s.%s = %s.%s',
            $this->getMainTableName($select),
            $this->mainTableField,
            $this->foreignTableName,
            $this->foreignTableField
        );
    }

    private function getMainTableName(Select $select): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $fromPart = $select->getPart(Select::FROM);
        return array_key_first($fromPart);
    }
}
