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
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

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

//if(isset($_GET['action']))    {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'DMX';
$socio = $_GET['socio'];
$nag = $_GET['nag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
// ---------------------------------------

    include("../config/_config.php");
    $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

    // Estrazione dei dati dell'archivio SOCI
    $selectdati = "     SELECT *, (NUM_AZIONI * 30.33) as VAL_AZIONI
                        FROM sds_sinergiareport_soci
                        WHERE NAG = ".$nag;

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $NuMSocio = '';
            $CodiceFiscale = '';
            //$LuogoNascita = '';
            //$ProvinciaNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $Comune = '';
            $Provincia = '';
            $Azioni = '';
            $ValoreNominale = '';
            $CagDelegato = '';
            $IntestazioneDelegato = '';
            //$DataAmmissione = '';
            $dataEntrata = '';
            $dataUscita = '';
            //$dataEstinzione = '';
            //$CausaleUscita = '';
            //$TitoloOnorifico = '';
            //$Sesso = '';
            $Telefono = '';
            $Mail = '';
            //$PEC = '';
            $Filiale = '';
            $DescFiliale = '';
            $co_filiale = '';  
            $co_conto = '';   
            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 


                // ********************************************************
                // RICERCA DEL NOME DELLA FILIALE
                // ********************************************************
                $select_filiale   = "   SELECT * FROM tab_psw
                                        WHERE filiale = ".$dati['FILIALE_ANAGRAFICA'];
                //logquery ($select_filiale);  
                $querydati_filiale = mysqli_query($connection, $select_filiale);
                    if(mysqli_num_rows($querydati_filiale) > 0)
                        while($datifiliale = mysqli_fetch_array($querydati_filiale))
                        {
                            $nomefiliale = $datifiliale['desc_filiale'];
                        }
                    else
                    {
                        $nomefiliale = '';
                    }


                $NuMSocio = $dati['IDSOCIO'];
                $CodiceFiscale = $dati['CODICE_FISCALE'];
                //$LuogoNascita = $dati['LuogoNascita'];
                $DataNascita = $dati['DATA_DI_NASCITA'];
                $Indirizzo = $dati['VIA_RES'];
                $Cap = $dati['CAP_RES'];
                $Localita = $dati['LOCALITA_RES'];
                $Comune = $dati['DESCR_COM_RES'];
                $Provincia = $dati['PROVINCIA_RES'];
                $Azioni = $dati['NUM_AZIONI'];
                $ValoreNominale = number_format($dati['VAL_AZIONI'],2,',','.');

                if ( $dati['TIPO_SOGGETTO'] == 'PF' )
                    {$datinascita = 'Nato/a il '.$DataNascita;}
                else {$datinascita = '';}

                $CagDelegato = $dati['NAG_RAPP'];
                $IntestazioneDelegato = $dati['RAPPRESENTANTE'];
                //$DataAmmissione = $dati['dataAmmiss'];
                $dataEntrata = $dati['DATA_ENTRATA'];
                $dataUscita = $dati['DATA_USCITA'];
                //$dataEstinzione = $dati['dataEstinzione'];
                //$CausaleUscita = $dati['causaleUscita'];
                //$TitoloOnorifico = $dati['titoloOnorifico'];
                //$Sesso = $dati['sesso'];
                $Telefono = $dati['CELL'];
                $Mail = $dati['MAIL'];
                //$PEC = $dati['indirizzoPEC'];
                $Filiale = $dati['FILIALE_ANAGRAFICA'];
                $DescFiliale = $nomefiliale;    
                $co_filiale = $dati['FILIALE_CC'];  
                $co_conto = $dati['NUM_RAPP_CC'];   
    
            }
        }

				// ----------------------------------------------
				// Variabili STRUTTURA FORMADOC
				// ----------------------------------------------
                /*
                COLNAME                 FROM    TO FORMAT  DEFAULT      LUNGHEZZA
                CO_FILIALE_OPERANTE     22      25                          3
                DT_DOCUMENTO            34      41                          7          OBBLIGATORIO
                CO_CONTRATTO            42      51                          9          OBBLIGATORIO
                CO_DOCUMENTO            52      71                          19
                CO_NAG                  72      79                          7          OBBLIGATORIO
                CO_RAPP                 80      83                          3
                CO_FIL                  84      87                          3
                CO_NUM_RAPP             88      99                          11
                CO_NU_CONTRATTO         100     121                         21
                NU_DOCUMENTI_STAMPATI   122     124                         2
                ID_UNIVOCO              125     143 TRIM    $CKSUM_BARCODE  18
                CO_RIF_FORMADOC         148     157 TRIMTONULL              9
                */


                $start = 'CBC1';                    //  
                $nag = $nag;                        // CO_NAG
                $co_rapporto = '002';               // CO_RAPP forzato per conti correnti
                $co_filiale = $co_filiale;          // CO_FIL
                $co_conto = $co_conto;              // CO_NUM_RAPP
                $co_contratto = 'SOCICN02';         // CO_CONTRATTO e CO_DOCUMENTO e CO_RIF_FORMADOC
                $co_nu_contratto = '';              // CO_NU_CONTRATTO
                $filoper = '990';                   // CO_FILIALE_OPERANTE
                $datadoc = date("Ymd");             // DT_DOCUMENTO
                $dataacq = '         ';             // DT_ACQUISIZIONE la mette da solo
                $note = 'Test datamatrix';

                /*
				$co_num_doc = '01';                 // NU_DOCUMENTI_STAMPATI
				$co_pratica = '';                   // ID_UNIVOCO
                $nome = $dati['INTESTAZIONE'];  
                */

				// Creazione stringa di codice da inserire nel Datamatrix (127)
				$code   = $start;                                               // CBC1
                $code  .= substr(str_repeat("0", 8).$nag, -8, 8);               // NAGxxxxx
                $code  .= $co_rapporto;                                         // CRx
                $code  .= substr(str_repeat("0", 3).$co_filiale, -3, 3);        // FIL
                $code  .= substr(str_repeat("0", 6).$co_conto, -6, 6);          // RAPPxx
                $code  .= 'SOCICN02  ';                                         // CONTRATTOx
                $code  .= 'SOCICN02  ';                                         // RIFxxxxxxx
                $code  .= substr(str_repeat(" ", 20).$co_contratto, -20, 20);   // DOCUMxxxxxxxxxxxxxxx
                $code  .= substr(str_repeat(" ", 21).$co_nu_contratto, -22, 22);// NUCONTRATTxxxxxxxxxxxx
                $code  .= substr(str_repeat("0", 3).$filoper, -3, 3);           // FOx
                $code  .= $datadoc;                                             // DATADOCxxx
                $code  .= $dataacq;                                             // DTACQxxxxx
                $code  .= substr(str_repeat(" ", 90).$note, -90, 90);           // NOTExxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx


                /*
                $code  .= 'SOCICN02  ';
                $code  .= substr(str_repeat(" ", 1).$id_documento, -1, 1);
                $code  .= substr(str_repeat("0", 19).$id_documento, -19, 19);
                $code  .= $co_rapporto;   
                $code  .= substr(str_repeat(" ", 3).$co_filiale, -3, 3);    
                $code  .= substr(str_repeat(" ", 11).$co_conto, -11, 11);   
                $code  .= substr(str_repeat("0", 21).$co_num_doc, -21, 21);   
                $code  .= $co_num_doc;   
                $code  .= substr(str_repeat(" ", 18).$co_pratica, -18, 18);   
                $code  .= substr(str_repeat("0", 9).$id_documento, -9, 9);
                */

                /*  ----- ORIGINALE ex PISTOIA -----
                $code   = $start;
                $code  .= str_repeat($filoper, 2);
                $code  .= substr(str_repeat("0", 8).$nag, -8, 8);
                $code  .= substr(str_repeat("0", 6).$id_documento, -6, 6);
                $code  .= substr($nome.str_repeat(" ", 50), 0, 50);
                $code  .= substr(str_repeat(" ", 4).$co_rapporto, -4, 4);   // Messo valore vuoto in caso di assenza della variabile, altrimenti Isidoc da errore
                $code  .= substr(str_repeat(" ", 3).$co_filiale, -3, 3);    // idem c.s.
                $code  .= substr(str_repeat(" ", 10).$co_conto, -10, 10);   // idem c.s.
                $code  .= substr(str_repeat(" ", 5).$co_num_socio, -5, 5);  // idem c.s.
                $code  .= substr($co_pratica.str_repeat(" ", 16), 0, 16);
                $code  .= substr($note.str_repeat(" ", 137), 0, 137);
                */

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

			

//        logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('_testdmx.pdf');   // test
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // -- COMPOSIZIONE DATI NEL DOCUMENTO --

    if($pageNo == 1){ 

	  	$pdf->SetFont('Helvetica','B','9');
        $pdf->SetTextColor(0,0,0);                  //nero
		//$pdf->SetTextColor(0,48,119); 			//blu

        // Prendo l'immagine del Datamatrix nella cartella /DMX
        $pdf->Image('dmx/'.$timestamp.'.gif', 150, 50, 30, 30, "GIF", "");
        //$pdf->Image('dmx/1645083989.gif', 20, 50, 30, 30, "GIF", "");

        $pdf->SetFont('Helvetica','','9');
        $pdf->SetTextColor(0,0,0);                  

        // Nome Banca
        $pdf->SetXY(54, 48);
        $pdf->Write(0, "CHIANTIBANCA CRED.COOP.S.C.");

        // Codice Prodotto
        $pdf->SetXY(40, 100);
        $pdf->Write(0, $code);

        // Circuito Mastercard
        $pdf->SetXY(116, 86);
        $pdf->Write(0, "X");

        // Circuito Bancomat/PB
        $pdf->SetXY(166, 92);
        $pdf->Write(0, "X");

        // Sesso
        $pdf->SetXY(84, 117);
        // $pdf->Write(0, $Sesso);

        // Natura Rapporto
        $pdf->SetXY(131, 117);
        // $pdf->Write(0, $NaturaRapporto);

        // Scopo Rapporto
        $pdf->SetXY(178, 117);
        // $pdf->Write(0, $ScopoRapporto);

        // Cognome
        $pdf->SetFont('Helvetica','b','11');
        $pdf->SetXY(42, 124);
        $pdf->Write(0, $CodiceFiscale);

        // Nome
        $pdf->SetFont('Helvetica','b','11');
        $pdf->SetXY(118, 124);
        //$pdf->Write(0, $Nome);

        // Indirizzo
        $pdf->SetFont('Helvetica','','9');
        $pdf->SetXY(42, 132);
        // $pdf->Write(0, $Indirizzo);

        // CAP
        $pdf->SetXY(142, 132);
        // $pdf->Write(0, $CAP);

        // ComuneLocalita
        $pdf->SetXY(57, 140);
        // $pdf->Write(0, $ComuneLocalita);

        // Provincia
        $pdf->SetXY(156, 140);
        // $pdf->Write(0, $Provincia);

        // Nazione
        $pdf->SetXY(186, 140);
        // $pdf->Write(0, $Nazione);

        // DataNascita
        $pdf->SetXY(50, 148);
        // $pdf->Write(0, $DataNascita);

        // ComuneNascita
        $pdf->SetXY(108, 148);
        // $pdf->Write(0, $ComuneNascita);

        // ProvinciaNascita
        $pdf->SetXY(44, 156);
        // $pdf->Write(0, $ProvinciaNascita);

        // NazioneNascita
        $pdf->SetXY(71, 156);
        // $pdf->Write(0, $NazioneNascita);

        // StatoCivile
        $pdf->SetXY(114, 156);
        // $pdf->Write(0, $StatoCivile);

        // CodiceFiscale
        $pdf->SetXY(143, 156);
        // $pdf->Write(0, $CodiceFiscale);

        // TelAbitazione
        $pdf->SetXY(57, 164);
        // $pdf->Write(0, $TelAbitazione);

        // TelCellulare
        $pdf->SetXY(143, 164);
        // $pdf->Write(0, $TelCellulare);

        // TipoDocumento
        $pdf->SetXY(55, 172);
        // $pdf->Write(0, $TipoDocumento);

        // NumeroDocumento
        $pdf->SetXY(94, 172);
        // $pdf->Write(0, $NumeroDocumento);

        // DataRilascio
        $pdf->SetXY(163, 172);
        // $pdf->Write(0, $DataRilascio);

        // EnteRilascio
        $pdf->SetXY(58, 179);
        // $pdf->Write(0, $EnteRilascio);

        // DataScadenza
        $pdf->SetXY(91, 179);
        // $pdf->Write(0, $DataScadenza);

        // LuogoRilascio
        $pdf->SetXY(57, 187);
        // $pdf->Write(0, $LuogoRilascio);

        // ProvinciaRilascio
        $pdf->SetXY(148, 187);
        // $pdf->Write(0, $ProvinciaRilascio);

        // NazioneRilascio
        $pdf->SetXY(180, 187);
        // $pdf->Write(0, $NazioneRilascio);

        // CodiceQualifica
        $pdf->SetXY(64, 195);
        // $pdf->Write(0, $CodiceQualifica);

        // CodiceSettore
        $pdf->SetXY(110, 195);
        // $pdf->Write(0, $CodiceSettore);

        // TipoAttivita
        $pdf->SetXY(154, 195);
        // $pdf->Write(0, $TipoAttivita);

        // TitoloStudio
        $pdf->SetXY(64, 203);
        // $pdf->Write(0, $TitoloStudio);

        // Email
        $pdf->SetXY(38, 211);
        // $pdf->Write(0, $Email);

        // Data odierna
        $pdf->SetFont('Helvetica','','9');
        $pdf->SetXY(26, 240);
        // $pdf->Write(0, $oggi);
       
    }



// Fine FPDF
}

// Output the new PDF
$pdf->Output(); 

// Cancello il DMX GIF creato
unlink('dmx/'.$timestamp.'.gif');

/*
}
else
{


echo '<center><h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                Motivazioni della richiesta di recesso:<br>
                <textarea name="motivazione" cols="60" rows="5" required></textarea><br>';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="tessera" id="tessera" value="'.$_GET['tessera'].'">
                <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
                <input type="hidden" class="form-control" name="socio" id="socio" value="'.$_GET['socio'].'">
                <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$_GET['idsocio'].'">
                <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">


                <br>
                <button type="submit" class="btn btn-primary">Stampa modello</button><br>
    </form>
    </center>
';
}
*/

?>