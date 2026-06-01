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

/*
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
*/

/*  if(!in_array($_COOKIE['filiale_id'], array('998','999')) )
  {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
}
else 
	{
*/   

if ( (in_array($filiale_id, array('998','999')) ) OR (in_array($usr_id, array('00390')) ) )
{
// if ($_POST['psw'] == 'cicalo') {$idk = $_POST['psw'];}

	echo '<div id="load" style="display:none;color:red;background: #fafafa url(img/page-loader.gif) no-repeat center center;height: 50%;"><br>Loading... Please wait</div>';

?>

<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-warning"><h3>Dashboard Soci ChiantiBanca</h3>
	Ufficio Soci</div>
</div>


<center>
<a class="btn btn-outline-success" style="text-decoration:none;" href="lista_domande.php" target="_blank">
<i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Lista Domande presenti</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="lista_domande_daregolare.php" target="_blank"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Lista Domande da regolare</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="lista_domande_dasollecitare.php" target="_blank"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Domande da sollecitare</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="stats/situazione.php?f=999" target="_blank"><i class="fa fa-users fa-1x text-gray"></i>&nbsp;Situazione Soci alla data odierna</a>
&nbsp;&nbsp;&nbsp;
<a class="btn btn-outline-success" style="text-decoration:none;" href="stats/situazione_plafond.php" target="_blank"><i class="fa fa-euro-sign fa-1x text-gray"></i>&nbsp;Situazione Plafond</a>
</center>
<br>
<br>


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


                  <a href="statistiche.php?auth=1" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Fasce e distribuzione"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Dati Generali</h4>
                  </a>

                  <br>

                  <a href="stats/previsionale.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Previsionale</h4>
                  </a>

                  <br>

                  <a href="stats/ammissioni_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Ammissioni</h4>
                  </a>

                  <br>

                  <a href="stats/liquidazioni_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Liquidazioni</h4>
                  </a>

                  <br>                  
                  
                  <a href="stats/cessioni_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Cessione a Banca</h4>
                  </a>

                  <br>
                  
                  <a href="stats/esclusioni_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per art.6, art.14 e Sofferenze"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Esclusioni</h4>
                  </a>
<!--
                  <br>
                  
                  <a href="stats/eredi_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Decessi (Grafico)</h4>
                  </a>
-->
                  <br>
                  
                  <a href="deceduti.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Deceduti</h4>
                  </a>

                  <br>
                  
                  <a href="deceduti_presunti.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per Aree e Filiali"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Deceduti Presunti</h4>
                  </a>

                  <br>
                  
                  <a href="stats/fasce_classisocio.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per età nella compagine"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Classi anzianità a Socio</h4>
                  </a>

                  <br>
                  
                  <a href="stats/fasce_classisocio_rapporto.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per anzianità di rapporto"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Classi anzianità per Rapporto</h4>
                  </a>
            
                  <br>

                  <a href="stats/fasce_azioni.php" target="_blank" title="Fasce Soci per Azioni possedute" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Fasce Soci Azioni/Età</h4>
                  </a> 
      
                  <br>

                  <a href="stats/fasce_eta_per_anno.php" target="_blank" title="Fasce Soci per Anno ammissione" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Fasce Soci Anno Ammissione/Età</h4>
                  </a> 

                  <br>

                  <a href="stats/fasce_consenzarichieste.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="per fasce di quote"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Fasce Soci con/senza richieste</h4>
                  </a>

                  <br>
                  
                  <a href="stats/volumi_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:gray;" title=""><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Volumi</h4>
                  </a>

                  <br>
                  

    </div>
    <div class="card-footer">
      <small class="text-muted">per analisi dati</small>
    </div>
  </div>


  <?php
//////////////////////////////////////////////////////////////////
// LISTE E REPORT
//////////////////////////////////////////////////////////////////   
?>
  <div class="card">
      <div class="card-header bg-success">LISTE E REPORT
        <i class="fas fa-list fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body">

                  <a href="lista_soci.php" target="_blank" title="Lista Soci in essere Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Lista Soci in essere Banca"><i class="fa fa-download fa-1x text-gray"></i>&nbsp;Lista Soci in essere</h4>
                  </a>

                  <br>

                  <a href="stats/repcda_prospetto_consiglio.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Grafici e tabelle per copia-incolla CDA"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Prospetto CDA per delibera</h4>
                  </a>

                  <br>

                  <a href="stats/rep/_report.php?periodo=202201" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Report Generale Banca</h4>
                  </a>

                  <br>

                  <a href="lista_ammissioni.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Ammissioni a Socio</h4>
                  </a>

                  <br>

                  <a href="lista_trasferimenti.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Trasferimenti tra Soci</h4>
                  </a>

                  <br>

                  <a href="lista_usciti.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Soci usciti</h4>
                  </a>

                  <br>

                  <a href="lista_mail_mailup.php?scelta=full" target="_blank" title="Elenco mail per comunicazioni" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-download fa-1x text-gray"></i>&nbsp;Tracciato per Mailup (tutti)</h4>
                  </a>
    
                  <br>

                  <a href="lista_mail_mailup.php?scelta=u35" target="_blank" title="Elenco mail per comunicazioni" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-download fa-1x text-gray"></i>&nbsp;Tracciato per Mailup (Under 35)</h4>
                  </a>
    
                  <br>
    <!--
                  <a href="stats/assemblea_buoni.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Statistica rilascio buoni"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Rilascio buoni Olio</h4>
                  </a>

                  <br>
    -->                  
     </div>
    <div class="card-footer">
      <small class="text-muted">Report e Prospetti</small>
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

                  <a href="check_vari.php?scelta=cessioni" target="_blank" title="Check Cessioni banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Controllo XLS con SDS SOCI"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Verifica Cessioni</h4>
                  </a>

                  <br>

                  <a href="motivazioni_check.php?filiale=999" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Motivazioni Ingressi e Uscite</h4>
                  </a>

                  <br>

                  <a href="check_zonecompetenza.php?fuorizona=italia&filiale=" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Zone Competenza Italia</h4>
                  </a>

                  <br>

                  <a href="check_zonecompetenza.php?fuorizona=estero&filiale=" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title=""><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Zone Competenza Estero</h4>
                  </a>

                  <br>

          <?php
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
                            AND a.nag = b.nag)
                            " ;
            $qry_cnt = mysqli_query($connection, $select_cnt);
            while($cnt = mysqli_fetch_array($qry_cnt)){ 
              $sociPisa = $cnt['qta'];
            }

        ?>
                  <a href="check_vari_csv.php?scelta=pisa" title="Sono compresi i Soci con piazza PISA + quelli limitrofi (Cascina, Collesalvetti, Livorno, San Giuliano)" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci zona PISA = <?php echo $sociPisa;?></h4>
                  </a>  

                  <br>

            <?php
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

                  <a href="check_vari_csv.php?scelta=montalcino" title="Sono compresi i Soci con piazza MONTALCINO + quelli limitrofi (Asciano, Buonconvento, Castel del Piano, Castiglione d'Orcia, Cinigiano, Civitella Paganico, Murlo, San Quirico d'Orcia)" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci zona MONTALCINO = <?php echo $sociMontalcino;?></h4>
                  </a>  
            
                  <br>

            <?php
        //////////////////////////////////////////////////////////////////
        // Soci anche dipendenti Banca
        //////////////////////////////////////////////////////////////////

            $select_cnt2 = "SELECT count(*) as qta
                                    FROM sds_soci
                                    where SEGMENTO_CLIENTE = 18
                            " ;
            $qry_cnt2 = mysqli_query($connection, $select_cnt2);
            while($cnt2 = mysqli_fetch_array($qry_cnt2)){ 
              $sociDIP = $cnt2['qta'];
          }
          
        ?>
                 <a href="check_vari_csv.php?scelta=dipendenti" title="" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-check-square fa-1x text-gray"></i>&nbsp;Soci Dipendenti = <?php echo $sociDIP;?></h4>
                  </a>  

                  <br>

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

                  <a href="mp/assemblea_stats_moduli.csv" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Scarica il dettaglio Banca"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Log Stampe Assemblea Banca</h4>
                  </a>

                  <br>

                  <a href="mp/assemblea_stats_moduli_mutua.csv" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Scarica il dettaglio Mutua"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Log Stampe Assemblea Mutua</h4>
                  </a>

                  <br>

    </div>
    <div class="card-footer">
      <small class="text-muted">Report e Prospetti</small>
    </div>
  </div>


  <?php
//////////////////////////////////////////////////////////////////
// UNDER 30 & CHIANTIMUTUA
//////////////////////////////////////////////////////////////////   
?>
  <div class="card">
      <div class="card-header bg-primary">UNDER 35 & CHIANTIMUTUA
        <i class="fas fa-list fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body">

                  <form id="form2" action="mutua_listaschedasocio.php" method="post">
                      <a href="javascript:;" onclick="document.getElementById('form2').submit();" style="text-decoration: none;"><h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Elenco Soci MUTUA</h4></a>
                      <input type="hidden" name="filiale" value="full">
                      <input type="hidden" class="form-control" name="ricerca" id="ricerca" value="filiale">
                  </form>

                  <br>

                  <a href="stats/giovani_grafico.php" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="elenchi per età"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Giovani 18-35 anni</h4>
                  </a>

                  <br>

                  <a href="check_vari.php?scelta=pac" target="_blank" title="Statistiche Banca" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Check pagamenti PAC Under 35 e ChiantiMutua"><i class="fa fa-list-alt fa-1x text-gray"></i>&nbsp;Verifica PAC</h4>
                  </a>

                  <br>

    </div>
    <div class="card-footer">
      <small class="text-muted">Report e Prospetti</small>
    </div>
  </div>


<!-- Chiudo DIV blocco -->
</div>



<?php

// fine IF
}

else

  {
  echo '<center><h2>Sorry, non sei autorizzato ad accedere a questa pagina! :-(
      <br><span style="color:gray;"><i>AREA RISERVATA UFFICIO SOCI CHIANTIBANCA </i></span></h2> </center>';
  echo '<br><br>';    
}


?>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>

<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>

