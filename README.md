# TYPO3 Extending extbase domain models

![build](https://github.com/evoWeb/extender/workflows/build/badge.svg?branch=develop)
[![Latest Stable Version](https://poser.pugx.org/evoweb/extender/v/stable)](https://packagist.org/packages/evoweb/extender)
[![Monthly Downloads](https://poser.pugx.org/evoweb/extender/d/monthly)](https://packagist.org/packages/evoweb/extender)
[![Total Downloads](https://poser.pugx.org/evoweb/extender/downloads)](https://packagist.org/packages/evoweb/extender)

## Installation

### via Composer

The recommended way to install TYPO3 Console is by using [Composer](https://getcomposer.org):

    composer require evoweb/extender

### quick introduction

Add the extending classname to your packages Services.yaml and add a tag to it.
The tag must contain the name 'extender.extends' and the class it is extending.

Services.yaml
```yaml
services:

  Fixture\ExtendingExtension\Domain\Model\BlobExtend:
    tags:
      -
        name: 'extender.extends'
        class: Fixture\BaseExtension\Domain\Model\Blob
```
