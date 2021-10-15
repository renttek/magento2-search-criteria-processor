<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit\FieldExtractor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FilterFieldExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FilterFieldExtractorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var FilterGroup[]|MockObject[]
     */
    private $filterGroupMocks;

    /**
     * @var Filter[]|MockObject[]
     */
    private $filterMocks;

    /**
     * @var FilterFieldExtractor
     */
    private $filterFieldExtractor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->filterMocks = [
            $this->getFilterMock('table-1.field-1', 'value-1', 'eq'),
            $this->getFilterMock('table-2.field-2', 'value-2', 'neq'),
        ];

        $this->filterGroupMocks = [
            $this->getFilterGroupMock([$this->filterMocks[0]]),
            $this->getFilterGroupMock([$this->filterMocks[1]]),
        ];

        $this->searchCriteriaMock
            ->method('getFilterGroups')
            ->willReturn($this->filterGroupMocks);

        $this->filterFieldExtractor = new FilterFieldExtractor();
    }

    public function testGetsFilterGroupsFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getFilterGroups');

        $this->filterFieldExtractor->getFields($this->searchCriteriaMock);
    }

    public function testGetsFiltersFromFilterGroups(): void
    {
        foreach ($this->filterGroupMocks as $filterGroupMock) {
            $filterGroupMock->expects(self::once())
                ->method('getFilters');
        }

        $this->filterFieldExtractor->getFields($this->searchCriteriaMock);
    }

    public function testGetsFiltersFieldsFromFilters(): void
    {
        foreach ($this->filterMocks as $filterMocks) {
            $filterMocks->expects(self::once())
                ->method('getField');
        }

        $this->filterFieldExtractor->getFields($this->searchCriteriaMock);
    }

    public function testReturnsTablesOfAllFilters(): void
    {
        $expected = [['table-1', 'field-1'], ['table-2', 'field-2']];
        $actual   = $this->filterFieldExtractor->getFields($this->searchCriteriaMock);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param Filter[]|MockObject[] $filters
     *
     * @return FilterGroup|MockObject
     */
    private function getFilterGroupMock(array $filters): MockObject
    {
        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filterGroupMock->method('getFilters')->willReturn($filters);

        return $filterGroupMock;
    }

    /**
     * @return Filter[]|MockObject
     */
    private function getFilterMock(string $field, string $value, string $conditionType): MockObject
    {
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filterMock->method('getField')->willReturn($field);
        $filterMock->method('getValue')->willReturn($value);
        $filterMock->method('getConditionType')->willReturn($conditionType);

        return $filterMock;
    }
}
