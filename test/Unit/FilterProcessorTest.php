<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\FilterProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FilterProcessorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var MockObject
     */
    private $connectionMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    /**
     * @var Filter[]|MockObject[]
     */
    private $filterMocks;

    /**
     * @var FilterGroup[]|MockObject[]
     */
    private $filterGroupMocks;

    /**
     * @var FilterProcessor
     */
    private $filterProcessor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->filterMocks = [
            $this->getFilterMock('field-1', 'value-1', 'eq'),
            $this->getFilterMock('field-2', 'value-2', 'neq'),
            $this->getFilterMock('field-3', 'value-3', 'eq'),
            $this->getFilterMock('field-4', 'value-4', 'neq'),
        ];

        $this->filterGroupMocks = [
            $this->getFilterGroupMock([$this->filterMocks[0], $this->filterMocks[1]]),
            $this->getFilterGroupMock([$this->filterMocks[2], $this->filterMocks[3]]),
        ];

        $this->searchCriteriaMock
            ->method('getFilterGroups')
            ->willReturn($this->filterGroupMocks);

        $this->connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->getMock();

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectMock
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->filterProcessor = new FilterProcessor;
    }

    public function testGetsGroupsFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getFilterGroups');

        $this->filterProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testGetsFiltersFromFilterGroups(): void
    {
        foreach ($this->filterGroupMocks as $filterGroupMock) {
            $filterGroupMock->expects(self::once())
                ->method('getFilters');
        }

        $this->filterProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testPassedField(): void
    {
        $this->connectionMock
            ->expects(self::exactly(4))
            ->method('prepareSqlCondition')
            ->withConsecutive(
                ['field-1', self::anything()],
                ['field-2', self::anything()],
                ['field-3', self::anything()],
                ['field-4', self::anything()]
            );

        $this->filterProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testPassedValueAndConditionType(): void
    {
        $this->connectionMock
            ->expects(self::exactly(4))
            ->method('prepareSqlCondition')
            ->withConsecutive(
                [self::anything(), ['eq' => 'value-1']],
                [self::anything(), ['neq' => 'value-2']],
                [self::anything(), ['eq' => 'value-3']],
                [self::anything(), ['neq' => 'value-4']]
            );

        $this->filterProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testJoinsSqlConditionsFromOneGroupWithOrAndFilterGroupsWithAnd(): void
    {
        $this->connectionMock
            ->method('prepareSqlCondition')
            ->willReturnOnConsecutiveCalls('sql-1', 'sql-2', 'sql-3', 'sql-4');

        $this->selectMock
            ->expects(self::once())
            ->method('setPart')
            ->with(Select::WHERE, ['(sql-1 OR sql-2) AND (sql-3 OR sql-4)']);

        $this->filterProcessor->process($this->selectMock, $this->searchCriteriaMock);
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
