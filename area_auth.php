<?php
// *****************************************************************************
// Portale ChiantiMutua
// Sviluppo e realizzazione: Alessio Fedi (2019)
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

/*
if (!isset($_POST['psw']))
	{
	echo '<div id="load" style="display:none;">Loading... Please wait</div>';

	echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
			<br><span style="color:gray;"><i>RISERVATO CAPI AREA </i></span></h2>	</center>';
	 echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="area_auth.php" method="post" onsubmit="return ray.ajax()">
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
*/

if(in_array($_COOKIE['filiale_id'], array('995')))   {

    switch ($usr_id) {
      case "00283":    // Centineo
       $area = 'CAMPI-PRATO';
          break;
      case "00496":    // Fittipaldi
       $area = 'CAMPI-PRATO';
          break;
      case "00271":    // Piccioli
       $area = 'CHIANTI-FIRENZE';
          break;
      case "00051":    // Gheri
       $area = 'CHIANTI-FIRENZE';
          break;
      case "00095":    // Palazzi
       $area = 'SIENA';
          break;
      case "00092":    // Martini U.
       $area = 'SIENA';
          break;
      case "00395":    // Melani
       $area = 'PISTOIA-TIRRENO';
          break;
      case "00416":    // Passini
       $area = 'PISTOIA-TIRRENO';
          break;

      default:
      $area = '_NonUsare_';
      echo '<center><h1 style="color:red;">Password errata</h1></center>';
      exit;
    }

  echo '<div id="load" style="display:none;">Loading... Please wait</div>';

/*	
	{
$idk = $_POST['psw'];


		switch ($_POST['psw']) {
			// CAPI AREA
			case "centineo0283":  
			 $area = 'CAMPI-PRATO';
	        break;
			case "piccioli0271": 
			 $area = 'CHIANTI-FIRENZE';
	        break;
			case "palazzi0095": 
			 $area = 'SIENA';
	        break;
			case "melani0395": 
			 $area = 'PISTOIA-TIRRENO';
	        break;

	        // Controllo di Gestione
			case "coge01":  
			 $area = 'SIENA';
	        break;
			case "coge02": 
			 $area = 'CHIANTI-FIRENZE';
	        break;
			case "coge03": 
			 $area = 'PISTOIA-TIRRENO';
	        break;
			case "coge04": 
			 $area = 'CAMPI-PRATO';
	        break;

			default:
			$area = '_NonUsare_';
			echo '<center><h1 style="color:red;">Password errata</h1></center>';
			exit;
		}

	echo '<div id="load" style="display:none;">Loading... Please wait</div>';
  */

// ********************************************************
// ESTRAZIONE FILIALE
// ********************************************************
    $select_areafil = " SELECT filiale 
                    FROM tab_psw
                    WHERE area = '".$area."'
                    " ;
    $elenco = '';
	$qry_areafil = mysqli_query($connection, $select_areafil);
    while($tot_areafil = mysqli_fetch_array($qry_areafil)){ 
        $elenco .= $tot_areafil['filiale'].",";
	}
	$elenco .= "9999";

	$chiaveURL = $elenco."&area=".$area;
	$titoloarea = 'Area '.$area;			
	
	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';


?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-light"><h3>Statistiche Soci ChiantiBanca - Area <?php echo $area;?></h3>
	</div>
</div>



<div class="col-lg-12">

<div class="card-deck" style="text-align: left;">
  
<?php
//////////////////////////////////////////////////////////////////
// STATISTICHE
//////////////////////////////////////////////////////////////////   
?>
  <div class="card">
      <div class="card-header bg-warning">STATISTICHE
        <i class="fas fa-chart-pie fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body">
  
                  <a href="stats/situazione.php?f=&filiale=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Situazione</h4>
                  </a>

                  <br>

                  <a href="stats/previsionale.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Previsionale</h4>
                  </a>

                  <br>

                  <a href="stats/rep/_report.php?key=<?php echo $chiaveURL;?>&periodo=202001" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                  <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Report Generale Area</h4>
                  </a>

                  <br>

                  <a href="stats/ammissioni_grafico.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Nuove Ammissioni</h4>
                  </a>

                  <br>

                  <a href="stats/liquidazioni_grafico.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Liquidazioni</h4>
                  </a>

                  <br>

                  <a href="stats/cessioni_area.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Cessione a Banca</h4>
                  </a>

                  <br> 

                  <a href="stats/esclusioni_grafico.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Esclusioni</h4>
                  </a>
       
                  <br>   

                  <a href="stats/eredi_grafico.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Decessi</h4>
                  </a>
      
                  <br>

                  <a href="stats/fasce_azioni.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Fasce Soci per Azioni possedute" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Fasce Soci per azioni</h4>
                  </a>  

                  <br>
   
                  <a href="stats/fasce_senzarichieste.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Soci senza richieste</h4>
                  </a>
   
                  <br>                     

                  <a href="stats/fasce_classisocio.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Classi anzianità</h4>
                  </a>
          
                  <br>

                  <a href="stats/giovani_grafico.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Giovani 18-35 anni</h4>
                  </a>

                  <br>

                  <a href="stats/volumi_area.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Volumi</h4>
                  </a>

    </div>
    <div class="card-footer">
      <small class="text-muted">per analisi dati</small>
    </div>
  </div>

<?php
//////////////////////////////////////////////////////////////////
// LISTE
//////////////////////////////////////////////////////////////////   
?>
  <div class="card">
      <div class="card-header bg-success">LISTE
        <i class="fas fa-list fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body">

                  <br>
                  
                  <a href="lista_domande.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Domande da esaminare" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Domande da esaminare</h4>
                  </a>               

                  <br>

                  <a href="lista_ammissioni.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Ammissioni a Socio" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Ammissioni a Socio</h4>
                  </a>               

                  <br>

                  <a href="lista_trasferimenti.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Trasferimenti tra Soci/Non Soci" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Trasferimenti tra Soci</h4>
                  </a>               

                  <br>

                  <a href="campagna_azioni.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Campagna ulteriori azioni" style="text-decoration: none;"></a>
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Soci con meno di 33 azioni</h4>
                  </a>  

    </div>
    <div class="card-footer">
      <small class="text-muted">elenchi di dettaglio</small>
    </div>
  </div>

<?php
//////////////////////////////////////////////////////////////////
// CONTROLLI
//////////////////////////////////////////////////////////////////   
?>
  <div class="card">
      <div class="card-header bg-danger">CONTROLLI
        <i class="fas fa-check-double fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body">
  
                  <a href="motivazioni_check.php?tipo=area&filiale=<?php echo $chiaveURL;?>" target="_blank" title="Motivazioni Ingressi" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Motivazioni Ingressi</h4>
                  </a>               

                  <br>           

          
            <?php
            if ($area == 'PISTOIA-TIRRENO') {
        //////////////////////////////////////////////////////////////////
        // controllo soci su PISA
        //////////////////////////////////////////////////////////////////

            $select_cnt = " SELECT count(*) as qta
                            FROM sds_soci a
                            WHERE PA_3 in ('009','008','017','061','019')
                            AND SOCIO_ISTITUTO between 1 AND 8
                            AND not exists 
                            (select * from tab_xls_cessionibanca as b 
                            WHERE b.Rimborsato <> 'S'
                            AND b.Totale_Parziale = 'T'
                            AND a.nag = b.nag) ";
          

            $qry_cnt = mysqli_query($connection, $select_cnt);
            while($cnt = mysqli_fetch_array($qry_cnt)){ 
              $sociPisa = $cnt['qta'];
            }
          
          // Preparo il file per l'estrazione in CSV
          $contenutopisa = '';
                $select_cnta = "
                            SELECT *
                            FROM sds_soci a
                            WHERE PA_3 in ('009','008','017','061','019')
                            AND SOCIO_ISTITUTO between 1 AND 8
                            AND not exists 
                            (select * from tab_xls_cessionibanca as b 
                            WHERE b.Rimborsato <> 'S'
                            AND b.Totale_Parziale = 'T'
                            AND a.nag = b.nag)
                            " ;
            
                    $qry_cnta = mysqli_query($connection, $select_cnta);
                    $myfile = fopen("tmp/socipisa.csv", "w");
                    $contenutopisa .= "Nag;Intestazione_a;Intestazione_b;Filiale_Capofila\n";
            while($cnta = mysqli_fetch_array($qry_cnta)){ 
              $contenutopisa .= $cnta['NAG'].";".$cnta['INTESTAZIONE_A'].";".$cnta['INTESTAZIONE_B'].";".$cnta['FILIALE_CAPOFILA']."\n";
          }
                    fwrite($myfile, $contenutopisa);
                    fclose($myfile);
        ?>
                  <a href="tmp/socipisa.csv" title="Sono compresi i Soci con piazza PISA + quelli limitrofi (Cascina, Collesalvetti, Livorno, San Giuliano)" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci zona PISA = <?php echo $sociPisa;?></h4>
                  </a>      

              <br>

            <?php
          }

           if ($area == 'SIENA') {
        //////////////////////////////////////////////////////////////////
        // controllo soci su MONTALCINO
        //////////////////////////////////////////////////////////////////

            $select_cntM = " SELECT count(*) as qta
                            FROM sds_soci a
                            WHERE PA_3 in ('021','081','082','098','086','099','100','091','096')
                            AND SOCIO_ISTITUTO between 1 AND 8
                            AND not exists 
                            (select * from tab_xls_cessionibanca as b 
                            WHERE b.Rimborsato <> 'S'
                            AND b.Totale_Parziale = 'T'
                            AND a.nag = b.nag)
                            " ;
            $qry_cntM = mysqli_query($connection, $select_cntM);
            while($cntM = mysqli_fetch_array($qry_cntM)){ 
              $sociMontalcino = $cntM['qta'];
          }
          
        ?>
                  <a href="check_vari_csv.php?scelta=montalcino" title="Soci zona MONTALCINO" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Sono compresi i Soci con piazza MONTALCINO + quelli limitrofi (Asciano, Buonconvento, Castel del Piano, Castiglione d'Orcia, Cinigiano, Civitella Paganico, Murlo, San Quirico d'Orcia)"><i class="fa fa-check-squares fa-1x text-gray"></i>&nbsp;Soci zona MONTALCINO = <?php echo $sociMontalcino;?></h4>
                  </a>      

              <br>                       
            <?php
            }
            ?>

    </div>
    <div class="card-footer">
      <small class="text-muted">per attività di verifica e controllo</small>
    </div>
  </div>

<!-- Chiudo DIV blocco -->
</div>



<?php
// fine ELSE
}

?>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>






<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
