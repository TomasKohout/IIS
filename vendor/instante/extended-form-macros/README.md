# Instante Extended Form Macros

[![Build Status](https://travis-ci.org/instante/extended-form-macros.svg?branch=master)](https://travis-ci.org/instante/extended-form-macros)
[![Downloads this Month](https://img.shields.io/packagist/dm/instante/extended-form-macros.svg)](https://packagist.org/packages/instante/extended-form-macros)
[![Latest stable](https://img.shields.io/packagist/v/instante/extended-form-macros.svg)](https://packagist.org/packages/instante/extended-form-macros)

Support for additional Latte form macros:

- {pair}
- {container}
- {group}
- {form.body}
- {form.errors}
- {input.errors}

Overrides built-in Nette form macros to enable advanced features.

Needs form renderer implementing Instante\ExtendedFormMacros\IExtendedFormRenderer,
 for example [Bootstrap 3 renderer](https://github.com/instante/bootstrap3renderer)

## Requirements

- PHP 5.6 or higher
- [Nette Framework](https://github.com/nette/nette) 2.4


## Installation

The best way to install Extended Form Macros is using  [Composer](http://getcomposer.org/):

```sh
$ composer require instante/extended-form-macros:@dev
```

## Usage

See [Documentation](https://github.com/instante/extended-form-macros/blob/master/docs/index.md)
