# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Portale ChiantiBanca - Soci** is a web portal for managing ChiantiBanca's member base (soci). Developed by Alessio Fedi, the system tracks member data, transfers, admissions, deceased members, and financial plafonds.

Version history:
- v1.00 (2020): Initial release
- v2.00 (2021): Unified with former Portale Mutua
- v3.00 (2022): Migration to Sicra system

## Technology Stack

- **Backend**: PHP (no framework)
- **Databases**:
  - MySQL for application data
  - SADAS (ODBC connection) for banking system integration
- **Frontend**: Bootstrap, jQuery, DataTables
- **PDF Generation**: FPDF and TCPDF libraries
- **Charts**: FusionCharts
- **Email**: PHPMailer

## Key Architecture

### Database Connections

All PHP scripts follow this pattern:
1. Include `config/_config.php` for database credentials and `$inizioanno` variable
2. Include `config/_functions.php` for utility functions
3. Connect to MySQL using mysqli
4. Connect to SADAS using ODBC (`odbc_connect('SADAS', NULL, NULL)`)

### Configuration

**`config/_config.php`** contains:
- MySQL connection parameters (`$host`, `$db_user`, `$db_psw`, `$db_name`)
- `$inizioanno` variable: defines the fiscal year start date (format: 'dd/mm/yyyy')
  - **IMPORTANT**: This must be updated at the beginning of each year
  - Used as default value for date filters across the portal
  - Current value: '01/01/2025'

### Core Pages

Main portal files in root directory:

**Member Management:**
- `lista_soci.php` - Complete member listing (Situazione Soci)
- `lista_trasferimenti.php` - Member transfers
- `lista_ammissioni.php` - New member admissions
- `deceduti.php` - Deceased members
- `schedasocio.php` - Individual member record

**Administration:**
- `admin.php` - Main admin interface
- `admin_news.php` - News management
- `admin_cessioni.php` - Transfers administration

**Assemblies:**
- `assemblea2021.php`, `assemblea2022.php` - Assembly management by year

### Date Handling Pattern

Most reporting pages use this pattern for date filtering:

```php
if (!isset($_GET['datain']) OR empty($_GET['datain'])) {
    $_GET['datain'] = $inizioanno;  // Uses config value
}

if (!isset($_GET['dataout']) OR empty($_GET['dataout'])) {
    $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));
    // -1 day because SADAS data is always from previous evening
}
```

**Exception**: `deceduti.php` has a hardcoded date at line 80 that needs manual updating annually.

### Directory Structure

- `config/` - Configuration files
- `function/` - Utility functions and TCPDF library
- `modulistica/` - PDF generation libraries (FPDF, TCPDF, FPDI)
- `routines/` - Helper classes (sql2xls, Multi_Edit, PHPMailer)
- `css/`, `js/`, `img/` - Frontend assets
- `graph/` - FusionCharts library
- `faq/` - FAQ system
- `oauth/` - OAuth integration
- `download/`, `upload/` - File management
- `tmp/`, `log/` - Temporary and log files

## Common Development Tasks

### Updating Fiscal Year

At the start of each year, update the start date in two locations:

1. `config/_config.php` - Update `$inizioanno` variable (line 17)
2. `deceduti.php` - Update hardcoded date (line 80)

Format: 'dd/mm/yyyy' (e.g., '01/01/2026')

### Working with Reports

Reports query data from:
- `sds_soci` - Main member table
- `sds_soci_certificati` - Member certificates
- `sds_soci_daticontatto` - Contact information
- Other tables for historical and consolidated data

### Database Queries

The system uses raw SQL queries through mysqli. No ORM is used. Queries often join multiple tables from both MySQL and SADAS sources.
