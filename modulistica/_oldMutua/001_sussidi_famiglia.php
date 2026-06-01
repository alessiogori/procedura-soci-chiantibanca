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


        $selectdati = " SELECT  cag as Cag, 
                                date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') as DataNascita, 
                                codiceFiscale as CodiceFiscale, 
                                date_format(STR_TO_DATE(socioDal, '%Y-%m-%d'),'%d/%m/%Y') as DataAmmissione, 
                                numeroCartaMutuaSalus as Tessera 
                        FROM tab_mutua_elencosoci
                        WHERE Cag = ".$cag."
                        GROUP BY cag, codiceFiscale"; 
/*
        $selectdati = " SELECT  Cag, LuogoNascita, DataNascita, Indirizzo, Cap, Localita, CodiceFiscale
                        FROM tab_mutua30
                        WHERE cag = ".$cag."
                        GROUP BY Cag, LuogoNascita, DataNascita, Indirizzo, Cap, Localita, CodiceFiscale";
*/
        $querydati = mysqli_query($connection, $selectdati); 

        	if (mysqli_num_rows($querydati) <= 0) {

            //$LuogoNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $CodiceFiscale = '';
            $DataAmmissione = '';
            $Tessera = '';

        	}
        	else
        	{

	        while($dati=mysqli_fetch_array($querydati)){ 

		        //$LuogoNascita = $dati['LuogoNascita'];
	            $DataNascita = $dati['DataNascita'];
	            $Indirizzo = $_GET['ind'];
	            $Cap = $_GET['cap'];
	            $Localita = $_GET['com'].' '.$_GET['prov'];
	            $CodiceFiscale = $dati['CodiceFiscale'];
	            $DataAmmissione = $dati['DataAmmissione'];
	            $Tessera = $dati['Tessera'];

	        }
	    }

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('https://www.chiantimutua.it/documenti/2');
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
		$pdf->SetTextColor(0,48,119); 			//blu
      	// Nome Socio
        $pdf->SetXY(65, 45);
      	$pdf->Write(0, $socio);
        // Nato a 
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(65, 51);
        //$pdf->Write(0, $LuogoNascita);
        // Nato il
        $pdf->SetXY(25, 51);
        $pdf->Write(0, $DataNascita);
        // Codice Fiscale
        $pdf->SetXY(100, 56);
        $pdf->Write(0, $CodiceFiscale);
        // Tessera
        $pdf->SetXY(44, 56);
        $pdf->Write(0, $tessera);
        // Riferimenti in piè di pagina (portati in alto)
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(10, 24);
        $pdf->Write(0, 'CAG: '.$cag.' - IDsocio '.$idsocio);
        // Data Ammissione a Socio Mutua
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(24, 260);
        $pdf->Write(0, $DataAmmissione);        
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