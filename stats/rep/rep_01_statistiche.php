<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


?>
<center>

<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3>Statistiche Soci ChiantiBanca</h3>
	<?php echo $titolofiliale; ?></div>
</div>


<?php
//////////////////////////////////////////////////////////////////
// CONTEGGI BANCA
//////////////////////////////////////////////////////////////////

    $select_cnt_banca = " SELECT count(*) as qta, SESSO as sessoVAL
                    FROM sds_soci
                    WHERE DATA_ENTRATA <= NOW()
                    AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                    GROUP BY SESSO " ; 
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
    $select_tot = " SELECT count(*) as qta
                    FROM sds_soci 
                    WHERE DATA_ENTRATA <= NOW()
                    AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                    ".$condizionefiliale2."
                    " ; 
    //echo $select_tot;
    $qry_tot = mysqli_query($connection, $select_tot);
    while($tot = mysqli_fetch_array($qry_tot)){ 
        $totalesoci = $tot['qta'];
	}

    $select_cnt = " SELECT count(*) as qta, SESSO as sessoVAL
                    FROM sds_soci
                    WHERE DATA_ENTRATA <= NOW()
                    AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                    ".$condizionefiliale2."
                    GROUP BY SESSO" ;
    //echo $select_cnt;
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


<table border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td align="center" width="25%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">SOCI IN ESSERE</div>
        <div class="card-body">
          <p class="card-text"><i class="fa fa-users fa-3x "></i><h4 class="card-title"><?php echo number_format($totalesoci, 0, ',', '.'); ?></h4></p>
          <p class="card-text">
              <?php echo number_format($perc_totalesoci_filiale,2,',','.'); ?> % dei Soci della Banca<br><br>
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="25%">
        <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
        <div class="card-header">MASCHI</div>
        <div class="card-body">
          <p class="card-text"><i class="fa fa-users fa-3x "></i><h4 class="card-title"><?php echo number_format($maschi, 0, ',', '.'); ?></h4></p>
          <p class="card-text">
              <?php echo $rif.' '.number_format($perc_maschi_filiale,2,',','.'); ?> %
              <br>(Banca <?php echo number_format($perc_maschi_banca,2,',','.'); ?> %)
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="25%">
        <div class="card text-white bg-danger mb-3" style="max-width: 20rem;">
        <div class="card-header">FEMMINE</div>
        <div class="card-body">
          <p class="card-text"><i class="fa fa-users fa-3x "></i><h4 class="card-title"><?php echo number_format($femmine, 0, ',', '.'); ?></h4></p>
          <p class="card-text">
              <?php echo $rif.' '.number_format($perc_femmine_filiale,2,',','.'); ?> %
             <br>(Banca <?php echo number_format($perc_femmine_banca,2,',','.'); ?> %)
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="25%">
        <div class="card text-white bg-warning mb-3" style="max-width: 20rem;">
        <div class="card-header">AZIENDE</div>
        <div class="card-body">
          <p class="card-text"><i class="fa fa-building fa-3x "></i><h4 class="card-title"><?php echo number_format($aziende, 0, ',', '.');; ?></h4></p>
          <p class="card-text">
              <?php echo $rif.' '.number_format($perc_aziende_filiale,2,',','.'); ?> %
              <br>(Banca <?php echo number_format($perc_aziende_banca,2,',','.'); ?> %)
          </p>
        </div>
      </div>
    </td>
  </tr>
</table>


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
                    ".$condizionefiliale3."
                  	GROUP BY Fascia" ;
    //echo $select_etaB;
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
				    WHERE qta <> -1
            ".$condizionefiliale."
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

 
?>

<table border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td align="center" width="20%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">Fascia Età<br>(18-30 anni)</div>
        <div class="card-body">
          <h4 class="card-title"><?php echo number_format($Fascia1, 0, ',', '.'); ?></h4>
          <p class="card-text"><small><?php echo $rif.' '.number_format($perc1,2,',','.'); ?> %
              <br>(Banca <?php echo number_format($perc1B,2,',','.'); ?> %)</small>
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="20%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">Fascia Età<br>(31-50 anni)</div>
        <div class="card-body">
          <h4 class="card-title"><?php echo number_format($Fascia2, 0, ',', '.'); ?></h4>
          <p class="card-text"><small><?php echo $rif.' '.number_format($perc2,2,',','.'); ?> %
              <br>(Banca <?php echo number_format($perc2B,2,',','.'); ?> %)</small>
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="20%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">Fascia Età<br>(51-60 anni)</div>
        <div class="card-body">
          <h4 class="card-title"><?php echo number_format($Fascia3, 0, ',', '.'); ?></h4>
          <p class="card-text"><small><?php echo $rif.' '.number_format($perc3,2,',','.'); ?> %
             <br>(Banca <?php echo number_format($perc3B,2,',','.'); ?> %)</small>
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="20%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">Fascia Età<br>(61-70 anni)</div>
        <div class="card-body">
          <h4 class="card-title"><?php echo number_format($Fascia4, 0, ',', '.'); ?></h4>
          <p class="card-text"><small><?php echo $rif.' '.number_format($perc4,2,',','.'); ?> %
              <br>(Banca <?php echo number_format($perc4B,2,',','.'); ?> %)</small>
          </p>
        </div>
      </div>
    </td>
    <td align="center" width="20%">
        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
        <div class="card-header">Fascia Età<br>(oltre 70 anni)</div>
        <div class="card-body">
          <h4 class="card-title"><?php echo number_format($Fascia5, 0, ',', '.'); ?></h4>
          <p class="card-text"><small><?php echo $rif.' '.number_format($perc5,2,',','.'); ?> %
             <br>(Banca <?php echo number_format($perc5B,2,',','.'); ?> %)</small>
          </p>
        </div>
      </div>
    </td>
  </tr>
</table>


<?php
//////////////////////////////////////////////////////////////////
// SOCIO PIU' GIOVANE e PIU' ANZIANO
//////////////////////////////////////////////////////////////////

    $select_eta = "   select IDSOCIO, NAG, concat(INTESTAZIONE_A, ' ', INTESTAZIONE_B) AS INTESTAZIONE,           
                      DATA_NASCITA, DATA_ENTRATA, ETA, 'NEW' as TIPO
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
                      ".$condizionefiliale2."
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
                      ".$condizionefiliale2."
                       ) 
					          " ;
    //echo $select_eta;                
    $qry_eta = mysqli_query($connection, $select_eta);
    while($eta = mysqli_fetch_array($qry_eta)){ 

      if    ($eta['TIPO'] == 'NEW') 
            {
              $G_prot = $eta['IDSOCIO'];
              $G_cag  = $eta['NAG'];
              $G_nome = $eta['INTESTAZIONE'];
              $G_dt   = substr($eta['DATA_NASCITA'],6,2).'/'.substr($eta['DATA_NASCITA'],4,2).'/'.substr($eta['DATA_NASCITA'],0,4);
              $G_anni = $eta['ETA'];
              $G_dtam = $eta['DATA_ENTRATA'];
            }
      
      if    ($eta['TIPO'] == 'OLD') 
            {
              $V_prot = $eta['IDSOCIO'];
              $V_cag  = $eta['NAG'];
              $V_nome = $eta['INTESTAZIONE'];
              $V_dt   = substr($eta['DATA_NASCITA'],6,2).'/'.substr($eta['DATA_NASCITA'],4,2).'/'.substr($eta['DATA_NASCITA'],0,4);
              $V_anni = $eta['ETA'];
              $V_dtam = $eta['DATA_ENTRATA'];
            }
      
      }

?>

<table border="0" align="center" cellpadding="2" cellspacing="2">
  <tr>
    <td align="center" width="20%">
      <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
          <div class="card-header">Socio più GIOVANE</div>
          <div class="card-body">
            <h4 class="card-title"><?php echo $G_nome; ?></h4>
            <p class="card-text"><?php echo $G_anni; ?> anni </p>
            <small>
            <a class="text-white" href="#">NAG <?php echo $G_cag; ?></a><br>
              Nato il <?php echo $G_dt; ?> - 
              Socio dal <?php echo $G_dtam; ?> <br>
          </small>
        </div>
      </div>
    </td>
    <td align="center" width="20%">
      <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
          <div class="card-header">Socio più ANZIANO</div>
          <div class="card-body">
            <h4 class="card-title"><?php echo $V_nome; ?></h4>
            <p class="card-text"><?php echo $V_anni; ?> anni </p>
            <small>
            <a class="text-white" href="#">NAG <?php echo $V_cag; ?></a><br>
              Nato il <?php echo $V_dt; ?> - 
              Socio dal <?php echo $V_dtam; ?> <br>
          </small>
        </div>
      </div>
    </td>
  </tr>
</table>

</center>
