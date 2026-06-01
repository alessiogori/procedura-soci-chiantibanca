# 07 - Amministrazione, Controlli e Comunicazioni (documento funzionale)

> Questo documento descrive **chi fa cosa e perché** nelle attività amministrative e di controllo del Portale Soci ChiantiBanca: gestione utenti e password, monitoraggio delle pratiche sul socio, controlli e quadrature dei dati, gestione delle zone di competenza territoriale, invio di comunicazioni e segnalazioni via email/PEC, ed esportazioni dati.

Documento tecnico correlato: [07 - Amministrazione e Utility](../tecnica/07-amministrazione-utility.md).

## Attori

| Attore | Ruolo nel portale |
|--------|-------------------|
| **Ufficio Soci (Admin)** | Profilo amministrativo (codice filiale `999`, in alcune funzioni anche `997`/`998`). Gestisce password, monitoraggio, controlli, zone di competenza, esportazioni. Riceve le segnalazioni dalle filiali. |
| **Filiale / Dipendente** | Personale di sportello/filiale. Invia segnalazioni all'Ufficio Soci (decesso, scissione certificato, eliminazione vincolo) e consulta i dati di propria competenza. |
| **Sistemi esterni** | ChiantiMutua (info soci entrati/usciti), MailUp (newsletter ai soci), PEC Actalis, IsiDoc (documentale), SADAS (sistema bancario). |

---

## 1. Gestione utenti e password (Admin Center)

L'**Admin Center** è la pagina di regia dell'Ufficio Soci: da qui si lanciano l'aggiornamento dei dati dal sistema bancario, il caricamento dei file, l'accesso alla WebMail PEC, l'invio dei flussi al CDA, il protocollo, la gestione news, la modulistica e la gestione eventi.

La **gestione password filiali** consente all'Ufficio Soci di:
- consultare l'elenco delle filiali con la relativa password di accesso alla sezione riservata del portale;
- modificare la password di una filiale;
- inviare via email alla filiale la propria password (link di posta precompilato).

Perché: ogni filiale accede ad un'area riservata con credenziali proprie; l'Ufficio Soci ne è il gestore.

> Nota di rischio funzionale: le password sono visibili in chiaro a chi accede alla pagina. Trattare l'accesso a questa funzione come riservato.

---

## 2. Monitoraggio delle pratiche sul socio (Monitor Socio)

Il **Monitor Socio** è il registro delle comunicazioni e pratiche ricevute o aperte su un singolo socio: contestazioni, solleciti, reclami, richieste di informazioni, altro.

Per ciascuna segnalazione si registrano:
- **tipologia** (contestazione, sollecito, reclamo, info, altro);
- **forma e data di ricezione** (mail, PEC, lettera, raccomandata, telefonata) e **da chi** è stata ricevuta;
- **descrizione** del caso;
- **esito** e **data esito**, con **stato** (Chiuso Positivo / Chiuso Negativo / In Sospeso, evidenziati a semaforo);
- a chi è stata **segnalata** e quando;
- eventuali **note**.

Chi: l'Ufficio Soci (e profili abilitati) apre, aggiorna e chiude le segnalazioni; può disattivarle (archiviazione logica) ed esportarle in formato CSV. Si accede al Monitor dalla scheda del socio.

Perché: tenere traccia documentata del contenzioso/relazione con il socio e dello stato di lavorazione.

---

## 3. Controlli e quadrature dei dati

L'Ufficio Soci dispone di una serie di **controlli automatici** che confrontano i dati del portale con il sistema bancario (SADAS/SDS) per individuare anomalie:

- **Cessioni vs SDS**: verifica che le richieste di cessione in essere siano coerenti con le azioni effettivamente possedute dal socio a sistema (evidenzia i casi in cui le azioni in cessione superano quelle disponibili). Serve a intercettare cessioni su posizioni già estinte o incongruenti.
- **Piano di Accumulo (PAC)**: monitora i Giovani Soci e i soci ChiantiMutua aderenti al piano di accumulo azioni, calcolando le azioni residue da sottoscrivere e le scadenze.
- **Deceduti senza volontà eredi**: elenca i soci deceduti la cui pratica è in stato di attesa (eredi che non hanno ancora manifestato la propria volontà), con il calcolo dei giorni trascorsi dal decesso (oltre/entro 1 anno) per dare priorità alle lavorazioni.
- **Aggiornamento saldi (CoGe)**: consente di aggiornare manualmente i valori di Capitale Sociale, Sovrapprezzo, Fondo e Plafond residuo per le liquidazioni, usati nelle altre viste del portale.

A supporto dei controlli territoriali esiste anche la **matrice delle distanze (km) tra filiali**, derivata da Google Maps, utile a valutare la filiale di riferimento più vicina al socio.

Chi: l'Ufficio Soci. I controlli si consultano dal menu, con possibilità di filtrare per filiale ed esportare gli elenchi.

---

## 4. Zone di competenza territoriale

ChiantiBanca opera su determinati territori. La gestione delle **zone di competenza** serve a:

- mappare i **comuni** (della Toscana e confinanti) indicando se ricadono nella **competenza** della banca e se vi è **presenza di filiale**, con possibilità di esplorare i comuni adiacenti;
- individuare i **soci residenti fuori zona di competenza** (in Italia o all'estero) e gestirne la **validazione**.

Per ogni socio fuori zona, l'Ufficio Soci registra un **esito di verifica**:
- **Valido** (la posizione è ammissibile, es. per motivazioni emerse in fase di domanda di ammissione: lavoro o immobili in zona);
- **Da escludere**;
- **Da verificare**.

Insieme all'esito si annotano la **descrizione della verifica**, la presenza o meno di **documentazione sul documentale**, la **matricola dell'operatore** e la data. Il sistema mostra in automatico eventuali motivazioni già presenti nella domanda di ammissione e produce un elenco esportabile.

Perché: garantire che la base sociale rispetti i requisiti territoriali statutari e documentare le eccezioni.

---

## 5. Comunicazioni e segnalazioni via email/PEC

Il portale automatizza diverse comunicazioni. Una regola trasversale: **gli invii automatici non avvengono il sabato e la domenica**.

### Segnalazioni dalle filiali all'Ufficio Soci
Il dipendente di filiale, partendo dalla ricerca del socio (per NAG, nome o numero conto), può inviare all'Ufficio Soci (`soci@chiantibanca.it`):

- **Segnalazione socio deceduto**: indica la data di decesso e conferma l'archiviazione dei documenti su IsiDoc; il sistema allega automaticamente l'elenco dei documenti già presenti nel documentale e ricorda di impostare la data decesso in anagrafica.
- **Richiesta di scissione certificato azionario**: indica come scindere il certificato del socio.
- **Richiesta di eliminazione vincolo conto**: indica il conto da cui rimuovere il vincolo.

In tutti i casi la mail riporta il nominativo del dipendente segnalante e un link per rispondere "eseguito".

### Comunicazioni periodiche (automatiche)
- **Aggiornamento ChiantiMutua**: elenco giornaliero dei soci entrati e usciti, inviato a ChiantiMutua per allineare le rispettive basi.
- **Compleanni dipendenti (BDAY)** e **movimenti di alcuni conti correnti**: comunicazioni interne di servizio.

### Newsletter / MailUp
- **Esportazione email soci per MailUp**: l'Ufficio Soci genera l'elenco delle email dei soci attivi (tutti, oppure i soli Under 35 persone fisiche) in un file da importare nella piattaforma di e-mail marketing MailUp.

Perché: dare un canale strutturato e tracciato alle richieste delle filiali ed automatizzare gli scambi informativi ricorrenti.

---

## 6. Esportazioni e utility

- **Esportazioni in Excel/CSV**: diverse viste (monitor, controlli, zone di competenza, mail MailUp, rubrica aziendale) producono file scaricabili per elaborazioni esterne.
- **Utility di modulistica**: l'Ufficio Soci stampa addendum contrattuali (ulteriori quote, rateizzazione, donazione Under 35) partendo dal NAG del socio.
- **Rubrica telefonica aziendale**: esportazione dell'elenco contatti interni.
- **Gestione eventi**: anagrafica eventi (con posti disponibili/residui) gestita dall'Ufficio Soci.

---

## Sintesi "chi fa cosa"

| Attività | Chi | Output |
|----------|-----|--------|
| Gestione password filiali | Ufficio Soci | Aggiornamento/invio credenziali alle filiali |
| Monitoraggio pratiche socio | Ufficio Soci | Registro segnalazioni con esito + CSV |
| Controlli/quadrature (cessioni, PAC, deceduti, saldi) | Ufficio Soci | Elenchi di anomalie / valori aggiornati |
| Zone di competenza | Ufficio Soci | Validazione soci fuori zona + CSV |
| Segnalazioni decesso/scissione/vincolo | Filiale -> Ufficio Soci | Email a `soci@chiantibanca.it` |
| Aggiornamento ChiantiMutua | Sistema (auto) | Email entrati/usciti a ChiantiMutua |
| Export MailUp | Ufficio Soci | CSV email soci per newsletter |
| Modulistica/utility/eventi/rubrica | Ufficio Soci | Stampe e file di servizio |
