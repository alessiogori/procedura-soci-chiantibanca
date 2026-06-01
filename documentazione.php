<?php
// *****************************************************************************
// Portale ChiantiBanca
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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<!-- Begin Page Content -->
<div class="container-fluid">

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">DOCUMENTAZIONE</h4>
    </div>

  <!-- BANCA -->
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="60%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<td align="center" style="font-size: 16px;width:48%;vertical-align:top;">Chianti BANCA<br></td>
			</tr>
		</thead>
		<tbody>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-clipboard-list fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="statuto.php">Statuto ChiantiBanca</a>
				</td>
			</tr>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-map-marked-alt fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="docs/processi.html" target="_blank">Schema processi ESCLUSIONE - CESSIONE A BANCA - MORTE (e relative liquidazioni)
				</td>
			</tr>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-map-marked-alt fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="https://www.google.com/maps/d/viewer?mid=16N-nkxAZbAaziIdNUicNRS4yPwrYopUE&ll=43.47812011571083%2C11.31080859843748&z=9" target="_blank">Distribuzione territoriale delle Filiali</a>
				</td>				
			</tr>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-video fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="video.php">Video Formazione Portale Socio
				</td>
			</tr>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="https://chiantibanca.worktogether.it/views/docArt/docDownload.asp?DocId=16789&open=Y&preview=" target="_blank">Manuale Soci SicraWeb<a>
				</td>
			</tr>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="docs/manuale_eventi.pdf" target="_blank">Manuale Eventi<a>
				</td>
			</tr>

			<tr class="table-secondary">   
				<td style="text-align:left;">

<b>RIFERIMENTI INTERNI</b>

<br><br>

<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
<a class="text-success" href="https://chiantibanca.worktogether.it/ODS.asp?jump=views/docArt/DocArtCompleteView.asp&DOCFId=14600&idcat=0&" target="_blank">ODS 103/2020</a><br><span style="color:lightgray;"> <i> Nuova modalità di ammissione a Socio ed iniziative riservate agli aspiranti soci under 30 (rateizzazione U30 3x11 e Donazione U30)</i></span>

<br><br>

<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
<a class="text-success" href="https://chiantibanca.worktogether.it/ODS.asp?jump=views/docArt/DocArtCompleteView.asp&DOCFId=16552&idcat=0&" target="_blank">ODS 072/2022</a><br><span style="color:lightgray;"> <i> Proroga deroga sottoscrizione quote sociali per i Soci ChiantiMutua (ulteriore proroga con Nota M22084 del 28.12.2022)</i></span>

<br><br>

<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
<a class="text-success" href="https://chiantibanca.worktogether.it/views/docArt/docDownload.asp?DocId=17604&open=Y&preview=" target="_blank">ODS 050/2023</a><br><span style="color:lightgray;"> <i> Variazione categoria Soci Under30 in Under35</i></span>

<br><br>

<i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
<a class="text-success" href="https://chiantibanca.worktogether.it/views/docArt/docDownload.asp?DocId=17532&open=Y&preview=" target="_blank">ODS 040/2023</a><br> <span style="color:lightgray;">"... <i>In occasione della sottoscrizione quote sociali della Banca, deve essere effettuata una valutazione complessiva sulla posizione dell’aspirante socio e formalizzata in un KYC di tipo 3, firmato dallo stesso aspirante socio, il quale deve descrivere l’operatività che intende realizzare con la banca. Il questionario, completo del quadro H validato dal Titolare di Filiale, deve essere inviato all’Ufficio Soci unitamente alla domanda.</i>"</span><br>

				</td>
			</tr>

</tbody>
	</table>
      </div>
    </div>



<!-- MUTUA -->
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="60%" cellspacing="0">
		<thead>
			<tr class="table-success">
				<td align="center" style="font-size: 16px;width:48%;vertical-align:top;">Chianti MUTUA<br></td>
			</tr>
		</thead>
		<tbody>
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-clipboard-list fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="https://www.chiantimutua.it/comefunziona">Come funzione ChiantiMutua</a>
				</td>
			</tr>			
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-search fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="https://www.chiantimutua.it/convenzionati">Ricerca Convenzionati</a>
				</td>
			</tr>			
			<tr class="table-secondary">   
				<td style="text-align:left;">
				<i class="fas fa-clipboard-list fa-1x text-lightgray-300 col-auto"></i>
					<a class="text-success" href="https://www.chiantimutua.it/statuto">Statuto ChiantiMutua</a>
				</td>
			</tr>
		</tbody>
		</table>
      </div>
    </div>






  </div>
</div>
<!-- /.container-fluid -->

<br/><br>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
