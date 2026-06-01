<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
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


if(empty($_GET['action']))    {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'QRCODE';
$socio = $_GET['socio'];
$NAG = $_GET['cag'];
$oggi = date("d.m.Y H:i:s");
$titolo = '';
// ---------------------------------------

// ---------------------------------------
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // A - Estrazione dei dati anagrafici necessari
        // ---------------------------------------------------------------
        $selectdati = " SELECT  FILIALE_CAPOFILA AS codFil, CONCAT(INTESTAZIONE_A,' ',INTESTAZIONE_B) as Nominativo, 
                                VIA_RES AS indirSpedIndirizzo, CAP_RES AS indirSpedCAP, DESCR_COM_RES AS indirSpedLocalita, PROVINCIA_RES AS indirSpedProvincia, 
                                VALORE_DATO_CNT AS indirizzoPEC
                        FROM sds_soci LEFT JOIN sds_soci_daticontatto
                        ON sds_soci.NAG = sds_soci_daticontatto.NAG
                        WHERE TIPO_DATO_CNT = 'PEC'
                        AND sds_soci.nag = ".$NAG;

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $codFil = '';
            $Nominativo = '';
            $indirSpedIndirizzo = '';
            $indirSpedCAP = '';
            $indirSpedLocalita = '';
            $indirSpedProvincia = '';
            $indirizzoPEC = '';

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                $codFil = $dati['codFil'];
                $Nominativo = $dati['Nominativo'];
                $indirSpedIndirizzo = $dati['indirSpedIndirizzo'];
                $indirSpedCAP = $dati['indirSpedCAP'];
                $indirSpedLocalita = $dati['indirSpedLocalita'];
                $indirSpedProvincia = $dati['indirSpedProvincia'];
                $indirizzoPEC = $dati['indirizzoPEC'];
      
            }
        }

        logquery_modelli ($modello,$NAG,$codFil);        // scrive il LOG del documento prodotto
echo $_GET['action'];

echo '<center style="font-family:courier;">
        <h3>Integra le informazioni necessarie</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Definizione del <b>QRCODE</b> per <br>&nbsp; Nag '.$NAG.' <b>'.$Nominativo.'</b></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" ><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
<br>
                <input type="text" class="form-control form-control-sm" name="fil" id="fil" placeholder="Filiale" size=10><br>
                <input type="text" class="form-control form-control-sm" name="mg" id="mg" placeholder="LN" size=10><br>
                ';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="nag" id="nag" value="'.$NAG.'">
                <input type="hidden" class="form-control" name="socio" id="socio" value="'.$Nominativo.'">

                <input type="hidden" class="form-control" name="codfil" id="codfil" value="'.$codFil.'">
                <input type="hidden" class="form-control" name="Nominativo" id="Nominativo" value="'.$Nominativo.'">
                <input type="hidden" class="form-control" name="indirSpedIndirizzo" id="indirSpedIndirizzo" value="'.$indirSpedIndirizzo.'">
                <input type="hidden" class="form-control" name="indirSpedCAP" id="indirSpedCAP" value="'.$indirSpedCAP.'">
                <input type="hidden" class="form-control" name="indirSpedLocalita" id="indirSpedLocalita" value="'.$indirSpedLocalita.'">
                <input type="hidden" class="form-control" name="indirSpedProvincia" id="indirSpedProvincia" value="'.$indirSpedProvincia.'">
                <input type="hidden" class="form-control" name="indirizzoPEC" id="indirizzoPEC" value="'.$indirizzoPEC.'">
                <br>
                <button type="submit" class="btn btn-primary">Genera QRCODE</button><br>
    </form>
    </center>
';


}
else
{

echo '<table style="font-family:courier;" align="center" width="25%" border="0">
        <tr>
            <td align="center">
                <h3>QRCODE generati per </h3>
                Nag '.$_GET['nag'].' <b>'.$_GET['socio'].'</b>
            </td>
        <tr>';

    //  LEGENDA CODIFICHE QRCODE CHIANTIBANCA
    //      P = PEC
    //      S = RACCOMANDATA
    //      R = RACCOMANDATA A.R.
    //      M = ORDINARIA

    $P = $_GET['fil'].";".$_GET['mg'].";P;".$_GET['Nominativo'].";".$_GET['indirSpedIndirizzo'].';'.$_GET['indirSpedCAP'].' '.$_GET['indirSpedLocalita'].' '.$_GET['indirSpedProvincia'].';'.$_GET['indirizzoPEC'];

    $S = $_GET['fil'].";".$_GET['mg'].";S;".$_GET['Nominativo'].";".$_GET['indirSpedIndirizzo'].';'.$_GET['indirSpedCAP'].' '.$_GET['indirSpedLocalita'].' '.$_GET['indirSpedProvincia'];

    $R = $_GET['fil'].";".$_GET['mg'].";R;".$_GET['Nominativo'].";".$_GET['indirSpedIndirizzo'].';'.$_GET['indirSpedCAP'].' '.$_GET['indirSpedLocalita'].' '.$_GET['indirSpedProvincia'];

    $M = $_GET['fil'].";".$_GET['mg'].";M;".$_GET['Nominativo'].";".$_GET['indirSpedIndirizzo'].';'.$_GET['indirSpedCAP'].' '.$_GET['indirSpedLocalita'].' '.$_GET['indirSpedProvincia'];


    echo '  <tr>
                <td>
                    <br><br>
                    <a style="text-decoration:none;color:#999999" href="https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl='.$P.'" target="_blank">
                       &diams; Link QRCODE per invio PEC
                    </a>
                    <br><br>
                    <a style="text-decoration:none;color:#999999" href="https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl='.$P.'" target="_blank">
                       &diams;  Link QRCODE per invio RACCOMANDATA
                    </a>
                    <br><br>
                    <a style="text-decoration:none;color:#999999" href="https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl='.$P.'" target="_blank">
                       &diams;  Link QRCODE per invio RACCOMANDATA A.R.
                    </a>
                    <br><br>
                    <a style="text-decoration:none;color:#999999" href="https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl='.$P.'" target="_blank">
                      &diams;   Link QRCODE per invio POSTA ORDINARIA
                    </a>
                    <br><br>';


                // ------------------------------
                // COSTRUZIONE DATAMATRIX
                // ------------------------------
                
                $start = 'FORMADOC1.008673380500990BTCH9999';   // aggiunto ABI e CAB generici + terminale BTCH e matricola 9999
                $filoper = '990';                   // CO_FILIALE_OPERANTE
                //$datadoc = date("Ymd");             // DT_DOCUMENTO

                $time = time();
                $datadoc = date('Ymd',$time);
                $NAG = $_GET['nag'];

                // Creazione stringa di codice da inserire nel Datamatrix (127)
                $code   = $start;                                               // Stringa di partenza
                $code  .= $datadoc;                                             // DT_DOCUMENTO
                $code  .= 'SOCICN02  ';                                         // CO_CONTRATTO
                $code  .= 'Vari Documenti      ';                                   // CO_DOCUMENTO
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
                imagegif($image_p, 'dmx/v_'.$NAG.'.gif');
                imagedestroy($im);
                imagedestroy($image_p);                

                // ------------------------------  

echo '
                      &diams;   DataMatrix ISIDOC (TipoDoc SOCI - Vari Documenti)
                      <br>
                    <img src="dmx/v_'.$NAG.'.gif' . '">
                </td>
            </tr>
        </table>            ';

}

?>