<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2022)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

use \setasign\Fpdi\Fpdi;
require_once('fpdf/fpdf.php');
require_once('setasign/fpdi/autoload.php');

require_once('_functions.php');   //logquery ($selectdati); 
include('PHPBarcode/php-barcode.php');

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// ---------------------------------------
// Settings del Datamatrix
// (impostazione generale)
// ---------------------------------------
$x      = 960;  // barcode center
$y      = 960;  // barcode center
$height = 30;   // barcode height in 1D ; module size in 2D
$width  = 30;   // barcode height in 1D ; not use in 2D
$angle  = 0;    // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
$type   = 'datamatrix';
$rect   = false;
$bordo  = 1920;
$bordof = 192;

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'SO52';
//$socio = $_GET['socio'];
$nag = $_GET['nag'];            // Nuovo Socio
//$idsocio = $_GET['idsocio'];
$oggi = date("d.m.Y");
// ---------------------------------------

// Azioni 
$NumAzioni      = $_GET['numaz'];  
$FilCC          = $_GET['filcc'];  
$Calcolo        = $NumAzioni * 30.33;
$Valore         = number_format($Calcolo, 2, ',', '.') ;

// ---------------------------------------
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione della data ultimo caricamento
        $select_last = " SELECT  caricamento
                        FROM tab_ultimo_caricamento
                        WHERE fonte = 'sds_soci'
                        ";

        $query_last = mysqli_query($connection, $select_last); 
            while($dati_last=mysqli_fetch_array($query_last)){ 
                $ultimo_aggiornamento = $dati_last['caricamento'];
            }

        // Estrazione dei dati anagrafici necessari
        // select SADAS
        $selectdati = "     
                            SELECT
                                 ANAG_NAG.NAG AS NAG ,
                                 ANAG_NAG.CODICE_FISCALE AS CODICE_FISCALE ,
                                 rtrim(ANAG_NAG.INTESTAZIONE_A) + ' ' + rtrim(ANAG_NAG.INTESTAZIONE_B) AS NOMINATIVO ,
                                 ANAG_NAG.FILIALE_CAPOFILA AS FILIALE_CAPOFILA ,
                                 rtrim(ANAG_NAG.VIA_RES) AS VIA_RES ,
                                 ANAG_NAG.CAP_RES AS CAP_RES ,
                                 rtrim(ANAG_NAG.DESCR_COM_RES) AS DESCR_COM_RES ,
                                 ANAG_NAG.PROVINCIA_RES AS PROVINCIA_RES ,
                                 ANAG_PERSONE_FISICHE.DATA_NASCITA AS DATA_NASCITA ,
                                 rtrim(ANAG_PERSONE_FISICHE.DESCR_COM_NASC) AS DESCR_COM_NASC ,
                                 ANAG_PERSONE_FISICHE.PROVINCIA_NASC AS PROVINCIA_NASC ,
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.NOME_FILIALE) AS NOME_FILIALE ,
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.LOCALITA) AS LUOGO_FILIALE , 
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.INDIRIZZO) AS INDIRIZZO_FILIALE ,
                                 TAB_ANAGRAFICA_SPORTELLI.CAP AS CAP_FILIALE,
                                 TAB_ANAGRAFICA_SPORTELLI.PROVINCIA as PROV_FILIALE
                            FROM
                                ANAG_NAG LEFT OUTER JOIN ANAG_PERSONE_FISICHE ON (ANAG_NAG.NAG = ANAG_PERSONE_FISICHE.NAG ) ,
                                ANAG_NAG LEFT OUTER JOIN TAB_ANAGRAFICA_SPORTELLI ON (ANAG_NAG.FILIALE_CAPOFILA = TAB_ANAGRAFICA_SPORTELLI.FILIALE )  
                            WHERE
                                ANAG_NAG.TIPO_NAG =  'PF'  
                            AND
                                ANAG_NAG.STATO_NAG in ('0','1','2')
                            AND
                                ANAG_NAG.NAG = ".$nag."
                            ORDER BY
                                ANAG_NAG.NAG ASC
                            UNION
                            SELECT
                                 ANAG_NAG.NAG AS NAG ,
                                 ANAG_NAG.CODICE_FISCALE AS CODICE_FISCALE ,
                                 rtrim(ANAG_NAG.INTESTAZIONE_A) + ' ' + rtrim(ANAG_NAG.INTESTAZIONE_B) AS NOMINATIVO ,
                                 ANAG_NAG.FILIALE_CAPOFILA AS FILIALE_CAPOFILA ,
                                 rtrim(ANAG_NAG.VIA_RES) AS VIA_RES ,
                                 ANAG_NAG.CAP_RES AS CAP_RES ,
                                 rtrim(ANAG_NAG.DESCR_COM_RES) AS DESCR_COM_RES ,
                                 ANAG_NAG.PROVINCIA_RES AS PROVINCIA_RES ,
                                 '' AS DATA_NASCITA ,
                                 '' AS DESCR_COM_NASC ,
                                 '' AS PROVINCIA_NASC ,
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.NOME_FILIALE) AS NOME_FILIALE ,
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.LOCALITA) AS LUOGO_FILIALE , 
                                 rtrim(TAB_ANAGRAFICA_SPORTELLI.INDIRIZZO) AS INDIRIZZO_FILIALE ,
                                 TAB_ANAGRAFICA_SPORTELLI.CAP AS CAP_FILIALE,
                                 TAB_ANAGRAFICA_SPORTELLI.PROVINCIA as PROV_FILIALE
                            FROM
                                ANAG_NAG LEFT OUTER JOIN ANAG_PERSONE_GIURIDICHE ON (ANAG_NAG.NAG = ANAG_PERSONE_GIURIDICHE.NAG ) ,
                                ANAG_NAG LEFT OUTER JOIN TAB_ANAGRAFICA_SPORTELLI ON (ANAG_NAG.FILIALE_CAPOFILA = TAB_ANAGRAFICA_SPORTELLI.FILIALE )  
                            WHERE
                                ANAG_NAG.TIPO_NAG !=  'PF'  
                            AND
                                ANAG_NAG.STATO_NAG =  '1'  
                            AND
                                ANAG_NAG.NAG = ".$nag."
                            ORDER BY
                                ANAG_NAG.NAG ASC                                
                        ";
        //echo $selectdati;
        $querydati = odbc_exec($connect, $selectdati);

            while($dati = odbc_fetch_object($querydati)) {

                $NAG            = $dati->NAG;
                $CODICE_FISCALE = $dati->CODICE_FISCALE;
                $NOMINATIVO     = $dati->NOMINATIVO;
                $VIA_RES        = $dati->VIA_RES;
                $CAP_RES        = $dati->CAP_RES;
                $DESCR_COM_RES  = $dati->DESCR_COM_RES;
                $PROVINCIA_RES  = $dati->PROVINCIA_RES;
                $DESCR_COM_NASC = $dati->DESCR_COM_NASC;
                $PROVINCIA_NASC = $dati->PROVINCIA_NASC;
                $DATA_NASCITA   = $dati->DATA_NASCITA;
                $FILIALE        = $dati->FILIALE_CAPOFILA;
                $NOME_FILIALE   = $dati->NOME_FILIALE;
                $LUOGO_FILIALE  = $dati->LUOGO_FILIALE;
                $INDIRIZZO_FILIALE = $dati->INDIRIZZO_FILIALE;
                $CAP_FILIALE    = $dati->CAP_FILIALE;
                $PROV_FILIALE   = $dati->PROV_FILIALE;
          
            }

        logquery_modelli ($modello,$nag,$FILIALE);        // scrive il LOG del documento prodotto

        // ------------------------------
        // COSTRUZIONE DATAMATRIX
        // ------------------------------
        
        $start = 'FORMADOC1.008673380500990BTCH9999';   // aggiunto ABI e CAB generici + terminale BTCH e matricola 9999
        $filoper = '990';                   // CO_FILIALE_OPERANTE
        $datadoc = date("Ymd");             // DT_DOCUMENTO
        $id_documento = 'SOCICN02';         // CO_CONTRATTO e CO_DOCUMENTO e CO_RIF_FORMADOC
        $nag = $NAG;                        // CO_NAG
        $co_rapporto = '002';               // CO_RAPP
        //$co_filiale = $co_filiale;          // CO_FIL
        //$co_conto = $co_conto;              // CO_NUM_RAPP
        $co_nu_contratto = '';              // CO_NU_CONTRATTO
        $co_num_doc = '01';                 // NU_DOCUMENTI_STAMPATI
        $co_pratica = '';                   // ID_UNIVOCO
        //$nome = $dati['INTESTAZIONE'];  
        //$note = 'Test datamatrix';

        // Creazione stringa di codice da inserire nel Datamatrix (127)
        $code   = $start;
        $code  .= $datadoc;
        $code  .= 'SOCICN02  ';
        //$code  .= 'VARI                ';
        $code  .= 'SOCICN02            ';
        $code  .= substr(str_repeat("0", 8).$nag, -8, 8);
        $code  .= '                                          ';         // ??
        $code  .= '                      ';                             // ID_UNIVOCO
        $code  .= 'BTCH';                                               // Matricola

        // Creazione datamatrix
        $im     = imagecreatetruecolor($bordo, $bordo);
        
        $black  = ImageColorAllocate($im,0x00,0x00,0x00);
        $white  = ImageColorAllocate($im,0xff,0xff,0xff);
        $red    = ImageColorAllocate($im,0xff,0x00,0x00);
        $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
        imagefilledrectangle($im, 0, 0, $bordo, $bordo, $white);
        
        $data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code, 'rect'=>$rect), $width, $height);
        
        $image_p = imagecreatetruecolor($bordof, $bordof);
        imagetruecolortopalette($image_p, false, 2); 
        imagecolorset($image_p, 0, 0, 0, 0);
        imagecolorset($image_p, 1, 255, 255, 255);
        imagecopyresampled($image_p, $im, 0, 0, 0, 0, $bordof, $bordof, $bordo, $bordo);
        
        $timestamp = time();          // uso timestamp per nominare il file
        imagegif($image_p, 'dmx/'.$timestamp.'.gif');
        imagedestroy($im);
        imagedestroy($image_p);                

        // ------------------------------

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('SO52_addendum_ulterioriquote.pdf');
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // Scrivo solo nella prima pagina
    if($pageNo == 1){ 
        $pdf->SetFont('Helvetica','B','11');
        $pdf->SetTextColor(0,0,0);                  //nero
        //$pdf->SetTextColor(0,48,119);             //blu

        // Prendo l'immagine del Datamatrix nella cartella /DMX
        $pdf->Image('dmx/'.$timestamp.'.gif', 160, 40, 30, 30, "GIF", "");

        // Addendum per Socio
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(28, 40.5);
        $pdf->Write(0, substr($NOMINATIVO,0,40).' (nag '.$NAG.')');
        $pdf->SetFont('Helvetica','B','11');
        // Giorno e luogo
        $pdf->SetFont('Helvetica','','10');
        //$pdf->SetXY(88, 71);
        //$pdf->Write(0, $LUOGO);
        $pdf->SetXY(142, 66.5);
        $pdf->Write(0, $oggi);
        // Nome Nuovo Socio
        $pdf->SetFont('Helvetica','B','10');
        $pdf->SetXY(19, 78);
        $pdf->Write(0, $NOMINATIVO. ' (nag '.$NAG.'),');
        $pdf->SetFont('Helvetica','','9');
        $pdf->SetXY(19, 83);
        $pdf->Write(0, 'nato a '.$DESCR_COM_NASC.' ('.$PROVINCIA_NASC.') il '.$DATA_NASCITA.',');
        $pdf->SetXY(19, 88);
        $pdf->Write(0, 'residente in '.$DESCR_COM_RES.' ('.$PROVINCIA_RES.'), '.$VIA_RES.', C.F. '.$CODICE_FISCALE.',');
        // Numero azioni e importo
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(85, 118);
        $pdf->Write(0, $NumAzioni);
        $pdf->SetXY(32, 123);
        $pdf->Write(0, $Valore);
        // Filiale e Conto
        $pdf->SetFont('Helvetica','','10');
        $pdf->SetXY(110, 148);
        $pdf->Write(0, $FilCC);
        $pdf->SetXY(59, 153);
        $pdf->Write(0, $NOME_FILIALE);
        // Filiale piè di pagina        
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(19, 270);
        $pdf->Write(0, 'Filiale '.$FILIALE.' '.$NOME_FILIALE); 

    }

}

// Output the new PDF
$pdf->Output(); 

// Close ODBC
odbc_close($connect);

?>