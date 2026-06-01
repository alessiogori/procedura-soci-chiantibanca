# Changelog

Tutte le modifiche rilevanti a questo progetto sono documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.1.0/)
e il progetto aderisce (per quanto possibile, trattandosi di un'applicazione legacy)
al [Versionamento Semantico](https://semver.org/lang/it/).

> **Nota sulle date.** Per i rilasci storici (v1–v3) è noto con certezza solo l'anno,
> ricavato dalle intestazioni dei sorgenti (`index.php`) e dalla documentazione di progetto;
> il giorno indicato è quindi convenzionale (1° gennaio dell'anno di rilascio).

## [Unreleased]

### Added
- Documentazione tecnica completa in [`docs/tecnica/`](docs/tecnica/): 8 documenti di analisi
  divisi per dominio (architettura/infrastruttura, anagrafica soci, domande/ammissioni/cessioni,
  statistiche/reportistica, import/ETL, assemblea/eventi/news, amministrazione/utility,
  componenti di terze parti).
- Documentazione funzionale completa in [`docs/funzionale/`](docs/funzionale/): 8 documenti che
  descrivono processi di business, ruoli e output per ciascun dominio.
- `README.md` riscritto come panoramica completa del progetto (architettura, stack, ruoli, domini
  funzionali, struttura del repository, manutenzione, avvertenze di sicurezza, terze parti).
- `CHANGELOG.md` (questo file).
- Collegamento del repository locale al remote GitHub `git@github.com:alessiogori/procedura-soci-chiantibanca.git`.

### Notes
- L'analisi del codice ha evidenziato criticità di sicurezza e debito tecnico (autorizzazione su
  cookie non sicura, SQL injection diffusa, credenziali in chiaro versionate, API PHP rimosse in
  PHP 7+, dati personali nel repository). I dettagli sono nei documenti tecnici di ciascun dominio
  e riassunti nel [README](README.md#avvertenze-di-sicurezza). Nessuna di queste criticità è stata
  ancora corretta: la presente release introduce solo documentazione.

## [3.0.0] - 2022-01-01

### Changed
- **Passaggio al sistema gestionale Sicra**: rivisti i flussi di alimentazione dati e gli script di
  import (`upload/csv2sql_sicra_*`, pipeline `crea_*`) per integrare le estrazioni provenienti da Sicra
  oltre a SADAS.

## [2.0.0] - 2021-01-01

### Added
- **Unificazione con l'ex Portale Mutua**: integrate nel Portale Soci le funzionalità della Mutua
  (schede e liste lato Mutua, modulistica e flussi dati dedicati).

## [1.0.0] - 2020-01-01

### Added
- Primo rilascio del Portale ChiantiBanca – Soci: gestione anagrafica soci, domande di ammissione,
  azioni/certificati, trasferimenti, soci deceduti, plafond, statistiche e reportistica, gestione
  dell'Assemblea, degli eventi e delle comunicazioni ai soci.

[Unreleased]: https://github.com/alessiogori/procedura-soci-chiantibanca/compare/v3.0.0...HEAD
[3.0.0]: https://github.com/alessiogori/procedura-soci-chiantibanca/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/alessiogori/procedura-soci-chiantibanca/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/alessiogori/procedura-soci-chiantibanca/releases/tag/v1.0.0
