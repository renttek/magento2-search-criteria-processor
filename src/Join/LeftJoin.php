<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Join;

use Magento\Framework\DB\Select;

class LeftJoin implements JoinInterface
{
    private string $mainTableField;
    private string $foreignTableName;
    private string $foreignTableField;
    private ?string $foreignTableAlias;

    public function __construct(
        string $mainTableField,
        string $foreignTableName,
        string $foreignTableField,
        ?string $foreignTableAlias = null
    ) {
        $this->mainTableField    = $mainTableField;
        $this->foreignTableName  = $foreignTableName;
        $this->foreignTableField = $foreignTableField;
        $this->foreignTableAlias = $foreignTableAlias;
    }

    public function supportsTable(string $table): bool
    {
        return $this->hasAlias()
            ? $this->foreignTableAlias === $table
            : $this->foreignTableName === $table;
    }

    public function addJoinToSelect(Select $select, array $fields): void
    {
        $condition    = $this->getJoinConditionString($select);
        $foreignTable = $this->hasAlias()
            ? [$this->foreignTableAlias => $this->foreignTableName]
            : $this->foreignTableName;

        $select->joinLeft($foreignTable, $condition, $fields);
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

    private function hasAlias(): bool
    {
        return $this->foreignTableAlias !== null;
    }
}
