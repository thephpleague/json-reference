# JSON Reference

[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Documentation][ico-docs]][link-docs]

Most JSON schemas use JSON references to minimize duplication. A JSON reference is an object that looks like {"$ref": "http://some/where"} and points to a JSON object somewhere else.

JSON Reference is a library for resolving references.

- Resolves all references, replacing them with proxy objects.
- Supports references to external files, urls, or custom sources.
- Safely resolves circular references.
- Supports caching dereferenced schemas.
- Dereferenced schemas can be safely json_encoded.
- Works with Swagger, JSON Schema, and any other spec compliant JSON documents.

## Install

### Via Composer

```bash
composer require league/json-reference
```

## Usage

Complete documentation is available [here](http://json-guard.thephpleague.com/).

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

``` bash
$ composer test-server
$ composer test
```

## Benchmarks

The benchmarks require a local redis server to be running on localhost at the default port.

```bash
$ composer bench
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email matthew.james.allan@gmail.com instead of using the issue tracker.

## Credits

- [Matt Allan][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-travis]: https://travis-ci.org/thephpleague/json-reference
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/json-reference/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/json-reference
[link-docs]: http://json-guard.thephpleague.com/
[link-author]: https://github.com/thephpleague
[link-contributors]: ../../contributors

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/json-reference/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/json-reference.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/json-reference.svg?style=flat-square
[ico-docs]: https://img.shields.io/badge/Docs-Latest-brightgreen.svg?style=flat-square
