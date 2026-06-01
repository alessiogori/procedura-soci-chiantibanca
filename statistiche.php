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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<?php
// echo $_SERVER['HTTP_REFERER'];

$adesso = date("d.m.Y");


if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/1900';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }


//////////////////////////////////////////////////////////////////
// CONTEGGI
//////////////////////////////////////////////////////////////////
//                    -- DATA_ENTRATA <= NOW()

    $select_cnt = " SELECT count(*) as qta, SESSO as sessoVAL
                    FROM sds_soci
                    WHERE 
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('".$_GET['datain']."','%d/%m/%Y')
                        AND
                        str_to_date(DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('".$_GET['dataout']."','%d/%m/%Y')
                    AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                    GROUP BY SESSO " ;
    $qry_cnt = mysqli_query($connection, $select_cnt);
    while($cnt = mysqli_fetch_array($qry_cnt)){ 
	    if 		($cnt['sessoVAL'] == 'M') {$maschi = number_format($cnt['qta'], 0, ',', '.');}
	    elseif 	($cnt['sessoVAL'] == 'F') {$femmine = number_format($cnt['qta'], 0, ',', '.');}
	    else 	{$aziende = number_format($cnt['qta'], 0, ',', '.');}
	}

    $totalesoci = $maschi + $femmine + $aziende;


?>
<center>

<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row">	

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-secondary"><h3>Statistiche Soci ChiantiBanca</h3></div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-success mb-3" style="max-width: 20rem;">
  <div class="card-header">SOCI IN ESSERE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_people.png" height="60"><h4 class="card-title"><?php echo $totalesoci; ?></h4></p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-info mb-3" style="max-width: 20rem;">
  <div class="card-header">MASCHI</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_man.png" height="60"><h4 class="card-title"><?php echo $maschi; ?></h4></p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-danger mb-3" style="max-width: 20rem;">
  <div class="card-header">FEMMINE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_woman.png" height="60"><h4 class="card-title"><?php echo $femmine; ?></h4></p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card text-white bg-light mb-3" style="max-width: 20rem;">
  <div class="card-header">AZIENDE</div>
  <div class="card-body">
    <p class="card-text"><img src="img/ico_azienda.png" height="60"><h4 class="card-title"><?php echo $aziende; ?></h4></p>
  </div>
</div>
</div>

<!-- chiudo la riga --></div>

<!-- Content Row 2 -->
<div class="row">	

<?php
//////////////////////////////////////////////////////////////////
// FASCE DI ETA'
//////////////////////////////////////////////////////////////////

    $select_eta = " SELECT	
					sum(if(fascia = 'Fascia 1 (18-30 anni)', qta, 0)) as qtaF1,
					sum(if(fascia = 'Fascia 2 (31-50 anni)', qta, 0)) as qtaF2,
					sum(if(fascia = 'Fascia 3 (51-60 anni)', qta, 0)) as qtaF3,
					sum(if(fascia = 'Fascia 4 (61-70 anni)', qta, 0)) as qtaF4,
					sum(if(fascia = 'Fascia 5 (oltre 70 anni)', qta, 0)) as qtaF5,
					sum(qta) as qtaTot
				    FROM view_fasce
                  	GROUP BY Fascia" ;
    $qry_eta = mysqli_query($connection, $select_eta);
    while($eta = mysqli_fetch_array($qry_eta)){ 

	    if 		($eta['qtaF1'] != 0) {$Fascia1 = $eta['qtaF1'];
	    							  $perc1 = $Fascia1 / $totalesoci / 10; }
	    if 		($eta['qtaF2'] != 0) {$Fascia2 = $eta['qtaF2'];
	    							  $perc2 = $Fascia2 / $totalesoci / 10; }
	    if 		($eta['qtaF3'] != 0) {$Fascia3 = $eta['qtaF3'];
	    							  $perc3 = $Fascia3 / $totalesoci / 10; }
	    if 		($eta['qtaF4'] != 0) {$Fascia4 = $eta['qtaF4'];
	    							  $perc4 = $Fascia4 / $totalesoci / 10; }
	    if 		($eta['qtaF5'] != 0) {$Fascia5 = $eta['qtaF5'];
	    							  $perc5 = $Fascia5 / $totalesoci / 10; }
	}


    // ETA' MEDIA
    $select_etamedia = " SELECT	fascia, eta
				    FROM view_fasce_etamedia
                  	GROUP BY Fascia" ;
    $qry_etamedia = mysqli_query($connection, $select_etamedia);
    while($etamedia = mysqli_fetch_array($qry_etamedia)){ 

	    if 		($etamedia['fascia'] == 'Fascia 1 (18-30 anni)') {$etamedia1 = $etamedia['eta'];}
	    if 		($etamedia['fascia'] == 'Fascia 2 (31-50 anni)') {$etamedia2 = $etamedia['eta'];}
	    if 		($etamedia['fascia'] == 'Fascia 3 (51-60 anni)') {$etamedia3 = $etamedia['eta'];}
	    if 		($etamedia['fascia'] == 'Fascia 4 (61-70 anni)') {$etamedia4 = $etamedia['eta'];}
	    if 		($etamedia['fascia'] == 'Fascia 5 (oltre 70 anni)') {$etamedia5 = $etamedia['eta'];}
	} 
	
?>
<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(18-30 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia1, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php echo number_format($perc1,2,',','.'); ?> %
    - Età media <?php echo number_format($etamedia1,0,',','.'); ?></p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(31-50 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia2, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php echo number_format($perc2,2,',','.'); ?> %
    - Età media <?php echo number_format($etamedia2,0,',','.'); ?></p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(51-60 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia3, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php echo number_format($perc3,2,',','.'); ?> %
    - Età media <?php echo number_format($etamedia3,0,',','.'); ?></p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(61-70 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia4, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php echo number_format($perc4,2,',','.'); ?> %
    - Età media <?php echo number_format($etamedia4,0,',','.'); ?></p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">Fascia Età<br>(oltre 70 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($Fascia5, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php echo number_format($perc5,2,',','.'); ?> %
    - Età media <?php echo number_format($etamedia5,0,',','.'); ?></p>
  </div>
</div>
</div>

<!-- Età media -->
<?php
    $select_media = "   select avg((eta)) AS media 
                        FROM sds_soci 
                        WHERE sds_soci.DATA_ENTRATA <= now()
                        AND ( (sds_soci.DATA_USCITA = '0')
                            OR (sds_soci.DATA_USCITA > now() ) )
                        AND SESSO in ('M','F')
                        AND TIPO_NAG = 'PF'  " ;
    $qry_media = mysqli_query($connection, $select_media);
    while($media = mysqli_fetch_array($qry_media)){ 
        $etamedia = $media['media'];
    }
?>
<div class="col-lg-2">
<div class="card border-warning mb-3" style="max-width: 20rem;">
  <div class="card-header">ETA' MEDIA BANCA</div>
  <div class="card-body">
    <h4 class="card-title"><?php echo number_format($etamedia, 0, ',', '.'); ?></h4>
    <p class="card-text">&nbsp;</p>
  </div>
</div>
</div>
<!-- chiudo la riga --></div>

<!-- Content Row 2 -->

<!-- chiudo la riga --></div>

<!-- Content Row 3 -->
<div class="row">	

<?php
//////////////////////////////////////////////////////////////////
// SOCIO PIU' GIOVANE e PIU' ANZIANO
//////////////////////////////////////////////////////////////////

    $select_eta = " 
            select IDSOCIO, NAG, concat(INTESTAZIONE_A, ' ', INTESTAZIONE_B) AS INTESTAZIONE, DATA_NASCITA, DATA_ENTRATA, ETA, 'NEW' as TIPO
                      from sds_soci
                      where TIPO_NAG = 'PF' 
                      AND DATA_NASCITA = (
                        SELECT max(DATA_NASCITA)
                        FROM sds_soci 
                        WHERE sds_soci.DATA_ENTRATA <= now()
                        AND ( (sds_soci.DATA_USCITA = '0')
                            OR (sds_soci.DATA_USCITA > now() ) )
                        AND SESSO in ('M','F')
                        AND TIPO_NAG = 'PF'
                      ) 
            UNION 
            select IDSOCIO, NAG, concat(INTESTAZIONE_A, ' ', INTESTAZIONE_B) AS INTESTAZIONE, DATA_NASCITA, DATA_ENTRATA, ETA, 'OLD' as TIPO
                      from sds_soci
                      where TIPO_NAG = 'PF' 
                      AND DATA_NASCITA = (
                        SELECT min(DATA_NASCITA)
                        FROM sds_soci 
                        WHERE sds_soci.DATA_ENTRATA <= now()
                        AND ( (sds_soci.DATA_USCITA = '0')
                            OR (sds_soci.DATA_USCITA > now() ) )
                        AND SESSO in ('M','F')
                        AND TIPO_NAG = 'PF'
                      )
                    " ; 
    $qry_eta = mysqli_query($connection, $select_eta);
    while($eta = mysqli_fetch_array($qry_eta)){ 

	    if 		($eta['TIPO'] == 'NEW') 
	    			{
	    				$G_prot = $eta['IDSOCIO'];
	    				$G_cag  = $eta['NAG'];
	    				$G_nome = $eta['INTESTAZIONE'];
	    				$G_dt   = $eta['DATA_NASCITA'];
	    				$G_anni = $eta['ETA'];
	    				$G_dtam = $eta['DATA_ENTRATA'];
	    			}
	    
	    if 		($eta['TIPO'] == 'OLD') 
	    			{
	    				$V_prot = $eta['IDSOCIO'];
	    				$V_cag  = $eta['NAG'];
	    				$V_nome = $eta['INTESTAZIONE'];
	    				$V_dt   = $eta['DATA_NASCITA'];
	    				$V_anni = $eta['ETA'];
	    				$V_dtam = $eta['DATA_ENTRATA'];
	    			}
	    
	    }

  //if (($_SERVER['HTTP_REFERER'] == 'http://10.197.139.22:8080/soci/soci_auth.php') OR ($_SERVER['HTTP_REFERER'] =='http://10.197.139.22:8080/soci/direzione_auth.php')) {
  // if ($_GET['auth'] == '1')  {

    // Connessione necessaria per formato Fusionchart
    $dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
    if ($dbhandle -> connect_error) {
      exit("There was an error with your connection: ".$dbhandle -> connect_error);
    }
    include ("stats/area_grafico.php"); 
  //}
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
// SOCI CON ANZIANTA' BANCARIA
//////////////////////////////////////////////////////////////////
/*
    $select_anz = " SELECT  
                    sum(if(fascia = 'Fascia 1 (1-3 anni)', qta, 0)) as qtaF1,
                    sum(if(fascia = 'Fascia 2 (4-6 anni)', qta, 0)) as qtaF2,
                    sum(if(fascia = 'Fascia 3 (7-10 anni)', qta, 0)) as qtaF3,
                    sum(if(fascia = 'Fascia 4 (11-20 anni)', qta, 0)) as qtaF4,
                    sum(if(fascia = 'Fascia 5 (oltre 20 anni)', qta, 0)) as qtaF5,
                    sum(qta) as qtaTot
                    FROM view_fasce_anzianita
                    GROUP BY Fascia" ;

    $qry_anz = mysqli_query($connection, $select_anz);
    while($anz = mysqli_fetch_array($qry_anz)){ 

      if    ($anz['qtaF1'] != 0) {$Fascia1 = $anz['qtaF1'];
                      $perc1 = $Fascia1 / $totalesoci / 10; }
      if    ($anz['qtaF2'] != 0) {$Fascia2 = $anz['qtaF2'];
                      $perc2 = $Fascia2 / $totalesoci / 10; }
      if    ($anz['qtaF3'] != 0) {$Fascia3 = $anz['qtaF3'];
                      $perc3 = $Fascia3 / $totalesoci / 10; }
      if    ($anz['qtaF4'] != 0) {$Fascia4 = $anz['qtaF4'];
                      $perc4 = $Fascia4 / $totalesoci / 10; }
      if    ($anz['qtaF5'] != 0) {$Fascia5 = $anz['qtaF5'];
                      $perc5 = $Fascia5 / $totalesoci / 10; }
  }

?>
<div class="col-lg-3">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(1-3 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia1, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php //echo number_format($perc1,2,',','.'); ?> %</p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(4-6 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia2, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php //echo number_format($perc2,2,',','.'); ?> %</p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(7-10 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia3, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php //echo number_format($perc3,2,',','.'); ?> %</p>
  </div>
</div>
</div>

<div class="col-lg-2">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(11-20 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia4, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php //echo number_format($perc4,2,',','.'); ?> %</p>
  </div>
</div>
</div>

<div class="col-lg-3">
<div class="card border-light  mb-3" style="max-width: 20rem;">
  <div class="card-header">Anzianità Rapporto<br>(oltre 20 anni)</div>
  <div class="card-body">
    <h4 class="card-title"><?php //echo number_format($Fascia5, 0, ',', '.'); ?></h4>
    <p class="card-text"><?php //echo number_format($perc5,2,',','.'); ?> %</p>
  </div>
</div>
</div>

*/
?>

<!-- chiudo la riga --></div>

<!-- FINE ULTIMO DIV -->
</div>

<?php 
  //if (($_SERVER['HTTP_REFERER'] == 'http://10.197.139.22:8080/soci/soci_auth.php') OR ($_SERVER['HTTP_REFERER'] =='http://10.197.139.22:8080/soci/direzione_auth.php')) {
  // if ($_GET['auth'] == '1')  {
    include ("stats/aree_dettaglio.php"); 
  //}
?>

</center>
</body>
</html>