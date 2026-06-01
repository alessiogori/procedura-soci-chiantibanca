# 01 - Architettura, Infrastruttura, Autenticazione e Sicurezza

> Documento tecnico - Portale ChiantiBanca - Soci
> Dominio: Architettura, Infrastruttura, Autenticazione e Sicurezza
> Stato: documentazione del codice esistente (as-is). Le ipotesi non verificabili dal solo codice sono marcate con "(da verificare)".

---

## 1. Panoramica architetturale

Il Portale Soci è un'applicazione **PHP "procedurale" senza framework**: ogni pagina `.php` nella root è un punto di ingresso autonomo che ricostruisce per intero il proprio contesto (connessioni, autenticazione, header HTML) tramite una serie di `include`. Non esiste un front controller, né un router, né un layer applicativo condiviso oltre ai file di `config/` e `css/`.

I dati provengono da due sorgenti distinte:

- **MySQL** (database `soci`), interrogato con l'estensione `mysqli`. Contiene i dati applicativi, le tabelle di staging/consolidamento alimentate da caricamenti periodici (SADAS, Sicra, XLS, Mutua) e le tabelle di servizio (log, password filiali, ecc.).
- **SADAS** (sistema informativo bancario), interrogato in sola lettura via **ODBC** con `odbc_connect('SADAS', NULL, NULL)`. Espone le anagrafiche e i movimenti soci "live" (`TAB_UTENTI`, `SOCI_ANAGRAFICA`, `SOCI_MOVIMENTI`, `ANAG_NAG`, ecc.).

### 1.1 Diagramma a blocchi (layer e connessioni)

```
                         +-------------------------------------------------+
   Browser utente  --->  |  Intranet ChiantiBanca (SSO)                    |
   (rete interna)        |  chiantibanca.worktogether.it/login.asp         |
                         |  -> imposta i cookie usr_id, usr_mail,          |
                         |     filiale_id e rimanda al Portale Soci        |
                         +-------------------------------------------------+
                                              |
                                              | cookie (usr_id, usr_mail, filiale_id)
                                              v
   +--------------------------------------------------------------------------------+
   |  PORTALE SOCI  (server PHP, http://10.197.139.22:8080/soci/)                   |
   |                                                                                |
   |  Pagina .php (es. index.php, lista_*.php, *_auth.php)                          |
   |    1. include config/_config.php      -> credenziali MySQL + $inizioanno      |
   |    2. include config/_functions.php   -> funzioni di utilità + logquery()     |
   |    3. mysqli_connect($host,...)        -> connessione MySQL (var $connection)  |
   |    4. odbc_connect('SADAS', NULL, NULL)-> connessione SADAS  (var $connect)    |
   |    5. include css/main.php             -> AUTENTICAZIONE + <head>/HTML         |
   |    6. include css/menu.php             -> barra di navigazione                 |
   |    7. logica di pagina + query                                                |
   +--------------------------------------------------------------------------------+
            |                                   |                         |
            v                                   v                         v
   +-----------------+              +---------------------+      +-------------------+
   |  MySQL "soci"   |              |  SADAS (ODBC)       |      |  Filesystem       |
   |  (mysqli)       |              |  sola lettura       |      |  tmp/ CSV, log/,  |
   |  tab_log,       |              |  TAB_UTENTI,        |      |  counter/, upload/|
   |  tab_psw,       |              |  SOCI_ANAGRAFICA,   |      +-------------------+
   |  sds_soci, ...  |              |  ANAG_NAG, ...      |
   +-----------------+              +---------------------+
```

Le librerie esterne (FPDF/TCPDF per i PDF, FusionCharts per i grafici, PHPMailer per le email, DataTables/Bootstrap/jQuery per il frontend) vengono incluse on-demand dalle singole pagine che ne hanno bisogno.

---

## 2. Pattern di bootstrap delle pagine

Quasi tutte le pagine "applicative" (con interfaccia utente) ripetono il blocco marcato come **"SEZIONE DA NON MODIFICARE"**. Esempio canonico (da [soci_auth.php](../../soci_auth.php), [direzione_auth.php](../../direzione_auth.php), [index.php](../../index.php)):

```php
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

include("config/_config.php");      // credenziali MySQL + $inizioanno
include("config/_functions.php");   // funzioni di utilità

$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);  // MySQL
$connect    = odbc_connect('SADAS', NULL, NULL) or die ('0');      // SADAS (non sempre presente)

include("css/main.php");            // <head>, JS/CSS  e  AUTENTICAZIONE
include("css/menu.php");            // barra di navigazione
```

Osservazioni:

- L'autenticazione **non** è in un file dedicato chiamato esplicitamente, ma è incorporata in [css/main.php](../../css/main.php). Includere quel file equivale a "proteggere" la pagina.
- L'ordine è significativo: `error_reporting` viene impostato per primo (nasconde i `E_DEPRECATED`), poi si aprono le connessioni, infine si emette l'HTML. Poiché `css/main.php` invia `header()` e `setcookie()`, deve essere incluso prima di qualsiasi output - cosa che il pattern garantisce.
- Gli **script di reportistica/diagnostica** (es. [filiale_check.php](../../filiale_check.php), [_sadas_test.php](../../_sadas_test.php), [stats/_sadas_test.php](../../stats/_sadas_test.php)) usano una variante: NON includono `css/main.php` (quindi **non sono protetti dall'autenticazione**), impostano `ini_set('max_execution_time','0')`, includono `graph/fusioncharts.php` ed emettono HTML minimale proprio.

---

## 3. Modello di autenticazione e autorizzazione

### 3.1 Single Sign-On con l'Intranet

L'identità dell'utente non è gestita dal Portale Soci ma dall'**Intranet aziendale** (`chiantibanca.worktogether.it`). Il meccanismo, implementato in [css/main.php](../../css/main.php), è:

1. Se il cookie `usr_id` **non** è presente, la pagina reindirizza al login Intranet e termina (`exit`):

   ```php
   if (!isset($_COOKIE['usr_id'])){
       header('Location: https://chiantibanca.worktogether.it/login.asp?ReturnUrl=...AppId=14');
       exit;
   }
   ```

   Questo controllo è stato aggiunto il 20240419 (commento `#mz Stop accesso anonimo.`): prima di tale data l'accesso anonimo era possibile.

2. L'Intranet, dopo l'autenticazione, rimanda al portale impostando i cookie. Il file [_auth.php](../../_auth.php) mostra il punto di "atterraggio" che crea i cookie a partire dai parametri GET (`u`, `f`, `e`) quando non già presenti:

   ```php
   if (!isset($_COOKIE['usr_id'])) { $usr_id = ltrim($_GET['u'],'0'); setcookie('usr_id',$usr_id); }
   // analogamente per filiale_id (da $_GET['f']) e usr_mail (da $_GET['e'])
   header("refresh: 1; url = http://10.197.139.22:8080/soci/");
   ```

### 3.2 Cookie utilizzati

| Cookie | Origine | Contenuto | Uso |
|---|---|---|---|
| `usr_id` | Intranet / `_auth.php` (`$_GET['u']`, senza zeri iniziali) | Codice utente numerico | Identificazione su SADAS `TAB_UTENTI`, eccezioni puntuali (es. utente `00390`) |
| `usr_mail` | Intranet / `_auth.php` (`$_GET['e']`) | Email utente | Visualizzazione/identificazione |
| `filiale_id` | Intranet / `_auth.php` (`$_GET['f']`) | Codice filiale **oppure** codice di ruolo speciale | Autorizzazione (chi vede cosa) |
| `user_nomeCookie` | Impostato da `css/main.php` | Nome utente letto da SADAS | Solo visualizzazione |

In assenza di `filiale_id`, `css/main.php` assume `$filiale_id = 1000` (utente generico senza riquadro riservato).

### 3.3 Identificazione utente su SADAS

In [css/main.php](../../css/main.php) il `usr_id` viene usato per recuperare i dati anagrafici dell'operatore dalla tabella SADAS `TAB_UTENTI`:

```php
$select_user = "SELECT * FROM TAB_UTENTI WHERE TAB_UTENTI.COD_USE_NUMERICO = ".$usr_id;
$result_user = odbc_exec($connect, $select_user);
while ($dati_user = odbc_fetch_object($result_user)) {
    $user          = 'LN00'.$usr_id;
    $user_nag      = $dati_user->NAG;
    $user_nome     = $dati_user->NOME_UTENTE;
    $user_mansione = $dati_user->DESCR_MANSIONE_WPROF;
    setcookie('user_nomeCookie',$user_nome);
}
```

`$usr_id` è interpolato direttamente nella query ODBC (vedi sezione "Debito tecnico").

### 3.4 Mappa codici `filiale_id` -> ruolo

L'autorizzazione si basa sul valore del cookie `filiale_id`. I codici "alti" sono ruoli speciali; gli altri sono codici filiale reali. La mappatura emerge da [css/main.php](../../css/main.php) (visualizzazione utente), [index.php](../../index.php) (riquadro riservato), [soci_auth.php](../../soci_auth.php), [direzione_auth.php](../../direzione_auth.php), [area_auth.php](../../area_auth.php), [filiale_auth.php](../../filiale_auth.php).

| `filiale_id` | Ruolo / Profilo | Note dal codice |
|---|---|---|
| `999` | **Admin Ufficio Soci** | Icona `fa-user-cog` arancione. Accesso a `soci_auth.php`, `direzione_auth.php` (con `998`) |
| `998` | **Segreteria Presidenza / Direzione** | Accesso ai riquadri "SOCI" e a `direzione_auth.php`/`soci_auth.php` |
| `997` | **Legale** | Solo visualizzazione profilo (icona gialla); nessun riquadro riservato dedicato in `index.php` (ricade nel ramo "else") (da verificare) |
| `996` | **Controllo di Gestione** | Profilo dedicato; in `direzione_auth.php` autorizzato insieme a `999` |
| `995` | **Aree (Capi Area)** | Gestito da `area_auth.php`; l'area effettiva è derivata dallo `usr_id` via `switch` |
| `100a/100b/100c/100d` | **Centro Imprese (filiale 100)** | Ramo dedicato in `index.php`, mappato su `$filiale = 100` |
| `<= 100` (e diverso da `90`) | **Filiale** (codice filiale reale) | Riquadro "FILIALE" in `index.php`; `90` e `> 100` => "UTENTE NON AUTORIZZATO" in `filiale_auth.php` |
| `90` | Codice escluso | Trattato come non autorizzato |
| `1000` (default) / altri | **Utente generico** | Nessun riquadro riservato ("Non presente per questo Utente") |

> Nota: in `index.php` il test `$filiale_id >= 990` viene usato per discriminare la "Banca" (tutte le filiali) dalla singola filiale negli andamentali. Questo include i ruoli 99x.

### 3.5 Autorizzazione per pagina (esempi)

- [soci_auth.php](../../soci_auth.php): `if ( in_array($filiale_id, array('998','999')) OR in_array($usr_id, array('00390')) )` - altrimenti la pagina non mostra contenuti.
- [direzione_auth.php](../../direzione_auth.php): `if(!in_array($_COOKIE['filiale_id'], array('999','996'))) { ...messaggio "non autorizzato"... } else { ... }`.
- [area_auth.php](../../area_auth.php): `if(in_array($_COOKIE['filiale_id'], array('995')))` e poi `switch ($usr_id)` per assegnare l'area; default => `exit` con "Password errata".
- [filiale_auth.php](../../filiale_auth.php): legge `$idk = $_COOKIE['filiale_id']`, accetta `$_GET['f']` come override, blocca `90` e `> 100`.

Storicamente (codice ora commentato in tutti i file `*_auth.php`) l'accesso avveniva con **password condivise via form POST** (es. `"cicalo"`, `"centineo0283"`, `"coge01"`). Questo sistema è stato sostituito dal controllo basato su cookie ma le password in chiaro restano nei commenti del codice.

### 3.6 Considerazione chiave

**Non esiste una vera sessione applicativa.** L'autorizzazione si fonda esclusivamente sulla fiducia nel valore dei cookie (`usr_id`, `filiale_id`), che sono dati lato client e quindi modificabili dall'utente. Vedi sezione 8.

---

## 4. Gestione delle date

Il fuso temporale dei dati SADAS è "sera precedente", per cui il portale lavora di default fino a "ieri".

- **`$inizioanno`** è definita in [config/_config.php](../../config/_config.php) (attualmente `'01/01/2026'`, formato `dd/mm/yyyy`). Va aggiornata manualmente a inizio anno.
- Pattern di default dei filtri data (presente in molte pagine, es. [filiale_check.php](../../filiale_check.php)):

  ```php
  if (!isset($_GET['datain']) OR empty($_GET['datain']))  { $_GET['datain']  = $inizioanno; }
  if (!isset($_GET['dataout']) OR empty($_GET['dataout'])){ $_GET['dataout'] = date("d/m/Y", strtotime("-1 day")); } // SADAS a ieri sera
  ```

- Alcuni script di test hanno date **hardcoded** (es. [_sadas_test.php](../../_sadas_test.php) e [stats/_sadas_test.php](../../stats/_sadas_test.php) usano `'01/01/2022'`).
- In [index.php](../../index.php) le date di inizio anno sono **interpolate letteralmente** nelle query (`'01/01/2026'` ripetuto), quindi anch'esse vanno aggiornate manualmente a inizio anno (oltre a `deceduti.php` indicato in CLAUDE.md).
- Funzione di conversione: `DATE_TO_MYSQL()` in `_functions.php` trasforma `dd/mm/yyyy` in `yyyy-mm-dd` usando `ereg()` (deprecato).

---

## 5. Funzioni di `config/_functions.php`

File: [config/_functions.php](../../config/_functions.php).

| Funzione | Scopo | Note tecniche / criticità |
|---|---|---|
| `Pulisci($dato)` | Rimuove i doppi apici `"` da una stringa | Sanitizzazione minimale; **non** protegge da SQL injection |
| `DATE_TO_MYSQL($data)` | Converte `dd/mm/yyyy` -> `yyyy-mm-dd` | Usa `ereg()`, **rimosso in PHP 7+**; funziona solo grazie a fallback/poly-fill o PHP datato (da verificare) |
| `Navigazione($id_table)` | Stampa link "torna su" + pulsante "copia tabella" (JS `selectElementContents`) | Emette `<script>` inline; duplicata in `copia()` |
| `copia($id_table)` | Variante di `Navigazione()` con solo il pulsante copia | Duplicazione di codice JS |
| `get_browser_name($user_agent)` | Riconosce il browser dallo user-agent | Euristica basata su `strpos`; solo cosmetica |
| `select_filiale()` | Stampa uno `<select>` con l'elenco **hardcoded** delle filiali | Elenco filiali statico nel codice: va aggiornato a mano se cambiano le filiali |
| `logquery($testo_query)` | Inserisce la query eseguita in `tab_log` (MySQL) | **Apre una propria connessione MySQL con credenziali duplicate in chiaro**; interpola `$testo_query` in una INSERT senza escaping |
| `puliscistringa($stringa)` | Sostituisce lettere accentate e rimuove caratteri non alfanumerici | Usa `preg_replace` con pattern `"[^A-Za-z0-9 ]"` non delimitato correttamente (manca il delimitatore regex) (da verificare comportamento) |
| `diff_date_ingiorni($data1,$data2)` | Differenza in giorni tra due date (formato `Y-m-d`) via `DateTime::diff` | Usata per la regola "domanda ammissione oltre 60 giorni" |

---

## 6. Logging delle query (`tab_log`)

La funzione `logquery()` (in [config/_functions.php](../../config/_functions.php)) implementa una tracciatura applicativa delle query verso MySQL:

```php
function logquery ($testo_query) {
    $dbname='soci'; $dbuser='3qa25raa3f'; $dbpass='8ynDHEuDkMhM63dy'; $dbhost='localhost';
    $connect = mysqli_connect($dbhost,$dbuser,$dbpass) or die(...);
    mysqli_select_db($connect,$dbname) or die(...);
    $ip_provenienza = $_SERVER['REMOTE_ADDR'];
    $data_query     = date('YmdHis');
    $nomefile       = basename($_SERVER['PHP_SELF']);
    $update_log = "INSERT INTO `tab_log`(`id`,`ip`,`data_query`,`testo_query`,`nomefile`)
                   VALUES (null,'".$ip_provenienza."','".$data_query."','".$testo_query."','".$nomefile."')";
    mysqli_query($connect,$update_log);
}
```

Struttura logica della tabella **`tab_log`**: `id`, `ip` (IP chiamante), `data_query` (timestamp `YmdHis`), `testo_query` (la query loggata), `nomefile` (script che ha chiamato). La funzione viene invocata in modo selettivo (es. [filiale_auth.php](../../filiale_auth.php) chiama `logquery($select_psw)`), **non** automaticamente su tutte le query.

### 6.1 Logger ALBA (tracciatura accessi ai dati cliente)

In [lib/loggerALBA.php](../../lib/loggerALBA.php) è presente una libreria **esterna** (autore Massimo Zanini, v1.7) per la produzione del tracciato di log "ALBA" richiesto da BccSi (consultazione anagrafiche/rapporti, campi `NAG`, `RAP`, `MULTIPLO`). È scritta come classe utilizzabile sia dentro CodeIgniter sia standalone, scrive file a larghezza fissa (record da 2151 caratteri) in `E:/Logs/logAlba/`. Allo stato del codice esaminato **non risulta inclusa/istanziata dalle pagine del Portale Soci** (è una libreria predisposta, presumibilmente per integrazione futura o per altri applicativi - da verificare). Esiste anche una versione `.old`.

---

## 7. Integrazione SADAS via ODBC

- Connessione: `$connect = odbc_connect('SADAS', NULL, NULL) or die('0');` - usa un **DSN di sistema** denominato `SADAS` configurato a livello di server (utente/password gestiti dal DSN, qui `NULL`). I commenti indicano l'utenza `ODBCUSER01` creata per SOCI.
- Esecuzione: `odbc_exec($connect, $query)` + iterazione con `odbc_fetch_object()`.
- Tabelle SADAS osservate: `TAB_UTENTI` (utenti/operatori), `SOCI_ANAGRAFICA`, `SOCI_MOVIMENTI`, `ANAG_NAG` (vedi [_sadas_test.php](../../_sadas_test.php), [stats/_sadas_test.php](../../stats/_sadas_test.php)).
- Sintassi SQL SADAS particolare: la concatenazione stringhe usa l'operatore `+` (`INTESTAZIONE_A + ' ' + INTESTAZIONE_B`), tipico di backend non-MySQL.
- Le query SADAS interpolano direttamente input non sanitizzato (es. `WHERE COD_USE_NUMERICO = ".$usr_id`, `DATA_MOVIMENTO >= '".$_GET['datain']."'`).
- Chiusura: `odbc_close($connect)` (es. in coda a [index.php](../../index.php)).

---

## 8. Debito tecnico e criticità di sicurezza

> Le criticità sono ordinate per impatto potenziale sulla sicurezza dei dati (anagrafiche e dati bancari dei soci).

### 8.1 Autorizzazione basata su cookie facilmente falsificabile (CRITICA)

L'intero modello di autorizzazione dipende da `$_COOKIE['usr_id']` e `$_COOKIE['filiale_id']`, valori controllati dal client. Un utente già autenticato (o chiunque possa impostare cookie verso il server) può:

- impostare `filiale_id = 999` per ottenere il profilo **Admin Ufficio Soci**;
- impostare `usr_id = 00390` per accedere a `soci_auth.php`;
- cambiare `filiale_id` per vedere i dati di **qualsiasi altra filiale**.

Non c'è firma, cifratura o validazione server-side dei cookie, né una sessione PHP. File coinvolti: [css/main.php](../../css/main.php), [_auth.php](../../_auth.php), tutti i `*_auth.php`, [index.php](../../index.php).

### 8.2 SQL injection potenziale (CRITICA)

Input da `$_GET` e `$_COOKIE` viene interpolato direttamente nelle query, sia MySQL sia ODBC/SADAS, senza prepared statement né escaping coerente:

- SADAS: `... WHERE COD_USE_NUMERICO = ".$usr_id` ([css/main.php](../../css/main.php)); `... DATA_MOVIMENTO >= '".$_GET['datain']."'` ([_sadas_test.php](../../_sadas_test.php)).
- MySQL: `... WHERE filiale = cast("'.$queryfil.'" as unsigned)` ([filiale_auth.php](../../filiale_auth.php), dove `$queryfil` deriva da `$_GET['f']`); `INSERT INTO tab_log ... '".$testo_query."'` ([config/_functions.php](../../config/_functions.php)).
- In `index.php` `$filiale_id` (cookie) finisce in condizioni SQL come `AND Filiale = '.$filiale_id`.

`Pulisci()` rimuove solo i doppi apici e non è applicata sistematicamente.

### 8.3 Credenziali in chiaro nel repository (CRITICA)

- [config/_config.php](../../config/_config.php): host, utente, password e nome del DB MySQL in chiaro (`$db_psw = "8ynDHEuDkMhM63dy"`).
- [config/_functions.php](../../config/_functions.php): le **stesse** credenziali sono **duplicate** dentro `logquery()`.
- [config/_db_connect_test.php](../../config/_db_connect_test.php): credenziali di un secondo database (`mutua` / `uasdn93n` / `YFYQDQrldfIycbPS`).
- [_t.php](../../_t.php) (in un blocco commentato): credenziali PEC (`soci@pecchiantibanca.it` con password) e logica IMAP.
- Vecchie password applicative in chiaro nei commenti dei file `*_auth.php` (`"cicalo"`, ecc.).

Tutte queste sono versionate in git e quindi recuperabili dalla storia anche se rimosse.

### 8.4 Estensione `mysql_*` deprecata/rimossa (ALTA)

[config/_connessione.php](../../config/_connessione.php) usa `mysql_connect()` / `mysql_error()`, rimossi da PHP 7. Il file [config/mysql-fix.php](../../config/mysql-fix.php) è uno **shim** che ridefinisce le vecchie funzioni `mysql_*` come wrapper su `mysqli_*` (solo se non già definite). Anche [config/_db_connect_test.php](../../config/_db_connect_test.php) dipende dalle `mysql_*`. Questo crea dipendenza fragile dalla disponibilità dello shim e dalla configurazione PHP del server.

### 8.5 `ereg()` deprecato (ALTA)

`DATE_TO_MYSQL()` in [config/_functions.php](../../config/_functions.php) usa `ereg()`, rimosso in PHP 7. Una sua chiamata in PHP moderno produrrebbe un errore fatale; il fatto che il portale funzioni implica un runtime PHP datato o un poly-fill (da verificare l'ambiente reale).

### 8.6 `error_reporting(E_ALL ^ E_DEPRECATED)` (MEDIA)

Tutte le pagine sopprimono i warning di deprecazione. Questo **nasconde** proprio i segnali (`mysql_*`, `ereg`) che indicherebbero l'incompatibilità con PHP moderno, rendendo più difficile l'evoluzione/manutenzione e mascherando potenziali errori a runtime.

### 8.7 Pagine non protette ed endpoint diagnostici esposti (ALTA)

- [config/info.php](../../config/info.php) e [_t.php](../../_t.php) eseguono `phpinfo()`: espongono configurazione del server, percorsi, estensioni, variabili d'ambiente.
- [config/_db_connect_test.php](../../config/_db_connect_test.php), [_sadas_test.php](../../_sadas_test.php), [test.php](../../test.php), [stats/_sadas_test.php](../../stats/_sadas_test.php), [filiale_check.php](../../filiale_check.php) **non** includono `css/main.php`, quindi **non passano dal controllo di autenticazione**: se raggiungibili via URL, eseguono query (anche su dati reali soci) senza alcun controllo.
- [config/_editor.php](../../config/_editor.php) carica TinyMCE da cloud con una API key placeholder.

### 8.8 Charset legacy ISO-8859-1 (BASSA/MEDIA)

[css/main.php](../../css/main.php) dichiara `<meta ... charset=iso-8859-1>`. La funzione `puliscistringa()` rimuove le accentate. La gestione caratteri non è UTF-8: possibili problemi di codifica/mojibake e incoerenze con dati provenienti da SADAS/MySQL (da verificare la collation reale del DB).

### 8.9 Altre note

- **Endpoint con IP/porta hardcoded**: ovunque (menu, redirect) compaiono URL assoluti tipo `http://10.197.139.22:8080/soci/...`; un cambio di host o di rete richiede modifiche diffuse. In `index.php` (commenti) e altrove compaiono anche altri IP (`10.119.192.46`, `10.197.139.22`), segno di migrazioni non completamente ripulite.
- **Scrittura file in `tmp/`**: diversi report generano CSV in `tmp/` con dati anagrafici dei soci (es. `tmp/socipisa.csv`), potenzialmente accessibili via URL (da verificare i permessi della cartella).
- **Connessioni non chiuse coerentemente**: alcune pagine chiudono ODBC, altre no; le connessioni MySQL vengono lasciate alla chiusura automatica.
- **Codice morto / duplicazioni**: ampi blocchi commentati (vecchi sistemi di autenticazione a password, vecchie query) in quasi tutti i file, che aumentano il rumore e il rischio di confusione.

---

## 9. Struttura delle directory di sistema

| Directory | Contenuto / Ruolo |
|---|---|
| `config/` | Configurazione: `_config.php` (credenziali + `$inizioanno`), `_functions.php`, `_connessione.php` (legacy `mysql_*`), `mysql-fix.php` (shim), `_editor.php`, `info.php`/`_db_connect_test.php` (diagnostica) |
| `css/` | `main.php` (head + **autenticazione**), `menu.php` (navigazione), fogli di stile, `old_menu/` (menu storici) |
| `function/` | Funzioni di utilità e libreria TCPDF |
| `lib/` | `loggerALBA.php` (tracciato accessi dati, libreria esterna) |
| `counter/` | Contatore visite (`counter.php`, classe `Esi\SimpleCounter`), log su file in `counter/logs/` |
| `modulistica/` | Librerie PDF (FPDF, TCPDF, FPDI) e modulistica |
| `routines/` | Helper: `sql2xls`, `Multi_Edit`, PHPMailer |
| `graph/` | Libreria FusionCharts (`fusioncharts.php`) |
| `stats/` | Pagine statistiche e di reportistica (alcune non autenticate) |
| `faq/` | Sistema FAQ |
| `oauth/` | Integrazione OAuth (da verificare l'uso effettivo) |
| `download/`, `upload/` | Gestione file in ingresso/uscita |
| `tmp/`, `log/` | File temporanei (CSV generati) e log |
| `news/`, `sondaggi/`, `assemblea/`, `help/`, `video/`, `img/`, `js/`, `sql/`, `old/`, `bin/` | Asset e moduli accessori |
| `docs/` | Documentazione (PDF manuali, statuto, e questa documentazione tecnica/funzionale) |

---

## 10. Riepilogo tabelle/oggetti dati toccati in questo dominio

- **MySQL `soci`**: `tab_log` (logging query), `tab_psw` (password/associazione filiale-area), `sds_soci` (anagrafica soci consolidata), `tab_xls_cessionibanca`, `tab_xls_esclusioni`, `sds_soci_domande`, `sds_soci_domande_nopdf`, `tab_motivazioni`, `tab_comuni_soci_note`, `sds_soci_dati_consolidati`, `TAB_MUTUA`, `tab_ultimo_caricamento` (semafori aggiornamento), `tab_news`.
- **SADAS (ODBC)**: `TAB_UTENTI`, `SOCI_ANAGRAFICA`, `SOCI_MOVIMENTI`, `ANAG_NAG`, `SDS_SOCI` (alcune query usano nomi in maiuscolo).
- **Altro DB MySQL**: `mutua` (solo in `config/_db_connect_test.php`).

> Il dettaglio funzionale delle tabelle anagrafiche soci è trattato in altri documenti del dominio "Gestione Soci".
