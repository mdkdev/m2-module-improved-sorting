Mdkdev_ImprovedSorting
=

# Minimum Requirements

PHP: >= 8.1 \
Magento: >= 2.4.4

# Summary

This module allows you to sort category and search page collections that are handles by ElasticSearch or OpenSearch, on
"Best Selling" of "Most Viewed". The sort orders are updated by cron directly in ES/OS to avoid product indexing. In 
addition, this module allows you to set the default sort direction per attribute.

# Why

By default, Magento does not offer the option to sort a product collection on the category or search results page by 
“Best Selling”, or “Most Viewed”. Because the sorts on these pages are controlled by ElasticSearch or OpenSearch, they 
must contain attribute values that can be sorted by. These values need to be updated regularly, but when you update 
this via a product attribute, these products all need to be re-indexed as well, and this can be quite an heavy process.
That is why we would like to avoid this index.

In addition, it is not possible to configure the sorting direction on the category page by default. This ensures that 
all sorts have the same sorting direction, while for example you want to sort "price" ascending, but "Most viewed" 
descending.

# How

To enable sorting by “Most Sold” and “Most Viewed”, a cron expression can be set via the configuration for which the 
attributes are updated in ElasticSearch/OpenSearch. This is done via a direct connection, so that no product attributes 
are updated and no indexing is required. The update performed by the cron also immediately ensures that the cache is 
flushed for the category pages where a change has taken place. This means you no longer have to worry about this.

In addition, the module contains configuration for setting the sorting direction per attribute. In this way you can, 
for example, sort price from low to high, and another attribute from high to low.


## Composer requirement

Add the following to your project's composer.json:
```bash
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mdkdev/m2-module-improved-sorting.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/mdkdev/m2-core.git"
        }
    ]
```

Install the module with the following command:

```bash
composer require mdkdev/module-improved-sorting
```

## Install

```bash
php bin/magento module:enable Mdkdev_ImprovedSorting
php bin/magento setup:upgrade
```

## Usage

- Make sure you setup an extra cron group.
```bash
* * * * * php /absolute/path/to/bin/magento cron:run --group=mdkdev_cron_group
```
- Make sure your catalog is synced with ElasticSearch/OpenSearch. If this is not the case, make sure you run:
```bash
php bin/magento indexer:reindex catalogsearch_fulltext
```
- After installation, you can sync the initial sort attributes by running the following command:
```bash
php bin/magento mdkdev:improved-sorting:update
```
- The default cron expression for updating the "most viewed", and "best sold" attributes in ES/OS, is "0 0 * * *". You
can change this value under Stores > Configuration > Mdkdev > Improved Sorting > General.
- You can set the default sorting direction of the attributes under Stores > Configuration > Mdkdev > Improved Sorting > 
Storefront

## Contributors
1. Marcel de Koning
