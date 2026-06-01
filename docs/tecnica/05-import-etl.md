# 05 - Import dati / ETL / Generazione tabelle (Documento Tecnico)

> Portale ChiantiBanca - Soci. Dominio: alimentazione delle tabelle MySQL `soci` a
> partire dalle sorgenti esterne (gestionale **Sicra**, datawarehouse **SADAS** via
> ODBC, archivio documentale **Isidoc**, fogli Excel dell'Ufficio Soci).

## 1. Panoramica dell'architettura ETL

Il portale non genera autonomamente i dati anagrafici e finanziari dei soci: li
importa periodicamente da sistemi esterni e li materializza in tabelle MySQL
(prefissi `sds_*`, `tab_*`, `sicra_*`) che vengono poi interrogate dalle pagine
di reporting.

Esistono **due meccanismi distinti** di alimentazione:

| Meccanismo | Innesco | Sorgente immediata | Tecnica scrittura |
|---|---|---|---|
| (a) Script `crea_*` | Riga di comando / schedulato (`.cmd`) | SADAS via ODBC + tabelle MySQL già caricate | `TRUNCATE` + `INSERT` riga-per-riga da `odbc_fetch_object` |
| (b) Script `csv2sql_*` | Manuale, via browser (pagina [admin_upload.php](../../admin_upload.php)) | File CSV esportato a mano da Sicra/Isidoc/Excel | `TRUNCATE` + `LOAD DATA LOCAL INFILE` **oppure** `fgetcsv` + `INSERT` |

Entrambi i meccanismi seguono la stessa filosofia: **rigenerazione completa** della
tabella di destinazione (full refresh), mai aggiornamento incrementale. Ogni
caricamento svuota la tabella (`TRUNCATE`) e la ricostruisce da zero.

```
                 SADAS (ODBC 'SADAS')                      Excel CDA_ELENCO
                        │                                         │
         crea_sds_soci.php (CLI)                       Ufficio Soci esporta CSV
         crea_*_*.php (CLI)                                       │
                        │                              admin_upload.php (browser)
                        ▼                                         ▼
   ┌─────────────────────────────────┐         ┌─────────────────────────────────┐
   │ MySQL  soci  (sds_*, tab_*)     │◄────────│ upload/csv2sql_*.php             │
   │ TRUNCATE + INSERT da ODBC       │         │ TRUNCATE + LOAD DATA / fgetcsv   │
   └─────────────────────────────────┘         └─────────────────────────────────┘
                        ▲                                         ▲
                  Sicra (DbQuery, Riepiloghi, Sinergia, Isidoc) → CSV
```

### Tracciamento dei caricamenti

La tabella MySQL `tab_ultimo_caricamento` funge da registro: ogni script
`csv2sql_*` esegue al termine
`UPDATE tab_ultimo_caricamento SET caricamento=now() WHERE fonte='<tabella>'`.
La pagina [admin_upload.php](../../admin_upload.php) legge questa tabella per
mostrare, per ciascuna fonte, descrizione, cadenza, tabella di destinazione e
data/ora dell'ultimo aggiornamento, con il link al rispettivo `csv2sql_*`.

La libreria [lib/loggerALBA.php](../../lib/loggerALBA.php) **non** è usata
dall'ETL: è una libreria di tracciatura accessi (tracciato normativo "ALBA"
BccSi, log su `E:/Logs/logAlba/`) pensata per gli accessi a NAG/rapporti, non per
i processi di import. (da verificare se richiamata altrove)

---

## 2. Meccanismo (a) — Script `crea_*` (CLI da SADAS)

### 2.1 Innesco da riga di comando

Gli script `crea_*` sono pagine PHP eseguibili anche da CLI tramite file `.cmd`
Windows, presumibilmente schedulati (Utilità di pianificazione) per girare di
notte.

[crea_sds_soci.cmd](../../crea_sds_soci.cmd) verifica la presenza di `php.exe`
nel PATH (atteso `C:\wamp64\bin\php\php5.6.35`) e lancia `php crea_sds_soci.php`.
[bin/mutua_crea_elenco.cmd](../../bin/mutua_crea_elenco.cmd) fa lo stesso per
`mutua_crea_elencosoci.php` posizionandosi prima in `E:\www\soci\`.

Il file [crea_sds_soci.log](../../crea_sds_soci.log) committato nel repository
documenta un'esecuzione **fallita**: l'ODBC `SADAS` non riusciva a caricare il
driver (`SQLConnect ... Accesso negato ... SdsODBC.dll`, SQL state IM003). Il
percorso reale di produzione è quindi `E:\www\soci\`, server WAMP con PHP 5.6.

### 2.2 Pattern comune degli script `crea_*`

Tutti gli script `crea_*` aprono **due** connessioni:

```php
$connect    = odbc_connect('SADAS', NULL, NULL) or die ('0');   // datawarehouse
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name); // MySQL soci
```

e per ogni tabella di destinazione applicano lo schema:

1. `SELECT COUNT(*)` MySQL (conteggio pre-caricamento, per il report);
2. `TRUNCATE <tabella>` sulla tabella MySQL;
3. `SELECT ...` su SADAS via `odbc_exec($connect, ...)`;
4. ciclo `while (odbc_fetch_object(...))` con `INSERT INTO <tabella> ...` su MySQL;
5. `SELECT COUNT(*)` MySQL post-caricamento, differenza nel corpo email.

### 2.3 Pipeline principale: `crea_sds_soci.php`

[crea_sds_soci.php](../../crea_sds_soci.php) (≈1328 righe, autore Alessio Fedi,
03.11.2022) è l'**orchestratore** dell'aggiornamento notturno da SADAS. Imposta
`max_execution_time = 0`, **si interrompe il sabato e la domenica**
(`if(date('w')=="0" || date('w')=="6") return;`), e popola in sequenza (blocchi
etichettati A→L) le seguenti tabelle MySQL, ciascuna con TRUNCATE + INSERT da una
query SADAS:

| Blocco | Tabella MySQL di destinazione | Contenuto |
|---|---|---|
| A | `SDS_SOCI` | Anagrafica soci |
| B | `SDS_SOCI_CERTIFICATI` | Certificati azionari |
| C | `SDS_SOCI_DATICONTATTO` | Recapiti |
| D | `SDS_SOCI_SUBENTRATI` | Subentri |
| E | `SDS_SOCI_TRASFERIMENTI` | Trasferimenti |
| F | `SDS_SOCI_MOVINOUT` | Movimenti ingresso/uscita |
| G | `SDS_SOCI_DOMANDE` | Domande di ammissione |
| H | `SDS_SOCI_UNDER35` | Soci under 35 (soglia calcolata: oggi − 35 anni) |
| I | `SDS_SOCI_ISIDOC` + `SDS_SOCI_DOMANDE_NOPDF` | Corrispondenza documentale Isidoc |
| L | `SDS_SOCI_PRODOTTO_CC` | Prodotto conto corrente |

Al termine invia una mail riepilogativa a `soci@chiantibanca.it` (SMTP
`smtp.bccsi.bcc.it:25`) con conteggi pre/post per ciascuna tabella, e infine
**include in cascata** tre sotto-pipeline:

```php
include ("crea_sds_soci_dati_consolidati.php");
include ("crea_previsionale.php");
include ("crea_impieghiraccolta.php");
```

### 2.4 Pipeline statistica: `crea_sds_soci_dati_consolidati.php`

[crea_sds_soci_dati_consolidati.php](../../crea_sds_soci_dati_consolidati.php)
(≈1523 righe) **non** segue lo schema TRUNCATE: è una pipeline di calcolo di KPI
storicizzati. Logica:

1. `INSERT INTO SDS_SOCI_DATI_CONSOLIDATI (data_inserimento) VALUES (NOW())` —
   crea una **nuova riga-snapshot** e ne recupera l'`id` con `MAX(...)`;
2. esegue decine di query di conteggio/aggregazione (alcune su SADAS via ODBC —
   es. `COUNT(anag.NAG)` su `ANAG_NAG`, `COUNT(IDSOCIO)` su `SOCI_ANAGRAFICA` —
   altre su MySQL) e con `UPDATE` valorizza i singoli campi KPI della riga appena
   creata (`BA_NAG_TOTALI`, `SOCI_TOTALI`, ecc.).

A differenza degli altri, questo script **accumula** storico (una riga per
esecuzione): alimenta i grafici/andamenti temporali del portale.

### 2.5 Altre sotto-pipeline `crea_*`

| Script | Tabella destinazione | Sorgente | Note |
|---|---|---|---|
| [crea_previsionale.php](../../crea_previsionale.php) | `TAB_PREVISIONALE` | SADAS | Soci necessari al pareggio per area/filiale |
| [crea_decaduti_liquidati.php](../../crea_decaduti_liquidati.php) | `TAB_DECADUTI_LIQUIDATI` | viste SADAS `view_decaduti` + `view_decaduti_liquidati` LEFT JOIN `tab_xls_esclusioni` | Soci decaduti già liquidati |
| [crea_decaduti_nonliquidati.php](../../crea_decaduti_nonliquidati.php) | `TAB_DECADUTI_NONLIQUIDATI` | SADAS | Soci decaduti da liquidare |
| [crea_impieghiraccolta.php](../../crea_impieghiraccolta.php) | `SDS_SOCI_IMPIEGHIRACCOLTA` | vista SADAS `IMPIEGHI_E_RACCOLTA_01` | Impieghi/raccolta/numero rapporti per socio |

> Nota: i tre script `crea_decaduti_*`, `crea_previsionale`, `crea_impieghiraccolta`
> riportano nei commenti l'intestazione copia-incollata "CREA TABELLA TAB_PREVISIONALE"
> e data "23.13.2022" (mese inesistente): refusi, non incidono sull'esecuzione.

---

## 3. Meccanismo (b) — Script `csv2sql_*` (upload CSV manuale)

### 3.1 Flusso operativo

1. L'operatore apre [admin_upload.php](../../admin_upload.php), che elenca tutte le
   fonti caricabili (lette da `tab_ultimo_caricamento`) con un link a
   `upload/csv2sql_<fonte>.php`.
2. La pagina `csv2sql_<fonte>.php` mostra un form di upload con istruzioni su come
   produrre il CSV (da Sicra/Isidoc/Excel) e dei **campi hidden con le credenziali
   MySQL in chiaro**.
3. All'invio, il file viene spostato in `upload/` con `move_uploaded_file`, poi la
   tabella viene `TRUNCATE`ata e ricaricata.

### 3.2 Il motore generico: `csv2sql_generico.php`

[upload/csv2sql_generico.php](../../upload/csv2sql_generico.php) è il **template
base** ("FILE GENERICO (da adattare)"). Definisce in testa le variabili
`$titolo`, `$tabella`, `$nomefile`, `$istruzioni` e le credenziali, poi:

```php
mysqli_query($cons,"SET SESSION sql_mode = 'TRADITIONAL'");
$truncatetabella = mysqli_query($cons,"TRUNCATE ".$table);
mysqli_query($cons, '
    LOAD DATA LOCAL INFILE "'.$file.'"
        INTO TABLE '.$table.'
        FIELDS TERMINATED by \';\'
        LINES TERMINATED BY \'\r\n\'
        IGNORE 1 LINES ');
```

Caratteristiche del motore:
- **delimitatore di campo**: `;` (punto e virgola — formato "CSV delimitato da
  separatore di elenco" di Excel italiano);
- **fine riga**: `\r\n`;
- **header**: la prima riga viene scartata (`IGNORE 1 LINES`);
- **modalità**: sempre full refresh (TRUNCATE + LOAD);
- il numero di record caricati è calcolato come differenza dei `COUNT(*)`
  pre/post e mostrato a video.

### 3.3 Il template documentale: `csv2sql_tab_ESEMPIO.php`

[upload/csv2sql_tab_ESEMPIO.php](../../upload/csv2sql_tab_ESEMPIO.php) è una
copia storica (eredità del "Portale ChiantiMutua", DB `mutua`) usata come esempio
documentato. Oltre al LOAD DATA standard, contiene logica accessoria specifica
Mutua (ricostruzione di `tab_sib_filiali` e `tab_comipa_filiali` via
`INSERT ... SELECT`) e l'`UPDATE tab_ultimo_caricamento`. Punta al DB `mutua` con
credenziali diverse da quelle del portale Soci: serve da modello, non è in uso
sul portale Soci attuale (da verificare).

### 3.4 Due varianti di caricamento

Analizzando tutti gli script, emergono due implementazioni:

- **Variante LOAD DATA INFILE** — usata per i file "puliti" (estrazioni dal
  verdone/host, fogli Excel CDA_ELENCO). Veloce, ma fragile sui caratteri di
  testo.
- **Variante `fgetcsv` + `INSERT` riga-per-riga** — adottata per le estrazioni
  Sicra/SADAS/Isidoc, che contengono testo libero (nomi, indirizzi). Legge con
  `fgetcsv($handler, 0, ';')`, salta la prima riga (`$i>0`), e costruisce un
  `INSERT` con `mysqli_real_escape_string` sui soli campi testuali. In diversi
  file il blocco `LOAD DATA` resta presente ma **commentato** come fallback.

Esempio della variante `fgetcsv` (da
[upload/csv2sql_sds_soci_anagrafica.php](../../upload/csv2sql_sds_soci_anagrafica.php),
60 colonne):

```php
$handler=fopen($file, "r");
$i=0;
while($data=fgetcsv($handler, 0, ';')){
    if($i>0) {
        mysqli_query($cons, "INSERT INTO ".$table." VALUES (
            '".$data[0]."', ... '".mysqli_real_escape_string($cons,$data[4])."', ...)");
    }
    $i++;
}
```

Il mapping colonne è **posizionale**: l'ordine delle colonne nel CSV deve
corrispondere esattamente all'ordine dei campi della tabella; non c'è mapping per
nome di colonna.

### 3.5 Catalogo completo degli script `csv2sql_*`

Sorgenti: **DbQuery** = Sicra > Sistemi Guida (query SQL su SADAS);
**Riepiloghi** = Sicra > Soci > Riepiloghi; **Sinergia** = Sicra > Servizi
Sinergia; **Isidoc** = archivio documentale; **ZW37/AS37/AS75** = transazioni
host "verdone"; **CDA_ELENCO** = workbook Excel con macro dell'Ufficio Soci.

| Script `csv2sql_*` | Tabella destinazione | Nome file CSV | Meccanismo | Sorgente |
|---|---|---|---|---|
| [csv2sql_generico.php](../../upload/csv2sql_generico.php) | `tab_mutua_raccordo` (da adattare) | Generico.csv | LOAD DATA | Template base |
| [csv2sql_tab_ESEMPIO.php](../../upload/csv2sql_tab_ESEMPIO.php) | `tab_comipa` (DB mutua) | COMIPA.csv | LOAD DATA | Procedura COMIPA (esempio storico) |
| [csv2sql_sds_anag_nag.php](../../upload/csv2sql_sds_anag_nag.php) | `sds_anag_nag` | sds_anag_nag.csv | fgetcsv+INSERT | Sicra DbQuery (SADAS ANAG_NAG) |
| [csv2sql_sds_anagraficaristretta_clienti.php](../../upload/csv2sql_sds_anagraficaristretta_clienti.php) | `sds_anagraficaristretta_clienti` | sds_anagraficaristretta_clienti.csv | fgetcsv+INSERT | Sicra DbQuery (SADAS) |
| [csv2sql_sds_sinergiareport_soci.php](../../upload/csv2sql_sds_sinergiareport_soci.php) | `sds_sinergiareport_soci` | sds_sinergiareport_soci.csv | fgetcsv+INSERT | Sicra Sinergia Report |
| [csv2sql_sds_soci_anagrafica.php](../../upload/csv2sql_sds_soci_anagrafica.php) | `sds_soci_anagrafica` | sds_soci_anagrafica.csv | fgetcsv+INSERT (60 col.) | Sicra DbQuery PS_SociAnagrafica |
| [csv2sql_sds_soci_certificati.php](../../upload/csv2sql_sds_soci_certificati.php) | `sds_soci_certificati` | sds_soci_certificati.csv | fgetcsv+INSERT | Sicra DbQuery |
| [csv2sql_sds_soci_domande.php](../../upload/csv2sql_sds_soci_domande.php) | `sds_soci_domande` | sds_soci_domande.csv | fgetcsv+INSERT | Sicra DbQuery |
| [csv2sql_sds_soci_riacquisto_azioni.php](../../upload/csv2sql_sds_soci_riacquisto_azioni.php) | `sds_soci_riacquisto_azioni` | sds_soci_riacquisto_azioni.csv | fgetcsv+INSERT | Sicra DbQuery |
| [csv2sql_sicra_decaduti_liquidati.php](../../upload/csv2sql_sicra_decaduti_liquidati.php) | `sicra_decaduti_liquidati` | sicra_decaduti_liquidati.csv | fgetcsv+INSERT | Sicra Riepiloghi (Decaduti Liquidati) |
| [csv2sql_sicra_decaduti_nonliquidati.php](../../upload/csv2sql_sicra_decaduti_nonliquidati.php) | `sicra_decaduti_nonliquidati` | sicra_decaduti_nonliquidati.csv | fgetcsv+INSERT | Sicra Riepiloghi (Decaduti non liquidati) |
| [csv2sql_sicra_isidoc_soci_corrispondenza.php](../../upload/csv2sql_sicra_isidoc_soci_corrispondenza.php) | `sicra_isidoc_soci_corrispondenza` | sicra_isidoc_soci_corrispondenza.csv | fgetcsv+INSERT | Isidoc (corrispondenza documentale) |
| [csv2sql_tab_comuni_soci.php](../../upload/csv2sql_tab_comuni_soci.php) | `tab_comuni_soci` | COMUSOCI.csv | LOAD DATA | Verdone ZW37 query LF_SOCIIND |
| [csv2sql_tab_deceduti.php](../../upload/csv2sql_tab_deceduti.php) | `tab_deceduti` | Deceduti.csv | LOAD DATA | Verdone ZW37 query LF_RAPPBLO (StatoBlocco SU/94) |
| [csv2sql_tab_dipendenti.php](../../upload/csv2sql_tab_dipendenti.php) | `tab_dipendenti` | Dipendenti.csv | LOAD DATA | Verdone ZW37 query LF_DIPENDE |
| [csv2sql_tab_giovani.php](../../upload/csv2sql_tab_giovani.php) | `tab_giovani` | Giovani.csv | LOAD DATA | Verdone ZW37 query LF_CLIETA |
| [csv2sql_tab_mutua.php](../../upload/csv2sql_tab_mutua.php) | `tab_mutua` | mutua.csv | fgetcsv+INSERT | CSV settimanale via mail da WTech |
| [csv2sql_tab_sdd.php](../../upload/csv2sql_tab_sdd.php) | `tab_sdd` | sdd.xml | LOAD DATA | Gestionale COMIPA (tracciato SDD InBank) |
| [csv2sql_tab_soci_as37.php](../../upload/csv2sql_tab_soci_as37.php) | `tab_soci_as37` | DATISOCI.csv | LOAD DATA | Transazione host AS37 |
| [csv2sql_tab_soci_as75.php](../../upload/csv2sql_tab_soci_as75.php) | `tab_soci_as75` | AS96ANA.csv | LOAD DATA | Transazione host AS75 |
| [csv2sql_tab_volumi.php](../../upload/csv2sql_tab_volumi.php) | `tab_volumi` | SOCIVOL.csv | LOAD DATA | Verdone ZW37 query MZ_ASVOL |
| [csv2sql_tab_xls_acquistoulterioriazioni.php](../../upload/csv2sql_tab_xls_acquistoulterioriazioni.php) | `tab_xls_acquistoulterioriazioni` | AcquistoUlterioriAzioni.csv | LOAD DATA | Excel CDA_ELENCO (macro) |
| [csv2sql_tab_xls_ammissioni.php](../../upload/csv2sql_tab_xls_ammissioni.php) | `tab_xls_ammissioni` | Ammissioni.csv | LOAD DATA | Excel CDA_ELENCO (macro) |
| [csv2sql_tab_xls_cessionibanca.php](../../upload/csv2sql_tab_xls_cessionibanca.php) | `tab_xls_cessionibanca` | CessioniBanca.csv | fgetcsv+INSERT | Excel CDA_ELENCO (macro) |
| [csv2sql_tab_xls_decessi_eredi.php](../../upload/csv2sql_tab_xls_decessi_eredi.php) | `tab_xls_decessi_eredi` | Decessi-Eredi.csv | LOAD DATA | Excel CDA_ELENCO (macro) |
| [csv2sql_tab_xls_esclusioni.php](../../upload/csv2sql_tab_xls_esclusioni.php) | `tab_xls_esclusioni` | Esclusioni.csv | fgetcsv+INSERT | Excel CDA_ELENCO (macro) |
| [csv2sql_tmp_formadoc_log.php](../../upload/csv2sql_tmp_formadoc_log.php) | `tmp_formadoc_log` | tmp_formadoc_log.csv | fgetcsv+INSERT | LOG SOCICN02 FORMADOC (Organizzazione) |

---

## 4. Tabella riepilogativa "script → destinazione → sorgente"

| Script | Tabella/e destinazione | Tipo | Sorgente primaria |
|---|---|---|---|
| crea_sds_soci.php | SDS_SOCI, SDS_SOCI_CERTIFICATI, SDS_SOCI_DATICONTATTO, SDS_SOCI_SUBENTRATI, SDS_SOCI_TRASFERIMENTI, SDS_SOCI_MOVINOUT, SDS_SOCI_DOMANDE, SDS_SOCI_UNDER35, SDS_SOCI_ISIDOC, SDS_SOCI_DOMANDE_NOPDF, SDS_SOCI_PRODOTTO_CC | crea (CLI) | SADAS (ODBC) |
| crea_sds_soci_dati_consolidati.php | SDS_SOCI_DATI_CONSOLIDATI (snapshot storico) | crea (CLI) | SADAS + MySQL |
| crea_previsionale.php | TAB_PREVISIONALE | crea (CLI) | SADAS |
| crea_decaduti_liquidati.php | TAB_DECADUTI_LIQUIDATI | crea (CLI) | viste SADAS + tab_xls_esclusioni |
| crea_decaduti_nonliquidati.php | TAB_DECADUTI_NONLIQUIDATI | crea (CLI) | SADAS |
| crea_impieghiraccolta.php | SDS_SOCI_IMPIEGHIRACCOLTA | crea (CLI) | vista SADAS IMPIEGHI_E_RACCOLTA_01 |
| 27 × csv2sql_*.php | vedi catalogo §3.5 | csv (upload) | Sicra / Isidoc / host verdone / Excel CDA_ELENCO |

---

## 5. Debito tecnico e rischi

| # | Rilievo | Dettaglio | Gravità |
|---|---|---|---|
| 1 | **SQL injection negli import** | Le varianti `fgetcsv+INSERT` concatenano i valori CSV direttamente nella stringa SQL; `mysqli_real_escape_string` è applicato **solo ad alcuni campi** (quelli ritenuti testuali), mentre numerici/date sono inseriti grezzi. Un CSV malevolo o malformato compromette la query. Il nome tabella `$table` arriva dal POST e finisce in `TRUNCATE`/`LOAD`/`COUNT` senza whitelist. | Alta |
| 2 | **Credenziali DB hardcoded e in chiaro nel client** | Ogni `csv2sql_*` definisce `$username`/`$password` in chiaro nel sorgente e li rende come **campi hidden HTML** nel form, quindi visibili a chiunque apra la pagina (View Source). Le stesse credenziali sono in [config/_config.php](../../config/_config.php). | Alta |
| 3 | **Assenza di transazioni** | Il pattern `TRUNCATE` + caricamento non è transazionale: se l'import si interrompe a metà (es. errore su una riga, timeout, errore ODBC come in `crea_sds_soci.log`), la tabella resta **vuota o parziale** e il portale mostra dati incompleti finché non si rilancia. Nessun rollback. | Alta |
| 4 | **`die()` a metà pipeline** | `crea_*` e `csv2sql_*` usano `or die(mysqli_error(...))`/`die('0')`; un errore aborta l'intero script lasciando le tabelle a valle non aggiornate (in `crea_sds_soci.php` un fallimento blocca anche i sotto-include consolidati/previsionale/impieghi). | Alta |
| 5 | **Percorsi e ambiente hardcoded** | Percorsi `E:\www\soci\`, `C:\wamp64\bin\php\php5.6.35`, `E:/Logs/logAlba/`, SMTP `smtp.bccsi.bcc.it`, mail `soci@chiantibanca.it` cablati nel codice. PHP 5.6 a fine vita. | Media |
| 6 | **Dati reali e log nel repository** | Sono committati nel repo file con **dati personali di soci**: `upload/*.csv` (es. `sds_sinergiareport_soci.csv`, `sicra_*.csv`, `mutua.csv`, `CessioniBanca.csv`) e [crea_sds_soci.log](../../crea_sds_soci.log). Da rimuovere dal versionamento (GDPR). | Alta |
| 7 | **Mapping posizionale fragile** | L'allineamento CSV↔tabella è per indice numerico; una colonna aggiunta/spostata in Sicra disallinea silenziosamente tutti i campi a valle. Nessuna validazione di header o numero colonne. | Media |
| 8 | **Full refresh non idempotente in caso di errore** | Nessun file di staging: si carica direttamente sulla tabella di produzione. Non esiste backup automatico pre-TRUNCATE. | Media |
| 9 | **Processo manuale e non monitorato** | I `csv2sql_*` dipendono interamente dall'operatore (esportazione manuale da Sicra/Excel, upload). L'unico controllo è la data in `tab_ultimo_caricamento` mostrata in [admin_upload.php](../../admin_upload.php); nessun alert su mancato/obsoleto caricamento. | Media |
| 10 | **`LOAD DATA LOCAL INFILE`** | Richiede `local_infile` abilitato lato server/client; spesso disabilitato per sicurezza nelle versioni MySQL recenti — possibile causa di rotture in migrazione. | Bassa |

---

## 6. File coinvolti

- Orchestrazione CLI: [crea_sds_soci.php](../../crea_sds_soci.php), [crea_sds_soci.cmd](../../crea_sds_soci.cmd), [bin/mutua_crea_elenco.cmd](../../bin/mutua_crea_elenco.cmd)
- Sotto-pipeline: [crea_sds_soci_dati_consolidati.php](../../crea_sds_soci_dati_consolidati.php), [crea_previsionale.php](../../crea_previsionale.php), [crea_decaduti_liquidati.php](../../crea_decaduti_liquidati.php), [crea_decaduti_nonliquidati.php](../../crea_decaduti_nonliquidati.php), [crea_impieghiraccolta.php](../../crea_impieghiraccolta.php)
- Upload manuale: [admin_upload.php](../../admin_upload.php), [upload/csv2sql_generico.php](../../upload/csv2sql_generico.php) e i 27 `upload/csv2sql_*.php` (catalogo §3.5)
- Config: [config/_config.php](../../config/_config.php)
- Logging accessi (non ETL): [lib/loggerALBA.php](../../lib/loggerALBA.php)
- Evidenza esecuzione: [crea_sds_soci.log](../../crea_sds_soci.log)
