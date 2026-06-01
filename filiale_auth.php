<?php
// *****************************************************************************
// Portale Soci
// Sviluppo e realizzazione: Alessio Fedi (2019)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

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
			<br><span style="color:gray;"><i>AREA RISERVATA FILIALE </i></span></h2>	</center>';
	 echo '<br><br>
	 <table border=0 align="center" width="25%">
	 <tr>
	 <td>
		<form class="form-inline" action="filiale_auth.php" method="post" onsubmit="return ray.ajax()">
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
   */

//$idk = $_POST['psw'];

$idk = $_COOKIE['filiale_id'];

if (!isset($_GET['f'])) {$queryfil = $idk;} else {$queryfil = $_GET['f'];}

	echo '<div id="load" style="display:none;">Loading... Please wait</div>';


if ( ($queryfil == '90') OR ($queryfil > '100') )
  { echo '<center>UTENTE NON AUTORIZZATO</center>';}
else
{

// ********************************************************
// ESTRAZIONE FILIALE
// ********************************************************
	$select_psw =	'SELECT 
						filiale, desc_filiale, psw
					 FROM tab_psw
					 WHERE filiale = cast("'.$queryfil.'" as unsigned)';
           // WHERE psw = "'.$idk.'"';
//echo $select_psw;
logquery ($select_psw); 

	$querydati = mysqli_query($connection, $select_psw);	
	
	while($datipsw=mysqli_fetch_array($querydati)){ 

			if ( $datipsw['filiale'] == '') {echo '<b style="color:red;">UTENTE NON AUTORIZZATO</b>'; }
			else {
			$filiale = $datipsw['filiale']; 
			$desc_filiale = $datipsw['desc_filiale']; 
			$chiaveURL = $idk.$idk;
      $chiaveURL2 = $filiale."&area=";
			}	
	
	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';


?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3>Statistiche Filiale Soci ChiantiBanca</h3>
	<?php echo $filiale.' '.$desc_filiale;?></div>
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
  
                  <a href="stats/situazione.php?f=<?php echo $filiale;?>&filiale=<?php echo $filiale;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Situazione</h4>
                  </a>

                  <br>

                  <a href="stats/previsionale.php?filiale=<?php echo $chiaveURL2;?>" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Previsionale</h4>
                  </a>

                  <br>

                  <a href="stats/rep/_report.php?key=<?php echo $chiaveURL2;?>&periodo=202001" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                  <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Report Generale Filiale</h4>
                  </a>

                  <br>

                  <a href="stats/liquidazioni_grafico.php?key=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Liquidazioni</h4>
                  </a>

                  <br>

                  <a href="stats/cessioni_filiale.php?key=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Cessione a Banca</h4>
                  </a>

                  <br> 

                  <a href="stats/esclusioni_grafico.php?key=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Esclusioni</h4>
                  </a>
       
                  <br>   

                  <a href="deceduti.php?filiale=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Decessi</h4>
                  </a>
      
                  <br>

                  <a href="deceduti_presunti.php?filiale=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Decessi presunti</h4>
                  </a>
      
                  <br>

                  <a href="stats/fasce_azioni.php?key=<?php echo $filiale;?>" target="_blank" title="Fasce Soci per Azioni possedute" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Fasce Soci per azioni</h4>
                  </a> 

                  <br>
   
                  <a href="stats/fasce_senzarichieste.php?key=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Soci senza richieste</h4>
                  </a>
   
                  <br>                     

                  <a href="stats/fasce_classisocio.php?key=<?php echo $filiale;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Classi anzianità</h4>
                  </a>
          
                  <br>

                  <a href="stats/giovani_grafico.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Statistiche banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Giovani 18-35 anni</h4>
                  </a>

                  <br>

                  <a href="stats/volumi_filiale.php?key=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche banca" style="text-decoration: none;">
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

                    <form id="form1" action="schedasocio.php" method="post">
                        <a href="javascript:;" onclick="document.getElementById('form1').submit();" style="text-decoration: none;"><h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Elenco Soci BANCA</h4></a>
                        <input type="hidden" name="filiale" value="<?php echo $filiale;?>">
                        <input type="hidden" class="form-control" name="ricerca" id="ricerca" value="filiale">
                    </form>

                  <br>

                    <form id="form2" action="mutua_listaschedasocio.php" method="post">
                        <a href="javascript:;" onclick="document.getElementById('form2').submit();" style="text-decoration: none;"><h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Elenco Soci MUTUA</h4></a>
                        <input type="hidden" name="filiale" value="<?php echo $filiale;?>">
                        <input type="hidden" class="form-control" name="ricerca" id="ricerca" value="filiale">
                    </form>

                  <br>

                  <a href="lista_domande.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Domande da esaminare" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Domande da esaminare</h4>
                  </a>               

                  <br>

                  <a href="lista_ammissioni.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Ammissioni a Socio" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Ammissioni a Socio</h4>
                  </a>               

                  <br>

                  <a href="lista_trasferimenti.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Trasferimenti tra Soci/Non Soci" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Trasferimenti tra Soci</h4>
                  </a>            

                  <br>

                  <a href="lista_usciti.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Soci usciti" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Soci usciti</h4>
                  </a>                    

                  <br>

                  <a href="campagna_azioni.php?filiale=(<?php echo $filiale;?>)" target="_blank" title="Campagna ulteriori azioni" style="text-decoration: none;"></a>
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Soci con meno di 33 azioni</h4>
                  </a>  

                  <br>

                  <a href="check_vari.php?scelta=pac&filiale=(<?php echo $filiale;?>)" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Check pagamenti PAC Under 30 e ChiantiMutua"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Verifica PAC</h4>
                  </a>

                  <br>

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
  

                    <a href="check_zonecompetenza.php?fuorizona=italia&filiale=<?php echo $filiale;?>" target="_blank" title="Zone Competenza" style="text-decoration: none;">
                      <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Zone Competenza Italia</h4>

                  <br>

                    <a href="check_zonecompetenza.php?fuorizona=estero&filiale=<?php echo $filiale;?>" target="_blank" title="Zone Competenza" style="text-decoration: none;">
                      <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Zone Competenza Estero</h4>

                  <br>

                  <a href="motivazioni_check.php?tipo=filiale&start=IN&nag=&filiale=<?php echo $filiale;?>" target="_blank" title="Motivazioni Ingressi" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Motivazioni Ingressi</h4>
                  </a>               

<!--
                  <br>
                  <a href="motivazioni.php?start=OUT&filiale=<?php echo $filiale;?>" target="_blank" title="Motivazioni Uscite" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Motivazioni Uscite</h4>
                  </a>               
-->
                  <br>

                  <a href="filiale_check.php?tipo=1&tiponag=PF&socio=1&filiale=<?php echo $filiale;?>" target="_blank" title="C/C Saldi > 5.000 euro" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci con saldo C/C < 5.000 Euro</h4>
                  </a>               

                  <br>

                  <a href="filiale_check.php?tipo=2&tiponag=PF&socio=1&filiale=<?php echo $filiale;?>" target="_blank" title="Finanziarie terze attive" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci con contratti Finanziarie in essere</h4>
                  </a>              

                  <br>
          
            <?php
            if (($filiale == '053') OR ($filiale == '054')) {
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

            if ($filiale == '051')  {
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
                            AND a.nag = b.nag) ";
          
            $qry_cntM = mysqli_query($connection, $select_cntM);
            while($cntM = mysqli_fetch_array($qry_cntM)){ 
              $sociMontalcino = $cntM['qta'];
            }
          
          // Preparo il file per l'estrazione in CSV
          $contenutoMontalcino = '';
                $select_cntaM = "
                            SELECT *
                            FROM sds_soci a
                            WHERE PA_3 in ('021','081','082','098','086','099','100','091','096')
                            AND SOCIO_ISTITUTO between 1 AND 8
                            AND not exists 
                            (select * from tab_xls_cessionibanca as b 
                            WHERE b.Rimborsato <> 'S'
                            AND b.Totale_Parziale = 'T'
                            AND a.nag = b.nag)
                            " ;
            
                    $qry_cntaM = mysqli_query($connection, $select_cntaM);
                    $myfileM = fopen("tmp/socimontalcino.csv", "w");
                    $contenutoMontalcino .= "Nag;Intestazione_a;Intestazione_b;Filiale_Capofila\n";
            while($cntaM = mysqli_fetch_array($qry_cntaM)){ 
              $contenutoMontalcino .= $cntaM['NAG'].";".$cntaM['INTESTAZIONE_A'].";".$cntaM['INTESTAZIONE_B'].";".$cntaM['FILIALE_CAPOFILA']."\n";
          }
                    fwrite($myfileM, $contenutoMontalcino);
                    fclose($myfileM);
        ?>
                  <a href="tmp/socimontalcino.csv" title="Sono compresi i Soci con piazza MONTALCINO + quelli limitrofi (Asciano, Buonconvento, Castel del Piano, Castiglione d'Orcia, Cinigiano, Civitella Paganico, Murlo, San Quirico d'Orcia)" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci zona MONTALCINO = <?php echo $sociMontalcino;?></h4>
                  </a>      

              <br>

            <?php
            }
        //////////////////////////////////////////////////////////////////
        // controllo soci senza mail
        //////////////////////////////////////////////////////////////////
/*
                    // Totale senza mail           
            $select_cnt1 = " SELECT count(*) as qta
                            FROM tab_soci_as37 
                            WHERE indirizzoEMail = ''
                            AND StatoVAL not in ('E','S','N')
                            AND codFil = CAST(".$filiale." AS UNSIGNED)
                            " ;
            $qry_cnt1 = mysqli_query($connection, $select_cnt1);
            while($cnt1 = mysqli_fetch_array($qry_cnt1)){ 
              $sociNOmail = $cnt1['qta'];
          }
          
          // Preparo il file per l'estrazione in CSV
          $contenuto = '';
                $select_cnt1a = "
                                    SELECT *
                                    FROM tab_soci_as37
                                    WHERE indirizzoEMail = ''
                            AND StatoVAL not in ('E','S')
                            AND codFil = CAST(".$filiale." AS UNSIGNED)
                            " ;
            
                    $qry_cnt1a = mysqli_query($connection, $select_cnt1a);
                    $myfile = fopen("tmp/socisenzaemail".$filiale.".csv", "w");
                    $contenuto .= "stato;cag;int1Socio;int2Socio;codFil;int1Filiale;telefono;Email;PEC\n";
            while($cnt1a = mysqli_fetch_array($qry_cnt1a)){ 
              $contenuto .= $cnt1a['stato'].";".$cnt1a['cag'].";".$cnt1a['int1Socio'].";".$cnt1a['int2Socio'].";".$cnt1a['codFil'].";".$cnt1a['int1Filiale'].";".$cnt1a['telefono'].";".$cnt1a['indirizzoEMail'].";".$cnt1a['indirizzoPEC']."\n";
          }
                    fwrite($myfile, $contenuto);
                    fclose($myfile);
*/
        ?>

                  <a href="tmp/socisenzaemail<?php //echo $filiale;?>.csv" title="Soci senza Mail" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci senza Email = <?php //echo $sociNOmail;?></h4>
                  </a>     

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
}
?>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>






<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
