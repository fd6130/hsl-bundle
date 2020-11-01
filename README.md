# hsl-bundle

hsl-bundle provide you some nice feature that you might / might not needed:

* maker command for DTOs
* maker command for Transformers
* maker command for API CRUD Controller
* pagination
* vich upload event listener
* lexik custom command for generate RSA keys

Please do take note that all the feature from this bundle are using my preference way / format because the purpose of this bundle is used to ease the development of my project.

Install this on your own risk ya.  :)

**This bundle has nothing related with Discrete Maths. The words 'HSL' is a meme from our 17-B students in Discrete Maths course.**

## Prerequisites

1. PHP 7.2 or above
1. Symfony 5 or above

**Protips:** Refer to suggest section in `composer.json` to see more useful tools.

## Documentation
--------------------
Will update this soon.


## Command at a glance
----------------------
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

Lexik RSA Keys (require LexikJWTAuthenticationBundle)

```
php bin/console lexik:generate-keys
```

