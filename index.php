<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2022)
// v.3.0
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
$ver = "v1.00 (2020) - Primo rilascio";
$ver = "v2.00 (2021) - Unificazione con ex Portale Mutua";
$ver = "v3.00 (2022) - Passaggio a Sicra";

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");
//include("counter/counter.php");

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
// Mi connetto al database SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<!--
<style>
body {
  background: url('img/tmp.jpg') no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  background-size: cover;
  -o-background-size: cover;
}
</style>
-->


<!--
*****************************************************************************
POPUP ALL'APERTURA
*****************************************************************************
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ATTENZIONE</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
        <p>Il Portale Mutua è in corso di migrazione all'interno del <b>Portale Soci</b>
          che diventerà a breve l'unico portale destinato al mondo delle due compagini sociali.<br>
          <br>
          Se vuoi proseguire su questo portale clicca la X in alto<br>
          altrimenti clicca qui per accedere al <a href="/soci/index.php">Portale Soci</a>.</p>

            </div>
        </div>
    </div>
</div>
-->

<?php
// CONTATORE ASSEMBLEA

      $cd = "<script type='text/javascript'>
          CountDownTimer('04/27/2022 10:00:00 AM', 'countdown');
          function CountDownTimer(date, id) {
               var end = new Date(date);

               var _second = 1000;
               var _minute = _second * 60;
               var _hour = _minute * 60;
               var _day = _hour * 24;
               var timer;

               function showRemaining() {
                   var now = new Date();
                   var distance = end - now;
                   if (distance < 0) {

                       clearInterval(timer);
                       document.getElementById(id).innerHTML = 'EXPIRED!';

                       return;
                   }
                   var days = Math.floor(distance / _day);
                   var hours = Math.floor((distance % _day) / _hour);
                   var minutes = Math.floor((distance % _hour) / _minute);
                   var seconds = Math.floor((distance % _minute) / _second);

                   document.getElementById(id).innerHTML = days + ' gg ';
                   document.getElementById(id).innerHTML += hours + ' hh ';
                   document.getElementById(id).innerHTML += minutes + ' mm ';
                   document.getElementById(id).innerHTML += seconds + ' ss ';
               }

               timer = setInterval(showRemaining, 1000);
           }
           </script>";

          $cd .= '&nbsp;&nbsp;&nbsp;&nbsp;<small><span id="countdown"></span></small>';

/*
          // Testo da inserire nel riquadro
           <br><small><i>Mancano<?php echo $cd; ?></i></small>
*/
?>



<!--  
*****************************************************************************
RIQUADRI OPERATIVI
*****************************************************************************
-->

<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Content Row 1 -->
  <div class="row justify-content-between">

    <div class="col-md-3">
          <div class="card shadow border-info mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-light">INFO GENERALI</h6>
            </div>
            <div class="card-body float-sm-left">
              <div class="text-center">
              </div>

              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="statistiche.php" title="Statistiche banca" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-chart-pie fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Dati Statistici
                </a>
                <br>
                <a href="assemblea_auth.php" title="Assemblea banca" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
                  Assemblea Soci
                </a>
                <br>
                <a href="eventi_gestionale.php" title="Eventi banca" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-hand-peace fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;&nbsp;Eventi
                </a>                
                <br><br>
              </h5>

            </div>
          </div>
    </div>            

<?php
  if ( (in_array($filiale_id, array('998','999')) ) OR (in_array($usr_id, array('00390')) ) )
  {
    $colore_riservato = 'warning';
    $titolo_riservato = ' SOCI ';
    $contenuto_riservato = '
        <table border="0" width="90%">
          <tr>
            <td width="50%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="stats/situazione.php?f=999" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
                  Situazione Soci alla data odierna
                </a>
                <br>
                <a href="stats/situazione_plafond.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-euro-sign fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;&nbsp;&nbsp;Situazione Plafond
                </a>
                <br>
                <a href="lista_domande_daregolare.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Lista Domande da regolare
                </a>    
                <br>
                <a href="lista_domande.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Lista Domande presenti
                </a>       
              </h5>   
            </td>
            <td width="50%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="stats/previsionale.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Previsionale
                </a>
                <br>
                <a href="stats/liquidazioni_grafico.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Liquidazioni
                </a>
                <br>
                <a href="lista_ammissioni.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Ammissioni a Socio
                </a>
                <br>
                <a href="lista_trasferimenti.php" title="Riservato Soci" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Trasferimenti tra Soci
                </a>
              </h5>   
            </td>
          </tr>
        </table>            
              ';
}
  elseif ( ($filiale_id <= '100') AND ($filiale_id != '90') )
  {
    $filiale = $filiale_id;
    $colore_riservato = 'success';
    $titolo_riservato = ' FILIALE ';
    $contenuto_riservato = '
        <table border="0" width="90%">
          <tr>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="stats/situazione.php?f='.$filiale.'&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
                  Situazione Soci 
                </a>
                <br>
                <a href="stats/previsionale.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Previsionale
                </a>
                <br>
                <a href="lista_domande_daregolare.php" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande da regolare
                </a>    
                <br>
                <a href="lista_domande.php?filiale=('.$filiale.')" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande presenti
                </a>       
              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="lista_ammissioni.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Ammissioni a Socio
                </a>
                <br>
                <a href="lista_trasferimenti.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Trasferimenti tra Soci
                </a>
                <br>
                <a href="stats/giovani_grafico.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Giovani 17-35 anni
                </a>
                <br>
                <a href="stats/liquidazioni_grafico.php?key='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Liquidazioni
                </a>
                <br>

              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="check_zonecompetenza.php?fuorizona=italia&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Zone Competenza Italia
                </a>
                <br>
                <a href="check_zonecompetenza.php?fuorizona=estero&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Zone Competenza Estero
                </a>
                <br>
                <a href="deceduti.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Deceduti
                </a>
                <br>
                <a href="lista_usciti.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Usciti
                </a>
              </h5>   
            </td>            
          </tr>
        </table>            
              ';
}
  elseif(in_array($filiale_id, array('100a','100b','100c','100d'))) 
  {
    $filiale = 100;
    $colore_riservato = 'success';
    $titolo_riservato = ' CENTRO IMPRESE ';
    $contenuto_riservato = '
        <table border="0" width="90%">
          <tr>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="stats/situazione.php?f='.$filiale.'&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
                  Situazione Soci 
                </a>
                <br>
                <a href="stats/previsionale.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Previsionale
                </a>
                <br>
                <a href="lista_domande_daregolare.php" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande da regolare
                </a>    
                <br>
                <a href="lista_domande.php?filiale=('.$filiale.')" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande presenti
                </a>       
              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="lista_ammissioni.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Ammissioni a Socio
                </a>
                <br>
                <a href="lista_trasferimenti.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Trasferimenti tra Soci
                </a>
                <br>
                <a href="stats/giovani_grafico.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Giovani 17-35 anni
                </a>
                <br>
                <a href="stats/liquidazioni_grafico.php?key='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Liquidazioni
                </a>
                <br>

              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="check_zonecompetenza.php?fuorizona=italia&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Zone Competenza Italia
                </a>
                <br>
                <a href="check_zonecompetenza.php?fuorizona=estero&filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Zone Competenza Estero
                </a>
                <br>
                <a href="deceduti.php?filiale='.$filiale.'" title="Riservato Filiale" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Deceduti
                </a>
              </h5>   
            </td>            
          </tr>
        </table>            
              ';
}
  elseif(in_array($filiale_id, array('995'))) 
  {
    $filiale = $filiale_id;
    $colore_riservato = 'success';

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
    }

    $titolo_riservato = ' - AREA '.$area;

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

    $contenuto_riservato = '
        <table border="0" width="90%">
          <tr>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="stats/situazione.php?f=&filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
                  Situazione Soci 
                </a>
                <br>
                <a href="stats/previsionale.php?filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Previsionale
                </a>
                <br>
                <a href="lista_domande_daregolare.php" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande da regolare
                </a>    
                <br>
                <a href="lista_domande.php?filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Domande presenti
                </a>       
              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="lista_ammissioni.php?filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Ammissioni a Socio
                </a>
                <br>
                <a href="lista_trasferimenti.php?filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-list-alt fa-1x text-lightgray-300 col-auto"></i>
                  Trasferimenti tra Soci
                </a>
                <br>
                <a href="stats/giovani_grafico.php?filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Giovani 17-35 anni
                </a>
                <br>
                <a href="stats/liquidazioni_grafico.php?key='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-poll fa-1x text-lightgray-300 col-auto"></i>
                  &nbsp;Liquidazioni
                </a>
                <br>

              </h5>   
            </td>
            <td width="33%" valign="top">
              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="motivazioni_check.php?tipo=area&filiale='.$chiaveURL.'" title="Riservato Area" style="color:#FFFFFF;text-decoration: none;" target="_blank">
                  <i class="fas fa-check-square fa-1x text-lightgray-300 col-auto"></i>
                  Motivazioni Ingressi
                </a>
              </h5>   
            </td>            
          </tr>
        </table>            
              ';
}
else 
  { $colore_riservato = 'info';
    $titolo_riservato = ' non presente';
    $contenuto_riservato = ' Non presente per questo Utente<br><br><br><br>';
  }
?>    

    <div class="col-md-6">
          <div class="card shadow border-<?php echo $colore_riservato; ?> mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-light">
                <i class="fas fa-user-lock fa-1x text-lightgray-300 col-auto"></i>
                AREA RISERVATA <?php echo $titolo_riservato; ?></h6>
            </div>
            <div class="card-body float-sm-left">
              <div class="text-center">
              </div>

              <?php echo $contenuto_riservato; ?>

            </div>
          </div>
    </div>   

    <div class="col-md-3">
          <div class="card shadow border-info mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-light">UTILITY<i class="fas fa-cogs fa-1x text-lightgray-300 col-auto"></i></h6>
            </div>
            <div class="card-body float-sm-left">
              <div class="text-center">
              </div>

              <h5 class="card-title" style="color:#FFFFFF;">
                <a href="utility.php" title="Ulteriori quote" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                  Addendum Acquisto Ulteriori Quote
                </a>                
                <br>
                <a href="utility.php" title="Under35 - ChiantiMutua" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                  Addendum Rateizzazione U35 / CM
                </a>
                <br>
                <a href="utility.php" title="Donazione Under35" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                  Addendum Donazione U35
                </a>
                <br>
                <a href="filiali_matricekm.php" title="Utility banca" style="color:#FFFFFF;text-decoration: none;">
                  <i class="fas fa-car fa-1x text-lightgray-300 col-auto"></i>
                  Matrice distanze KM tra Filiali
                </a>                
              </h5>

            </div>
          </div>
    </div>   

  <!-- FINE RIGA 1 -->
  </div>

<!--  
*****************************************************************************
CONTEGGI BASE SOCIALE & NEWS
*****************************************************************************
-->

<!-- Content Row 2 -->
<div class="row align-items-start">

<div class="col-md-3">
  <!-- Conteggio Soci Banca in essere -->
    <?php
      // --------------------------
      // CONTEGGI SOCI CHIANTIBANCA
      // --------------------------
          $select_cnt = " SELECT count(*) as qta, SESSO
                          FROM SDS_SOCI
                          WHERE DATA_ENTRATA <= NOW()
                          AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())
                          GROUP BY SESSO
                          UNION
                          SELECT count(*) as qta, 'TOTALE' as SESSO
                          FROM SDS_SOCI
                          WHERE DATA_ENTRATA <= NOW()
                          AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW()) " ;

          $qry_cnt = mysqli_query($connection, $select_cnt);
          while($cnt = mysqli_fetch_array($qry_cnt))
          {
            if ($cnt['SESSO'] == 'M') 
              { $PFM = number_format($cnt['qta'], 0, ',', '.'); }
            if ($cnt['SESSO'] == 'F') 
              { $PFF = number_format($cnt['qta'], 0, ',', '.'); }
            if ($cnt['SESSO'] == ' ') 
              { $PG = number_format($cnt['qta'], 0, ',', '.'); }
            if ($cnt['SESSO'] == 'TOTALE') 
              { $totalesoci = number_format($cnt['qta'], 0, ',', '.'); }
          }
        
      ?>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-light">Sintesi Soci ChiantiBanca </h6>
      </div>
      <div class="card-body float-sm-left">
        <div class="text-center">
        </div>

          <table border ="0" width="80%">
              <tr>
                  <td width="60%" align="center" rowspan="3"><img src="img/ico_people.png" height="60"><br><small>Totale Soci</small><br><h4 class="card-title"><?php echo $totalesoci; ?></h4></td>
                  <td align="right"><img src="img/ico_man.png" height="30" title="Persone Fisiche Maschi"></td><td align="right"><span class="card-title"><?php echo $PFM; ?></span>
<div class="progress">
  <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: <?php echo round($PFM/$totalesoci*100) ; ?>%;" aria-valuenow="<?php echo round($PFM/$totalesoci*100) ; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($PFM/$totalesoci*100) ; ?>%</div>
</div>
                  </td>
              </tr>
              <tr>
                  <td align="right"><img src="img/ico_woman.png" height="30" title="Persone Fisiche Femmine"></td><td align="right"><span class="card-title"><?php echo $PFF; ?></span>
<div class="progress">
  <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: <?php echo round($PFF/$totalesoci*100) ; ?>%;" aria-valuenow="<?php echo round($PFF/$totalesoci*100) ; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($PFF/$totalesoci*100) ; ?>%</div>
</div>
                  </td>
              </tr>
              <tr>
                  <td align="right"><img src="img/ico_azienda.png" height="30" title="Persone Giuridiche"></td><td align="right"><span class="card-title"><?php echo $PG; ?></span>
<div class="progress">
  <div class="progress-bar progress-bar-striped bg-light" role="progressbar" style="width: <?php echo round($PG/$totalesoci*100) ; ?>%;" aria-valuenow="<?php echo round($PG/$totalesoci*100) ; ?>" aria-valuemin="0" aria-valuemax="100"> <?php echo round($PG/$totalesoci*100) ; ?>%</div>
</div>
                  </td>

              </tr>
          </table>

      </div>
    </div>
</div>

<div class="col-md-6">
            <!-- News -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-light">Ultime novità</h6>
                </div>
                <div class="card-body">
                  <div class="text-center">
                  </div>

          <!-- FINESTRA NEWS -->
                  <?php 
                  /*
          $query = mysqli_query($connection, 
          "SELECT DATE_FORMAT(datainsert,'%d.%m.%Y %H:%i') as datainsert, newstitolo, newscategoria, newspost
           FROM   tab_news");
            while($news=mysqli_fetch_array($query)){ 
                     echo html_entity_decode($news['newspost']); 
                  } 
                  */
              ?>

          <a href="img/sondaggio_portale_20240110.jpg" target="_blank"><img src="img/sondaggio_homeesito.png" width="100%" ></a>

                </div>
              </div>
</div>

<div class="col-md-3">
  <!-- Conteggio Soci Mutua in essere -->
    <?php
      // --------------------------
      // CONTEGGI SOCI CHIANTIMUTUA
      // --------------------------
      
          $select_cntM = " SELECT count(*) as qta, sesso
                          FROM TAB_MUTUA 
                          GROUP BY sesso" ;
          $qry_cntM = mysqli_query($connection, $select_cntM);
          while($cntM = mysqli_fetch_array($qry_cntM)){ 
            if    ($cntM['sesso'] == 'M') 
                  {$maschiM = number_format($cntM['qta'], 0, ',', '.'); $mM = $cntM['qta']-1; } // tolgo ChiantiBanca
            elseif  ($cntM['sesso'] == 'F') 
                  {$femmineM = number_format($cntM['qta'], 0, ',', '.'); $fM = $cntM['qta'];} 
            else {}
        }
      
          $totalesociM = $mM + $fM + 1 ; //echo $totalesoci
      ?>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-light">Sintesi Soci ChiantiMutua </h6>
      </div>
      <div class="card-body float-sm-left">
        <div class="text-center">
        </div>

          <table border ="0" width="80%">
              <tr>
                  <td width="60%" align="center" rowspan="3"><img src="img/ico_people.png" height="60"><br><small>Totale Soci</small><br><h4 class="card-title"><?php echo number_format($totalesociM, 0, ',', '.'); ?></h4></td>
                  <td align="right"><img src="img/ico_man.png" height="30"></td><td align="right"><span class="card-title"><?php echo $maschiM; ?></span>
<div class="progress">
  <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo round($mM/$totalesociM*100) ; ?>%;" aria-valuenow="<?php echo round($mM/$totalesociM*100) ; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($mM/$totalesociM*100) ; ?>%</div>
</div>
                  </td>
              </tr>
              <tr>
                  <td align="right"><img src="img/ico_woman.png" height="30"></td><td align="right"><span class="card-title"><?php echo $femmineM; ?></span>
<div class="progress">
  <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: <?php echo round($fM/$totalesociM*100) ; ?>%;" aria-valuenow="<?php echo round($fM/$totalesociM*100) ; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($fM/$totalesociM*100) ; ?>%</div>
</div>
                </td>
              </tr>
          </table>

      </div>
    </div>
</div>

<!-- FINE RIGA 2 -->
</div>

<!-- Content Row 3 -->
<div class="row align-items-start">

<div class="col-md-3">
  <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-light">Trend Soci da 01.01.2026</h6>
      </div>
      <div class="card-body float-sm-left">
        <div class="text-center">

            <?php
            // STATISTICHE ANDAMENTALI ANNO IN CORSO
            $select_trend = "   SELECT 'A - Soci inizio' as Tipo,  '01/01/2026'  as Periodo, count(*) as qta
                                FROM
                                  SDS_SOCI
                                WHERE
                                  str_to_date(DATA_ENTRATA,'%d/%m/%Y') <=  str_to_date('01/01/2026','%d/%m/%Y')  
                                AND
                                  ( DATA_USCITA =  '0'  
                                OR
                                  str_to_date(DATA_USCITA,'%d/%m/%Y') >  str_to_date('01/01/2026','%d/%m/%Y')  )
                                  
                                UNION

                                SELECT 'B - Soci incrementati' as Tipo, '01/01/2026' as Periodo, count(*) as qta
                                FROM
                                  SDS_SOCI  
                                WHERE
                                  str_to_date(DATA_ENTRATA,'%d/%m/%Y') >=  str_to_date('01/01/2026','%d/%m/%Y')  
                                AND
                                  DATA_ENTRATA < NOW()
                                  
                                UNION

                                SELECT 'C - Soci decrementati' as Tipo, '01/01/2026' as Periodo, count(*) as qta
                                FROM
                                  SDS_SOCI  
                                WHERE
                                     str_to_date(DATA_USCITA,'%d/%m/%Y') >= str_to_date('01/01/2026','%d/%m/%Y') AND
                                     DATA_USCITA <= NOW()

                                UNION

                                SELECT 'D - Soci alla data odierna' as Tipo,  NOW() as Periodo, count(*) as qta
                                FROM
                                  SDS_SOCI  
                                WHERE
                                     DATA_ENTRATA <= NOW()
                                     AND (DATA_USCITA =  '0' OR
                                         DATA_USCITA > NOW())

                                ORDER BY 1 " ;

           $qry_trend = mysqli_query($connection, $select_trend);

              echo '<table border="0" width="98%">';

              while($trend = mysqli_fetch_array($qry_trend)){ 
                  
                if ($trend['Tipo'] == 'A - Soci inizio')               {$soci_inizio = $trend['qta'];}
                if ($trend['Tipo'] == 'D - Soci alla data odierna')    {$soci_fine   = $trend['qta'];}

                echo '
                      <tr>
                          <td align="left"><b>'.$trend['Tipo'].'</b></td>
                          <td align="right"><b>'.number_format($trend['qta'], 0, ',', '.').'</b></td>
                      </tr>';

              }

                if (($soci_fine - $soci_inizio) > 0) {$plusminus = ' style="color:lightgreen;"> &#43;'; } else {$plusminus = ' style="color:lightred;"> &#8722;'; }
                echo '
                      <tr>
                          <td align="left"></td>
                          <td align="right"><b '.$plusminus.' '.number_format(($soci_fine - $soci_inizio), 0, ',', '.').'</b></td>
                      </tr>';
                      
              echo '</table>';

            ?>

        </div>
      </div>
    </div>
</div>  

<div class="col-md-3">

              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-light">Dati Consolidati (Banca / Soci)</h6>
                </div>
                <div class="card-body float-sm-left">
                  <div class="text-center">
                  </div>

      <?php
      $daticonsolidati = " 
                SELECT
                    DATA_RIF,
                    BA_NAG_TOTALI,
                    SO_NAG_TOTALI,
                    BA_CC_TOTALI,
                    SO_CC_TOTALI,
                    BA_TOT_QUOTE,
                    BA_PATRIMONIO,
                    BA_ACCORDATO_TOTALE,
                    BA_UTILIZZATO_TOTALE,
                    BA_DEPOSITI_TOTALE,
                    SO_ACCORDATO_TOTALE,
                    SO_UTILIZZATO_TOTALE,
                    SO_DEPOSITI_TOTALE
                from sds_soci_dati_consolidati
                group by DATA_RIF
                ORDER BY MAX(DATA_RIF) desc limit 1
                ";

       $qry_ds = mysqli_query($connection, $daticonsolidati);

          echo '<table border="0" width="98%" style="font-size:13px;">';

          while($ds = mysqli_fetch_array($qry_ds)){ 

                $coloreds = '<span style="color:lightgreen;">';

                echo "
                <tr>
                  <td align='left' width='70%'><b>NAG&nbsp;</td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_NAG_TOTALI'],0,',','.')."&nbsp;&nbsp;".$coloreds."(".number_format($ds['SO_NAG_TOTALI'],0,',','.').")</td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Conti Correnti&nbsp;</td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_CC_TOTALI'],0,',','.')."&nbsp;&nbsp;".$coloreds."(".number_format($ds['SO_CC_TOTALI'],0,',','.').")</td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Numero Azioni&nbsp;</td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_TOT_QUOTE'],0,',','.')." </td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Capitale Sociale&nbsp;</td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_PATRIMONIO'],0,',','.')."</td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Accordato&nbsp;&euro; </td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_ACCORDATO_TOTALE'],0,',','.')."&nbsp;&nbsp;".$coloreds."(".number_format($ds['SO_ACCORDATO_TOTALE'],0,',','.').")</td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Utilizzato&nbsp;&euro; </td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_UTILIZZATO_TOTALE'],0,',','.')."&nbsp;&nbsp;".$coloreds."(".number_format($ds['SO_UTILIZZATO_TOTALE'],0,',','.').")</td>
                </tr>
                <tr>
                  <td align='left' width='70%'><b>Depositi&nbsp;&euro; </td>
                  <td align='right' width='30%'><b>".number_format($ds['BA_DEPOSITI_TOTALE'],0,',','.')."&nbsp;&nbsp;".$coloreds."(".number_format($ds['SO_DEPOSITI_TOTALE'],0,',','.').")</td>
                </tr>                      
                ";

              }

    echo '</table>';

?>                

                </div>
              </div>
</div>

<div class="col-md-3">

<?php
if ($filiale_id >= 990) 
  {$filiale_andamentale = ' Banca';
   $condizione1 = 'AND Filiale <= 999 ';
   $condizione2 = 'AND Filiale_Domanda <= 999 ';
   $condizione3 = 'AND FILIALE_CAPOFILA <= 999 ';
  }
  elseif(in_array($filiale_id, array('100a','100b','100c','100d'))) 
  {$filiale_andamentale = 'Filiale 100';
   $condizione1 = 'AND Filiale = 100';
   $condizione2 = 'AND Filiale_Domanda = 100';
   $condizione3 = 'AND FILIALE_CAPOFILA = 100';
  }  
else 
  {$filiale_andamentale = 'Filiale '.$filiale_id;
   $condizione1 = 'AND Filiale = '.$filiale_id;
   $condizione2 = 'AND Filiale_Domanda = '.$filiale_id;
   $condizione3 = 'AND FILIALE_CAPOFILA = '.$filiale_id;
  }
?>

              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-light">Andamentale <?php echo $filiale_andamentale; ?></h6>
                </div>
                <div class="card-body float-sm-left">
                  <div class="text-center">
                  </div>

      <?php
      $andamentale = " 
            SELECT 'Cessioni a Banca' as Tipo, count(*) as qta, Filiale, sum(Valore_Nominale) as Valore_Nominale 
              FROM tab_xls_cessionibanca
              WHERE 
              Rimborsato <> 'S' 
              ".$condizione1."
              GROUP BY Filiale 
            UNION
            SELECT 'Cessioni a Banca' as Tipo, count(*) as qta, 'Banca' as Filiale, sum(Valore_Nominale) as Valore_Nominale 
              FROM tab_xls_cessionibanca
              WHERE 
              Rimborsato <> 'S' 
              ".$condizione1."
            UNION
            SELECT 'Esclusioni' as Tipo, count(*) as qta, Filiale, sum(Valore_Nominale) as Valore_Nominale 
              FROM tab_xls_esclusioni
              WHERE 
              MovimentoSicra = 'ID' 
              AND Escluso_x_Passaggio_a_Sofferenze != 'S'
              ".$condizione1."
              GROUP BY Filiale 
            UNION
            SELECT 'Esclusioni' as Tipo, count(*) as qta, 'Banca' as Filiale, sum(Valore_Nominale) as Valore_Nominale 
              FROM tab_xls_esclusioni
              WHERE 
              MovimentoSicra = 'ID' 
              AND Escluso_x_Passaggio_a_Sofferenze != 'S'
              ".$condizione1."
            UNION
            select 'Domande Ammissione in esame' as Tipo, count(*) as qta, Filiale_Domanda as Filiale, sum((nazioni * 30.33)) as Valore_Nominale
              from sds_soci_domande 
              where ctipodom not in ('DL','DQ','DR','DT','DS','DD')
              and data_delibera = '0'
              -- AND TRASFERIMENTO_DA_IDSOCIO = '0' 
              -- AND DEFUNTO_IDSOCIO = '0'
              ".$condizione2."
              group by Filiale_Domanda
            UNION
            select 'Domande Ammissione in esame' as Tipo, count(*) as qta, 'Banca' as Filiale, sum((nazioni * 30.33)) as Valore_Nominale
              from sds_soci_domande 
              where ctipodom not in ('DL','DQ','DR','DT','DS','DD')
              and data_delibera = '0'
              -- AND TRASFERIMENTO_DA_IDSOCIO = '0' 
              -- AND DEFUNTO_IDSOCIO = '0'
              ".$condizione2."
            UNION
            select 'Domande Ammissione senza PDF' as Tipo, count(*) as qta, Filiale_Domanda as Filiale, 0 as Valore_Nominale
              from sds_soci_domande_nopdf
              where nag <> 0
              ".$condizione2."
              group by Filiale_Domanda
            UNION
            select 'Domande Ammissione senza PDF' as Tipo, count(*) as qta, 'Banca' as Filiale, 0 as Valore_Nominale
              from sds_soci_domande_nopdf 
              where nag <> 0
              ".$condizione2."              
            UNION 
            select 'Domande Ammissione da motivare' as Tipo, count(*) as qta, Filiale_Domanda as Filiale, sum((nazioni * 30.33)) as Valore_Nominale
              from sds_soci_domande d
              where ctipodom not in ('DL','DQ','DR','DT','DS','DD')
              and data_delibera = '0'
              and d.nag not in (select m.nag from tab_motivazioni m)
              ".$condizione2."
              group by Filiale_Domanda
            UNION
            select 'Domande Ammissione da motivare' as Tipo, count(*) as qta, 'Banca' as Filiale, sum((nazioni * 30.33)) as Valore_Nominale
              from sds_soci_domande d
              where ctipodom not in ('DL','DQ','DR','DT','DS','DD')
              and data_delibera = '0'
              and d.nag not in (select m.nag from tab_motivazioni m)
              ".$condizione2."
                ";

       $qry_and = mysqli_query($connection, $andamentale);

          echo '<table border="0" width="98%" style="font-size:13px;">';

          while($and = mysqli_fetch_array($qry_and)){ 
          
            if ($and['Filiale'] == 'Banca') {
              echo" <tr>
                      <td align='left' width='50%'><b>".$and['Tipo']."</td>
                      <td align='right' width='10%'><b>".number_format($and['qta'],0,',','.')."</td>
                      <td align='right' width='20%'><b>&euro; ".number_format($and['Valore_Nominale'],0,',','.')."</td>
                    </tr>";
            }

          }

      // ZONE DI COMPETENZA DA VALIDARE
      $zone = " 
              SELECT count(*) as qta,
              'Estero' as Tipo,
              FILIALE_CAPOFILA as Filiale, 
              CASE status_esito
                      WHEN 'Valido' THEN '1'
                      WHEN 'Escludere' THEN '2'
                      ELSE '3' 
                      END as counter
              FROM sds_soci as c left join tab_comuni_soci_note as n
              ON c.nag = n.cag
              WHERE status_esito is null
              AND PROVINCIA_RES = 'SE'
              AND SOCIO_ISTITUTO = '1'
              ".$condizione3."
              group by FILIALE_CAPOFILA
              UNION
              SELECT count(*) as qta,
              'Estero' as Tipo,
              'Banca' as Filiale, 
              CASE status_esito
                      WHEN 'Valido' THEN '1'
                      WHEN 'Escludere' THEN '2'
                      ELSE '3' 
                      END as counter
              FROM sds_soci as c left join tab_comuni_soci_note as n
              ON c.nag = n.cag
              WHERE status_esito is null
              AND PROVINCIA_RES = 'SE'
              AND SOCIO_ISTITUTO = '1'
              ".$condizione3."
              UNION
              SELECT count(*) as qta,
              'Italia' as Tipo,
              FILIALE_CAPOFILA as Filiale, 
              CASE status_esito
                      WHEN 'Valido' THEN '1'
                      WHEN 'Escludere' THEN '2'
                      ELSE '3' 
                      END as counter
              FROM sds_soci as c left join tab_comuni_soci_note as n
              ON c.nag = n.cag
              WHERE status_esito is null
              AND PA_3 IN (998,999)
              AND PROVINCIA_RES <> 'SE'
              AND SOCIO_ISTITUTO = '1'
              ".$condizione3."
              group by FILIALE_CAPOFILA
              UNION
              SELECT count(*) as qta,
              'Italia' as Tipo,
              'Banca' as Filiale, 
              CASE status_esito
                      WHEN 'Valido' THEN '1'
                      WHEN 'Escludere' THEN '2'
                      ELSE '3' 
                      END as counter
              FROM sds_soci as c left join tab_comuni_soci_note as n
              ON c.nag = n.cag
              WHERE status_esito is null
              AND PA_3 IN (998,999)
              AND PROVINCIA_RES <> 'SE'
              AND SOCIO_ISTITUTO = '1'
              ".$condizione3."
                ";

       $qry_zone = mysqli_query($connection, $zone);

          while($zone = mysqli_fetch_array($qry_zone)){ 
          
            if ($zone['Filiale'] == 'Banca') {
              echo" <tr>
                      <td align='left' width='50%'><b>Soci ".$zone['Tipo']." da verificare</td>
                      <td align='right' width='10%'><b>".number_format($zone['qta'],0,',','.')."</td>
                      <td align='right' width='20%'><b>--</td>
                    </tr>";
            }
          }


    echo '</table>';
      ?>

                </div>
              </div>
</div>

<div class="col-md-3">
  <!-- Comuni di competenza -->
  <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-light">Comuni Toscani & competenza territoriale</h6>
      </div>
      <div class="card-body float-sm-left">
        <div class="text-center">
        </div>
            <center>
              <a href="zonecompetenza.php"><img src="img/ico_toscana_bianca.png" alt="Comuni della Toscana (e zone di competenza ChiantiBanca)" title="Comuni della Toscana (e zone di competenza ChiantiBanca)" width="140" ></a>
            </center>
    
      </div>
    </div>
</div>  


</div>
<!--  
*****************************************************************************
FOOTER
*****************************************************************************
-->
<footer class="text-muted ">
  <div class="copyright text-center my-auto">

<!--  
*****************************************************************************
DATI DI AGGIORNAMENTO
*****************************************************************************
-->

  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Aggiornamenti (Sadas/Sicra/XLS)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">

            <?php
            // Ultimo Aggiornamento 
            $select_aggto = " SELECT *
                              FROM tab_ultimo_caricamento WHERE tipo in ('SICRA','SADAS','SINERGIA','XLS','MUTUA','SIB')" ;
            $qry_aggto = mysqli_query($connection, $select_aggto);
              while($aggto = mysqli_fetch_array($qry_aggto)){ 

                    // Date per semaforo status aggiornamento
                    $f0 = date("Ymd") ;
                    $f1 = ( date("Ymd") - 7 );
                    $f2 = ( date("Ymd") - 15 );
                    $limite = date("Ymd",strtotime($aggto['caricamento'])); 

                    if ( ($limite <= $f0) AND ($limite > $f1) ) {$semaforo = "<img src='img/ico_pallino_green.png'>";}
                    if ( ($limite <= $f1) AND ($limite > $f2) ) {$semaforo = "<img src='img/ico_pallino_yellow.png'>";}
                    if   ($limite <= $f2) {$semaforo = "<img src='img/ico_pallino_red.png'>";}

                    if ($aggto['fonte'] == 'sds_sinergiareport_soci') 
                        {$sin01 = $aggto['caricamento']; $sin01_semaforo = $semaforo;}

                    if ($aggto['fonte'] == 'SDS_SOCI') 
                        {$sad08 = $aggto['caricamento']; $sad08_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_DATICONTATTO') 
                        {$sad09 = $aggto['caricamento']; $sad09_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_SUBENTRATI') 
                        {$sad10 = $aggto['caricamento']; $sad10_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_TRASFERIMENTI') 
                        {$sad11 = $aggto['caricamento']; $sad11_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_CERTIFICATI') 
                        {$sad12 = $aggto['caricamento']; $sad12_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_MOVINOUT') 
                        {$sad06 = $aggto['caricamento']; $sad06_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_DOMANDE') 
                        {$sad05 = $aggto['caricamento']; $sad05_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_ISIDOC') 
                        {$sad01 = $aggto['caricamento']; $sad01_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_UNDER35') 
                        {$sad02 = $aggto['caricamento']; $sad02_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'SDS_SOCI_DATI_CONSOLIDATI') 
                        {$sad03 = $aggto['caricamento']; $sad03_semaforo = $semaforo;}

                    if ($aggto['fonte'] == 'TAB_DECADUTI_LIQUIDATI') 
                        {$sicra01 = $aggto['caricamento']; $sicra01_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'TAB_DECADUTI_NONLIQUIDATI') 
                        {$sicra02 = $aggto['caricamento']; $sicra02_semaforo = $semaforo;}

                    /* 
                    if ($aggto['fonte'] == 'tab_comuni_soci') 
                        {$comuni = $aggto['caricamento']; $comuni_semaforo = $semaforo;}   
                    if ($aggto['fonte'] == 'tab_giovani') 
                        {$giovani = $aggto['caricamento']; $gio_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_deceduti') 
                        {$deceduti = $aggto['caricamento']; $dec_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_dipendenti') 
                        {$dipendenti = $aggto['caricamento']; $dip_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_volumi') 
                        {$volumi = $aggto['caricamento']; $vol_semaforo = $semaforo;}

                    if ($aggto['fonte'] == 'tab_xls_acquistoulterioriazioni') 
                        {$ulteriori = $aggto['caricamento']; $ult_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_xls_ammissioni')
                        {$ammissioni = $aggto['caricamento']; $amm_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_xls_decessi_eredi') 
                        {$eredi = $aggto['caricamento']; $ere_semaforo = $semaforo;}
                    */

                    if ($aggto['fonte'] == 'tab_xls_cessionibanca') 
                        {$cessioni = $aggto['caricamento']; $ces_semaforo = $semaforo;}
                    if ($aggto['fonte'] == 'tab_xls_esclusioni') 
                        {$esclusioni = $aggto['caricamento']; $esc_semaforo = $semaforo;}

                    if ($aggto['fonte'] == 'tab_mutua') 
                        {$mutua = $aggto['caricamento']; $mta_semaforo = $semaforo;}

                  }
              ?>                     

                <table border="0" width="98%">
                      <tr>
                          <td align="left"><small><?php echo $sad08_semaforo; ?> <b>Sadas SOCI</b></td>
                          <td align="left"><small><?php echo $sad08; ?></td>
                          <td align="left"><small><?php echo $sin01_semaforo; ?> <b>SinergiaReport Soci (Lettere)</b></td>
                          <td align="left"><small><?php echo $sin01; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad12_semaforo; ?> <b>Sadas SOCI CERTIFICATI</b></td>
                          <td align="left"><small><?php echo $sad12; ?></td>
                          <td align="left"><small><?php echo $mta_semaforo; ?> <b>Mutua - Elenco Soci</b></td>
                          <td align="left"><small><?php echo $mutua; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad09_semaforo; ?> <b>Sadas SOCI DATI CONTATTO</b></td>
                          <td align="left"><small><?php echo $sad09; ?></td>
                          <td align="left"><small><?php echo $ces_semaforo; ?> <b>XLS Cessioni a Banca</b></td>
                          <td align="left"><small><?php echo $cessioni; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad10_semaforo; ?> <b>Sadas SOCI SUBENTRATI</b></td>
                          <td align="left"><small><?php echo $sad10; ?></td>
                          <td align="left"><small><?php echo $esc_semaforo; ?> <b>XLS Esclusioni</b></td>
                          <td align="left"><small><?php echo $esclusioni; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad11_semaforo; ?> <b>Sadas SOCI TRASFERIMENTI</b></td>
                          <td align="left"><small><?php echo $sad11; ?></td>
                          <td align="left"><small><?php echo $sicra01_semaforo; ?> <b>Sicra DECADUTI liquidati</b></td>
                          <td align="left"><small><?php echo $sicra01; ?></td>
                      </tr>

                      <tr>
                          <td align="left"><small><?php echo $sad05_semaforo; ?> <b>Sadas SOCI DOMANDE</b></td>
                          <td align="left"><small><?php echo $sad05; ?></td>
                          <td align="left"><small><?php echo $sicra02_semaforo; ?> <b>Sicra DECADUTI non liquidati</b></td>
                          <td align="left"><small><?php echo $sicra02; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad06_semaforo; ?> <b>Sadas SOCI MOVINOUT</b></td>
                          <td align="left"><small><?php echo $sad06; ?></td>
                          <td align="left"><small><?php echo $sad02_semaforo; ?> <b>Sadas SOCI UNDER35</b></td>
                          <td align="left"><small><?php echo $sad02; ?></td>
                      </tr>
                      <tr>
                          <td align="left"><small><?php echo $sad01_semaforo; ?> <b>Sadas SOCI ISIDOC</b></td>
                          <td align="left"><small><?php echo $sad01; ?></td>
                          <td align="left"><small><?php echo $sad03_semaforo; ?> <b>Sadas SOCI DATI CONSOLIDATI</b></td>
                          <td align="left"><small><?php echo $sad03; ?></td>
                      </tr>
                      
                    </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
        </div>
      </div>
    </div>
  </div>

<a style="text-decoration:none;color:gray;" data-toggle="modal" data-target="#exampleModal" >Aggiornamenti</a>
  
  </div>
</footer>

  
<!-- End of Footer -->

<?php
// ----------------------
// Close ODBC
// ----------------------
odbc_close($connect);
?>