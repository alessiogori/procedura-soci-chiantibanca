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
if (!isset($_POST['psw']) OR ($_POST['psw'] != 'cicalo') )
	{
	echo '<div id="load" style="display:none;color:red;"><img src="page-loader.gif"><br>Loading... Please wait</div>';

	echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:gray;"><i>AREA RISERVATA DIREZIONE CHIANTIBANCA </i></span></h2>	</center>';
	echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="soci_auth.php" method="post" onsubmit="return ray.ajax()">
		  <div class="form-group mx-sm-3 mb-2">
		    <label for="psw" class="sr-only">Password</label>
		    <input type="password" class="form-control" name="psw" id="psw" placeholder="Password">
		  </div>
		  <button type="submit" class="btn btn-success mb-2">ACCEDI</button>
		</form>
	</td>
	</tr>
	</table>

	';
}
else 
	{

if ($_POST['psw'] == 'cicalo') {$idk = $_POST['psw'];}

	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';

?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-warning"><h3>Statistiche Generali Soci ChiantiBanca</h3>
	Area Ufficio Soci</div>
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
			// REPORT CDA
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">CDA
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <a href="soci_auth_cda.php?auth=1" target="_blank" title="Report CDA" style="text-decoration: none;"> 
                   <h4 class="card-title" style="color:#FFFFFF;">Report e Prospetti</h4></a>
                   <p class="card-text">&nbsp;</p> 
              </div>
              </div>
            </div>
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali Banca
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="soci_auth_statistiche.php?auth=1" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Analisi Dati</h4>
                  </a>
                  <p class="card-text">&nbsp;</p> 
              </div>
              </div>
            </div>

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali Banca
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-primary mb-3" style="max-width: 20rem;">
                <div class="card-header">UNDER 30 & CHIANTIMUTUA
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="soci_auth_under30_cmutua.php?auth=1" target="_blank" title="Statistiche Under 30" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Operatività e Dati</h4>
                  </a>
                  <p class="card-text">&nbsp;</p> 
              </div>
              </div>
            </div>

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali Banca
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">CHECK
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="soci_auth_check.php?auth=1" target="_blank" title="Check Soci" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Controllo Dati</h4>
                  </a>
                  <p class="card-text">&nbsp;</p> 
              </div>
              </div>
            </div>

        
            <!-- FINE ULTIMO DIV -->
          </div>

</td>
</tr>
<tr>
  <td>

<center>
<a class="btn btn-outline-success" style="text-decoration:none;" href="lista_domande.php" target="_blank">Lista Domande presenti</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="stats/situazione.php" target="_blank">Situazione Soci alla data odierna</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="stats/situazione_plafond.php" target="_blank">Situazione Plafond</a>
</center>

        </td>
    </tr>
</table>




<?php

// fine ELSE
}

?>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>

<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>

