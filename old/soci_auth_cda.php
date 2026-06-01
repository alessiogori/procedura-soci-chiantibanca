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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
if (!isset($_GET['auth']) OR ($_GET['auth'] != '1') )
	{
	    echo 'Accesso non consentito !!';
}
else 
	{
	    
	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';

?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-warning"><h3>AREA RISERVATA UFFICIO SOCI</h3>
	REPORT E PROSPETTI CDA</div>
</div>


<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%" >
	<tr>
		<td valign="top">


        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Content Row 1 -->
          <div class="row">

            <!-- PROSPETTO PER DELIBERA CDA -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">PROSPETTO CDA
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <a href="stats/repcda_prospetto_consiglio.php" target="_blank" title="Statistiche banca" style="text-decoration: none;"> 
                   <h4 class="card-title" style="color:#FFFFFF;">Da inserire in delibera</h4></a>
                   <p class="card-text">Grafici e tabelle per copia-incolla CDA</p> 
              </div>
              </div>
            </div>

            <!-- REPORT GENERALE -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <a href="stats/rep/_report.php?periodo=202001" target="_blank" title="Report Ammissioni" style="text-decoration: none;"> 
                   <h4 class="card-title" style="color:#FFFFFF;">Report Generale Banca</h4></a>
                   <p class="card-text">Completo</p> 
              </div>
              </div>
            </div>            

            <!-- REPORT AMMISSIONI -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">AMMISSIONI E USCITE
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <a href="stats/repcda_ammissioni.php?annomese=" target="_blank" title="Report Ammissioni" style="text-decoration: none;"> 
                   <h4 class="card-title" style="color:#FFFFFF;">Report per Area</h4></a>
                   <p class="card-text">(con esportazioni di dettaglio)</p> 
              </div>
              </div>
            </div>    

            <!-- ASSEMBLEA - RILASCIO BUONI PER OLIO -->
            <!--
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">ASSEMBLEA
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <a href="stats/assemblea_buoni.php" target="_blank" title="Statistica rilascio buoni" style="text-decoration: none;"> 
                   <h4 class="card-title" style="color:#FFFFFF;">Rilascio buoni Olio</h4></a>
                   <p class="card-text">Statistica</p> 
              </div>
              </div>
            </div>
            -->
            
            <?php
				//////////////////////////////////////////////////////////////////
				// Assemblea - Conteggio stampe Moduli
				//////////////////////////////////////////////////////////////////
                
                    // Totale moduli				   
				    $contenuto_moduli = '';
				    $select_mod1 = "SELECT  count(*) as qta, 
				                            modellostampato as modulo, 
				                            ipstampa, 
				                            datastampa, 
				                            cag,
				                            filiale
									FROM tab_log_modelli 
									group by modellostampato, ipstampa, datastampa
				                    " ; 
				    $qry_mod1 = mysqli_query($connection, $select_mod1);
				    $myfilemoduli = fopen("tmp/assemblea_stats_moduli.csv", "w");
				    $contenuto_moduli .= "INSERIRE NEL FILE O:\COMUNITY BANKING\_Statistiche\LogModelli.xls\n";
				    $contenuto_moduli .= "qta;modulo;ip;data;cag;filiale\n";
				    while($mod1 = mysqli_fetch_array($qry_mod1)){ 
				        $contenuto_moduli .= $mod1['qta'].";"
				                            .$mod1['modulo'].";"
				                            .$mod1['ipstampa'].";"
				                            .$mod1['datastampa'].";"
				                            .$mod1['cag'].";"
				                            .$mod1['filiale']."\n";
					}
					
                    fwrite($myfilemoduli, $contenuto_moduli);
                    fclose($myfilemoduli);
                    
                    // ---------- MUTUA ------------
                    $connection_mutua = mysqli_connect("localhost", "uasdn93n", "YFYQDQrldfIycbPS", "mutua");
				    $contenuto_moduliM = '';
				    $select_mod1M = "SELECT  count(*) as qta, 
				                            modellostampato as modulo, 
				                            ipstampa, 
				                            datastampa, 
				                            cag,
				                            filiale
									FROM tab_log_modelli 
									group by modellostampato, ipstampa, datastampa
				                    " ; 
				    $qry_mod1M = mysqli_query($connection_mutua, $select_mod1M);
				    $myfilemoduliM = fopen("tmp/assemblea_stats_moduli_mutua.csv", "w");
				    $contenuto_moduliM .= "INSERIRE NEL FILE O:\COMUNITY BANKING\_Statistiche\LogModelli.xls\n";
				    $contenuto_moduliM .= "qta;modulo;ip;data;cag;filiale\n";
				    while($mod1M = mysqli_fetch_array($qry_mod1M)){ 
				        $contenuto_moduliM.= $mod1M['qta'].";"
				                            .$mod1M['modulo'].";"
				                            .$mod1M['ipstampa'].";"
				                            .$mod1M['datastampa'].";"
				                            .$mod1M['cag'].";"
				                            .$mod1M['filiale']."\n";
					}
					
                    fwrite($myfilemoduliM, $contenuto_moduliM);
                    fclose($myfilemoduliM);                    
                    
                    
				?>
				
            <!-- ASSEMBLEA - STATISTICHE STAMPE MODULI -->		    
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">MODULI
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <h4 class="card-title" style="color:#FFFFFF;">Log stampe</h4>
                   <p class="card-text">
                       <i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="tmp/assemblea_stats_moduli.csv">Scarica il dettaglio Banca</a>
                       <br>
                       <i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="tmp/assemblea_stats_moduli_mutua.csv">Scarica il dettaglio Mutua</a>
                   </p> 
              </div>
              </div>
            </div>	
        
            
            <!-- FINE ULTIMO DIV -->
          </div>

        </td>
    </tr>
</table>


<?php

// fine ELSE
}
?>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>
