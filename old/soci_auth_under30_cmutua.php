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
	UNDER 30 & ChiantiMutua</div>
</div>


<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%" >
	<tr>
		<td valign="top">


        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Content Row 1 -->
          <div class="row">

            <!-- STATISTICHE -->

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali - Giovani
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="stats/giovani_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Giovani 17-30 anni</h4>
                  </a>
                  <p class="card-text">elenchi per età</p> 
              </div>
              </div>
            </div> 
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Controllo PAC
			//////////////////////////////////////////////////////////////////   
			?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">CHECK
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="check_vari.php?scelta=pac" target="_blank" title="Check pagamenti PAC Giovani Soci" style="text-decoration: none;" >
                    <h4 class="card-title" style="color:#FFFFFF;">Verifica PAC</h4>
                  </a>
                  <p class="card-text">
                  		Per Under 30 e ChiantiMutua
                      <!-- <i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="check_vari_csv.php?scelta=pac">Scarica il dettaglio</a></p> -->
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
