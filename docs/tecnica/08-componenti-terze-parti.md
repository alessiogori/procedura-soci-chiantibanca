# 08 - Componenti e Librerie di Terze Parti

Documento tecnico relativo al dominio **"Moduli e Librerie di Terze Parti integrati"** nel Portale ChiantiBanca - Soci.

Il portale e' un'applicazione PHP "senza framework", ma incorpora una quantita' rilevante di **codice di terze parti versionato direttamente nel repository** (codice "vendored"): due intere applicazioni PHP autonome (Question2Answer e PHP Survey), diverse librerie per la generazione di PDF, una libreria di grafici, librerie frontend e alcune utility. Questo documento ne fornisce l'inventario, l'identificazione di versione/origine e una valutazione delle implicazioni di manutenzione, sicurezza e licenze.

> Nota metodologica: le versioni indicate sono state lette direttamente dai file (`QA_VERSION`, `FPDF_VERSION`, `$tcpdf_version`, `const VERSION`, `$Version`, `package.json`, header dei sorgenti). Dove non e' stato possibile determinarle con certezza viene indicato "(versione non determinata)".

---

## 1. Tabella inventario

| Componente | Tipo | Versione | Percorso | Scopo | Come e' integrato | Note sicurezza / manutenzione |
|---|---|---|---|---|---|---|
| **Question2Answer (Q2A)** | Applicazione PHP autonoma (Q&A / forum) | **1.8.6** (build 2021-04-20) | [faq/](../../faq/) | Sezione FAQ / Domande & Risposte per soci e operatori | App separata sotto `/soci/faq/`; link dal menu del portale ([css/menu.php](../../css/menu.php)). DB MySQL proprio configurato in `faq/qa-config.php` | Versione 2021 non aggiornata; espone una superficie d'attacco web autonoma (login, captcha, ricerca). Da tenere patchata o segregata |
| **PHP Survey (NetArt Media)** | Applicazione PHP autonoma (sondaggi) | (versione non determinata) | [sondaggi/](../../sondaggi/) | Creazione e somministrazione di sondaggi | App separata con propria area `admin/`; dati su file XML in `sondaggi/data/`, non su MySQL. Non risulta linkata dal menu (vedi sez. 3.2) | Credenziali admin in chiaro come hash MD5 in [sondaggi/config.php](../../sondaggi/config.php); MD5 obsoleto. Modulo apparentemente dismesso |
| **FPDF** | Libreria PDF | **1.81** | [modulistica/fpdf/fpdf.php](../../modulistica/fpdf/fpdf.php) | Generazione moduli PDF (modulistica soci) | `require_once('fpdf/fpdf.php')` negli script di `modulistica/` (es. [AS00_completo.php](../../modulistica/AS00_completo.php)) | Versione stabile e matura; poco soggetta a CVE ma non mantenuta attivamente |
| **TCPDF** | Libreria PDF | **6.2.26** | [modulistica/tcpdf/](../../modulistica/tcpdf/) | Generazione PDF avanzati (font, barcode, QR) | `require_once('tcpdf/tcpdf.php')` (es. [function/esempio.php](../../function/esempio.php)) | Versione datata; TCPDF ha avuto CVE storiche (SSRF/XSS via HTML). Cartella `examples/` da rimuovere in produzione |
| **TCPDI** | Estensione TCPDF (import PDF esistenti) | basata su FPDI 1.4.4 | [function/tcpdf/tcpdi.php](../../function/tcpdf/tcpdi.php) | Importazione di PDF template in TCPDF | Shim `class FPDF extends TCPDF` + `fpdf_tpl.php` | Copia parallela/duplicata della logica di import PDF rispetto a `modulistica/setasign/fpdi` |
| **FPDI (setasign)** | Libreria import PDF | (versione non determinata, namespace `setasign\Fpdi`) | [modulistica/setasign/fpdi/](../../modulistica/setasign/fpdi/) | Uso di PDF preesistenti come template su cui scrivere | `use \setasign\Fpdi\Fpdi;` + `require_once('setasign/fpdi/autoload.php')` in [modulistica/AS00_completo.php](../../modulistica/AS00_completo.php) | Coesistono due meccanismi FPDI (questo + TCPDI in `function/tcpdf`): ridondanza da consolidare |
| **wkhtmltox / wkhtmltopdf** | Binari esterni HTML->PDF/immagine | (versione non determinata) | [routines/wkhtmltox/](../../routines/wkhtmltox/) | Conversione HTML in PDF/immagine | Eseguibili Windows (`wkhtmltopdf.exe`, `wkhtmltoimage.exe`, `wkhtmltox.dll`) richiamati da PHP. Presente solo `_prova.php` come test | Progetto upstream **non piu' mantenuto** (deprecato). Binari nel VCS; verificare se realmente in uso (sembra prototipale) |
| **FusionCharts (PHP wrapper)** | Libreria grafici | (versione non determinata, JS caricato a parte) | [graph/fusioncharts.php](../../graph/fusioncharts.php) | Grafici nei cruscotti (deceduti, eventi, filiali) | `include("graph/fusioncharts.php")` in [deceduti.php](../../deceduti.php), eventi_*, [filiale_check.php](../../filiale_check.php) | Libreria **commerciale** con licenza proprietaria: verificare il titolo di licenza. Il runtime JS e' caricato separatamente (CDN o `js/`) |
| **Bootstrap** | Framework CSS/JS frontend | (versione non determinata; bundle minificato) | [js/bootstrap.bundle.min.js](../../js/bootstrap.bundle.min.js), css/ | Layout e componenti UI | Incluso da `css/main.php` / template | Versionatura non tracciata; aggiornamenti manuali |
| **jQuery** | Libreria JS | **2.2.4** | [js/jquery.min.js](../../js/jquery.min.js) | Base per DataTables, datepicker, plugin | Caricato nei template | jQuery 2.x e' **EOL**; consigliato passaggio a 3.x. Presenti piu' copie (`jquery.js`, `jquery.min.js`, `jquery.min.js.191`) |
| **DataTables** | Plugin jQuery tabelle | (versione non determinata) | [js/jquery.dataTables.min.js](../../js/jquery.dataTables.min.js), buttons.*, jszip, pdfmake | Tabelle ordinabili/esportabili (Excel/PDF/print) nelle liste soci | Caricato nei template; export PDF via `pdfmake.min.js` | Catena di plugin (buttons, jszip, pdfmake, vfs_fonts) da aggiornare in blocco |
| **SB Admin 2** | Tema admin Bootstrap | (versione non determinata) | [js/sb-admin-2.min.js](../../js/sb-admin-2.min.js) | Tema/layout dell'area amministrativa | Caricato nei template | Tema gratuito di StartBootstrap; aggiornamento manuale |
| **FontAwesome Free** | Icon set | **5.10.2** | [css/fontawesome-free/](../../css/fontawesome-free/) | Icone interfaccia | Foglio di stile incluso nei template | Versione 2019; aggiornabile alla 6.x |
| **Bootstrap Datepicker** | Plugin JS date | (versione non determinata) | [js/datepicker.min.js](../../js/datepicker.min.js) | Selezione date nei filtri report | Caricato nei template | - |
| **nicEdit** | Editor WYSIWYG JS | (versione non determinata) | [js/nicEdit.js](../../js/nicEdit.js) | Editing testo ricco (es. news) | Caricato dove serve | Progetto **abbandonato** da anni; valutare sostituzione |
| **Intro.js** | Tour guidato UI | (versione non determinata) | [js/intro.js](../../js/intro.js) | Tour/onboarding interfaccia | Caricato nei template | - |
| **PHPMailer** (copia "moderna") | Libreria invio email | **6.8.0** | [routines/PHPMailer/src/PHPMailer.php](../../routines/PHPMailer/src/PHPMailer.php) | Invio email dal portale | Inclusa dalle routine di invio (es. mail_dip.php) | Versione recente e supportata: e' la copia da preferire |
| **PHPMailer** (copia in Q2A) | Libreria invio email | **5.2.28** | [faq/qa-include/vendor/PHPMailer/class.phpmailer.php](../../faq/qa-include/vendor/PHPMailer/class.phpmailer.php) | Email del modulo FAQ | Usata internamente da Q2A | Serie 5.x EOL; aggiornata solo aggiornando Q2A |
| **PHPMailer** (copia in Sondaggi /admin) | Libreria invio email | **5.2.13** | [sondaggi/admin/include/class.phpmailer.php](../../sondaggi/admin/include/class.phpmailer.php) | Email modulo sondaggi (admin) | Interna a PHP Survey | Serie 5.x EOL; copia obsoleta |
| **PHPMailer** (copia in Sondaggi /include) | Libreria invio email | **5.1** | [sondaggi/include/class.phpmailer.php](../../sondaggi/include/class.phpmailer.php) | Email modulo sondaggi (frontend) | Interna a PHP Survey | **Versione molto datata** con vulnerabilita' note nelle 5.x precoci |
| **PHP OAuth API (Manuel Lemos)** | Libreria client OAuth 1/2 | (versione non determinata) | [oauth/](../../oauth/) | Login social / accesso API OAuth | Raccolta di esempi `login_with_*.php` + `oauth_client.php` | Solo esempi/scaffolding; integrazione effettiva non evidente. Codice ridondante e potenziale superficie d'attacco se esposto |
| **PhpTestBed** | Libreria "teste de mesa" (visualizzazione esecuzione PHP) | **0.2.0** | [routines/php_testbed/](../../routines/php_testbed/) | Strumento didattico di tracing di script PHP (usa `nikic/php-parser` v3) | Progetto Composer autonomo con `index.php`/`test.php` | **Non e' una dipendenza funzionale del portale**: strumento di terze parti finito nel VCS, probabilmente residuo. Rimovibile |
| **sql2xls (ExcelGen)** | Utility export Excel | (versione non determinata) | [routines/sql2xls/](../../routines/sql2xls/) | Esportazione query SQL in file XLS | Classe inclusa dalle pagine di export | Formato XLS legacy; valutare migrazione a PhpSpreadsheet |
| **Multi_Edit** | Mini-app editing tabellare | (versione non determinata) | [routines/Multi_Edit/](../../routines/Multi_Edit/) | Editing massivo di righe DB | Mini-applicazione con propri jQuery/DataTables/Bootstrap embedded | Porta copie proprie di jQuery/DataTables/Bootstrap (duplicazione). Verificarne l'uso reale |
| **Dir (function/structure)** | Libreria utility filesystem | (versione non determinata) | [function/structure/](../../function/structure/) | Gestione directory/struttura file | `require 'src/Dir.php'` ([function/structure/index.php](../../function/structure/index.php)) | - |
| **Event Photo Gallery / Uploader** | Script upload | (incorporato) | [admin_news.php](../../admin_news.php) | Upload immagini news | Script di terze parti inglobato direttamente nella pagina | Richiede GD; logica di password integrata nello script |
| **Contatore visite** | Contatore "fatto in casa" | n/a (non terza parte) | [counter/counter.php](../../counter/counter.php) | Conteggio visite con cifre GIF | Logica propria, log su file `counter/logs/` | Non e' terza parte: incluso per completezza. Scrive su file di testo (`counter.txt`, `ips.txt`) |

---

## 2. Question2Answer (modulo FAQ)

### 2.1 Identificazione

- **Prodotto**: Question2Answer, di Gideon Greenspan e contributori (GPL v2+).
- **Versione**: `QA_VERSION = '1.8.6'`, `QA_BUILD_DATE = '2021-04-20'` — vedi [faq/qa-include/qa-base.php](../../faq/qa-include/qa-base.php).
- **Percorso**: [faq/](../../faq/), con entry point [faq/index.php](../../faq/index.php) che fa `require 'qa-include/qa-index.php'`.
- **Configurazione**: `faq/qa-config.php` (credenziali e parametri del DB MySQL dedicato a Q2A).

### 2.2 Plugin presenti (`faq/qa-plugin/`)

`basic-adsense`, `event-logger`, `example-page`, **`facebook-login`**, `mouseover-layer`, `opensearch-support`, `recaptcha-captcha`, `tag-cloud-widget`, `wysiwyg-editor`, `xml-sitemap`.

Il plugin **`facebook-login`** include una libreria Facebook SDK legacy (`base_facebook.php`, `facebook.php`, `fb_ca_chain_bundle.crt`): si tratta dell'SDK PHP Facebook di prima generazione, **obsoleto e non funzionante** con le attuali API Graph/OAuth di Meta. E' un rischio (codice morto con certificati datati) e va disabilitato se non utilizzato.

### 2.3 Temi (`faq/qa-theme/`)

`Candy`, `Classic`, `Snow`, **`SnowFlat`** (tema di default moderno di Q2A 1.8.x).

### 2.4 Integrazione col portale

- **Link dal menu**: in [css/menu.php](../../css/menu.php) la voce "FAQ" punta a `http://10.197.139.22:8080/soci/faq/?qa=questions` (apertura in nuova scheda). Un vecchio menu ([css/old_menu/menu.php](../../css/old_menu/menu.php)) puntava a `faq/?qa=hot`.
- **Database**: Q2A usa un **proprio database MySQL** configurato in `faq/qa-config.php`, **separato** dalle tabelle `sds_*` del portale soci.
- **Autenticazione**: gestita internamente da Q2A (utenti/login propri); **non c'e' single sign-on** con il portale. Esiste un esempio di integrazione utenti esterni ([faq/qa-external-example/qa-external-users.php](../../faq/qa-external-example/qa-external-users.php)) ma e' solo codice di esempio.
- **Email**: Q2A usa la propria copia di **PHPMailer 5.2.28** in [faq/qa-include/vendor/PHPMailer/](../../faq/qa-include/vendor/PHPMailer/).

---

## 3. PHP Survey / Sondaggi

### 3.1 Identificazione

- **Prodotto**: PHP Survey di **NetArt Media** (netartmedia.net) — vedi intestazione di [sondaggi/include/SiteManager.class.php](../../sondaggi/include/SiteManager.class.php).
- **Versione**: (versione non determinata) — nessun marcatore di versione nei sorgenti.
- **Struttura**: front-end (`index.php`, `pages/`, `include/SiteManager.class.php`), area amministrativa (`admin/`, con `login_action.php`, `pages/create_survey.php`, ecc.) e proprie copie di `class.phpmailer.php` / `class.smtp.php`.

### 3.2 Integrazione col portale e stato

- **Persistenza dati su file XML**, non su MySQL: `SiteManager::InitData()` copia/legge `data/surveys_<md5(salt)>.xml` (cartella [sondaggi/data/](../../sondaggi/data/)). E' quindi un modulo **indipendente** dal DB soci.
- **Configurazione**: [sondaggi/config.php](../../sondaggi/config.php) contiene l'utente admin (`admin`) e la **password come hash MD5** (`b197f9b9eb5e712a26687320b3844c21`), email admin `alessiofedi@chiantibanca.it`, SMTP `localhost:25`, captcha disattivato.
- **Stato: verosimilmente DISMESSO.** Non risulta alcun link al modulo `sondaggi/` dal menu o dalle pagine del portale. L'unico riferimento ai "sondaggi" nella home ([index.php](../../index.php) riga ~653) e' un **link a un'immagine statica** (`img/sondaggio_portale_20240110.jpg` / `img/sondaggio_homeesito.png`), non all'applicazione PHP Survey. Il modulo sembra quindi un residuo non collegato.

### 3.3 Rischi specifici

- Autenticazione admin basata su **MD5 non salato** (debole).
- **PHPMailer molto datato** (5.1 nel front-end, 5.2.13 nell'admin).
- Se il path `sondaggi/admin/` resta raggiungibile via web, costituisce una superficie d'attacco per un modulo non piu' presidiato.

---

## 4. Librerie per categoria

### 4.1 Generazione PDF

- **FPDF 1.81** ([modulistica/fpdf/](../../modulistica/fpdf/)) — base per i moduli PDF semplici.
- **TCPDF 6.2.26** ([modulistica/tcpdf/](../../modulistica/tcpdf/) e copia in [function/tcpdf/](../../function/tcpdf/)) — PDF avanzati, font, barcode/QR ([modulistica/PHPBarcode/](../../modulistica/PHPBarcode/) per i barcode lato GD/FPDF).
- **FPDI (setasign)** ([modulistica/setasign/fpdi/](../../modulistica/setasign/fpdi/)) e **TCPDI** ([function/tcpdf/tcpdi.php](../../function/tcpdf/tcpdi.php), basato su FPDI 1.4.4) — import di PDF preesistenti come template.
- **wkhtmltox** ([routines/wkhtmltox/](../../routines/wkhtmltox/)) — binari HTML->PDF, apparentemente solo in prototipo (`_prova.php`).

> Ridondanza: esistono due percorsi paralleli per l'import PDF (setasign/fpdi vs function/tcpdf/tcpdi) e due copie di TCPDF. Da consolidare.

### 4.2 Grafici

- **FusionCharts** ([graph/fusioncharts.php](../../graph/fusioncharts.php)) — wrapper PHP che genera lo script di rendering; usato nei cruscotti ([deceduti.php](../../deceduti.php), [deceduti_presunti.php](../../deceduti_presunti.php), [eventi_gestionale.php](../../eventi_gestionale.php), [eventi_iscrizioni_nag.php](../../eventi_iscrizioni_nag.php), [filiale_check.php](../../filiale_check.php)). Prodotto **commerciale**: verificare la licenza d'uso.

### 4.3 Frontend

- **Bootstrap**, **jQuery 2.2.4**, **DataTables** (+ buttons/jszip/pdfmake/vfs_fonts per export), **SB Admin 2**, **FontAwesome Free 5.10.2**, **Bootstrap Datepicker**, **nicEdit**, **Intro.js** — vedi [js/](../../js/) e [css/](../../css/).

### 4.4 Utility e altro

- **PHPMailer** — copia "moderna" **6.8.0** in [routines/PHPMailer/](../../routines/PHPMailer/) (da preferire), piu' tre copie obsolete (5.2.28 in Q2A, 5.2.13 e 5.1 in Sondaggi).
- **sql2xls/ExcelGen** ([routines/sql2xls/](../../routines/sql2xls/)) — export XLS.
- **Multi_Edit** ([routines/Multi_Edit/](../../routines/Multi_Edit/)) — editing tabellare, con proprie copie embedded di jQuery/DataTables/Bootstrap.
- **PhpTestBed 0.2.0** ([routines/php_testbed/](../../routines/php_testbed/)) — strumento didattico basato su `nikic/php-parser` v3; **non e' una dipendenza del portale** (residuo nel VCS, rimovibile).
- **PHP OAuth API** ([oauth/](../../oauth/)) — client OAuth 1/2 di Manuel Lemos, presente come collezione di esempi senza integrazione evidente.
- **Dir** ([function/structure/](../../function/structure/)) — utility filesystem.

---

## 5. Rischi e raccomandazioni di sintesi

1. **Versioni datate e non aggiornabili facilmente.** Q2A 1.8.6 (2021), TCPDF 6.2.26, FPDF 1.81, jQuery 2.2.4 (EOL), FontAwesome 5.10.2 (2019). Definire una policy di aggiornamento, dando priorita' ai componenti web-facing (Q2A, jQuery).
2. **PHPMailer duplicato in 4 copie e versioni diverse** (6.8.0 / 5.2.28 / 5.2.13 / 5.1). Le serie 5.x sono EOL e con CVE note (header injection, RCE in 5.2.x precoci). Consolidare sull'unica copia 6.8.0 dove possibile.
3. **Plugin `facebook-login` obsoleto** in Q2A: SDK Facebook di prima generazione non funzionante, da disabilitare/rimuovere insieme al certificato datato.
4. **Modulo Sondaggi (PHP Survey) apparentemente dismesso** ma ancora presente: password admin in MD5, PHPMailer 5.1. Se non usato, rimuoverlo; se usato, isolarlo e aggiornare le credenziali.
5. **wkhtmltox deprecato upstream** e presente come binario: verificarne l'uso effettivo, altrimenti rimuovere.
6. **FusionCharts e' commerciale**: confermare la titolarita' della licenza; tutti gli altri componenti principali sono open source (GPL, LGPL, MIT/BSD-like).
7. **Codice vendored nel VCS** in grande quantita' (intere app + esempi `oauth/`, `php_testbed`, cartelle `examples/` di TCPDF). Aumenta la superficie d'attacco e il rumore nel repo. Valutare la rimozione del codice morto (`php_testbed`, `oauth/` se inutilizzato, `examples/`) e, dove sensato, la gestione via Composer/package manager invece del copia-incolla.
8. **Ridondanza PDF**: due percorsi FPDI e due copie TCPDF da consolidare.
