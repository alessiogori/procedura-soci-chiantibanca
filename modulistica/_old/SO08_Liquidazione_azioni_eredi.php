<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

require_once('_functions.php');   //logquery ($selectdati); 

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// Then inside the body tags put what you would like to be displayed in the doc file such as:    

if(isset($_GET['action']))    {
    
    
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; Filename=SO08.doc");

// Prepara il documento in HTML con i corretti meta tags
echo '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">
    </head>
    ';

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'SO07';
$socio = $_GET['socio'];
$cag = $_GET['cag'];
$cag2 = $_GET['cagcessionario'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
$titolo = '';
// ---------------------------------------

/*
if ($_GET['motivazione'] == 'Parziale') 
    {	//$titolo = "CESSIONE PARZIALE AZIONI DA SOCIO A SOCIO";
		$simbolo = "P";
    }
else 
    {	//$titolo = "CESSIONE TOTALE AZIONI DA SOCIO A SOCIO";
		$simbolo = "T";
    }
*/

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
        $socio2         = $_GET['nominativocessionario'];
        $LuogoNascita2  = $_GET['luogonascitacessionario'];
        $DataNascita2   = $_GET['datanascitacessionario'];
        $Localita2      = '';
        $Provincia2     = '';
        $Indirizzo2     = $_GET['indirizzocessionario'];

        logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto        

?>

<body style="font-size:14px;font-family:Verdana;">

<table border="0" witdh="90%" align="center">
<tr>
    <td width="10%" align="left" style="font-size:14px;font-family:Verdana;">SO08</td>
    <td width="90%" align="center" style="font-family:Verdana;"><h3>DOMANDA DI LIQUIDAZIONE AZIONI AGLI EREDI</h1></td>
</tr>
</table>

<br>
<br>

<table border="0" witdh="90%" align="center">
<tr>
<td width="60%">&nbsp;</td>
    <td width="40$" style="font-size:14px;font-family:Verdana;">
        <b>
        Spett.le<br>
        CHIANTIBANCA<br>
        CREDITO COOPERATIVO S.C. <br>               
        Via Cassia Nord n. 2/4/6 <br>
        53035 Monteriggioni<br>
        </b>
    </td>
</tr>
</table>

<br>
<br>
<p align="justify">
Il/I sottoscritto/i <b></b><?php echo '[NOME/I EREDE/I]'.'<br>'; ?>
<br></b>in qualita' di erede/i del/della defunto/a sig./sig.ra <br>
<b><?php echo substr($socio,0,50).'<br>'; ?></b> nato/a a <?php echo $LuogoNascita; ?> il <?php echo $DataNascita; ?> <br>e deceduto/a in <?php echo '[LUOGO DECESSO]'; ?> in data <?php echo '<b>'.'[DATA DECESSO]'.'</b>'; ?>, 
<br>con riferimento alla partecipazione al capitale di codesta Banca detenuta dal predetto nominativo rappresentata da n. <b><?php echo $Azioni; ?></b> azioni ciascuna del valore nominale di Eur 30,09 per complessivi <b><?php echo $Nominale; ?></b>
<br>
<center>chiede/chiedono</center> 
<br>
in esecuzione della divisione ereditaria concordata di liquidare il controvalore di detta cessione mediante: 
<br>
<br>
[   ] accredito sul C/C n. _____________________ <br>
[   ] consegna di assegno circolare non trasferibile intestato a "gli eredi di" <br>
[   ] bonifico su C/C presso altra Banca IBAN _________________________________ <br>

<br>
<br>

Il/i  sottoscritto/i altresi' Vi esonera/no espressamente da ogni e qualsiasi responsabilita', sia presente che futura, relativa alla predetta assegnazione, rinunziando, fin da ora, ad ogni eccezione, azione e riserva al riguardo.
</p>
<br>
<br>

<?php echo $luogo .', '.$oggi; ?>

<table border="0" width="90%" align="center">
<tr>
<td width="60%">&nbsp;</td>
    <td width="40$" align="center" style="font-size:14px;font-family:Verdana;">
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>         
                                    (firme)<br>
    </td>
</tr>
</table>

<br>
<br>
<br>
<br>

<small>Per la documentazione da allegare si rinvia espressamente a quanto previsto dall'apposito ODS in tema di Successioni</small>

<br>
<br>
<?php echo 'A03.'.$idsocio .' <br> A01.'.$cag; ?>


</body>


<!--
// initiate FPDI
$pdf = new Fpdi();
// get the page count
$pageCount = $pdf->setSourceFile('SO05_cessione_da_Socio_a_NON_Socio.pdf');
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
        $pdf->Write(0, substr($socio,0,50));  // limito i primi 50 caratteri del nome del socio
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
        $pdf->Write(0, strtoupper($socio2));
        $pdf->SetXY(172, 136);
        $pdf->Write(0, $cag2);  
        // Dati nascita e residenza 2
        $pdf->SetFont('Helvetica','','11');
        $pdf->SetXY(24, 143);
        $pdf->Write(0, strtoupper($LuogoNascita2));
        $pdf->SetXY(128, 143);
        $pdf->Write(0, $DataNascita2);
        // Indirizzo Socio 2
        $pdf->SetXY(50, 151);
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
        $pdf->Write(0, 'CAG Cedente: '.$cag.' - IDsocio '.$idsocio);
        $pdf->SetXY(8, 274);
        $pdf->Write(0, 'CAG Cessionario: '.$cag2);
    }

}

// Output the new PDF
$pdf->Output(); 
-->
<?php

} /*
else
{

echo 'http://10.119.192.46:8080/soci/modulistica/SO08_liquidazione_eredi.php?modello=SO08&cag=03033148&socio=FEDI+ALESSIO+&idsocio=3060086&luogo=Pistoia';


echo '<center style="font-family:courier;">
        <h3>Integra le informazioni necessarie per completare il modulo</h3>';
echo '  <fieldset style="width:700px;text-align:left;"">
        <legend>&nbsp; Definizione della <b>LIQUIDAZIONE A EREDI (SO08)</b> <br>&nbsp; Cag '.$_GET['cag'].' <b>'.$_GET['socio'].'</b><i>(deceduto)</i></legend>';
echo '
    <form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()"><table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
<br>
                <input type="radio" name="motivazione" value="Totale">TOTALE<br>
                <input type="radio" name="motivazione" value="Parziale">PARZIALE<br>
                <input type="text" name="numazioni" size="10">&nbsp;Numero Azioni da cedere<br>
                <br><b>Dati del Cessionario</b><br>
                <input type="text" name="cagcessionario" size="10">&nbsp;CAG<br>
                <input type="text" name="nominativocessionario" size="40">&nbsp;Nominativo o Ragione Sociale<br>
                <input type="text" name="indirizzocessionario" size="40">&nbsp;Indirizzo completo (località e via)<br>
                <input type="text" name="luogonascitacessionario" size="30">&nbsp;Luogo di nascita<br>
                <input type="text" name="datanascitacessionario" size="10" placeholder="gg/mm/aaaa">&nbsp;Data di nascita<br>
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
*/

?>