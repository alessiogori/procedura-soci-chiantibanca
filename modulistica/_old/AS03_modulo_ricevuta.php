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
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'as20')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'as20')) ) {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
$adesso = date("d.m.Y G:i:s");
// ---------------------------------------

        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione dei dati anagrafici necessari
        $selectdati = " SELECT  prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, titoloOnorifico,
                                sesso, telefono, indirizzoEmail, indirizzoPEC, tipoContropVAL, codFil, int1Filiale
                        FROM tab_soci_as37
                        WHERE cag = ".$cag;
        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $NuMSocio = '';
            $LuogoNascita = '';
            $ProvinciaNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $Provincia = '';
            $CagDelegato = '';
            $IntestazioneDelegato = '';
            $DataAmmissione = '';
            $dataEntrata = '';
            $TitoloOnorifico = '';
            $Sesso = '';
            $Telefono = '';
            $Mail = '';
            $PEC = '';
            $Filiale = '';
            $DescFiliale = '';
            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                $NuMSocio = $dati['prot'];
                $Indirizzo = $dati['Indirizzo'];
                $Cap = $dati['Cap'];
                $Localita = $dati['Localita'];
                $Provincia = $dati['indirSpedProvincia'];
               
                if ( $dati['tipoContropVAL'] <> 11000 )
                    {$DataNascita = '';
                     $LuogoNascita = ''; }
                else {$LuogoNascita = $dati['LuogoNascita'];
                      $DataNascita = $dati['DataNascita'];}

                $CagDelegato = $dati['cagDelegato'];
                $IntestazioneDelegato = $dati['int1Delegato'];
                $DataAmmissione = $dati['dataAmmiss'];
                $dataEntrata = $dati['dataEntrata'];
                $TitoloOnorifico = $dati['titoloOnorifico'];
                $Sesso = $dati['sesso'];
                $Telefono = $dati['telefono'];
                $Mail = $dati['indirizzoEmail'];
                $PEC = $dati['indirizzoPEC'];
                $Filiale = $dati['codFil'];
                $DescFiliale = $dati['int1Filiale'];       
            }
        }

        logquery ($selectdati,$Filiale); 

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('AS03_modulo_ricevuta.pdf');
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
        
        // Data odierna
        // $pdf->SetFont('Helvetica','','11');
        // $pdf->SetXY(20, 226);
        // $pdf->Write(0, $luogo);
        // $pdf->SetXY(70, 226);
        // $pdf->Write(0, $oggi);
        // Filiale
        // $pdf->SetXY(26, 34);
        // $pdf->Write(0, $Filiale);  
        // $pdf->SetXY(26, 38);
        // $pdf->Write(0, $DescFiliale); 
        $pdf->SetFont('Helvetica','B','10');
        // Nome Socio
        $pdf->SetXY(40, 26);
        $pdf->Write(0, $socio);
        $pdf->SetXY(40, 116);
        $pdf->Write(0, $socio);
        $pdf->SetXY(40, 210);
        $pdf->Write(0, $socio);
        // Dati nascita 
        $pdf->SetFont('Helvetica','','9');
        // $pdf->SetXY(30, 36);
        // $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(120, 26);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(120, 116);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(120, 210);
        $pdf->Write(0, $DataNascita);
        // Indirizzo Socio
        // $pdf->SetXY(65, 43);
        // $pdf->Write(0, $Localita .' '.$Provincia);        
        // $pdf->SetXY(42, 50);
        // $pdf->Write(0, $Indirizzo);        
        // Telefono Socio
        // $pdf->SetXY(130, 56);
        // $pdf->Write(0, $Telefono);        
        // Email Socio
        // $pdf->SetXY(42, 63);
        // $pdf->Write(0, $Mail);        

        // Riferimenti in piè di pagina 
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(130, 70);
        $pdf->Write(0, 'Codice Socio: '.$idsocio. ' - CAG: '.$cag);
        $pdf->SetXY(130, 160);
        $pdf->Write(0, 'Codice Socio: '.$idsocio. ' - CAG: '.$cag);        
        $pdf->SetXY(130, 252);
        $pdf->Write(0, 'Codice Socio: '.$idsocio. ' - CAG: '.$cag); 
        // Data di stampa
        //$pdf->SetXY(130, 118);
        //$pdf->Write(0, 'Stampato il '.$adesso);
        //$pdf->SetXY(130, 250);
        //$pdf->Write(0, 'Stampato il '.$adesso);        
        
        
    }

}

// Output the new PDF
$pdf->Output(); 


}
else
{

echo '<center style="font-family:courier;">
        <h2>RISERVATO UFFICIO SOCI</h2>
        <h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Informazioni necessarie</legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <input type="password" name="passwordsoci" size="10">&nbsp;Password Soci<br>
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