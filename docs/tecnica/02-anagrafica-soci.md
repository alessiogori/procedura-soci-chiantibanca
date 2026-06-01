# Documento Tecnico — Anagrafica Soci, Schede Socio e Stati del Socio

> Dominio: gestione della base sociale (anagrafica soci, scheda del singolo socio, stati del socio).
> Stack: PHP senza framework, MySQL `soci` (mysqli) + SADAS via ODBC (`odbc_connect('SADAS')`), Bootstrap/jQuery/DataTables, FusionCharts, FPDF/TCPDF, PHPMailer.

## 1. Indice delle pagine documentate

| File | Scopo | Fonte dati prevalente |
|------|-------|------------------------|
| [lista_soci.php](../../lista_soci.php) | Estrazione completa "Situazione Soci in essere" in CSV | MySQL |
| [schedasocio.php](../../schedasocio.php) | Maschera di ricerca socio (form) | MySQL |
| [sqldati_schedasocio.php](../../sqldati_schedasocio.php) | Scheda anagrafica del singolo socio (dati + tab) | MySQL + SADAS |
| [mutua_listaschedasocio.php](../../mutua_listaschedasocio.php) | Elenco soci ChiantiMutua (lato ex-Mutua) | MySQL |
| [giovani_lista.php](../../giovani_lista.php) | Soci/clienti giovani (Under 35) per età | MySQL |
| [lista_ammissioni.php](../../lista_ammissioni.php) | Nuove ammissioni a socio nel periodo | MySQL + SADAS |
| [lista_usciti.php](../../lista_usciti.php) | Soci usciti nel periodo | MySQL + SADAS |
| [deceduti.php](../../deceduti.php) | Soci deceduti e stato pratiche eredi | MySQL (view) |
| [deceduti_presunti.php](../../deceduti_presunti.php) | Soci attivi con documentazione che presume decesso | SADAS |
| [migracarte_lista.php](../../migracarte_lista.php) | Migrazione/stampa carte debito ICCREA | MySQL |
| [soci_ass.php](../../soci_ass.php) | Gestionale presenze/deleghe assemblea | MySQL |
| [soci_ass_edit.php](../../soci_ass_edit.php) | Modifica presenza/delega/pullman del socio | MySQL |
| [soci_ass_totali.php](../../soci_ass_totali.php) | Statistiche/totalizzatori assemblea | MySQL |

Tutte le pagine includono `config/_config.php` (credenziali MySQL + `$inizioanno`) e `config/_functions.php`. Le pagine interattive includono anche `css/main.php` e `css/menu.php`; le estrazioni/report autonomi stampano invece l'`<html>` direttamente e importano i CSS Bootstrap.

---

## 2. Modello dati Socio

### 2.1 `sds_soci` — anagrafica socio principale (MySQL)
Tabella consolidata costruita dai job batch (`crea_sds_soci.php`, `crea_sds_soci_dati_consolidati.php`) a partire da SADAS. Campi chiave rilevati dalle query:

| Campo | Significato (dedotto dall'uso) |
|-------|-------------------------------|
| `IDSOCIO` | Identificativo socio (numero socio). Chiave usata in tutti i link `sqldati_schedasocio.php?id=` |
| `IDSOCIO_SUB` | IDSocio del socio "subentrato" (es. erede o cessionario) — usato per collegare ammissione e defunto |
| `NAG` | Numero Anagrafico Generale (CAG) del soggetto sul sistema banca |
| `INTESTAZIONE_A`, `INTESTAZIONE_B` | Cognome/nome o ragione sociale (concatenati in `INTESTAZIONE`) |
| `CODICE_FISCALE`, `PARTITA_IVA` | Identificativi fiscali |
| `TIPO_NAG` | `PF` persona fisica / (PG persona giuridica) |
| `TIPO_SOGGETTO`, `SESSO` | Tipologia soggetto; sesso `M`/`F` (altro = azienda) |
| `DATA_NASCITA`, `ETA` | Data nascita (formato `AAAAMMGG`) ed età calcolata |
| `DATA_ENTRATA` | Data di ingresso come socio (formato `gg/mm/aaaa`) |
| `DATA_USCITA` | Data uscita (0 = ancora socio) |
| `DATA_DECESSO` | Data decesso (0 = non deceduto) |
| `CTIPMOVUSCITA` | Tipo movimento di uscita: `ES` esclusione, `RE` recesso, `MO` morte, `'  '`/blank = cessione quote a banca |
| `SOCIO_ISTITUTO` | `1` = socio a capitale attivo; `9`/diverso da 1 = ex socio |
| `STATO_NAG` | `0` cliente potenziale, `1` cliente con rapporti, `2` ex cliente |
| `DIRITTO_DI_VOTO` | Data da cui matura il diritto di voto |
| `FILIALE_CAPOFILA` | Filiale anagrafica del socio |
| `PA_3` | Codice piazza (join con `sds_anag_piazze`) |
| `SETTORISTA` | Codice gestore (join con `tab_dipendenti`) |
| `SETTORE`, `RAMO`, `PROF_ATTIVITA` | Codici merceologici (join con `sds_soci_merceologico`) |
| `SEGMENTO_CLIENTE` | Segmentazione (valore `18` = dipendente ChiantiBanca) |
| `COD_RAPP`, `FILIALE_RAPP`, `NUM_RAPP` | Coordinate del rapporto/conto corrente di riferimento |
| `ACQUISTO_PERIOD`, `NAZIONI_PERIOD`, `DATA_FINEPACK_PERIOD` | "PACK azioni": acquisto periodico (`Y`), n. azioni/mese, fine pack |
| `VIA_RES`, `CAP_RES`, `DESCR_COM_RES`, `LOCALITA_RES`, `PROVINCIA_RES` | Indirizzo di residenza |
| `INTESTAZ_CORR`, `VIA_CORR`, `CAP_CORR`, `DESCR_COM_CORR`, `LOCALITA_CORR`, `PROVINCIA_CORR` | Indirizzo di corrispondenza/spedizione |
| `TEL`, `CELL`, `MAIL`, `PEC` | Contatti (presenti anche in tabella dedicata) |
| `NAG_RAPPR`, `INTESTAZIONE_RAPPR`, `CODICE_FISCALE_RAPPR`, `DATA_NASCITA_RAPPR`, `TEL_RAPPR` | Dati del rappresentante/delegato (per persone giuridiche) |

### 2.2 Tabelle collegate (MySQL, prefisso `sds_`)

| Tabella | Contenuto | Join tipico |
|---------|-----------|-------------|
| `sds_soci_certificati` | Certificati azionari: `NUMERO_AZIONI`, `VALORE_AZIONI` | `ON sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO` |
| `sds_soci_daticontatto` | Contatti per NAG: `TIPO_DATO_CNT` (`TEL`/`CELL`/`MAIL`/`PEC`), `VALORE_DATO_CNT`, `PROCEDURA`, `NOTE` | `ON sds_soci.NAG = sds_soci_daticontatto.NAG` |
| `sds_soci_movinout` | Movimenti di ingresso/uscita: `CTIPOMOV` (`AM` ammissione, `ID` inizio decadenza, `RE` recesso, `ES` esclusione, `MO` morte, `RS` subentro eredi, `RL` rimborso eredi), `DATA_DELIBERA`, `XMOTIVO` | `ON sds_soci.idsocio = movinout.idsocio` |
| `sds_soci_tipomovimento` | Decodifica `CTIPOMOV` → `XTIPOMOV` | `ON t.ctipomov = tm.ctipomov` |
| `sds_soci_merceologico` | Decodifica ramo/settore/professione (`RIFERIMENTO`, `TIPO`, `DESCRIZIONE`) | per `ramo`/`settore`/`PROF_ATTIVITA` |
| `sds_anag_piazze` | Descrizione piazze (`PA_3`, `DESCR_PIAZZA`) | `ON sds_soci.PA_3 = sds_anag_piazze.PA_3` |
| `sds_soci_prodotto_cc` | Conto/prodotto C/C del socio (`COD_CLASSE`, `DESCRIZIONE`, `STATO`) | su `COD_RAPP`+`FILIALE_RAPP`+`NUM_RAPP` |
| `SDS_SOCI_DOMANDE` / `sds_soci_domande` | Domande: `CTIPODOM` (`DA` ammissione, `DL` rimborso/liquidazione, `DQ` ulteriori quote, `DR` recesso, `DS` subentro erede, `DT` trasferimento), `SOGLIA`, `SUBENTRANTE_IDSOCIO`, `TRASFERIMENTO_DA_IDSOCIO`, `DEFUNTO_IDSOCIO`, date domanda/delibera/operazione/accoglimento | per `NAG` o `DEFUNTO_IDSOCIO` |
| `SDS_SOCI_ISIDOC` | Documenti ISIDOC del socio (`COD_CONTRATTO`, `DESCR_DOCUMENTO`, date, `PRESENZA_DOCUMENTO`) | per `NAG` |
| `sds_soci_under35` | Vista/tabella pre-calcolata dei giovani (`ETA`, `NAG`, `INTESTAZIONE_A/B`, `FIL_ANAGRAFICA`, `RAPPORTI`, `SOCIOBANCA`) | usata da `giovani_lista.php` |
| `view_decessi` | Vista decessi: `IDSOCIO`, `NAG`, `Nominativo`, `NumeroAzioni`, `ValoreTotaleAzioni`, `Data_Decesso`, `Data_Uscita`, `Filiale_Capofila`, `Desc_Filiale`, `Area` | usata da `deceduti.php` |

### 2.3 Tabelle di supporto (MySQL, prefisso `tab_`)

| Tabella | Contenuto |
|---------|-----------|
| `TAB_MUTUA` / `tab_mutua` | Anagrafica soci ChiantiMutua (`CODICEFISCALE`, `NAG`, `cognome`, `nome`, `ClasseTariffaria`, `sociodal`, `Filiale`, `sesso`) |
| `tab_psw` | Decodifica filiali (`filiale`, `desc_filiale`, `luogo`) |
| `tab_dipendenti` | Decodifica settorista/gestore (`settorista`, `dipendente`, `mansionewprof`) |
| `tab_monitor_soci` | Segnalazioni "Monitor" sul socio (`cag`, `attivo`, esiti) |
| `tab_xls_cessionibanca` | Richieste di cessione quote a Banca (`IDSOCIO`, `Rimborsato`, date) |
| `tab_xls_esclusioni` | Esclusioni (`IDSOCIO`, `MovimentoSicra` `ID`/`RL`/`ES`, art.6/art.14/sofferenze, date) |
| `tab_xls_decessi_eredi_storico` | Storico decessi/eredi (fonte "SIB") |
| `tab_eventi_iscrizioni` | Iscrizioni del socio agli eventi |
| `tab_comuni_soci_note` | Note su requisito di competenza territoriale (`status_esito`) |
| `tab_motivazioni` | Motivazioni di ammissione inserite a mano |
| `tab_storico_pistoia` | Dato storico ex Pistoia (`prot`, `dataEntrata_origine`) |
| `tab_soci_as37`, `tab_soci_scelta` | Tabelle assemblea (anagrafica estratta + scelte presenza/delega/location) |
| `migracarte_archivio`, `migracarte_stampa` | Archivio carte debito ICCREA e stato stampa contratti |

### 2.4 Tabelle SADAS (via ODBC) usate direttamente

| Tabella SADAS | Uso |
|---------------|-----|
| `SOCI_ANAGRAFICA`, `ANAG_NAG`, `ANAG_RAPPORTI` | Trasferimenti tra soci, tipo spedizione |
| `SOCI_MOVIMENTI` | Movimenti quote: `CTIPOMOV` `TR`/`CO`/`FU`/`DO`/`SU`/`VE` (trasferimento/compravendita/fusione/donazione/successione/vendita) |
| `ANAG_PERSONE_FISICHE`, `ANAG_PERSONE_GIURIDICHE` | Data nascita per calcolo Under35, decessi presunti |
| `ISIDOC_CONTRATTI`, `ISIDOC_DOCUMENTI_PERSONALE` | Conteggio documenti contrattuali e documenti personali (decessi) |

---

## 3. Stati del socio

Lo stato del socio è ricostruito a runtime combinando più campi (non esiste un singolo campo "stato").

### 3.1 Status come socio — `SOCIO_ISTITUTO`
- `1` → **Socio a capitale** (in essere). Etichetta "Socio a capitale", pallino verde.
- `9` / diverso da 1 → **Ex Socio**.

### 3.2 Status anagrafico cliente — `STATO_NAG`
- `0` → Cliente Potenziale (POT)
- `1` → Cliente con rapporti (CLI / "Cliente")
- `2` → Ex Cliente (EX)

### 3.3 Motivo di uscita — `CTIPMOVUSCITA` / `sds_soci_movinout.CTIPOMOV`
- `ES` → Uscito per Esclusione (pallino rosso)
- `RE` → Uscito per Recesso (pallino rosso)
- `MO` → Uscito per Decesso/Morte (pallino bianco)
- `'  '` (blank) con `DATA_USCITA` valorizzata → Uscito per cessione quote a Banca (pallino rosso)
- nessun motivo e nessuna data uscita → In essere (pallino verde)

### 3.4 Stati derivati / liste dedicate
- **Deceduto**: `DATA_DECESSO != 0` (e/o `CTIPMOVUSCITA = 'MO'`). Gestito da `deceduti.php` via `view_decessi`.
- **Deceduto presunto**: socio **ancora attivo** (`SOCIO_ISTITUTO = '1'`) ma con documento ISIDOC personale tra i tipi `DI000006TP000001/03/04/06/07` → `deceduti_presunti.php`.
- **Giovane (Under 35)**: calcolato confrontando `DATA_NASCITA` con la data odierna meno 35 anni (`$DataLimiteU30`), oppure pre-calcolato in `sds_soci_under35`.

### 3.5 Codifica colori (icone pallino)
`img/ico_pallino_green.png` = in essere; `ico_pallino_red.png` = uscito (esclusione/recesso/cessione/estinto); `ico_pallino_white.png` = uscito per decesso; `ico_pallino_yellow.png` = stato in sospeso (Monitor).

---

## 4. Dettaglio pagine

### 4.1 [lista_soci.php](../../lista_soci.php) — Situazione Soci in essere
- **Parametri**: nessuno (estrazione totale).
- **Query principale (MySQL)**: `sds_soci` LEFT JOIN `sds_soci_certificati` (azioni), `sds_soci_daticontatto` (contatti), `tab_storico_pistoia`; filtro `WHERE SOCIO_ISTITUTO = '1'`; `GROUP BY INTESTAZIONE, DATA_NASCITA, IDSOCIO, NAG, FILIALE_CAPOFILA`.
- **Sub-query per riga**: lookup su `TAB_MUTUA` per CF → flag socio Mutua.
- **Output**: nessuna tabella HTML; genera `tmp/lista_soci.csv` (separatore `;`) e mostra un pulsante di download dopo 5s (timeout JS).
- **Valore azioni** calcolato come `NUMERO_AZIONI * 30.33`.

### 4.2 [schedasocio.php](../../schedasocio.php) — Ricerca socio
- **Parametri**: `POST ricerca` (cognome/nome, CAG, numero socio, telefono), opzionale `POST filiale`, `POST conProdottoCC`.
- Se `ricerca` vuota mostra solo il form (action verso `schedasocio2.php`, **da verificare** la presenza di tale file).
- **Query (MySQL)**: stessa base di `lista_soci` con LIKE multipli su intestazione/rappresentante/contatto/IDSOCIO/NAG/CF; opzionale JOIN `sds_soci_prodotto_cc`.
- **Sub-query per riga**: `tab_monitor_soci`, `tab_xls_cessionibanca`, `tab_xls_esclusioni` (sofferenza), `TAB_MUTUA`, `tab_psw` (filiale), `tab_dipendenti` (settorista).
- **Output**: tabella DataTables + CSV `tmp/listasoci{filiale}.csv`. Il nominativo è link a `sqldati_schedasocio.php?id=IDSOCIO`.

### 4.3 [sqldati_schedasocio.php](../../sqldati_schedasocio.php) — Scheda del singolo socio
- **Parametri**: `GET id` (= IDSOCIO).
- **Tracciatura**: funzione `logAlbaNAG($NAG)` (via `lib/loggerALBA.php`), utente derivato dal cookie `usr_id` come `LN` + id padded a 5 cifre; redirect a login se cookie assente.
- **Query anagrafica (MySQL)**: `sds_soci` LEFT JOIN `sds_soci_certificati`, `sds_anag_piazze`, `WHERE IDSOCIO = '$_GET[id]'`.
- **Sezioni / sub-query**:
  - `TAB_MUTUA` (badge Mutua), `tab_psw` (filiale), `tab_dipendenti` (settorista), `tab_comuni_soci_note` (note competenza territoriale).
  - `sds_soci_movinout` + `sds_soci_tipomovimento` (motivi ingresso/uscita e date delibera CDA).
  - `sds_soci_daticontatto` (contatti), `sds_soci_merceologico` (ramo/settore/professione con UNION), `tab_eventi_iscrizioni` (eventi).
  - **SADAS**: `SOCI_ANAGRAFICA`+`ANAG_RAPPORTI` (tipo spedizione `P`/`H`/`K` = Posta/Relax Banking/PEC), `ISIDOC_CONTRATTI` e `ISIDOC_DOCUMENTI_PERSONALE` (conteggio documenti), `SOCI_MOVIMENTI`+`ANAG_NAG` (trasferimenti/cessioni a socio).
- **Tab pagina**: Info, Monitor (`tab_monitor_soci`), Domande (`SDS_SOCI_DOMANDE`), Documenti (`SDS_SOCI_ISIDOC` + SADAS), Trasferimenti / Cessioni a Banca (`tab_xls_cessionibanca`) / Cessioni a Socio (SADAS `SOCI_MOVIMENTI`), Esclusioni (`tab_xls_esclusioni` + `sds_soci_movinout`), Decessi (UNION `tab_xls_decessi_eredi_storico` "SIB" + `sds_soci_movinout` "SICRA").
- **Output**: HTML (fieldset + tabella + tab Bootstrap). Nessun CSV.

### 4.4 [mutua_listaschedasocio.php](../../mutua_listaschedasocio.php) — Elenco soci ChiantiMutua
- **Parametri**: `POST ricerca` (cognome/nome o CF), `POST filiale` (numero, oppure `'full'` = tutte tranne 990).
- **Query (MySQL)**: `tab_mutua` LEFT JOIN `sds_soci` ON NAG; campo derivato `SocioBanca` da `socio_istituto` (1=Socio Banca, 9=Ex Socio Banca).
- **Output**: DataTables + CSV `tmp/listasocimutua{filiale}.csv`. Link a `sqldati_schedasocio.php?id=idsocio`.

### 4.5 [giovani_lista.php](../../giovani_lista.php) — Soci/clienti giovani (Under 35)
- **Parametri**: `GET eta` (anni), `GET filiale` (lista), `GET rapporti` (`si`/altro), `GET socio` (`si`/altro).
- **Query (MySQL)**: `SELECT * FROM sds_soci_under35` con filtri su `FIL_ANAGRAFICA`, `eta`, `rapporti`, `SOCIOBANCA`; `GROUP BY NAG`.
- **Sub-query per riga**: `sds_soci` per ricavare `IDSOCIO` da NAG; `tab_mutua` per flag Mutua.
- **Output**: DataTables; link scheda socio. Nessun CSV.

### 4.6 [lista_ammissioni.php](../../lista_ammissioni.php) — Ammissioni nel periodo
- **Parametri**: `GET datain` (default `$inizioanno`), `GET dataout` (default ieri), `GET filiale`, `GET area`.
- **Validazione**: funzione `isValidDate()` (aggiunta 06/02/25) — `die()` se le date non sono valide `d/m/Y`.
- **Query (MySQL)**: `sds_soci s1` LEFT JOIN `sds_soci_certificati`, self-join su `IDSOCIO_SUB` (socio defunto), LEFT JOIN `sds_soci_prodotto_cc`; filtro su `DATA_ENTRATA` nel range; `GROUP BY s1.NAG`. Più due query di conteggio (totale + età media su `TIPO_NAG='PF'`).
- **Sub-query per riga**: `tab_motivazioni` (presenza motivazione ammissione); **SADAS** `ANAG_PERSONE_FISICHE`/`ANAG_PERSONE_GIURIDICHE` per flag U35.
- **Output**: tabella HTML + CSV `tmp/ammissioni.csv`. Conta gli U35.

### 4.7 [lista_usciti.php](../../lista_usciti.php) — Soci usciti nel periodo
- **Parametri**: come ammissioni (`datain`/`dataout`/`filiale`/`area`) ma filtro su `DATA_USCITA`.
- **Query (MySQL)**: `sds_soci s1` + `sds_soci_certificati` + self-join `IDSOCIO_SUB` + `sds_soci_prodotto_cc`; conteggio totale ed età media.
- **Decodifica uscita** via `switch` su `CTIPMOVUSCITA` (MO/ES/RE/blank=CB) e su `STATO_NAG` (0/1/2 → POT/CLI/EX). Flag U35 da SADAS.
- **Output**: tabella HTML + CSV `tmp/usciti.csv`.

### 4.8 [deceduti.php](../../deceduti.php) — Soci deceduti e pratiche eredi
- **Parametri**: `GET datain`, `GET dataout`, `GET filiale`, `GET area`.
- **Query (MySQL)**: tutte su `view_decessi` LEFT JOIN `sds_soci_domande` (su `IDSOCIO = DEFUNTO_IDSOCIO`). Cinque conteggi: totale, intestazioni eredi eseguite (`DATA_DELIBERA!=0 AND CTIPODOM!='DL'`), senza domande (`DATA_DOMANDA is null`), intestazioni in corso (`DATA_DELIBERA=0 AND CTIPODOM!='DL'`), liquidazioni avanzate (`CTIPODOM='DL'`). Query di dettaglio con decodifica `CTIPODOM` (DL/DS/DA).
- **Output**: riepilogo + tabella `dataTable` con pulsante "Seleziona tabella per CTRL+C" (no DataTables, no CSV — export via copia/incolla).

### 4.9 [deceduti_presunti.php](../../deceduti_presunti.php) — Decessi presunti
- **Parametri**: `GET filiale`, `GET area`.
- **Query (SADAS, ODBC)**: join `ISIDOC_DOCUMENTI_PERSONALE` + `SOCI_ANAGRAFICA` + `ANAG_NAG` + `ANAG_PERSONE_FISICHE`; filtro `SOCIO_ISTITUTO='1'` AND `TIPO_NAG='PF'` AND documento tra i 5 codici personali di morte. Decodifica `STATO_NAG`/`SOCIO_ISTITUTO`.
- **Output**: tabella HTML (copia/incolla). Nessun CSV.

### 4.10 [migracarte_lista.php](../../migracarte_lista.php) — Migrazione carte debito ICCREA
- **Parametri**: `POST ricerca` (cognome/nome, CAG, numero carta).
- **Query (MySQL)**: `migracarte_archivio a` LEFT JOIN `migracarte_stampa s` ON `Carta`; LIKE su nominativo/carta/CAG. Sub-query `tab_psw` per nome/luogo filiale.
- **Output**: DataTables; se `RichiestoFlussoICCREA='S'` link a `modulistica/_testdmx.php` (contratto precompilato modello DMX). Non è gestione di stati del socio in senso stretto, ma di carte associate ai soggetti. Età socio calcolata in modo grezzo: `date("Y") - substr(DataNascita,-4)`.

### 4.11 Assemblea — [soci_ass.php](../../soci_ass.php) / [soci_ass_edit.php](../../soci_ass_edit.php) / [soci_ass_totali.php](../../soci_ass_totali.php)
Gestionale **presenze / deleghe / pullman / location** per l'assemblea soci (non è anagrafica in senso stretto, ma vista sulla base sociale ai fini del voto).
- **soci_ass.php**: autenticazione tramite `POST psw`; un grosso `switch` mappa la password a un set di filiali (`a.codFil in (...)`) — credenziali e perimetro filiali **hardcoded**. Query su `tab_soci_as37 a` JOIN `tab_soci_scelta b` ON `prot`. Calcola data limite voto (`-90 giorni` dalla data assemblea hardcoded `2020-05-03`).
- **soci_ass_edit.php**: `UPDATE tab_soci_scelta SET presente=..., ...` per registrare scelte del singolo socio (`prot`).
- **soci_ass_totali.php**: totalizzatori (presenti, deleghe date/ricevute, pullman, location Fontebecci/San Casciano/Pistoia). Data assemblea hardcoded `2019-05-12`.

---

## 5. Debito tecnico e rilievi

### 5.1 SQL Injection (critico, diffuso)
Tutte le pagine concatenano direttamente input utente nelle query, senza prepared statement né escaping:
- `sqldati_schedasocio.php`: `WHERE sds_soci.IDSOCIO = '".$_GET['id']."'` e numerose query SADAS con `$_GET['id']` non sanitizzato.
- `schedasocio.php`, `mutua_listaschedasocio.php`, `migracarte_lista.php`: `LIKE '%".$_POST['ricerca']."%'` e `FILIALE = ".$_POST['filiale']`.
- `lista_ammissioni.php` / `lista_usciti.php` / `deceduti*.php`: `$_GET['filiale']`, `$_GET['area']` inseriti dentro clausole `IN (...)`. Solo `lista_ammissioni.php` valida `datain`/`dataout` (via `isValidDate`), le altre no.
- `giovani_lista.php`: `eta`, `filiale`, `rapporti`, `socio` da GET direttamente in query.

### 5.2 Date hardcoded da aggiornare manualmente ogni anno
- [deceduti.php](../../deceduti.php) **riga 80**: `$_GET['datain'] = '01/01/2026';` (allineato all'anno corrente nel sorgente, ma fuori da `$inizioanno`).
- [deceduti_presunti.php](../../deceduti_presunti.php) **riga 80**: `$_GET['datain'] = '01/01/2025';` — **disallineato** rispetto a `$inizioanno = '01/01/2026'`.
- [soci_ass.php](../../soci_ass.php) riga 26 e [soci_ass_totali.php](../../soci_ass_totali.php) riga 25: date assemblea hardcoded (2020 / 2019).

### 5.3 Credenziali e perimetri hardcoded
- `config/_config.php`: credenziali MySQL in chiaro nel repository.
- `soci_ass.php`: password di accesso e mappatura password→filiali codificate nel sorgente.
- URL/IP interni hardcoded (es. `http://10.197.139.22:8080/...`, `http://10.119.192.46:8080/...`) variabili da pagina a pagina.

### 5.4 Duplicazione di codice
- La query base "anagrafica + certificati + contatti + storico_pistoia" è ripetuta quasi identica in `lista_soci.php` e `schedasocio.php`.
- Il blocco "lookup Mutua su `TAB_MUTUA` per CF/NAG" è duplicato in almeno 4 pagine.
- Il blocco "decodifica settorista", "decodifica filiale" e il calcolo U35 (`-35 years`) sono ricopiati in più file.
- Il pattern date `datain/dataout` è copiato in `lista_ammissioni`, `lista_usciti`, `deceduti`, `deceduti_presunti`.

### 5.5 Altri rilievi
- `lista_usciti.php` / `deceduti_presunti.php` usano `switch` con `exit;` nel ramo `default` (riga senza match interrompe l'intera pagina).
- `valore azioni` calcolato con costante magica `30.33` ripetuta in più punti.
- Riferimenti a file/funzionalità potenzialmente assenti: `schedasocio.php` punta a `schedasocio2.php` (da verificare), e in `sqldati_schedasocio.php` resta codice commentato verso `mutua_schedasocio.php`.
- `deceduti.php` non produce CSV (blocco di export commentato): l'esportazione avviene per copia/incolla.
- `error_reporting(E_ALL ^ E_DEPRECATED)` e `ini_set('max_execution_time', 0)` su quasi tutte le pagine (mascheramento errori + nessun limite di esecuzione).
