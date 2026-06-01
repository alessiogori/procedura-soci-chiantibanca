<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);

// Includo i dati di connessione
include("config/_config.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

echo "<style>tr.collapse.in {
.table tr {
    cursor: pointer;
}
.table{
	background-color: #fff !important;
}
.hedding h1{
	color:#fff;
	font-size:25px;
}
.main-section{
	margin-top: 120px;
}
.hiddenRow {
    padding: 0 4px !important;
    background-color: #eeeeee;
    font-size: 13px;
}
.accordian-body span{
	color:#a2a2a2 !important;
}
</style>";

echo "<script>
$('.accordion-toggle').click(function(){
	$('.hiddenRow').hide();
	$(this).next('tr').find('.hiddenRow').show();
});
</script>";

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


/*
if ( !isset($_GET['psw']) OR ($_COOKIE['filialedipendente'] != 999) )
	{
	echo '<center><h2><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI</i></span></h2>	
					Inserisci la password per accedere al Monitor Socio</center>';
	echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="monitor_lista.php" method="get" onsubmit="return ray.ajax()">
		  <div class="form-group mx-sm-3 mb-2">
		    <label for="psw" class="sr-only">Password</label>
		    <input type="password" class="form-control" name="psw" id="psw" placeholder="Password">
		    <input type="hidden" class="form-control" name="nominativo" id="nominativo" value="'.$_GET['nominativo'].'">
		    <input type="hidden" class="form-control" name="cag" id="cag" value="'.$_GET['cag'].'">
		  </div>
		  <button type="submit" class="btn btn-success mb-2">ACCEDI</button>
		</form>
	</td>
	</tr>
	</table>

	';
}
*/
  if(!in_array($_COOKIE['filiale_id'], array('997','998','999')) )
  {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
}

else
//	( ($_COOKIE['filialedipendente'] == 999) OR ($_GET['psw'] == "legale") )
{

// Dalla scheda socio passo in GET il CAG + NOMINATIVO
$cag 		= $_GET['cag'] ;
$nominativo	= $_GET['nominativo'] ;

// Interrogo la tabella del database Monitor
    $select = "SELECT 'Soci' as Fonte, 
					 id, cag, tipologia, data_ricezione, forma_ricezione, amezzodi, descrizione, esito, data_esito, status_esito, note, segnalato_a, data_segnalazione, attivo
					 FROM 	tab_monitor_soci
					 WHERE  cag = cast(".$cag." as unsigned)
					 AND attivo = 'S'
					 ORDER BY id desc
                    " ;
    //echo $select;
    $querydati = mysqli_query($connection, $select);

  	// Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $myfile = fopen("tmp/monitorsocio.csv", "w");
    $contenutofile .= "CAG;Fonte;Tipologia;DataRicezione;FormaRicezione;RicevutoDa;Descrizione;Esito;DataEsito;StatusEsito;Note;SegnalatoA;DataSegnalazione;ID\n";

// Titolo pagina
echo '<table width="90%" align="center" border="0" cellpadding="1" cellspacing="1" style="background-color:#222222;font-size:13px;">
    <tr>
		<td align="left"><i class="fas fa-desktop fa-2x text-gray-300 col-auto"></i>Monitor Socio<br>
                <b><h6>'.$cag.' - '.$nominativo.'</h6></b></td>
        <td align="right" width="15%">
        <a href="monitor_new.php?tipo=new&cag='.$cag.'&nominativo='.$nominativo.'" style="color:white;text-decoration:none;"><button type="button" class="btn btn-success">Nuova Segnalazione</button></a></td>
        <td align="right" width="15%">
        <a href="tmp/monitorsocio.csv" style="color:white;text-decoration:none;"><button type="button" class="btn btn-primary">Esporta</button></a></td>
    </tr>
    </table>';                

echo '<table class="table table-bordered" width="90%" align="center" border="0" cellpadding="1" cellspacing="1" style="background-color:#222222;border-collapse:collapse;">
  <thead>
    <tr class="table-active">
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Fonte</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Data<br>Ricezione</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Tipologia</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Forma<br>Ricezione</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Ricevuto da</th>
		<!-- <th scope="col"><small style="color:#C1D6E8;">Descrizione</th> -->
		<!-- <th scope="col"><small style="color:#C1D6E8;">Esito</th> -->
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Data<br>Esito</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Status<br>Esito</th>
		<!-- <th scope="col"><small style="color:#C1D6E8;">Note</th> -->
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Segnalato a</th>
		<th scope="col" width="10%"><small style="color:#C1D6E8;">Data<br>Segnalazione</th>
		<th scope="col" width="10%"></th>
	  </tr>
	   </thead>';

while($monitordati=mysqli_fetch_array($querydati)){ 

//$descrizione = html_entity_decode($monitordati['descrizione']); 
//$esito = html_entity_decode($monitordati['esito']); 
//$note = html_entity_decode($monitordati['note']); 

if ($monitordati['status_esito'] == 'ChiusoPositivo') { $icostatus = '<img src="img/ico_pallino_green.png" title="Positivo">'; }
if ($monitordati['status_esito'] == 'ChiusoNegativo') { $icostatus = '<img src="img/ico_pallino_red.png" title="Negativo">'; }
if ($monitordati['status_esito'] == 'InSospeso') { $icostatus = '<img src="img/ico_pallino_yellow.png" title="In Sospeso">'; }

		echo '<tr data-toggle="collapse" data-target="#riga" class="accordion-toggle">
				<td width="10%"><small>'.$monitordati['Fonte'].'</td>
				<td width="10%"><small>'.$monitordati['data_ricezione'].'</td>
				<td width="10%"><small>'.$monitordati['tipologia'].'</td>
				<td width="10%"><small>'.$monitordati['forma_ricezione'].'</td>
				<td width="10%"><small>'.$monitordati['amezzodi'].'</td>
				<td width="10%"><small>'.$monitordati['data_esito'].'</td>
				<td width="10%"><small>'.$icostatus.'</td>
				<td width="10%"><small>'.$monitordati['segnalato_a'].'</td>
				<td width="10%"><small>'.$monitordati['data_segnalazione'].'</td>
				<td width="10%" align="right"><a href="monitor_edit.php?id='.$monitordati['id'].'&cag='.$cag.'&nominativo='.$nominativo.'&tipo=edit" title="Aggiorna"><span class="badge badge-info">&nbsp;&nbsp;Edit&nbsp;&nbsp;</span></a>
				    &nbsp;
				    <a href="monitor_edit.php?id='.$monitordati['id'].'&cag='.$cag.'&nominativo='.$nominativo.'&tipo=off" title="Disattiva"><span class="badge badge-danger">Disattiva</span></a>
				    </td>
			  </tr>
			  <tr class="p table-secondary">
			  	<td colspan="4" class="hiddenRow" width="40%">
		            <div class="accordian-body collapse p-3" id="riga">
		            	<h6 style="color:#C1D6E8;">Descrizione</h6>
		            	<p style="font-size:12px;white-space: pre-line;">'.$monitordati['descrizione'].'</p>
		        	</div> 
	        	</td> 
			  	<td colspan="4" class="hiddenRow" width="40%">
		            <div class="accordian-body collapse p-3" id="riga">
		            	<h6 style="color:#C1D6E8;">Esito</h6>
		            	<p style="font-size:12px;white-space: pre-line;">'.$monitordati['esito'].'</p>
		        	</div> 
	        	</td> 
			  	<td colspan="2" class="hiddenRow" width="20%">
		            <div class="accordian-body collapse p-3" id="riga">
		            	<h6 style="color:#C1D6E8;">Note</h6>
		            	<p style="font-size:12px;white-space: pre-line;">'.$monitordati['note'].'</p>
		        	</div> 
	        	</td> 
	        </tr>			  		
			  ';

	    // Scrivo su file
        $contenutofile .= 
			$monitordati['cag'].";".
			$monitordati['Fonte'].";".
			$monitordati['tipologia'].";".
			$monitordati['data_ricezione'].";".
			$monitordati['forma_ricezione'].";".
			$monitordati['amezzodi'].";".
			strip_tags($descrizione).";".
			strip_tags($esito).";".
			$monitordati['data_esito'].";".
			$monitordati['status_esito'].";".
			strip_tags($note).";".
			$monitordati['segnalato_a'].";".
			$monitordati['data_segnalazione'].";".
			$monitordati['ID']."\n";	



		}

echo '</table>';

    fwrite($myfile, $contenutofile);
    fclose($myfile);

    
}


?>

