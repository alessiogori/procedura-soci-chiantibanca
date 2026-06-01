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


if ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] != 'as22')) ) {
    echo 'Errore, password non valida';
}

elseif ( ((isset($_GET['action'])) && ($_GET['passwordsoci'] == 'as22')) ) {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'AS05';
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
$adesso = date("d.m.Y G:i:s");
$ipaddress = $_SERVER['REMOTE_ADDR'];
// ---------------------------------------

        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
        
        // Estraggo la Filiale del Socio
        $selectfiliale = "SELECT b.filiale, b.desc_filiale
                        FROM tab_soci_as37 as a, tab_psw as b
                        WHERE a.cag = '".$cag."'
                        AND a.codfil = CAST(b.filiale AS UNSIGNED)";
                        //echo $selectfiliale;
        $queryfil = mysqli_query($connection, $selectfiliale); 
        while($datifil=mysqli_fetch_array($queryfil)){ 
            $Filiale = $datifil['filiale'];
            $nomefiliale = $datifil['desc_filiale'];
        }


          // Controllo se per quel Socio è già stato inserito un buono
          $selectbuono = "SELECT * from tab_soci_buoni
                          WHERE cag = '".$cag."' ";
                          //echo $selectbuono;
          $querybuono = mysqli_query($connection, $selectbuono);
            //echo mysqli_num_rows($querybuono);
            if(mysqli_num_rows($querybuono) != "0")
            {
            echo "<center><h2 style='color:red;'>Trovato un buono già inserito! Non è possibile rilasciarne altri per questo Socio.</h2></center>";
            }                
            else
            { 
            // Se OK inserisco il buono rilasciato

                // Calcolo il numero di buono - formato 2020Bxxxxx
                $numerobuono =  1 ;

                $insert =  "INSERT into tab_soci_buoni
                                (Cag,Socio,Filiale,NomeFiliale,Genere,NumeroBuono,DataConsegnaBuono,IpAddress) 
                            VALUES 
                                ('". $cag."','".addslashes($socio)."','".$Filiale."','".$nomefiliale."','Olio','".$numerobuono."',now(),'". $ipaddress."') " ;
                                //echo $insert;
                mysqli_query($connection,$insert) or die(mysql_error());;


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
            $NumeroBuono = '';
            $Filiale = '';

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
                $Mail = $dati['indirizzoEmail']; 
                $PEC = $dati['indirizzoPEC'];
                
                $NumeroBuono = 1;
                $Filiale = $dati['codFil']; 
        
            }
        }

        logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto
        
// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('AS05_coupon_ritiro_olio.pdf');
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
        $pdf->SetFont('Helvetica','B','14');
        $pdf->SetXY(49, 58);
        $pdf->Write(0, $numerobuono);
        // Corpo della lettera
        $pdf->SetFont('Helvetica','','14');
        //$pdf->SetXY(75, 155);
        //$pdf->Write(0, $Filiale);      
        $pdf->SetXY(125, 130);
        $pdf->Write(0, $nomefiliale);       

        // Data di stampa
        $pdf->SetFont('Helvetica','I','7');        
        $pdf->SetXY(24, 266);
        $pdf->Write(0, 'Stampato il '.$adesso);
        
    }

}

// Output the new PDF
$pdf->Output(); 


    }
}

else
{

echo '<center style="font-family:courier;">
        <h2>RISERVATO UFFICIO SOCI</h2>
        <h3>Integra le informazioni necessarie per completare il rilascio del buono</h3>';
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