<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Join;

use Magento\Framework\DB\Select;

interface JoinInterface
{
    public function supportsTable(string $table): bool;

    public function addJoinToSelect(Select $select, array $fields): void;
}
