# Change Log

## Unreleased

### Added

* Added a `DecoderInterface` and `DecodingException`.  Both extend the original `JsonDecoderInterface` and `JsonDecodingException` for backwards compatibility.
* Added a decoder for YAML.  #18 by @peterpostmann

### Changed

* Performance improvements. Refactored the internals of the Pointer and the JSON Schema scope resolver. #4

### Fixed

* Fixed a bug where paths would be stripped if they matched the fragment.  #7 by @natebrunette
* The test suite now passes on windows. #17 by @peterpostmann
* Fixed a bug where file URIs would not load correctly on windows.  #17 by @peterpostmann

## 1.0.0 - 2017-04-29

Initial release of league/json-reference as a standalone package.
