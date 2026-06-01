# Documentazione Funzionale - Dominio "Domande, Ammissioni, Trasferimenti, Cessioni e Campagna Azioni"

Portale ChiantiBanca - Soci

Questo documento descrive, in ottica di business, i processi di gestione del ciclo di vita del socio e delle azioni: dalla domanda iniziale all'ammissione, fino ai trasferimenti e alle cessioni (rimborsi). È pensato per gli utenti dell'Ufficio Soci, della rete (filiali/aree) e per chi deve comprendere le regole operative del portale.

## Attori e ruoli

L'accesso al portale è governato da un identificativo (`filiale_id`) memorizzato in fase di login:

| Profilo | Codice | Ambito |
|---|---|---|
| Admin Ufficio Soci | 999 | Accesso completo, incluse le funzioni riservate (es. calcolo cessioni) |
| Segreteria | 998 | Funzioni di segreteria |
| Legale | 997 | Area legale |
| Controllo Gestione | 996 | Reportistica di controllo |
| Aree | 995 | Vista per area territoriale |
| Filiale | altro | Vista limitata alla propria filiale |

La pagina di calcolo cessioni è **riservata all'Ufficio Soci** (profilo 999). Le pagine di reportistica filtrano i dati per filiale o area in base ai parametri di navigazione.

## Concetti e regole chiave

- **Azione sociale:** valore nominale unitario **30,33 EUR**.
- **Minimo per essere socio:** **33 azioni** (controvalore di circa 1.000 EUR). Le campagne mirano a portare i soci sotto soglia almeno a 33 azioni; nei trasferimenti chi cede non dovrebbe scendere sotto 33 azioni.
- **Termine dei 60 giorni:** una domanda deliberata va regolarizzata entro **60 giorni dalla delibera**; oltre tale termine va valutato il rigetto. Il portale evidenzia in arancione le domande in scadenza (55-60 giorni) e in rosso quelle oltre i 60 giorni.
- **Under 35:** il portale evidenzia i nuovi soci/riceventi con meno di 35 anni (incentivo all'ingresso dei giovani).
- **Allineamento dati con la banca dati SADAS:** i dati bancari sono aggiornati "a ieri sera", quindi i report propongono di default come data finale il giorno precedente.
- **Anno fiscale:** la maggior parte dei report parte di default dal 1° gennaio dell'anno corrente.

## I processi di business

### 1. Domanda di sottoscrizione / acquisto azioni (da esaminare)

Quando un cliente presenta domanda per diventare socio o per acquistare azioni, la domanda entra nello stato **"da esaminare"**.

- **Pagina:** Domande a Socio (da esaminare).
- **Chi la usa:** Ufficio Soci e rete, per monitorare le domande in attesa.
- **Cosa mostra:** elenco delle domande con presenza/assenza del contratto in formato PDF nel documentale, anzianità della domanda, eventuale flag Under 35, e — in caso di acquisto — un avviso se il venditore resterebbe sotto le 33 azioni.
- **Allerta:** le domande ferme da troppo tempo vengono evidenziate in rosso (oltre 90 giorni).
- **Azione possibile:** registrare una motivazione di ingresso (vedi processo Motivazioni).

### 2. Sollecito domande senza documentazione

Alcune domande risultano presenti ma **senza il PDF del contratto archiviato**. Vanno sollecitate.

- **Pagina:** Domande a Socio (da sollecitare).
- **Chi la usa:** Ufficio Soci.
- **Cosa mostra:** elenco delle domande prive di documento, con il nominativo, la data della domanda, la data e l'utente che ha stampato il contratto.
- **Azione possibile:** inviare con un clic una **mail di sollecito** al dipendente che ha gestito la pratica (in copia l'Ufficio Soci), invitandolo a verificare o eliminare la domanda. Le mail non vengono inviate nel weekend. Le domande molto vecchie sono segnalate con un'icona di allarme.

### 3. Regolarizzazione domanda deliberata (da regolare)

Dopo la delibera, la domanda passa allo stato **"da regolare"**: occorre completare l'operazione contabile.

- **Pagina:** Domande a Socio (da regolare).
- **Chi la usa:** Ufficio Soci.
- **Cosa mostra:** per ogni domanda deliberata, il saldo del conto del richiedente (per verificare la capienza necessaria a coprire azioni + sovrapprezzo) e il **conteggio dei giorni dalla delibera** ("Time GG").
- **Regola dei 60 giorni:** verde/normale entro i tempi, arancione tra 55 e 60 giorni (in scadenza), rosso oltre i 60 giorni (da valutare il rigetto).

### 4. Ammissione di un nuovo socio

Una volta deliberata e regolarizzata la domanda, il cliente diventa socio.

- **Pagina:** Ammissioni Soci.
- **Chi la usa:** Ufficio Soci, rete, controllo di gestione.
- **Cosa mostra:** elenco dei soci ammessi nel periodo, azioni sottoscritte e relativo importo, eventuale **socio defunto** a cui si subentra (successione), prodotto di conto corrente associato, flag Under 35 ed **età media** dei nuovi soci.
- **Output operativo:** scarico in formato CSV ("tracciato delle Ammissioni") e conteggio dei soci Under 35.
- **Azione possibile:** registrare la motivazione di ingresso.

### 5. Campagna azioni (raggiungimento del minimo)

Molti soci possiedono meno delle 33 azioni minime. La campagna mira a farli salire al minimo.

- **Pagina:** Campagna Azioni.
- **Chi la usa:** rete commerciale e Ufficio Soci.
- **Cosa mostra:** per filiale/area, quanti soci possiedono da 1 a 32 azioni. Cliccando su una quantità si ottiene l'elenco nominativo, con la raccolta diretta e indiretta di ciascuno (per dare priorità ai contatti) e l'indicazione di quante quote/euro mancano al minimo.
- **Note operative:** i conteggi includono i soci con rateizzazione in corso (ma non li mostrano nel dettaglio); i dipendenti hanno la raccolta oscurata.

### 6. Trasferimenti di azioni

Le azioni possono passare tra soci o tra soci e non soci, per diverse causali.

- **Pagina:** Trasferimenti Soci.
- **Chi la usa:** Ufficio Soci.
- **Cosa mostra:** elenco dei trasferimenti del periodo con cedente, ricevente, data, tipologia, importo e sovrapprezzo, azioni residue del cedente e flag Under 35 del ricevente.
- **Tipologie di movimento:** Trasferimento, Compravendita, Successione, Donazione, Fusione. Per alcune tipologie viene ricordato di verificare la necessità di un regolamento.
- **Allerta:** se il cedente resta con meno di 33 azioni (e il ricevente non è Under 35) viene segnalato.
- **Output operativo:** scarico in formato CSV ("tracciato dei Trasferimenti").

### 7. Cessione / rimborso azioni

Il socio può chiedere la liquidazione (rimborso) delle proprie azioni. Il rimborso è subordinato alla disponibilità del **Fondo Riacquisto Azioni Proprie** e al plafond, e segue un ordine di coda.

- **Pagina:** Amministrazione Cessioni (riservata Ufficio Soci).
- **Chi la usa:** Ufficio Soci.
- **Cosa mostra/calcola:** quante richieste di rimborso precedono quella in esame e per quale importo, il valore disponibile (Fondo/plafond), il netto ancora da pagare e una **stima dei mesi al rientro** con un'**ipotesi di mese/anno di rimborso**. La stima si basa sulla media delle ammissioni degli ultimi 6 mesi (proiettata su base annua).
- **Output operativo:** scarico CSV previsionale con tutte le cessioni residue e l'ipotesi di rientro per ciascuna.

### 8. Motivazioni di ingresso / uscita

Per analisi e statistiche, gli operatori registrano manualmente la motivazione per cui un socio entra o esce.

- **Pagine:** form di inserimento motivazione, elenco motivazioni, statistiche motivazioni.
- **Chi le usa:** rete (inserimento) e Ufficio Soci/Controllo di gestione (analisi).
- **Motivazioni di INGRESSO previste:** Vantaggi economici su Rapporti, Vantaggi economici ChiantiMutua, Altro.
- **Motivazioni di USCITA previste:** Cambio Banca, Cessione azioni, Deceduto, Sofferenza.
- **Cosa offre:** statistiche aggregate per motivazione (IN e OUT affiancate) con possibilità di scendere al dettaglio dei nominativi, filtrabili per filiale o area.
- **Integrazione:** dalle liste Domande/Ammissioni/Trasferimenti, un'icona indica se la motivazione è già presente (verde) o da inserire (gialla), con accesso diretto al form.

### 9. Fasce di anzianità del socio

A supporto di campagne e iniziative sociali, i soci sono segmentati per anzianità.

- **Pagine:** elenco soci per fascia di anzianità; elenco soci per fascia.
- **Chi le usa:** Ufficio Soci, marketing/iniziative sociali.
- **Fasce:** fino a 10 anni, 10-20, 20-30, 30-40, 40-50, oltre 50 anni. È possibile anche vedere i soci con una specifica anzianità (anche proiettata all'anno successivo).
- **Cosa mostra:** anagrafica, anzianità, età, azioni e controvalore, e indicazione se il socio è anche **socio della Mutua**.
- **Output operativo:** scarico CSV per fascia.

## Output operativi disponibili (riepilogo)

| Processo | Esportazione |
|---|---|
| Ammissioni | CSV `ammissioni.csv` |
| Trasferimenti | CSV `trasferimenti.csv` |
| Cessioni | CSV previsionale `cessioni_ipotesirimborso.csv` |
| Fasce (senza richieste) | CSV `fasciaN.csv` |
| Domande (esaminare/regolare/sollecitare) | Copia tabella negli appunti (CTRL+C); l'export su file non è attivo |

## Note e avvertenze funzionali

- Alcuni report di domande segnalano l'anzianità a **90 giorni** mentre la regola di rigetto è a **60 giorni dalla delibera**: le due soglie misurano momenti diversi del processo (ricezione domanda vs. delibera) ma la differenza va tenuta presente per evitare confusione operativa *(da verificare con l'Ufficio Soci la corretta interpretazione)*.
- La lista "domande da regolare" parte di default dal 2022 e non dal 1° gennaio dell'anno corrente: per ottenere il periodo desiderato impostare manualmente la data di inizio.
- I dati provengono in parte dal sistema bancario (SADAS) aggiornato alla sera precedente: i totali del giorno corrente non sono mai inclusi.

---

Documenti correlati:
- [Documento tecnico del dominio](../tecnica/03-domande-ammissioni-cessioni.md)
