<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Renttek\SearchCriteriaProcessor\assertImplementsInterface;
use function Renttek\SearchCriteriaProcessor\flatMap;
use function Renttek\SearchCriteriaProcessor\groupFieldsByTables;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider flatMapProvider
     */
    public function testFlatMap(array $input, callable $callable, array $expected): void
    {
        $output = flatMap($callable, $input);
        self::assertEquals($output, $expected);
    }

    public function testAssertImplementsInterfaceThrowsExceptionIfObjectIsNoClassInstance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter is expected to be an object, NULL given');
        assertImplementsInterface('Foo', null);
    }

    public function testAssertImplementsInterfaceThrowsExceptionIfObjectDoesNotImplementInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Object does not implement DateTimeInterface');
        assertImplementsInterface(DateTimeInterface::class, new stdClass);
    }

    public function testAssertImplementsInterfaceThrowsNoExceptionIfObjectImplementsInterface(): void
    {
        assertImplementsInterface(DateTimeInterface::class, new DateTime);
        self::assertTrue(true);
    }

    public function testGroupFieldsByTables(): void
    {
        $input    = [
            ['table-1', 'field-1-1'],
            ['table-2', 'field-2-1'],
            ['table-2', 'field-2-2'],
            ['table-1', 'field-1-2'],
            ['table-1', 'field-1-3'],
        ];
        $expected = [
            'table-1' => ['field-1-1', 'field-1-2', 'field-1-3'],
            'table-2' => ['field-2-1', 'field-2-2'],
        ];

        self::assertEquals($expected, groupFieldsByTables($input));
    }

    public function flatMapProvider(): array
    {
        return [
            [
                ['a', 'b', 'c'],
                static function ($x) {
                    return [$x, $x, $x];
                },
                ['a', 'a', 'a', 'b', 'b', 'b', 'c', 'c', 'c'],
            ],
            [
                ['a', 'b', 'c'],
                static function ($x) {
                    return [[$x, $x, $x]];
                },
                [['a', 'a', 'a'], ['b', 'b', 'b'], ['c', 'c', 'c']],
            ],
        ];
    }
}
