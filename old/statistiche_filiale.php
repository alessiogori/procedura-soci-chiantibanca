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

// including FusionCharts PHP wrapper
include("graph/fusioncharts.php"); 

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

$adesso = date("d.m.Y");
$anno = date("Y");

// creating FusionCharts instance
// ------------------------------
// $Chartcolumn2d = new FusionCharts("column2d", "myChart" , "95%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartcolumn3d = new FusionCharts("column3d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartline = new FusionCharts("line", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartarea2d = new FusionCharts("area2d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartbar2d = new FusionCharts("bar2d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartbar3d = new FusionCharts("bar3d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartpie2d = new FusionCharts("pie2d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartpie3d = new FusionCharts("pie3d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartdoughnut2d = new FusionCharts("doughnut2d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartdoughnut3d = new FusionCharts("doughnut3d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartpareto2d = new FusionCharts("pareto2d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
// $Chartpareto3d = new FusionCharts("pareto3d", "myChart" , "100%", "450", "chart-container", "json", $jsonEncodedData);
     
// FusionCharts render method
// ------------------------------
// $Chartcolumn2d->render();
// $Chartcolumn3d->render();
// $Chartline->render();
// $Chartarea2d->render();
// $Chartbar2d->render();
// $Chartbar3d->render();
// $Chartpie2d->render();
// $Chartpie3d->render();
// $Chartdoughnut2d->render();
// $Chartdoughnut3d->render();
// $Chartpareto2d->render();
// $Chartpareto3d->render();


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
if (!isset($_GET['key']))
    {$condizionefiliale = '';
     $titolofiliale = '';
     $filiale = '';
    }
    else
    {$condizionefiliale = 'AND filiale = '.substr($_GET['key'],0,3);
     $titolofiliale = ' - Filiale '.substr($_GET['key'],0,3);  
     $filiale = substr($_GET['key'],0,3);
    }
?>
<center>
<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3>Statistiche Soci ChiantiBanca</h3>
	Filiale <?php echo $filiale; ?></div>
</div>


<?php
//////////////////////////////////////////////////////////////////
// CONTEGGI BANCA
//////////////////////////////////////////////////////////////////

    $select_cnt_banca = " SELECT count(*) as qta, SESSO as sessoVAL
                          FROM sds_sinergiareport_soci
                          WHERE DATA_ENTRATA <= NOW()
                          AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                          GROUP BY SESSO" ;
    $qry_cnt_banca = mysqli_query($connection, $select_cnt_banca);
    while($cnt_banca = mysqli_fetch_array($qry_cnt_banca)){ 
	    if 		($cnt_banca['sessoVAL'] == 'M') {$maschi_banca = $cnt_banca['qta'];}
	    elseif 	($cnt_banca['sessoVAL'] == 'F') {$femmine_banca = $cnt_banca['qta'];}
	    else 	{$aziende_banca = $cnt_banca['qta'];}
	}

    $totalesoci_banca   = $maschi_banca + $femmine_banca + $aziende_banca;
    $perc_maschi_banca  = $maschi_banca / $totalesoci_banca * 100;
    $perc_femmine_banca = $femmine_banca / $totalesoci_banca * 100;
    $perc_aziende_banca = $aziende_banca / $totalesoci_banca * 100;

//////////////////////////////////////////////////////////////////
// CONTEGGI FILIALE
//////////////////////////////////////////////////////////////////
    $select_tot = " SELECT count(*) as qta, 'TOTALE' as sessoVAL
                          FROM sds_sinergiareport_soci
                          WHERE DATA_ENTRATA <= NOW()
                          AND FILIALE_ANAGRAFICA = ".$filiale."
                          AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                    " ;
    $qry_tot = mysqli_query($connection, $select_tot);
    while($tot = mysqli_fetch_array($qry_tot)){ 
        $totalesoci = $tot['qta'];
	}

    $select_cnt = " SELECT count(*) as qta, SESSO as sessoVAL
                          FROM sds_sinergiareport_soci
                          WHERE DATA_ENTRATA <= NOW()
                          AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                          AND FILIALE_ANAGRAFICA = ".$filiale."
                          GROUP BY SESSO" ;
    $qry_cnt = mysqli_query($connection, $select_cnt);
    while($cnt = mysqli_fetch_array($qry_cnt)){ 
	    if 		($cnt['sessoVAL'] == 'M') {$maschi = $cnt['qta'];}
	    elseif 	($cnt['sessoVAL'] == 'F') {$femmine = $cnt['qta'];}
	    else 	{$aziende = $cnt['qta'];}
	}
	
	$perc_totalesoci_filiale = $totalesoci / $totalesoci_banca * 100;
    $perc_maschi_filiale  = $maschi / $totalesoci * 100;
    $perc_femmine_filiale = $femmine / $totalesoci * 100;
    $perc_aziende_filiale = $aziende / $totalesoci * 100;

?>

<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-3">
<div class="card text-white bg-success mb-3" style="max-width: 20rem;">
  <div class="card-header">SOCI IN ESSERE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_people.png" height="60"><h4 class="card-title"><?php echo number_format($totalesoci, 0, ',', '.'); ?></h4></p>
    <p class="card-text"><?php echo number_format($perc_totalesoci_filiale,2,',','.'); ?> % dei Soci della Banca</p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-info mb-3" style="max-width: 20rem;">
  <div class="card-header">MASCHI</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_man.png" height="60"><h4 class="card-title"><?php echo number_format($maschi, 0, ',', '.'); ?></h4></p>
    <p class="card-text">
        Filiale <?php echo number_format($perc_maschi_filiale,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc_maschi_banca,2,',','.'); ?> %)
    </p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-danger mb-3" style="max-width: 20rem;">
  <div class="card-header">FEMMINE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_woman.png" height="60"><h4 class="card-title"><?php echo number_format($femmine, 0, ',', '.'); ?></h4></p>
    <p class="card-text">
        Filiale <?php echo number_format($perc_femmine_filiale,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc_femmine_banca,2,',','.'); ?> %)
    </p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-light mb-3" style="max-width: 20rem;">
  <div class="card-header">AZIENDE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_azienda.png" height="60"><h4 class="card-title"><?php echo number_format($aziende, 0, ',', '.');; ?></h4></p>
    <p class="card-text">
        Filiale <?php echo number_format($perc_aziende_filiale,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc_aziende_banca,2,',','.'); ?> %)
    </p>
  </div>
</div>
</div>

<!-- chiudo la riga --></div>

<!-- Content Row 2 -->
<div class="row">	

<?php
//////////////////////////////////////////////////////////////////
// FASCE DI ETA' BANCA
//////////////////////////////////////////////////////////////////

    $select_etaB = " SELECT	
					sum(if(fascia = 'Fascia 1 (18-30 anni)', qta, 0)) as qtaF1,
					sum(if(fascia = 'Fascia 2 (31-50 anni)', qta, 0)) as qtaF2,
					sum(if(fascia = 'Fascia 3 (51-60 anni)', qta, 0)) as qtaF3,
					sum(if(fascia = 'Fascia 4 (61-70 anni)', qta, 0)) as qtaF4,
					sum(if(fascia = 'Fascia 5 (oltre 70 anni)', qta, 0)) as qtaF5,
					sum(qta) as qtaTot
				    FROM view_fasce
                  	GROUP BY Fascia" ;
    $qry_etaB = mysqli_query($connection, $select_etaB);
    while($etaB = mysqli_fetch_array($qry_etaB)){ 

	    if 		($etaB['qtaF1'] != 0) {$Fascia1B = $etaB['qtaF1'];
	    							  $perc1B = $Fascia1B / $totalesoci_banca * 100; }
	    if 		($etaB['qtaF2'] != 0) {$Fascia2B = $etaB['qtaF2'];
	    							  $perc2B = $Fascia2B / $totalesoci_banca * 100; }
	    if 		($etaB['qtaF3'] != 0) {$Fascia3B = $etaB['qtaF3'];
	    							  $perc3B = $Fascia3B / $totalesoci_banca * 100; }
	    if 		($etaB['qtaF4'] != 0) {$Fascia4B = $etaB['qtaF4'];
	    							  $perc4B = $Fascia4B / $totalesoci_banca * 100; }
	    if 		($etaB['qtaF5'] != 0) {$Fascia5B = $etaB['qtaF5'];
	    							  $perc5B = $Fascia5B / $totalesoci_banca * 100; }
	}
	
//////////////////////////////////////////////////////////////////
// FASCE DI ETA' FILIALE
//////////////////////////////////////////////////////////////////

    $select_eta = " SELECT	
					sum(if(fascia = 'Fascia 1 (18-30 anni)', qta, 0)) as qtaF1,
					sum(if(fascia = 'Fascia 2 (31-50 anni)', qta, 0)) as qtaF2,
					sum(if(fascia = 'Fascia 3 (51-60 anni)', qta, 0)) as qtaF3,
					sum(if(fascia = 'Fascia 4 (61-70 anni)', qta, 0)) as qtaF4,
					sum(if(fascia = 'Fascia 5 (oltre 70 anni)', qta, 0)) as qtaF5,
					sum(qta) as qtaTot
				    FROM view_fasce
				    WHERE codFil = ".$filiale."
                  	GROUP BY Fascia" ;
    $qry_eta = mysqli_query($connection, $select_eta);
    while($eta = mysqli_fetch_array($qry_eta)){ 

	    if 		($eta['qtaF1'] != 0) {$Fascia1 = $eta['qtaF1'];
	    							  $perc1 = $Fascia1 / $totalesoci * 100; }
	    if 		($eta['qtaF2'] != 0) {$Fascia2 = $eta['qtaF2'];
	    							  $perc2 = $Fascia2 / $totalesoci * 100; }
	    if 		($eta['qtaF3'] != 0) {$Fascia3 = $eta['qtaF3'];
	    							  $perc3 = $Fascia3 / $totalesoci * 100; }
	    if 		($eta['qtaF4'] != 0) {$Fascia4 = $eta['qtaF4'];
	    							  $perc4 = $Fascia4 / $totalesoci * 100; }
	    if 		($eta['qtaF5'] != 0) {$Fascia5 = $eta['qtaF5'];
	    							  $perc5 = $Fascia5 / $totalesoci * 100; }
	}

/*
select 
	CAST(codFil AS UNSIGNED) as codFil, int1Filiale as Filiale,
	sum(if(fascia = 'Fascia 1 (18-30 anni)', qta, 0)) as qtaF1,
	sum(if(fascia = 'Fascia 2 (31-50 anni)', qta, 0)) as qtaF2,
	sum(if(fascia = 'Fascia 3 (51-60 anni)', qta, 0)) as qtaF3,
	sum(if(fascia = 'Fascia 4 (61-70 anni)', qta, 0)) as qtaF4,
	sum(if(fascia = 'Fascia 5 (oltre 70 anni)', qta, 0)) as qtaF5,
	sum(qta) as qtaTot
from view_fasce
group by CAST(codFil AS UNSIGNED), int1Filiale
ORDER BY 1
*/  
?>
<div class="col-lg-3">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(18-30 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia1, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc1,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc1B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(31-50 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia2, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc2,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc2B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(51-60 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia3, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc3,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc3B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(61-70 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia4, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc4,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc4B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(oltre 70 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia5, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc5,2,',','.'); ?> %
        &nbsp;(Banca <?php echo number_format($perc5B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<!-- chiudo la riga --></div>

<!-- Content Row 2 -->
<div class="row">	

<?php
//////////////////////////////////////////////////////////////////
// SOCIO PIU' GIOVANE e PIU' ANZIANO
//////////////////////////////////////////////////////////////////
    $select_eta = " 
          select *, 'NEW' as eta 
          from sds_sinergiareport_soci
          where TIPO_SOGGETTO = 'PF' 
          AND STR_TO_DATE(DATA_DI_NASCITA,'%d/%m/%Y') = (
            SELECT max(STR_TO_DATE(DATA_DI_NASCITA,'%d/%m/%Y'))
            FROM sds_sinergiareport_soci 
            WHERE sds_sinergiareport_soci.DATA_ENTRATA <= now()
            AND ( (sds_sinergiareport_soci.DATA_USCITA = '0')
                OR (sds_sinergiareport_soci.DATA_USCITA > now() ) )
            AND SESSO in ('M','F')
            AND TIPO_SOGGETTO = 'PF'
            AND FILIALE_ANAGRAFICA = ".$filiale."
          )
					UNION
          select *, 'OLD' as eta 
          from sds_sinergiareport_soci
          where TIPO_SOGGETTO = 'PF' 
          AND STR_TO_DATE(DATA_DI_NASCITA,'%d/%m/%Y') = (
            SELECT min(STR_TO_DATE(DATA_DI_NASCITA,'%d/%m/%Y'))
            FROM sds_sinergiareport_soci 
            WHERE sds_sinergiareport_soci.DATA_ENTRATA <= now()
            AND ( (sds_sinergiareport_soci.DATA_USCITA = '0')
                OR (sds_sinergiareport_soci.DATA_USCITA > now() ) )
            AND SESSO in ('M','F')
            AND TIPO_SOGGETTO = 'PF'
            AND FILIALE_ANAGRAFICA = ".$filiale."
          )
                    " ; 

    $qry_eta = mysqli_query($connection, $select_eta);
    while($eta = mysqli_fetch_array($qry_eta)){ 

	    if 		($eta['eta'] == 'NEW') 
	    			{
	    				$G_prot = $eta['IDSOCIO'];
	    				$G_cag  = $eta['NAG'];
	    				$G_nome = $eta['INTESTAZIONE'];
	    				$G_dt   = $eta['DATA_DI_NASCITA'];
	    				$G_anni = $anno - substr($eta['DATA_DI_NASCITA'],6,4);
	    				$G_dtam = $eta['DATA_ENTRATA'];
	    			}
	    
	    if 		($eta['eta'] == 'OLD') 
	    			{
	    				$V_prot = $eta['IDSOCIO'];
	    				$V_cag  = $eta['NAG'];
	    				$V_nome = $eta['INTESTAZIONE'];
	    				$V_dt   = $eta['DATA_DI_NASCITA'];
	    				$V_anni = $anno - substr($eta['DATA_DI_NASCITA'],6,4);
	    				$V_dtam = $eta['DATA_ENTRATA'];
	    			}
	    
	    }

?>

<div class="col-lg-4">
<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
  <div class="card-header">Socio più GIOVANE</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo $G_nome; ?></h4>
    <p class="card-text"><?php echo $G_anni; ?> anni </p>
    <small>
		<a class="text-success" href="sqldati_schedasocio.php?id=<?php echo $G_prot; ?>"><?php echo $G_cag; ?></a><br>
    	Nato il <?php echo $G_dt; ?> - 
    	Socio dal <?php echo $G_dtam; ?> <br>
	</small>
  </div>
</div>
</div>

<div class="col-lg-4">
    <div id="aree0"><!-- Fusion Charts will render here--></div>
</div>

<div class="col-lg-4">
<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
  <div class="card-header">Socio più ANZIANO</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo $V_nome; ?></h4>
    <p class="card-text"><?php echo $V_anni; ?> anni </p>
    <small>
		<a class="text-success" href="sqldati_schedasocio.php?id=<?php echo $V_prot; ?>"><?php echo $V_cag; ?></a><br>
    	Nato il <?php echo $V_dt; ?> - 
    	Socio dal <?php echo $V_dtam; ?> <br>
	</small>
  </div>
</div>
</div>

<!-- chiudo la riga --></div>

<!-- Content Row 2 -->
<div class="row"> 

<?php
//////////////////////////////////////////////////////////////////
// SOCI CON ANZIANTA' BANCARIA BANCA
//////////////////////////////////////////////////////////////////
/*
    $select_anzB = " SELECT  
                    sum(if(fascia = 'Fascia 1 (1-3 anni)', qta, 0)) as qtaF1,
                    sum(if(fascia = 'Fascia 2 (4-6 anni)', qta, 0)) as qtaF2,
                    sum(if(fascia = 'Fascia 3 (7-10 anni)', qta, 0)) as qtaF3,
                    sum(if(fascia = 'Fascia 4 (11-20 anni)', qta, 0)) as qtaF4,
                    sum(if(fascia = 'Fascia 5 (oltre 20 anni)', qta, 0)) as qtaF5,
                    sum(qta) as qtaTot
                    FROM view_fasce_anzianita
                    GROUP BY Fascia" ;

    $qry_anzB = mysqli_query($connection, $select_anzB);
    while($anzB = mysqli_fetch_array($qry_anzB)){ 

      if    ($anzB['qtaF1'] != 0) {$Fascia1B = $anzB['qtaF1'];
                      $perc1B = $Fascia1B / $totalesoci_banca * 100; }
      if    ($anzB['qtaF2'] != 0) {$Fascia2B = $anzB['qtaF2'];
                      $perc2B = $Fascia2B / $totalesoci_banca * 100; }
      if    ($anzB['qtaF3'] != 0) {$Fascia3B = $anzB['qtaF3'];
                      $perc3B = $Fascia3B / $totalesoci_banca * 100; }
      if    ($anzB['qtaF4'] != 0) {$Fascia4B = $anzB['qtaF4'];
                      $perc4B = $Fascia4B / $totalesoci_banca * 100; }
      if    ($anzB['qtaF5'] != 0) {$Fascia5B = $anzB['qtaF5'];
                      $perc5B = $Fascia5B / $totalesoci_banca * 100; }
  }
  
//////////////////////////////////////////////////////////////////
// SOCI CON ANZIANTA' BANCARIA FILIALE
//////////////////////////////////////////////////////////////////

    $select_anz = " SELECT  
                    sum(if(fascia = 'Fascia 1 (1-3 anni)', qta, 0)) as qtaF1,
                    sum(if(fascia = 'Fascia 2 (4-6 anni)', qta, 0)) as qtaF2,
                    sum(if(fascia = 'Fascia 3 (7-10 anni)', qta, 0)) as qtaF3,
                    sum(if(fascia = 'Fascia 4 (11-20 anni)', qta, 0)) as qtaF4,
                    sum(if(fascia = 'Fascia 5 (oltre 20 anni)', qta, 0)) as qtaF5,
                    sum(qta) as qtaTot
                    FROM view_fasce_anzianita
                    WHERE codFil = ".$filiale."
                    GROUP BY Fascia" ;

    $qry_anz = mysqli_query($connection, $select_anz);
    while($anz = mysqli_fetch_array($qry_anz)){ 

      if    ($anz['qtaF1'] != 0) {$Fascia1 = $anz['qtaF1'];
                      $perc1 = $Fascia1 / $totalesoci * 100; }
      if    ($anz['qtaF2'] != 0) {$Fascia2 = $anz['qtaF2'];
                      $perc2 = $Fascia2 / $totalesoci * 100; }
      if    ($anz['qtaF3'] != 0) {$Fascia3 = $anz['qtaF3'];
                      $perc3 = $Fascia3 / $totalesoci * 100; }
      if    ($anz['qtaF4'] != 0) {$Fascia4 = $anz['qtaF4'];
                      $perc4 = $Fascia4 / $totalesoci * 100; }
      if    ($anz['qtaF5'] != 0) {$Fascia5 = $anz['qtaF5'];
                      $perc5 = $Fascia5 / $totalesoci * 100; }
  }
*/
?>
<div class="col-lg-3">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(1-3 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia1, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc1,2,',','.'); ?> %
        &nbsp;(Banca <?php //echo number_format($perc1B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(4-6 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia2, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc2,2,',','.'); ?> %
        &nbsp;(Banca <?php //echo number_format($perc2B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(7-10 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia3, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc3,2,',','.'); ?> %
        &nbsp;(Banca <?php //echo number_format($perc3B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(11-20 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia4, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc4,2,',','.'); ?> %
        &nbsp;(Banca <?php //echo number_format($perc4B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(oltre 20 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia5, 0, ',', '.'); ?></h4>
    <p class="card-text"><small>Filiale <?php echo number_format($perc5,2,',','.'); ?> %
        &nbsp;(Banca <?php //echo number_format($perc5B,2,',','.'); ?> %)</small>
    </p>
  </div>
</div>
</div>

<!-- chiudo la riga --></div>



<!-- FINE ULTIMO DIV -->
</div>

</center>
</body>
</html>