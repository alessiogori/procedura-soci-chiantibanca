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
$modello = 'SO13';
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$cag2 = $_GET['cagrappresentante'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
$titolo = '';
// ---------------------------------------

        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione della data ultimo caricamento
        $select_last = " SELECT  caricamento
                        FROM tab_ultimo_caricamento
                        WHERE fonte = 'sds_sinergiareport_soci'
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
        $socio2         = $_GET['nominativorappresentante'];
        $LuogoNascita2  = $_GET['luogonascitarappresentante'];
        $DataNascita2   = $_GET['datanascitarappresentante'];
        $Localita2      = '';
        $Provincia2     = '';
        $Indirizzo2     = $_GET['indirizzorappresentante'];
        
logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto

// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('SO13_Modifica_del_Rappresentante.pdf');
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
        
        // Filiale
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(125, 54);
        $pdf->Write(0, $Filiale);  
        $pdf->SetXY(130, 54);
        $pdf->Write(0, $DescFiliale); 
        // Titolo
        /*
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(36, 20);
        $pdf->Write(0, $titolo);
        $pdf->SetFont('Helvetica','B','11');
        */
        
        // Nome Socio 1
        $pdf->SetFont('Helvetica','','10');
        $pdf->SetXY(172, 62);
        $pdf->Write(0, 'A01.'.$cag);   
        $pdf->SetXY(172, 66);
        $pdf->Write(0, 'A03.'.$idsocio);   
        
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(45, 72);
        $pdf->Write(0, $socio);
       
        // Dati nascita e residenza 1
        /*
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(24, 80);
        $pdf->Write(0, $LuogoNascita);
        $pdf->SetXY(128, 80);
        $pdf->Write(0, $DataNascita);
        */
        // Indirizzo Socio 1
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(45, 79);
        $pdf->Write(0, $Localita .' '.$Provincia .' - '.$Indirizzo);  
        // Dati rappresentante attuale
        //$pdf->SetXY(18, 92);
        //$pdf->Write(0, $IntestazioneDelegato);  
        //$pdf->SetXY(172, 92);
        //$pdf->Write(0, $CagDelegato);  
        
        // Nome Socio 2
        $pdf->SetFont('Helvetica','B','12');
        $pdf->SetXY(35, 160);
        $pdf->Write(0, strtoupper($socio2));
        $pdf->SetXY(172, 160);
        $pdf->Write(0, $cag2);  
        // Dati nascita e residenza 2
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(24, 167);
        $pdf->Write(0, strtoupper($LuogoNascita2));
        $pdf->SetXY(132, 167);
        $pdf->Write(0, $DataNascita2);
        // Indirizzo Socio 2
        $pdf->SetXY(50, 174);
        $pdf->Write(0, strtoupper($Localita2) .' '.strtoupper($Provincia2) .' '.strtoupper($Indirizzo2));          
        
        // Data odierna
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(8, 210);
        $pdf->Write(0, $luogo .', '.$oggi);
        /*
        // Dati ultimo aggiornamento
        $pdf->SetXY(24, 266);
        $pdf->Write(0, 'Dati aggiornati al '.$ultimo_aggiornamento);
        */
        // Riferimenti in piè di pagina (portati in alto)
        $pdf->SetFont('Helvetica','I','8');
        $pdf->SetXY(8, 270);
        $pdf->Write(0, 'CAG societa\': A01.'.$cag.' - IDsocio A03.'.$idsocio);
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
        <legend>&nbsp; Definizione della <b>Modifica del Rappresentante (SO13)</b> <br>&nbsp; Cag '.$_GET['cag'].' <b>'.$_GET['socio'].'</b><i></i></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >

                <br><b>Dati del nuovo Rappresentante</b><br>
                <input type="text" name="cagrappresentante" size="10">&nbsp;CAG<br>
                <input type="text" name="nominativorappresentante" size="40">&nbsp;Cognome e Nome<br>
                <input type="text" name="indirizzorappresentante" size="40">&nbsp;Indirizzo completo (località e via)<br>
                <input type="text" name="luogonascitarappresentante" size="30">&nbsp;Luogo di nascita<br>
                <input type="text" name="datanascitarappresentante" size="10" placeholder="gg/mm/aaaa">&nbsp;Data di nascita<br>
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