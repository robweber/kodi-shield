# Changelog

## [Version 2.5](https://github.com/robweber/kodi-shield/compare/v2.0...v2.5)

### Added
- created an endpoint /downloads that will give total downloads for a specific addon version in a Kodi repo
- added Nexus library comparisons

### Changed
- modifed the url path to generate the badge, it is now ```/version/```
- updated the landing page to include previews for both badge types
- updated placeholder values to show examples

## [Version 2.0](https://github.com/robweber/kodi-shield/compare/v1.0...v2.0)

### Added
- use the [Slim PHP framework](https://www.slimframework.com/) instead of just a basic PHP file. will help expand this to include a few other pages
- added a landing page with preview area
- configurable variables to set domain and path

### Changed
- changed index.php to use Slim Framework, helps with argument parsing

## [Version 1.0](https://github.com/robweber/kodi-shield/compare/v0.1...v1.0)

### Added
- added .htaccess file to redirect all input to index.php
- added use of url path params instead of GET query params
- badge shows red if addon.xml cannot be loaded or parsed, otherwise blue unknown
- when using currentonly param an additional >= modifier is set to show this is the minimum version
- added check if import version actually exists, show unknown if it doesn't
- added imports for other xbmc.* libs

### Changed
- no longer use GET query params (?username=name) and instead use positional params as part of url (/:username/:repo)
- modified README with updated instructions

### Removed
- removed kodi_shield.php for index.php

## [Version 0.1](https://github.com/robweber/kodi-shield/commits/v0.1)

### Added

- added basic README document with some usage examples
- added a changelog document to record changes to this project, format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
- added first working version. Currently only works for xbmc.python addons, others to be added later
