<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

use Closure;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class ChainProcessor implements ProcessorInterface
{
    private $processors;

    public function __construct(array $processors)
    {
        array_walk($processors, 'Renttek\SearchCriteriaProcessor\assertImplementsProcessorInterface');
        $this->processors = $processors;
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
}
