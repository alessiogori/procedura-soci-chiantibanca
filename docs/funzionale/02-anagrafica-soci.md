# Documento Funzionale — Anagrafica Soci, Schede Socio e Stati del Socio

Questo documento descrive, dal punto di vista dell'operatore dell'Ufficio Soci (e delle Filiali/Aree abilitate), le funzioni del Portale ChiantiBanca relative alla base sociale: come si consultano i soci, cosa mostra la scheda del singolo socio, quali sono gli stati di un socio e come si producono le estrazioni.

## 1. Gli stati di un socio (regole di business)

Un socio non ha un singolo "stato", ma una combinazione di informazioni. Il portale le sintetizza con un pallino colorato e con etichette testuali.

| Stato | Come viene determinato | Indicatore |
|-------|------------------------|------------|
| **In essere (Socio a capitale)** | È socio attivo, senza data di uscita | Pallino verde |
| **Uscito per recesso** | Il socio ha chiesto e ottenuto il recesso | Pallino rosso |
| **Uscito per esclusione** | Esclusione deliberata (es. art.6, art.14, passaggio a sofferenza) | Pallino rosso |
| **Uscito per cessione quote a Banca** | Ha ceduto le quote alla Banca | Pallino rosso |
| **Uscito per decesso** | Risulta deceduto | Pallino bianco |
| **Deceduto presunto** | Ancora attivo come socio, ma con documentazione (es. certificato di morte) caricata negli archivi documentali | Lista dedicata "Deceduti presunti" |
| **Giovane (Under 35)** | Età inferiore a 35 anni | Etichetta verde "U35" |

In parallelo allo stato di socio, il portale riporta lo **stato anagrafico come cliente** (Status NAG): Cliente Potenziale, Cliente con rapporti, Ex Cliente.

Note di business utili all'operatore:
- L'uscita di un socio è sempre legata a una delibera del CDA (è riportata la data).
- In caso di decesso, le quote possono essere liquidate agli eredi, subentrate da un erede già socio, o subentrate da un nuovo socio: il portale traccia queste casistiche tramite le "domande".
- I soci che hanno acquisti azionari periodici ("PACK") sono evidenziati con il numero di azioni/mese e la data di fine pack.
- Un socio può essere anche socio di ChiantiMutua: in tal caso compare il logo/badge Mutua.

---

## 2. Consultazione e ricerca dei soci

### 2.1 Ricerca di un socio (Scheda Socio)
Pagina di ricerca: si può cercare per cognome/nome, CAG (numero anagrafico), numero socio o telefono. Il risultato è un elenco navigabile (ordinabile e filtrabile) con: numero socio, filiale, NAG, nominativo, data di nascita (con icona torta il giorno del compleanno), età, data di entrata, data di uscita, telefono/cellulare, numero e valore delle azioni, stato (pallino colorato) e segnalazioni rapide (Monitor, Cessioni a Banca, Sofferenza, badge Mutua).
Cliccando sul nominativo si apre la **scheda completa del socio**. È inoltre disponibile il download dell'elenco in formato CSV.

### 2.2 Scheda del singolo socio
La scheda raccoglie in un'unica pagina tutte le informazioni del socio:
- **Intestazione e sintesi**: nominativo, stato (pallino), numero socio, data di ingresso, NAG, azioni possedute e relativo valore, filiale, piazza, conto corrente, delegato/rappresentante, settorista, eventuale PACK, badge Mutua, indicazione "Dipendente ChiantiBanca".
- **Dati anagrafici**: data di nascita ed età, codice fiscale, indirizzo di residenza e di spedizione (con link a Google Maps), tipo di spedizione (Posta / Relax Banking / PEC, con alert in caso di PEC esclusiva), eventuali note sul requisito di competenza territoriale.
- **Dati di contatto**: telefono, cellulare, email, PEC (con indicazione della fonte) e iscrizioni a eventi.
- **Dati ramo/settore/attività** (codifica merceologica).
- **Dati gestionali**: date e motivi di ingresso/uscita (ammissione, inizio decadenza, recesso, esclusione, morte) con riferimento alla delibera CDA; status socio, status NAG, eventuale data di decesso.
- **Linguette informative** (si colorano se contengono dati):
  - **Monitor**: segnalazioni sul socio, con esito e possibilità di inserirne di nuove.
  - **Domande**: domande presentate (ammissione, recesso, rimborso, ulteriori quote, subentro erede, trasferimento) con date e collegamenti ai soggetti coinvolti.
  - **Documenti**: documenti contrattuali e personali archiviati.
  - **Trasferimenti / Cessioni a Socio**: movimenti di quote tra soci (trasferimento, compravendita, donazione, fusione, successione).
  - **Cessioni a Banca**: richieste di cessione quote alla Banca e stato del rimborso.
  - **Esclusioni**: pratiche di esclusione con tipologia (art.6, art.14, sofferenze) e stato della liquidazione/pagamento.
  - **Decessi**: pratiche di decesso ed eredi, sia storiche sia attuali, con eventuale erede subentrante.

La consultazione della scheda è **tracciata** (log dell'utente che la apre).

---

## 3. Elenchi e report per stato del socio

### 3.1 Situazione Soci in essere
Estrazione completa di tutti i soci attivi (a capitale), prodotta come file CSV scaricabile. Include dati anagrafici, contatti, azioni e valore, data entrata/uscita, settorista, stato NAG ed eventuale appartenenza a ChiantiMutua. Utile per analisi massive e invii.

### 3.2 Ammissioni a Socio
Elenco dei nuovi soci ammessi in un periodo (per default dall'inizio dell'anno a ieri), filtrabile per filiale o area.
Mostra filiale, numero socio, NAG, indicatore Under 35, nominativo, data di entrata, azioni e importo, presenza di PACK, ed eventuale socio defunto da cui si è subentrati (con relativo decesso). Sono indicati il totale degli ammessi, l'età media e il numero di Under 35. Per ogni ammissione è possibile registrare/consultare una **motivazione**. Esportabile in CSV.

### 3.3 Soci Usciti
Elenco dei soci usciti in un periodo, filtrabile per filiale/area. Per ciascuno riporta filiale, numero socio, NAG, indicatore U35, nominativo, date di entrata e uscita, azioni e importo, **motivo dell'uscita** (morte, esclusione, recesso, cessione a Banca), stato NAG, conto e prodotto collegato con relativo stato. Totale usciti, età media e numero di U35. Esportabile in CSV.

### 3.4 Deceduti
Cruscotto dei soci deceduti dall'inizio dell'anno (filtrabile per filiale/area) con il **dettaglio dello stato delle pratiche eredi**:
- domande di intestazione a eredi già eseguite;
- soci senza alcuna domanda avviata dagli eredi (con relativo controvalore azioni);
- domande di intestazione in corso;
- domande di liquidazione avanzate.
Il dettaglio per socio mostra azioni, valore, data decesso, data uscita, tipo domanda, erede e date. L'esportazione avviene selezionando la tabella e copiandola (CTRL+C). Segnala i casi in cui manca la data di decesso in anagrafica.

### 3.5 Deceduti Presunti
Elenco di posizioni **ancora attive come socio** per le quali esiste documentazione che fa presumere il decesso (es. certificato di morte archiviato). Serve come segnalazione alle Filiali per la verifica e l'eventuale comunicazione all'Ufficio Soci. Mostra filiale, NAG, numero socio, nominativo, stato NAG e stato socio, data nascita/decesso, tipo e date del documento, matricola dell'operatore.

### 3.6 Soci/Clienti Giovani (Under 35)
Elenco dei soggetti giovani per età selezionata, filtrabile per filiale, con possibilità di distinguere chi ha rapporti e chi è già socio della Banca. Indica per ciascuno se è già Socio Banca e/o Socio Mutua, con accesso diretto alla scheda. Strumento per attività commerciali/associative verso i giovani.

### 3.7 Soci ChiantiMutua
Elenco dei soci di ChiantiMutua, ricercabile per nominativo o codice fiscale e filtrabile per filiale. Per ciascuno indica filiale, NAG, nominativo, codice fiscale, classe tariffaria, data di adesione e se è anche socio della Banca. Esportabile in CSV. Consente di incrociare la base Mutua con quella Banca.

---

## 4. Funzioni collegate

### 4.1 Migrazione Carte Debito ICCREA
Funzione operativa per la ricerca e la stampa dei contratti delle carte di debito ICCREA migrate. Si ricerca per nominativo, CAG o numero carta; l'elenco evidenzia lo stato della richiesta del flusso ICCREA e se il contratto è già stato stampato, con accesso al modulo di contratto precompilato. Non riguarda direttamente lo stato del socio ma il soggetto come intestatario di carta.

### 4.2 Assemblea Soci (presenze, deleghe, pullman)
Gestionale a uso interno per l'assemblea: previa password di accesso (per Direzione, Capi Area, singole Filiali), mostra l'elenco dei soci di competenza con la possibilità di registrare presenza, delega data/ricevuta, scelta del pullman e della location. È presente la pagina dei totalizzatori (presenti, deleghe, scelte di location) e il calcolo del potenziale di voto. Le operazioni di stampa e copia della pagina sono tracciate.

---

## 5. Filtri e parametri ricorrenti

- **Periodo**: la maggior parte dei report usa un intervallo `dal / al`. Il "dal" è preimpostato all'inizio dell'anno fiscale; l'"al" è preimpostato a ieri (i dati di sistema sono aggiornati alla sera precedente).
- **Filiale / Area**: quasi tutti gli elenchi possono essere ristretti a una filiale o a un'area.
- **Esportazioni**: prevalentemente in CSV scaricabile; per "Deceduti" e "Deceduti presunti" l'esportazione è per copia/incolla della tabella.

> Nota operativa: alcune date di riferimento (in particolare nella lista "Deceduti presunti" e nelle pagine assemblea) sono fissate nel sistema e vanno aggiornate manualmente; in caso di discordanza tra l'anno mostrato e l'anno corrente, segnalare all'assistenza tecnica.
