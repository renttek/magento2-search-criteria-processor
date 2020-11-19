<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DB\Select;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Renttek\SearchCriteriaProcessor\SortOrderProcessor;

class SortOrderProcessorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    private SortOrderProcessor $sortOrderProcessor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $this->selectMock         = $this->createMock(Select::class);

        $this->selectMock
            ->method('order')
            ->willReturn($this->selectMock);

        $sortOrders = [
            $this->getSortOrderMock('field-1', 'ASC'),
            $this->getSortOrderMock('field-2', 'DESC'),
        ];

        $this->searchCriteriaMock
            ->method('getSortOrders')
            ->willReturn($sortOrders);

        $this->sortOrderProcessor = new SortOrderProcessor;
    }

    public function testReadsSortOrdersFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getSortOrders');

        $this->sortOrderProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testJoinsSortFieldsAndDirectionWithSpaceAndPassesToSelect(): void
    {
        $this->selectMock
            ->expects(self::once())
            ->method('order')
            ->with(['field-1 ASC', 'field-2 DESC']);

        $this->sortOrderProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testSortOrdersWithEmptyFieldValueAreIgnored(): void
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchCriteriaMock
            ->method('getSortOrders')
            ->willReturn([
                $this->getSortOrderMock('field-1', 'ASC'),
                $this->getSortOrderMock(null, 'DESC'),
            ]);

        $this->selectMock
            ->expects(self::once())
            ->method('order')
            ->with(['field-1 ASC']);

        $this->sortOrderProcessor->process($this->selectMock, $searchCriteriaMock);
    }

    /**
     * @return SortOrder|MockObject
     * @noinspection PhpDocSignatureInspection
     */
    private function getSortOrderMock(?string $field, string $direction): MockObject
    {
        $mock = $this->getMockBuilder(SortOrder::class)->getMock();
        $mock->method('getField')->willReturn($field);
        $mock->method('getDirection')->willReturn($direction);

        return $mock;
    }
}
