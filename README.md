# CiviRFM

## Description

This extension implements a [Recency, Frequency, Monetary (RFM) model](https://en.wikipedia.org/wiki/RFM_(market_research)).

The RFM values are calculated from contribution data, according to two configurable settings:

1. RFM time period (years) - only contributions received between "now" and this many years ago are included
2. RFM Financial Type(s) - only contributions of the stated financial types are included; if no types are specified all contributions are included

RFM values can be viewed on a new RFM tab that is added to the tabset of the Contact view page. The values are defined as follows:

- Recency - the number of days since the last eligible contribution
- Frequency - the number of eligible contributions within the RFM period
- Monetary - the average value of eligible contributions within the RFM period

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

After installation carry out the following steps:

1. Configure the extension

Head to `/civicrm/admin/setting/civirfm` and specify the RMF period (whole years) and relevant financial type(s).

2. Create a scheduled job for processing RFM calculation tasks




## Known Issues

None.