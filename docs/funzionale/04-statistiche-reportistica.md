# 04 - Statistiche, Grafici e Reportistica (Documentazione Funzionale)

> Cosa offre il Portale Soci in termini di indicatori, grafici e report, a chi servono e come si filtrano.

## 1. A cosa serve

L'area "Statistiche" del Portale ChiantiBanca - Soci fornisce alla **Direzione**, al **Consiglio di Amministrazione (CdA)**, alla **Segreteria Soci**, alle **Aree** e alle **Filiali** una fotografia e un andamento della compagine sociale: quanti soci ci sono, come si distribuiscono (sesso, età, anzianità, azioni, area/filiale), come evolvono nel tempo (ammissioni, uscite, cessioni, decessi, esclusioni) e quale impatto economico hanno sul capitale sociale e sul plafond.

Le visualizzazioni a video usano grafici interattivi; per la documentazione formale (Direzione/CdA) è disponibile un **Report Soci complessivo** stampabile in PDF.

## 2. Indicatori e report disponibili

### 2.1 Cruscotto statistico (sintesi)

Pagina di apertura con le carte di sintesi:

- **Soci in essere**, ripartiti in **Maschi / Femmine / Aziende**.
- **Fasce d'età** (18-30, 31-50, 51-60, 61-70, oltre 70) con quantità, percentuale ed **età media** di fascia.
- **Età media della banca**.
- **Socio più giovane** e **socio più anziano** (con link alla scheda).
- Grafico e dettaglio **per Area**.

### 2.2 Situazione soci alla data

Espone, per il periodo scelto, la variazione di:

- **Capitale sociale** e **numero azioni**: valore iniziale, incremento, decremento, valore finale (azione = 30,33 €).
- **Sovrapprezzo** azioni (iniziale/incremento/decremento/finale).
- **Numero soci**: iniziale, ammessi, usciti, finale.
- **Ammissioni e uscite per filiale** e andamento mensile (grafici).
- Per Admin Soci, sezione aggiuntiva "Nuove Ammissioni".

È la base della relazione andamentale al CdA.

### 2.3 Situazione plafond

Controllo del **plafond** (limite di capitale che la banca può movimentare per acquisti/cessioni di azioni). Mostra le somme di movimento contabile nel periodo, la disponibilità residua rispetto al plafond di partenza e una proiezione basata sul ritmo medio di ammissioni degli ultimi 6 mesi. Consente anche la **verifica di cessione di un singolo socio**.

### 2.4 Previsionale uscite

Stima degli **importi da rimborsare** per uscite future, per Area e per Filiale, suddivisi per tipologia: **Esclusione, Esclusione per sofferenza, Recesso, Morte, Cessione a Banca**. Per ogni filiale indica il **totale** e il **numero di nuovi soci necessari al pareggio** (per compensare i capitali in uscita).

Due modalità:
- **Completo (FULL)**: tutti i rimborsi previsti.
- **Limitato (LIMIT)**: rimborsi fino all'anno precedente (le cessioni a Banca sono sempre complete).

### 2.5 Movimentazioni: ammissioni, cessioni, liquidazioni, esclusioni, decessi

- **Ammissioni**: nuovi soci, trend mensile, dettaglio e drill-down per area/filiale.
- **Cessioni a Banca**: azioni riacquistate dalla banca, per area/fasce di importo/storico.
- **Liquidazioni**: posizioni decadute liquidate e da liquidare, con dettaglio.
- **Esclusioni**: soci esclusi e relativa tipologia/da liquidare.
- **Eredi / Decessi**: soci deceduti e trend.

### 2.6 Distribuzioni per fasce

- **Fasce d'età** (anche per singolo anno).
- **Fasce di numero azioni**.
- **Classi soci per anzianità** di appartenenza alla compagine e di rapporto bancario.
- **Fasce quote soci senza richieste in corso** (usata nel prospetto CdA).
- Ripartizione **per sesso** e **per tipo controparte** (Persona Fisica / Persona Giuridica).

### 2.7 Analisi per Area / Filiale

Quasi tutti gli indicatori sono declinabili per **Area** o per **singola Filiale**, con tabelle di drill-down e grafici dedicati (ammissioni, cessioni, volumi, liquidazioni).

### 2.8 Volumi e buoni assemblea

- **Volumi soci** per area e filiale.
- **Buoni assemblea**: rilascio buoni ai soci in occasione dell'assemblea.

## 3. Filtri disponibili

| Filtro | Comportamento |
| --- | --- |
| **Data inizio** (`datain`) | Default = inizio esercizio in corso (`$inizioanno`). Sul cruscotto generale considera tutto lo storico. |
| **Data fine** (`dataout`) | Default = ieri (i dati bancari SADAS sono allineati "alla sera precedente"). |
| **Ambito** | Banca intera, **Area** o **Filiale**. Le pagine adattano automaticamente titolo e dati all'ambito selezionato. |
| **Tipo previsionale** | Completo (FULL) o Limitato (LIMIT). |
| **Periodo** (report) | Mese/anno di riferimento per il report complessivo (es. 2022-01). |

## 4. Chi usa cosa

| Ruolo | Utilizzo tipico |
| --- | --- |
| **Direzione / CdA** | Report Soci complessivo, situazione capitale, previsionale, prospetto CdA. |
| **Admin Soci / Segreteria** | Cruscotto, situazione, ammissioni/uscite, liquidazioni, gestione buoni assemblea. |
| **Responsabili Area** | Statistiche e report filtrati sulla propria area. |
| **Filiale** | Statistiche e report della propria filiale. |

## 5. Prospetto per il Consiglio di Amministrazione

La pagina **"Prospetto CDA"** funge da guida operativa: elenca, nell'ordine, le viste da copiare per predisporre la documentazione del Consiglio (Situazione, Fasce quote, Liquidazioni, Cessioni a Banca, Esclusioni, Deceduti) e mette a disposizione due file Excel/CSV con il dettaglio delle posizioni **liquidate** e **da liquidare**.

## 6. Report PDF complessivo (Direzione/CdA)

Il portale produce un unico **Report Soci** in PDF (stampa) che raccoglie in sequenza:

1. **Copertina** — logo, ambito (Banca/Area/Filiale) e periodo.
2. **Statistiche generali** — totali, sesso, fasce d'età, età media, socio più giovane/anziano.
3. **Situazione** — capitale sociale, sovrapprezzo, numero soci (iniziale/finale).
4. **Previsionale** — uscite previste per area e filiale con soci necessari al pareggio.
5. **Liquidazioni** — posizioni da liquidare e liquidate.
6. **Giovani Under 35**.
7. **Azioni e fasce d'età**.
8. **Soci storici** — per anzianità di appartenenza.
9. **Indici generali uscite** — cessioni, esclusioni, decessi rapportati al fondo.
10. **Elenco filiali** (solo per report di Area o di Banca).

Il report è generabile a livello di **Banca**, **Area** o **Filiale** ed è pensato per essere allegato alla documentazione direzionale e consiliare.

## 7. Note operative

- I dati bancari (capitale, certificati, movimenti, plafond) provengono dal sistema **SADAS** e sono aggiornati **alla sera precedente**: per questo i filtri impostano per default la data fine a "ieri".
- All'inizio di ogni anno va aggiornata la data di inizio esercizio (`$inizioanno`) affinché i filtri di default puntino all'anno corrente.
- I dati del previsionale vengono ricalcolati da un processo batch (aggiornamento tabella previsionale) e datati nell'ultimo caricamento.
