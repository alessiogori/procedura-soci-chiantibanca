<?php

/**
 *
 * Logger ALBA library for Code Igniter.
 *
 * @package        loggerAlba
 * @author         Massimo Zanini
 * @version        1.6
 * @license        GPL v3
 * 
 * Versioni:
 *      1.0  20240401 Prima Versione.
 *      1.1  20240429 Fix per implementatazione tracciato Alba.
 *      1.2  20240502 Disabilitato log per attivazioni cli().
 *      1.3  20240503 Classe utilizzabile fuori CI, Introdotta confLogMode, Introdotto flush().
 *      1.31 20240513 Introdotta setMode().
 *      1.4  20240625 Aggiornato a Spaziatura fissa con indicazioni BccSi (Nadia Bugini del 25/06/24), 
 *                    La funzione MULTIPLO() accetta Info di dettaglio per campo DETTAGLIO-INFO.
 *                    Disattivato log dell'indirizzo ip. (Indicazione BccSi NB del 26/06/24)
 *                    Allineamento a sinistra per tutti i campi escluso numerici. (Indicazione BccSi NB del 26/06/24)
 *                    Allineamento a destra per tutti i campi numerici con riempimento '0'. (Indicazione BccSi NB del 26/06/24)
 *                    Vista la lunghezza dei campi è stata rivista la disposizione dei campi:
 *                     - in FUNZIONE viene inserito il nome del controller (max 10 caratteri)
 *                     - in DESCRIZIONE-FUNZIONE viene inserito il nome della funzione richiamata. (max 16 caratteri).
 *      1.5  20250316 Fix: In caso di impersonificazione riporta utente reale. 
 *      1.6  20250407 Fix per base_url con http://. Si prende app_name se esiste oppure ultima parte di base_url.
 *      1.7  20251222 Fix come da indicazioni ticket 202405031303. La funzione RAP() adesso accetta il NAG.
 *                    Fix vari leggibilità a codice. Rettificato help. Centralizzata funzione setMode(), adesso viene richiamata anche da costruttore.
 *                    Log Mode preso da config se inizializzato con CI (conf loggeralba_logmode). 
 *                    Controllo abilitazione degli Hook in fase di caricamento.
 * 
 * Modalità di attivazione:
 *
 *      Senza CodeIgniter:
 *       
 *          1 Copiare:
 *               libraries/loggerALBA.php
 *          
 *          2 Nello script da cui fare log:
 *          
 *              require_once("./loggerALBA.php");
 *              $logger = New loggerALBA("PROVENIENZA","FUNZIONE","UTENTE");
 *              $logger->setMode("TEST"); //oppure $logger->setMode(""); per produzione
 *              $logger->NAG("NAG", "DETTAGLIO INFORMATIVO");
 *              $logger->flush();
 *      
 *      In CodeIgniter:
 *      
 *          1 Copiare:
 *               libraries/loggerALBA.php
 *               hooks/LoggerALBAHook.php
 *          
 *          2 Nel file config/hooks.php inserire:
 *          
 *               //Logger ALBA
 *               $hook['post_system'][] = array(	'class' => 'LoggerALBAHook',
 *                                                   'function' => 'flusher',
 *                                                   'filename' => 'LoggerALBAHook.php',
 *                                                   'filepath' => 'hooks');
 *          
 *          3 Aggiungere a config.php:
 *          
 *                / * <--- modifica qui
 *                |--------------------------------------------------------------------------
 *                | LOGGER ALBA > Log Mode 
 *                |--------------------------------------------------------------------------
 *                | Imposta il modo di esecuzione di LoggerAlba.
 *                | Esempio.
 *                |   $config['loggeralba_logmode'] = "TEST";     // Per TEST o applicativi da non tracciare.
 *                |   $config['loggeralba_logmode'] = "";         // Per PRODUZIONE in applicativi da tracciare.
 *                * / <--- modifica qui
 *                $config['loggeralba_logmode'] = "TEST";
 *          
 *          4 Attivare gli hooks in config/config.php
 *          
 *               $config['enable_hooks'] = TRUE;
 *          
 *          5 Configrare metodi e funzioni dei controller
 *          
 *               in Controller / costruttore:
 *          	        //Inizializza Logger ALBA
 *          	        $this->load->library(['loggerALBA']);
 *          
 *               poi, dove serve (es. funzione in Controller):
 *          
 *          	        //Per loggare accesso a NAG
 *          	        $this->loggeralba->NAG($NAG, $DETTAGLIO_INFO);
 *          
 *                      //Per loggare accesso a RAPPORTO
 *          	        $this->loggeralba->RAP($NAG, $COD_RAPP, $FILIALE, $NUM_RAPP, $DETTAGLIO_INFO)
 *          
 *                      //Per flaggare il log come accesso MULTIPLO
 *          	        $this->loggeralba->MULTIPLO($DETTAGLIO_INFO)
 *                       // NOTA BENE: Non è chiaro come deve essere usato il flag Multiplo.
 * 
 */

class loggerALBA
{
    private $CI = null;

    //Config
    public $confLogMode = "TEST";   //Impostare blank per Produzione
                                    //
    public $confLogPath = "E:/Logs/logAlba/";
    public $confLogFile = "";
    public $confErrFile = "";
    public $logType = null;

    //Tracciato ALBA
    public $logArr = [
        // "CRA" => ["alf", 3, 'S'],                             // INIT       ### CRA Codice Identificativo della banca    //Ticket 202405031303:  Il file dovrà avere specificata una sola volta il codice cassa.
        "BANCA" => ["alf", 3, 'S'],                           // INIT       ### BANCA Codice identificativo della banca
        "PROVENIENZA" => ["alf", 10, 'S'],                    // INIT       FUN PROVENIENZA Codice Identificativo della procedura alimentante
        "FUNZIONE" => ["alf", 12, 'S'],                       // INIT       FUN FUNZIONE Codice identificativo della funzione applicativa tracciata
        "DATA-OPERAZIONE" => ["alf", 8, 'S'],                 // INIT       ### DATA-OPERAZIONE Data dell'operazione nel formato
        "ORA-OPERAZIONE" => ["alf", 8, 'S'],                  // INIT       ### ORA-OPERAZIONE Ora dell'operazione nel formato HH:MM:SS:CC
        "TERMINALE" => ["alf", 4, 'S'],                       // INIT       USR TERMINALE Codice della postazione utilizzata dall'operatore della banca
        "UTENTE" => ["alf", 20, 'S'],                         // INIT       USR UTENTE Codice identificativo dell'operatore della banca nel S.I.
        "CODFISC-OPERATORE" => ["alf", 16, 'N'],              //            USR* Codice fiscale dell'operatore della banca
        "NAG" => ["num", 8, 'N'],                             // NAG        DAT NAG Codice di censimento del soggetto anagrafico consultato
        "TIPO-NAG" => ["alf", 3, 'N'],                        //            DAT* Specifica se persona fisica, ditta Individuale, persona giuridica o cointestazione "PF" "DI" "PG" "COI"
        "CODFISC-CLIENTE" => ["alf", 16, 'N'],                //            DAT* Codice fiscale del cliente
        "COD-RAPP" => ["num", 4, 'N'],                        // RAP        DAT COD-RAPP Codice identificativo del tipo rapporto consultato
        "FILIALE" => ["num", 3, 'N'],                         // RAP        DAT Codice della filiale di appartenenza del rapporto consultato
        "NUM-RAPP" => ["num", 6, 'N'],                        // RAP        DAT NUM-RAPP Numero del rapporto consultato
        "FILIALE-TERMINALE" => ["num", 3, 'N'],               //            USR FILIALE-TERMINALE Codice della Filiale di appartenenza del terminale. 
        "DATO-ULTERIORE" => ["alf", 16, 'N'],                 // OPT        FUN DATO-ULTERIORE Per eventuali campil di ricerca aggiuntivi (es. nome stampa) 
        "DETTAGLIO-INFO" => ["alf", 1600, 'N'],               // OPT        DAT DETTAGLIO-INFO Dati specifici della funzione di consultazione utilizzata
        "DETTAGLIO-INFO-2" => ["alf", 10, 'N'],               //                DETTAGLIO-INFO-2 Riservato per l'oscuramento dei record - DA NON UTILIZZARE
        "DETTAGLIO-INFO-3" => ["alf", 10, 'N'],               //                DETTAGLIO-INFO-3 iservato per la registrazione della Transazione Sicra - DA NON UTILIZZARE
        "DETTAGLIO-INFO-4" => ["alf", 10, 'N'],               //                DETTAGLIO-INFO-4 Espansione informativa per futuri ublizzi
        "DETTAGLIO-INFO-5" => ["alf", 20, 'N'],               //                DETTAGLIO-INFO-5 Espansione informativa per futuri ublizzi
        "DETTAGLIO-INFO-6" => ["alf", 20, 'N'],               //                DETTAGLIO-INFO-6 Riservato per registrazione dell'utente originario-DA NON UTILIZZARE
        "DETTAGLIO-INFO-7" => ["alf", 30, 'N'],               //                DETTAGLIO-INFO-7 Riservato per registrazione del TimeStamp di caricamento - DA NON UTILIZZARE
        "GBI-CODCLIENTE" => ["alf", 40, 'N'],                 //            DAT* Hash MDS del Codice Fiscale del Cliente
        "ABI-UTENTE" => ["alf", 5, 'N'],                      //            ### ABI dell'utente che ha eseguito l'operazione di consultazione
        "ABI-RAPPORTO" => ["alf", 5, 'N'],                    //            ### ABI del rapporto cliente consultato
        "ABI-BANCA-COLLOCATRICE" => ["alf", 5, 'N'],          //            ### ABI della banca collocatrice
        "GBI-UNITAORG" => ["alf", 10, 'N'],                   //            USR* Identificazion unità organizzativa dell'operatore della banca.
        "GBI-NUM-RAPP" => ["alf", 25, 'N'],                   // RAP,MULT   DAT Codice del rapporto consultato, 'MULTIPLO' per accessi multipli
        "GBI-OPERATORE" => ["alf", 40, 'N'],                  //            USR* Hash MD5 del codice fiscale dell'Utente
        "GBI-TERMINALE" => ["alf", 50, 'N'],                  //            USR Codice identificativo del terminale utilizzato per l'accesso (terminale VTAM o CICS, l'indirizzo IP o nome dns)
        "CONSULTAZIONE-MASSIVA" => ["alf", 1, 'N'],           // MULT       DAT Valorizzato con "S" in caso di registrazione funzione di accesso massivo ai dati
        "DESCRIZIONE-FUNZIONE" => ["alf", 50, 'N'],           //            FUN Descrizione della funzione
        "ESITO-OPERAZIONE" => ["alf", 80, 'N'],               //            FUN Eventuale nota relativa all'esito del tentativo di consultazione
    ];
    public $logObj = [];

    //Funzioni di Inizializzazione
    public function __construct($PROVENIENZA = null, $FUNZIONE = null, $UTENTE = null) {
        try {
            
            //Imposta Log Mode (e path Log/Err)
            $this->setMode($this->confLogMode);

            //Initialize Log Obj
            // $this->logObj["CRA"] = "299";    //Ticket 202405031303:  Il file dovrà avere specificata una sola volta il codice cassa.
            $this->logObj["BANCA"] = "299";
            $this->logObj["PROVENIENZA"] = "";  //trim($CI->config->item('base_url'), "/");
            $this->logObj["FUNZIONE"] = "";     //$CI->router->fetch_class() . "/" . $CI->router->fetch_method();
            $this->logObj["DATA-OPERAZIONE"] = date("Ymd");
            //data con centesimi di s
            date_default_timezone_set('Europe/Rome');
            $time_ms = microtime(true);
            $cs = sprintf("%02d", ($time_ms - floor($time_ms))*100);
            $this->logObj["ORA-OPERAZIONE"] = date("His", floor($time_ms)).$cs;
            $this->logObj["TERMINALE"] = "";     //gethostbyaddr($_SERVER['REMOTE_ADDR'])
            $this->logObj["UTENTE"] = "";       //$CI->ion_auth->user()->row()->username;
            $this->logObj["CODFISC-OPERATORE"] = "";
            $this->logObj["NAG"] = "";
            $this->logObj["TIPO-NAG"] = "";
            $this->logObj["CODFISC-CLIENTE"] = "";
            $this->logObj["COD-RAPP"] = "";
            $this->logObj["FILIALE"] = "";
            $this->logObj["NUM-RAPP"] = "";
            $this->logObj["FILIALE-TERMINALE"] = "";
            $this->logObj["DATO-ULTERIORE"] = "";
            $this->logObj["DETTAGLIO-INFO"] = "";
            $this->logObj["DETTAGLIO-INFO-2"] = "";
            $this->logObj["DETTAGLIO-INFO-3"] = "";
            $this->logObj["DETTAGLIO-INFO-4"] = "";
            $this->logObj["DETTAGLIO-INFO-5"] = "";
            $this->logObj["DETTAGLIO-INFO-6"] = "";
            $this->logObj["DETTAGLIO-INFO-7"] = "";
            $this->logObj["GBI-CODCLIENTE"] = "";
            $this->logObj["ABI-UTENTE"] = "";
            $this->logObj["ABI-RAPPORTO"] = "";
            $this->logObj["ABI-BANCA-COLLOCATRICE"] = "";
            $this->logObj["GBI-UNITAORG"] = "";
            $this->logObj["GBI-NUM-RAPP"] = "";
            $this->logObj["GBI-OPERATORE"] = "";
            $this->logObj["GBI-TERMINALE"] = "";
            $this->logObj["CONSULTAZIONE-MASSIVA"] = "";
            $this->logObj["DESCRIZIONE-FUNZIONE"] = "";
            $this->logObj["ESITO-OPERAZIONE"] = "";

            // Load CI instance
            $CI = null;
            if (function_exists("get_instance")) {
                //Get CI Istance
                $this->CI = &get_instance();
                $CI = $this->CI;
                //Carico Librerie
                $CI->load->library(['ion_auth']);
                $CI->load->helper(['url']);
                //Check necesary libs
                if (
                    !$CI->load->config('config') ||
                    !$CI->load->is_loaded('ion_auth') ||
                    !$CI->load->is_loaded('router') 
                ) {
                    throw new Exception("Cannot load loggerALBA!");
                }
                //Prendi logMode da config
                // senza scrivere errore se il config non c'è - L'errore eventuale viene scritto solo nel momento di utilizzo con funzione checkConfig()
                $CIconfig_logMode = $CI->config->item('loggeralba_logmode');
                if ($CIconfig_logMode !== false) {
                    $this->setMode($CIconfig_logMode);
                }
                //Valorizza dati da CI
                // $this->logObj["PROVENIENZA"] = trim($CI->config->item('base_url'), "/");
                $this->logObj["PROVENIENZA"] = $this->getApp($CI);      // #MZ 07/04/2025 v1.6 Fix per base_url con http://. Si prende app_name se esiste oppure ultima parte di base_url.
                $this->logObj["FUNZIONE"] = $CI->router->fetch_class();
                $this->logObj["DESCRIZIONE-FUNZIONE"] = $CI->router->fetch_method();
                //Utente
                $user_row =$CI->ion_auth->user()->row();
                $this->logObj["UTENTE"] = strtoupper($user_row->username); // #MZ 22/12/2025 strtoupper 
                //Utente - Codice Fiscale
                if(!empty($user_row->codice_fiscale)) $this->logObj["CODFISC-OPERATORE"] = $user_row->codice_fiscale;
                //Impersonificazione
                if(!is_null($CI->session->userdata('real_identity')) && $CI->session->userdata('real_identity')!=$CI->session->userdata('identity')) {
                    //Real Identity
                    $this->logObj["UTENTE"] = $CI->session->userdata('real_identity');
                    //Codice Fiscale Operatore (se esiste)
                    if ($CI->db->field_exists('codice_fiscale', 'users')) {
                        $cf = $CI->db
                            ->select('codice_fiscale')
                            ->where('username', $this->logObj["UTENTE"])
                            ->get('users', 1)
                            ->row('codice_fiscale');
                        if (!empty($cf)) {
                            $this->logObj['CODFISC-OPERATORE'] = $cf;
                        }
                    }
                }
                // Carica il file di configurazione se non già caricato
                if (!isset($CI->config)) {
                    $CI->load->config('config');
                }
                // Recupera il percorso dal config
                $envPath = $CI->config->item('env_repository');
                if (empty($envPath)) {
                    throw new Error("EnvLoader: 'env_repository' non trovato in config.php");
                }

            } else {
                //Prendi dati da costruttore se non siamo in CI
                if (
                    is_null($PROVENIENZA) ||
                    is_null($FUNZIONE) ||
                    is_null($UTENTE)
                ) {
                    throw new Exception("LoggerAlba: Inizializzazione fuori da CI senza parametria.");
                } else {
                    $this->logObj["PROVENIENZA"] = $PROVENIENZA;
                    $this->logObj["FUNZIONE"] = $FUNZIONE;
                    $this->logObj["UTENTE"] = $UTENTE;
                    $this->logObj["DESCRIZIONE-FUNZIONE"]  = ""; // Non valorizzato per Applicativi fuori CI
                }
            }

        } catch (Exception $e) {
            // echo 'Caught exception: ', $e->getMessage(), ".";

            //Scrivi errore
            $handle = fopen($this->confErrFile, "a+");
            fwrite($handle, date('YmdHis') . " construct(): " . $e->getMessage() . "\r\n");
            fclose($handle);

        }
    }
    // per impostare la modalità fuori CI
    public function setMode($mode){
        //Imposta Log
        $this->confLogMode = $mode;
        //Imposta Path
        $pathlogMode = $this->confLogMode;
        if(!empty($pathlogMode)) $pathlogMode = $pathlogMode."-";
        $this->confLogFile = $this->confLogPath . "Log-" . $pathlogMode . date('Ymd') . ".log";
        $this->confErrFile = $this->confLogPath . "Err-" . $pathlogMode . date('Ymd') . ".log";
    }
    // per prendere il nome della App CI
    private function getApp($CI){
        $app_name = $CI->config->item('app_name');
        if (!empty($app_name)) {
            return trim($app_name, "/");
        } else {
            $base_url = $CI->config->item('base_url');
            $parsed_url = parse_url(rtrim($base_url, "/"));
            if (!empty($parsed_url['path'])) {
                $segments = explode('/', trim($parsed_url['path'], '/'));
                $result = end($segments);
            } else {
                $result = $base_url;
            }
        }
    }
    private function checkConfig(){
        if($this->CI){
            // Verifica presenza di config
            $CIconfig_logMode = $this->CI->config->item('loggeralba_logmode');
            if ($CIconfig_logMode===false) {
               log_message("error", "Libreria LoggerAlba inizializzata con parametro loggeralba_logmode non definito in config.");
            }
            // Verifica Hook abilitati
            $hooks_enabled = (bool) $this->CI->config->item('enable_hooks');
            $has_logger_alba = false;
            if ($hooks_enabled && !empty($GLOBALS['EXT']->hooks['post_system'])) {
                foreach ($GLOBALS['EXT']->hooks['post_system'] as $h) {
                    if (
                        ($h['class'] ?? null) === 'LoggerALBAHook' &&
                        ($h['function'] ?? null) === 'flusher'
                    ) {
                        $has_logger_alba = true;
                        break;
                    }
                }
            }
            // display errori
            $errorHooks = "";
            if(!$hooks_enabled ){ $errorHooks .= "HOOKS disabilitati ,"; }
            if(!$has_logger_alba){ $errorHooks .= "Funzione LoggerALBAHook non abilitata nel file hooks ,"; }
            if(!empty($errorHooks)){ log_message("error", "Libreria LoggerAlba inizializzata con i seguenti errori:".trim($errorHooks, ",")); }
            // Verifica presenza del file LoggerALBAHook.php
            if(!file_exists(APPPATH."hooks/LoggerALBAHook.php")) {
                log_message("error", "Libreria LoggerAlba inizializzata ma file hooks/LoggerALBAHook.php assente."); 
            }
        }
    }

    //Funzioni per tracciatura
    //Richiamabili da programma
    public function NAG($NAG, $DETTAGLIO_INFO="") {
        try {

            //Check Configurazione
            $this->checkConfig();

            //Set log Type
            $this->logType = "NAG";

            //Set data
            $this->logObj["NAG"] = $NAG;
            //Set Opt data
            if(!empty($DETTAGLIO_INFO))
                $this->logObj["DETTAGLIO-INFO"] = $DETTAGLIO_INFO;

        } catch (Exception $e) {
            //Scrivi errore
            $handle = fopen($this->confErrFile, "a+");
            fwrite($handle, date('YmdHis') . " logNAG(): " . $e->getMessage() . "\r\n");
            fclose($handle);
            //Exception
            throw new Exception('Caught exception: '.$e->getMessage().".");
        }
    }

    public function RAP($COD_RAPP, $FILIALE, $NUM_RAPP, $DETTAGLIO_INFO="", $NAG=""){
        try {

            //Check Configurazione
            $this->checkConfig();

            //Set log Type
            $this->logType = "RAP";

            //Set data
            $this->logObj["COD-RAPP"] = $COD_RAPP;
            $this->logObj["FILIALE"] = $FILIALE;
            $this->logObj["NUM-RAPP"] = $NUM_RAPP;
            // $this->logObj["GBI-NUM-RAPP"] = $COD_RAPP . "/" . $FILIALE . "/" . $NUM_RAPP;    //Ticket 202405031303: Il campo GBI-NUM-RAPP non piò essere valorizzato in quanto fa parte dei campi riservati ad attività tecniche.
            //Set Opt data
            if(!empty($NAG))
                $this->logObj["NAG"] = $NAG;
            if(!empty($DETTAGLIO_INFO))
                $this->logObj["DETTAGLIO-INFO"] = $DETTAGLIO_INFO;

        } catch (Exception $e) {
            //Scrivi errore
            $handle = fopen($this->confErrFile, "a+");
            fwrite($handle, date('YmdHis') . " logRAP(): " . $e->getMessage() . "\r\n");
            fclose($handle);
            //Exception
            throw new Exception('Caught exception: '.$e->getMessage().".");
        }
    }

    public function MULTIPLO($DETTAGLIO_INFO=""){
        try {

            //Check Configurazione
            $this->checkConfig();

            //Set log Type
            $this->logType = "MULTIPLO";

            //Set DETTAGLIO-INFO
            if(!empty($DETTAGLIO_INFO))
                $this->logObj["DETTAGLIO-INFO"] = $DETTAGLIO_INFO;

            //Set data
            // $this->logObj["GBI-NUM-RAPP"] = "MULTIPLO";  //Ticket 202405031303: Il campo GBI-NUM-RAPP non piò essere valorizzato in quanto fa parte dei campi riservati ad attività tecniche.
            $this->logObj["CONSULTAZIONE-MASSIVA"] = "S";
            //Set Opt data

        } catch (Exception $e) {
            //Scrivi errore
            $handle = fopen($this->confErrFile, "a+");
            fwrite($handle, date('YmdHis') . " logMULTIPLO(): " . $e->getMessage() . "\r\n");
            fclose($handle);
            //Exception
            throw new Exception('Caught exception: '.$e->getMessage().".");
        }
    }

    // Flush dati
    // Funzioni richiamate da Hook
    public function close(){
        return $this->flush();
    }
    public function flush(){
        try{

            //Scrivi solo se è stato definito un logtype, ovvero è stata chiamata almeno una vola una delle funzioni NAG(), RAP() o MULTIPLO().
            if (!is_null($this->logType)) {
                // 1 Check aggiuntivi
                // TODO

                // 2 Scrivi tracciato
                //Aggiorna path
                $pathlogMode = $this->confLogMode;
                if(!empty($pathlogMode)) $pathlogMode = $pathlogMode."-";
                $this->confLogFile = $this->confLogPath . "Log-" . $pathlogMode . date('Ymd') . ".log";
                $this->confErrFile = $this->confLogPath . "Err-" . $pathlogMode . date('Ymd') . ".log";
                //Output handler
                $handle = fopen($this->confLogFile, "a+");
                //Debug
                // var_dump($this->logObj);
                //Genera Record Log
                $logRecord = "";
                foreach($this->logObj as $k=>$logDato){
                    //Definizione del dato
                    // es. ["CRA", "num", 3, 'S'],
                    $datoDefinizione = $this->logArr[$k];
                    $datoTipo = $datoDefinizione[0];
                    $datoLen = $datoDefinizione[1];
                    if(!empty($logDato) && $datoTipo=="num"){
                        //tipo num: Pad Numerico
                        // solo se non vuoto, se il campo è assente deve essere riempito da spazi
                        $datoStr = substr(str_pad($logDato, $datoLen, "0", STR_PAD_LEFT), 0, $datoLen); //STR_PAD_LEFT -> Allineamento a Destra
                    } else {
                        //tipo alf: Pad Alfanumerico
                        $datoStr = substr(str_pad($logDato, $datoLen, " ", STR_PAD_RIGHT), 0, $datoLen); //STR_PAD_RIGHT -> Allineamento a Sinistra
                    }
                    //Aggiungi dato
                    $logRecord .= $datoStr;
                }
                //Verifica lunghezza totale
                // $logRecordTotalLen = 2154;       
                $logRecordTotalLen = 2151;      //Ticket 202405031303: Le fasi prevedono di avere in input un file con lunghezza 2151
                $logRecord = str_pad($logRecord, $logRecordTotalLen, " ", STR_PAD_RIGHT);
                //Scrivi Record
                // diretto
                fwrite($handle, $logRecord . "\r\n");
                //Output handler
                fclose($handle);
                //return Ok
                return true;
            }
        
        } catch (Exception $e) {
            //Scrivi errore
            $handle = fopen($this->confErrFile, "a+");
            fwrite($handle, date('YmdHis') . " logMULTIPLO(): " . $e->getMessage() . "\r\n");
            fclose($handle);
            //Exception
            throw new Exception('Caught exception: '.$e->getMessage().".");
        }

    }

}