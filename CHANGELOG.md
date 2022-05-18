# Released Notes

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