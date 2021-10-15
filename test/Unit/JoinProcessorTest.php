<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use InvalidArgumentException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FieldExtractorInterface;
use Renttek\SearchCriteriaProcessor\Join\JoinInterface;
use Renttek\SearchCriteriaProcessor\JoinProcessor;
use Renttek\SearchCriteriaProcessor\LimitProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class JoinProcessorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    /**
     * @var FieldExtractorInterface|MockObject
     */
    private $fieldExtractorMock;

    /**
     * @var JoinInterface|MockObject
     */
    private $joinMock;

    /**
     * @var JoinProcessor
     */
    private $joinProcessor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fieldExtractorMock = $this->getMockBuilder(FieldExtractorInterface::class)
            ->getMock();

        $this->joinMock = $this->getMockBuilder(JoinInterface::class)
            ->getMock();

        $joins = [
            'join_1' => $this->joinMock
        ];

        $this->joinProcessor = new JoinProcessor($this->fieldExtractorMock, $joins);
    }

    public function testGetsTablesFromAllFieldExtractors(): void
    {
        $this->fieldExtractorMock
            ->expects(self::once())
            ->method('getFields')
            ->with($this->searchCriteriaMock);

        $this->joinProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testMatchesJoinsAgainstAllTables(): void
    {
        $this->fieldExtractorMock
            ->method('getFields')
            ->willReturn([['table-1', 'field-1'], ['table-2', 'field-2']]);

        $this->joinMock
            ->expects(self::exactly(2))
            ->method('supportsTable')
            ->withConsecutive(['table-1'], ['table-2'])
            ->willReturnOnConsecutiveCalls(true, true);

        $this->joinProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testThrowsExceptionIfNoMatchingJoinIsFound(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No matching Join found for table table-1');

        $this->fieldExtractorMock
            ->method('getFields')
            ->willReturn([['table-1', 'field-1']]);

        $this->joinMock
            ->method('supportsTable')
            ->willReturn(false);

        $this->joinProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testAddsMatchingJoinsToSelect(): void
    {
        $this->fieldExtractorMock
            ->method('getFields')
            ->willReturn([['table-1', 'field-1']]);

        $this->joinMock
            ->method('supportsTable')
            ->willReturn(true);

        $this->joinMock
            ->expects(self::once())
            ->method('addJoinToSelect')
            ->with($this->selectMock, ['field-1']);

        $this->joinProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }
}
