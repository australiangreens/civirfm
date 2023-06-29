# CiviRFM

## Description

This extension implements a [Recency, Frequency, Monetary (RFM) model](https://en.wikipedia.org/wiki/RFM_(market_research)).

The RFM values are calculated from contribution data, according to two configurable settings:

1. RFM time period (years) - only contributions received between "now" and this many years ago are included
2. RFM Financial Type(s) - only contributions of the stated financial types are included; if no types are specified all contributions are included

![CiviRFM settings](/images/rfmsettings.png)

RFM values can be viewed on a new RFM tab that is added to the tabset of the Contact view page. The values are defined as follows:

- Recency - the number of days since the last eligible contribution
- Frequency - the number of eligible contributions within the RFM period
- Monetary - the average value of eligible contributions within the RFM period

![CiviRFM contact tab](/images/rfmtab.png)

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM 5.57+

## Installation (Web UI)
Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)
Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl civirfm@https://github.com/australiangreens/civirfm/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/australiangreens/civirfm.git
cv en civirfm
```
## Getting Started

After installation carry out the following steps.

### Configure the extension

Head to `/civicrm/admin/setting/civirfm` and specify the RMF period (whole years) and relevant financial type(s).

### Review scheduled jobs for RFM processing

The extension installs two scheduled jobs; one for calculating RFM values and another for finding expired RFM values and
queuing them for recalculation.

The jobs are set to run hourly and daily respectively; you may wish to change these schedules to better suit your requirements.

## How it works

Once configured, the extension queues jobs to calculate RFM values for contacts after relevant contributions are created or updated.

The extension similarly queues calculation jobs when merging contacts if necessary.

The scheduled job `CiviRFM calculation processing` processes these jobs and creates (or updates) RFM records accordingly.

The scheduled job `CiviRFM find expired CiviRFM records` finds expired RFM records and queues them for recalculation

## Technical notes

The extension creates a new entity - ContactRfm - with its own table in the database (`civicrm_contact_rfm`) for storing RFM data.

While scheduled jobs must use CiviCRM's APIv3 framework, the extension provides a complete set of APIv4 actions:

* Contact.calculateRFM - calculate the RFM values for a contact and create (or update) a ContactRfm record 
* ContactRfm.refreshExpired - find and queue expired ContactRfm records for recalculation
* ContactRfm.runqueue - process queued jobs for calculating RFM values

Therefore it's possible to avoid using Scheduled Jobs entirely and use cron jobs or similar to call the APIv4 actions
to deliver all of the extension's functionality.

## Known Issues

The ContactRfm.refreshexpired APIv3 action returns incorrect data in that thevalue of the `count` value inside
the `values` array isn't accurate. The top-level `count` value in the return object is correct however.

The action does perform its intended function regardless.
