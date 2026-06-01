# 05 - Flussi dati e import (Documento Funzionale)

> Portale ChiantiBanca - Soci. Questo documento spiega, in chiave di processo,
> **da dove arrivano i dati dei soci**, con quale frequenza vengono aggiornati,
> quali tabelle alimentano quali funzionalità del portale e quali attività
> manuali competono all'Ufficio Soci.

## 1. In sintesi

Il portale **non è la fonte primaria** dei dati: è un sistema di consultazione e
reportistica che lavora su una **copia locale** (database MySQL `soci`) dei dati
provenienti dai sistemi della banca. Questa copia viene rigenerata
periodicamente da due flussi:

1. **Flusso automatico notturno** — un'elaborazione schedulata interroga il
   datawarehouse **SADAS** e ricarica le tabelle anagrafiche e statistiche dei
   soci. Avviene senza intervento umano (esclusi sabato e domenica).
2. **Flusso manuale dell'Ufficio Soci** — alcuni dati non disponibili in SADAS
   (estrazioni dal gestionale **Sicra**, dall'archivio documentale **Isidoc**,
   da transazioni host "verdone" e da fogli Excel interni) vengono esportati a
   mano in file CSV e caricati dall'operatore tramite la pagina di upload.

In entrambi i casi vale la regola: **ogni aggiornamento sostituisce
integralmente i dati precedenti** (la tabella viene svuotata e ricaricata da
zero). Non esistono aggiornamenti parziali.

> Nota temporale: i dati provenienti da SADAS si riferiscono sempre alla **sera
> precedente**; per questo nei filtri di data il portale propone come data finale
> "ieri" (vedi pagine di reporting).

---

## 2. Le sorgenti dei dati

| Sorgente | Cos'è | Esempi di dati forniti |
|---|---|---|
| **SADAS** | Datawarehouse della banca, interrogato in automatico | Anagrafica soci, certificati azionari, recapiti, trasferimenti, domande, under 35, impieghi/raccolta, decaduti |
| **Sicra** (gestionale, dal 2022) | Sistema di gestione soci; estrazioni manuali via DbQuery, Riepiloghi, Sinergia | Anagrafica soci di dettaglio, certificati, domande, riacquisto azioni, soci decaduti |
| **Isidoc** | Archivio documentale | Corrispondenza inviata ai soci |
| **Host "verdone"** (transazioni ZW37, AS37, AS75) | Sistema centrale | Comuni dei soci, deceduti, dipendenti, giovani, volumi, dati statistici |
| **Excel CDA_ELENCO** | Workbook interno dell'Ufficio Soci con macro | Ammissioni, cessioni, esclusioni, decessi/eredi, acquisto ulteriori azioni deliberate dal CdA |
| **COMIPA / ChiantiMutua** | Gestionale storico Mutua | File SDD, anagrafica Mutua (eredità v2.00) |
| **WTech** (fornitore) | Invio periodico via mail | File `mutua.csv` settimanale |
| **Organizzazione interna** | Log applicativo | Log SOCICN02 FORMADOC |

---

## 3. Flusso automatico notturno (da SADAS)

### 3.1 Come funziona

Un'elaborazione schedulata sul server (procedura `crea_sds_soci`) gira
tipicamente di notte e:

1. si collega al datawarehouse SADAS;
2. per ciascuna area informativa, **svuota** la corrispondente tabella del
   portale e la **ricarica** con i dati aggiornati di SADAS;
3. al termine invia una **email di riepilogo** all'indirizzo
   `soci@chiantibanca.it` con il numero di record caricati per ciascuna tabella,
   così l'Ufficio Soci può verificare la buona riuscita dell'aggiornamento.

**Cadenza**: giornaliera dal lunedì al venerdì. L'elaborazione **non viene
eseguita il sabato e la domenica**.

### 3.2 Cosa aggiorna (e a cosa serve)

| Area informativa aggiornata | Alimenta nel portale |
|---|---|
| Anagrafica soci | Situazione Soci, scheda socio |
| Certificati azionari | Posizione azionaria del socio |
| Recapiti / dati di contatto | Scheda socio, invii corrispondenza |
| Trasferimenti, movimenti ingresso/uscita | Lista trasferimenti, statistiche flussi |
| Domande di ammissione | Lista ammissioni |
| Soci under 35 | Reportistica giovani soci |
| Corrispondenza documentale (Isidoc) | Storico documenti del socio |
| Impieghi / raccolta / numero rapporti | Reportistica commerciale per socio |
| Soci decaduti da liquidare / liquidati | Gestione liquidazioni |
| Previsionale per area/filiale | Pianificazione (soci necessari al pareggio) |
| **Dati consolidati storici** | **Grafici e andamenti temporali** della base sociale (un'istantanea per ogni esecuzione) |

I "dati consolidati" sono particolari: a differenza delle altre tabelle (che
vengono sovrascritte), ad ogni esecuzione **aggiungono una nuova fotografia**
(numero soci totali, NAG attivi, ecc.), costruendo lo storico che alimenta i
grafici di andamento.

---

## 4. Flusso manuale dell'Ufficio Soci (upload CSV)

### 4.1 Quando serve

Per i dati che **non transitano da SADAS** (estrazioni Sicra di dettaglio,
documenti Isidoc, transazioni host, delibere del CdA gestite in Excel), il
caricamento è **a cura dell'operatore dell'Ufficio Soci**, in modo manuale e su
richiesta, con la cadenza opportuna per ciascuna fonte.

### 4.2 Come si carica un file

1. L'operatore apre la pagina **Admin Upload Area** ([admin_upload.php](../../admin_upload.php)).
   La pagina elenca tutte le fonti caricabili con: descrizione, **cadenza**
   prevista, tabella di destinazione e **data/ora dell'ultimo caricamento**.
2. Sceglie la fonte e clicca l'icona di upload: si apre una pagina dedicata con
   le **istruzioni passo-passo** su come produrre il file (da Sicra, Isidoc,
   verdone o Excel) e salvarlo in CSV con il nome atteso.
3. Carica il file (`sfoglia...`) e conferma. Il sistema svuota la tabella,
   ricarica i dati e mostra **quanti record sono stati inseriti**.
4. La data dell'ultimo caricamento si aggiorna e diventa visibile a tutti
   sull'Admin Upload Area, fungendo da promemoria/controllo.

> La pagina evidenzia in giallo le fonti più critiche da tenere aggiornate
> (es. `sds_sinergiareport_soci` e `tab_mutua`).

### 4.3 Le fonti caricabili manualmente

| Fonte / file | Cosa contiene | Come si ottiene | Cadenza |
|---|---|---|---|
| Anagrafica soci (Sicra) | Anagrafica di dettaglio | Sicra > DbQuery > PS_SociAnagrafica → CSV | (da verificare) |
| Certificati / Domande / Riacquisto azioni (Sicra) | Posizioni azionarie e domande | Sicra > DbQuery → CSV | (da verificare) |
| Anag NAG / Anagrafica ristretta (Sicra) | Dati anagrafici clienti | Sicra > DbQuery → CSV | (da verificare) |
| Sinergia Report Soci | Report soci Sinergia | Sicra > Servizi Sinergia → CSV | (da verificare) |
| Decaduti liquidati / non liquidati (Sicra) | Soci decaduti | Sicra > Soci > Riepiloghi > Stampa Soci Decaduti → CSV | (da verificare) |
| Corrispondenza Isidoc | Documenti inviati ai soci | Estrazione da Isidoc → CSV | (da verificare) |
| Comuni soci | Comuni di residenza | Verdone ZW37, query LF_SOCIIND → CSV | (da verificare) |
| Deceduti | Clienti deceduti (StatoBlocco SU/94) | Verdone ZW37, query LF_RAPPBLO → CSV | (da verificare) |
| Dipendenti | Elenco dipendenti ChiantiBanca | Verdone ZW37, query LF_DIPENDE → CSV | (da verificare) |
| Giovani | Soci per fascia d'età | Verdone ZW37, query LF_CLIETA → CSV | (da verificare) |
| Volumi | Volumi soci | Verdone ZW37, query MZ_ASVOL → CSV | (da verificare) |
| Soci AS37 / AS75 | Dati e statistiche soci | Transazioni host AS37 / AS75 → CSV | (da verificare) |
| Ammissioni | Nuove ammissioni deliberate dal CdA | Excel CDA_ELENCO, macro → CSV | Dopo ogni CdA |
| Cessioni banca | Cessioni azioni | Excel CDA_ELENCO, macro → CSV | Dopo ogni CdA |
| Esclusioni | Esclusioni soci | Excel CDA_ELENCO, macro → CSV | Dopo ogni CdA |
| Decessi-Eredi | Decessi e subentro eredi | Excel CDA_ELENCO, macro → CSV | Dopo ogni CdA |
| Acquisto ulteriori azioni | Acquisti ulteriori deliberati | Excel CDA_ELENCO, macro → CSV | Dopo ogni CdA |
| Mutua | Anagrafica ChiantiMutua | File ricevuto **settimanalmente** via mail da WTech | Settimanale |
| SDD | Tracciato SDD per InBank | Gestionale COMIPA → file | (da verificare) |
| Log FORMADOC | Log applicativo SOCICN02 | Fornito dall'Organizzazione | (da verificare) |

### 4.4 Dato importante per gli operatori

- Il **formato file** atteso è CSV con **separatore punto e virgola (`;`)** —
  cioè il "CSV delimitato da separatore di elenco" prodotto da Excel italiano. La
  prima riga (intestazioni) viene ignorata.
- Il **nome del file** deve essere esattamente quello indicato nelle istruzioni
  della pagina (es. `sds_soci_anagrafica.csv`): è preimpostato e non va cambiato.
- L'**ordine delle colonne** deve restare quello previsto: se in Sicra o nella
  macro Excel viene aggiunta/spostata una colonna, i dati rischiano di finire nei
  campi sbagliati senza segnalazione (vedi documento tecnico).
- Alcuni caricamenti possono **richiedere tempo**: non chiudere la pagina prima
  del messaggio di conferma con il conteggio dei record.

---

## 5. Quadro complessivo: chi alimenta cosa

```
   SADAS  ──(notte, lun-ven, automatico)──►  SDS_SOCI, certificati, contatti,
                                              trasferimenti, domande, under35,
                                              Isidoc, impieghi/raccolta, decaduti,
                                              previsionale, dati consolidati (storico)

   Sicra  ──(manuale, CSV via upload)─────►  sds_soci_anagrafica, sds_soci_certificati,
                                              sds_soci_domande, sds_anag_nag,
                                              sinergia, decaduti liquidati/non

   Isidoc ──(manuale, CSV)────────────────►  sicra_isidoc_soci_corrispondenza

   Verdone (ZW37/AS37/AS75) ──(manuale)───►  tab_comuni_soci, tab_deceduti,
                                              tab_dipendenti, tab_giovani, tab_volumi,
                                              tab_soci_as37, tab_soci_as75

   Excel CDA_ELENCO ──(manuale, post-CdA)─►  tab_xls_ammissioni, _cessionibanca,
                                              _esclusioni, _decessi_eredi,
                                              _acquistoulterioriazioni

   WTech (mail settimanale) ──(manuale)───►  tab_mutua
```

Le funzionalità del portale (Situazione Soci, Trasferimenti, Ammissioni,
Deceduti, scheda socio, grafici di andamento) leggono **solo** da queste tabelle
MySQL: la loro correttezza dipende quindi dalla **regolarità degli
aggiornamenti** sopra descritti.

---

## 6. Punti di attenzione operativi

- **Affidabilità**: se l'elaborazione notturna fallisce (es. problema di
  collegamento a SADAS), alcune tabelle possono restare vuote o parziali e il
  portale mostra dati incompleti finché non si rilancia. La mail di riepilogo e
  i conteggi sono il principale strumento di controllo.
- **Dipendenza dall'operatore**: i caricamenti manuali non sono automatizzati;
  l'unico promemoria è la colonna "Ultimo Agg.to" in Admin Upload Area. È buona
  prassi controllarla periodicamente e ricaricare le fonti dopo ogni CdA o,
  per la Mutua, alla ricezione settimanale del file da WTech.
- **Riservatezza**: i file CSV contengono dati personali dei soci; vanno trattati
  e conservati di conseguenza (vedi rilievi nel documento tecnico).

---

## 7. Riferimenti

- Pagina di upload: [admin_upload.php](../../admin_upload.php)
- Dettaglio tecnico degli script: [docs/tecnica/05-import-etl.md](../tecnica/05-import-etl.md)
