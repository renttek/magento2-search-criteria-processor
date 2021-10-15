<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use function count;

abstract class AbstractFieldExtractor
{
    /**
     * @param string $field
     *
     * @return array{string, string}|null
     */
    protected function getFieldTable(string $field): ?array
    {
        $parts = explode('.', $field);

        if (count($parts) !== 2) {
            return null;
        }

        return [$parts[0], $parts[1]];
    }
}
