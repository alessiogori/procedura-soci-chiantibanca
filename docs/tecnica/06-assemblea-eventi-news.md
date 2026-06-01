# 06 - Assemblea, Eventi, News, Compleanni, Documentazione/Modulistica (Documento Tecnico)

> Dominio funzionale del Portale ChiantiBanca - Soci che raggruppa: la gestione dell'**Assemblea annuale dei soci**, la gestione degli **Eventi** e delle relative iscrizioni, la pubblicazione di **News/Comunicazioni**, gli **auguri di compleanno** ai soci e la messa a disposizione di **Documentazione, Modulistica, Statuto e Video**.

Stack di riferimento: PHP senza framework, MySQL `soci` (mysqli) + SADAS via ODBC, Bootstrap/jQuery/DataTables, FPDF/TCPDF, PHPMailer (presente in `routines/PHPMailer/`).

Bootstrap pagina standard (presente in quasi tutte le pagine del dominio):
```php
error_reporting(E_ALL ^ E_DEPRECATED);
include("config/_config.php");      // credenziali + $inizioanno
include("config/_functions.php");   // funzioni utility (logquery, get_browser_name, ...)
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
include("css/main.php");
include("css/menu.php");
```

Indice:
- [1. Assemblea](#1-assemblea)
- [2. Eventi](#2-eventi)
- [3. News / Comunicazioni](#3-news--comunicazioni)
- [4. Compleanni](#4-compleanni)
- [5. Documentazione / Modulistica / Statuto / Video](#5-documentazione--modulistica--statuto--video)
- [6. Tabelle e oggetti DB del dominio](#6-tabelle-e-oggetti-db-del-dominio)
- [7. Debito tecnico e rilievi di sicurezza](#7-debito-tecnico-e-rilievi-di-sicurezza)

---

## 1. Assemblea

### 1.1 Pagine coinvolte

| File | Ruolo |
|------|-------|
| [`../../assemblea_auth.php`](../../assemblea_auth.php) | Entry point ("Assemblea Soci" da `index.php` e dal menu). Imposta le variabili dell'assemblea corrente e include la pagina di contenuto dell'anno. |
| [`../../assemblea2021.php`](../../assemblea2021.php) | Pagina di contenuto Assemblea Ordinaria 2021 (HTML statico). |
| [`../../assemblea2022.php`](../../assemblea2022.php) | Pagina di contenuto Assemblea Ordinaria 2022 (HTML statico). |
| [`../../old/assemblea2020.php`](../../old/assemblea2020.php) | Versione storica 2020 (archiviata in `old/`). |
| [`../../stats/assemblea_buoni.php`](../../stats/assemblea_buoni.php) | Collegata al dominio (gestione buoni/omaggi assemblea — non analizzata in dettaglio). |

### 1.2 `assemblea_auth.php` — entry point e definizioni

Il file, nonostante il nome ("auth"), **non implementa alcuna autenticazione attiva**. La parte di login a password e il "vecchio sistema di autenticazione e gestione" (estrazione filiale da `tab_psw`) sono interamente **commentati** (blocchi `/* ... */`). Il flusso effettivo è:

```php
$dataassemblea = '2022-04-28';        // hardcoded
$tipoassemblea = 'ORDINARIA';         // hardcoded
$limitevoto    = date('d.m.Y', strtotime('-90 day', strtotime($dataassemblea)));
include("assemblea2022.php");          // include diretto della pagina anno corrente
```

Quindi, in pratica, `assemblea_auth.php` è oggi solo un wrapper che fissa i metadati dell'assemblea corrente e include la pagina HTML dell'anno. Per cambiare anno si deve modificare la riga `include("assemblea2022.php")` (e le costanti `$dataassemblea`, `$tipoassemblea`).

Nel codice commentato la password prevista era `"cicalo"` (vedi §7) e l'estrazione filiale avveniva tramite query su `tab_psw` (con SQL injection diretta su `$_POST['psw']`).

### 1.3 Contenuto delle pagine `assemblea20XX.php`

Sono pagine **interamente statiche** (nessuna query SQL, nessun parametro). Forniscono:
- Titolo, date di prima/seconda convocazione, location (es. Auditorium San Casciano).
- Box "Cosa è stato inviato al socio": link alla lettera di convocazione PDF.
- Video formativo sulla raccolta deleghe (`<video>` con `source` su `http://10.119.192.46:8080/soci/video/as00_raccoltadeleghe.mp4`).
- Box "Area riservata": elenco link a PDF degli atti d'assemblea (avviso di convocazione, calendario deleghe, lettera del Presidente, istruzioni operative voto tramite Rappresentante Designato, indicazioni compilazione delega, informativa OdG, prospetti di bilancio, politiche di remunerazione, regolamento elettorale, ecc.).
- Testo descrittivo del processo di rilascio delega e istruzioni di voto al **Rappresentante Designato** (Notaio), con modalità di consegna (originale, raccomandata, PEC) e relativi termini.
- Link alle FAQ (`faq/?qa=questions/assemblea`).

Differenza 2021 vs 2022: in 2022 si introduce l'accesso all'area riservata "mediante CODICE FISCALE del Socio" (indicazione testuale, non implementata in questa pagina) e cambia il layout/ordine dei box. Le date, i termini e i nomi file PDF sono diversi e **codificati a mano** in ciascun file.

### 1.4 Documenti PDF dell'assemblea

I PDF risiedono sotto [`../../assemblea/`](../../assemblea/):
- `assemblea/2020/…`, `assemblea/2021/…`: archivi per anno (avviso convocazione, calendario deleghe, lettera presidente, prospetti bilancio, politiche di remunerazione, regolamento elettorale, istruzioni voto RD, ecc.).
- `assemblea/00_lettera_convocazione.pdf`, `assemblea/00_calendario_deleghe.pdf`: file dell'anno "corrente" tenuti nella radice della cartella (sovrascritti di anno in anno).

> Nota: `assemblea2021.php` punta a path tipo `assemblea/00_avviso_di_convocazione.pdf` (radice), mentre i file 2021 reali sono in `assemblea/2021/…`. La pagina anno corrente referenzia quindi i file nella radice di `assemblea/`, che vanno aggiornati manualmente quando si imposta l'anno (da verificare l'allineamento esatto dei path al momento del rollover annuale).

### 1.5 Countdown in `index.php`

In [`../../index.php`](../../index.php) è presente uno script JS "CONTATORE ASSEMBLEA" con data hardcoded (`04/27/2022 10:00:00 AM`); il riquadro testuale che lo mostra è commentato, quindi attualmente non visibile.

---

## 2. Eventi

### 2.1 Pagine coinvolte

| File | Ruolo |
|------|-------|
| [`../../eventi_gestionale.php`](../../eventi_gestionale.php) | Gestionale eventi: lista eventi in programma/trascorsi, form iscrizione, inserimento iscrizione, elenco iscritti. |
| [`../../eventi_iscrizioni_nag.php`](../../eventi_iscrizioni_nag.php) | Elenco delle iscrizioni eventi di un singolo socio (per NAG). |
| [`../../eventi_gestionale - Copia.php`](../../eventi_gestionale%20-%20Copia.php) | **Copia di backup** lasciata in produzione (debito tecnico, vedi §7). |
| [`../../old/eventi.php`](../../old/eventi.php) | Versione storica (archiviata in `old/`). |
| [`../../docs/manuale_eventi.pdf`](../../docs/manuale_eventi.pdf) | Manuale utente, linkato da varie viste eventi. |

### 2.2 `eventi_gestionale.php` — controllo accessi e routing

Controllo di visibilità basato sul cookie `filiale_id`:
```php
if (in_array($_COOKIE['filiale_id'], array('999','998','995','996','100')))
     $link = 'SI';   // Ufficio Soci / Segreteria / Aree / Controllo Gestione / filiale 100
else $link = 'NO';
```
Il flag `$link` **non blocca la pagina**: abilita solo la visualizzazione dei link per iscrivere partecipanti (i numeri di "Posti Residui" diventano cliccabili verso il form). Gli utenti non abilitati vedono comunque l'elenco eventi in sola lettura.

Routing tramite parametro `$_GET['action']`:

| `action` | Comportamento |
|----------|---------------|
| (vuoto) | Mostra informativa "Festa del Socio" + tabella **Eventi interni in programma** (`data_evento >= oggi`) e tabella **Eventi interni trascorsi** (`data_evento < oggi`). |
| `form` | Form di inserimento di un nominativo (partecipante) per l'evento `idevento`. |
| `insert` | Esegue l'INSERT dell'iscrizione e decrementa `posti_residui`. |
| `elenco` | Elenco degli iscritti all'evento `idevento`. |
| altro | stampa letterale `peeeee` (placeholder/debug). |

Parametri principali: `action`, `idevento`, e (per `insert`) `ID`, `user`, `nag`, `nominativo`, `datanascita`, `luogonascita`, `email`, `cellulare`, `note`.

### 2.3 Query principali

Lista eventi (in programma / trascorsi) su `tab_eventi`, ordinata per data convertita con `str_to_date(data_evento,'%d/%m/%Y')`:
```sql
SELECT idevento, tipo_evento, descrizione_evento, data_evento, ora_evento, luogo_evento,
       note, link, posti_disponibili, posti_residui
FROM tab_eventi
WHERE str_to_date(data_evento,'%d/%m/%Y') >= str_to_date('<oggi>','%d/%m/%Y')
ORDER BY str_to_date(data_evento,'%d/%m/%Y')
```

Inserimento iscrizione (`action=insert`):
```sql
INSERT INTO TAB_EVENTI_ISCRIZIONI
 (idevento,data_richiesta,utente_inserimento,nag,nominativo,data_nascita,luogo_nascita,email,cellulare,note)
VALUES ('<ID>', NOW(), '<user>', '<nag>', '<nominativo>', '<datanascita>', '<luogonascita>', '<email>', '<cellulare>', '<note>')
```
Subito dopo legge `posti_residui` da `TAB_EVENTI`, lo decrementa di 1 in PHP e fa `UPDATE TAB_EVENTI SET posti_residui = ...` (operazione **non atomica** → race condition possibile, vedi §7). La query INSERT viene anche stampata a video (`echo $select_insert;`) — residuo di debug.

Elenco iscritti (`action=elenco`) e elenco per socio (`eventi_iscrizioni_nag.php`) fanno JOIN:
```sql
SELECT i.idevento, tipo_evento, descrizione_evento, NAG, nominativo, data_nascita, luogo_nascita,
       email, cellulare, data_richiesta, utente_inserimento, i.note
FROM tab_eventi_iscrizioni AS i JOIN tab_eventi AS e ON i.idevento = e.idevento
WHERE i.idevento = <idevento>          -- (oppure WHERE NAG = <nag> in eventi_iscrizioni_nag.php)
```

L'utente che inserisce è preso dal cookie `user_nomeCookie` (`trim($_COOKIE['user_nomeCookie'])`). L'icona dell'evento è derivata da `tipo_evento` (Calcio, Basket, Pallavolo, Teatro, Concerto, default).

### 2.4 Invio email / QR-Code

`eventi_gestionale.php` **non invia direttamente email né genera QR-Code**: l'informativa testuale descrive che il socio riceve mail/SMS con link di registrazione e, dopo conferma, una seconda mail con invito e QR-Code. L'invio effettivo è demandato ad altri processi/canali (da verificare quali; non presente in questo file).

---

## 3. News / Comunicazioni

Il dominio "News" è realizzato con **tre meccanismi distinti e non del tutto coerenti**:

### 3.1 News come file in cartella `news/`

| File | Ruolo |
|------|-------|
| [`../../news.php`](../../news.php) | "Elenco Comunicazioni": fa `scandir("news/")` e mostra i file con icona per estensione (pdf, doc/docx, xls/xlsx, ppt/pptx, txt, csv), linkandoli in `target=_blank`. Nessun DB. |
| [`../../news_index.php`](../../news_index.php) | Variante semplificata ("News inerenti la compagine sociale") che fa `scandir("news/")` e stampa i nomi file come link. Non risulta inclusa/linkata da altre pagine (Grep: nessun riferimento). |
| [`../../news_soci.php`](../../news_soci.php) | Identica logica `scandir("news/")` di `news_index.php`. Anch'essa senza riferimenti entranti (Grep: nessun match). |

In `news.php` la scelta dell'estensione immagine usa `get_browser_name($_SERVER['HTTP_USER_AGENT'])` (jpg per Internet Explorer, png altrimenti) — pattern comune a molte pagine del portale.

### 3.2 Gestionale comunicazioni a password — `admin_news.php`

[`../../admin_news.php`](../../admin_news.php) (≈1207 righe) **non è il gestore della tabella news**, ma una libreria di terze parti **"Evoluted Directory Listing Script - Version 4"** configurata per gestire la cartella `news/` (`$startDirectory = 'news'`). Caratteristiche:
- `session_start()` in testa al file.
- Autenticazione a password tramite sessione PHP:
  ```php
  public $passwordProtect = true;
  public $password = 'cicalo';                 // PASSWORD HARDCODED (vedi §7)
  ...
  if ($password === $this->password) { $_SESSION['evdir_loggedin'] = true; }
  else { $_SESSION['evdir_loginfail'] = true; }
  ```
- Login: il form POST `password` invoca `$listing->login()`; finché `$_SESSION['evdir_loggedin']` non è settata viene mostrato solo il form di login.
- Funzionalità abilitate: **upload file** (`enableUploads = true`, con whitelist MIME ampia), **eliminazione file** (`enableFileDeletion = true`), eliminazione/creazione directory, unzip automatico degli zip caricati, sovrascrittura file con stesso nome (`overwriteOnUpload = true`).
- Estensioni nascoste dalla listing: `php`, `ini`; nomi nascosti: `.htaccess`, `.DS_Store`, `Thumbs.db`.

In sostanza: il responsabile carica/elimina i file di comunicazione nella cartella `news/` tramite `admin_news.php` (previa password), e i soci/utenti li vedono tramite `news.php`.

### 3.3 News su tabella `tab_news` — `news.php` (ramo update)

`news.php` contiene anche un ramo POST (`action=update`) che **aggiorna un record di `tab_news`**:
```php
$post = mysqli_real_escape_string($connection, htmlspecialchars($_POST['newspost']));
mysqli_query($connection,
  "UPDATE tab_news SET datainsert = now(),
                       newscategoria = '".$_POST['newscategoria']."',
                       newstitolo    = '".$_POST['newstitolo']."',
                       newspost      = '".$post."'");
header("location: index.php");
```
Senza POST, lo stesso file cicla `SELECT * FROM tab_news` mostrando un editor WYSIWYG (`js/nicEdit.js`) con campi Titolo/Categoria/Post. La UPDATE è **senza clausola WHERE** (aggiorna tutte le righe della tabella — vedi §7) e con categoria forzata a `Mutua` nel form. Questo ramo coesiste con la versione "file in cartella" descritta in §3.1 (il file `news.php` mostra in realtà l'elenco file; il ramo `tab_news` sembra un residuo legacy — da verificare quale sia effettivamente raggiungibile in produzione).

---

## 4. Compleanni

| File | Ruolo |
|------|-------|
| [`../../bday.php`](../../bday.php) | Vista a schermo "Oggi è il COMPLEANNO di..." (compleanni anagrafici + anniversari di ingresso a Socio del giorno). |
| [`../../bday_mail.php`](../../bday_mail.php) | Routine di invio email automatica alle filiali con l'elenco dei soci che compiono gli anni oggi. |
| Link da [`../../css/menu.php`](../../css/menu.php) | Icona torta nel menu → `bday.php?key=<filiale_id>`. |

### 4.1 `bday.php`

Parametro `$_GET['key']` (= filiale dell'utente). Se `key > 100` (Ufficio/Aree) non filtra per filiale; altrimenti aggiunge `AND FILIALE_CAPOFILA = '<key>'`.

Costruisce `$adesso = date('md')` e interroga `sds_soci` con una UNION:
```sql
SELECT IDSOCIO, NAG, INTESTAZIONE_A, INTESTAZIONE_B, DATA_NASCITA, ETA,
       FILIALE_CAPOFILA, DATA_ENTRATA, STATO_NAG, DATA_ENTRATA_ORIG
FROM sds_soci
WHERE cast(SOCIO_ISTITUTO as unsigned) = 1
  AND (DATA_ENTRATA LIKE '<dd/mm>%' OR DATA_ENTRATA_ORIG LIKE '<dd/mm>%')   -- anniversari ingresso
  <condizionefiliale>
UNION
SELECT ... FROM sds_soci
WHERE cast(SOCIO_ISTITUTO as unsigned) = 1
  AND DATA_NASCITA LIKE '%<mmdd>'                                          -- compleanni
  <condizionefiliale>
ORDER BY INTESTAZIONE_A, INTESTAZIONE_B
```
Per ogni socio esegue sotto-query aggiuntive (pattern N+1):
- `tab_xls_cessionibanca` → icona se presenti cessioni a banca non rimborsate;
- `tab_xls_esclusioni` → icona se posizione esclusa per sofferenza;
- `TAB_MUTUA` (per `CODICE_FISCALE`) → flag "Socio Mutua".

Rendering con DataTables (`#dataTable`), icone torta per compleanno (`DATA_NASCITA`) e per anniversario d'ingresso (`DATA_ENTRATA`).

### 4.2 `bday_mail.php` — invio email

- **Skip nei weekend**: `if(date('w')=="0" || date('w')=="6") return;`.
- Configura SMTP via `ini_set` (NON usa PHPMailer): `SMTP = smtp.bccsi.bcc.it`, `smtp_port = 25`, `sendmail_from = soci@chiantibanca.it`. Invio con la funzione nativa `mail()`.
- Mittente: "Ufficio Soci" `soci@chiantibanca.it`.
- Estrae le filiali con soci che compiono gli anni oggi da `tab_soci_as37` (join con `tab_psw` per `email_estesa`/`desc_filiale`), escludendo CAG con richieste in corso (`view_richiesteincorso`), soci con `statoVAL in ('E','S','N')` e con `tipoContropVAL = 11000`.
- Per ogni filiale costruisce un corpo HTML con il dettaglio soci (`tab_soci_as37`: cag, nominativo, età, filiale, telefono, email) e invia con `mail()` (corpo `base64_encode`, header `Content-Transfer-Encoding: base64`).

> **Importante**: il destinatario è **hardcoded a fini di test**: `$mail_dest = "alessiofedi@chiantibanca.it";`. La riga che userebbe la mail reale di filiale (`$mail_dest = $dati2['email'];`) è **commentata**. In produzione la mail va quindi sempre al solo indirizzo di test (vedi §7).

---

## 5. Documentazione / Modulistica / Statuto / Video

| File | Ruolo |
|------|-------|
| [`../../documentazione.php`](../../documentazione.php) | Pagina statica "DOCUMENTAZIONE": link a Statuto, schema processi, mappa filiali, video formazione, manuali (Soci SicraWeb, Eventi), riferimenti interni (ODS su WorkTogether), sezione ChiantiMutua. |
| [`../../modulistica.php`](../../modulistica.php) | Elenco modelli BANCA/MUTUA estratti da `tab_modelli`; genera link ai moduli PHP precompilati. |
| [`../../modulistica_mutua.php`](../../modulistica_mutua.php) | Variante MUTUA che prima chiama via **OAuth le API COMIPA** per recuperare i dati anagrafici/familiari del socio mutua, poi mostra i modelli. |
| [`../../statuto.php`](../../statuto.php) | Include `docs/statuto.htm` (statuto ChiantiBanca in HTML). |
| [`../../video.php`](../../video.php) | Galleria di video formativi (`<video>` su `http://10.197.139.22:8080/soci/video/*.mp4`). |
| [`../../help/help.php`](../../help/help.php) | Player di aiuto on-line: `?nome=XXXX` → carica `help/XXXX.wmv` in un controllo ActiveX Windows Media Player (obsoleto). |

### 5.1 `modulistica.php`

- Discrimina su `$_GET['mutua']` (`no`/vuoto = BANCA, altrimenti MUTUA).
- Estrae i modelli attivi: `SELECT rif, Codice, Descrizione, NomeFile FROM tab_modelli WHERE status='S' AND rif='BANCA' (o 'MUTUA') ORDER BY ...`.
- Il comportamento dipende dal `HTTP_REFERER`: se l'utente arriva da `schedasocio.php` / `sqldati_schedasocio.php` i moduli vengono linkati **precompilati** con parametri (`modello`, `cag`, `socio`, `idsocio`, `luogo`, eventualmente `action=print`); altrimenti i moduli sono mostrati ma non attivabili (invito a entrare dalla Scheda Socio).
- Estrae il `luogo` della filiale del socio con join `tab_psw` × `sds_soci`.
- Tre tipologie di "visibilità" del modello (icona): libero (lucchetto aperto), con password (lucchetto), con invio mail (busta) — derivate da liste di `Codice` (es. `SO01/SO02` con password, `SO06` con invio mail).
- Riga dedicata alla generazione QR-Code (`modulistica/QRCODE.php`).
- I file dei moduli risiedono in `modulistica/` (es. `SO03_cessione_da_Socio_a_Socio.php`); i parametri sono passati in GET (con `urlencode` su `socio`).

### 5.2 `modulistica_mutua.php` e OAuth COMIPA

Prima di mostrare i modelli MUTUA, esegue una chiamata OAuth alle API COMIPA per arricchire i dati:
- Usa `oauth/http.php` + `oauth/oauth_client.php` (`oauth_client_class`).
- **Credenziali hardcoded nel sorgente**: `client_id = 'COMIPA_08673'`, `client_secret = 'V6YYWZRL64YNZT79CDEVZ7M1R2SN1V3B'` (vedi §7).
- Endpoint: `https://services.sinergia.bcc.it/WiSeHub/rest/comipa/v1/richiestainformazionisocio` (GET, con `abi=08673`, `codiceMutua=chiantimutua`, `nag=<cag>`).
- Dal risultato estrae indirizzo di residenza ed eventuali familiari, passandoli come parametri ai moduli MUTUA.
- In caso di errore esegue `var_dump($client)` / `var_dump($result)` (espone dettagli interni — vedi §7).

---

## 6. Tabelle e oggetti DB del dominio

| Tabella / Vista | Uso | File |
|-----------------|-----|------|
| `tab_eventi` | Anagrafica eventi (idevento, tipo_evento, descrizione, data, ora, luogo, note, link, posti_disponibili, posti_residui). | `eventi_gestionale.php`, `eventi_iscrizioni_nag.php` |
| `tab_eventi_iscrizioni` / `TAB_EVENTI_ISCRIZIONI` | Iscrizioni/partecipanti agli eventi. | `eventi_gestionale.php`, `eventi_iscrizioni_nag.php` |
| `tab_news` | News redazionali (datainsert, newscategoria, newstitolo, newspost). | `news.php` (ramo update legacy) |
| `tab_modelli` | Catalogo modulistica (rif BANCA/MUTUA, Codice, Descrizione, NomeFile, status). | `modulistica.php`, `modulistica_mutua.php` |
| `tab_psw` | Anagrafica filiali/password (filiale, desc_filiale, psw, email_estesa, luogo). | `bday_mail.php`, `modulistica.php`, codice commentato di `assemblea_auth.php` |
| `sds_soci` | Anagrafe soci SADAS importata. | `bday.php` |
| `tab_soci_as37` | Anagrafe soci (sorgente AS400/AS37) per la mail compleanni. | `bday_mail.php` |
| `view_richiesteincorso` | Vista CAG con richieste in corso (esclusione dalla mail). | `bday_mail.php` |
| `tab_xls_cessionibanca` | Cessioni a banca (icona in bday). | `bday.php` |
| `tab_xls_esclusioni` | Esclusioni per sofferenza (icona in bday). | `bday.php` |
| `TAB_MUTUA` | Soci Mutua (flag in bday). | `bday.php` |

> Le pagine Assemblea (`assemblea20XX.php`) e Documentazione/Statuto/Video sono **statiche** (nessuna query). `news.php`/`news_index.php`/`news_soci.php` lavorano via filesystem (`scandir("news/")`).

---

## 7. Debito tecnico e rilievi di sicurezza

### Sicurezza
- **Password hardcoded `cicalo`**: presente in `admin_news.php` (`public $password = 'cicalo';`) e nel codice commentato di `assemblea_auth.php`. Password debole, in chiaro nel sorgente, versionata.
- **Credenziali OAuth COMIPA hardcoded** in `modulistica_mutua.php` (`client_id`/`client_secret` in chiaro). Da spostare in `config/_config.php` o in un secret store.
- **SQL injection diffusa**: i parametri GET/POST sono concatenati direttamente nelle query senza prepared statement né escaping in `eventi_gestionale.php` (INSERT iscrizione, UPDATE posti), `eventi_iscrizioni_nag.php` (`WHERE NAG = <nag>`), `bday.php` (`$_GET['key']`), `news.php` ramo update (`newscategoria`/`newstitolo` non escaped), codice commentato di `assemblea_auth.php` (`WHERE psw = '<psw>'`).
- **`admin_news.php` (Evoluted Directory Listing)**: consente upload, unzip e cancellazione file/directory protetti solo da una password statica in sessione, con `overwriteOnUpload = true`. Rischio di sovrascrittura/upload arbitrario nella cartella `news/`.
- **`help/help.php`**: `$_GET['nome']` concatenato in URL/HTML senza sanitizzazione (XSS/path manipulation) e tecnologia obsoleta (ActiveX Windows Media Player, `.wmv`).
- **`var_dump` di oggetti interni** in `modulistica_mutua.php` in caso di errore API (espone token/credenziali/dettagli).
- **Debug residuo**: `echo $select_insert;` in `eventi_gestionale.php` stampa la query; branch finale che stampa `peeeee`.
- **`error_reporting(0)`** attivo in più file (`news.php`, `bday.php`, `bday_mail.php`, `video.php`, `statuto.htm` via include) — nasconde errori a runtime.

### Logica / qualità
- **UPDATE senza WHERE** in `news.php` (ramo `tab_news`): aggiorna *tutte* le righe della tabella.
- **Aggiornamento posti_residui non atomico** in `eventi_gestionale.php` (read-modify-write in PHP): possibile overbooking in concorrenza; nessun controllo che `posti_residui > 0` prima dell'INSERT.
- **`bday_mail.php` invia sempre a un destinatario di test** (`alessiofedi@chiantibanca.it`); la riga con la mail reale di filiale è commentata. La routine va corretta prima dell'uso reale.
- **PHPMailer presente ma non usato** dal dominio: `bday_mail.php` usa `ini_set` + `mail()` nativa, non `routines/PHPMailer/`. Coesistenza di due approcci d'invio.

### Duplicazione per anno / file morti
- **Assemblea duplicata per anno** invece di essere parametrizzata: `assemblea2021.php`, `assemblea2022.php`, `old/assemblea2020.php`. Date, termini e path PDF sono hardcoded in ciascun file; il cambio anno richiede di modificare `assemblea_auth.php` (`include` + costanti) e i file in `assemblea/`.
- **File "- Copia"** lasciati in produzione: `eventi_gestionale - Copia.php`, oltre ad altri presenti in `old/` (`admin_cessioni - Copia.php`, `check_zonecompetenza - Copia.php`, `soci_auth - Copia.php`).
- **Tre pagine News parzialmente ridondanti**: `news.php`, `news_index.php`, `news_soci.php` con logica `scandir` quasi identica; `news_index.php` e `news_soci.php` non risultano referenziate da nessuna pagina (codice potenzialmente morto — da verificare). Inoltre `news.php` mescola due paradigmi (file in cartella + `tab_news`).
- **`assemblea_auth.php`**: il nome suggerisce un'autenticazione che in realtà è tutta commentata; la pagina è un semplice wrapper di `include`. Logica obsoleta lasciata in commento (oltre 200 righe).
- **IP/host hardcoded** sparsi (`10.119.192.46:8080`, `10.197.139.22:8080`) per video, FAQ, link socio: ambienti diversi non parametrizzati.
- **Countdown assemblea** in `index.php` con data 2022 hardcoded (riquadro commentato).
