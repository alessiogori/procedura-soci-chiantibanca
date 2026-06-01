<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

echo '
<center>
<div class="col-lg-12">
  <div class="alert alert-dismissible alert-success"><h3>Soci Storici</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
       

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// DETTAGLIO SOCI STORICI
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary" align="center">
        <strong>Soci storici (primi 40 per anzianità socio in ordine di azioni possedute)
       </div>';

$dettaglioA = " 
              SELECT  *
              FROM view_fasce_anzianitasocio
              WHERE nag <> 0
              ".$condizionefiliale."
              GROUP BY nag
              ORDER BY STR_TO_DATE(DataEntrata ,'%d/%m/%Y') ASC, cast(NumeroAzioni as unsigned) desc, Nominativo
              LIMIT 40
                 "; 

$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '
            <table border="0" valign="top" width="100%" align="center">
              <thead>
                <tr class="table-light" style="color:black;">
                  <th style="text-align:center;">Filiale</th>
                  <th style="text-align:center;">NAG</th>
                  <th style="text-align:left;">Nominativo</th>
                  <th style="text-align:center;"><small>Anni<br>Anz.Socio</small></th>
                  <th style="text-align:center;">Età</th>        
                  <th style="text-align:center;"><small>Data<br>Entrata</small></th>
                  <th style="text-align:right;">Azioni</th>
                  <th style="text-align:right;">Valore</th>
                  <th style="text-align:center;">Socio Mutua</th>        
                </tr>
              </thead>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {

      // ********************************************************
      // RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
      // ********************************************************
      $select_mutua     = " SELECT * FROM tab_mutua
                  WHERE nag = ".$row_areeA['NAG'];
                
      logquery ($select_mutua);  
      $querydati_mutua = mysqli_query($connection, $select_mutua);
        if(mysqli_num_rows($querydati_mutua) > 0)
            while($datisociomutua = mysqli_fetch_array($querydati_mutua))
            {
               $esistenzasociomutua = 
            "<img src='../../img/ico_pallino_green.png' >";
             $modulimutua = 'si';
            }
        else
        {
            $esistenzasociomutua = '';
            $modulimutua = 'no';
        }
    
    $tab_dettaglioA .=  "
      <tr >   
        <td style='text-align:center;'><small>".$row_areeA['Filiale']."&nbsp;</small></td>        
        <td style='text-align:center;'><small>".$row_areeA['NAG']."</small></td>
        <td style='text-align:left;'><small>".$row_areeA['Nominativo']."</small></td>
        <td style='text-align:center;'><small>".$row_areeA['AnniAnzianitaSocio']."&nbsp;</small></td>
        <td style='text-align:center;'><small>".$row_areeA['Eta']."&nbsp;</small></td>
        <td style='text-align:center;'><small>".$row_areeA['DataEntrata']."&nbsp;</small></td>
        <td style='text-align:right;'><small>".number_format($row_areeA['NumeroAzioni'],0,',','.')."&nbsp;</small></td>
        <td style='text-align:right;'><small>&euro; ".number_format(($row_areeA['Importo']),0,',','.')."&nbsp;</small></td>
        <td style='text-align:center;'><small>".$esistenzasociomutua."</small></td>          
      </tr>
          "; 
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</table>';
?>


<table border="0" align="center" width="90%">
  <tr>     
       <td valign="top" width="50%"><?php echo $tab_dettaglioA; ?></td>
  </tr>
</table>
<br>

<?php
    
// closing database connection      
$dbhandle->close();       
?>

