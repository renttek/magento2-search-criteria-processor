# SearchCriteriaProcessor for Magento 2

Helper library for processing search M2 SearchCriteria in custom repositories/queries.

[![Latest Stable Version](https://poser.pugx.org/renttek/magento2-search-criteria-processor/version)](https://packagist.org/packages/renttek/magento2-search-criteria-processor)

License: MIT

## Features

This library contains processor classes which will add the following things to queries:

- Filtering (Filters & FilterGroups)
- Sorting
- Pagination
- Automatic joins*

(*kind of)

## Basics

This library brings a set of Processor, which add conditions, limits etc. to an `Magento\Framework\DB\Select` instance.
Every Processor implements `Renttek\SearchCriteriaProcessor\ProcessorInterface`.

There are the following implementations of `Renttek\SearchCriteriaProcessor\ProcessorInterface` are provided:

| Class                          | Description                                                                  |
| ------------------------------ | ---------------------------------------------------------------------------- |
| FilterProcessor                | Applies Filters & FilterGroups as Where-Statements                           |
| SortOrderProcessor             | Applies 1 or multiple SortOrders as OrderBy-Clauses                          |
| LimitProcessor                 | Applies PageSize & CurrentPage as Limit-Offset-Clauses                       |
| JoinProcessor                  | Adds Joins to the query based on used Tables/Fields                          |
| ChainProcessor                 | Takes multiple processors and applies all                                    | 
| DefaultSearchCriteriaProcessor | Applies the default Filter-, SortOrder- & LimitProcessor, Also Accepts Joins |

(Every Processor is in the `Renttek\SearchCriteriaProcessor\` namespace)

The except for the JoinProcessor all processors should be are pretty simple and self-explanatory.


### JoinProcessor

The JoinProcessor extracts used tables from the `Magento\Framework\DB\Select` and adds the joins to the query.

To achieve this, the JoinProcessor takes 2 parameters:

1. A FieldExtractor (`Renttek\SearchCriteriaProcessor\FieldExtractor\FieldExtractorInterface`)
2. A list of Joins (`Renttek\SearchCriteriaProcessor\Join\JoinInterface`)

When processing the SearchCriteria, the processor runs the field extractor to get a list of used tables and fields.
This list is then matched the list of Joins to find an instance which supports the given table.
The Join itself is then added by the Join-Instance.
If no matching Join is found, an `\RuntimeException` is thrown.

List of provided FieldExtractors:

|  Class                  | Description                                         |
| ----------------------- | --------------------------------------------------- |
| FilterFieldExtractor    | Extracts fields & tables from Filter & FilterGroups |
| SortOrderFieldExtractor | Extracts fields & tables from SortOrder             |
| ChainFieldExtractor     | Takes multiple extractors and applies all           |
| DefaultFieldExtractor   | Apples the default Filter- & Sortorder-Extractors   |

(Every Processor is in the `Renttek\SearchCriteriaProcessor\FieldExtractor` namespace)


List of provided Joins:

|  Class                  | Description                     |
| ----------------------- | ------------------------------- |
| LeftJoin                | Adds an Left-Join to the Select |

(Every Join is in the `Renttek\SearchCriteriaProcessor\Join` namespace)


## Usage

In the most simple cases, the DefaultSearchCriteria is all that is needed:

```php
class Bar
{
    // ...
    /** @var DefaultSearchCriteriaProcessor */
    private $searchCriteriaProcessor;

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $select = $this->getBaseSelect();
        $select = $this->searchCriteriaProcessor->process($select, $searchCriteria);

        return $this->fetchResult($select);
    }
    // ...
}
```

If you want to customize the behaviour, simply implement a custom Processor using the ProcessorInterface.

### Joins

To enable automatic Joins you have to use the JoinProcessor (directly or indirectly using the Default- or ChainProcessor)
and provide it with a FieldExtractor (e.g. `Renttek\SearchCriteriaProcessor\FieldExtractor\DefaultFieldExtractor`) and a 
list of of Join-Instances.
This Library currently only provides a LeftJoin class.

The LeftJoin takes 3 parameters:

1. Name of the field in the main-table to join by
2. Name of the foreign table to join
3. Name of the field in the foreign-table to join by


## Installation

Via composer:

```bash
composer require renttek/magento2-search-criteria-processor
```


## Contributing

You want to improve this library, report or even fix a bug? Awesome! Please, do it :)

You got questions? You can reach me here:

- Twitter: [@Renttek92](https://twitter.com/Renttek92)
- E-Mail:  [juliann+github@renttek.de](mailto:juliann+github@renttek.de?subject=Question about SearchCriteriaProcessor)
