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
$modello = 'AS00';
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
                        WHERE cag = ".$cag."
                        AND statoVal not in ('E','N','S')";

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

    logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto
                
// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('AS00_completo.pdf');
// iterate through all pages
for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);

    $pdf->AddPage();
    // use the imported page and adjust the page size
    $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

    // Piè di pagina
    // --------------------------------------------------
    $pdf->SetFont('Helvetica','','6');
        if (    ($pageNo == 1) OR 
                ($pageNo == 3) OR 
                ($pageNo == 4) 
            ){ 
        $pdf->SetXY(10, 276);       // Copia socio 
        $pdf->Write(0, $idsocio. ' - '.$cag.' - '.$socio);
    }
        $pdf->SetFont('Helvetica','','6');
        if (    ($pageNo == 6) OR 
                ($pageNo == 7) OR 
                ($pageNo == 8) 
            ){ 
        $pdf->SetXY(15, 188);       // Copia socio 
        $pdf->Write(0, $idsocio. ' - '.$cag.' - '.$socio);
    }
    // Scrivo il nome del socio in alto nella prima pagina
    // ---------------------------------------------------
    if($pageNo == 1){ 
        $pdf->SetFont('Helvetica','I','6');
        $pdf->SetTextColor(0,0,0);                  //nero
        // Nome Socio
        $pdf->SetXY(20, 10);
        $pdf->Write(0, $socio);
	}    
    // Scrivo il modulo di DELEGA
    // --------------------------------------------------
    if($pageNo == 2){ 
        $pdf->SetFont('Helvetica','B','11');
        $pdf->SetTextColor(0,0,0);                  //nero
        //$pdf->SetTextColor(0,48,119);             //blu
        // Data odierna
        //$pdf->SetFont('Helvetica','','11');
        //$pdf->SetXY(20, 226);
        //$pdf->Write(0, $luogo);
        //$pdf->SetXY(70, 226);
        //$pdf->Write(0, $oggi);
        // Filiale
        // $pdf->SetXY(26, 34);
        // $pdf->Write(0, $Filiale);  
        // $pdf->SetXY(26, 38);
        // $pdf->Write(0, $DescFiliale); 
        $pdf->SetFont('Helvetica','B','12');
        // Nome Socio
        $pdf->SetXY(26, 29);
        $pdf->Write(0, $socio);
        // Dati nascita 
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(30, 36);
        $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(86, 36);
        $pdf->Write(0, $DataNascita);
        // Indirizzo Socio
        $pdf->SetXY(65, 43);
        $pdf->Write(0, $Localita .' '.$Provincia);        
        $pdf->SetXY(42, 50);
        $pdf->Write(0, $Indirizzo);        
        // Telefono Socio
        $pdf->SetXY(130, 56);
        $pdf->Write(0, $Telefono);        
        // Email Socio
        $pdf->SetXY(42, 63);
        $pdf->Write(0, $Mail);        

        // Riferimenti in piè di pagina 
        $pdf->SetFont('Helvetica','I','10');
        $pdf->SetXY(45, 273);
        $pdf->Write(0, $idsocio. ' - CAG: '.$cag);
        
        // Data di stampa
        $pdf->SetFont('Helvetica','I','7');        
        $pdf->SetXY(130, 274);
        //$pdf->Write(0, 'Stampato il '.$adesso);

    }

    // Scrivo il modulo di VOTO
    // --------------------------------------------------
    if($pageNo == 5){ 
        $pdf->SetFont('Helvetica','B','11');
        $pdf->SetTextColor(0,0,0);                  //nero
        //$pdf->SetTextColor(0,48,119);             //blu
        // Data odierna
        //$pdf->SetFont('Helvetica','','11');
        //$pdf->SetXY(16, 160);
        //$pdf->Write(0, $luogo);
        //$pdf->SetXY(64, 160);
        //$pdf->Write(0, $oggi);
        // Filiale
        // $pdf->SetXY(26, 34);
        // $pdf->Write(0, $Filiale);  
        // $pdf->SetXY(26, 38);
        // $pdf->Write(0, $DescFiliale); 
        $pdf->SetFont('Helvetica','B','13');
        // Nome Socio
        $pdf->SetXY(26, 93);
        $pdf->Write(0, $socio);
        // Dati nascita 
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(34, 100);
        $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(130, 100);
        $pdf->Write(0, $DataNascita);
        // Indirizzo Socio
        $pdf->SetXY(190, 100);
        $pdf->Write(0, $Localita .' '.$Provincia);        
        $pdf->SetXY(48, 107);
        $pdf->Write(0, $Indirizzo);        
        // Telefono Socio
        $pdf->SetXY(48, 114);
        $pdf->Write(0, $Telefono);        
        // Email Socio
        $pdf->SetXY(134, 114);
        $pdf->Write(0, $Mail);        

        // Riferimenti in piè di pagina 
        $pdf->SetFont('Helvetica','I','12');
        $pdf->SetXY(50, 178);
        $pdf->Write(0, $idsocio. ' - CAG: '.$cag);
        
        // Data di stampa
        $pdf->SetFont('Helvetica','I','7');        
        $pdf->SetXY(212, 185);
        // $pdf->Write(0, 'Stampato il '.$adesso);   
        
    }

    // Scrivo il modulo di RICEVUTA
    // --------------------------------------------------    
    if($pageNo == 9){ 
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
        $pdf->SetXY(33, 28);
        $pdf->Write(0, $socio);
        $pdf->SetXY(33, 106);
        $pdf->Write(0, $socio);
        $pdf->SetXY(33, 187);
        $pdf->Write(0, $socio);
        // Dati nascita 
        $pdf->SetFont('Helvetica','','9');
        // $pdf->SetXY(30, 36);
        // $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(110, 28);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(110, 106);
        $pdf->Write(0, $DataNascita);
        $pdf->SetXY(110, 187);
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
        $pdf->SetXY(20, 80);
        $pdf->Write(0, 'Codice Socio: '.$idsocio. ' - CAG: '.$cag);
        $pdf->SetXY(20, 155);
        $pdf->Write(0, 'Codice Socio: '.$idsocio. ' - CAG: '.$cag);        
        $pdf->SetXY(109, 240);
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
						
echo '<style> 
        .btn-banca {
          color: #fff;
          background-color: #318939;
          border-radius: 3px;
          border: 1px solid rgba(21, 87, 36, 0.75);
          box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.5);
        } 
        .btn-mutua {
          color: #fff;
          background-color: #44883B;
          border-radius: 3px;
          border: 1px solid rgba(21, 87, 36, 0.75);
          box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.5);
        }
        input[type=text] {
          background-color: #AAF7DB;
          color: black;
        }
        </style>';

// Controllo se è Socio MUTUA
// se sì, presento il pulsante di stampa Delega+IStruzioni per ChiantiMutua
// ------------------------------------------------------------------------
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

$select_mutua 	  = "	SELECT * FROM tab_mutua_elencosoci
						WHERE cag = ".$_GET['cag'];

$querydati_mutua = mysqli_query($connection, $select_mutua);
	if(mysqli_num_rows($querydati_mutua) > 0) {
	    while($datisociomutua = mysqli_fetch_array($querydati_mutua)) 
	    {

        $esistenzasociomutua = '  
        <center>
        <br>
        <fieldset style="width:700px;text-align:left;"">
        <legend></legend>
        
        <br><center><span style="font-size:14px;font-family:Verdana;color:black;">
        Il socio Banca '.$_GET['socio'].' è anche <b>Socio MUTUA</b> <br>
        </span>
        ';
        
        // <form action="http://10.119.192.46:8080/mutua/modulistica/as01_modulo_delegavoto.php" method="GET" onsubmit="return ray.ajax()" target="_blank">
        $esistenzasociomutua .= '
        <form action="/soci/modulistica/as00_mutua.php" method="GET" onsubmit="return ray.ajax()" target="_blank">
        <br>
        
                <input type="hidden" class="form-control" name="passwordsoci" value="as22">
                <input type="hidden" class="form-control" name="source" value="banca">
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
                <input type="hidden" class="form-control" name="socio" id="socio" value="'.$_GET['socio'].'">
                <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$datisociomutua['idSocio'].'">
                <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">
        
                <button type="submit" class="btn-mutua" style="font-size:16px;font-family:Verdana;"><img src="../img/mutua_logo_bianco.png" height="60"><br><small>Stampa modello AS00 MUTUA<br>Delega + Istruzioni Voto</small><br><b>'.$_GET['socio'].'</b></button>                
        <br><br>   
        </form>
        

        ';

		}
	
	} else {
	    $esistenzasociomutua = '';
		}

// Controllo quante stampe sono state fatte - BANCA
// ------------------------------------------------------------------------
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
        
$select_log = "	SELECT count(*) as qta FROM tab_log_modelli
						WHERE Cag = ".$_GET['cag']." AND modellostampato = 'AS00'";
$querydati_log = mysqli_query($connection, $select_log);
	    while($datilogsocio = mysqli_fetch_array($querydati_log)) 
	    {
    	    if ($datilogsocio['qta'] >= 1) {
            $modelligiastampatiB = '<br><span style="font-size:12px;font-family:Verdana;">ATTENZIONE: già stampato '.$datilogsocio['qta'].' volta/e questo modello</span>';  
    	    } else {
    	    $modelligiastampatiB = '';
    	    }
	    }
	
// Controllo quante stampe sono state fatte -  MUTUA
// ------------------------------------------------------------------------	
        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
	
$select_log_mutua = "	SELECT count(*) as qta FROM tab_log_modelli
						WHERE Cag = ".$_GET['cag']." AND modellostampato = 'AS00M'";   
$querydati_log_mutua = mysqli_query($connection, $select_log_mutua);
	    while($datilogsociomutua = mysqli_fetch_array($querydati_log_mutua)) 
	    {
    	    if ($datilogsociomutua['qta'] >= 1) {
            $modelligiastampatiM = '<br><span style="font-size:12px;font-family:Verdana;">ATTENZIONE: già stampato '.$datilogsociomutua['qta'].' volta/e questo modello</span>';  
    	    } else {
    	    $modelligiastampatiM = '';
    	    }
	    }

// Identifico il soggetto Banca
// ------------------------------------------------------------------------
$select_sesso = "	SELECT sesso, cagDelegato, int1Delegato, dataEntrata, prot, statoVAL 
                    FROM tab_soci_as37
					WHERE Cag = ".$_GET['cag'];  //echo $select_sesso;
$querydati_sesso = mysqli_query($connection, $select_sesso);
	    while($datisessosocio = mysqli_fetch_array($querydati_sesso)) 
	    {
	        /*
	        // LIMITE DATA ASSEMBLEA 90 GG
            $dataassemblea 	= '2022-04-27';
            $limitevoto 	= strtotime ( '-90 day' , strtotime ( $dataassemblea ) ) ;
            $limitevoto     = date ( 'Y-m-d' , $limitevoto );
            //    echo '<br>Limite voto '.$limitevoto;
                
            // DATA ENTRATA SOCIO
	        $dataEntrata = substr($datisessosocio['dataEntrata'],6,4).'-'.
	                       substr($datisessosocio['dataEntrata'],3,2).'-'.
	                       substr($datisessosocio['dataEntrata'],0,2); 
	        $dataEntrata = strtotime($datisessosocio['dataEntrata']);
            $dataEntrata = date('Y-d-m',$dataEntrata);
            //    echo '<br>Data Entrata '.$dataEntrata;
            
            if ($dataEntrata < $limitevoto) 
                {$puovotare = 'SI';}
            else
                {$puovotare = 'NO';}
	        */
            $num = substr($datisessosocio['dataEntrata'],6,4).substr($datisessosocio['dataEntrata'],3,2) ;
            //echo $num;
            if ($num <= 202201)
                {$puovotare = 'SI';}
            else
                {$puovotare = 'NO';}
	        
                if ($datisessosocio['sesso'] == 'MASCHIO') 
                        {$SessoImg = '<img src="../img/ico_manB.png" height="50" align="absmiddle">';
                         $rappresentante = '';
                         $esistenzasociomutua2 = '';
                         $esistenzasocio2 = '';
                         }
                elseif ($datisessosocio['sesso'] == 'FEMMINA') 
                        {$SessoImg = '<img src="../img/ico_womanB.png" height="50" align="absmiddle">'; 
                         $rappresentante = '';
                         $esistenzasociomutua2 = '';
                         $esistenzasocio2 = '';
                         }
                else    {$SessoImg = '<img src="../img/ico_aziendaB.png" height="50" align="absmiddle">'; 
                         $rappresentante = '<small>'.$datisessosocio['cagDelegato'].' '.$datisessosocio['int1Delegato'].'</small>';    
            
            
                        // --------------------------------------------
                        // Verifica rappresentante se Socio attivo o no
                        // --------------------------------------------
                        $select_sociorapp = "SELECT	cagDelegato, statoVAL 
                                            FROM tab_soci_as37
                        					WHERE Cag = ".$datisessosocio['cagDelegato']."
                        					AND statoVAL not in ('E','N','S')"; // echo $select_sociorapp;
                        $querydati_sociorapp = mysqli_query($connection, $select_sociorapp);
                        if(@mysqli_num_rows($querydati_sociorapp) > 0) {
                            while($datisociorapp = mysqli_fetch_array($querydati_sociorapp)) 
                                {
                            $esistenzasocio2 = ' 
                            <center>
                            <center><span style="font-size:14px;font-family:Verdana;">
                            <i>Il rappresentante <b>'.$datisessosocio['int1Delegato'].'</b> è anche <b>Socio BANCA</b> <br><br>

                            <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()" target="_blank"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
                                      
                            <input type="hidden" class="form-control" name="passwordsoci" size=5 value="as22">
                            <input type="hidden" class="form-control" name="action" id="action" value="print">
                            <input type="hidden" class="form-control" name="cag" id="cag" value="'.$datisessosocio['cagDelegato'].'">
                            <input type="hidden" class="form-control" name="socio" id="socio" value="'.$datisessosocio['int1Delegato'].'">
                            <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$datisessosocio['prot'].'">
                            <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">
            
                            <button type="submit" class="btn-banca" style="font-size:16px;font-family:Verdana;"><small>Stampa modello AS00 BANCA<br>Delega + Istruzioni Voto</small><br><b>'.$datisessosocio['int1Delegato'].'</b></button>
            
                            </form>
                                          
                            </center>
                            ';
                        	    }
                        	    
                             } else {
                        	    $esistenzasocio2 = '';
         
                        	    }
                         
                            // CERCO IL RAPPRESENTANTE TRA I SOCI MUTUA
                            $select_mutua2 	  = "	SELECT * FROM tab_mutua_elencosoci
                            						WHERE Cag = ".$datisessosocio['cagDelegato'];
                            //echo $select_mutua2;
                            $querydati_mutua2 = mysqli_query($connection, $select_mutua2);
                            	if(mysqli_num_rows($querydati_mutua2) > 0) {
                            	    while($datisociomutua2 = mysqli_fetch_array($querydati_mutua2)) 
                            	    {
                            
                                    $esistenzasociomutua2 = '  
                                    <center>
                                    <br>
                                    <br><center><span style="font-size:14px;font-family:Verdana;color:black;">
                                    Il rappresentante <b>'.$datisessosocio['int1Delegato'].'</b> è anche <b>Socio MUTUA</b> <br>
                                    <table border="0" align="center" width="90%">
                                    </span>

                                    <form action="/soci/modulistica/as00_mutua.php" method="GET" onsubmit="return ray.ajax()" target="_blank">
                                    <br>
                                    
                                    <input type="hidden" class="form-control" name="passwordsoci" value="as22">
                                    <input type="hidden" class="form-control" name="source" value="banca">
                                    <input type="hidden" class="form-control" name="action" id="action" value="print">
                                    <input type="hidden" class="form-control" name="cag" id="cag" value="'.$datisessosocio['cagDelegato'].'">
                                    <input type="hidden" class="form-control" name="socio" id="socio" value="'.$datisessosocio['int1Delegato'].'">
                                    <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$datisociomutua2['idSocio'].'">
                                    <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">
                            
                                    <button type="submit" class="btn-mutua" style="font-size:16px;font-family:Verdana;"><img src="../img/mutua_logo_bianco.png" height="60"><br><small>Stampa modello AS00 MUTUA<br>Delega + Istruzioni Voto</small><br><b>'.$datisessosocio['int1Delegato'].'</b></button>
                                    <br><br>   
                                    </form>
                                    
                                    </fieldset>
                                    </center>
                            
                                    ';
                            
                            		}
                            	
                            	} else {
                            	    $esistenzasociomutua2 = '';
                            		}
                                                
                                      
                    
                }
	    }
                         
echo '<center style="font-family:courier;">
        <h2>ASSEMBLEA 2022 - DELEGA R.D. + ISTRUZIONI DI VOTO</h2>';
        // <h3>Integra le informazioni necessarie per procedere alla stampa</h3>
        
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend><img src="../img/logocb.png" height="60"></legend>
     ';    

if ($puovotare == 'SI') {        
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()" target="_blank"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
    
    <table border="0" align="center" width="90%">
    <tr><td width="5%">
    '.$SessoImg.'
    </td><td align="left">
    <b style="font-size:20px;font-family:Verdana;">&nbsp;&nbsp;'.$_GET['socio'].'</b>
    <br>&nbsp;&nbsp;'.$rappresentante.'
    </td></tr>
    <tr><td align="center" colspan="2">
    <button type="submit" class="btn-banca" style="font-size:16px;font-family:Verdana;">Stampa modello AS00 BANCA<br>Delega + Istruzioni Voto</button>
    </td></tr>
    <tr><td align="center" colspan="2" width="60%" style="color:#BF0000;">
        <br><b>Password stampa: <input type="password" class="form-control" name="passwordsoci" size=5></b><br>
        '.$modelligiastampatiB.'
    </td></tr>
    </table>';

echo '
                <input type="hidden" class="form-control" name="action" id="action" value="print">
                <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
                <input type="hidden" class="form-control" name="socio" id="socio" value="'.$_GET['socio'].'">
                <input type="hidden" class="form-control" name="idsocio" id="idsocio" value="'.$_GET['idsocio'].'">
                <input type="hidden" class="form-control" name="luogo" id="luogo" value="'.$_GET['luogo'].'">


    </form>
    ';
    
} else {
    echo '<center><h2>NON PUO\' VOTARE !</h2>
            Ammesso da meno di 90 giorni dalla data dell\'Assemblea</center>';
}

echo $esistenzasocio2.'<small style="color:#BF0000";>';  

echo '</fieldset>
        </center>';


echo $esistenzasociomutua.'<small style="color:#BF0000";>'.$modelligiastampatiM;    

echo $esistenzasociomutua2.'<small style="color:#BF0000";>';    

    
}


?>