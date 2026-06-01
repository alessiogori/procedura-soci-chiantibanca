# Documentazione Tecnica - Dominio "Domande, Ammissioni, Trasferimenti, Cessioni e Campagna Azioni"

Portale ChiantiBanca - Soci (v3.00 - Sicra)

Questo documento descrive le pagine PHP del dominio relativo al ciclo di vita delle domande di sottoscrizione/acquisto azioni, alle ammissioni di nuovi soci, ai trasferimenti, alle cessioni e alla campagna azioni.

> Nota di lettura: il codice è PHP legacy senza framework. Tutte le query sono SQL grezzo costruito per concatenazione di stringhe. Le criticità trasversali (SQL injection, codice duplicato, date hardcoded) sono raccolte nella sezione finale "Debito tecnico".

## Indice

- [Bootstrap comune e fonti dati](#bootstrap-comune-e-fonti-dati)
- [Stati e codici di dominio](#stati-e-codici-di-dominio)
- [lista_domande.php - Domande a Socio (da esaminare)](#lista_domandephp---domande-a-socio-da-esaminare)
- [lista_domande_daregolare.php - Domande a Socio (da regolare)](#lista_domande_daregolarephp---domande-a-socio-da-regolare)
- [lista_domande_dasollecitare.php - Domande a Socio (da sollecitare)](#lista_domande_dasollecitarephp---domande-a-socio-da-sollecitare)
- [lista_ammissioni.php - Ammissioni Soci](#lista_ammissioniphp---ammissioni-soci)
- [lista_trasferimenti.php - Trasferimenti Soci](#lista_trasferimentiphp---trasferimenti-soci)
- [admin_cessioni.php - Amministrazione Cessioni](#admin_cessioniphp---amministrazione-cessioni)
- [campagna_azioni.php - Campagna Azioni](#campagna_azioniphp---campagna-azioni)
- [motivazioni*.php - Motivazioni IN/OUT](#motivazioniphp-motivazioni_formphp-motivazioni_checkphp---motivazioni-inout)
- [fasce_*.php - Fasce e classi socio](#fasce_classisocio_listaphp-e-fasce_senzarichieste_listaphp---fasce-anzianita)
- [Ciclo di vita di una domanda/azione](#ciclo-di-vita-di-una-domandaazione)
- [Debito tecnico](#debito-tecnico)

---

## Bootstrap comune e fonti dati

Quasi tutte le pagine seguono lo stesso schema di avvio:

1. `include("config/_config.php")` - credenziali MySQL (`$host`, `$db_user`, `$db_psw`, `$db_name`) e variabile `$inizioanno` (attualmente `'01/01/2026'`).
2. `include("config/_functions.php")` - funzioni di utilità (tra cui `diff_date_ingiorni()` e `logquery()`).
3. Connessione MySQL: `mysqli_connect($host, $db_user, $db_psw, $db_name)` (variabile `$connection`). Alcune pagine creano anche un secondo handle OO `new mysqli(...)` (`$dbhandle`).
4. Connessione SADAS via ODBC: `odbc_connect('SADAS', NULL, NULL) or die ('0')` (variabile `$connect`).
5. Frontend: Bootstrap, FontAwesome, jQuery + DataTables, FusionCharts.

Le pagine si dividono in due famiglie di layout:

- **Pagine "report SADAS"** (`lista_domande*`, `lista_ammissioni`, `lista_trasferimenti`): emettono direttamente `<html>` con import CSS, NON includono `css/main.php` / `css/menu.php`, e contengono inline la funzione JS `selectElementContents()` per la selezione/copia della tabella (CTRL+C).
- **Pagine "applicative"** (`admin_cessioni`, `campagna_azioni`, `motivazioni*`, `fasce_*`): includono `css/main.php` + `css/menu.php` e usano il plugin DataTables.

### Pattern date ricorrente

```php
if (!isset($_GET['datain']) OR empty($_GET['datain'])) { $_GET['datain'] = $inizioanno; }
if (!isset($_GET['dataout']) OR empty($_GET['dataout'])) {
    $_GET['dataout'] = date("d/m/Y", strtotime("-1 day")); // SADAS è sempre a ieri sera
}
```

### Tabelle / viste coinvolte

| Sorgente | Oggetto | Uso |
|---|---|---|
| SADAS | `SOCI_DOMANDE` | Domande di sottoscrizione/acquisto/trasferimento (stato in `CTIPOESITO`) |
| SADAS | `ISIDOC_CONTRATTI` | Contratti documentali; `COD_CONTRATTO='SOCICN02'`, `PRESENZA_DOCUMENTO`, `PRESENZA_NOTE` |
| SADAS | `SOCI_ANAGRAFICA`, `SOCI_MOVIMENTI` | Anagrafica e movimenti soci (trasferimenti) |
| SADAS | `ANAG_NAG`, `ANAG_PERSONE_FISICHE`, `ANAG_PERSONE_GIURIDICHE` | Anagrafica generale NAG (intestazioni, data nascita) |
| SADAS | `CG_SALDI`, `CC_SALDI_DISPON`, `CG_MOVIMENTI_CONTABILI` | Saldi conto/disponibilità e movimenti contabili (plafond/cessioni) |
| MySQL | `sds_soci` | Tabella principale soci |
| MySQL | `sds_soci_certificati` | Certificati azionari (`NUMERO_AZIONI`, `VALORE_AZIONI`, `TRASFERIMENTO_DA_IDSOCIO`) |
| MySQL | `sds_soci_domande` | Replica/cache MySQL delle domande (usata per griglia per Anno/Mese) |
| MySQL | `sds_soci_domande_nopdf` / vista `SDS_SOCI_DOMANDE_NOPDF` | Domande senza PDF in ISIDOC (da sollecitare) |
| MySQL | `sds_soci_prodotto_cc` | Prodotto/classe del conto corrente del socio |
| MySQL | `sds_soci_movinout` | Movimenti IN/OUT soci (es. decessi `CTIPMOVUSCITA='MO'`) |
| MySQL | `tab_motivazioni` | Motivazioni manuali IN/OUT inserite dagli operatori |
| MySQL | `tmp_formadoc_log` | Log stampe FormaDoc (utente che ha stampato la domanda) |
| MySQL | `tab_xls_cessionibanca` | Coda delle cessioni/richieste di rimborso |
| MySQL | `tab_soci_as37`, `tab_volumi`, `tab_xls_ammissioni`, `view_azioni_meno_minimo` | Campagna azioni |
| MySQL | `view_fasce_anzianitasocio`, `view_fasce_senzarichieste`, `tab_mutua` | Fasce di anzianità e socio Mutua |

---

## Stati e codici di dominio

Codici principali osservati nel codice (descrizioni in parte dedotte dal comportamento - segnalate con "da verificare"):

**`SOCI_DOMANDE.CTIPOESITO` (stato esito domanda):**

| Codice | Significato | Pagina che lo filtra |
|---|---|---|
| `DE` | Domanda **da esaminare** (in attesa di delibera) | `lista_domande.php` |
| `DR` | Domanda **deliberata/da regolare** (delibera presente, regolarizzazione contabile pendente) (da verificare) | `lista_domande_daregolare.php` |

**`SOCI_DOMANDE.CTIPODOM` (tipo domanda):**

| Codice | Significato (da verificare) |
|---|---|
| `DA` | Domanda di acquisto/sottoscrizione azioni (caso "standard" con verifica trasferente) |
| `DL` | Esclusa dalla lista "da regolare" (`not in ('DL','DR')`) |
| `DR` | Esclusa dalla lista "da regolare" |

In `lista_domande_daregolare.php` se `CTIPODOM <> 'DA'` la riga viene evidenziata in ciano (caso non "acquisto standard").

**`SOCI_MOVIMENTI.CTIPOMOV` (tipo movimento - trasferimenti):**

| Codice | Significato (mappato nel codice) |
|---|---|
| `TR` | Trasferimento (old) - verificare se serve regolamento |
| `CO` | Compravendita - verificare se serve regolamento |
| `SU` | Successione |
| `DO` | Donazione - verificare se serve regolamento |
| `FU` | Fusione |

**`sds_soci_movinout`:** `CTIPMOVUSCITA='MO'` + `CTIPOMOV='ID'` = socio deceduto.

**Regole numeriche ricorrenti:**

- Valore nominale azione: **30,33 EUR** (costante hardcoded in più punti: calcolo azioni `IMPORTO / 30.33`, soglia disponibilità, ecc.).
- Numero minimo azioni per essere socio: **33** (controvalore ~1.000 EUR). Più volte verificato (cedente che resta sotto 33, campagna azioni).
- Soglia "Under 35": data nascita > (oggi - 35 anni). NB: in `lista_domande_daregolare.php` la variabile è calcolata a -30 anni (incoerenza, vedi Debito tecnico).

---

## lista_domande.php - Domande a Socio (da esaminare)

**Scopo:** elenco delle domande di ammissione/acquisto azioni ancora **da esaminare** (`CTIPOESITO='DE'`), con verifica della presenza del PDF del contratto in ISIDOC e dell'anzianità della domanda.

**Parametri (GET):** `filiale` (lista CSV di filiali, usata dentro un `IN (...)`), `area` (se valorizzata cambia solo l'etichetta), `datain`/`dataout` (default `$inizioanno` / ieri).

**Logica principale:**

1. Conteggio soci deceduti dal `datain` (MySQL `sds_soci` + `sds_soci_movinout`).
2. Conteggi su SADAS: domande `DE` con/senza PDF (`ISIDOC_CONTRATTI.PRESENZA_DOCUMENTO='S'`), totale, e data della domanda più vecchia.
3. Griglia riepilogativa per Anno/Mese da MySQL `sds_soci_domande` dove `DATA_DELIBERA = '0'` (domande non ancora deliberate).
4. Query principale su SADAS: `SOCI_DOMANDE` (alias `SOCI_DOMANDE_01`) in `LEFT OUTER JOIN` con `ISIDOC_CONTRATTI`, `ANAG_NAG` (ricevente, subentro, anagrafica), `SOCI_ANAGRAFICA`, filtrando `CTIPOESITO='DE'` e `COD_CONTRATTO='SOCICN02'`.
5. Per ogni riga:
   - cerca in MySQL `tab_motivazioni` (per `nag` + `data_domanda`) se esiste una motivazione: icona verde (presente) o gialla con link a `motivazioni_form.php` (da inserire);
   - calcola l'età della domanda con `diff_date_ingiorni(data_domanda, oggi)`: se **> 90 giorni** evidenzia in `coral` (qui la soglia è 90, non 60);
   - verifica Under 35 su SADAS (`ANAG_PERSONE_FISICHE.DATA_NASCITA`);
   - se è una domanda di acquisto (`CTIPODOM='DA'` con ricevente valorizzato) controlla in `sds_soci_certificati`/`sds_soci_domande` quante azioni rimangono al cedente: se `NUMERO_AZIONI - NAZIONI < 33` evidenzia "CHI CEDE RESTEREBBE CON MENO DI 33 AZIONI".

**Output:** tabella HTML molto larga (~32 colonne) con flag PDF/Note, dati domanda, ricevente, subentro, professione, residenza, U35, rimanenza azioni. Bottone "Seleziona tabella per CTRL+C" (no export su file: il blocco CSV è commentato).

**Note tecniche:** esiste un grande blocco "QUERY PER UNDER 35" interamente commentato (righe ~558-680). La data limite U30 qui è calcolata a -35 anni nonostante il nome.

[lista_domande.php](../../lista_domande.php)

---

## lista_domande_daregolare.php - Domande a Socio (da regolare)

**Scopo:** elenco delle domande **deliberate ma da regolarizzare** contabilmente (`CTIPOESITO='DR'`), con controllo della capienza del conto del richiedente e del termine dei **60 giorni dalla delibera**.

**Parametri (GET):** come sopra. **ATTENZIONE:** default `datain` qui è **hardcoded** a `'01/01/2022'` (non usa `$inizioanno`).

**Logica principale:**

1. Query principale su SADAS `SOCI_DOMANDE` filtrando `CTIPOESITO='DR'`, `COD_CONTRATTO='SOCICN02'` e `CTIPODOM not in ('DL','DR')`, ordinata per intestazione.
2. Per ogni domanda interroga i saldi SADAS (`CG_SALDI` + `CC_SALDI_DISPON`, `COD_RAPP=2`) sul conto del richiedente (`FILIALE_RAPP`/`NUM_RAPP`):
   - se il **saldo disponibile** copre il fabbisogno `NAZIONI*30,33 + NAZIONI*1` (azioni × valore + sovrapprezzo presunto 1 EUR/azione) evidenzia in verde.
3. Calcolo del **Time GG** con `diff_date_ingiorni(data_delibera, oggi)`:
   - `> 60` giorni → testo rosso (oltre il termine);
   - tra `55` e `60` → arancione (in scadenza);
   - altrimenti nessun colore.

Questo è il punto in cui si applica la regola dei **60 giorni** citata nel commento di `diff_date_ingiorni` ("Se superiori a 60 rigettare domanda di ammissione").

**Output:** tabella con progressivo, PDF, NAG, intestazione, filiale/conto, saldo conto+disponibile, data domanda, data delibera, Time GG colorato, soglia, tipo, azioni, residenza/professione. Nessun export attivo (commentato).

[lista_domande_daregolare.php](../../lista_domande_daregolare.php)

---

## lista_domande_dasollecitare.php - Domande a Socio (da sollecitare)

**Scopo:** elenco delle domande presenti **senza PDF archiviato in ISIDOC** (vista MySQL `SDS_SOCI_DOMANDE_NOPDF`), con possibilità di inviare una **mail di sollecito** al dipendente che ha stampato il contratto.

**Parametri (GET):** `filiale`/`area`; in modalità invio: `inviamail=si`, `mail_utente`, `nag`, `nominativo`, `data_domanda`, `data_stampa`, `id`.

**Logica principale:**

- **Modalità lista** (default): conta le domande senza PDF (`FILIALE_DOMANDA <> 990`), trova la più vecchia, ed elenca i record unendo `sds_soci_domande_nopdf` con `tmp_formadoc_log` (su NAG) per recuperare `DATASTAMPA` e `UTENTE`. L'utente viene trasformato in indirizzo mail `utente@chiantibanca.it`. Se la domanda ha più di **90 giorni** mostra un GIF di allarme. Ogni riga ha un link che richiama la stessa pagina con `inviamail=si`.
- **Modalità invio mail** (`inviamail=si`): non invia il sabato/domenica (`date('w')`); configura SMTP via `ini_set` (`smtp.bccsi.bcc.it:25`), invia con la funzione nativa PHP `mail()` (oggetto Base64, corpo HTML Base64) a `mail_utente` + `soci@chiantibanca.it`. Il corpo invita a verificare/eliminare la domanda da Sistemi Guida > Soci > Domande Soci.

**Note:** usa `mail()` nativo (non PHPMailer come da stack dichiarato). Output `$debug` non definito → i messaggi di esito non vengono mai mostrati.

[lista_domande_dasollecitare.php](../../lista_domande_dasollecitare.php)

---

## lista_ammissioni.php - Ammissioni Soci

**Scopo:** elenco dei **nuovi soci ammessi** in un intervallo di date (per `DATA_ENTRATA`), con azioni sottoscritte, eventuale socio defunto subentrato, prodotto C/C, flag Under 35 e conteggio età media.

**Parametri (GET):** `filiale`/`area`, `datain`/`dataout` (default `$inizioanno` / ieri).

**Logica principale:**

1. Conteggio soci ammessi e **età media** (solo `TIPO_NAG='PF'`) da MySQL.
2. Validazione date introdotta il 06/02/25 (commento `#MZ`): funzione `isValidDate()` con `DateTime::createFromFormat`; se le date non sono valide `die("date non corrette.")` — **unica pagina del dominio con un minimo di validazione input**.
3. Query principale MySQL su `sds_soci s1` con LEFT JOIN a `sds_soci_certificati` (azioni/valore), `sds_soci` s2 (socio defunto subentrato via `IDSOCIO_SUB`) e `sds_soci_prodotto_cc` (prodotto conto). Filtro per `DATA_ENTRATA` tra `datain` e `dataout`, group by `NAG`.
4. Per ogni riga: icona motivazione (presenza in `tab_motivazioni` per NAG), verifica Under 35 su SADAS (`ANAG_PERSONE_FISICHE`/`GIURIDICHE`), flag `pack` se `ACQUISTO_PERIOD='Y'`, link a `sqldati_schedasocio.php`.

**Output:** tabella HTML + **export CSV** `tmp/ammissioni.csv` (scrittura attiva) con link di download e conteggio Under 35.

[lista_ammissioni.php](../../lista_ammissioni.php)

---

## lista_trasferimenti.php - Trasferimenti Soci

**Scopo:** elenco dei **trasferimenti di azioni** tra soci (e soci/non soci) in un intervallo, con tipologia di movimento, importo, sovrapprezzo, azioni residue del cedente e flag Under 35 del ricevente.

**Parametri (GET):** `filiale`/`area`, `datain`/`dataout`.

**Logica principale:**

1. Query principale su SADAS `SOCI_MOVIMENTI` join `SOCI_ANAGRAFICA` (trasferente e ricevente) e `ANAG_NAG`, filtrando `CTIPOMOV IN ('TR','CO','FU','DO','SU')` e `DATA_MOVIMENTO` nell'intervallo. Le azioni sono calcolate come `abs(IMPORTO / 30.33)`.
2. Per ogni riga:
   - icona motivazione (MySQL `tab_motivazioni` su NAG ricevente);
   - Under 35 del ricevente calcolato in MySQL su `sds_soci` confrontando `DATA_MOVIMENTO` e `DATA_NASCITA` con `DateTime::diff()->y <= 35`;
   - azioni residue del cedente da MySQL `sds_soci_certificati` (`IDSOCIO = IDSOCIO_TRASFERENTE`); se `< 33` e ricevente non U35, alert "CHI CEDE RESTA CON MENO DI 33 AZIONI";
   - mappatura `CTIPOMOV` → tipo leggibile + nota regolamento.

**Output:** tabella HTML (intestazioni Trasferente / Ricevente / Dati operazione) + **export CSV** `tmp/trasferimenti.csv` con link di download.

[lista_trasferimenti.php](../../lista_trasferimenti.php)

---

## admin_cessioni.php - Amministrazione Cessioni

**Scopo:** calcolo previsionale del **rimborso di una cessione** (richiesta di liquidazione azioni) di un socio: stima quante posizioni precedenti in coda, quanto plafond/Fondo Riacquisto è disponibile e in quanti mesi si prevede il rientro. Pagina riservata all'Ufficio Soci.

**Autorizzazione:** accessibile solo se `$_COOKIE['filiale_id'] == 999` (Admin Soci); altrimenti messaggio "non autorizzato".

**Parametri (GET):** `id2`, `nominativo`, `dr` (data richiesta), `vn` (valore nominale richiesto), `plafond_iniziale`, `disponibilita`, `disp_senzaliquidaz`, `valA`/`valB`/`valC`/`valE` (passati dalla pagina chiamante, presumibilmente `lista_cessioni`/plafond - da verificare).

**Logica principale:**

1. Legge da SADAS i saldi contabili chiave su filiale 990/100: `COD_RAPP 2881` = Capitale Sociale, `1770` = Fondo Riacquisto Azioni Proprie (con offset +400.000), `2557` = Quote da Liquidare.
2. Calcola il `$limiterimborso` (valore utilizzabile dal Fondo, in funzione del plafond disponibile/netto).
3. Conta da MySQL `tab_xls_cessionibanca` le **posizioni precedenti non ancora rimborsate** (`Rimborsato <> 'S'`) con `id2` inferiore a quella in esame, e somma i relativi `Valore_Nominale` (= importo da erogare prima della richiesta in esame).
4. Calcola la **media mensile delle ammissioni** dagli ultimi 6 mesi (SADAS `CG_MOVIMENTI_CONTABILI`, segno A, rapporti 2881/1770), proietta su base annua e ripartisce con una logica stagionale (90% al Fondo nei mesi 9-12). Da qui ricava i mesi necessari al rientro e una "Ipotesi rientro" (mese-anno).
5. Genera un **CSV previsionale** `tmp/cessioni_ipotesirimborso.csv` con tutte le posizioni residue e mostra a video le cessioni fino all'`id2` in esame (con `Note_Motivazioni`).

**Confronto con `admin_cessioni.php.old` (cenni storici):** la versione precedente proteggeva l'accesso con un **form password hardcoded** (`$_POST['psw'] == "cicalo"`, con autofill se `filialedipendente==999`). Quel blocco è stato commentato e sostituito dall'attuale controllo via cookie `filiale_id != 999`. Resta quindi traccia in chiaro della vecchia password nel file `.old`.

[admin_cessioni.php](../../admin_cessioni.php) - [admin_cessioni.php.old](../../admin_cessioni.php.old)

---

## campagna_azioni.php - Campagna Azioni

**Scopo:** supporto alla **campagna di sottoscrizione azioni** per portare i soci al minimo di legge (33 azioni). Mostra, per filiale/area, quanti soci hanno da 1 a 32 azioni; cliccando su un numero si ottiene l'elenco nominativo dei soci con quella quantità, con la loro raccolta diretta/indiretta (per priorizzare i contatti commerciali).

**Parametri (GET):** `action` (`list` o vuoto), `azioni` (numero azioni possedute), `filiale`/`area`.

**Logica principale:**

- **Vista riepilogo** (`action != 'list'`): query su MySQL `view_azioni_meno_minimo` (per Area/Filiale), una colonna per ogni quantità da 1 a 32 azioni con link drill-down. Avviso che i conteggi includono i soci con rateizzazione in corso ma il dettaglio non li riporta.
- **Vista dettaglio** (`action='list'`): query su `tab_soci_as37` join `tab_volumi` (raccolta) e `tab_xls_ammissioni`, filtrando `nAzTot = azioni`, escludendo stati `statoVAL in ('E','S','N')` e i soci con PAC attivo (`a.Pac='N' or null`). Calcola quote/valore mancanti al minimo (`33 - azioni`). I dipendenti hanno la raccolta mascherata con un'icona "fantasma".

**Output:** tabelle DataTables interattive, link a `sqldati_schedasocio.php`. Nessun export su file.

[campagna_azioni.php](../../campagna_azioni.php)

---

## motivazioni.php, motivazioni_form.php, motivazioni_check.php - Motivazioni IN/OUT

Sotto-sistema per registrare **manualmente** le motivazioni di ingresso/uscita dei soci, collegate alle domande tramite `nag` + `data_domanda`. Tabella unica MySQL `tab_motivazioni` (campi: `nag, nominativo, tipologia [IN/OUT], motivazione, note, filiale, operatore, data_segnalazione, attivo, data_domanda`).

### motivazioni_form.php
Form di **inserimento** motivazione. Richiamato (con icona gialla) da `lista_domande.php`, `lista_ammissioni.php`, `lista_trasferimenti.php`.
- `action=""`: mostra il form. La tendina motivazioni dipende da `start`:
  - `IN`: "Vantaggi economici su Rapporti", "Vantaggi economici ChiantiMutua", "Altro".
  - `OUT`: "Cambio Banca", "Cessione azioni", "Deceduto", "Sofferenza".
- `action="insert"`: esegue `INSERT INTO tab_motivazioni ...` con `data_segnalazione=now()`, `attivo='S'`. Le note sono ripulite con `mysqli_real_escape_string(htmlspecialchars(...))`; gli altri campi NO.

### motivazioni.php
**Elenco** delle motivazioni attive (`attivo='S'`) filtrate per tipologia IN (e `nag`/`filiale` se passati). Visualizzazione tabellare DataTables. Contiene una grande query alternativa (su `sds_soci`) commentata.

### motivazioni_check.php
**Statistiche** sulle motivazioni: due tabelle affiancate IN e OUT con conteggio per motivazione (`GROUP BY motivazione`), drill-down (`dettaglio=SI`) all'elenco nominativi per tipologia+motivazione. Filtro per `tipo` = `area` / `filiale` / (default tutte tranne 999).

[motivazioni.php](../../motivazioni.php) - [motivazioni_form.php](../../motivazioni_form.php) - [motivazioni_check.php](../../motivazioni_check.php)

---

## fasce_classisocio_lista.php e fasce_senzarichieste_lista.php - Fasce anzianità

Pagine collegate alla segmentazione dei soci per **anzianità** (utili a campagne e iniziative sociali). Non gestiscono direttamente le richieste azioni, ma incrociano gli azionisti con l'eventuale appartenenza alla Mutua.

### fasce_classisocio_lista.php
Lista soci per **fascia di anzianità** (1: ≤10 anni; 2: 10-20; 3: 20-30; 4: 30-40; 5: 40-50; 6: >50). Due modalità via `start`:
- `start=0`: elenco per fascia da vista `view_fasce_anzianitasocio`.
- `start=1`: dettaglio per anzianità puntuale (`anzianita`), con opzione `nextyear=si` (anzianità dell'anno prossimo).

Per ogni socio recupera `idsocio` da `sds_soci` (query annidata per ogni riga) e verifica la presenza in `tab_mutua` (socio Mutua, pallino verde).

> Bug evidente: a riga 65 usa `$$param` (variabile-variabile) invece di `$param` nel ramo "filiale valorizzata" — la condizione risulta vuota. Vedi Debito tecnico.

### fasce_senzarichieste_lista.php
Lista soci per fascia (`Fascia like 'Fascia X%'`) da vista `view_fasce_senzarichieste`, con stessa logica Mutua e calcolo azioni da `ValAzTotali/30.33`. **Export CSV** in `tmp/fasciaN.csv`.

[fasce_classisocio_lista.php](../../fasce_classisocio_lista.php) - [fasce_senzarichieste_lista.php](../../fasce_senzarichieste_lista.php)

---

## Ciclo di vita di una domanda/azione

Ricostruzione del flusso (alcuni passaggi avvengono nel gestionale Sicra/SADAS, non nel portale; segnalati come "esterno"):

```
1. SOTTOSCRIZIONE / ACQUISTO
   Il cliente presenta domanda di ammissione o acquisto azioni in filiale.
   La domanda viene caricata su SADAS (SOCI_DOMANDE) con CTIPOESITO = 'DE' (da esaminare)
   e il contratto SOCICN02 dovrebbe essere archiviato in ISIDOC (PRESENZA_DOCUMENTO='S').
        |
        | -> lista_domande.php         (monitoraggio domande DE, alert > 90 gg)
        | -> lista_domande_dasollecitare.php (domande senza PDF -> sollecito via mail)
        v
2. DELIBERA / REGOLARIZZAZIONE
   Dopo la delibera, lo stato passa a CTIPOESITO = 'DR' (da regolare).
   Va verificata la capienza del conto e rispettato il termine di 60 giorni dalla delibera.
        |
        | -> lista_domande_daregolare.php (saldo conto, Time GG: rosso > 60, arancio 55-60)
        v
3. AMMISSIONE
   Il socio entra (DATA_ENTRATA valorizzata in sds_soci); subentro per successione
   collega IDSOCIO_SUB al socio defunto.
        |
        | -> lista_ammissioni.php     (export CSV, U35, prodotto C/C)
        | -> motivazioni_form.php     (registrazione motivazione di ingresso)
        v
4. CERTIFICATO
   Vengono emesse le azioni: sds_soci_certificati (NUMERO_AZIONI, VALORE_AZIONI).
   Minimo per essere socio: 33 azioni (valore unitario 30,33 EUR).
        |
        | -> campagna_azioni.php      (soci sotto le 33 azioni, spinta a regolarizzare)
        v
5. TRASFERIMENTO (tra soci / soci-non soci)
   Movimenti SOCI_MOVIMENTI: TR/CO/SU/DO/FU. Controllo che il cedente non scenda
   sotto 33 azioni (salvo ricevente U35).
        |
        | -> lista_trasferimenti.php  (export CSV, regolamento, U35)
        v
6. CESSIONE / RIMBORSO
   Il socio chiede la liquidazione: coda tab_xls_cessionibanca (Rimborsato <> 'S').
   Il rimborso è vincolato al Fondo Riacquisto Azioni Proprie e al plafond.
        |
        | -> admin_cessioni.php       (calcolo ipotesi rientro, CSV previsionale)
```

---

## Debito tecnico

### SQL injection (critico, diffuso)
Praticamente tutti i parametri `$_GET`/`$_COOKIE` sono concatenati direttamente nelle query, sia MySQL sia ODBC, **senza prepared statement né escaping**:
- `lista_*`: `$_GET['filiale']` iniettato dentro `IN (...)`, `$_GET['datain']`/`dataout` nelle date.
- `motivazioni_form.php` (`insert`): solo `note` è passato in `mysqli_real_escape_string`; `nag, nome, tipologia, motivazione, filiale, operatore, data_domanda` sono inseriti grezzi.
- `motivazioni.php` / `motivazioni_check.php`: `$_GET['nag']`, `$_GET['filiale']`, `$_GET['tipologia']`, `$_GET['motivazione']` concatenati.
- `admin_cessioni.php`: `$_GET['id2']`, `$_GET['vn']` (parzialmente mitigato da `floatval` su `vn`).
- `campagna_azioni.php`: `$_GET['azioni']`, `$_GET['filiale']`.
- `fasce_*`: `$_GET['filiale']`, `$_GET['fascia']`, `$_GET['anzianita']`.
È inoltre presente un controllo CSV cookie debole (`filiale_id == 999`) come unica autorizzazione lato server di `admin_cessioni.php`.

### Date / costanti hardcoded
- `lista_domande_daregolare.php` riga 79: `datain` default `'01/01/2022'` invece di `$inizioanno` (dato non aggiornato).
- `motivazioni.php` / `motivazioni_check.php`: `$annostart = '2024'` hardcoded.
- `campagna_azioni.php`/SADAS: valore azione `30.33`, minimo `33`, sovrapprezzo `1` ripetuti come literal in più file.
- `admin_cessioni.php`: offset Fondo `400000`, rapporti contabili `2881/1770/2557`, filiale `990`/rapporto `100` hardcoded.
- `admin_cessioni.php.old`: password applicativa `"cicalo"` ancora leggibile in chiaro nel file di backup versionato.

### Incoerenze logiche
- "Under 35" usa soglia 35 anni in `lista_domande.php`/`lista_ammissioni.php`/`lista_trasferimenti.php`, ma `$DataLimiteU30` in `lista_domande_daregolare.php` è calcolata a **-30 anni** (commento "Unnder 30") pur non essendo poi usata.
- Soglia anzianità domanda incoerente: `lista_domande.php` e `lista_domande_dasollecitare.php` allertano a **90 giorni**, mentre la regola di business (`diff_date_ingiorni`) e `lista_domande_daregolare.php` parlano di **60 giorni**.
- `fasce_classisocio_lista.php` riga 65: `$$param` (variabile-variabile) anziché `$param` → filtro fascia perso quando si specifica una filiale.

### Codice duplicato / morto
- Funzione JS `selectElementContents()` e l'intero bootstrap copiati identici in tutte le pagine `lista_*`.
- Blocco "QUERY PER UNDER 35" (~120 righe) interamente commentato in `lista_domande.php`.
- Grandi query alternative commentate in `motivazioni.php`, `motivazioni_check.php`, `admin_cessioni.php`.
- Costruzione `$condizionefiliale`/`$condizionefiliale2` ripetuta in ogni pagina, con i rami "filiale" e "area" identici tra loro.

### Robustezza / manutenibilità
- `lista_domande_dasollecitare.php` usa la funzione nativa `mail()` (non PHPMailer come da stack dichiarato) con SMTP configurato a runtime via `ini_set`; la variabile `$debug` non è definita.
- Query annidate in loop riga-per-riga (es. saldo per ogni domanda in `daregolare`, `idsocio` per ogni socio nelle fasce, motivazioni per ogni riga): pattern N+1, potenzialmente lento su grandi volumi (mitigato da `max_execution_time=0`).
- Doppia connessione MySQL in più file (`$connection` procedurale + `$dbhandle` OO) senza reale necessità.
- `error_reporting(0)` in `campagna_azioni.php` e `motivazioni.php` nasconde eventuali errori runtime.

---

Documenti correlati:
- [Documento funzionale del dominio](../funzionale/03-domande-ammissioni-cessioni.md)
