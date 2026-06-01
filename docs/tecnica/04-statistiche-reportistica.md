# 04 - Statistiche, Grafici e Reportistica PDF (Documentazione Tecnica)

> Dominio: cruscotto statistico, grafici FusionCharts, dettagli per area/filiale, fasce, previsionale e motore di reportistica PDF.
> Cartella principale: [`stats/`](../../stats) (~40 file PHP) + cruscotto root [`statistiche.php`](../../statistiche.php) e job [`crea_previsionale.php`](../../crea_previsionale.php).

## 1. Pattern architetturale comune

Tutte le pagine del dominio seguono lo stesso bootstrap:

1. `error_reporting(E_ALL ^ E_DEPRECATED)` (spesso seguito da `error_reporting(0)`).
2. `include("config/_config.php")` → credenziali MySQL (`$host`, `$db_user`, `$db_psw`, `$db_name`) e `$inizioanno`.
3. `include("config/_functions.php")` → funzioni di utilità.
4. `include("graph/fusioncharts.php")` → wrapper PHP di FusionCharts.
5. Connessione MySQL: `mysqli_connect(...)` e, per le query "ad oggetti"/FusionCharts, una seconda connessione `new mysqli(...)` (`$dbhandle`).
6. Connessione SADAS (banca): `odbc_connect('SADAS', NULL, NULL) or die('0')` — usata via `odbc_exec()`.
7. Le pagine "intere" emettono direttamente `<html>...<head>` con gli script FusionCharts; gli include parziali (`area_grafico.php`, `aree_dettaglio.php`) vengono iniettati dentro `statistiche.php`.

### Pattern date (filtri periodo)

```php
if (empty($_GET['datain']))  $_GET['datain']  = $inizioanno;            // inizio esercizio
if (empty($_GET['dataout'])) $_GET['dataout'] = date("d/m/Y", strtotime("-1 day")); // SADAS è "a ieri sera"
```

Eccezione: [`statistiche.php`](../../statistiche.php) usa `datain = '01/01/1900'` (conteggio cumulativo di tutti i soci in essere).

### Pattern filtro area/filiale

Le pagine ricevono `$_GET['filiale']` e/o `$_GET['area']` e costruiscono più varianti di condizione SQL (`$condizionefiliale`, `$condizionefiliale2..5`) perché le query toccano tabelle/viste con nomi di colonna diversi (`FILIALE_CAPOFILA`, `FIL_ANAGRAFICA`, `Filiale`). Se entrambi vuoti → ambito "BANCA". Il flag `?f=999` (passato dalla home/auth) abilita le sezioni riservate ad Admin Soci (es. blocco "Nuove Ammissioni" in `situazione.php`).

### Libreria PDF

**Non esiste FPDF/TCPDF nel dominio statistiche.** Il "report PDF" è in realtà HTML stampabile: il motore [`stats/rep/_report.php`](../../stats/rep/_report.php) genera una pagina HTML/Bootstrap con interruzioni di pagina (`<P style="page-break-before: always">`) destinata alla stampa "Salva come PDF" del browser. FPDF/TCPDF (citati nel CLAUDE.md) sono usati altrove (modulistica), non qui.

---

## 2. Categorie dei file

### 2.1 Cruscotti / pagine di sintesi

| File | Metrica / contenuto |
| --- | --- |
| [`statistiche.php`](../../statistiche.php) | Cruscotto principale: totale soci in essere, ripartizione M/F/Aziende, 5 fasce d'età con % ed età media, età media banca, socio più giovane/anziano, grafico aree (include `area_grafico.php`) e dettaglio aree (include `aree_dettaglio.php`). |
| [`stats/situazione.php`](../../stats/situazione.php) | Pagina più grande (~1.773 righe). Situazione soci/capitale alla data: capitale sociale e n. azioni (iniziale/incremento/decremento/finale), sovrapprezzo, conteggio soci, ammissioni/uscite per filiale, trend andamentale e medie annuali (FusionCharts). |
| [`stats/situazione_plafond.php`](../../stats/situazione_plafond.php) | Situazione del PLAFOND (limite di capitale): `$plafond_iniziale = 400000`, somme per conti contabili SADAS (`CG_MOVIMENTI_CONTABILI`, mastri 2881/2885/...), media ammissioni a 6 mesi. |
| [`stats/previsionale.php`](../../stats/previsionale.php) | Visualizza il previsionale uscite (FULL/LIMIT) per area e filiale a partire dalla tabella materializzata `tab_previsionale`. |
| [`stats/repcda_prospetto_consiglio.php`](../../stats/repcda_prospetto_consiglio.php) | "Prospetto CDA": pagina-indice con link alle viste da copiare per il CdA + genera due CSV (`tmp/cda_liquidati.csv`, `tmp/cda_nonliquidati.csv`) da `TAB_DECADUTI_LIQUIDATI`/`TAB_DECADUTI_NONLIQUIDATI`. |
| [`stats/assemblea_buoni.php`](../../stats/assemblea_buoni.php) | Rilascio buoni assemblea (da `tab_soci_buoni`). |

### 2.2 Grafici FusionCharts (`*_grafico.php`)

Usano `new FusionCharts(tipo, id, w, h, divId, "json", $jsonEncodedData)` + `$chart->render()`; il `<div id=...>` è il punto di rendering.

| File | Grafico / metrica |
| --- | --- |
| [`stats/ammissioni_grafico.php`](../../stats/ammissioni_grafico.php) | Nuove ammissioni soci (trend, 2 grafici). |
| [`stats/andamentale_grafico.php`](../../stats/andamentale_grafico.php) | Andamento soci nel tempo per ambito. |
| [`stats/andamentale_grafico_graphimporto.php`](../../stats/andamentale_grafico_graphimporto.php) | Andamentale per importo (variante grafico). |
| [`stats/andamentale_grafico_trendimporto.php`](../../stats/andamentale_grafico_trendimporto.php) | Andamentale trend importi. |
| [`stats/cessioni_grafico.php`](../../stats/cessioni_grafico.php) | Cessioni a Banca in essere (4 grafici: aree, fasce importo, storico). |
| [`stats/eredi_grafico.php`](../../stats/eredi_grafico.php) | Soci deceduti / trend decessi ed eredi. |
| [`stats/esclusioni_grafico.php`](../../stats/esclusioni_grafico.php) | Soci esclusi, tipologia da liquidare. |
| [`stats/giovani_grafico.php`](../../stats/giovani_grafico.php) | Soci giovani (under 35). |
| [`stats/liquidazioni_grafico.php`](../../stats/liquidazioni_grafico.php) | Liquidazioni effettuate/da effettuare. |
| [`stats/volumi_grafico.php`](../../stats/volumi_grafico.php) | Volumi soci (3 grafici). |
| [`stats/sex_grafico.php`](../../stats/sex_grafico.php) | Torta ripartizione per sesso. |
| [`stats/pfpg_grafico.php`](../../stats/pfpg_grafico.php) | Torta tipo controparte (Persona Fisica / Persona Giuridica). |
| [`stats/area_grafico.php`](../../stats/area_grafico.php) | Grafico soci per area (incluso in `statistiche.php`, div `aree0`). |
| [`stats/aree_grafico.php`](../../stats/aree_grafico.php) | Soci in essere per area in percentuale (3 grafici). |

### 2.3 Dettagli e drill-down per area/filiale (`*_area.php`, `*_filiale.php`, `*_dettaglio.php`)

| File | Contenuto |
| --- | --- |
| [`stats/ammissioni_area.php`](../../stats/ammissioni_area.php) | Soci ammessi per area + trend mensile. |
| [`stats/ammissioni_filiale.php`](../../stats/ammissioni_filiale.php) | Soci ammessi per filiale + trend mensile. |
| [`stats/ammissioni_dettaglio.php`](../../stats/ammissioni_dettaglio.php) | Elenco di dettaglio ammissioni. |
| [`stats/cessioni_area.php`](../../stats/cessioni_area.php) | Trend cessioni a Banca per area. |
| [`stats/cessioni_filiale.php`](../../stats/cessioni_filiale.php) | Trend cessioni a Banca per filiale. |
| [`stats/cessioni_dettaglio.php`](../../stats/cessioni_dettaglio.php) | Elenco di dettaglio cessioni a Banca. |
| [`stats/liquidazioni_dettaglio.php`](../../stats/liquidazioni_dettaglio.php) | Dettaglio liquidazioni effettuate per ambito. |
| [`stats/aree_dettaglio.php`](../../stats/aree_dettaglio.php) | Tabella riepilogo per area (incluso in `statistiche.php`; usa `view_pf_pg`, `sds_soci`, `tab_psw`). |
| [`stats/volumi_area.php`](../../stats/volumi_area.php) | Volumi soci per area. |
| [`stats/volumi_filiale.php`](../../stats/volumi_filiale.php) | Volumi soci per filiale. |

### 2.4 Fasce (segmentazioni)

| File | Segmentazione |
| --- | --- |
| [`stats/fasce_azioni.php`](../../stats/fasce_azioni.php) | Distribuzione soci per fasce di numero azioni. |
| [`stats/fasce_classisocio.php`](../../stats/fasce_classisocio.php) | Classi soci per anzianità di appartenenza alla compagine sociale. |
| [`stats/fasce_classisocio_rapporto.php`](../../stats/fasce_classisocio_rapporto.php) | Classi soci per anzianità di rapporto bancario. |
| [`stats/fasce_consenzarichieste.php`](../../stats/fasce_consenzarichieste.php) | Fasce quote soci senza richieste in corso (prima tabella usata dal CdA). |
| [`stats/fasce_eta_per_anno.php`](../../stats/fasce_eta_per_anno.php) | Distribuzione per fascia d'età per anno. |

### 2.5 Utility / test

| File | Note |
| --- | --- |
| [`stats/_sadas_test.php`](../../stats/_sadas_test.php) | Script di test connessione/query SADAS. |
| `*.old`, `*.backup` | Versioni storiche (es. `situazione.php.old`, `situazione_plafond.php.old`, `liquidazioni_grafico.php.old`) — debito tecnico, da rimuovere. |

---

## 3. Dettaglio pagine chiave

### 3.1 `statistiche.php` (cruscotto root)

- **Parametri**: `datain` (default `01/01/1900`), `dataout` (default ieri). Nessun filtro filiale.
- **Tabelle/viste MySQL**: `sds_soci`, `view_fasce`, `view_fasce_etamedia`.
- **Calcoli**:
  - Conteggio per `SESSO` (M/F/altro=Aziende) su `sds_soci` con `DATA_USCITA = '0' OR > NOW()` → totale soci.
  - 5 fasce d'età aggregate da `view_fasce`, con `%` = `Fascia / totalesoci / 10` ed età media da `view_fasce_etamedia`.
  - Età media banca: `AVG(eta)` su PF attivi.
  - Socio più giovane/anziano: `UNION` di `MAX/MIN(DATA_NASCITA)` su `sds_soci` (TIPO_NAG='PF').
- **Include**: `stats/area_grafico.php` (grafico aree), `stats/aree_dettaglio.php` (tabella aree).

### 3.2 `stats/situazione.php`

- **Parametri**: `datain`/`dataout`, `filiale`, `area`, `f` (999=Admin).
- **Fonte dati**: SADAS via `odbc_exec` (`SOCI_CERTIFICATI`, `SOCI_MOVIMENTI`, `SOCI_ANAGRAFICA`, `ANAG_NAG`) + tabelle temporanee MySQL (`tmp_soci_inout2`) e viste (`view_ammissioni_uscite`).
- **Blocchi**:
  - **A. Capitale sociale** (A1 iniziale / A2 incremento / A3 decremento / A4 finale): `SUM(NAZIONI * 30.33)` (valore nominale azione = 30,33 €) e numero azioni, filtrando per `DATA_ACQUISTO`/`DATA_ANNULLAMENTO`/`DATA_VENDITA` rispetto a datain/dataout.
  - **B. Sovrapprezzo** (B1..B4): `SUM(ISOVRAPPREZZO)` per data movimento.
  - **C. Soci** (C1..C4): `COUNT(*)` iniziale/incremento/decremento/finale.
  - Costruzione tabelle temporanee per filiale (`createtable_1..8`, `createtable_inout3`) → ammissioni/uscite e capitale per filiale.
  - **Grafici** (FusionCharts `msline`): andamentale ammissioni/uscite per filiale (`amm2`), trend mensile da `view_ammissioni_uscite` (`amm3`), medie annuali 2020→anno corrente.
- È la base del prospetto CdA "01 - Situazione".

### 3.3 `stats/situazione_plafond.php`

- **Parametri**: `datain`/`dataout`, `filiale`/`area`, `nominativo` (verifica cessione singolo socio).
- **Costanti**: `$plafond_iniziale = 400000`; `$dataMediaPlafond` = datain − 6 mesi.
- **Fonte**: SADAS `CG_MOVIMENTI_CONTABILI` filtrato su `DATA_CONT` e su conti contabili (mastri 2881, 2885, ...). Calcola somme di movimento per costruire la disponibilità del plafond e proiettare la capienza dato il ritmo medio di ammissioni.

### 3.4 `stats/previsionale.php` + `crea_previsionale.php`

- **`crea_previsionale.php`** (job, richiamato da `CREA_SDS_SOCI.PHP`): tronca e ricostruisce `TAB_PREVISIONALE` con due dataset:
  - `FULL` da `view_previsionale_full`, `LIMIT` da `view_previsionale` (fino all'anno precedente).
  - Per ogni filiale calcola importi per tipologia uscita (ESCLUSIONE, ESCLUSIONE SOFFERENZA, RECESSO, MORTE, CESSIONE A BANCA), `TOTALE` e `NUMERO_SOCI` necessari al pareggio = `round((TOTALE / 30.33) / 33)` (33 = numero medio azioni per nuovo socio). Aggiorna `tab_ultimo_caricamento` e invia mail (`routines/mail_dip.php`).
- **`stats/previsionale.php`**: legge `tab_previsionale` filtrando `Tipo` (FULL/LIMIT) e area/filiale, mostra due tabelle (per Area con `ROLLUP`, per Filiale ordinata).

---

## 4. Motore di reportistica PDF — `stats/rep/_report.php`

Il "Report Soci" complessivo per Direzione/CdA è generato da [`stats/rep/_report.php`](../../stats/rep/_report.php), che funge da orchestratore.

### Funzionamento

1. Bootstrap comune (MySQL + SADAS), emette `<html>` con CSS Bootstrap e script FusionCharts.
2. Normalizza i parametri:
   - `datain`/`dataout` (default `01/01/<anno>` → ieri).
   - `periodo` (es. `202201`) → `$Condizione_AnnoMeseRichiesta` + `$datarichiesta`; calcola `$numeromesi` (mesi trascorsi).
   - `key` (codici filiale/area) e `area` → imposta `$rif` (`BANCA` / `Filiale` / `Area`), `$titolofiliale` e le condizioni `$condizionefiliale..5`.
3. Include in sequenza i blocchi del report, ognuno preceduto da un'interruzione di pagina HTML (`page-break-before`):

| Ordine | Include | Sezione |
| --- | --- | --- |
| 1 | `rep_00_cover.php` | Copertina (logo CB, "REPORT SOCI", ambito e periodo). |
| 2 | `rep_01_statistiche.php` | Statistiche generali (totali, sesso, fasce età, età media, giovane/anziano) da `sds_soci`/`view_fasce`. |
| 3 | `rep_02_situazione.php` | Situazione capitale/sovrapprezzo/soci da SADAS (`SOCI_CERTIFICATI`, `SOCI_MOVIMENTI`, `SOCI_ANAGRAFICA`, `ANAG_NAG`). |
| 4 | `rep_02b_previsionale.php` | Previsionale uscite per area/filiale da `tab_previsionale`. |
| 5 | `rep_03_liquidazioni.php` | Liquidazioni da `TAB_DECADUTI_NONLIQUIDATI` (e liquidati). |
| 6 | `rep_04_giovani.php` | Giovani Under 35 da `view_under35`. |
| 7 | `rep_05_azionifasce.php` | Azioni e fasce d'età da `view_fasce_azioni`. |
| 8 | `rep_06_socistorici.php` | Soci storici per anzianità da `view_fasce_anzianitasocio` + `tab_mutua`. |
| 9 | `rep_99_indici.php` | Indici generali uscite (`tab_valorefondo`, `tab_xls_cessionibanca`, `tab_xls_esclusioni`, `view_decessi`). |
| 10 | `rep_99_filiali.php` | Elenco filiali da `tab_psw` (solo se `$rif` = Area o BANCA). |

4. Ogni `rep_*.php` costruisce stringhe HTML (`$tab_dettaglio...`) e le stampa; la "PDF" finale è la stampa del browser.

### Lancio

- Admin Soci ([`soci_auth.php`](../../soci_auth.php)): `stats/rep/_report.php?periodo=202201`.
- Area ([`area_auth.php`](../../area_auth.php)): `stats/rep/_report.php?key=<chiave>&periodo=202001`.
- Filiale ([`filiale_auth.php`](../../filiale_auth.php)): `stats/rep/_report.php?key=<chiave>&periodo=202001`.

---

## 5. Tabella riepilogativa file → metrica/output

| File | Output | Fonte dati principale | Tipo |
| --- | --- | --- | --- |
| `statistiche.php` | Cruscotto soci/sesso/età | `sds_soci`, `view_fasce*` | MySQL + FC |
| `stats/situazione.php` | Capitale/sovrapprezzo/soci, andamentale | SADAS + `tmp_soci_inout2`, `view_ammissioni_uscite` | SADAS + MySQL + FC |
| `stats/situazione_plafond.php` | Plafond e capienza | SADAS `CG_MOVIMENTI_CONTABILI` | SADAS |
| `stats/previsionale.php` | Previsionale uscite per area/filiale | `tab_previsionale` | MySQL |
| `crea_previsionale.php` | (Job) popola `TAB_PREVISIONALE` | `view_previsionale[_full]` | MySQL |
| `stats/*_grafico.php` | Grafici (ammissioni, cessioni, esclusioni, eredi, giovani, liquidazioni, volumi, sesso, pf/pg, aree, andamentale) | viste/SADAS varie | FC |
| `stats/*_area.php` / `*_filiale.php` | Drill-down per area/filiale | viste/`tab_psw` | MySQL + FC |
| `stats/*_dettaglio.php` | Elenchi di dettaglio | viste/SADAS | MySQL/SADAS |
| `stats/fasce_*.php` | Segmentazioni (azioni, classi socio, quote, età/anno) | `view_fasce*` | MySQL + FC |
| `stats/assemblea_buoni.php` | Buoni assemblea | `tab_soci_buoni` | MySQL |
| `stats/repcda_prospetto_consiglio.php` | Indice CdA + CSV liquidazioni | `TAB_DECADUTI_*` | MySQL |
| `stats/rep/_report.php` + `rep_*.php` | Report PDF complessivo | tutte le precedenti | HTML stampabile |

---

## 6. Tabelle e viste dati ricorrenti

- **MySQL applicative**: `sds_soci`, `tab_psw` (anagrafica filiali), `tab_previsionale`, `tab_soci_buoni`, `tab_mutua`, `tab_valorefondo`, `tab_xls_cessionibanca`, `tab_xls_esclusioni`, `tab_ultimo_caricamento`, `TAB_DECADUTI_LIQUIDATI`, `TAB_DECADUTI_NONLIQUIDATI`.
- **Viste**: `view_fasce`, `view_fasce_etamedia`, `view_fasce_azioni`, `view_fasce_anzianitasocio`, `view_ammissioni_uscite`, `view_previsionale`, `view_previsionale_full`, `view_under35`, `view_decessi`, `view_pf_pg`.
- **SADAS (ODBC)**: `SOCI_CERTIFICATI`, `SOCI_MOVIMENTI`, `SOCI_ANAGRAFICA`, `ANAG_NAG`, `CG_MOVIMENTI_CONTABILI`.
- **Costanti di dominio**: valore nominale azione **30,33 €**; **33** azioni medie per nuovo socio; plafond iniziale **400.000 €**.

---

## 7. Debito tecnico e rischi

| Rilievo | Dettaglio | Gravità |
| --- | --- | --- |
| **SQL injection** | `$_GET['datain']`, `dataout`, `filiale`, `area`, `key`, `periodo`, `nominativo` concatenati direttamente nelle query MySQL e SADAS senza prepared statement né validazione (escape solo sporadico in `crea_previsionale.php`). | Alta |
| **Query inline e duplicate** | Le stesse query A1..A4 / B / C compaiono in `situazione.php` e in `rep_02_situazione.php`; le tabelle previsionale in `previsionale.php` e `rep_02b_previsionale.php`. Nessuna funzione condivisa. | Alta |
| **Doppia connessione MySQL** | Ogni pagina apre `mysqli_connect()` + `new mysqli()` (`$dbhandle`) per compatibilità FusionCharts. | Media |
| **Soppressione errori** | `error_reporting(0)` in `_report.php` nasconde errori SQL/ODBC in produzione, difficile diagnosticare. | Media |
| **File `.old`/`.backup`** | `situazione.php.old/.backup`, `situazione_plafond.php.old`, `liquidazioni_grafico.php.old`, `rep_*.php.old` mantenuti accanto agli attivi. | Bassa |
| **Date/periodi cablati** | `repcda_prospetto_consiglio.php` parte da `2020-01-01`; `_report.php` usa `periodo` di default cablato (`202201`/`202001`) nei link auth. | Media |
| **PDF = stampa browser** | Nessuna libreria PDF reale per il report: dipende da resa HTML/FusionCharts e dal "Salva come PDF" del browser; layout fragile su grafici JS. | Media |
| **Logica morta** | Grande blocco "anzianità rapporto" commentato in `statistiche.php`; controlli `HTTP_REFERER` commentati (auth aggirabile). | Bassa |
| **Branch colore inutile** | In `previsionale.php` il ternario su `NUMERO_SOCI` assegna lo stesso `style="color:red;"` in entrambi i rami. | Bassa |
