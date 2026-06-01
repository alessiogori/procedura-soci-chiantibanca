<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
//
// Questo script serve a controllare se fossero presenti nel db CESSIONI
// posizioni che in realtà sono già estinte
//
// SEZIONE DA NON MODIFICARE
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

<?php		
if (empty($_GET['filiale'])) 
{$condizionefiliale = "WHERE filiale <> 999 ";
$condizionefiliale2 = "WHERE filiale_capofila <> 999 ";}
else 
{$condizionefiliale = " WHERE filiale = ".$_GET['filiale']." ";
$condizionefiliale2 = "WHERE filiale_capofila = ".$_GET['filiale']." ";}


if ($_GET['scelta']=="cessioni") {
  
  echo "  
  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	order: [[ 7, 'desc' ]],
          	lengthMenu: [ 10, 25, 50, 75, 100, 500, 1000 ],
          	deferRender: true,
  	        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    } );

		});</script>   
    ";
    
// ********************************************************
// CESSIONI
// ********************************************************
/*
$strQuery = "       SELECT s.stato_nag, s.idsocio, c.NAG, concat(s.intestazione_a, ' ', s.intestazione_b) as Nominativo, 
                    CAST(s.nAzTot as signed) as nAzTot, CAST(s.nominaleAzTot as signed) as nominaleAzTot, 
                    c.Numero_Azioni as nAzCessione, c.Valore_Nominale as nominaleAzCessione, Totale_Parziale as Tipo, 
                    c.Data_Richiesta, c.ID
                    FROM `tab_xls_cessioni` as c right join sds_soci as s 
                    ON c.CAG = s.cag 
                    WHERE c.Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VC','VB') 
                    GROUP BY s.prot, c.CAG
                    ORDER by c.CAG   ";
*/
$strQuery = "
            SELECT              
              case 
              when s.stato_nag = '0' then 'Potenziale'
              when s.stato_nag = '1' then 'Attivo'
              when s.stato_nag = '2' then 'Ex Cliente'
              else '' end as stato_nag, s.idsocio, c.NAG, concat(s.intestazione_a, ' ', s.intestazione_b) as Nominativo, 
            CAST(a.Numero_Azioni as signed) as nAzTot, CAST(a.Valore_Azioni as signed) as nominaleAzTot, 
            c.Numero_Azioni as nAzCessione, CAST(c.Valore_Nominale as signed) as nominaleAzCessione, Totale_Parziale as Tipo, 
            c.Data_Richiesta, c.ID, c.note_motivazioni
            FROM 
            sds_soci s RIGHT JOIN tab_xls_cessionibanca c
            ON s.idsocio = c.idsocio
            INNER JOIN sds_soci_certificati a
            ON s.idsocio = a.idsocio
            WHERE c.Rimborsato != 'S' 
            GROUP BY s.idsocio, c.nag
            ORDER by c.ID        
            ";            

$result = mysqli_query($connection, $strQuery);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Elenco CESSIONI in essere con verifica su SDS SOCI</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th style="text-align:left; font-size:13px;">Stato</th>
        <th style="text-align:left; font-size:13px;">Prot</th>
        <th style="text-align:left; font-size:13px;">CAG</th>
        <th style="text-align:left; font-size:13px;">Nominativo</th>
        <th style="text-align:left; font-size:13px;">Numero<br>Azioni<br>Sicra</th>
        <th style="text-align:left; font-size:13px;">Valore<br>Nominale<br>Sicra</th>
        <th style="text-align:left; font-size:13px;">Numero<br>Azioni<br>Cessione</th>
        <th style="text-align:left; font-size:13px;">Valore<br>Nominale<br>Cessione</th>
        <th style="text-align:center; font-size:13px;">Totale<br>Parziale</th>
        <th style="text-align:center; font-size:13px;">Data<br>Richiesta</th>
        <th style="text-align:center; font-size:13px;">ID</th>
    </tr>
    </thead>
    <tbody>
'; 

  // iterating over each data and pushing it into $arrData array
  while ($row = mysqli_fetch_array($result)) {
      
    if (number_format($row['nAzTot'],0,',','.') < number_format($row['nAzCessione'],0,',','.') )
    {
        $coloreazioni = " style='color:orange;' ";
        $controllo = "!";
    }
    else
    {
        $coloreazioni = "";
        $controllo = "";
    }

    echo "<tr class='table-secondary'>
            <td align='right'>".$row['stato_nag']." ".$controllo."</td>
            <td align='right'>".$row['idsocio']."</td>
            <td align='right'>".$row['NAG']."&nbsp;</td>
            <td><a class='text-success' href='sqldati_schedasocio.php?id=".$row['idsocio']."'>".$row['Nominativo']."</a></td>
            <td align='right' ".$coloreazioni.">".number_format($row['nAzTot'],0,',','.')."</td>
            <td align='right'>".number_format($row['nominaleAzTot'],0,',','.')."</td>
            <td align='right'>".number_format($row['nAzCessione'],0,',','.')."</td>
            <td align='right'>".number_format($row['nominaleAzCessione'],0,',','.')."</td>            
            <td align='center'>".$row['Tipo']."</td>
            <td align='center'>".$row['Data_Richiesta']."</td>
            <td align='right'><a style='color:white;' href='admin_cessioni.php?nominativo=".$row['Nominativo']."&id=".$row['ID']."&dr=".$row['Data_Richiesta']."&vn=".$row['nominaleAzCessione']."'>".$row['ID']."</a></td>
          </tr>
        ";
  }

echo '    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';
}


if ($_GET['scelta']=="pac") {

   
  echo "
  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
	      	order: [[ 3, 'desc' ]],
          	lengthMenu: [ 50, 10, 25, 50, 75, 100, 500, 1000 ],
            oLanguage: {'sLengthMenu': 'Mostra _MENU_ ', 'sSearch': 'Cerca nella lista:'},
          	deferRender: true,
          	buttons: ['csvHtml5']
    } );

		});</script>   
    ";    
// ********************************************************
// PAC
// ********************************************************

$strQueryP = "  SELECT 
                FILIALE_CAPOFILA, s.IDSOCIO, NAG, CONCAT(INTESTAZIONE_A,' ',INTESTAZIONE_B) AS NOMINATIVO, ETA, DATA_ENTRATA, CONCAT(FILIALE_RAPP,'-',NUM_RAPP) AS CONTO,
                ACQUISTO_PERIOD, NAZIONI_PERIOD, DATA_FINEPACK_PERIOD, NUMERO_AZIONI, VALORE_AZIONI,
                CASE
                  when nazioni_period = 3 then (33 - c.NUMERO_AZIONI)
                  when nazioni_period = 8 then (16 - c.NUMERO_AZIONI)
                  -- when nazioni_period = 3 then (33 - (c.NUMERO_AZIONI+NAZIONI_PERIOD))
                  -- when nazioni_period = 8 then (16 - (c.NUMERO_AZIONI+NAZIONI_PERIOD))
                else '' end as RESIDUO
                FROM sds_soci as s, sds_soci_certificati as c
                ".$condizionefiliale2."
                AND SOCIO_ISTITUTO = 1
                AND ACQUISTO_PERIOD = 'Y'
                AND s.IDSOCIO = c.IDSOCIO
                ORDER BY cast(RESIDUO as unsigned), FILIALE_CAPOFILA, NOMINATIVO   ";

$resultP = mysqli_query($connection, $strQueryP);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Controllo situazione PIANO DI ACCUMULO per GIOVANI SOCI e CHIANTIMUTUA</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover"  id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th rowspan="2" style="text-align:left; font-size:13px;">Filiale</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">IDSOCIO</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">NAG</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">Conto</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">Nominativo</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Eta\'</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Data Entrata</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">PAC<br>Num Azioni</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">PAC<br>Data Fine</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Totale<br>Azioni ad oggi</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Totale<br>Valore ad oggi</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Azioni<br>Residue</th>
    </tr>
    </thead>
    <tbody>
'; 

  while ($dati_p = mysqli_fetch_array($resultP)) {

    		echo "	<tr class='table-secondary'>
    		            <td align='left'>".$dati_p['FILIALE_CAPOFILA']."</td>
                    <td align='left'>".$dati_p['IDSOCIO']."&nbsp;</td>
                    <td align='left'>".$dati_p['NAG']."&nbsp;</td>
                    <td align='left'>".$dati_p['CONTO']."&nbsp;</td>
                    <td><a class='text-success' href='sqldati_schedasocio.php?id=".$dati_p['IDSOCIO']."'>".$dati_p['NOMINATIVO']."</a></td>
                    <td align='center'>".$dati_p['ETA']."&nbsp;</td>
                    <td align='center'>".$dati_p['DATA_ENTRATA']."&nbsp;</td>
                    <td align='center'>".$dati_p['NAZIONI_PERIOD']."&nbsp;</td>
                    <td align='center'>".$dati_p['DATA_FINEPACK_PERIOD']."&nbsp;</td>
                    <td align='center'>".$dati_p['NUMERO_AZIONI']."&nbsp;</td>
                    <td align='center'>".$dati_p['VALORE_AZIONI']."&nbsp;</td>
                    <td align='center'>".$dati_p['RESIDUO']."&nbsp;</td>
					</tr>";

  }

echo '    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';
}

if ($_GET['scelta']=="deceduti") {
    
  echo '  
  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $(\'#dataTable\').DataTable( {
          	"order": [[ 7, "desc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   
    ';     
    
// ********************************************************
// Deceduti (X2) per i quali gli eredi non hanno manifestato la loro volontà
// ********************************************************

$strQuery = "   SELECT 
                Filiale 
                ,CAG 
                ,Nominativo 
                ,Numero_Azioni 
                ,Valore_Nominale 
                ,STR_TO_DATE(Data_Richiesta, '%d/%m/%Y') as DataRichiesta
                ,Note_Motivazioni 
                ,Deceduto 
                ,STR_TO_DATE(Data_Decesso, '%d/%m/%Y') as DataDecesso
                ,STR_TO_DATE(Data, '%d/%m/%Y') as DataRegistrazione
                ,Note_AO08 as Stato
                ,Anno_Liquidazione 
                ,DATEDIFF(now(), STR_TO_DATE(Data_Decesso, '%d/%m/%Y')) as GGDIFF
                ,IF(DATEDIFF(now(), STR_TO_DATE(Data_Decesso, '%d/%m/%Y')) > 365, 'Oltre 1 anno', 'Meno di 1 anno') as PeriodoDIFF
                FROM tab_xls_decessi_eredi
                WHERE Note_AO08 in ('X2','X?')
                AND Intestazione_a_eredi = ''
                AND Liquidazione_a_eredi = ''
                ORDER BY 13 DESC    ";

$result = mysqli_query($connection, $strQuery);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Deceduti (X2) per i quali gli eredi non hanno manifestato la loro volontà</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th style="text-align:left; font-size:13px;">Filiale</th>
        <th style="text-align:left; font-size:13px;">CAG</th>
        <th style="text-align:left; font-size:13px;">Nominativo</th>
        <th style="text-align:left; font-size:13px;">Numero<br>Azioni</th>
        <th style="text-align:left; font-size:13px;">Valore<br>Nominale</th>
        <th style="text-align:center; font-size:11px;">Data<br>Richiesta</th>
        <th style="text-align:left; font-size:13px;">Note</th>
        <th style="text-align:center; font-size:13px;">Deceduto</th>
        <th style="text-align:center; font-size:11px;">Data<br>Decesso</th>
        <th style="text-align:center; font-size:11px;">Data<br>Registrazione</th>
        <th style="text-align:center; font-size:13px;">Stato</th>
        <th style="text-align:center; font-size:13px;">Anno<br>Liquid.</th>
        <th style="text-align:center; font-size:13px;">GG<br>Diff</th>
        <th style="text-align:center; font-size:13px;">Periodo<br>Diff</th>
    </tr>
    </thead>
    <tbody>
'; 

  // iterating over each data and pushing it into $arrData array
  while ($row = mysqli_fetch_array($result)) {

    echo "<tr class='table-secondary'>
            <td align='right'>".$row['Filiale']."</td>
            <td align='right'>".$row['CAG']."&nbsp;</td>
            <td>".$row['Nominativo']."</td>
            <td align='right'>".number_format($row['Numero_Azioni'],0,',','.')."</td>
            <td align='right'>".number_format($row['Valore_Nominale'],0,',','.')."</td>
            <td align='center'>".$row['DataRichiesta']."</td>
            <td align='center'>".$row['Note_Motivazioni']."</td>
            <td align='center'>".$row['Deceduto']."</td>
            <td align='center'>".$row['DataDecesso']."</td>
            <td align='center'>".$row['DataRegistrazione']."</td>
            <td align='center'>".$row['Stato']."</td>
            <td align='center'>".$row['Anno_Liquidazione']."</td>         
            <td align='center'>".$row['GGDIFF']."</td>
            <td align='center'>".$row['PeriodoDIFF']."</td>
          </tr>
        ";
  }

echo '    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';
}


if(($_GET['scelta']=="coge") && ($_GET['action']==""))
{
    
// ********************************************************
// Aggiornamento saldi Plafond + Fondo + Capitale Sociale
// ********************************************************
    // Leggo i valori
    // *****************************************************************************
    $select = " select DATE_FORMAT(aggiornamento, '%d/%m/%Y') as aggiornamento, valore, plafond, capitale, sovrapprezzo from tab_valorefondo";  
    $querydati = mysqli_query($connection, $select);      
    while($datifondo=mysqli_fetch_array($querydati)){       
      $data_aggto   = $datifondo['aggiornamento'];
      $fondo_valore = $datifondo['valore'];
      $plafond_valore = $datifondo['plafond'];
      $capitale_valore = $datifondo['capitale'];
      $sovrapprezzo_valore = $datifondo['sovrapprezzo'];
    }
    
  echo '<center><h2><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI</i></span></h2>
  <small style="color:gray;">Dati aggiornati al '.$data_aggto.'&nbsp; </small></center>';
  
  echo '<br><br>
    <form class="form-inline" action="check_vari.php" method="GET" onsubmit="return ray.ajax()">

   <table border="0" align="center" width="20%">
   <tr>
      <td align="left"><small>Valore Capitale Sociale 2881.990.100 </small> &nbsp; </td>
      <td align="left"><input type="text" style="background-color:#C6F2E8;text-align:right;" class="form-control" name="capitale" id="capitale" value="'.$capitale_valore.'" size=8></td>
   </tr>
   <tr>
      <td align="left"><small>Valore Sovrapprezzo 2885.990.100 </small> &nbsp; </td>
      <td align="left"><input type="text" style="background-color:#C6F2E8;text-align:right;" class="form-control" name="sovrapprezzo" id="sovrapprezzo" value="'.$sovrapprezzo_valore.'" size=8></td>
   </tr>
   <tr>
      <td align="left"><small>Valore Fondo 1770.990.100 </small> &nbsp; </td>
      <td align="left"><input type="text" style="background-color:#C6F2E8;text-align:right;" class="form-control" name="fondo" id="fondo" value="'.$fondo_valore.'" size=8></td>
   </tr>
   <tr>
      <td align="left"><small>Valore Residuo Liquidazioni da fare </small> &nbsp; </td>
      <td align="left"><input type="text" style="background-color:#C6F2E8;text-align:right;" class="form-control" name="plafond" id="plafond" value="'.$plafond_valore.'" size=8></td>
   </tr>
  <tr>
      <td align="center" colspan="2"><br>
        <input type="hidden" class="form-control" name="scelta" id="scelta" value="coge">
        <input type="hidden" class="form-control" name="action" id="action" value="update">
        <button type="submit" class="btn btn-success mb-2">AGGIORNA</button>
        </td>
  </tr>
  </table>

    </form>
  ';
  }
 
if  (($_GET['scelta']=="coge") && ($_GET['action']=="update"))
  {

$sovrapprezzo = $_GET['sovrapprezzo'];  // valore del sovrapprezzo inserito manualmente 
$capitale = $_GET['capitale'];  // valore del capitale sociale inserito manualmente 
$fondo    = $_GET['fondo'];     // valore del fondo inserito manualmente 
$plafond  = $_GET['plafond'];   // valore del plafond inserito manualmente 

    $updatefondo = "UPDATE tab_valorefondo SET aggiornamento=now(),capitale=".$capitale.",sovrapprezzo = ".$sovrapprezzo.",valore = ".$fondo.",plafond = ".$plafond;
    $queryupdate = mysqli_query($connection, $updatefondo);


echo '<center> Valore aggiornato! </center>';
}

?>