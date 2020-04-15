<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use InvalidArgumentException;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FieldExtractorInterface;
use Renttek\SearchCriteriaProcessor\Join\JoinInterface;

/**
 * @codeCoverageIgnore
 */
function assertImplementsProcessorInterface($object): void
{
    assertImplementsInterface(ProcessorInterface::class, $object);
}

/**
 * @codeCoverageIgnore
 */
function assertImplementsFieldExtractorInterface($object): void
{
    assertImplementsInterface(FieldExtractorInterface::class, $object);
}

/**
 * @codeCoverageIgnore
 */
function assertImplementsJoinInterface($object): void
{
    assertImplementsInterface(JoinInterface::class, $object);
}

function assertImplementsInterface(string $interface, $object): void
{
    if (!is_object($object) || !is_string(get_class($object))) {
        $message = sprintf('Parameter is expected to be an object, %s given', gettype($object));
        throw new InvalidArgumentException($message);
    }

    $interfaces = class_implements($object);
    $implementsInterface = in_array($interface, $interfaces, true);

    if (!$implementsInterface) {
        $message = sprintf('Object does not implement %s', $interface);
        throw new InvalidArgumentException($message);
    }
}

function flatMap(callable $fn, $array): array
{
    return array_merge(...array_map($fn, $array));
}

function groupFieldsByTables(array $fields)
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
