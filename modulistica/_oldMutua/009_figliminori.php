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


if(isset($_GET['action']))    {

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

// ------- DATI DEI FIGLI MINORI -------

$nome1 = $_GET['nome1'];
$cognome1 = $_GET['cognome1'];
$natoa1 = $_GET['natoa1'];
$prov1 = $_GET['prov1'];
$datanato1 = $_GET['datanato1'];
$cf1 = $_GET['cf1'];

$nome2 = $_GET['nome2'];
$cognome2 = $_GET['cognome2'];
$natoa2 = $_GET['natoa2'];
$prov2 = $_GET['prov2'];
$datanato2 = $_GET['datanato2'];
$cf2 = $_GET['cf2'];

$nome3 = $_GET['nome3'];
$cognome3 = $_GET['cognome3'];
$natoa3 = $_GET['natoa3'];
$prov3 = $_GET['prov3'];
$datanato3 = $_GET['datanato3'];
$cf3 = $_GET['cf3'];

$nome4 = $_GET['nome4'];
$cognome4 = $_GET['cognome4'];
$natoa4 = $_GET['natoa4'];
$prov4 = $_GET['prov4'];
$datanato4 = $_GET['datanato4'];
$cf4 = $_GET['cf4'];

// ---------------------------------------

        // Estrazione dei dati anagrafici necessari
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        $selectdati = " SELECT  *, date_format(STR_TO_DATE(dataNascita, '%Y-%m-%d'),'%d/%m/%Y') as dataNascita
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
            $TelefonoSIB = '';
            $EmailSIB = '';

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                $LuogoNascita = '';
                $DataNascita = $dati['dataNascita'];
                $Indirizzo = '';
                $Cap = '';
                $Localita = '';
                $CodiceFiscale = $dati['codiceFiscale'];
                $TelefonoSIB = '';
                $EmailSIB = '';

            }

        }



// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('009_figliminori.pdf');
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
        $pdf->SetXY(47, 72);
      	$pdf->Write(0, $socio);
        // Nato a 
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(140, 72);
        $pdf->Write(0, $LuogoNascita);
        // Nato il
        $pdf->SetXY(18, 78);
        $pdf->Write(0, $DataNascita);
        // Residente a
        $pdf->SetXY(70, 78);
        $pdf->Write(0, $Localita);
        // In via
        $pdf->SetXY(36, 85);
        $pdf->Write(0, $Indirizzo);
        // CAP
        $pdf->SetXY(170, 85);
        $pdf->Write(0, $Cap);
        // Telefono
        $pdf->SetXY(36, 91);
        $pdf->Write(0, $TelefonoSIB);
        // Email
        $pdf->SetXY(100, 91);
        $pdf->Write(0, $EmailSIB);
        // Codice Fiscale
        //$pdf->SetXY(42, 76);
        //$pdf->Write(0, $CodiceFiscale);
        // Tessera
        $pdf->SetFont('Helvetica','','14');
        $pdf->SetXY(100, 98);
        $pdf->Write(0, $tessera);
        // Motivazione
        //$pdf->SetFont('Helvetica','','12');
        //$pdf->SetXY(19, 180);
        //$pdf->Write(0, $motivazione);
        // Luogo e data
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(14, 262);
        $pdf->Write(0, $luogo.', '.$oggi);
        // Riferimenti in piè di pagina
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(115, 276);
        $pdf->Write(0, 'CAG: '.$cag.' - IDsocio '.$idsocio.' - CF '.$CodiceFiscale);


        // FIGLI MINORI
        // FIglio 1
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(30, 125);
        $pdf->Write(0, $nome1);
        $pdf->SetXY(126, 125);
        $pdf->Write(0, $cognome1);
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(30, 132);
        $pdf->Write(0, $natoa1);
        $pdf->SetXY(90, 132);
        $pdf->Write(0, $prov1);
        $pdf->SetXY(100, 132);
        $pdf->Write(0, $datanato1);
        $pdf->SetXY(148, 132);
        $pdf->Write(0, $cf1);

        // FIglio 2
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(30, 144);
        $pdf->Write(0, $nome2);
        $pdf->SetXY(126, 144);
        $pdf->Write(0, $cognome2);
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(30, 151);
        $pdf->Write(0, $natoa2);
        $pdf->SetXY(90, 151);
        $pdf->Write(0, $prov2);
        $pdf->SetXY(100, 151);
        $pdf->Write(0, $datanato2);
        $pdf->SetXY(148, 151);
        $pdf->Write(0, $cf2);

        // FIglio 3
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(30, 163);
        $pdf->Write(0, $nome3);
        $pdf->SetXY(126, 163);
        $pdf->Write(0, $cognome3);
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(30, 170);
        $pdf->Write(0, $natoa3);
        $pdf->SetXY(90, 170);
        $pdf->Write(0, $prov3);
        $pdf->SetXY(100, 170);
        $pdf->Write(0, $datanato3);
        $pdf->SetXY(148, 170);
        $pdf->Write(0, $cf3);

        // FIglio 4
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(30, 182);
        $pdf->Write(0, $nome4);
        $pdf->SetXY(126, 182);
        $pdf->Write(0, $cognome4);
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(30, 189);
        $pdf->Write(0, $natoa4);
        $pdf->SetXY(90, 189);
        $pdf->Write(0, $prov4);
        $pdf->SetXY(100, 189);
        $pdf->Write(0, $datanato4);
        $pdf->SetXY(148, 189);
        $pdf->Write(0, $cf4);

    }
    
    // Scrivo solo nella prima pagina
    if($pageNo == 2){ 
	  	$pdf->SetFont('Helvetica','','8');
		$pdf->SetTextColor(0,48,119); 			//blu
      	// Nome Socio
        $pdf->SetXY(140, 138);
      	$pdf->Write(0, $socio);
        // Luogo e data
        $pdf->SetXY(10, 115);
        $pdf->Write(0, $luogo.', '.$oggi);     	

        // FIGLI MINORI
        // Intestazione tabella
        $pdf->SetXY(10, 150);
        $pdf->SetFont('Helvetica','I','10');        
        $pdf->Cell(10);
        $pdf->Cell(20,10,'Cognome e Nome',0,'L',0);
        $pdf->Cell(50);
        $pdf->Cell(20,10,'Luogo e data di nascita',0,'L',0);
        $pdf->Cell(60);
        $pdf->Cell(20,10,'Codice Fiscale',0,'L',0);
        
        // FIglio 1
        $pdf->SetFont('Helvetica','','10');
        $pdf->SetXY(10, 160);
        $pdf->Write(0, $cognome1.' '.$nome1);
        $pdf->SetXY(72, 160);
        $pdf->Write(0, $natoa1.' '.$prov1.' '.$datanato1);
        $pdf->SetXY(155, 160);
        $pdf->Write(0, $cf1);

        // FIglio 2
        $pdf->SetXY(10, 165);
        $pdf->Write(0, $cognome2.' '.$nome2);
        $pdf->SetXY(72, 165);
        $pdf->Write(0, $natoa2.' '.$prov2.' '.$datanato2);
        $pdf->SetXY(155, 165);
        $pdf->Write(0, $cf2);
        
        // FIglio 3
        $pdf->SetXY(10, 170);
        $pdf->Write(0, $cognome3.' '.$nome3);
        $pdf->SetXY(72, 170);
        $pdf->Write(0, $natoa3.' '.$prov3.' '.$datanato3);
        $pdf->SetXY(155, 170);
        $pdf->Write(0, $cf3);

        // FIglio 4
        $pdf->SetXY(10, 175);
        $pdf->Write(0, $cognome4.' '.$nome4);
        $pdf->SetXY(72, 175);
        $pdf->Write(0, $natoa4.' '.$prov4.' '.$datanato4);
        $pdf->SetXY(155, 175);
        $pdf->Write(0, $cf4);

    }
    

}

// Output the new PDF
$pdf->Output(); 


}
else
{


echo '<center style="font-family:courier;"><h3>Integra le informazioni necessarie per completare il modulo</h3>';

echo '  <fieldset style="width:700px;text-align:left;background-color:#FFFFFF;">
        <legend>&nbsp;Dati anagrafici dei <b>FIGLI MINORI</b></legend>';

echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <br>
                <small><b>Figlio 1</b></small><br>
                Nome <input type="text" name="nome1" required> Cognome <input type="text" name="cognome1" required><br>
                Nato a <input type="text" name="natoa1" required> Prov. <input type="text" name="prov1" size="3" required>
                il <input type="text" name="datanato1" required size="12"><br>
                Cod.Fiscale <input type="text" name="cf1" required>
                <br>

                <br>
                <small><b>Figlio 2</b></small><br>
                Nome <input type="text" name="nome2" > Cognome <input type="text" name="cognome2" ><br>
                Nato a <input type="text" name="natoa2" > Prov. <input type="text" name="prov2" size="3" >
                il <input type="text" name="datanato2"  size="12"><br>
                Cod.Fiscale <input type="text" name="cf2" >
                <br>

                <br>
                <small><b>Figlio 3</b></small><br>
                Nome <input type="text" name="nome3" > Cognome <input type="text" name="cognome3" ><br>
                Nato a <input type="text" name="natoa3" > Prov. <input type="text" name="prov3" size="3" >
                il <input type="text" name="datanato3"  size="12"><br>
                Cod.Fiscale <input type="text" name="cf3" >
                <br>

                <br>
                <small><b>Figlio 4</b></small><br>
                Nome <input type="text" name="nome4" > Cognome <input type="text" name="cognome4" ><br>
                Nato a <input type="text" name="natoa4" > Prov. <input type="text" name="prov4" size="3" >
                il <input type="text" name="datanato4"  size="12"><br>
                Cod.Fiscale <input type="text" name="cf4" >
                <br><br>
                ';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="tessera" id="tessera" value="'.$_GET['tessera'].'">
                <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
                <input type="hidden" class="form-control" name="socio" id="socio" value="'.$_GET['socio'].'">
                <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$_GET['idsocio'].'">
                <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">


                <br>
                <button type="submit" class="btn btn-success mb-2">Stampa modello</button><br>
    </form>
    </fieldset>
    </center>
';
}


?>