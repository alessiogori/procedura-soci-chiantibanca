# 01 - Architettura, Ruoli e Accessi

> Documento funzionale - Portale ChiantiBanca - Soci
> Dominio: Architettura, Ruoli e Accessi
> Destinatari: referenti di business, Ufficio Soci, responsabili di filiale/area, project manager.

---

## 1. Cos'è il Portale Soci e a chi è destinato

Il **Portale ChiantiBanca - Soci** è l'applicazione web interna che consente alla banca di gestire e consultare l'intera **base sociale** (i "soci" di ChiantiBanca). Raccoglie in un unico punto i dati anagrafici dei soci, l'andamento delle ammissioni e delle uscite, i trasferimenti di quote, i decessi, i plafond finanziari e le statistiche territoriali.

È uno strumento **a uso esclusivamente interno**, raggiungibile dalla rete aziendale e dall'Intranet di ChiantiBanca. Non è rivolto ai soci finali ma al personale della banca:

- **Ufficio Soci** (gestione centrale della base sociale);
- **Segreteria di Presidenza e Direzione**;
- **Ufficio Legale** e **Controllo di Gestione**;
- **Capi Area** (responsabili di area territoriale);
- **Filiali** (operatori e responsabili di sportello).

Il portale ha avuto tre versioni principali:

- **v1.00 (2020)** - primo rilascio;
- **v2.00 (2021)** - unificazione con l'ex Portale Mutua (ora il portale gestisce sia i soci ChiantiBanca sia i soci ChiantiMutua);
- **v3.00 (2022)** - passaggio al sistema gestionale Sicra.

---

## 2. Come avviene l'accesso (Single Sign-On con l'Intranet)

L'utente **non inserisce credenziali specifiche** per il Portale Soci. L'autenticazione è delegata all'**Intranet aziendale ChiantiBanca** (`chiantibanca.worktogether.it`), secondo questo flusso:

1. L'utente apre il Portale Soci.
2. Se non risulta già autenticato, viene **reindirizzato automaticamente alla pagina di login dell'Intranet**.
3. Una volta effettuato il login sull'Intranet, l'utente viene riportato al portale, che riceve dall'Intranet le informazioni di identità: **codice utente, email e codice di profilo/filiale**.
4. Da quel momento l'utente vede il portale con i contenuti corrispondenti al proprio profilo.

In pratica chi è già loggato sull'Intranet entra nel Portale Soci **senza ulteriori passaggi** (Single Sign-On). Fino al 2024 era possibile anche un accesso anonimo; oggi l'accesso senza autenticazione è bloccato.

> Nota di sicurezza (per i referenti tecnici): il riconoscimento del profilo si basa su informazioni memorizzate lato browser (cookie). Questo è semplice e comodo, ma rende il controllo degli accessi meno robusto rispetto a una sessione server "forte". Il dettaglio e le raccomandazioni sono nel documento tecnico [01-architettura-infrastruttura.md](../tecnica/01-architettura-infrastruttura.md).

Il nome dell'utente e la sua mansione vengono recuperati dal sistema bancario (SADAS) e mostrati in alto a destra nell'intestazione del portale, con un colore diverso a seconda del profilo (arancione per l'Admin Soci, giallo per i profili di staff centrale, verde per gli altri).

---

## 3. I profili utente e cosa vede ciascuno

Il portale riconosce l'utente tramite un **codice di profilo** (tecnicamente il campo `filiale_id`). Alcuni codici identificano ruoli speciali (uffici centrali, aree); tutti gli altri rappresentano la **filiale di appartenenza**. In base al profilo, la pagina principale mostra un diverso "riquadro Area Riservata" con le funzioni pertinenti.

| Profilo | A chi corrisponde | Cosa vede / può fare |
|---|---|---|
| **Admin Ufficio Soci** | Personale dell'Ufficio Soci | Vista completa sull'intera banca: situazione soci alla data, situazione plafond, liste domande (presenti e da regolare), ammissioni, trasferimenti, previsionale, liquidazioni. È il profilo con la visibilità più ampia |
| **Segreteria Presidenza / Direzione** | Segreteria di Presidenza e Direzione | Accesso alle viste riservate "Soci" (analogo all'Admin Soci per la parte di consultazione direzionale) e alle pagine di Direzione |
| **Legale** | Ufficio Legale | Profilo riconosciuto e identificato a video; consultazione delle aree comuni del portale (le funzioni dedicate dipendono dalle abilitazioni effettive - da verificare con l'Ufficio Soci) |
| **Controllo di Gestione** | Ufficio Controllo di Gestione | Statistiche generali della banca: dati generali, previsionale per aree e filiali, ammissioni, situazione plafond. Accesso alla sezione "Direzione" insieme all'Admin Soci |
| **Aree (Capi Area)** | Responsabili di area territoriale | Vista aggregata su tutte le filiali della **propria area** (es. CAMPI-PRATO, CHIANTI-FIRENZE, SIENA, PISTOIA-TIRRENO): statistiche, liste (domande, ammissioni, trasferimenti), controlli e motivazioni ingressi. L'area di competenza è associata al singolo Capo Area |
| **Filiale** | Operatori e responsabili di sportello | Vista limitata alla **propria filiale**: situazione soci, previsionale, domande, ammissioni, trasferimenti, soci usciti/deceduti, controlli sulle zone di competenza e sui saldi, giovani 18-35, liquidazioni |
| **Centro Imprese** | Filiale 100 (Centro Imprese) | Vista analoga a quella di filiale, riferita alla filiale 100 |
| **Utente generico** | Utenti senza profilo specifico assegnato | Accesso alle informazioni generali del portale, senza riquadro riservato ("non presente per questo utente") |

### 3.1 Le Aree territoriali

I Capi Area vedono i dati di tutte le filiali della loro area. Le aree previste nel sistema sono:

- **CAMPI-PRATO**
- **CHIANTI-FIRENZE**
- **SIENA**
- **PISTOIA-TIRRENO**

L'associazione tra il singolo Capo Area e la sua area è gestita internamente al portale; le filiali che compongono ciascuna area sono ricavate da una tabella di configurazione interna.

---

## 4. La Home page e i riquadri operativi

La pagina principale ([index.php](../../index.php)) è organizzata a "riquadri" (card) e si adatta al profilo dell'utente. Si compone di tre fasce.

### 4.1 Prima fascia - operatività

- **INFO GENERALI** (sempre visibile): collegamenti a Dati Statistici, Assemblea Soci, Eventi.
- **AREA RISERVATA** (variabile per profilo): è il riquadro centrale che cambia contenuto e colore in base al ruolo:
  - per **Soci/Direzione**: situazione soci alla data odierna, situazione plafond, liste domande, ammissioni, trasferimenti, previsionale, liquidazioni;
  - per **Filiale** / **Centro Imprese**: situazione soci di filiale, previsionale, domande, ammissioni, trasferimenti, giovani 17-35 anni, liquidazioni, controlli sulle zone di competenza (Italia/Estero), deceduti, usciti;
  - per **Area**: le stesse informazioni aggregate sulle filiali dell'area, più le motivazioni ingressi;
  - per gli **altri utenti**: messaggio "non presente per questo utente".
- **UTILITY** (sempre visibile): stampa moduli (addendum acquisto ulteriori quote, rateizzazione Under 35 / ChiantiMutua, donazione Under 35) e matrice delle distanze chilometriche tra filiali.

### 4.2 Seconda fascia - sintesi della base sociale

- **Sintesi Soci ChiantiBanca**: totale soci e ripartizione tra persone fisiche maschi, femmine e persone giuridiche, con barre percentuali.
- **Ultime novità**: area news/comunicazioni (attualmente mostra un'immagine di esito sondaggio).
- **Sintesi Soci ChiantiMutua**: totale soci Mutua e ripartizione per genere.

### 4.3 Terza fascia - andamento e territorio

- **Trend Soci dall'inizio anno**: soci a inizio anno, incrementi, decrementi e saldo alla data odierna.
- **Dati Consolidati (Banca / Soci)**: numero rapporti (NAG), conti correnti, numero azioni, capitale sociale, accordato, utilizzato e depositi, con confronto Banca vs Soci.
- **Andamentale di Filiale/Area/Banca**: cessioni a Banca, esclusioni, domande di ammissione in esame, senza PDF e da motivare, più i soci con zone di competenza da verificare.
- **Comuni Toscani e competenza territoriale**: mappa di accesso alle zone di competenza.

### 4.4 Barra di navigazione e funzioni rapide

In alto, oltre al logo e ai recapiti dell'Ufficio Soci (telefono, email, chat Teams), la barra ([css/menu.php](../../css/menu.php)) offre:

- collegamenti a FAQ e Documentazione;
- accessi alle aree riservate per profilo: **Filiale**, **Area**, **Direzione**, **Soci**, **Admin**;
- una casella di **ricerca soci** (per cognome/nome, codice CAG, numero socio, telefono);
- icone rapide per segnalazioni: **decesso socio**, **eliminazione vincolo conto**, **scissione certificati**, **compleanni soci**;
- un menu "Altri Portali" verso Intranet, ambienti di formazione e postalizzazione.

### 4.5 Stato degli aggiornamenti dati

Nel footer è disponibile la finestra **"Aggiornamenti"** che mostra, con un semaforo (verde/giallo/rosso) in base a quanti giorni sono passati dall'ultimo caricamento, lo stato di freschezza delle varie fonti dati (Sadas, Sicra, Sinergia, file XLS di cessioni/esclusioni, elenco Soci Mutua, ecc.). È uno strumento utile per capire "a che data" sono allineate le informazioni mostrate.

---

## 5. In sintesi

- Il Portale Soci è lo strumento interno di ChiantiBanca per gestire e consultare la base sociale (soci Banca e soci Mutua).
- L'accesso avviene tramite l'Intranet aziendale (Single Sign-On): l'utente non gestisce password dedicate al portale.
- Il portale riconosce il profilo dell'utente e mostra contenuti e funzioni differenti per **Ufficio Soci, Segreteria/Direzione, Legale, Controllo di Gestione, Capi Area e Filiali**.
- La home page condensa la situazione della base sociale (numeri, trend, dati consolidati) e offre, nel riquadro "Area Riservata", le funzioni operative pertinenti al ruolo di chi accede.
