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
header("Content-Disposition: attachment; Filename=SO09.doc");

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
$modello = 'SO09';
$socio = $_GET['socio'];
$nag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y H:i:s");
$titolo = '';
// ---------------------------------------

$NumAzioni      = $_GET['numazioni'];  
$Calcolo        = $NumAzioni * 30.33;
$ValoreCessione = number_format($Calcolo, 2, ',', '.') ;
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

        // ---------------------------------------------------------------
        $selectdati = "     SELECT *, valore_azioni as VAL_AZIONI
                            FROM sds_soci LEFT JOIN sds_soci_certificati
                                 ON sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO
                            WHERE NAG = ".$nag;

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $NuMSocio = '';
            $CodiceFiscale = '';
            //$LuogoNascita = '';
            //$ProvinciaNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $Comune = '';
            $Provincia = '';
            $Azioni = '';
            $ValoreNominale = '';
            $CagDelegato = '';
            $IntestazioneDelegato = '';
            //$DataAmmissione = '';
            $dataEntrata = '';
            $dataUscita = '';
            //$dataEstinzione = '';
            //$CausaleUscita = '';
            //$TitoloOnorifico = '';
            //$Sesso = '';
            $Telefono = '';
            $Mail = '';
            //$PEC = '';
            $Filiale = '';
            $DescFiliale = '';
            $co_filiale = '';  
            $co_conto = '';  

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                // ********************************************************
                // RICERCA DEL NOME DELLA FILIALE
                // ********************************************************
                $select_filiale   = "   SELECT * FROM tab_psw
                                        WHERE filiale = ".$dati['FILIALE_ANAGRAFICA'];
                //logquery ($select_filiale);  
                $querydati_filiale = mysqli_query($connection, $select_filiale);
                    if(mysqli_num_rows($querydati_filiale) > 0)
                        while($datifiliale = mysqli_fetch_array($querydati_filiale))
                        {
                            $nomefiliale = $datifiliale['desc_filiale'];
                        }
                    else
                    {
                        $nomefiliale = '';
                    }

                $NuMSocio = $dati['IDSOCIO'];
                $CodiceFiscale = $dati['CODICE_FISCALE'];
                //$LuogoNascita = $dati['LuogoNascita'];
                $DataNascita = $dati['DATA_DI_NASCITA'];
                $Indirizzo = $dati['VIA_RES'];
                $Cap = $dati['CAP_RES'];
                $Localita = $dati['LOCALITA_RES'];
                $Comune = $dati['DESCR_COM_RES'];
                $Provincia = $dati['PROVINCIA_RES'];
                $Azioni = $dati['NUM_AZIONI'];
                $ValoreNominale = number_format($dati['VAL_AZIONI'],2,',','.');

                if ( $dati['TIPO_SOGGETTO'] == 'PF' )
                    {$datinascita = 'Nato/a il '.$DataNascita;}
                else {$datinascita = '';}

                $CagDelegato = $dati['NAG_RAPP'];
                $IntestazioneDelegato = $dati['RAPPRESENTANTE'];
                //$DataAmmissione = $dati['dataAmmiss'];
                $dataEntrata = $dati['DATA_ENTRATA'];
                $dataUscita = $dati['DATA_USCITA'];
                //$dataEstinzione = $dati['dataEstinzione'];
                //$CausaleUscita = $dati['causaleUscita'];
                //$TitoloOnorifico = $dati['titoloOnorifico'];
                //$Sesso = $dati['sesso'];
                $Telefono = $dati['CELL'];
                $Mail = $dati['MAIL'];
                //$PEC = $dati['indirizzoPEC'];
                $Filiale = $dati['FILIALE_ANAGRAFICA'];
                $DescFiliale = $nomefiliale;
                $co_filiale = '';  
                $co_conto = '';        
            }
        }

        logquery_modelli ($modello,$nag,$Filiale);        // scrive il LOG del documento prodotto        

?>

<body style="font-size:12px;font-family:Verdana;">

<table border="0" witdh="90%" align="center">
<tr>
    <td width="10%" align="left" style="font-size:12px;font-family:Verdana;">SO09</td>
    <td width="90%" align="center" style="font-family:Verdana;"><h3>DESIGNAZIONE EREDE</h1></td>
</tr>
</table>

<br>
<br>

<table border="0" witdh="90%" align="center">
<tr>
<td width="60%">&nbsp;</td>
    <td width="40$" style="font-size:12px;font-family:Verdana;">
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
Il/I sottoscritto/i <br>
<b>
    [NOME/I EREDE/I NR.1] - [CODICE FISCALE EREDE/I NR.1]<br>
    [NOME/I EREDE/I NR.2] - [CODICE FISCALE EREDE/I NR.2]<br>
    [NOME/I EREDE/I NR.3] - [CODICE FISCALE EREDE/I NR.3]<br>
    [NOME/I EREDE/I NR.4] - [CODICE FISCALE EREDE/I NR.4]<br>
    [NOME/I EREDE/I NR.5] - [CODICE FISCALE EREDE/I NR.5]<br>
    [NOME/I EREDE/I NR.6] - [CODICE FISCALE EREDE/I NR.6]<br>
    [NOME/I EREDE/I NR.7] - [CODICE FISCALE EREDE/I NR.7]<br>
    [NOME/I EREDE/I NR.8] - [CODICE FISCALE EREDE/I NR.8]<br>
<br></b>
in qualita' di erede/i legittimo/i del/della defunto/a sig./sig.ra 
<br><b><?php echo substr($socio,0,50); ?> </b> c.f. <?php echo $CodiceFiscale; ?> 
<br>deceduto/a in data <b>[DATA DECESSO]</b>, gia' iscritto a Libro Soci al n.<?php echo $NuMSocio; ?> ,
designa/designano, ai sensi dello Statuto Sociale della Banca, 
<b>[NOME EREDE DESIGNATO] - [CODICE FISCALE EREDE DESIGNATO]</b> quale erede destinatario.
<br><br>Rimanendo in attesa di Vostra comunicazione in merito, porgiamo distinti saluti.
<br>
</p>

<?php echo $luogo .', '.$oggi; ?>

<table border="0" width="90%" align="center">
<tr>
<td width="60%">&nbsp;</td>
    <td width="40$" align="center" style="font-size:12px;font-family:Verdana;">
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>         
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>
                    _____________________________________ <br><br>         
                                    (firme)<br>
    </td>
</tr>
</table>

<br>
<br>
<p align="justify" style="font-size:10px;font-family:Verdana;">
Il/I sottoscritto/i [NOME TITOLARE DI FILIALE], responsabile della Filiale di [NOME FILIALE],
avendo accertato l'identita' di chi ha firmato il presente modulo, attestato che la firma
e' stata apposta in mia presenza e che il presente modulo e' stato ricevuto dalla Banca
oggi <?php echo $oggi; ?>
</p>
<table border="0" width="90%" align="center">
<tr>
<td width="60%">&nbsp;</td>
    <td width="40$" align="center" style="font-size:12px;font-family:Verdana;">
                    _____________________________________ <br><br>         
                                    (firma)<br>
    </td>
</tr>
</table>

<br>
<br>

<small>Documento da allegare alla domanda di trasferimento delle azioni del Socio defunto
(intestazione a erede) o alla domanda di rimborso delle azioni del Socio defunto.</small>

<br>
<br>
<?php echo 'IDSOCIO.'.$idsocio .' <br> NAG.'.$nag; ?>


</body>


<?php

} 


?>