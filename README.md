# hsl-bundle

hsl-bundle provide you some nice feature that can help you to speed up your development:

- maker command for DTOs (DTOs are use for POST and PUT request)
- maker command for Transformers (customize your result)
- maker command for API CRUD Controller (boilerplate code for your need)
- pagination (it is integrated during the controller creation)

```

## Prerequisites

1. PHP 7.2 or above
1. Symfony 4.4 / 5 (symfony new --full)

**Protips:** Refer to suggest section in `composer.json` to see more useful tools.

## Installation

```
composer require "fd6130/hsl-bundle"
```

## Configuration

Create `config/fd_hsl.yaml` and paste the following content:

```
fd_hsl:
    paginator:
        default_limit: 30
```

## Usage

1. [Pagination](./src/Resources/doc/pagination.md)
1. [Transformer](./src/Resources/doc/transformer.md)
1. [DTO and Mapper](./src/Resources/doc/dto_mapper.md)

## Command at a glance

**Append --help at the end of the command for more options. For example `php bin/console make:hsl:dto --help`**

DTOs

```
php bin/console make:hsl:dto
```

Transformers

```
php bin/console make:hsl:transformer
```

API CRUD Controller (make sure you have Entity, DTO and Transformer)

```
php bin/console make:hsl:crud
```

## Credits

[fd6130](https://github.com/fd6130)

## License

[![License: MIT](https://img.shields.io/badge/License-MIT-red.svg)](LICENSE)
