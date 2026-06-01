<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2022)
//
// Generazione frontespizi multipagina
// per archiazione Isidoc delle singole richieste deliberate come Esclusioni
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
$bordof = 152; //192

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

echo '
<style type="text/css">
html,body{
    height:297mm;
    width:210mm;
}

.button {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
}

/* Nascondo il pulsante in stampa */
@media print {
    #printbtn {
        display :  none;
    }
}

</style>

<body">';


if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'cicalo')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'cicalo')) ) {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'ETIK';
$oggi = date("d.m.Y");
// ---------------------------------------

// ---------------------------------------
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione dei dati necessari alla produzione delle etichette di Datamatrix
        // SOCI ESCLUSI
        $selectdati = "    
            SELECT *
            FROM tab_xls_esclusioni
            WHERE (Escluso_art_14 = 'S' OR Escluso_art_6 = 'S')
            AND CDA = '".$_GET['cda']."'
            ORDER BY Nominativo";  

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $NAG = '';
            $CDA = '';
            $Nominativo = '';
            $co_filiale = '';  
            $co_conto = '';  

            }
            else
            {

            $contenutoOutput = '';
            $myfileesclusioni = fopen("dmx/esclusioni_dmx.csv", "w");
            $contenutoOutput .= "NAG;Nominativo;Immagine\n";

            //echo '<button onclick="window.print()">Stampa queste pagine</button>';
            echo '<input id ="printbtn" type="button" value="Stampa queste pagine" onclick="window.print();" >';

            while($dati=mysqli_fetch_array($querydati)){ 

                $NAG = $dati['NAG'];
                $CDA = $dati['CDA'];
                $Nominativo = $dati['Nominativo'];                
                $DataRichiesta = substr($dati['Data_Richiesta'],6,4).substr($dati['Data_Richiesta'],3,2).substr($dati['Data_Richiesta'],0,2);
                $co_filiale = '';  
                $co_conto = '';  
              
                // ------------------------------
                // COSTRUZIONE DATAMATRIX
                // ------------------------------
                
                $datadoc = date("Ymd");             // DT_DOCUMENTO
                $start = 'FORMADOC1.008673380500990BTCH9999';   // aggiunto ABI e CAB generici + terminale BTCH e matricola 9999
                $filoper = '990';                   // CO_FILIALE_OPERANTE

                $time = strtotime($CDA);
                $datadoc = date('Ymd',$time);

                $id_documento = 'SOCICN02';         // CO_CONTRATTO e CO_DOCUMENTO e CO_RIF_FORMADOC
                $nag = $NAG;                        // CO_NAG
                $co_rapporto = '002';               // CO_RAPP
                $co_filiale = $co_filiale;          // CO_FIL
                $co_conto = $co_conto;              // CO_NUM_RAPP
                $co_nu_contratto = '';              // CO_NU_CONTRATTO
                $co_num_doc = '01';                 // NU_DOCUMENTI_STAMPATI
                $co_pratica = '';                   // ID_UNIVOCO
                //$nome = $dati['INTESTAZIONE'];  
                //$note = 'Test datamatrix';

                // Creazione stringa di codice da inserire nel Datamatrix (127)
                $code   = $start;                                               // Stringa di partenza
                $code  .= $DataRichiesta;                                             // DT_DOCUMENTO
                $code  .= 'SOCICN02  ';                                         // CO_CONTRATTO
                $code  .= 'SOCI_ESCLUSIONE     ';                               // CO_DOCUMENTO 20 char
                //$code  .= 'SOCI_CESSIONE_BANCA ';                             // CO_DOCUMENTO 20 char
                $code  .= substr(str_repeat("0", 8).$NAG, -8, 8);               // CO_NAG
                $code  .= '                                          ';         // CO_NU_CONTRATTO
                $code  .= '                      ';                             // ID_UNIVOCO
                $code  .= 'BTCH';                                               // Matricola

                //Esempio: FORMADOC1.008673380500990BTCH999920220713SOCICN02  VARI                01056561                                                                BTCH

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
                imagegif($image_p, 'dmx/e_'.$NAG.'.gif');
                imagedestroy($im);
                imagedestroy($image_p);                

                // ------------------------------      

                echo '<table align="center">
                      <tr>
                        <td align="center" style="size:14px;font-family:verdana;"><b>UFFICIO SOCI - ESCLUSIONE CDA ' . $CDA . '</b></td>
                      </tr>
                      <tr>
                        <td style="size:12px;font-family:verdana;">' . $NAG . ' - ' .  $Nominativo . '</td>
                      </tr>
                      <tr>
                        <td align="center">' . '<img src="dmx/e_'.$NAG.'.gif' . '" height="300"></td>
                      </tr>
                      <tr>
                        <td style="size:12px;font-family:verdana;">' . $NAG . ' - ' .  $Nominativo . '</td>
                      </tr>
                      <tr>
                        <td style="size:12px;font-family:verdana;">
                            Note  : ' . $dati['Note_Motivazioni'] . '<br>
                            Art. 6: ' . $dati['Escluso_art_6'] . '<br>
                            Art.14: ' . $dati['Escluso_art_14'] . '<br>
                            Soff. : ' . $dati['Escluso_x_Passaggio_a_Sofferenze'] . '<br>
                        </td>
                      </tr>
                      </table>'  ;

                echo '<P style="page-break-before: always">';

                $contenutoOutput .= $NAG.";".
                                    $Nominativo.";".
                                    "'http://10.197.139.22:8080/soci/modulistica/dmx/".$NAG.".gif'\n";

            // fine WHILE
            }
        }


    fwrite($myfileesclusioni, $contenutoOutput);
    fclose($myfileesclusioni);
}
else
{

echo '<center style="font-family:courier;">
        <h2>RISERVATO UFFICIO SOCI</h2>
        <h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Creazione Frontespizi Datamatrix <b>Esclusioni</b></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <input type="text" name="cda" size="12">&nbsp;Data CDA (gg/mm/aaaa)<br>
                <input type="password" name="passwordsoci" size="10">&nbsp;Password Soci<br>
                ';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <br>
                <button type="submit" class="btn btn-primary">Stampa modello</button><br>
    </form>
    </center>
';
}


?>