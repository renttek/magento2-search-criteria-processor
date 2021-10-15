<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

/**
 * @param array<array{string, string}> $fields
 *
 * @return array<string, array<string>>
 */
function groupFieldsByTables(array $fields): array
{
    $reducer = static function (array $carry, array $array) {
        [$table, $field] = $array;

        $value         = $carry[$table] ?? [];
        $value[]       = $field;
        $carry[$table] = $value;

        return $carry;
    };

    return array_reduce($fields, $reducer, []);
}

/**
 * @param array<mixed> $array
 * @param callable     $filterFn
 *
 * @return array<mixed>
 *
 * @codeCoverageIgnore
 */
function filter(array $array, callable $filterFn): array
{
    return array_filter($array, $filterFn);
}

/**
 * @param array<mixed> $array
 * @param callable     $mapFn
 *
 * @return array<mixed>
 *
 * @codeCoverageIgnore
 */
function map(array $array, callable $mapFn): array
{
    return array_map($mapFn, $array);
}
