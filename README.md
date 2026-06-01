# Portale ChiantiBanca – Soci

Portale web per la gestione della **base sociale (soci)** di ChiantiBanca: anagrafica dei soci,
domande di ammissione, sottoscrizione/cessione di azioni, trasferimenti, soci deceduti, plafond
finanziari, statistiche e reportistica direzionale, gestione dell'Assemblea, degli eventi e delle
comunicazioni ai soci.

> Sviluppo e realizzazione originali: **Alessio Fedi**.
> Applicazione interna, destinata alla rete e agli uffici di ChiantiBanca (accesso via Intranet aziendale).

---

## Indice

- [Panoramica](#panoramica)
- [Stack tecnologico](#stack-tecnologico)
- [Architettura in breve](#architettura-in-breve)
- [Profili e accessi](#profili-e-accessi)
- [Domini funzionali](#domini-funzionali)
- [Struttura del repository](#struttura-del-repository)
- [Documentazione](#documentazione)
- [Configurazione e manutenzione](#configurazione-e-manutenzione)
- [Avvertenze di sicurezza](#avvertenze-di-sicurezza)
- [Componenti di terze parti](#componenti-di-terze-parti)
- [Storico versioni](#storico-versioni)

---

## Panoramica

Il portale è un'applicazione **PHP "classica" senza framework**, sviluppata per pagine: ogni pagina
è uno script PHP autonomo che include la configurazione, si connette ai database, verifica l'identità
dell'utente tramite cookie di Intranet e produce direttamente HTML (Bootstrap + jQuery + DataTables).

I dati provengono da due fonti integrate:

- **MySQL** (database `soci`) – dati applicativi, tabelle `sds_*` e `tab_*` ricostruite periodicamente.
- **SADAS** (datawarehouse bancario, via **ODBC**) – anagrafiche, movimenti, certificati, utenti.

Una parte rilevante del sistema è costituita da **processi ETL** (`crea_*.php` schedulati e
`upload/csv2sql_*.php` per i caricamenti manuali) che alimentano le tabelle MySQL a partire da SADAS,
dal gestionale **Sicra** e da estrazioni Isidoc/Excel.

## Stack tecnologico

| Area | Tecnologia |
|------|------------|
| Backend | PHP (nessun framework), procedurale per-pagina |
| Database applicativo | MySQL (`soci`), accesso via `mysqli` |
| Datawarehouse | SADAS via ODBC (`odbc_connect('SADAS', …)`) |
| Frontend | Bootstrap, jQuery, DataTables, FontAwesome, SB Admin 2 |
| Grafici | FusionCharts |
| PDF / Moduli | FPDF, TCPDF, FPDI (+ wkhtmltox) |
| Email / PEC | PHPMailer (e in alcuni punti `mail()` nativo) |
| Moduli accessori | Question2Answer (FAQ), PHP Survey (sondaggi, dismesso) |

## Architettura in breve

Pattern di bootstrap presente in (quasi) ogni pagina:

```php
include("config/_config.php");      // credenziali DB + $inizioanno
include("config/_functions.php");   // funzioni di utilità
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);  // MySQL
$connect    = odbc_connect('SADAS', NULL, NULL);                   // SADAS (ODBC)
include("css/main.php");            // autenticazione + <head> + CSS/JS
include("css/menu.php");            // intestazione e barra di navigazione
```

- **`css/main.php`** è il punto di ingresso dell'autenticazione: se manca il cookie `usr_id`
  reindirizza al login dell'Intranet aziendale (single sign-on), identifica l'utente su SADAS
  `TAB_UTENTI` e prepara l'intestazione in base al ruolo.
- **Date**: i filtri usano `$inizioanno` come data iniziale di default e *ieri* come data finale
  (i dati SADAS sono sempre aggiornati alla sera precedente).

Dettagli completi in [docs/tecnica/01-architettura-infrastruttura.md](docs/tecnica/01-architettura-infrastruttura.md).

## Profili e accessi

L'autorizzazione si basa sul cookie **`filiale_id`** (impostato dall'Intranet):

| `filiale_id` | Profilo |
|--------------|---------|
| `999` | Amministrazione Ufficio Soci |
| `998` | Segreteria Presidenza / Direzione |
| `997` | Legale |
| `996` | Controllo di Gestione |
| `995` | Aree |
| codice filiale (`000`–`075`) | Operatore di filiale (visibilità sulla propria filiale) |

> ⚠️ Non esiste una vera sessione applicativa: l'autorizzazione si fonda sulla fiducia nei cookie.
> Vedi [Avvertenze di sicurezza](#avvertenze-di-sicurezza).

## Domini funzionali

| Dominio | Documentazione funzionale | Documentazione tecnica |
|---------|---------------------------|------------------------|
| Architettura, ruoli e accessi | [funzionale/01](docs/funzionale/01-architettura-ruoli-accessi.md) | [tecnica/01](docs/tecnica/01-architettura-infrastruttura.md) |
| Anagrafica soci e schede | [funzionale/02](docs/funzionale/02-anagrafica-soci.md) | [tecnica/02](docs/tecnica/02-anagrafica-soci.md) |
| Domande, ammissioni, cessioni | [funzionale/03](docs/funzionale/03-domande-ammissioni-cessioni.md) | [tecnica/03](docs/tecnica/03-domande-ammissioni-cessioni.md) |
| Statistiche e reportistica | [funzionale/04](docs/funzionale/04-statistiche-reportistica.md) | [tecnica/04](docs/tecnica/04-statistiche-reportistica.md) |
| Flussi dati / import (ETL) | [funzionale/05](docs/funzionale/05-flussi-dati-import.md) | [tecnica/05](docs/tecnica/05-import-etl.md) |
| Assemblea, eventi, comunicazioni | [funzionale/06](docs/funzionale/06-assemblea-eventi-comunicazioni.md) | [tecnica/06](docs/tecnica/06-assemblea-eventi-news.md) |
| Amministrazione e controlli | [funzionale/07](docs/funzionale/07-amministrazione-controlli.md) | [tecnica/07](docs/tecnica/07-amministrazione-utility.md) |
| Moduli accessori / terze parti | [funzionale/08](docs/funzionale/08-moduli-accessori.md) | [tecnica/08](docs/tecnica/08-componenti-terze-parti.md) |

### Concetti di dominio ricorrenti

- **Azione sociale**: valore unitario **30,33 €**; numero minimo per essere socio **33 azioni**.
- **Plafond**: limite di capitale (riferimento ricorrente **400.000 €**).
- **Termine domande**: una domanda di ammissione oltre **60 giorni** dalla delibera va rigettata
  (calcolo via `diff_date_ingiorni()`).
- **Stati del socio**: attivo, uscito (cessione/esclusione/recesso/decesso), deceduto, deceduto
  presunto, Under 35.

## Struttura del repository

```
soci-chiantibanca/
├── config/              # _config.php (credenziali + $inizioanno), _functions.php, connessioni
├── css/                 # main.php (auth + head), menu.php, fogli di stile, FontAwesome
├── js/                  # jQuery, DataTables, FusionCharts, datepicker, SB Admin 2
├── stats/               # Statistiche, grafici FusionCharts e motore report (stats/rep/)
├── upload/              # Script ETL csv2sql_* (caricamenti CSV → MySQL) + admin_upload.php
├── crea_*.php           # Pipeline ETL da SADAS/Sicra (eseguite da CLI via .cmd)
├── routines/            # Librerie interne: sql2xls, Multi_Edit, PHPMailer, backup DB, CRUD scaffold
├── modulistica/         # Librerie PDF (FPDF, TCPDF, FPDI)
├── faq/                 # Question2Answer 1.8.6 (modulo FAQ, terze parti)
├── sondaggi/            # PHP Survey (modulo sondaggi, dismesso, terze parti)
├── assemblea/           # PDF documenti assembleari per anno (2020, 2021, …)
├── graph/               # FusionCharts
├── docs/                # ► Documentazione tecnica e funzionale (questo progetto) + manuali PDF
├── old/                 # Versioni storiche di pagine (non in uso)
├── lib/                 # loggerALBA (tracciatura accessi)
├── *.php                # Pagine principali del portale (lista_soci, schedasocio, lista_domande, …)
├── README.md
└── CHANGELOG.md
```

## Documentazione

La cartella [`docs/`](docs/) contiene:

- **`docs/tecnica/`** – 8 documenti di analisi tecnica (architettura, modello dati, query, debito
  tecnico) divisi per dominio.
- **`docs/funzionale/`** – 8 documenti di analisi funzionale (processi di business, ruoli, output)
  divisi per dominio.
- Manuali e materiali storici già presenti: `PortaleSoci.pdf`, `manuale_eventi.pdf`,
  `manuale_sib.pdf`, `processi.drawio`, statuto.

## Configurazione e manutenzione

### File di configurazione

`config/_config.php` contiene i parametri di connessione MySQL e la variabile **`$inizioanno`**
(formato `dd/mm/yyyy`), usata come data iniziale di default nei filtri.

### Manutenzione annuale (inizio anno)

Aggiornare la data di inizio anno in **più punti** (il valore non è centralizzato):

1. `config/_config.php` → variabile `$inizioanno`
2. `deceduti.php` → data hardcoded (≈ riga 80)
3. `deceduti_presunti.php` → data hardcoded (≈ riga 80, attualmente disallineata)
4. Verificare eventuali date cablate in `index.php`, `lista_domande_daregolare.php`, `soci_ass*.php`

### Aggiornamento dati

- **Automatico** (notturno, lun–ven): le pipeline `crea_*.php` ripopolano le tabelle MySQL da SADAS.
- **Manuale**: l'Ufficio Soci carica i CSV (estrazioni Sicra/Isidoc/Excel) tramite `admin_upload.php`,
  che invoca gli script `upload/csv2sql_*.php`.

Dettaglio dei flussi in [docs/tecnica/05-import-etl.md](docs/tecnica/05-import-etl.md).

## Avvertenze di sicurezza

> Questa sezione riassume le criticità emerse dall'analisi del codice. Sono **rilievi documentali**:
> vanno valutati e prioritizzati prima di qualsiasi esposizione del portale al di fuori della rete interna.

- **Autorizzazione basata su cookie falsificabili** – impostando lato client `filiale_id=999` (o un
  `usr_id` privilegiato) si ottengono privilegi amministrativi/direzionali. Non c'è sessione server-side.
- **SQL injection diffusa** – parametri `$_GET`/`$_POST`/`$_COOKIE` concatenati direttamente nelle
  query MySQL e SADAS, senza prepared statement (incluse le pipeline di import).
- **Credenziali in chiaro versionate** – `config/_config.php` (duplicate in `logquery()`), credenziali
  PEC e OAuth COMIPA cablate in alcuni file, campi DB esposti come `hidden` HTML negli upload.
- **Password applicative hardcoded** – es. password `cicalo` in `admin_news.php` e in codice commentato.
- **Endpoint diagnostici non protetti** – `config/info.php`/`_t.php` (`phpinfo()`), vari `*_test.php` e
  `check_*` che non includono l'autenticazione; script di **backup/restore DB** e CRUD generici nel webroot.
- **Stack PHP legacy** – uso di `mysql_*` ed `ereg()` (rimossi in PHP 7+), `error_reporting` che nasconde
  i deprecation, charset ISO-8859-1, PHP 5.6 (EOL) sugli host ETL.
- **Dati personali nel repository** – `crea_sds_soci.log` e dati dei soci committati (implicazioni GDPR).
- **Operazioni non atomiche** – `UPDATE` senza `WHERE` su `tab_news`, aggiornamento posti eventi non
  transazionale (rischio overbooking), import ETL senza transazioni/rollback.

## Componenti di terze parti

| Componente | Versione | Stato | Note |
|------------|----------|-------|------|
| Question2Answer (FAQ) | 1.8.6 | Attivo | Linkato dal menu; DB MySQL proprio; plugin `facebook-login` obsoleto |
| PHP Survey (sondaggi) | n/d | Dismesso | Non linkato dal menu; dati su XML; password admin in MD5 |
| FPDF / TCPDF / FPDI | 1.81 / 6.2.26 / – | In uso | Generazione PDF e moduli |
| FusionCharts | – | In uso | Tutti i grafici lato client |
| PHPMailer | 6.8.0 + 5.x | In uso | Presente in più copie, alcune EOL con CVE note |
| jQuery / FontAwesome | 2.2.4 / 5.10.2 | In uso | Versioni datate |
| wkhtmltox | – | Residuo | Deprecato upstream |

Inventario completo in [docs/tecnica/08-componenti-terze-parti.md](docs/tecnica/08-componenti-terze-parti.md).

## Storico versioni

| Versione | Anno | Descrizione |
|----------|------|-------------|
| v1.00 | 2020 | Primo rilascio |
| v2.00 | 2021 | Unificazione con l'ex Portale Mutua |
| v3.00 | 2022 | Passaggio al sistema gestionale Sicra |

Dettaglio in [CHANGELOG.md](CHANGELOG.md).
