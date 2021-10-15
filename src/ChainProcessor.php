<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Closure;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class ChainProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private array $processors;

    /**
     * ChainProcessor constructor.
     *
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->setProcessors(...array_values($processors));
    }

    public function process(Select $select, SearchCriteriaInterface $searchCriteria): Select
    {
        return array_reduce(
            $this->processors,
            $this->getReducerFn($searchCriteria),
            $select
        );
    }

    private function getReducerFn(SearchCriteriaInterface $searchCriteria): Closure
    {
        return static function (Select $select, ProcessorInterface $processor) use ($searchCriteria): Select {
            return $processor->process($select, $searchCriteria);
        };
    }

    private function setProcessors(ProcessorInterface ...$processors): void
    {
        $this->processors = $processors;
    }
}
