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
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");

$motivazione = $_GET['motivazione'];

if ($_GET['motivazioneV'] != 'null') 
    {$motivazioneV = $_GET['motivazioneV'];
    }
else 
    {$motivazioneV = '';
    }
// ---------------------------------------

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('099_recessosocio.pdf');
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
        $pdf->SetXY(19, 135);
      	$pdf->Write(0, $socio);
        // Tessera
        $pdf->SetXY(140, 158);
        $pdf->Write(0, $tessera);
        // Motivazione
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(19, 180);
        $pdf->Write(0, $motivazione.$motivazioneV);
        // Luogo e data
        $pdf->SetFont('Helvetica','','12');
        $pdf->SetXY(19, 244);
        $pdf->Write(0, $luogo.', '.$oggi);
        // Riferimenti in piè di pagina
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(19, 275);
        $pdf->Write(0, 'CAG: '.$cag.' - IDsocio '.$idsocio);
    }

}

// Output the new PDF
$pdf->Output(); 

}
else
{


echo '<center style="font-family:courier;"><h3>Integra le informazioni necessarie per completare il modulo</h3>';

echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Motivazioni della richiesta di <b>recesso</b></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <input type="radio" name="motivazione" onclick="myFunction()" value="Mancato Utilizzo">Mancato Utilizzo<br>
                <input type="radio" name="motivazione" onclick="myFunction()" value="Chiusura Conto Corrente">Chiusura Conto Corrente<br>
                <input type="radio" name="motivazione" onclick="myFunction()" value="Decesso">Decesso<br>
                <br>
                <input type="radio" id="myCheck" name="motivazione" value="" onclick="myFunction()">
                Varie <small>(completare con delle annotazioni)</small><br>
                <script>
                function myFunction() {
                      // Get the radio
                      var radio = document.getElementById("myCheck");
                      // Get the output text
                      var text = document.getElementById("text");

                      // If the radio is checked, display the output text
                      if (radio.checked == true){
                        text.style.display = "block";
                      } else {
                        text.style.display = "none";
                        var text = document.getElementById("text").value=null;
                      }

                    }
                </script>
                <textarea id="text" style="display:none" name="motivazioneV" cols="60" rows="5"></textarea><br>';

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
    </fieldset>

    </center>
';
}

?>