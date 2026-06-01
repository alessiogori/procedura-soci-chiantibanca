<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

use \setasign\Fpdi\Fpdi;
require_once('fpdf/fpdf.php');
require_once('setasign/fpdi/autoload.php');

require_once('_functions.php');   //logquery ($selectdati); 
include('PHPBarcode/php-barcode.php');

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

  if ($_COOKIE['filiale_id'] != 999) {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'cicalo')) ) 

{
/*
if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'cicalo')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'cicalo')) ) {
*/

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'SO02';
$socio = $_GET['socio'];
$nag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
// ---------------------------------------

if ($_GET['motivazione'] == 'Parziale') 
    {	$titolo = "CESSIONE PARZIALE AZIONI";
        $titolo2 = "DA SOCIO PERSONA GIURIDICA/SOCIETA REGOLARMENTE COSTITUITA A BANCA";
		$simbolo = "P";
    }
else 
    {	$titolo = "CESSIONE TOTALE AZIONI";
        $titolo2 = "DA SOCIO PERSONA GIURIDICA/SOCIETA REGOLARMENTE COSTITUITA A BANCA";
		$simbolo = "T";
    }
    
$NumAzioni      = $_GET['numazioni'];  
$Calcolo        = $NumAzioni * 30.33;
$ValoreCessione = number_format($Calcolo, 2, ',', '.') ;
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
        $selectdati = "    SELECT s.*, c.*
                            FROM sds_soci as s join sds_soci_certificati as c
                            on s.idsocio = c.idsocio
                            WHERE s.SOCIO_ISTITUTO = 1
                            AND NAG = ".$nag;
        /*             
        $selectdati = " SELECT  prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, nAzTot, nominaleAzTot, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, dataUscita, dataEstinzione, causaleUscita, titoloOnorifico,
                                sesso, telefono, indirizzoEmail, indirizzoPEC, tipoContropVAL, codFil, int1Filiale
                        FROM tab_soci_as37
                        WHERE cag = ".$cag;
        */

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
                                        WHERE filiale = ".$dati['FILIALE_CAPOFILA'];
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
                $DataNascita = data_ita($dati['DATA_NASCITA']);
                $Indirizzo = $dati['VIA_RES'];
                $Cap = $dati['CAP_RES'];
                $Localita = $dati['LOCALITA_RES'];
                $Comune = $dati['DESCR_COM_RES'];
                $Provincia = $dati['PROVINCIA_RES'];
                $Azioni = $dati['NUMERO_AZIONI'];
                $ValoreNominale = number_format($dati['VALORE_AZIONI'],2,',','.');

                if ( $dati['TIPO_SOGGETTO'] == 'PF' )
                    {$datinascita = 'Nato/a il '.$DataNascita;}
                else {$datinascita = '';}

                $CagDelegato = $dati['NAG_RAPPR'];
                $IntestazioneDelegato = $dati['INTESTAZIONE_RAPPR'];
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
                $Filiale = $dati['FILIALE_CAPOFILA'];
                $DescFiliale = $nomefiliale;  
                $co_filiale = '';  
                $co_conto = '';            
            }
        }

        logquery_modelli ($modello,$nag,$Filiale);        // scrive il LOG del documento prodotto
        
        // ------------------------------
        // COSTRUZIONE DATAMATRIX
        // ------------------------------
        
        $start = 'FORMADOC1.008673380500990BTCH9999';   // aggiunto ABI e CAB generici + terminale BTCH e matricola 9999
        $filoper = '990';                   // CO_FILIALE_OPERANTE
        $datadoc = date("Ymd");             // DT_DOCUMENTO
        $id_documento = 'SOCICN02';         // CO_CONTRATTO e CO_DOCUMENTO e CO_RIF_FORMADOC
        $nag = $nag;                        // CO_NAG
        $co_rapporto = '002';               // CO_RAPP
        $co_filiale = $co_filiale;          // CO_FIL
        $co_conto = $co_conto;              // CO_NUM_RAPP
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
        $code  .= 'SOCI_CESSIONE_BANCA ';                               // CO_DOCUMENTO
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
$pageCount = $pdf->setSourceFile('SO02_CessioneAzioni_Banca_PG.pdf');
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
        $pdf->Image('dmx/'.$timestamp.'.gif', 80, 35, 30, 30, "GIF", "");

         // Data odierna
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(15, 176);
        $pdf->Write(0, $luogo);
        $pdf->SetXY(52, 178);
        $pdf->Write(0, $oggi);
        $pdf->SetXY(110, 11.5);
        $pdf->Write(0, $simbolo);  
        // Filiale
        $pdf->SetXY(26, 36);
        $pdf->Write(0, $Filiale. ' '.$DescFiliale);  
        //$pdf->SetXY(26, 40);
        //$pdf->Write(0, $DescFiliale); 
        $pdf->SetFont('Helvetica','I','7');
        // Titolo
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(80, 20);
        $pdf->Write(0, $titolo);
        $pdf->SetXY(16, 26);
        $pdf->Write(0, $titolo2);        
        $pdf->SetFont('Helvetica','B','11');
        // Nome Socio
        $pdf->SetXY(32, 65);
        $pdf->Write(0, $socio);
        $pdf->SetXY(172, 60);
        $pdf->Write(0, 'ID '.$NuMSocio); 
        $pdf->SetXY(172, 65);
        $pdf->Write(0, $nag);
        // Con sede in
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(32, 72);
        $pdf->Write(0, $Comune .' '.$Provincia);    
        // Codice Fiscale
        $pdf->SetXY(120, 72);
        $pdf->Write(0, $CodiceFiscale);    
        // Numero azioni e importo
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(25, 121);
        $pdf->Write(0, $NumAzioni);
        $pdf->SetXY(130, 121);
        $pdf->Write(0, $ValoreCessione);
        /*
        // Dati ultimo aggiornamento
        $pdf->SetXY(24, 266);
        $pdf->Write(0, 'Dati aggiornati al '.$ultimo_aggiornamento);
        */
        // Riferimenti in piè di pagina (portati in alto)
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(150, 270);
        $pdf->Write(0, 'NAG: '.$nag.' - IDSOCIO '.$idsocio);
    }

}

// Output the new PDF
$pdf->Output(); 


}
else
{
    if ($_COOKIE['filiale_id'] != 999) 
    {
        $passwordsoci = '';
    }
    else
    {
        $passwordsoci = 'cicalo';
    }
    
echo '<center style="font-family:courier;">
        <h2>RISERVATO UFFICIO SOCI</h2>
        <h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Definizione della <b>Cessione</b></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

    <form><input type="button" value="CALC" onclick="javascript:window.open(\'http://ostermiller.org/calc/calculator.html\',\'calculator\'+new Date().getTime(),\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=450,height=300\');"></form>
    <br>


                <input type="radio" name="motivazione" value="Totale">TOTALE<br>
                <input type="radio" name="motivazione" value="Parziale">PARZIALE<br>
                <input type="text" name="numazioni" size="9">&nbsp;Numero Azioni da cedere<br>
                <input type="password" name="passwordsoci" size="10" value="'.$passwordsoci.'">&nbsp;Password Soci<br>
                ';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
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


?>