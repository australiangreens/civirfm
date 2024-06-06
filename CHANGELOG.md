# Changelog
All notable changes for the CiviRFM extension will be noted here.

## [2.0.0] - 2024-06-07

### Changed
The CiviRFM extension now makes use of the [CiviModels](https://github.com/australiangreens/civimodels) extension
for presentation of data on individual contact records. It is not possible to install this extension by itself.

Future work may look to refactor the codebase to enable standalone use. For now however, installation of
the other extension is required. See the CiviModels repository for more information.

- use the CiviModel extension for displaying model data to back office users
- remove page route for RFM tab
- move CiviRFM extension settings menu item under CiviModels menu item

## [1.2.1] - 2023-11-06

### Changed
Fixed a bug in the use of $maxRunTime in the processing of queued
RFM calculation jobs.

## [1.2.0] - 2023-10-31

### Changed
Shifted from civicrm_post() to civicrm_postCommit() hook function to
reduce the risk of deadlocks within a transaction context.

## [1.1.0] - 2023-07-12

### Added
Ability to set a maximum procesing time for the ContactRfm.runqueue action.
Defaults to 600 seconds if parameter `max_runtime` is not supplied to API call.

## [1.0.3] - 2023-07-07

### Changed
Fixed a logic bug in the CRM_Civirfm_Utils::calculateRFM()

## [1.0.2] - 2023-07-03

### Added
Ignore test contributions in calculating RFM values

## [1.0.1] - 2023-07-03

### Added
Human readable titles to RFM queue tasks

### Changed
Fixed a couple of typos

## [1.0.0] - 2023-06-30

### Changed
Explicitly exclude all 0 value contributions from calculations.

## [1.0.0-RC1] - 2023-06-29

Initial release candidate of the CiviRFM extension.
