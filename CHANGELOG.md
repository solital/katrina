# Released Notes

## v2.6.0 - (2024-05-15)

### Added

- Added Yac cache support
- Added new tests and SQL file
- Added new functions: `groupConcat`, `min`, `year`, `round`, 
    `concat`, `ltrim`, `rtrim`, `trim` and `custom`

### Fixed

- Fixed `update` method without `where` method

### Changed

- Changed trait structures

### Removed

- Removed `verifyLogin`, `generate`, `customQuery` and `executeFetchAll` methods

------------------------------------------------------------------------

## v2.5.0 - (2024-04-04)

### Added

- Added `findWithException` method
- Added support to UUID to MySQL

### Fixed

- Fixed connection when sqlite not exists
- Fixed properties names when `$table` and `$id` is null
- Fixed backtips on `DDLTrait`

### Changed

- Changed `executeFetchAll` and `generate` to deprecated
- Changed exceptions on `Katrina` and `Connection` classes

------------------------------------------------------------------------

## v2.4.0 - (2024-02-23)

### Added

- Added multiple database connections
- Added option to set `created_at` and `updated_at` column names
- Added cache adapter to Memcache, Memcached and APCu
- Added `.gitattributes` file

### Fixed

- Fixed `createdUpdatedAt` method name
- Fixed connection in memcached

------------------------------------------------------------------------

## v2.3.1 - (2024-01-20)

### Fixed

- Fixed bug in `$dns` property

------------------------------------------------------------------------

## v2.3.0 - (2023-11-30)

### Added

- Added `latest` method to return latest results
- Added array in `where` method

------------------------------------------------------------------------

## v2.2.0 - (2023-11-03)

### Added

- Added Cache in `all`, `find`, `get` and `getUnique` methods
- Added `CacheException`

------------------------------------------------------------------------

## v2.1.1 - (2023-03-02)

### Fixed

- Fixed `delete` method

------------------------------------------------------------------------

## v2.1.0 - (2022-10-16)

### Added

- Added alias in Traits
- Added `AND` and `OR` methods

### Fixed

- Fixed `select` and `delete` method

------------------------------------------------------------------------

## v2.0.1 - (2022-10-10)

### Fixed

- Fixed exception in `select` method
- Fixed parameters in `limit` method

### Changed

- Changed return of data in `pagination` method

------------------------------------------------------------------------

## v2.0.0 - (2022-05-18)

### Added

- Added `WHERE` and `GROUP BY` method
- Added inner join on multiple tables
- Added typing in variables
- Added `customQuery` method
- Added return in array and object in `build` method
- Added new tests
- Added function support
- Added columns `created_at` and `update_at` by default
- Added methods `onDelete` and `onUpdate`

### Change

- Changed methods in `Connection` class
- Changed `Pagination` class to `PaginationTrait` trait
- Changed ORM structure
- Changed `update` method
- Changed `Build` to `KatrinaStatement`

### Removed

- Removed `Custom` and `TypesTrait`
- Removed `customQueryOnly` and `CustomQueryAll` methods
- Removed `addConstraint` and `change` method in DDLTrait
------------------------------------------------------------------------