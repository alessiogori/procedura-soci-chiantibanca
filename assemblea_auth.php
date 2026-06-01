<?php
// *****************************************************************************
// Portale ChiantiMutua
// Sviluppo e realizzazione: Alessio Fedi (2021)
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

// Definizioni
$dataassemblea 	= '2022-04-28';
$tipoassemblea 	= 'ORDINARIA';
$limitevoto 	= strtotime ( '-90 day' , strtotime ( $dataassemblea ) ) ;
$limitevoto = date ( 'd.m.Y' , $limitevoto );

     include("assemblea2022.php");

// -----------------------------------------------
// ACCESSO RISERVATO PER ATTIVITA' DI MANUTENZIONE
// -----------------------------------------------
/*
if (!isset($_POST['psw']))
	{
	echo '<div id="load" style="display:none;">Loading... Please wait</div>';

	echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:gray;"><i>AREA RISERVATA ASSEMBLEA SOCI</i></span></h2>	</center>';
	 echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="assemblea_auth.php" method="post" onsubmit="return ray.ajax()">
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
    include("assemblea2022.php");
    }
/*
/*
// -----------------------------------------------
// VECCHIO SISTEMA DI AUTENTICAZIONE E GESTIONE
// -----------------------------------------------
if (!isset($_POST['psw']))
	{
	echo '<div id="load" style="display:none;">Loading... Please wait</div>';

	echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:gray;"><i>AREA RISERVATA ASSEMBLEA SOCI</i></span></h2>	</center>';
	 echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="assemblea_auth.php" method="post" onsubmit="return ray.ajax()">
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
$idk = $_POST['psw'];

	echo '<div id="load" style="display:none;">Loading... Please wait</div>';

// ********************************************************
// ESTRAZIONE FILIALE
// ********************************************************
	$select_psw =	'SELECT 
						filiale, desc_filiale, psw
					 FROM tab_psw
					 WHERE psw = "'.$idk.'"';

logquery ($select_psw); 

	$querydati = mysqli_query($connection, $select_psw);	
	
	while($datipsw=mysqli_fetch_array($querydati)){ 

			if ( $datipsw['filiale'] == '') {echo '<b style="color:red;">PASSWORD ERRATA</b>'; }
			else {
			$filiale = $datipsw['filiale']; 
			$desc_filiale = $datipsw['desc_filiale']; 
			$chiaveURL = $idk.$idk;
			}	
	
//	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';


?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-info"><h3>Gestionale Assemblea Soci</h3>
	<?php echo $filiale.' '.$desc_filiale;?></div>
</div>


<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
	<tr>
		<td valign="top">

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Content Row 1 -->
          <div class="row">

            <!-- DEFINIZIONI -->
            <?php
			//////////////////////////////////////////////////////////////////
			// Quando
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                <div class="card-header">PROSSIMA ASSEMBLEA
                  <i class="fas fa-users fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <h4 class="card-title" style="color:#FFFFFF;">xx.xx.2020</h4>
                  <p class="card-text">&nbsp;</p> 
              </div>
              </div>
            </div>

            <?php
			//////////////////////////////////////////////////////////////////
			// Dove
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                <div class="card-header">LOCATION
                  <i class="fas fa-users fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <h4 class="card-title" style="color:#FFFFFF;">Da definirsi</h4>
                  <p class="card-text">+ map</p> 
              </div>
              </div>
            </div>
            

            <?php
			//////////////////////////////////////////////////////////////////
			// Tipologia
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                <div class="card-header">TIPOLOGIA
                  <i class="fas fa-users fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <h4 class="card-title" style="color:#FFFFFF;">Ordinaria</h4>
                  <p class="card-text">con rinnovo cariche</p> 
              </div>
              </div>
            </div>
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Calendario Deleghe
			//////////////////////////////////////////////////////////////////   
			?>            
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                <div class="card-header">DELEGHE
                  <i class="fas fa-users fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <h4 class="card-title" style="color:#FFFFFF;">Calendario</h4>
                  <p class="card-text">una delega per Socio</p> 
              </div>
              </div>
            </div>
  
        <!-- chiudo la riga --></div>
        <!-- FINE ULTIMO DIV --></div>

        </td>
    </tr>
</table>

<div class="col-lg-3">
    <div></div>
</div>

<div class="col-lg-6">
	<div></div>
    
<div class="card mb-3">
      <h3 class="card-header">Moduli</h3>
      <div class="card-body">
        <h5 class="card-title">Lettera di convocazione</h5>
        <h6 class="card-subtitle text-muted">Inviata a mezzo posta ordinaria a tutti i Soci</h6>
      </div>
      <div class="card-body">
        <h5 class="card-title">Allegato xxx</h5>
        <h6 class="card-subtitle text-muted">Inviata a mezzo posta ordinaria a tutti i Soci</h6>
      </div>
      <div class="card-body">
        <h5 class="card-title">Delega</h5>
        <h6 class="card-subtitle text-muted">Inviata a mezzo posta ordinaria a tutti i Soci</h6>
      </div>
</div>

<div class="col-lg-3">
	<div></div>
</div>

<?php
// fine ELSE
}
} */
?>

<!-- chiudo la riga </div>-->
<!-- FINE ULTIMO DIV </div>-->

<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
