<?php
// *****************************************************************************
// Portale ChiantiMutua
// Sviluppo e realizzazione: Alessio Fedi (2019)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

use \setasign\Fpdi\Fpdi;
require_once('fpdf/fpdf.php');
require_once('setasign/fpdi/autoload.php');
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE MUTUA
// ---------------------------------------
$socio = $_GET['socio'];
$tessera = $_GET['tessera'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
$tessera1 = $tessera ;
$tessera2 = $tessera ;

$motivazione = 'Tessera in arrivo';

if ($motivazione == 'Tessera in arrivo') 
    {	$motivazione1 = "X";
		$motivazione2 = "";
		$motivazione3 = "";
		$tessera1 = "";
		$tessera2 = "";
    }
/*
        $pdf->SetXY(21, 132);
        $pdf->Write(0, $motivazione1);
        $pdf->SetXY(21, 142);
        $pdf->Write(0, $motivazione2);
        $pdf->SetXY(21, 147);
        $pdf->Write(0, $motivazione3);
*/
// ---------------------------------------
        // Estrazione dei dati anagrafici necessari
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        $selectdata = " SELECT codiceFiscale, date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') as dataNascita,
                        date_format(STR_TO_DATE(socioDal, '%Y-%m-%d'),'%d/%m/%Y') as socioDal
                        FROM tab_mutua_elencosoci
                        WHERE cag = ".$cag."
                        ";

        $querydata = mysqli_query($connection, $selectdata); 

        while($datidata=mysqli_fetch_array($querydata)){ 
            $DataAmmissione = $datidata['socioDal'];
            $codiceFiscale = $datidata['codiceFiscale'];
        }

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('901_lettera_sostitutiva.pdf');
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // Scrivo solo nella prima pagina
    if($pageNo == 1){ 
	  	$pdf->SetFont('Helvetica','B','12');
        // Luogo e data
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(70, 40);
      	$pdf->Write(0, $oggi);
      	// Nome Socio
	  	$pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(117, 51);
      	$pdf->Write(0, $socio);
        $pdf->SetTextColor(0,48,119);           //blu
        $pdf->SetXY(100, 103.5);
      	$pdf->Write(0, $socio);
        // Data Ammissione a Socio Mutua
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(75, 109);
        $pdf->Write(0, $DataAmmissione);        
        // Riferimenti in piè di pagina
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(17, 244);
        $pdf->Write(0, 'CAG: '.$cag.' - IDsocio '.$idsocio);
        // Dati dentro alle immagini delle tessere
        $pdf->SetFont('Helvetica','B','10');
        $pdf->SetXY(65, 205);
      	$pdf->Write(0, $socio);
        $pdf->SetFont('Helvetica','','10');
        $pdf->SetXY(65, 210);
        $pdf->Write(0, $codiceFiscale);
        $pdf->SetXY(65, 215);
        $pdf->Write(0, "Socio dal ".$DataAmmissione);
        $pdf->SetXY(65, 225);
        $pdf->Write(0, "Tessera provvisoria");


    }

}

// Output the new PDF
$pdf->Output(); 




?>