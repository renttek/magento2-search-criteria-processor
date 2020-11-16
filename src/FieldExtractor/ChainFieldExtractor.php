<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Closure;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;

class ChainFieldExtractor implements FieldExtractorInterface
{
    /**
     * @var FieldExtractorInterface[]
     */
    private array $fieldExtractors;

    /**
     * @param FieldExtractorInterface[] $fieldExtractors
     */
    public function __construct(array $fieldExtractors)
    {
        $this->setFieldExtractors(...$fieldExtractors);
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

    private function setFieldExtractors(FieldExtractorInterface ...$fieldExtractors): void
    {
        $this->fieldExtractors = $fieldExtractors;
    }
}
