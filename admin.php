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

// Calcolo la variabile data-ora per generare la password al link di Edit Password FiIliali
$urlpsw = date('YmdH');

/*
if (!isset($_POST['psw']))
	{
	echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:gray;"><i>AREA RISERVATA ADMINISTRATOR </i></span></h2>	</center>';
	echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="admin.php" method="post" onsubmit="return ray.ajax()">
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
elseif 
	($_POST['psw'] == "cicalo")
	{
*/

if ($_COOKIE['filiale_id'] != 999) {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
}

/*
elseif 
    ($_POST['psw'] == "modrapp")
    {

    $cagD = $_POST['cagD']; 
    $cagR = $_POST['cagR'];         
    $nomeR = $_POST['nomeR'];

    $updateRapp = "UPDATE tab_soci_as37 SET cagDelegato = '".$cagR."',int1Delegato = '".$nomeR."'
                    WHERE cag = ".$cagD;
    $queryupdate = mysqli_query($connection, $updateRapp);
}
*/

else 
    {

//    echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>
<table align="center" width="40%">
		<tr ><td colspan="3"><br></td></tr>
		<tr ><td colspan="3" align="center"> <div class="alert alert-dismissible alert-danger"><h3>Admin Center</h3></div>  </td></tr>
		<tr ><td colspan="3"><br></td></tr>

		<!-- FUNZIONI ADMIN -->
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:red;text-align: left;">
                    <a href="crea_sds_soci.php" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-database fa-1x text-lightgray-300 col-auto"></i>Aggiornamento tabelle Sadas MySql
                    </a>
                </button>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:green;text-align: left;">
                    <a href="admin_upload.php" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-cloud-upload-alt fa-1x text-lightgray-300 col-auto"></i>Caricamento files
                    </a>
                </button>
            </td>                
		</tr>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#0F79BD;text-align: left;">
                    <a href="https://webmail.pec.actalis.it/" style="color:white;text-decoration: none;" target="_blank" title="User: soci@pecchiantibanca.it - Psw: ChiantiBanca!S@ci23">
                    <i class="fas fa-mail-bulk fa-1x text-lightgray-300 col-auto"></i>WebMail Actalis PEC Soci
                    </a>
                </button>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <form class="form-inline" action="https://chiantibanca.worktogether.it/Flussi-Informativi.asp" method="post" onsubmit="return ray.ajax()" target="_blank">
                    <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#D5EAD9;color:black;text-align: left;">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Invio Flussi CDA
                    </button>
                </form>
            </td>            
        </tr>        
        <tr>
            <td align="left" width="48%">
                <form class="form-inline" action="http://10.197.139.22:8080/protocollo/login.php" method="post" onsubmit="return ray.ajax()" target="_blank">
                    <input type="hidden" name="u" value="asoci">    
                    <input type="hidden" name="p" value="asoci">
                    <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Protocollo
                    </button>
                </form>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <a href="news.php" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-newspaper fa-1x text-lightgray-300 col-auto"></i>News Home Aggiornamento
                    </a>
                </button>
            </td>
        </tr>
        <tr>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <a href="http://10.197.139.22:8080/documentale_Posta/90soci" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Invio Posta
                    </a>
                </button>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <a href="http://10.197.139.22:8080/documentale_Archivio/Posta/90soci" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Archivio Posta
                    </a>
                </button>                 
            </td>                
		</tr>	
        <tr>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <a href="modulistica/dmx_esclusioni_frontespizio.php" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-qrcode fa-1x text-lightgray-300 col-auto"></i>DMX per Esclusioni (frontespizio)
                    </a>
                </button>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:#F06F5F;text-align: left;">
                    <a href="modulistica/dmx_cessionibanca_frontespizio.php" style="color:white;text-decoration: none;" target="_blank">
                    <i class="fas fa-qrcode fa-1x text-lightgray-300 col-auto"></i>DMX per Cessioni Banca (frontespizio)
                    </a>
                </button>
            </td>                
        </tr>           	
        <tr>
            <td align="left" width="48%">
                <form class="form-inline" action="https://crp-acffiorentina.vivaticket.com/login?lang=it" method="post" onsubmit="return ray.ajax()" target="_blank">
                    <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:darkmagenta;color:white;text-align: left;" title="User: CHIANTIB - Psw: Soci1926CB">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Biglietteria Fiorentina
                    </button>
                </form>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td align="left" width="48%">
                <form class="form-inline" action="routines/basic/index.php" method="post" onsubmit="return ray.ajax()" target="_blank">
                    <button type="submit" class="btn btn-lg btn-block mb-2" style="background-color:darkmagenta;color:white;text-align: left;">
                    <i class="fas fa-share-square fa-1x text-lightgray-300 col-auto"></i>Gestione Eventi
                    </button>
                </form>
            </td>                
        </tr>               
<!--		<tr>
			<td><img src="img/edit.png" width="30"></td>
			<td align="left"><a href="admin_stats_portale.php?"><h3 style="color:#006028;">Statistiche consultazione Portale</h3></a></td>
		</tr>
-->
</table>
<br><br>
<!--
<table align="center" width="40%">
		<tr >
		  <td align="left">
		  <span style="color:#00BC8C;">Modifica Rappresentante</span><br>
        	<form class="form-inline" action="admin.php" method="post" onsubmit="return ray.ajax()">
                <small>CAG DITTA</small> &nbsp; <input type="text" class="form-control form-control-sm" name="cagD" id="cagD" size=9>
        		&nbsp;
        		<small>CAG Rapp</small> &nbsp; <input type="text" class="form-control form-control-sm" name="cagR" id="cagR" size=9>
        		&nbsp; 
        		<small>Nominativo</small> &nbsp; <input type="text" class="form-control form-control-sm" name="nomeR" id="nomeR" size=30>
        		<input type="hidden" class="form-control form-control-sm" name="psw" id="psw" value="modrapp"> &nbsp;
        		<button type="submit" class="btn btn-success mb-2" title="Aggiorna"><i class="fas fa-recycle fa-1x text-lightgray-300 col-auto"></i></button>
    		</form>
		  </td>
		</tr>
</table>
-->

<!-- TEST STAMPA ASSEMBLEA
<a href="http://10.119.192.46:8080/soci/modulistica/AS00_completo.php?modello=AS00&cag=03265370&socio=TURI GIAMPAOLO&idsocio=47362&luogo=Pistoia" target="_blank">Test AS00 SOCIO BANCA + MUTUA</a>
<br>
<a href="http://10.119.192.46:8080/soci/modulistica/AS00_completo.php?modello=AS00&cag=03001756&socio=FEDI ALBA&idsocio=42741&luogo=Pistoia" target="_blank">Test AS00 solo SOCIO BANCA PF</a>
<br>
<a href="http://10.119.192.46:8080/soci/modulistica/AS00_completo.php?modello=AS00&cag=03145334&socio=TOSCOFILATI S.R.L.&idsocio=42719&luogo=Montemurlo" target="_blank">Test AS00 solo SOCIO BANCA AZIENDA</a>
<br>
<a href="http://10.119.192.46:8080/soci/modulistica/AS05_coupon_ritiro_olio.php?modello=AS05&cag=03063244&socio=FEDI GIANNA&idsocio=42482&luogo=Pistoia" target="_blank">Test AS05 COUPON OLIO</a>
<br>
-->

<center>
<b>Link da aprire in "incognito" per fare test</b> 
<br>

<a href="http://10.197.139.22:8080/soci/_auth.php?f=995&u=00283&e=rossella.centineo@chiantibanca.it">AREA</a>
<br>

<a href="http://10.197.139.22:8080/soci/_auth.php?f=61&u=00444&e=cristiano.mazzei@chiantibanca.it">FILIALE</a>
<br>

<a href="http://10.197.139.22:8080/soci/_auth.php?f=100&u=00379&e=stefano.matteucci@chiantibanca.it">CENTRO IMPRESE</a>
</center>


<?php
	}
?>
