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
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'cicalo')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'cicalo')) ) {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$filiale = $_GET['filiale'];
$cag = $_GET['cag'];
$nominativo = $_GET['nominativo'];
$oggi = date("d.m.Y");
// ---------------------------------------

$codicebuono01 = $_GET['codicebuono01']   ;
$codicebuono02 = $_GET['codicebuono02']   ;
$codicebuono03 = $_GET['codicebuono03']   ;
$codicebuono04 = $_GET['codicebuono04']   ;
// ---------------------------------------
include("../config/_config.php");
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('VE01_invio_buono_ventis.pdf');
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
        $pdf->SetTextColor(0,0,0);                  // nero
        //$pdf->SetTextColor(0,48,119);             // blu
        // Data odierna
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(10, 5);
        $pdf->Write(0, 'Monteriggioni, '.$oggi);
        // Filiale
        $pdf->SetXY(180, 5);
        $pdf->Write(0, $filiale);  
        // Buoni
        $pdf->SetFont('Courier','B','16');
        $pdf->SetTextColor(255,255,255);            // bianco
        $pdf->SetXY(20, 413.5);
        $pdf->Write(0, $codicebuono01);
        $pdf->SetXY(76, 413.5);
        $pdf->Write(0, $codicebuono02);        
        $pdf->SetXY(130,413.5);
        $pdf->Write(0, $codicebuono03);        
        $pdf->SetXY(186,413.5);
        $pdf->Write(0, $codicebuono04);        
        // Nome Socio
        $pdf->SetFont('Helvetica','B','11');
        $pdf->SetXY(12, 425);
        $pdf->Write(0, "Socio ". $nominativo . " (".$cag.")");

    }

}

// Output the new PDF
$pdf->Output(); 


}
else
{

echo '<center style="font-family:courier;">
        <h2>RISERVATO DIREZIONE COMUNICATA\' E TERRITORI</h2>
        <h3>Integra le informazioni necessarie per completare il modulo</h3>
        '.$_GET['nominativo'].' ('.$_GET['cag'].')<br><br>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Inserimento dei codici buono Ventis</legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <input type="text" name="codicebuono01" size="9">&nbsp;Codice Buono nr.01<br>
                <input type="text" name="codicebuono02" size="9">&nbsp;Codice Buono nr.02<br>
                <input type="text" name="codicebuono03" size="9">&nbsp;Codice Buono nr.03<br>
                <input type="text" name="codicebuono04" size="9">&nbsp;Codice Buono nr.04<br>
                <input type="password" name="passwordsoci" size="10">&nbsp;Password Soci<br>
                ';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="filiale" id="filiale" value="'.$_GET['filiale'].'">
                <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
                <input type="hidden" class="form-control" name="nominativo" id="nominativo" value="'.$_GET['nominativo'].'">


                <br>
                <button type="submit" class="btn btn-primary">Genera PDF del buono per '.$_GET['nominativo'].'</button><br>
    </form>
    </center>
';
}


?>