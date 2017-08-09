---
layout: default
title: Introduction
---

# Introduction

[![Author][ico-author]][link-author]
[![Source Code][ico-source]][link-source]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

If you have used [Swagger](https://swagger.io/), [JSON Schema](http://json-schema.org/) or [RAML](https://raml.org/) to document your API you have probably used JSON references.  A JSON reference is a JSON object that looks like `{"$ref": "http://some/where"}` and points to a JSON object somewhere else so you don't have to copy and paste it.  It's kinda like a hyperlink for JSON.

JSON References are usually used with JSON Schema and API tooling but it's actually a [separate standard](https://tools.ietf.org/html/draft-pbryan-zyp-json-ref-03).  It's pretty handy any time you are writing a complex JSON document and need to repeat yourself.

JSON Reference is a library for resolving references.  You can use this library to resolve references into proxy objects, allowing you to work with a JSON schema with references like a normal JSON document.  You can also inline referenced JSON and create new JSON documents without references.

- Resolves all references, replacing them with proxy objects.
- Supports references to external files, urls, or custom sources.
- Safely resolves circular references.
- Supports caching dereferenced schemas.
- Dereferenced schemas can be safely encoded with `json_encode`.
- Works with Swagger, JSON Schema, and any other spec compliant JSON documents.

[link-source]: https://github.com/thephpleague/json-reference
[link-author]: https://twitter.com/__yuloh
[link-license]: https://github.com/thephpleague/json-reference/blob/master/LICENSE.md
[link-travis]: https://travis-ci.org/thephpleague/json-reference
[link-scrutinizer]: https://scrutinizer-ci.com/g/thephpleague/json-reference/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/thephpleague/json-reference
[link-docs]: https://github.com/thephpleague/json-reference/tree/gh-pages

[ico-source]: http://img.shields.io/badge/source-league/json--reference-blue.svg?style=flat-square
[ico-author]: http://img.shields.io/badge/author-@__yuloh-blue.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/thephpleague/json-reference/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/thephpleague/json-reference.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/thephpleague/json-reference.svg?style=flat-square
