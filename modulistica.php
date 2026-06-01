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

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

if (get_browser_name($_SERVER['HTTP_USER_AGENT']) == "Internet Explorer")
	{$imgext = "jpg";}
else
	{$imgext = "png";}

function current_url()
{
    $url      = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $validURL = str_replace("&", "&amp", $url);
    return $validURL;
}
// echo "page URL is : ".current_url();
// echo $_SERVER['HTTP_REFERER'];


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


// -----------------------------------------------------
// Se è un socio BANCA presento solo il relativi modelli
// -----------------------------------------------------
if (($_GET['mutua'] == 'no') OR ($_GET['mutua'] == '')) {
    
// Estrazione dei modelli 
$select = "	SELECT rif, Codice, Descrizione, NomeFile
			FROM tab_modelli
			WHERE status = 'S'
			AND rif = 'BANCA'
			ORDER BY rif, Codice ";
$querydati = mysqli_query($connection, $select); 

/*
if ( (isset($_GET['socio'])) && (isset($_GET['cag'])) ) 
{echo 'pee'; }
else
{
*/    
if (isset($_GET['socio'])) {$socio = "Socio <b>".$_GET['socio']."</b>&nbsp;(cag ".$_GET['cag'].")";} else {$socio = '';} 

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">MODULISTICA</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="dataTable" width="60%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<td align="center" style="font-size: 16px;width:48%;vertical-align:top;">
								'.$socio.'
								<br>
				</td>
			</tr>
		</thead>
		<tbody>
';	

while($datimodelli=mysqli_fetch_array($querydati)){ 

		// **** se l'utente arriva dalla Scheda Socio
		if (
			(strpos($_SERVER['HTTP_REFERER'], 'sqldati_schedasocio.php')) OR 
			(strpos($_SERVER['HTTP_REFERER'], 'schedasocio.php')) 
				)
			
		{

			// Estrazione del luogo
			$selectluogo = " SELECT  luogo
							FROM tab_psw as p, sds_soci as c
							WHERE c.nag = ".$_GET['cag']."
							AND c.filiale_capofila = CAST(p.filiale AS UNSIGNED)
							GROUP BY luogo"; 
			$queryluogo = mysqli_query($connection, $selectluogo); 
			while($datiluogo=mysqli_fetch_array($queryluogo)){ 
				$luogo = $datiluogo['luogo'];
			}

			if(in_array($datimodelli['Codice'], array('SO99','SO03','SO05','SO07','SO08','SO13')) )
			//if ($datimodelli['Codice'] = ('SO99','SO03') 
			        {$stilecolore = ' style="color:white;" ';
			         $visibilita = '<i class="fas fa-lock-open fa-1x text-gray-300 col-auto" title="Modello libero"></i>';
			        }
			elseif (in_array($datimodelli['Codice'], array('SO01','SO02')) )
			        {$stilecolore = 'class="text-warning" ';
			         $visibilita = '<i class="fas fa-lock fa-1x text-gray-300 col-auto" title="Modello con password di accesso"></i>';
			        }
			elseif (in_array($datimodelli['Codice'], array('SO06')) )
			        {$stilecolore = ' style="color:white;" ';
			         $visibilita = '<i class="fas fa-envelope fa-1x text-gray-300 col-auto" title="Modello con invio mail"></i>';
			        }
			else
					{
					$stilecolore = ' style="color:white;" ';
			         $visibilita = '<i class="fas fa-lock-open fa-1x text-gray-300 col-auto" title="Modello libero"></i>';
			        }
			        
			if ( ($datimodelli['Codice'] == 'SO07') OR ($datimodelli['Codice'] == 'SO08') OR ($datimodelli['Codice'] == 'SO09') )
			{$action = '&action=print';}
			else
			{$action = '';}

			if ( ($datimodelli['Codice'] == 'AS05') )
			{$as05 = ' ---> <i>da stampare solo in caso vengano a mancare le bottiglie di olio</i>';}
			else
			{$as05 = '';}

			if ($_GET['user'] != 'soci')  
			{$so03 = '';}
			else
			{$so03 = '<tr><td><a '.$stilecolore.' href="modulistica/SO03_cessione_da_Socio_a_Socio.php?modello=SO03&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.'" target="_blank"><b>SO03</b> - cessione_da_Socio_a_Socio</a></td></tr>
								<tr><td><a '.$stilecolore.' href="modulistica/SO05_cessione_da_Socio_a_NON_Socio.php?modello=SO05&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.'" target="_blank"><b>SO05</b> - cessione_da_Socio_a_NON_Socio</a></td></tr>';
			}

			// Presento il countdown di avvicinamento all'attivazione del modello
			/*
			if ($datimodelli['Codice'] == 'AS00') 
			{$cd = "<script type='text/javascript'>
					CountDownTimer('04/04/2022 09:15:00 AM', 'countdown');
					function CountDownTimer(date, id) {
					     var end = new Date(date);

					     var _second = 1000;
					     var _minute = _second * 60;
					     var _hour = _minute * 60;
					     var _day = _hour * 24;
					     var timer;

					     function showRemaining() {
					         var now = new Date();
					         var distance = end - now;
					         if (distance < 0) {

					             clearInterval(timer);
					             document.getElementById(id).innerHTML = 'EXPIRED!';

					             return;
					         }
					         var days = Math.floor(distance / _day);
					         var hours = Math.floor((distance % _day) / _hour);
					         var minutes = Math.floor((distance % _hour) / _minute);
					         var seconds = Math.floor((distance % _minute) / _second);

					         document.getElementById(id).innerHTML = days + ' giorni ';
					         document.getElementById(id).innerHTML += hours + ' ore ';
					         document.getElementById(id).innerHTML += minutes + ' min ';
					         document.getElementById(id).innerHTML += seconds + ' sec';
					     }

					     timer = setInterval(showRemaining, 1000);
					 }
					 </script>";

					$cd .= '&nbsp;&nbsp;&nbsp;&nbsp;<small><span id="countdown">All\'avvio della raccolta deleghe mancano ancora </span></small>';

					
}
			else
			{$cd = '';}
			*/
			$nuovosocio = '';
			$avvertenza = '';
			$rigamodello = '
			<tr class="table-secondary">   
				<td style="text-align:left;">
				'.$datimodelli['rif']
				.$visibilita.'
					<a '.$stilecolore.' href="'.$datimodelli['NomeFile'].'?modello='.$datimodelli['Codice'].'&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.'" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
					'.$as05.'
				</td>
			</tr>';
		}
		else
		{
			// Altrimenti permetto di inserire una nuova richiesta a Socio, ma non di aprire altri documenti
			$nuovosocio = '';
			// $nuovosocio =	'<img src="img/ico_modulistica.png" align="absmiddle">&nbsp;<a href="#" target="_blank">Richiesta di ammissione a Socio</a><br>';
			$avvertenza = '';
			$rigamodello = '
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<img src="img/ico_modulistica.png" align="absmiddle" title="Modello precompilato">
					'.$datimodelli['Codice'].' - '.$datimodelli['Descrizione'].' <small style="color:gray;"> >> Entra dalla <a href="schedasocio.php">Scheda Socio</a> per attivare i moduli precompilati</small>
				</td>
			</tr>';

		}

echo '								
								'.$avvertenza.'
								'.$rigamodello.'
								';

}

// RIGA PER QRCODE
echo '		<tr class="table-secondary">
				<td style="text-align:left;">BANCA
					<i class="fas fa-qrcode fa-1x text-gray-300 col-auto" title="Immagini QRCODE personalizzate"></i>
					<a '.$stilecolore.' href="modulistica/QRCODE.php?cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&action=" target="_blank">Genera set QRCODE</a>
				</td>
			</tr>';


echo $so03;

echo '		</tbody>
	</table>';

/*
echo '<h6>Modelli Word da CSD</h6>';

		<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
		<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=+Nf8T7CD5qQv4b5GoiCEqIirOlQZzNcekFka98ARJS+XhDQU5zep2makgi3WErr3&apl=105&codabi=08673" target="_blank" style="color:white;">SO03 Cessione da socio PF a socio</a>
        <br>
		<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
		<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=3FjQfHpvGNIOJgntXPMlUILeoarO8asHRwobNWHrZdyitWvObUmQAmWWQAxzoIzS&apl=105&codabi=08673" target="_blank" style="color:white;">SO04 Cessione da socio PG a socio</a>
        <br>
        <i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
        <a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=PJ8A6zA4+34Tttt+i0JwOH1HbaEcUh/O3xsiK/GagNrGs3d4J7SpmuWurZFvilWE&apl=105&codabi=08673" target="_blank" style="color:white;">SO05 Cessione da socio PF a non socio</a>
        <br>
        <i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
		<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=W6qqpq6sOvq1yVti2eczqfEVzPNi3uOzVrIQr1r8520bBe0ACwe6m2qmlNG0V1Ey&apl=105&codabi=08673" target="_blank" style="color:white;">SO06 Cessione da socio PG a non socio</a>
		<br>

echo '
		<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
		<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=soSk1tPTNi/V1C1TKb+aTh4+KH6AM0OVpEq+V0vKZJhGIuC0s+guAdQgGkPR3JY8&apl=105&codabi=08673" target="_blank" style="color:white;">SO07 - Intestazione azioni eredi</a>
        <br>
		<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
        <a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=he+d9UkvIcHSl3YUf4wrY4Arx+UK9ogMtRebfcoyuhxCmQ+fiOXJcC8PiB+gxX19&apl=105&codabi=08673" target="_blank" style="color:white;">SO08 - Liquidazione azioni eredi</a>
		<br>
    	<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
		<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=lZlHMGsMZRwEnJqqBg3tKcQ0GYyYs76PIXPB86KQ6HkVJAI2BIBtSWRVPDNBQ+5L&apl=105&codabi=08673" target="_blank" style="color:white;">SO13 Modifica del Rappresentante nella compagine sociale</a>		';
			
<tr class="table-secondary">   
				<td style="text-align:left;">
					<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
					<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=iBHUT/wE7S3BULZz7dUDfEASf65EoIDM6pTUn6lHtkVWC87V2gpi9KUNuSY3BAYf&apl=105&codabi=08673" target="_blank" style="color:white;">SO10 Recesso per mancanza requisiti art 6 PG</a>
				</td>
			</tr>
			</tr>
<tr class="table-secondary">   
				<td style="text-align:left;">
					<i class="fas fa-eye fa-1x text-gray-300 col-auto" title="Modello con CSD"></i>
					<a href="https://www.servizi.csdportal.it/cssb/CSD.SioDoc/SIOD/HttpHandlers/DirectAccess.ashx?flow=CK+WL/Selt64h7AgKp9KsiCBsHD41uaE1tyM4dmPwGG0NTEwMT+sVcVJo3eM63NQ&apl=105&codabi=08673" target="_blank" style="color:white;">SO11 Recesso per mancanza requisiti art 6 PF</a>
				</td>
			</tr>
			</tr>
*/			

echo '	
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

}
else
{

// ------------------------------------
// Modelli relativi ai soci MUTUA
// ------------------------------------
    
// Estrazione dei modelli 
$select = "	SELECT rif, Codice, Descrizione, NomeFile
			FROM tab_modelli
			WHERE status = 'S'
			AND rif = 'MUTUA'
			ORDER BY Codice ";
$querydati = mysqli_query($connection, $select); 

if (isset($_GET['socio'])) {$socio = "Socio <b>".$_GET['socio']."</b>&nbsp;(cag ".$_GET['cag'].")";} else {$socio = '';} 

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">MODULISTICA MUTUA</h4>
      <small class="m-2 text-success">ATTENZIONE: per accedere al sito internet www.chiantimutua.it è necessario usare un browser locale, diverso da Internet Explorer Citrix</small>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="dataTable" width="60%" cellspacing="0">
		<thead>
			<tr class="table-success">
				<td align="center" style="font-size: 16px;width:48%;vertical-align:top;">
								'.$socio.'
								<br>
				</td>
			</tr>
		</thead>
		<tbody>
';	

while($datimodelli=mysqli_fetch_array($querydati)){ 

		// **** se l'utente arriva dalla Scheda Socio
		if ((strpos($_SERVER['HTTP_REFERER'], 'mutua_schedasocio.php')) OR 
			(strpos($_SERVER['HTTP_REFERER'], 'mutua_listaschedasocio.php')))
			
		{
			$luogo = 'San Casciano VDP';
			     
			if ( ($datimodelli['Codice'] == 'SO07') OR ($datimodelli['Codice'] == 'SO08') )
			{$action = '&action=print';}
			else
			{$action = '';}

			if(in_array($datimodelli['Codice'], array('001','002','003','004','005','006')) )
			        {$stilecolore = ' style="color:white;" ';
			        $visibilita = '<i class="fas fa-globe fa-1x text-gray-300 col-auto" title="Modello su sito internet"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="https://www.chiantimutua.it/modulistica" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			        
			elseif(in_array($datimodelli['Codice'], array('009','010','011','013','099','901')) )
                    {$stilecolore = ' style="color:white;" ';
			         $visibilita = '<i class="fas fa-lock-open fa-1x text-gray-300 col-auto" title="Modello libero"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="modulistica/'.$datimodelli['NomeFile'].'?tessera='.$_GET['tessera'].'&modello='.$datimodelli['Codice'].'&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.'" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			else    
				
			        {$stilecolore = 'class="text-warning" ';
			         $visibilita = '<i class="fas fa-lock fa-1x text-gray-300 col-auto" title="Modello con password di accesso"></i>';
			        $avvertenza = '';
        			$rigamodello = '
        			<tr class="table-secondary">   
        				<td style="text-align:left;">
						'.$datimodelli['rif']
						.$visibilita.'
        					<a '.$stilecolore.' href="modulistica/'.$datimodelli['NomeFile'].'?tessera='.$_GET['tessera'].'&modello='.$datimodelli['Codice'].'&cag='.$_GET['cag'].'&socio='.urlencode($_GET['socio']).'&idsocio='.$_GET['idsocio'].'&luogo='.$luogo.$action.'" target="_blank"><b>'.$datimodelli['Codice'].'</b> - '.$datimodelli['Descrizione'].'</a>
        				</td>
        			</tr>';
			        }
			
		}
		else
		{

			$avvertenza = '';
			$rigamodello = '
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<img src="img/ico_modulistica.png" align="absmiddle" title="Modello precompilato">
					'.$datimodelli['Codice'].' - '.$datimodelli['Descrizione'].' <small style="color:gray;"> >> Entra dalla <a href="schedasocio.php">Scheda Socio</a> per attivare i moduli precompilati</small>
				</td>
			</tr>';

		}

echo '								
								'.$avvertenza.'
								'.$rigamodello.'
								';


}


echo '		</tbody>
	</table>';  
}	

// TEST PER DATAMATRIX
/*
echo '&nbsp;<a href="http://10.119.192.46:8080/soci/modulistica/_testdmx.php?modello=DMX&cag=03033148&socio=FEDI+ALESSIO+&idsocio=3060086&luogo=Pistoia" target="_blank">_</a>';
echo '<br>';
echo '&nbsp;<a href="http://10.119.192.46:8080/soci/modulistica/_testdmx2.php?modello=DMX&cag=03033148&socio=FEDI+ALESSIO+&idsocio=3060086&luogo=Pistoia" target="_blank">_</a>';
*/
echo '	
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

?>
<br/><br>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>



