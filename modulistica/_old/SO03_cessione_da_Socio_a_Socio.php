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


if(isset($_GET['action']))    {

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'SO03';
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$cag2 = $_GET['cagcessionario'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
$titolo = '';
// ---------------------------------------

if ($_GET['motivazione'] == 'Parziale') 
    {	//$titolo = "CESSIONE PARZIALE AZIONI DA SOCIO A SOCIO";
		$simbolo = "P";
		$uscita  = "";
    }
else 
    {	//$titolo = "CESSIONE TOTALE AZIONI DA SOCIO A SOCIO";
		$simbolo = "T";
		$uscita  = "Contestualmente, il sottoscritto richiede di uscire dalla compagine sociale di ChiantiBanca.";
    }
    
$NumAzioni      = $_GET['numazioni'];  
$Calcolo        = $NumAzioni * 30.33;
$ValoreCessione = number_format($Calcolo, 2, ',', '.') ;
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

        // A - Estrazione dei dati anagrafici necessari del cedente
        // ---------------------------------------------------------------
        $selectdati = " SELECT  prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, nAzTot, nominaleAzTot, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, dataUscita, dataEstinzione, causaleUscita, titoloOnorifico,
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
            $Filiale = '';
            $DescFiliale = '';
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
                else {$datinascita = '';
                    $LuogoNascita = '';
                    $DataNascita = '';}

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
                $Filiale = $dati['codFil'];
                $DescFiliale = $dati['int1Filiale'];        
            }
        }

        // B - Estrazione dei dati anagrafici necessari del cessionario
        // ---------------------------------------------------------------
        $selectdati2 = " SELECT CONCAT(int1Socio,' ',int2Socio) as Nominativo2, prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, nAzTot, nominaleAzTot, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, dataUscita, dataEstinzione, causaleUscita, titoloOnorifico,
                                sesso, telefono, indirizzoEmail, indirizzoPEC, tipoContropVAL, codFil, int1Filiale
                        FROM tab_soci_as37
                        WHERE cag = ".$cag2;

        $querydati2 = mysqli_query($connection, $selectdati2); 

        if (mysqli_num_rows($querydati2) <= 0) {

            $socio2 = '';
            $NuMSocio2 = '';
            $LuogoNascita2 = '';
            $ProvinciaNascita2 = '';
            $DataNascita2 = '';
            $Indirizzo2 = '';
            $Cap2 = '';
            $Localita2 = '';
            $Provincia2 = '';
            $Azioni2 = '';
            $Nominale2 = '';
            $CagDelegato2 = '';
            $IntestazioneDelegato2 = '';
            $DataAmmissione2 = '';
            $dataEntrata2 = '';
            $dataUscita2 = '';
            $dataEstinzione2 = '';
            $CausaleUscita2 = '';
            $TitoloOnorifico2 = '';
            $Sesso2 = '';
            $Telefono2 = '';
            $Mail2 = '';
            $PEC2 = '';
            $Filiale2 = '';
            $DescFiliale2 = '';
            }
            else
            {

            while($dati2=mysqli_fetch_array($querydati2)){ 

                $socio2 = $dati2['Nominativo2'];
                $NuMSocio2 = $dati2['prot'];
                $LuogoNascita2 = $dati2['LuogoNascita'];
                $DataNascita2 = $dati2['DataNascita'];
                $Indirizzo2 = $dati2['Indirizzo'];
                $Cap2 = $dati2['Cap'];
                $Localita2 = $dati2['Localita'];
                $Provincia2 = $dati2['indirSpedProvincia'];
                $nAzTot2 = $dati2['nAzTot'];
                $Azioni2 = $dati2['nAzTot'];

                if ( $dati2['tipoContropVAL'] == 11000 )
                    {$datinascita2 = 'Nato/a a '.$LuogoNascita2.' il '.$DataNascita2;
                     }
                else {$datinascita2 = '';
                    $LuogoNascita2 = '';
                    $DataNascita2 = '';
                }

                $Nominale2 = 'Eur '.substr($dati2['nominaleAzTot'],0,-1);

                $CagDelegato2 = $dati2['cagDelegato'];
                $IntestazioneDelegato2 = $dati2['int1Delegato'];
                $DataAmmissione2 = $dati2['dataAmmiss'];
                $dataEntrata2 = $dati2['dataEntrata'];
                $dataUscita2 = $dati2['dataUscita'];
                $dataEstinzione2 = $dati2['dataEstinzione'];
                $CausaleUscita2 = $dati2['causaleUscita'];
                $TitoloOnorifico2 = $dati2['titoloOnorifico'];
                $Sesso2 = $dati2['sesso'];
                $Telefono2 = $dati2['telefono'];
                $Mail2 = $dati2['indirizzoEmail'];
                $PEC2 = $dati2['indirizzoPEC'];
                $Filiale2 = $dati2['codFil'];
                $DescFiliale2 = $dati2['int1Filiale'];        
            }
        }

        logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('SO03_cessione_da_Socio_a_Socio.pdf');
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
        
        // Flag Totale o Parziale
        $pdf->SetXY(30, 19);
        $pdf->Write(0, $simbolo); 
        // Filiale
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(125, 55);
        $pdf->Write(0, $Filiale);  
        $pdf->SetXY(130, 55);
        $pdf->Write(0, $DescFiliale); 
        // Titolo
        /*
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(36, 20);
        $pdf->Write(0, $titolo);
        $pdf->SetFont('Helvetica','B','11');
        */
        // Nome Socio 1
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(55, 73);
        $pdf->Write(0, substr($socio,0,50));     // limito i primi 50 caratteri del nome socio
        $pdf->SetXY(172, 73);
        $pdf->Write(0, $cag);  
        // Dati nascita e residenza 1
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(24, 80);
        $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(128, 80);
        $pdf->Write(0, $DataNascita);
        // Indirizzo Socio 1
        $pdf->SetXY(50, 87);
        $pdf->Write(0, $Localita .' '.$Provincia .' - '.$Indirizzo);  
        // Numero azioni e importo
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(25, 122);
        $pdf->Write(0, $NumAzioni);
        $pdf->SetXY(130, 122);
        $pdf->Write(0, $ValoreCessione);
        
        // Nome Socio 2
        $pdf->SetXY(35, 136);
        $pdf->Write(0, $socio2);
        $pdf->SetXY(172, 136);
        $pdf->Write(0, $cag2);  
        // Dati nascita e residenza 2
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(24, 143);
        $pdf->Write(0, $LuogoNascita2);
        $pdf->SetXY(128, 143);
        $pdf->Write(0, $DataNascita2);
        // Indirizzo Socio 2
        $pdf->SetXY(50, 151);
        $pdf->Write(0, $Localita2 .' '.$Provincia2 .' - '.$Indirizzo2);          
        
        // Clausola uscita
        $pdf->SetFont('Helvetica','','9.5');
        $pdf->SetXY(9, 195);
        $pdf->Write(0, $uscita);
        
        // Data odierna
        $pdf->SetFont('Helvetica','','10');
        $pdf->SetXY(9, 210);
        $pdf->Write(0, $luogo .', '.$oggi);
        /*
        // Dati ultimo aggiornamento
        $pdf->SetXY(24, 266);
        $pdf->Write(0, 'Dati aggiornati al '.$ultimo_aggiornamento);
        */
        // Riferimenti in piè di pagina (portati in alto)
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(8, 270);
        $pdf->Write(0, 'CAG Cedente: A01.'.$cag.' - IDsocio A03.'.$idsocio);
        $pdf->SetXY(8, 274);
        $pdf->Write(0, 'CAG Cessionario: A01.'.$cag2.' - IDsocio A03.'.$NuMSocio2);
    }

}

// Output the new PDF
$pdf->Output(); 


}
else
{

echo '<center style="font-family:courier;">
        <h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Definizione della <b>Cessione a Socio (SO03)</b> <br>&nbsp; Cag '.$_GET['cag'].' <b>'.$_GET['socio'].'</b><i>(cedente)</i></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
<br>
                <input type="radio" name="motivazione" value="Totale">TOTALE<br>
                <input type="radio" name="motivazione" value="Parziale">PARZIALE<br>
                <input type="text" name="numazioni" size="10">&nbsp;Numero Azioni da cedere<br>
                <input type="text" name="cagcessionario" size="10">&nbsp;CAG del Cessionario<br>
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