# 07 - Amministrazione, Monitoraggio, Controlli/Quadrature, Zone di Competenza, Mail e Utility (documento tecnico)

> Portale ChiantiBanca - Soci. Stack: PHP senza framework, MySQL `soci` (mysqli) + SADAS via ODBC, Bootstrap/jQuery/DataTables, PHPMailer.
> Documento riferito al dominio amministrativo e di supporto: gestione utenti/password, monitoraggio segnalazioni socio, controlli e quadrature, zone di competenza territoriale, comunicazioni via email/PEC, utility e librerie interne `routines/`.

Documenti correlati:
- [Indice tecnico](./README.md) *(da verificare se presente)*
- [Documento funzionale del dominio](../funzionale/07-amministrazione-controlli.md)

---

## Pattern di bootstrap comune

Quasi tutte le pagine seguono lo schema:

```php
error_reporting(E_ALL ^ E_DEPRECATED);   // talvolta seguito da error_reporting(0)
include("config/_config.php");           // $host, $db_user, $db_psw, $db_name, $inizioanno
include("config/_functions.php");        // logquery(), select_filiale(), puliscistringa(), ...
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
$connect = odbc_connect('SADAS', NULL, NULL); // solo dove serve SADAS
include("css/main.php");
include("css/menu.php");
```

L'autenticazione è basata su cookie impostati altrove (`_auth.php`): `filiale_id`, `usr_id`, `usr_mail`. Il valore `filiale_id = 999` identifica l'**Ufficio Soci/Admin**; `997`, `998`, `999` sono i livelli con accesso esteso (es. monitor).

### Funzioni di supporto rilevanti (`config/_functions.php`)

| Funzione | Scopo |
|----------|-------|
| `logquery($testo_query)` | Inserisce in `tab_log` (`id, ip, data_query, testo_query, nomefile`) la query eseguita, l'IP del client e lo script chiamante. Apre una **connessione MySQL propria** con credenziali hardcoded. |
| `select_filiale()` | Genera la select HTML con l'elenco filiali (codici 000..075). |
| `get_browser_name($ua)` | Riconosce il browser dallo `User-Agent` (usato per scegliere estensione immagini jpg/png). |
| `puliscistringa($s)` | Normalizza accenti e caratteri non alfanumerici (per nomi file / CSV). |

---

## 1. Amministrazione e utenti

### `admin.php` - Admin Center
- **Scopo**: dashboard di lancio per l'Ufficio Soci. Raggruppa pulsanti/link verso funzioni e applicativi esterni (aggiornamento tabelle SADAS->MySQL `crea_sds_soci.php`, caricamento file `admin_upload.php`, WebMail PEC Actalis, invio flussi CDA, protocollo, news, modulistica DMX, biglietteria, gestione eventi `routines/basic/index.php`).
- **Controllo accesso**: `if ($_COOKIE['filiale_id'] != 999)` -> messaggio "Area riservata" e blocco del contenuto. (Il vecchio gate a password `$_POST['psw'] == "cicalo"` è commentato.)
- **Parametri**: nessuno significativo (pagina statica con link).
- **Debito tecnico rilevante**:
  - **Credenziali in chiaro nel markup**: titoli/tooltip dei link contengono utenze e password di sistemi esterni (PEC Actalis `soci@pecchiantibanca.it`, biglietteria Fiorentina, protocollo `u=asoci/p=asoci` inviati come campi hidden in POST).
  - Link "in incognito per fare test" con email e codici filiale/utente reali (`_auth.php?f=...&u=...&e=...`): bypass dell'autenticazione di fatto documentato in pagina.

### `admin_psw.php` - Gestione password filiali (lista)
- **Scopo**: elenca da `tab_psw` (`filiale, desc_filiale, email, psw`) le credenziali di accesso per filiale; per ogni riga link a `admin_psw_edit.php` e link `mailto:` precompilato per inviare la password alla filiale.
- **Tabella**: `tab_psw`. Query loggata con `logquery()`.
- **Debito tecnico**: la **password è in chiaro** nel database e mostrata a video (`<b>".$datipsw['psw']."</b>`). Nessun controllo `filiale_id` in cima alla pagina (header del file ancora "ChiantiMutua 2019"). Il `mailto` ha CC hardcoded `direzione@chiantimutua.it`.

### `admin_psw_edit.php` - Modifica password filiale
- **Scopo**: form di edit (GET `azione=change`) e UPDATE su `tab_psw` (POST).
- **Parametri**: GET `azione`, `filiale`, `psw`, `descfiliale`; POST `filiale`, `psw`.
- **Query**: `UPDATE tab_psw SET psw = '$psw' WHERE filiale = '$filiale'` (loggata).
- **Debito tecnico**: **SQL injection** diretta (variabili POST concatenate senza escape); nessun controllo di accesso; usa `mysql_error()` (API obsoleta) e mostra all'utente "non tenere conto dell'errore riportato qui sopra".

### `admin_upload.php` - Area upload (cenno)
- **Scopo**: tabella di stato dei caricamenti ETL letta da `tab_ultimo_caricamento` (`fonte, descrizione, tipo, cadenza, caricamento, nascosto`); ogni riga linka allo script `upload/csv2sql_<fonte>.php`.
- **Nota**: la logica ETL effettiva è trattata da altro dominio (caricamento/ETL). Qui rilevano solo: assenza di controllo accesso e link diretti agli script di import.

---

## 2. Monitoraggio (segnalazioni/note sul socio)

Il "Monitor Socio" è un registro di **segnalazioni/pratiche legate al singolo socio** (contestazioni, solleciti, reclami, richieste informazioni), con esito e workflow di chiusura. Tabella: **`tab_monitor_soci`**.

Colonne note: `id, cag, tipologia, data_ricezione, forma_ricezione, amezzodi, descrizione, esito, data_esito, status_esito, note, segnalato_a, data_segnalazione, attivo, riservato`.

### `monitor_lista.php`
- **Scopo**: elenco delle segnalazioni attive (`attivo='S'`) per un dato socio; esporta un CSV in `tmp/monitorsocio.csv`; link a "Nuova Segnalazione" e Edit/Disattiva.
- **Parametri (GET)**: `cag`, `nominativo`.
- **Controllo accesso**: `if (!in_array($_COOKIE['filiale_id'], array('997','998','999')))` -> blocco. (Gate a password "legale" commentato.)
- **Query**: `SELECT ... FROM tab_monitor_soci WHERE cag = cast($cag as unsigned) AND attivo='S' ORDER BY id desc`.
- **Note**: `status_esito` mappato su icone (verde/rosso/giallo). Vista ad accordion (descrizione/esito/note collassabili).
- **Debito tecnico**: `$cag`/`$nominativo` interpolati nei link e in query; `error_reporting(0)` nasconde errori; nel CSV vengono usate `$descrizione/$esito/$note` non popolate (bug residuo dovuto a `html_entity_decode` commentato).

### `monitor_new.php`
- **Scopo**: form di inserimento nuova segnalazione (GET `tipo=new`) e INSERT (POST `tipo=insert`).
- **Parametri**: GET `cag`, `nominativo`; POST `Tipologia, dataricezione, Forma, amezzodi, descrizione, esito, dataesito, Status, note, segnalato_a, datasegnalazione, cag`.
- **Query**: `INSERT INTO tab_monitor_soci (...) VALUES (...)` con `attivo='S'`, `riservato='N'`.
- **Mitigazione parziale**: `descrizione/esito/note` passati da `mysqli_real_escape_string(...htmlspecialchars(...))`; gli **altri campi restano interpolati** (injection sul resto del payload).

### `monitor_edit.php`
- **Scopo**: edit (GET `tipo=edit`), disattivazione logica (GET `tipo=off` -> `attivo='N'`) e UPDATE (POST `tipo=update`).
- **Parametri**: GET `id`, `cag`, `nominativo`, `tipo`.
- **Query**: `SELECT * ... WHERE id=$id`, `UPDATE tab_monitor_soci SET attivo='N' WHERE id=$id`, `UPDATE ... SET ... WHERE id=$id`.
- **Debito tecnico**: `$_GET['id']` e `$_POST['id']` concatenati in SQL senza cast; rami `var_dump($_GET)` residui di debug.

---

## 3. Controlli e quadrature

### `check_vari.php`
Pagina multi-controllo selezionata via GET `scelta` (e talvolta `filiale`, `action`). Usa DataTables con export. I controlli:

| `scelta` | Controllo | Tabelle / Query |
|----------|-----------|-----------------|
| `cessioni` | Cessioni in essere confrontate con SDS SOCI (segnala se le azioni in cessione superano quelle a sistema) | `tab_xls_cessionibanca` RIGHT JOIN `sds_soci` INNER JOIN `sds_soci_certificati`, filtro `Rimborsato != 'S'` |
| `pac` | Situazione Piano di Accumulo (PAC) per Giovani Soci e ChiantiMutua, con residuo azioni | `sds_soci`, `sds_soci_certificati`, filtri `SOCIO_ISTITUTO=1 AND ACQUISTO_PERIOD='Y'` |
| `deceduti` | Deceduti (stato `X2`/`X?`) per cui gli eredi non hanno manifestato volontà, con giorni trascorsi dal decesso | `tab_xls_decessi_eredi`, filtri `Note_AO08 in ('X2','X?')` e campi eredi vuoti |
| `coge` (`action` vuoto) | Form di aggiornamento manuale saldi Plafond/Fondo/Capitale/Sovrapprezzo (lettura da `tab_valorefondo`) | `tab_valorefondo` (SELECT) |
| `coge` (`action=update`) | UPDATE valori CoGe inseriti manualmente | `UPDATE tab_valorefondo SET aggiornamento=now(), capitale=.., sovrapprezzo=.., valore=.., plafond=..` |

- **Parametri**: GET `scelta`, `filiale` (filtra per filiale capofila; default esclude 999), `action`, e per `coge` i valori `capitale/sovrapprezzo/fondo/plafond`.
- **Controllo accesso**: nessun gate `filiale_id` (la pagina è linkata dal menu Ufficio Soci).
- **Debito tecnico**: `$_GET['filiale']` e i valori CoGe interpolati in SQL (injection); l'aggiornamento dei saldi avviene via **GET** (idempotenza/CSRF). Una query alternativa per le cessioni resta commentata nel file.

### `old/check_vari_csv.php` (storico)
- Versione precedente con controlli aggiuntivi selezionabili (es. `scelta=nomail` per soci senza mail) e generazione CSV. Conservata in `old/` come archivio; stesso pattern di interpolazione di `filiale`/`scelta`.

### `filiali_matricekm.php` - Matrice distanze tra filiali
- **Scopo**: visualizza una matrice km/distanze (fonte Google Maps) tra le filiali, letta da `tab_filiali_matricekm`. Colonne `F_0`, `F_1`, ... `F_73` (una colonna per ciascun codice filiale). Esclude filiali chiuse (`chiusa <> 'S'`).
- **Parametri**: nessuno.
- **Tabella**: `tab_filiali_matricekm` (`area, filiale, nome_filiale, indirizzo, F_*`). Query loggata.
- **Uso funzionale**: supporta i controlli di competenza territoriale (distanza socio/filiale di riferimento). La sezione "legenda filiali" è commentata.

---

## 4. Zone di competenza territoriale

Definiscono se un comune ricade nella zona di competenza ChiantiBanca e gestiscono la validazione dei soci residenti fuori zona.

### `zonecompetenza.php`
- **Scopo**: anagrafica comuni della Toscana con flag competenza/presenza filiale e drill-down sui comuni confinanti.
- **Parametri (GET)**: `cod_comune`, `comune` (vuoti = elenco completo; valorizzati = comuni adiacenti).
- **Tabella**: `tab_comuni` (`PRO_COM_COMUNE, COMUNE, PROVINCIA_COMUNE, CAB, FILIALE (presenza), COMPETENZA, PA_3 (piazza), PRO_COM_COMUNE_ADIACENTE, COMUNE_ADIACENTE`). Query loggate.
- **Logica colore**: verde = competenza S + filiale presente; arancione = competenza S senza filiale.

### `check_zonecompetenza.php`
- **Scopo**: elenco **soci residenti fuori zona di competenza** (Italia o Estero), con stato di validazione, motivazioni dalla domanda di ammissione e note. Genera CSV `tmp/checkzonecompetenza.csv`.
- **Parametri (GET)**: `filiale` (default sintesi su tutte, escluse 999), `fuorizona` (`estero`/`italia`).
- **Tabelle/Query**:
  - principale: `sds_soci` LEFT JOIN `tab_comuni_soci_note` (su `nag`), filtri `SOCIO_ISTITUTO='1'` + `PROVINCIA_RES='SE'` (estero) oppure `PA_3 IN (998,999) AND PROVINCIA_RES<>'SE'` (Italia, piazze fuori competenza);
  - per ogni socio: conteggio motivazioni in `tab_motivazioni`, lettura indicazioni dalla domanda in `sds_soci_domande` (PROF_*/IMM_*), valore nominale da `sds_soci`+`sds_soci_certificati`.
- **Stato esito**: `Valido` / `Escludere` / `Da verificare` (con icone e contatori aggregati).
- **Debito tecnico**: `ini_set('max_execution_time', 0)`; molte sotto-query in loop (N+1) con `$dati_p['Cag']` interpolato; tutte le variabili GET interpolate.

### `check_zonecompetenza_note.php`
- **Scopo**: form di annotazione/validazione della competenza territoriale del socio (INSERT se nuova nota `id='N'`, UPDATE se esistente). Tabella: **`tab_comuni_soci_note`** (`filiale, cag, nominativo, documentale, status_esito, note, operatore, data_segnalazione, attivo`).
- **Parametri**: GET `id`, `tipo` (`edit`/`update`), `filiale`, `cag`, `nominativo`; POST `descrizione, documentale, operatore, esito, ...`.
- **Note**: l'operatore è precompilato con `LN00`+`$_COOKIE['usr_id']`. `descrizione` passata da `mysqli_real_escape_string(htmlspecialchars())`; gli altri campi interpolati.

---

## 5. Mail e PEC

Coesistono **due meccanismi** di invio:
1. **`mail()` di PHP** via SMTP `smtp.bccsi.bcc.it:25` (la maggior parte delle segnalazioni). Corpo HTML codificato base64.
2. **PHPMailer** verso PEC Actalis (`ssl://smtp.pec.actalis.it:465`) - vedi `routines/test_pec.php`.

Tutti gli script di segnalazione hanno la regola **"niente email sabato/domenica"** (`if(date('w')=="0" || date('w')=="6") return;`).

### `mail_search.php`
- **Scopo**: endpoint AJAX di **autocomplete socio** usato dai form di segnalazione. Restituisce `<li>` con NAG, nominativo, data nascita, conto, stato, eventuale data decesso.
- **Parametri**: GET `segnalazione` (`decesso` filtra `TIPO_NAG='PF'`; `vincolo` nessun filtro); POST `keyword`.
- **Tabella**: `sds_soci`. Usa una classe locale `DBController` con **credenziali MySQL hardcoded**.
- **Debito tecnico**: **SQL injection** diretta su `$_POST['keyword']` (concatenato in `LIKE '...%'`).

### `segnalazione_mail_decesso.php`
- **Scopo**: il dipendente di filiale segnala all'Ufficio Soci un socio deceduto. Identifica l'utente da SADAS (`TAB_UTENTI`), interroga **IsiDoc** (`ISIDOC_DOCUMENTI_PERSONALE` + `SOCI_ANAGRAFICA`) per allegare l'elenco documenti già archiviati, poi invia email a **`soci@chiantibanca.it`**.
- **Parametri**: POST `inviamail`, `search-box` (nominativo + idsocio tra parentesi), `datadecesso`, `flagdocumentale`, `note`, `dipendente`.
- **Destinatario hardcoded**: `soci@chiantibanca.it`.

### `segnalazione_mail_scissione.php`
- **Scopo**: richiesta di **scissione di un certificato azionario** verso l'Ufficio Soci. Mittente `noreply@chiantibanca.it`/`usr_mail`, destinatario `soci@chiantibanca.it`.
- **Parametri**: POST `inviamail`, `search-box`, `note`, `dipendente`. Autocomplete via `mail_search.php?segnalazione=vincolo`.

### `segnalazione_mail_vincolo.php`
- **Scopo**: richiesta di **eliminazione vincolo conto** verso l'Ufficio Soci. Struttura identica alla scissione (destinatario `soci@chiantibanca.it`).

### `lista_mail_mailup.php` - Export per MailUp
- **Scopo**: estrae le email dei soci attivi per import in **MailUp**, generando `tmp/mailup.csv` (`Fonte;NAG;Nominativo;Mail`).
- **Parametri (GET)**: `scelta` (`full` = tutti i soci attivi; altrimenti Under 35 PF `ETA<=35 AND TIPO_NAG='PF'`).
- **Tabelle**: `sds_soci_daticontatto` + `sds_soci` (filtro `SOCIO_ISTITUTO='1' AND tipo_dato_cnt='MAIL'` escludendo `nomail@nomail.it`). Apre anche ODBC SADAS (di fatto non utilizzato nella query MySQL).
- **UI**: pulsante di download mostrato dopo 5s (timeout JS) per dare tempo alla scrittura del CSV.

### `mail_test.php`
- Funzione `invia_email()` di test (corpo "ok_inserito"). Mittente/destinatario hardcoded (`alessio.fedi@...`). Stesso pattern `mail()`+base64. Da considerare codice di test residuo.

---

## 6. Utility

### `utility.php`
- **Scopo**: pannello strumenti dell'Ufficio Soci. Espone form verso la modulistica:
  - Addendum **Ulteriori Quote** (`modulistica/SO52_addendum_ulterioriquote.php`);
  - Addendum **Rateizzazione** (`modulistica/SO51_addendum_rateizzazione.php`, Under 35 / ChiantiMutua);
  - Addendum **Donazione Under 35** (`modulistica/SO50_addendum_donazione.php`);
  - calcolo età (form attualmente commentato; logica `calcoloeta` ancora presente lato server).
- **Parametri**: POST `calcoloeta`, `dt1`, `dt2`; gli altri form passano in GET i parametri di stampa alla modulistica.

### `config/info.php`
- **Scopo**: chiama `phpinfo()`. **Debito/Rischio**: espone l'intera configurazione PHP/server; non dovrebbe essere accessibile in produzione.

---

## 7. Librerie interne `routines/`

Componenti riutilizzabili e di supporto (alcuni di terze parti integrati).

| Componente | File | Descrizione | Note / debito |
|------------|------|-------------|---------------|
| **PHPMailer** | `routines/PHPMailer/` | Libreria di terze parti per invio email/PEC (usata da `routines/test_pec.php`). | Credenziali PEC in chiaro nello script di test. |
| **sql2xls** | `routines/sql2xls/` (`sql2excel.class.php`, `excelgen.class.php`, esempio) | Esporta il risultato di una query MySQL in file Excel (.xls) verso il browser. | Basata sull'**API `mysql_*` deprecata/rimossa** in PHP 7+; di fatto non più funzionante senza adeguamento. |
| **Multi_Edit** | `routines/Multi_Edit/` (`index.php`, `edit.php`, `edit_save.php`, `dbcon.php`, `header.php`) | Editor multi-record: lista da `tab_protocollo`/`tab_comipa` con checkbox per aggiornamento massivo (uso: aggiornamento protocollo alla ricezione documenti). | Usa `mysql_*` deprecata; nessuna autenticazione. |
| **basic (CRUD scaffold)** | `routines/basic/` (`index.php`, `add.php`, `edit.php`, `update.php`, `delete.php`, `details.php`, `conn.php`) | CRUD generico su `tab_eventi` (Gestione Eventi, linkata da `admin.php`). `SHOW COLUMNS` dinamico per le intestazioni. | **CRUD senza protezione**: `delete.php?id=...` via GET, nessun controllo accesso/CSRF; injection sugli `id`. |
| **db-backup-restore** | `routines/db-backup-restore/` (`BackUp.php`, `backup_mutua.php`) | Classe (Djunehor) per dump/restore del DB MySQL su file `.sql`. `getTables()`, `backup()`, `restore()`, `lock()/unlock()`. | **Script di backup nel repository web**: se raggiungibile via HTTP consente dump dell'intero DB. `mkdir(..., 0777)`. |
| **CMySqldbHTML** | `routines/CMySqldbHTML.php` (+ `exampleCMSH.php`) | Classe per introspezione tabelle MySQL e generazione automatica di form/HTML dai metadati (`getcmpsForm`, `procForm`). Usata da `routines/enviar.php`. | Generatore di form generico senza validazione; `enviar.php` include `../config.php` (path/credenziali esterni). |
| **enviar.php** | `routines/enviar.php` | Handler che, dato `tablename` in POST, costruisce e salva un record tramite `CMySqldbHTML`. | Nessuna validazione (commenti del codice lo dichiarano); injection sul nome tabella e sui campi. |
| **rubricacb.php** | `routines/rubricacb.php` | Genera `tmp/rubricacb.csv` con la rubrica telefonica aziendale da DB `rubrica` (`lista`). | Connessione a un **secondo DB** con credenziali hardcoded; usa `ereg_replace` (rimossa in PHP 7). |
| **mail_dip.php** | `routines/mail_dip.php` | Job: invia email "BDAY" con i compleanni dei dipendenti del giorno (da SADAS `TAB_UTENTI`+`ANAG_PERSONE_FISICHE`). | Destinatari/mittente hardcoded; pensato per esecuzione schedulata. |
| **mail_mutua.php** | `routines/mail_mutua.php` | Job: invia a `info@chiantimutua.it` l'elenco soci entrati/usciti del giorno precedente (da `sds_soci`). | Destinatario hardcoded; mittente `soci@chiantibanca.it`. |
| **mail_mov_514442.php** | `routines/mail_mov_514442.php` | Job: invia i movimenti giornalieri di alcuni c/c (61.514442/611241/611240/914914) letti da SADAS `CG_MOVIMENTI_CONTABILI`. | Destinatario `alessio.fedi@chiantibanca.it` hardcoded. |
| **test_pec.php** | `routines/test_pec.php` | Test invio PEC via PHPMailer su Actalis. | Credenziali PEC in chiaro; `SMTPDebug=3`. |
| **php_testbed / wkhtmltox** | `routines/php_testbed/`, `routines/wkhtmltox/` | Testbed PHP e binari wkhtmltopdf/wkhtmltoimage (rendering HTML->PDF/IMG). | Binari `.exe`/`.dll` versionati nel repo. |

---

## Riepilogo del debito tecnico (dominio)

- **SQL injection diffusa**: la maggior parte delle pagine concatena `$_GET`/`$_POST` nelle query (es. `admin_psw_edit.php`, `monitor_*`, `check_*`, `mail_search.php`, `enviar.php`). Mitigazione solo parziale e limitata ai campi testo lunghi (`mysqli_real_escape_string(htmlspecialchars(...))`).
- **Credenziali in chiaro**: nel codice (`mail_search.php`, `routines/rubricacb.php`, `logquery()`), negli script PEC e nel markup di `admin.php`/`admin_psw.php` (password filiali visibili e password di sistemi esterni nei tooltip).
- **Controlli di accesso assenti o incoerenti**: `check_*`, `zonecompetenza.php`, `routines/basic/*`, `db-backup-restore`, `config/info.php` non verificano `filiale_id`; `admin_psw.php` non protegge la pagina pur mostrando password.
- **CRUD generici e script pericolosi nel webroot**: `routines/basic` (delete via GET), `routines/db-backup-restore` (dump DB), `routines/enviar.php` (insert dinamico) e `config/info.php` (`phpinfo`).
- **Mail con destinatari/mittenti hardcoded** e dipendenza da `mail()` + SMTP interno; regola "no weekend" che blocca silenziosamente gli invii.
- **API obsolete**: `mysql_*` (`sql2xls`, `Multi_Edit`, `routines/basic` indirettamente), `ereg_replace`, `mysql_error()` -> incompatibili con PHP 7+.
- **`error_reporting(0)`** su diverse pagine maschera errori reali.
- **Aggiornamenti via GET** non idempotenti (es. `check_vari.php?scelta=coge&action=update`).

> Tutte le segnalazioni sopra sono ricavate dalla lettura del codice presente nel repository. Eventuali mitigazioni applicate a livello di web server / rete (restrizioni di rete intranet, reverse proxy, ecc.) sono **da verificare**.
