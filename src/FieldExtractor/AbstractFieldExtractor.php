<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

abstract class AbstractFieldExtractor
{
    protected function getFieldTable(string $field): ?array
    {
        $fieldParts = explode('.', $field);

        if (count($fieldParts) !== 2) {
            return null;
        }

        return $fieldParts;
    }
}
