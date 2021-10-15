<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\ChainProcessor;
use Renttek\SearchCriteriaProcessor\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChainProcessorTest extends TestCase
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
     * @var ProcessorInterface[]|MockObject[]
     */
    private $processorMocks;

    /**
     * @var ChainProcessor
     */
    private $chainProcessor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processorMocks = [
            'processor_1' => $this->getMockBuilder(ProcessorInterface::class)->getMock(),
            'processor_2' => $this->getMockBuilder(ProcessorInterface::class)->getMock(),
            'processor_3' => $this->getMockBuilder(ProcessorInterface::class)->getMock(),
        ];

        $this->chainProcessor = new ChainProcessor($this->processorMocks);
    }

    public function testCallsEveryProcessorWithSelectAndSearchCriteria(): void
    {
        foreach ($this->processorMocks as $processorMock) {
            $processorMock->expects(self::once())
                ->method('process')
                ->with($this->selectMock, $this->searchCriteriaMock)
                ->willReturn($this->selectMock);
        }

        $this->chainProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }
}
