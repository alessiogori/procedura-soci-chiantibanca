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

require_once('_functions.php');   //logquery ($selectdati); 
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


//if(isset($_GET['action']))    {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE MUTUA
// ---------------------------------------
$modello = 'AS00M';
$socio = $_GET['socio'];
//$tessera = $_GET['tessera'];
//$motivazione = $_GET['motivazione'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
// ---------------------------------------

        // Estrazione dei dati anagrafici necessari
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        $selectdati = " SELECT  *
                        FROM tab_mutua_elencosoci
                        WHERE cag = ".$cag."
                        ";

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $Nominativo = '';
            $LuogoNascita = '';
            $DataNascita = '';
            $CodiceFiscale = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $Telefono = '';
            $Email = '';
            $CodiceSocio = '';       
            $Filiale = '';

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 
                
                    // LIMITE DATA ASSEMBLEA 90 GG
                    /*
                    $dataassemblea 	= '2021-05-27';
                    $limitevoto 	= strtotime ( '-90 day' , strtotime ( $dataassemblea ) ) ;
                    $limitevoto     = date ( 'Y-m-d' , $limitevoto );
                    //    echo '<br>Limite voto '.$limitevoto;
                        
                    // DATA ENTRATA SOCIO
        	        $dataEntrata = substr($dati['DataAmmissione'],6,4).'-'.
        	                       substr($dati['DataAmmissione'],3,2).'-'.
        	                       substr($dati['DataAmmissione'],0,2); 
        	        $dataEntrata = strtotime($dati['DataAmmissione']);
                    $dataEntrata = date('Y-d-m',$dataEntrata);
                    */
                    
                    /* --- SISTEMA USATO FINO A 2021 SU DUE PORTALI DIVERSI ---
                    $num = $dati['MeseAmm'];
                    // aggiungo zeri se mese minore di Ottobre, quindi a 1 carattere
                    if ($num < 10) {$num_padded = sprintf("%02d", $num);}
                            else   {$num_padded = $num;}

                    $limite = $dati['AnnoAmm'].$num_padded;
                    */

                    $limite = substr($dati['socioDal'],0,4).substr($dati['socioDal'],6,2);
                    if ($limite < 202201)
                        {$puovotare = 'SI';}
                    else
                        {$puovotare = 'NO';}
                        
                    // $testdataamm =  $limite;
                
                    /*    
                    if ($dataEntrata < $limitevoto) 
                        {$puovotare = 'SI';}
                    else
                        {$puovotare = 'NO';}
	                */


                $Nominativo = $dati['cognome'].' '.$dati['nome'];
                // $LuogoNascita = $dati['LuogoNascita'];
                $LuogoNascita = '';
                $DataNascita = $dati['dataNascita'];
                $CodiceFiscale = $dati['codiceFiscale'];
                /*
                $Indirizzo = $dati['Indirizzo'];
                $Cap = $dati['CAP'];
                $Localita = $dati['Citta'];
                $Telefono = $dati['Cellulare'];
                $Email = $dati['Email'];                
                */
                $Indirizzo = '';
                $Cap = '';
                $Localita = '';
                $Telefono = '';
                $Email = '';                
                
                $CodiceSocio = $dati['idSocio'];      
                // $Filiale = $dati['Filiale'];
                $Filiale = '';
            }
        }

if ($puovotare == 'SI') {   

    logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('AS00_mutua.pdf');
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // prima pagina - DELEGA
    if($pageNo == 1){ 
        // Scrivo il nome del socio in alto nella prima pagina
        $pdf->SetFont('Helvetica','I','6');
        $pdf->SetTextColor(0,0,0);                  //nero
        // Nome Socio
        $pdf->SetXY(15, 10);
        $pdf->Write(0, $socio);

	  	$pdf->SetFont('Helvetica','B','11');
		$pdf->SetTextColor(0,48,119); 			//blu
      	// Nome Socio
        $pdf->SetXY(105, 178);
      	$pdf->Write(0, $Nominativo);
        // Nato a 
        $pdf->SetXY(26, 185);
        $pdf->Write(0, $LuogoNascita);
        // Nato il
        $pdf->SetXY(78, 185);
        $pdf->Write(0, $DataNascita);
        // Codice Fiscale
        $pdf->SetXY(135, 185);
        $pdf->Write(0, $CodiceFiscale);
         // Localita
        //$pdf->SetXY(30, 183);
        //$pdf->Write(0, $Localita);       
        // Indirizzo
        //$pdf->SetXY(126, 183);
        //$pdf->Write(0, $Indirizzo);        
        // Telefono
        //$pdf->SetXY(26, 188);
        //$pdf->Write(0, $Telefono);
        // Email
        //$pdf->SetXY(90, 188);
        //$pdf->Write(0, $Email);
        // Codice Socio
        $pdf->SetXY(42, 275);
        $pdf->Write(0, $CodiceSocio);        
    }

    // terza pagina - VOTO
    if($pageNo == 3){ 
	  	$pdf->SetFont('Helvetica','B','11');
		$pdf->SetTextColor(0,48,119); 			//blu
      	// Nome Socio
        $pdf->SetXY(112, 40);
      	$pdf->Write(0, $Nominativo);
        // Nato a 
        $pdf->SetXY(30, 50);
        $pdf->Write(0, $LuogoNascita);
        // Nato il
        $pdf->SetXY(87, 50);
        $pdf->Write(0, $DataNascita);
        // Codice Fiscale
        $pdf->SetXY(145, 50);
        $pdf->Write(0, $CodiceFiscale);
         // Localita
        //$pdf->SetXY(35, 71);
        //$pdf->Write(0, $Localita);       
        // Indirizzo
        //$pdf->SetXY(134, 71);
        //$pdf->Write(0, $Indirizzo);        
        // Telefono
        //$pdf->SetXY(30, 79);
        //$pdf->Write(0, $Telefono);
        // Email
        //$pdf->SetXY(93, 79);
        //$pdf->Write(0, $Email);
        // Codice Socio
        $pdf->SetXY(55, 100);
        $pdf->Write(0, $CodiceSocio);        
    }
    
    // Quinta pagina - Scrivo il modulo di RICEVUTA
    // --------------------------------------------------    
    if($pageNo == 5){ 
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
        $pdf->SetXY(33, 29);
        $pdf->Write(0, $socio);
        $pdf->SetXY(33, 106);
        $pdf->Write(0, $socio);
        $pdf->SetXY(33, 188);
        $pdf->Write(0, $socio);
        // Dati nascita 
        $pdf->SetFont('Helvetica','','9');
        // $pdf->SetXY(30, 36);
        // $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(110, 29);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(110, 106);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(110, 188);
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
        $pdf->SetFont('Helvetica','I','7');
        $pdf->SetXY(23, 80);
        $pdf->Write(0, 'Codice Socio: '.$CodiceSocio);
        $pdf->SetXY(23, 155);
        $pdf->Write(0, 'Codice Socio: '.$CodiceSocio);        
        $pdf->SetXY(111, 243);
        $pdf->Write(0, 'Codice Socio: '.$CodiceSocio); 
        // Data di stampa
        //$pdf->SetXY(130, 118);
        //$pdf->Write(0, 'Stampato il '.$adesso);
        //$pdf->SetXY(130, 250);
        //$pdf->Write(0, 'Stampato il '.$adesso);             
        
    }
    
}

// Output the new PDF
$pdf->Output(); 

} else {
    echo '<center><h2>NON PUO\' VOTARE !</h2>
            Ammesso da meno di 90 giorni dalla data dell\'Assemblea</center>';
}
?>