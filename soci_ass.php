<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al date(format)tabase
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

// Definizioni
$dataassemblea 	= '2020-05-03';
$limitevoto 	= strtotime ( '-90 day' , strtotime ( $dataassemblea ) ) ;
$limitevoto = date ( 'd.m.Y' , $limitevoto );
//echo $limitevoto;

$tipoassemblea 	= 'ORDINARIA';

// INSERIMENTO PASSWORD DI ACCESSO 

if (!isset($_POST['psw']))
	{
	echo '<div id="load" style="display:none;">Loading... Please wait</div>';

	echo '<center><h1>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:green;"><i>ASSEMBLEA '.$tipoassemblea.' SOCI '.$dataassemblea.'</i></span></h1>
			<small style="color:green;">Gestionale presenze, deleghe, pullman</small><br><br>';
	 echo '	
	<table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="soci_ass.php" method="post" onsubmit="return ray.ajax()">
		  <div class="form-group mx-sm-3 mb-2">
		    <label for="password" class="sr-only">Password</label>
		    <input type="password" class="form-control" name="psw" id="psw" placeholder="Password">
		  </div>
		  <button type="submit" class="btn btn-success mb-2">ACCEDI</button>
		</form>
	</td>
	</tr>
	</table>';
	}
else 
	{

		switch ($_POST['psw']) {

			// DIREZIONE
         	case "direzione": 
			 $filiale = " AND b.presente in ('SI','NO') ";
	        break;

			// CAPI AREA
			case "canesi":  
			 $filiale = ' AND a.codFil in ("23","40","41","44","48","100")';
	        break;
			case "capiccioli": 
			 $filiale = ' AND a.codFil in ("20","21","22","24","25","26","27","30","32","33","35","42","57","100")';
	        break;
			case "capiazzini": 
			 $filiale = ' AND a.codFil in ("36","50","53","54","55","66","100")';
	        break;
			case "cafittipaldi": 
			 $filiale = ' AND a.codFil in ("43","67","70","71","73","75","76","100")';
	        break;
			case "camonnecchi": 
			 $filiale = ' AND a.codFil in ("0","1","2","3","4","5","9","12","51","56","100")';
	        break;
			case "camelani": 
			 $filiale = ' AND a.codFil in ("60","61","62","63","64","68","100")';
	        break;

	        // FILIALI
						case "000456": 
			 $filiale = ' AND a.codFil in ("0","100")';
	        break;
   						case "001154": 
			 $filiale = ' AND a.codFil in ("1","100")';
	        break;
            			case "002459": 
			 $filiale = ' AND a.codFil in ("2","100")';
	        break;
           				case "003481": 
			 $filiale = ' AND a.codFil in ("3","100")';
	        break;
            			case "004789": 
			 $filiale = ' AND a.codFil in ("4","100")';
	        break;
            			case "005698": 
			 $filiale = ' AND a.codFil in ("5","100")';
	        break;
            			case "009777": 
			 $filiale = ' AND a.codFil in ("9","100")';
	        break;
            			case "012463": 
			 $filiale = ' AND a.codFil in ("12","100")';
	        break;
            			case "020364": 
			 $filiale = ' AND a.codFil in ("20","100")';
	        break;
            			case "021464": 
			 $filiale = ' AND a.codFil in ("21","100")';
	        break;
            			case "022999": 
			 $filiale = ' AND a.codFil in ("22","100")';
	        break;
            			case "023888": 
			 $filiale = ' AND a.codFil in ("23","100")';
	        break;
            			case "024654": 
			 $filiale = ' AND a.codFil in ("24","100")';
	        break;
            			case "025456": 
			 $filiale = ' AND a.codFil in ("25","100")';
	        break;
            			case "026852": 
			 $filiale = ' AND a.codFil in ("26","100")';
	        break;
            			case "027777": 
			 $filiale = ' AND a.codFil in ("20","27","100")';
	        break;
            			case "030412": 
			 $filiale = ' AND a.codFil in ("30","100")';
	        break;
            			case "032321": 
			 $filiale = ' AND a.codFil in ("32","100")';
	        break;
	           			case "033666": 
			 $filiale = ' AND a.codFil in ("033","100")';
	        break;
            			case "035444": 
			 $filiale = ' AND a.codFil in ("035","100")';
	        break;
            			case "036555": 
			 $filiale = ' AND a.codFil in ("36","100")';
	        break;
            			case "040111": 
			 $filiale = ' AND a.codFil in ("40","100")';
	        break;
            			case "041555": 
			 $filiale = ' AND a.codFil in ("41","100")';
	        break;
            			case "042888": 
			 $filiale = ' AND a.codFil in ("42","100")';
	        break;
            			case "043797": 
			 $filiale = ' AND a.codFil in ("43","100")';
	        break;
            			case "044333": 
			 $filiale = ' AND a.codFil in ("44","100")';
	        break;
            			case "048111": 
			 $filiale = ' AND a.codFil in ("48","100")';
	        break;
            			case "050444": 
			 $filiale = ' AND a.codFil in ("50","100")';
	        break;
            			case "051895": 
			 $filiale = ' AND a.codFil in ("51","100")';
	        break;
            			case "052689": 
			 $filiale = ' AND a.codFil in ("52","100")';
	        break;
            			case "053235": 
			 $filiale = ' AND a.codFil in ("53","100")';
	        break;
            			case "054297": 
			 $filiale = ' AND a.codFil in ("54","100")';
	        break;
            			case "055861": 
			 $filiale = ' AND a.codFil in ("55","100")';
	        break;
            			case "056761": 
			 $filiale = ' AND a.codFil in ("56","100")';
	        break;
            			case "057488": 
			 $filiale = ' AND a.codFil in ("57","100")';
	        break;
            			case "060456": 
			 $filiale = ' AND a.codFil in ("60","100")';
	        break;
            			case "061321": 
			 $filiale = ' AND a.codFil in ("61","100")';
	        break;
            			case "062499": 
			 $filiale = ' AND a.codFil in ("62","100")';
	        break;
            			case "063741": 
			 $filiale = ' AND a.codFil in ("63","100")';
	        break;
            			case "064852": 
			 $filiale = ' AND a.codFil in ("64","100")';
	        break;
            			case "066963": 
			 $filiale = ' AND a.codFil in ("66","100")';
	        break;
            			case "067951": 
			 $filiale = ' AND a.codFil in ("67","100")';
	        break;
            			case "068753": 
			 $filiale = ' AND a.codFil in ("68","100")';
	        break;
            			case "070159": 
			 $filiale = ' AND a.codFil in ("70","100")';
	        break;
            			case "071357": 
			 $filiale = ' AND a.codFil in ("71","100")';
	        break;
            			case "073852": 
			 $filiale = ' AND a.codFil in ("73","100")';
	        break;
            			case "075456": 
			 $filiale = ' AND a.codFil in ("75","100")';
	        break;
            			case "076951": 
			 $filiale = ' AND a.codFil in ("76","100")';
	        break;
            			case "100984": 
			 $filiale = ' AND a.codFil in ("100")';
	        break;
         		default:
         		$filiale = ' AND a.codFil in ("999")';
		}

// ---FINE IDENTIFICAZIONE

// CONTEGGIO DEI SOCI DELLA FILIALE
$select_ass_c =	'SELECT count(*) as qta
				 FROM tab_soci_as37 as a, tab_soci_scelta as b
				 where a.prot = b.prot
				 '.$filiale.''
				 ;

logquery ($select_ass_c);
$querydati_c = mysqli_query($connection, $select_ass_c);
$datiass_c=mysqli_fetch_array($querydati_c);

// ELENCO DEI SOCI DELLA FILIALE
$select_ass =	'SELECT a.*, b.*
				 FROM tab_soci_as37 as a, tab_soci_scelta as b
				 where a.prot = b.prot
				 '.$filiale.'
				 ORDER BY a.codfil desc, a.int1Socio, a.int2Socio';

logquery ($select_ass);

$querydati = mysqli_query($connection, $select_ass);


// INTESTAZIONE TABELLA					
echo '<center><br>
		Data limite per diritto di voto: <b>'.$limitevoto.'</b>  - -  <a href="docs/ass12052019_delega.pdf" target="_blank">Clicca qui per aprire il modello di Delega</a> - - F5 per ricaricare la pagina 
			<a href="javascript:history.back();" title="Torna alla ricerca"><img height=40 align=absmiddle src="img/frecciasx.png"></a>
			<br>
			<small style="color:brown;">ATTENZIONE: sono tracciati la stampa ed il copia-incolla di questa pagina</small>
			<br>
			<small>Estratti nr. '.$datiass_c['qta'] .' Soci (in ordine di Filiale e alfabetico, comprensive di tutto il Centro Imprese)</small>
		';

/*
// CONTEGGIO DEI TOTALI
// -- PRESENTI		
	$select_1 =	'SELECT count(presente) as num_presenti FROM tab_soci_scelta
					 where presente = "SI" ';
	logquery ($select_1);
	$querydati1 = mysqli_query($connection, $select_1);
	$dati1=mysqli_fetch_array($querydati1);
	$num_presenti = $dati1['num_presenti'];
// -- HANNO DATO DELEGA		
	$select_2 =	'SELECT count(datodelega) as num_datodelega FROM tab_soci_scelta
					 where datodelega = "SI" ';
	logquery ($select_2);
	$querydati2 = mysqli_query($connection, $select_2);
	$dati2=mysqli_fetch_array($querydati2);
	$num_datodelega = $dati2['num_datodelega'];
// -- RICEVUTO RICVUTO DELEGA		
	$select_3 =	'SELECT count(ricevutodelega) as num_ricevutodelega FROM tab_soci_scelta
					 where ricevutodelega = "SI" ';
	logquery ($select_3);
	$querydati3 = mysqli_query($connection, $select_3);
	$dati3=mysqli_fetch_array($querydati3);
	$num_ricevutodelega = $dati3['num_ricevutodelega'];
// -- PULLMAN
	$select_4 =	'SELECT count(pullman) as num_pullman FROM tab_soci_scelta
					 where pullman = "SI" ';
	logquery ($select_4);
	$querydati4 = mysqli_query($connection, $select_4);
	$dati4=mysqli_fetch_array($querydati4);
	$num_pullman = $dati4['num_pullman'];
// -- LOCATION FONTEBECCI
	$select_5 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("Fontebecci")
				 GROUP BY location ';
	logquery ($select_5);
	$querydati5 = mysqli_query($connection, $select_5);
	$dati5=mysqli_fetch_array($querydati5);
	$num_location1 = "<b>".$dati5['num_location']."</b>";
// -- LOCATION FIRENZE
	$select_6 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("Firenze")
				 GROUP BY location ';
	logquery ($select_6);
	$querydati6 = mysqli_query($connection, $select_6);
	$dati6=mysqli_fetch_array($querydati6);
	$num_location2 = "<b>".$dati6['num_location']."</b>";
// -- LOCATION PISTOIA
	$select_7 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("Pistoia")
				 GROUP BY location ';
	logquery ($select_7);
	$querydati7 = mysqli_query($connection, $select_7);
	$dati7=mysqli_fetch_array($querydati7);
	$num_location3 = "<b>".$dati7['num_location']."</b>";
	

echo "<table width='40%' align='center'>
	  	<tr>
	  		<td width=50% align=right class='bgcolor_datatable_alt3' >
	  		nr.<b>".$num_presenti."</b> PRESENTI <br>
	  		nr.<b>".$num_datodelega."</b> HANNO DATO DELEGA <br>
	  		nr.<b>".$num_ricevutodelega."</b> HANNO RICEVUTO DELEGA
	  		</td>
	  		<td align=right class='bgcolor_datatable_alt3' >
	  		<b>Location</b> <br>
	  		Fontebecci nr.".$num_location1." <br>
	  		Firenze nr.".$num_location2." <br>
	  		Pistoia nr.".$num_location3." <br>
	  		nr.<b>".$num_pullman."</b> HANNO SCELTO IL PULLMAN 
	  		</td>
	  	</tr>
	  </table>
	  ";
*/
echo "<br>
		<br><small><center><img src='img/graph.png' align='absmiddle'>&nbsp<a href='soci_ass_totali.php'>Statistiche</a></br></small>";

echo "	<fieldset style='width:90%;text-align:left;'>
			<legend><b>Elenco Soci</b> (CTRL+F per ricerca)</legend>"; 

echo "<table width='100%'>
	  <thead> 
	  <tr>
		<th class='bgcolor_datatable_alt1x' align='left'>FIL</th>
		<th class='bgcolor_datatable_alt1x' align='center'>NR.SOCIO</th>
		<th class='bgcolor_datatable_alt1x' align='center'></th>
		<th class='bgcolor_datatable_alt1x' align='left'>INTESTAZIONE</th>
		<th class='bgcolor_datatable_alt1x' align='center'>DATA NASCITA</th>
		<th class='bgcolor_datatable_alt1x' align='left'>RAPPRESENTANTE</th>
		<th class='bgcolor_datatable_alt1x' align='center'>DATA ENTRATA</th>
		<th class='bgcolor_datatable_alt1x' align='right'>TELEFONO</th>
		<th class='bgcolor_datatable_alt1x' align='right'>PRESENTE</th>
		<th class='bgcolor_datatable_alt1x' align='right'>LOCATION</th>
		<th class='bgcolor_datatable_alt1x' align='right'>HA DATO<br>DELEGA</th>
		<th class='bgcolor_datatable_alt1x' align='right'>HA RICEVUTO<br>DELEGA</th>
		<th class='bgcolor_datatable_alt1x' align='right'>Qtà<br>Ricevute</th>
	  </tr>
	  </thead> 
	  <tbody>
	  ";
						
	while($datiass=mysqli_fetch_array($querydati)){ 

		if ($datiass['int1Delegato'] == "") {$int1Delegato = '';} 
			else {$int1Delegato = $datiass['int1Delegato']."<br>(".$datiass['cagDelegato'].")";} 

		if ($datiass['presente'] == "SI") {$colore1 = ' style=color:blue;' ;} else {$colore1 = '';}
		if ($datiass['datodelega'] == "SI") {$colore2 = ' style=color:blue;' ;} else {$colore2 = '';}
		if ($datiass['ricevutodelega'] == "SI") {$colore3 = ' style=color:blue;' ;} else {$colore3 = '';}
		if ($datiass['pullman'] == "SI") {$colore4 = ' style=color:blue;' ;} else {$colore4 = '';}

		if ($datiass['qtadeleghe'] == 1)  {$colore5 = ' style=background-color:lightyellow; color:blue;' ;} 
		elseif (($datiass['qtadeleghe'] == 2) OR ($datiass['qtadeleghe'] == 3)) {$colore5 = ' style=background-color:#F78686 color:white;' ;} 
		else {$colore5 = 'class=bgcolor_datatable_alt3';}

/* ORDINARIA E STRAORDINARIA
		if (($datiass['qtadeleghe'] == 1) OR ($datiass['qtadeleghe'] == 2)) {$colore5 = ' style=background-color:lightyellow; color:blue;' ;} 
		elseif ($datiass['qtadeleghe'] == 3) {$colore5 = ' style=background-color:#F78686 color:white;' ;} 
		else {$colore5 = 'class=bgcolor_datatable_alt3';}
*/
		echo "<tr>   
				<td class='bgcolor_datatable_alt3' align='left'>".$datiass['codFil']."</td>
				<td class='bgcolor_datatable_alt3' align='center'>".$datiass['prot']."</td>
				<td class='bgcolor_datatable_alt3' align='left'>
					<a href='soci_ass_edit.php?azione=change&prot=".$datiass['prot']."'>
						<img src='img/edit.png' width='20' align='absmiddle'></a>
				<td class='bgcolor_datatable_alt3' align='left'><b>".$datiass['int1Socio'].$datiass['int2Socio']."</b><br>(".$datiass['cag'].")</td>
				<td class='bgcolor_datatable_alt3' align='center'>".$datiass['dataNasc']."</td>
				<td class='bgcolor_datatable_alt3' align='left'>".$int1Delegato."</td>
				<td class='bgcolor_datatable_alt3' align='center'>".$datiass['dataEntrata']."</td>
				<td class='bgcolor_datatable_alt3' align='right'>".$datiass['telefono']."</td>
				<td class='bgcolor_datatable_alt3' align='center' ".$colore1.">".$datiass['presente']."</td>
				<td class='bgcolor_datatable_alt3' align='right'>".$datiass['location']."</td>
				<td class='bgcolor_datatable_alt3' align='center' ".$colore2.">".$datiass['datodelega']."</td>
				<td class='bgcolor_datatable_alt3' align='center' ".$colore3.">".$datiass['ricevutodelega']."</td>
				<td class='bgcolor_datatable_alt3' align='center' ".$colore5.">".$datiass['qtadeleghe']."</td>
				</td>
			 </tr>
			  "; 

	} 

	
echo "</tbody></table></fieldset>";

echo '	<br><br>
		<center>
			<a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a>
		</center>';

}
?>