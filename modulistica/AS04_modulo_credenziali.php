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


if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'cicalo')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'cicalo')) ) {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'AS04';
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
$adesso = date("d.m.Y G:i:s");
// ---------------------------------------

        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione della data ultimo caricamento
        $select_last = " SELECT  caricamento
                        FROM tab_ultimo_caricamento
                        WHERE fonte = 'tab_soci_as37'
                        ";

        $query_last = mysqli_query($connection, $select_last); 
            while($dati_last=mysqli_fetch_array($query_last)){ 
                $ultimo_aggiornamento = $dati_last['caricamento'];
            }

        // Estrazione dei dati anagrafici necessari
        $selectdati = " SELECT  prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, nAzTot, nominaleAzTot, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, dataUscita, dataEstinzione, causaleUscita, titoloOnorifico,
                                sesso, telefono, indirizzoEmail, indirizzoPEC, tipoContropVAL, codFil
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
            $Azioni = '';
            $Nominale = '';
            $CagDelegato = '';
            $IntestazioneDelegato = '';
            $DataAmmissione = '';
            $dataEntrata = '';
            $dataUscita = '';
            $dataEstinzione = '';
            $CausaleUscita = '';
            $TitoloOnorifico = '';
            $Sesso = '';
            $Telefono = '';
            $Mail = '';
            $PEC = '';
            $filiale = '';

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                $NuMSocio = $dati['prot'];
                $LuogoNascita = $dati['LuogoNascita'];
                $DataNascita = $dati['DataNascita'];
                $Indirizzo = $dati['Indirizzo'];
                $Cap = $dati['Cap'];
                $Localita = $dati['Localita'];
                $Provincia = $dati['indirSpedProvincia'];
                $nAzTot = $dati['nAzTot'];
                $Azioni = $dati['nAzTot'];

                if ( $dati['tipoContropVAL'] == 11000 )
                    {$datinascita = 'Nato/a a '.$LuogoNascita.' il '.$DataNascita;}
                else {$datinascita = '';}

                $Nominale = 'Eur '.substr($dati['nominaleAzTot'],0,-1);

                $CagDelegato = $dati['cagDelegato'];
                $IntestazioneDelegato = $dati['int1Delegato'];
                $DataAmmissione = $dati['dataAmmiss'];
                $dataEntrata = $dati['dataEntrata'];
                $dataUscita = $dati['dataUscita'];
                $dataEstinzione = $dati['dataEstinzione'];
                $CausaleUscita = $dati['causaleUscita'];
                $TitoloOnorifico = $dati['titoloOnorifico'];
                $Sesso = $dati['sesso'];
                $Telefono = $dati['telefono'];
                
                $Mail = $_GET['email_presente']; 
                    
                $PEC = $dati['indirizzoPEC'];
                
                // username
                $username = sprintf("%08d", $NuMSocio);
                // password
                $password = sprintf("%08d", $cag);
                
                $filiale = $dati['codFil']; 
                
        
            }
        }

        logquery_modelli ($modello,$cag,$filiale);        // scrive il LOG del documento prodotto
        
// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('AS04_modulo_credenziali.pdf');
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
		//$pdf->SetTextColor(0,48,119); 			//blu
        // Data odierna
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(46, 42.5);
        $pdf->Write(0, $oggi);

        // Titolo
        $pdf->SetFont('Helvetica','B','11');
        $pdf->SetXY(100, 52);
        $pdf->Write(0, $TitoloOnorifico);
      	// Nome Socio
        $pdf->SetXY(100, 57);
      	$pdf->Write(0, $socio);
        // Indirizzo Socio
        $pdf->SetXY(100, 62);
        $pdf->Write(0, $Indirizzo);
        // CAP/Luogo/Provincia Socio
        $pdf->SetXY(100, 67);
        $pdf->Write(0, $Cap.' '.$Localita .' '.$Provincia);
        $pdf->SetFont('Helvetica','I','7');
        $pdf->SetXY(100, 72);
        $pdf->Write(0, $Mail);
        // Corpo della lettera
        $pdf->SetFont('Courier','B','12');
        $pdf->SetXY(75, 155);
        $pdf->Write(0, $username);      
        $pdf->SetXY(130, 155);
        $pdf->Write(0, $password);       

        // Data di stampa
        $pdf->SetFont('Helvetica','I','7');        
        $pdf->SetXY(24, 266);
        $pdf->Write(0, 'Stampato il '.$adesso);
        
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
        
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
        // Cerco la mail se presente
        $select_mail = " SELECT  indirizzoEmail
                        FROM tab_soci_as37
                        WHERE cag = ".$_GET['cag'];

        $query_mail = mysqli_query($connection, $select_mail); 
            while($dati_mail=mysqli_fetch_array($query_mail)){ 
                $mail = $dati_mail['indirizzoEmail'];
            }

echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <input type="email" name="email_presente" size="60" value="'.$mail.'">&nbsp;Email<br>
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