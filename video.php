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
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    
<!-- Content Row 0 -->
<center>
<div class="row">

<!-- TITOLO PAGINA -->
<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3>Video formazione Portale Socio</h3>
	<small>Doppio click per allargare il video a pieno schermo</small>
    </div>  
</div>

</div> <!-- /. Fine Content Row 0 -->
</center>

<!-- Content Row 1 -->
<div class="row">	

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Introduzione<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/00_intro.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Ricerca<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/01_ricercasocio.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>
            
            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Scheda Socio<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/02_schedasocio.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>            
            
            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Esclusione<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/03_esclusione.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>  

</div> <!-- /. Fine content Row 1 -->
<br>
<!-- Content Row 2 -->
<div class="row">	

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Decesso<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/04_decesso.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Rateizzazione<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/05_rateizzazione.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>
            
            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">F.A.Q.<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/06_faq.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>            
            
            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Documentazione<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/07_documentazione.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>  

</div> <!-- /. Fine content Row 2 -->
<br>
<!-- Content Row 3 -->
<div class="row">	

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Stastistiche Filiale (1)<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/08_filiale_parte1.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Stastistiche Filiale (2)<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/09_filiale_parte2.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>

            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">Assemblea Soci<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <video width="300" controls>
                        <source src="/soci/video/as00_raccoltadeleghe.mp4" type="video/mp4">
                    </video>
             </div>
              </div>
            </div>
            
</div> <!-- /. Fine content Row 3 -->

</div> <!-- /. Fine Begin Page Content -->

</div>
<!-- /.container-fluid -->



	<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>

<!--    
<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" width="700"
        height="360" codebase="http://www.microsoft.com/Windows/MediaPlayer/">
   <param name="Filename" value="/soci/video/prova_audiopc.wmv">
   <param name="AutoStart" value="true">
   <param name="ShowControls" value="true">
   <param name="BufferingTime" value="2">
   <param name="ShowStatusBar" value="true">
   <param name="AutoSize" value="true">
   <param name="InvokeURLs" value="false">
   <embed src="/soci/video/prova_audiopc.wmv"
          type="application/x-mplayer2" autostart="1" enabled="1" showstatusbar="1"
          showdisplay="1" showcontrols="1" 
          pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" 
          CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,0,0" width="700" height="484"></embed>
</object>
-->
 