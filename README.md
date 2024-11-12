# LupaSearch Magento 2 MSI Plugin

## Requirements

- **PHP**: >=7.4
- **Magento 2**: compatible with Magento 2.2.x - 2.4.x

## Installation

Install with composer

```shell
composer require lupasearch/magento2-lupasearch-plugin-msi
```

Enable module

```shell
php bin/magento module:enable LupaSearch_LupaSearchPluginMSI
```

Run install scripts

```shell
php bin/magento setup:upgrade
```

Run compile scripts

```shell
php bin/magento setup:di:compile
```

Change Indexer Mode to "On Schedule" (only works on this mode)

```shell
bin/magento indexer:set-mode schedule lupasearch_source_item
```

Configurations:

```
Stores -> Configuration -> Catalog -> LupaSearch
```

Run indexer

```shell
bin/magento indexer:reindex lupasearch_source_item
```

## LupaSearch docs

https://api.lupasearch.com/docs/#/

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).
