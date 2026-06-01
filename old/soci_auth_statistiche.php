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
	STATISTICHE</div>
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
			// Statistiche Generali Banca
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="statistiche.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Dati Generali</h4>
                  </a>
                  <p class="card-text">Fasce e distribuzione</p> 
              </div>
              </div>
            </div>

            <?php
      //////////////////////////////////////////////////////////////////
      // Statistiche Situzione Generale
      //////////////////////////////////////////////////////////////////   
      ?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/situazione.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Situazione</h4>
                  </a>
                  <p class="card-text">per Aree e Filiali</p> 
              </div>
              </div>
            </div>  

            <?php
			//////////////////////////////////////////////////////////////////
			// Andamentale
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="stats/andamentale_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;">Andamentale</h4>
                  </a>
                  <p class="card-text" style="color:gray;">Entrate e Uscite</p> 
              </div>
              </div>
            </div>
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Situazione liquidazioni
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/liquidazioni_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Situazione liquidazioni</h4>
                  </a>
                  <p class="card-text">Fatte e da fare</p> 
              </div>
              </div>
            </div>
            

            <?php
			//////////////////////////////////////////////////////////////////
			// Situazione Volumi
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="stats/volumi_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;">Volumi</h4>
                  </a>
                  <p class="card-text" style="color:gray;">per Aree e Filiali</p> 
              </div>
              </div>
            </div>
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Ammissioni a Socio
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/ammissioni_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Ammissioni</h4>
                  </a>
                  <p class="card-text">per Aree e Filiali</p> 
              </div>
              </div>
            </div>            

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali per le Cessioni a Banca
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/cessioni_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Cessione a Banca</h4>
                  </a>
                  <p class="card-text">per Aree e Filiali</p> 
              </div>
              </div>
            </div>
 
            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali per le esclusioni
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/esclusioni_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Esclusioni</h4>
                  </a>
                  <p class="card-text">per art.6, art.14 e Sofferenze</p>
              </div>
              </div>
            </div>            
   
            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali per i decessi
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/eredi_grafico.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Decessi</h4>
                  </a>
                  <p class="card-text">per Aree e Filiali</p> 
              </div>
              </div>
            </div>            

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali - Suddivisione fasce senza richieste in corso
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="stats/fasce_senzarichieste.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;">Soci senza richieste</h4>
                  </a>
                  <p class="card-text" style="color:gray;">per fasce di quote</p> 
              </div>
              </div>
            </div>               

            <?php
			//////////////////////////////////////////////////////////////////
			// Statistiche Generali - Classi di anzianità
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">STATISTICHE
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="stats/fasce_classisocio.php" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Classi anzianità</h4>
                  </a>
                  <p class="card-text">per età nella compagine</p> 
              </div>
              </div>
            </div> 
            
            <?php
      //////////////////////////////////////////////////////////////////
      // Statistiche Generali - Motivazioni IN / OUT
      //////////////////////////////////////////////////////////////////   
      ?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-warning mb-3" style="max-width: 20rem;">
                <div class="card-header">MOTIVAZIONI 
                  <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                  <a href="motivazioni_check.php?filiale=999" target="_blank" title="Motivazioni" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;">Ingressi e Uscite</h4>
                  </a>
                  <p class="card-text">dalla compagine</p> 
              </div>
              </div>
            </div> 
            
            <?php
      //////////////////////////////////////////////////////////////////
      // Ammissioni di Soci
      //////////////////////////////////////////////////////////////////   
      ?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">LISTA
                  &nbsp;<i class="fas fa-list-alt fa-1x text-gray-300"></i>
                  <i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                   <a href="lista_ammissioni.php" target="_blank" title="Ammissioni a Socio" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Ammissioni a Socio</h4></a>
              </div>
              </div>
            </div>     
  
            <?php
      //////////////////////////////////////////////////////////////////
      // Trasferimenti tra Soci
      //////////////////////////////////////////////////////////////////   
      ?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-success mb-3" style="max-width: 20rem;">
                <div class="card-header">LISTA
                  &nbsp;<i class="fas fa-list-alt fa-1x text-gray-300"></i>
                  <i class="fab fa-scribd fa-1x text-gray-300"></i>
                </div>
                <div class="card-body">
                   <a href="lista_trasferimenti.php" target="_blank" title="Trasferimenti tra Soci/Non Soci" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Trasferimenti tra Soci</h4></a>
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

<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
