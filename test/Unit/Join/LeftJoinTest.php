<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit\Join;

use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\Join\LeftJoin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LeftJoinTest extends TestCase
{
    /**
     * @var Select|MockObject
     */
    private $selectMock;

    /**
     * @var LeftJoin
     */
    private $leftJoin;

    protected function setUp(): void
    {
        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectMock->method('getPart')
            ->with(Select::FROM)
            ->willReturn(['alias-1' => ['array content omitted']]);

        $this->leftJoin = new LeftJoin('mainTableField', 'foreignTableName', 'foreignTableField');
    }

    public function testSupportsTableReturnsTrueIfTableNameEqualsForeignTableName(): void
    {
        self::assertTrue($this->leftJoin->supportsTable('foreignTableName'));
        self::assertFalse($this->leftJoin->supportsTable('loremIpsum'));
    }

    public function testMainTableNameIsReadFromSelectFromPart(): void
    {
        $this->selectMock
            ->expects(self::once())
            ->method('getPart')
            ->with(Select::FROM);

        $this->leftJoin->addJoinToSelect($this->selectMock, []);
    }

    public function testFieldsArrayIsPassedToSelectJoinMethod(): void
    {
        $fields = ['a', 'foo', 'test'];

        $this->selectMock
            ->expects(self::once())
            ->method('joinLeft')
            ->with(self::anything(), self::anything(), $fields);

        $this->leftJoin->addJoinToSelect($this->selectMock, $fields);
    }

    public function testForeignTableNameIsPassedToSelectJoinMethod(): void
    {
        $this->selectMock
            ->expects(self::once())
            ->method('joinLeft')
            ->with('foreignTableName', self::anything(), self::anything());

        $this->leftJoin->addJoinToSelect($this->selectMock, []);
    }

    public function testJoinConditionIsBuildAndPassedFromConstructorParameters(): void
    {
        /** @var Select|MockObject $selectMock */
        $selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $selectMock->method('getPart')
            ->with(Select::FROM)
            ->willReturn(['mtn' => []]);

        $leftJoin          = new LeftJoin('mtf', 'ftn', 'ftf');
        $expectedCondition = 'mtn.mtf = ftn.ftf';

        $selectMock->expects(self::once())
            ->method('joinLeft')
            ->with(self::anything(), $expectedCondition, self::anything());

        $leftJoin->addJoinToSelect($selectMock, []);
    }
}
