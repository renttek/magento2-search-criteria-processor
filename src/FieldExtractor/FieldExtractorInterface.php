<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FieldExtractorInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array{string, string}[]
     */
    public function getFields(SearchCriteriaInterface $searchCriteria): array;
}
