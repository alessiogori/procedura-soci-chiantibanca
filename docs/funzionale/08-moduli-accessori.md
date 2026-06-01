# 08 - Moduli e Funzionalita' Accessorie

Documento funzionale relativo alle funzionalita' "accessorie" del Portale ChiantiBanca - Soci, cioe' quelle basate su componenti di terze parti che affiancano la gestione anagrafica dei soci: le **FAQ**, i **Sondaggi**, la generazione di **moduli PDF** e i **grafici**.

L'obiettivo e' spiegare, in ottica utente, a cosa servono queste funzioni, chi le usa e quali sono **attive** e quali **dismesse**.

---

## 1. Quadro d'insieme

| Funzionalita' | A cosa serve | Chi la usa | Stato |
|---|---|---|---|
| **FAQ - Domande & Risposte** | Raccolta di domande frequenti e relative risposte sul mondo soci | Soci e operatori di filiale/sede | **Attiva** (linkata dal menu) |
| **Sondaggi** | Somministrazione di questionari/sondaggi | (operatori) | **Dismessa / non collegata** (in home solo l'immagine di un esito) |
| **Modulistica PDF** | Stampa di moduli precompilati per pratiche soci (cessioni, ammissioni, coupon, ecc.) | Operatori | **Attiva** |
| **Grafici / cruscotti** | Visualizzazione grafica di andamenti (deceduti, eventi, dati di filiale) | Direzione e operatori | **Attiva** |
| **Esportazioni Excel / PDF** | Esportazione delle liste soci in Excel/PDF/stampa | Operatori | **Attiva** |

---

## 2. FAQ - Domande & Risposte (Question2Answer)

### A cosa serve
La sezione FAQ e' un piccolo portale di **Domande & Risposte** in stile community/forum, pensato per raccogliere in un unico posto le risposte alle domande piu' comuni sulla gestione dei soci (come avviene, ad esempio, nella descrizione del vecchio menu: "tutte le risposte alle domande piu' comuni che vengono fatte quotidianamente").

### Come si usa
- Si raggiunge dal menu del portale tramite la voce **"FAQ"**, che apre la sezione in una nuova scheda all'indirizzo `/soci/faq/?qa=questions`.
- All'interno l'utente puo' consultare le domande, cercarle, e (se abilitato) porne di nuove o rispondere.

### Caratteristiche per l'utente
- E' un'**area separata** dal resto del portale: ha una propria grafica (tema "SnowFlat"), una propria gestione utenti/login e un proprio archivio dati.
- L'accesso alle FAQ **non e' collegato al login del portale soci**: la sezione gestisce i propri utenti in autonomia.

### Stato
**Attiva**: e' la funzionalita' accessoria piu' strutturata ed e' linkata dal menu principale.

---

## 3. Sondaggi

### A cosa serve
Il modulo Sondaggi nasce per **creare e somministrare questionari** (es. indagini di gradimento verso i soci) e raccoglierne le risposte, con un'area amministrativa per la creazione dei sondaggi e una parte pubblica per la compilazione.

### Stato: dismesso / non collegato
- Nel portale **non esiste piu' un collegamento attivo** all'applicazione sondaggi: nessuna voce di menu vi punta.
- Nella home page e' presente unicamente il **rimando a un'immagine statica** che mostra l'**esito di un sondaggio gia' svolto** (un'anteprima/risultato in formato immagine), non al modulo di compilazione vero e proprio.
- Il modulo conserva i dati in file locali (non nel database soci) e ha una propria area amministrativa protetta da password.

In sintesi: la funzione **e' stata utilizzata in passato** ma oggi risulta **dismessa**; resta nel portale come residuo, con il solo esito storico mostrato in home.

---

## 4. Modulistica PDF

### A cosa serve
Permette agli operatori di **generare e stampare moduli PDF precompilati** con i dati del socio selezionato, evitando la compilazione manuale. Tipici moduli:
- **Cessioni** di quote (da socio a socio, da socio a non socio);
- **Richieste di ammissione** e modelli AS00/AS05 (es. coupon ritiro olio);
- Documenti per la **Mutua** e set di **QR code**.

### Come si usa
- Dalla pagina **"Modulistica"** del portale (e dalla scheda socio) l'operatore sceglie il modello desiderato; il sistema produce al volo il PDF con i dati gia' inseriti, aperto in una nuova scheda pronto per la stampa.
- Alcuni moduli partono da un **PDF prestampato** su cui il sistema scrive i dati (sovrastampa), altri sono generati interamente.

### Stato
**Attiva**: e' una delle funzioni operative piu' usate.

---

## 5. Grafici e cruscotti

### A cosa serve
Forniscono una **lettura visiva e immediata** di alcuni andamenti, a supporto di direzione e operatori. I grafici sono presenti, ad esempio, nelle pagine di:
- **Deceduti** e deceduti presunti;
- **Eventi** (iscrizioni, gestionale eventi);
- **Dati di filiale**.

### Come si usa
I grafici compaiono direttamente all'interno delle relative pagine del portale, alimentati dai dati estratti dal sistema; l'utente li consulta senza azioni particolari.

### Stato
**Attiva**.

---

## 6. Esportazioni (Excel / PDF / stampa)

### A cosa serve
Nelle liste soci e nei report l'utente puo' **esportare i dati** in Excel, PDF o inviarli alla stampa, per elaborazioni esterne o archiviazione.

### Come si usa
Tramite i pulsanti di esportazione presenti sopra le tabelle (liste soci, trasferimenti, ammissioni, ecc.).

### Stato
**Attiva**.

---

## 7. Riepilogo stato funzionalita'

- **Attive**: FAQ (Domande & Risposte), Modulistica PDF, Grafici/cruscotti, Esportazioni Excel/PDF.
- **Dismesse / non collegate**: Sondaggi (resta solo l'immagine di un esito in home).

> Nota: i dettagli tecnici (prodotti, versioni, percorsi e rischi di sicurezza/manutenzione) sono documentati in [docs/tecnica/08-componenti-terze-parti.md](../tecnica/08-componenti-terze-parti.md).
