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
	CHECK CONTROLLO DATI</div>
</div>


<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%" >
	<tr>
		<td valign="top">


        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Content Row 1 -->
          <div class="row">

          <?php
			//////////////////////////////////////////////////////////////////
			// Controllo delle cessioni a Banca
			// con verifica dell'esistenza della posizione in AS37
			//////////////////////////////////////////////////////////////////   
			?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">CESSIONI
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                  <a href="check_vari.php?scelta=cessioni" target="_blank" title="Check Cessioni banca" style="text-decoration: none;" >
                    <h4 class="card-title" style="color:#FFFFFF;">Verifica Cessioni</h4>
                  </a>
                  <p class="card-text">Controllo XLS con SDS SOCI</p>
              </div>
              </div>
            </div>

            <?php
            /*
			//////////////////////////////////////////////////////////////////
			// Controllo Deceduti (X2) per i quali gli eredi non hanno manifestato la loro volontà
			//////////////////////////////////////////////////////////////////   
			?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">DECEDUTI
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                  <a href="check_vari.php?scelta=deceduti" target="_blank" title="Check deceduti senza scelta eredi" style="text-decoration: none;" >
                    <h4 class="card-title" style="color:#FFFFFF;">Verifica stato X2</h4>
                  </a>
                  <p class="card-text">senza scelta eredi</p>
              </div>
              </div>
            </div>
            
            <?php
			//////////////////////////////////////////////////////////////////
			// Controllo Soci con stato blocco SU o 94 sul conto corrente
			//////////////////////////////////////////////////////////////////   
			?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">DECEDUTI
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                    <h4 class="card-title" style="color:#FFFFFF;" title="Decessi non comunicati a Ufficio Soci">Verifica stato SU-94</h4>
                  <p class="card-text"><a href="check_vari_csv.php?scelta=su94">Scarica il dettaglio</a></p>
              </div>
              </div>
            </div>
            
            <?php
				//////////////////////////////////////////////////////////////////
				// controllo soci senza mail
				//////////////////////////////////////////////////////////////////
       
				    $select_cnt1 = " SELECT count(*) as qta
				                    FROM tab_soci_as37 
				                    WHERE indirizzoEMail = ''
				                    AND StatoVAL not in ('E','S','N')
				                    " ;
				    $qry_cnt1 = mysqli_query($connection, $select_cnt1);
				    while($cnt1 = mysqli_fetch_array($qry_cnt1)){ 
					    $sociNOmail = $cnt1['qta'];
					}
					

				?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">EMAIL
                  <i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i>
                </div>
                <div class="card-body">
                   <h4 class="card-title" style="color:#FFFFFF;">Soci senza Email = <?php echo $sociNOmail;?></h4>
                   <p class="card-text"><i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="check_vari_csv.php?scelta=nomail">Scarica il dettaglio</a></p> 
              </div>
              </div>
            </div>
                                    
            <?php
        */       
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
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header" title="Sono compresi i Soci con piazza PISA + quelli limitrofi (Cascina, Collesalvetti, Livorno, San Giuliano)">PISA
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                   <h4 class="card-title" style="color:#FFFFFF;">Soci = <?php echo $sociPisa;?></h4>
                   <p class="card-text"><i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="check_vari_csv.php?scelta=pisa">Scarica il dettaglio</a></p> 
              </div>
              </div>
            </div>

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
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header" title="Sono compresi i Soci con piazza MONTALCINO + quelli limitrofi (Asciano, Buonconvento, Castel del Piano, Castiglione d'Orcia, Cinigiano, Civitella Paganico, Murlo, San Quirico d'Orcia)">MONTALCINO
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                   <h4 class="card-title" style="color:#FFFFFF;">Soci = <?php echo $sociMontalcino;?></h4>
                   <p class="card-text"><i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="check_vari_csv.php?scelta=montalcino">Scarica il dettaglio</a></p> 
              </div>
              </div>
            </div>

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
				
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">DIPENDENTI
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                   <h4 class="card-title" style="color:#FFFFFF;">Soci = <?php echo $sociDIP;?></h4>
                   <p class="card-text"><i class="fas fa-download fa-1x text-gray-200 col-auto"></i>
                       <a href="check_vari_csv.php?scelta=dipendenti">Scarica il dettaglio</a></p> 
              </div>
              </div>
            </div>



            <?php
      //////////////////////////////////////////////////////////////////
      // motivazione in / out soci
      //////////////////////////////////////////////////////////////////
      ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">CHECK
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                <a href="motivazioni.php?start=banca_IN" target="_blank" title="Motivazioni Ingressi" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Motivazioni Ingressi</h4>
                   </a>
                <a href="motivazioni.php?start=banca_OUT" target="_blank" title="Motivazioni Uscite" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Motivazioni Uscite</h4>
                   </a>    
              </div>
              </div>
            </div>

            <?php
      //////////////////////////////////////////////////////////////////
      // controllo zone competenza soci
      //////////////////////////////////////////////////////////////////
      ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-danger mb-3" style="max-width: 20rem;">
                <div class="card-header">ZONE COMPETENZA
                  &nbsp;<i class="fas fa-user-check fa-1x text-gray-300 col-auto"></i><i class="fab fa-scribd fa-1x text-lightgray-300"></i>
                </div>
                <div class="card-body">
                <a href="check_zonecompetenza.php?fuorizona=italia&filiale=" target="_blank" title="Zone Competenza" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Italia</h4></a>
                <a href="check_zonecompetenza.php?fuorizona=estero&filiale=" target="_blank" title="Zone Competenza" style="text-decoration: none;">
                   <h4 class="card-title" style="color:#FFFFFF;">Estero</h4></a>    
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
