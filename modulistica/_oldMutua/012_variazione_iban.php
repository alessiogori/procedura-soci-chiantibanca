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


//if(isset($_GET['action']))    {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE MUTUA
// ---------------------------------------
$socio = $_GET['socio'];
$tessera = $_GET['tessera'];
//$motivazione = $_GET['motivazione'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
// ---------------------------------------

        // Estrazione dei dati anagrafici necessari
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        $selectdati = " SELECT  *, date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') as dataNascita,
                        date_format(STR_TO_DATE(socioDal, '%Y-%m-%d'),'%d/%m/%Y') as socioDal
                        FROM tab_mutua_elencosoci
                        WHERE cag = ".$cag."
                        ";

        $querydati = mysqli_query($connection, $selectdati); 

        	if (mysqli_num_rows($querydati) <= 0) {

            $LuogoNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $CodiceFiscale = '';
            $Iban = '';
            $DataAmmissione = '';


        	}
        	else
        	{

	        while($dati=mysqli_fetch_array($querydati)){ 

		        $LuogoNascita = '';
	            $DataNascita = $dati['DataNascita'];
	            $Indirizzo = '';
	            $Cap = '';
	            $Localita = '';
	            $CodiceFiscale = $dati['codiceFiscale'];
	            $Iban = '';
                $DataAmmissione = $dati['socioDal'];

	        }
	    }

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('012_variazione_iban.pdf');
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // Scrivo solo nella prima pagina
    if($pageNo == 1){ 
	  	$pdf->SetFont('Helvetica','','12');
		$pdf->SetTextColor(0,48,119); 			//blu
      	// Nome Socio
        $pdf->SetXY(53, 65);
      	$pdf->Write(0, $socio);
        // Indirizzo
        $pdf->SetXY(53, 79);
        $pdf->Write(0, $Indirizzo);
        // CAP
        $pdf->SetXY(160, 79);
        $pdf->Write(0, $Cap);
        // Localita
        $pdf->SetXY(53, 93);
        $pdf->Write(0, $Localita);
        // Codice Fiscale
        $pdf->SetXY(80, 32);
        $pdf->Write(0, $CodiceFiscale);
        $pdf->SetXY(53, 105);
        $pdf->Write(0, $CodiceFiscale);
        // Iban
        $pdf->SetXY(53, 117);
        $pdf->Write(0, $Iban);
      	// Nome Socio - sottoscrittore
        $pdf->SetXY(53, 192);
      	$pdf->Write(0, $socio);
        // Codice Fiscale - sottoscrittore
        $pdf->SetXY(53, 202);
        $pdf->Write(0, $CodiceFiscale);
        // Luogo e data
        $pdf->SetXY(23, 247);
        $pdf->Write(0, $luogo.', '.$oggi);        
        // Tessera
        //$pdf->SetXY(44, 56);
        //$pdf->Write(0, $tessera);
        // Riferimenti in piè di pagina (portati in alto)
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(10, 24);
        $pdf->Write(0, 'CAG: '.$cag.' - IDsocio '.$idsocio);
    }

}

// Output the new PDF
$pdf->Output(); 

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