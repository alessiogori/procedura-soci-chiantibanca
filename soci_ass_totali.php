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

$dataassemblea 	= '2019-05-12';

echo '<center><h1 style=color:green;>Totalizzatori Assemblea Soci '.$dataassemblea.'</h1>';

echo '<a href="javascript:history.back();" title="Torna alla ricerca"><img height=40 align=absmiddle src="img/frecciasx.png"></a></center>';

echo '<table width=90% align=center border=0>
		<tr>
			<td valign=top colspan=4>';

// CONTEGGIO DEI TOTALI
// -- PRESENTI		
	$select_t1 =	'SELECT count(presente) as num_presenti FROM tab_soci_scelta
					 where presente = "SI" ';
	logquery ($select_t1);
	$querydatit1 = mysqli_query($connection, $select_t1);
	$datit1=mysqli_fetch_array($querydatit1);
	$num_presenti = $datit1['num_presenti'];
// -- ASSENTI		
/*
	$select_t2 =	'SELECT count(presente) as num_presenti FROM tab_soci_scelta
					 where presente = "NO" ';
	$querydatit2 = mysqli_query($connection, $select_t2);
	$datit2=mysqli_fetch_array($querydatit2);
	$num_assenti = $datit2['num_presenti'];
*/
	$select_t2 =	'SELECT count(datodelega) as num_datodelega FROM tab_soci_scelta
					 where datodelega = "SI" AND presente in ("NO","")';
	logquery ($select_t2);
	$querydatit2 = mysqli_query($connection, $select_t2);
	$datit2=mysqli_fetch_array($querydatit2);
	$num_datodelega = $datit2['num_datodelega'];

// -- RICEVUTO DELEGA		
	$select_t3 =	'SELECT count(ricevutodelega) as num_ricevutodelega FROM tab_soci_scelta
					 where ricevutodelega = "SI" ';
	logquery ($select_t3);
	$querydatit3 = mysqli_query($connection, $select_t3);
	$datit3=mysqli_fetch_array($querydatit3);
	$num_ricevutodelega = $datit3['num_ricevutodelega'];

	$select_t3a =  'SELECT  
					CASE
					    WHEN qtadeleghe = 0 THEN count(*) * 1
					    ELSE sum(qtadeleghe)
					END as qta
					FROM `tab_soci_scelta` 
                    WHERE ricevutodelega = "SI"  ';
	logquery ($select_t3a);
	$querydatit3a = mysqli_query($connection, $select_t3a);
	$datit3a=mysqli_fetch_array($querydatit3a);
	$num_qtadeleghe = $datit3a['qta'];

// -- PULLMAN
	$select_t4 =	'SELECT count(pullman) as num_pullman FROM tab_soci_scelta
					 where pullman = "SI" ';
	logquery ($select_t4);
	$querydatit4 = mysqli_query($connection, $select_t4);
	$datit4=mysqli_fetch_array($querydatit4);
	$num_pullman = $datit4['num_pullman'];
// -- LOCATION FONTEBECCI
	$select_t5 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("Fontebecci")
				 GROUP BY location ';
	logquery ($select_t5);
	$querydatit5 = mysqli_query($connection, $select_t5);
	$datit5=mysqli_fetch_array($querydatit5);
	$num_location1 = "<b>".$datit5['num_location']."</b>";
// -- LOCATION FIRENZE SAN CASCIANO
	$select_t6 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("San Casciano")
				 GROUP BY location ';
	logquery ($select_t6);
	$querydatit6 = mysqli_query($connection, $select_t6);
	$datit6=mysqli_fetch_array($querydatit6);
	$num_location2 = "<b>".$datit6['num_location']."</b>";
// -- LOCATION PISTOIA
	$select_t7 =	'SELECT location, count(location) as num_location FROM tab_soci_scelta
				 WHERE location in ("Pistoia")
				 GROUP BY location ';
	logquery ($select_t7);
	$querydatit7 = mysqli_query($connection, $select_t7);
	$datit7=mysqli_fetch_array($querydatit7);
	$num_location3 = "<b>".$datit7['num_location']."</b>";
	

	$totalevoti = $num_presenti + $num_qtadeleghe;
	echo "<center  style='background-color:#FCC96F;'>Conteggio potenziale voti nr.<b>".$totalevoti."</b> <small>(Presenti nr.".$num_presenti." + Deleghe nr.".$num_qtadeleghe.")</small>";
	echo '</tr><td valign=top>';


// CONTEGGIO DEI TOTALI
	$select_1 =	'SELECT a.codFil, a.int1Filiale, count(b.presente) as num_presenti
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where b.presente = "SI"
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';
	logquery ($select_1);
	$querydati1 = mysqli_query($connection, $select_1);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend>Soci <u>presenti</u> in Assemblea <small>(<b>".$num_presenti."</b>)</small></legend>";
					while($dati1=mysqli_fetch_array($querydati1))
						{ echo '<small>'.$dati1['codFil']. ' ' .$dati1['int1Filiale']. ' nr.<b>' .$dati1['num_presenti'].'</b></small><br>'; }
	echo '</td><td valign=top>';

// -- ASSENTI
	/*
	$select_2 =	'SELECT a.codFil, a.int1Filiale, count(b.presente) as num_presenti
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where b.presente = "NO"
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';
	logquery ($select_2);
	$querydati2 = mysqli_query($connection, $select_2);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend>Soci <u>assenti</u> <small>(<b>".$num_assenti."</b>)</small></legend>";
					while($dati2=mysqli_fetch_array($querydati2))
						{ echo '<small>'.$dati2['codFil']. ' ' .$dati2['int1Filiale']. ' nr.<b>' .$dati2['num_presenti'].'</b></small><br>'; }
	echo '</td><td valign=top>';
*/	

	$select_2 =	'SELECT a.codFil, a.int1Filiale, count(b.datodelega) as num_datodelega
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where b.datodelega = "SI"
				 AND presente in ("NO","") 
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';	
	logquery ($select_2);
	$querydati2 = mysqli_query($connection, $select_2);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend>Soci <u>assenti</u> che hanno rilasciato delega <small>(<b>".$num_datodelega."</b>)</small></legend>";
					while($dati2=mysqli_fetch_array($querydati2))
						{ echo '<small>'.$dati2['codFil']. ' ' .$dati2['int1Filiale']. ' nr.<b>' .$dati2['num_datodelega'].'</b></small><br>'; }
	echo '</td><td valign=top>';

// -- RICEVUTO RICEVUTO DELEGA
	$select_3 =	'SELECT a.codFil, a.int1Filiale, count(b.ricevutodelega) as num_ricevutodelega
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where b.ricevutodelega = "SI"
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';			
	logquery ($select_3);
	$querydati3 = mysqli_query($connection, $select_3);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend>Soci <u>presenti</u> che hanno <u>ricevuto</u> delega <small>(<b>".$num_ricevutodelega."</b> per nr.<b>".$num_qtadeleghe."</b> deleghe)</small></legend>";
					while($dati3=mysqli_fetch_array($querydati3))
						{ echo '<small>'.$dati3['codFil']. ' ' .$dati3['int1Filiale']. ' nr.<b>' .$dati3['num_ricevutodelega'].'</b></small><br>'; }

	// Raggruppamento per deleghe ricevute
	$select_3a =	'SELECT count(*) as qtasoci, qtadeleghe as numdeleghe, sum(qtadeleghe) as qtadeleghe 
					 FROM tab_soci_scelta
					 WHERE ricevutodelega = "SI" 
					 GROUP BY qtadeleghe
 					 ORDER BY numdeleghe';			
	logquery ($select_3a);
	$querydati3a = mysqli_query($connection, $select_3a);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend><small>Suddivisione per numerosità</small></legend>";
				echo "<table border=0 align=center>
						<tr>
							<td><small>Qtà Soci</small></td>
							<td><small>Num Deleghe ricevute</small></td>
							<td><small>Tot Deleghe</small></td>
						</tr>";
					while($dati3a=mysqli_fetch_array($querydati3a))
						{ 
							echo "<tr>
									<td align=right><small>".$dati3a['qtasoci']."</small></td>
									<td align=center><small>".$dati3a['numdeleghe']."</small></td>
									<td align=right><small>".$dati3a['qtadeleghe']."</small></td>
								  </tr>
									";
						}

				echo "</table>";


	echo '</td>
			</tr>
			<tr><td valign=top>';

// -- LOCATION FONTEBECCI
	$select_5 =	'SELECT a.codFil, a.int1Filiale, count(b.location) as num_location
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where location in ("Fontebecci")
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';				
	logquery ($select_5);
	$querydati5 = mysqli_query($connection, $select_5);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend style='background-color:#FCC96F;'>Scelta Fontebecci <small>(tot.nr.<b>".$num_location1."</b>)</small></legend>";
					while($dati5=mysqli_fetch_array($querydati5))
						{ echo '<small>'.$dati5['codFil']. ' ' .$dati5['int1Filiale']. ' nr.<b>' .$dati5['num_location'].'</b></small><br>'; }
	echo '</td><td valign=top>';

// -- LOCATION FIRENZE San Casciano
	$select_6 =	'SELECT a.codFil, a.int1Filiale, count(b.location) as num_location
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where location in ("San Casciano")
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';	
	logquery ($select_6);
	$querydati6 = mysqli_query($connection, $select_6);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend style='background-color:#FCC96F;'>Scelta San Casciano <small>(tot.nr.<b>".$num_location2."</b>)</small></legend>";
					while($dati6=mysqli_fetch_array($querydati6))
						{ echo '<small>'.$dati6['codFil']. ' ' .$dati6['int1Filiale']. ' nr.<b>' .$dati6['num_location'].'</b></small><br>'; }
	echo '</td><td valign=top>';

// -- LOCATION PISTOIA
	$select_7 =	'SELECT a.codFil, a.int1Filiale, count(b.location) as num_location
				 FROM tab_soci_as37 as a, tab_soci_scelta as b 
				 where location in ("Pistoia")
				 AND a.prot = b.prot
				 group by a.codFil, a.int1Filiale
				 ORDER BY 1 ';	
	logquery ($select_7);
	$querydati7 = mysqli_query($connection, $select_7);
		echo "	<fieldset style='width:80%;text-align:left;'>
				<legend style='background-color:#FCC96F;'>Scelta Pistoia <small>(tot.nr.<b>".$num_location3."</b>)</small></legend>";
					while($dati7=mysqli_fetch_array($querydati7))
						{ echo '<small>'.$dati7['codFil']. ' ' .$dati7['int1Filiale']. ' nr.<b>' .$dati7['num_location'].'</b></small><br>'; }
	
	echo '</td>
			</tr></table>';

?>