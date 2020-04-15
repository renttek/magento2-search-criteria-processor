<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Closure;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class ChainFieldExtractor implements FieldExtractorInterface
{
    private $fieldExtractors;

    public function __construct(array $fieldExtractors)
    {
        array_walk($fieldExtractors, 'Renttek\SearchCriteriaProcessor\assertImplementsFieldExtractorInterface');
        $this->fieldExtractors = $fieldExtractors;
    }

    public function getFields(SearchCriteriaInterface $searchCriteria): array
    {
        return array_reduce(
            $this->fieldExtractors,
            $this->getReducerFn($searchCriteria),
            []
        );
    }

    private function getReducerFn(SearchCriteriaInterface $searchCriteria): Closure
    {
        return static function (array $fields, FieldExtractorInterface $fieldExtractor) use ($searchCriteria): array {
            return array_merge([], $fields, $fieldExtractor->getFields($searchCriteria));
        };
    }
}
