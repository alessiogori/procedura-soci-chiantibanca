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
  <div class="alert alert-dismissible alert-success"><h3>Azioni e Fascie di età</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
       
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SINTESI PER FASCE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$tab_dettaglioA =  ' <div class="alert alert-dismissible alert-primary" align="center">
        <strong>Quantità Soci per azioni possedute e fasce di età
       </div>';

$dettaglioA = " 
              SELECT 
                cast(numero_azioni as UNSIGNED) as Azioni,
                cast(numero_azioni as UNSIGNED) * 30.33 as Valore,
                ROUND(SUM(qta),0) AS TOTALE_POSIZIONI,
                ROUND(SUM(
                    CASE
                        WHEN Fascia = 'Fascia 1 (18-30 anni)'
                        THEN qta 
                        ELSE 0
                    END
                ),0) AS qta_Fascia1,
                ROUND(SUM(
                    CASE
                        WHEN Fascia = 'Fascia 2 (31-50 anni)'
                        THEN qta 
                        ELSE 0
                    END
                ),0) AS qta_Fascia2,
                ROUND(SUM(
                    CASE
                        WHEN Fascia = 'Fascia 3 (51-60 anni)'
                        THEN qta 
                        ELSE 0
                    END
                ),0) AS qta_Fascia3,
                ROUND(SUM(
                    CASE
                        WHEN Fascia = 'Fascia 4 (61-70 anni)'
                        THEN qta 
                        ELSE 0
                    END
                ),0) AS qta_Fascia4,
                ROUND(SUM(
                    CASE
                        WHEN Fascia = 'Fascia 5 (oltre 70 anni)'
                        THEN qta 
                        ELSE 0
                    END
                ),0) AS qta_Fascia5
            FROM
                view_fasce_azioni
            WHERE numero_azioni >= 0   
            ".$condizionefiliale."
            GROUP BY
                cast(numero_azioni as UNSIGNED) WITH ROLLUP
                 ";


// INIZIO SCRIPT HEATMAP
?>

<script type="text/javascript" src="../../js/jquery.min.js"></script>

<script type="text/JavaScript">
$(document).ready(function(){
  // Function to get the Max value in Array
        Array.max = function( array ){
            return Math.max.apply( Math, array );
        };

        // get all values
        var counts= $('.heatmap tbody td').not('.first_row').map(function() {
            return parseInt($(this).text().replace(/,/g, "").replace(/\(|\)/g, ""));
        }).get();
  
  // return max value
  var max = Array.max(counts);
  
        // red color for lowest data
        xr = 250;
        xg = 214;
        xb = 211;
        
        // green color for highest data
        yr = 138;
        yg = 251;
        yb = 107;

        n = 100;
  
  // add classes to cells based on nearest 10 value
  $('.heatmap tbody td').not('.first_row').each(function(){
    var val = parseInt($(this).text().replace(/,/g, "").replace(/\(|\)/g, ""));
    var pos = parseInt((Math.round((val/max)*100)).toFixed(0));
    red = parseInt((xr + (( pos * (yr - xr)) / (n-1))).toFixed(0));
    green = parseInt((xg + (( pos * (yg - xg)) / (n-1))).toFixed(0));
    blue = parseInt((xb + (( pos * (yb - xb)) / (n-1))).toFixed(0));
    clr = 'rgb('+red+','+green+','+blue+')';
    $(this).css({backgroundColor:clr});
  });
    });
</script>

<?php
// FINE SCRIPT HEATMAP
                
$result_areeA = $dbhandle->query($dettaglioA) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioA .= '
      <table  border="1" valign="top" width="100%" align="center" class="heatmap">
      <thead>
        <tr class="chartResult table-light" style="color:black;">
          <td align="right" widtd="12%">Azioni</td>
          <td align="right" widtd="12%">Valore</td>
          <td align="right" widtd="12%">Totale Soci</td>
          <td align="right" widtd="12%">18-30 anni (1)</td>
          <td align="right" widtd="12%">31-50 anni (2)</td>          
          <td align="right" widtd="12%">51-60 anni (3)</td>          
          <td align="right" widtd="12%">61-70 anni (4)</td>          
          <td align="right" widtd="12%">+ 70 anni (5)</td>          
        </tr>
      </thead>
        <tbody>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeA = mysqli_fetch_array($result_areeA)) {
      
    if ( $row_areeA['Azioni']  == 0 ) 
        {$valore = "TOTALE";
         $trclass = ' class="chartResult table-light" style="color:black;" ' ;
         $tdclass = "class='first_row'";}
    else 
        {$valore = number_format($row_areeA['Valore'],2,',','.');
         $trclass = " class='stats-row' ";
         $tdclass = "";}
    
    $tab_dettaglioA .=  "<tr  ".$trclass.">
                          <td align='right' width='12%' class='first_row'>&nbsp;".number_format($row_areeA['Azioni'],0,',','.')."&nbsp;</td>
                          <td align='right' width='12%' class='first_row'>&nbsp;&euro; ".$valore."&nbsp;</td>
                          <td align='right' width='12%' class='first_row'>&nbsp;".number_format($row_areeA['TOTALE_POSIZIONI'],0,',','.')."&nbsp;</td>
                          <td align='right' width='12%' ".$tdclass.">".number_format($row_areeA['qta_Fascia1'],0,',','.')."</td>
                          <td align='right' width='12%' ".$tdclass.">".number_format($row_areeA['qta_Fascia2'],0,',','.')."</td>
                          <td align='right' width='12%' ".$tdclass.">".number_format($row_areeA['qta_Fascia3'],0,',','.')."</td>
                          <td align='right' width='12%' ".$tdclass.">".number_format($row_areeA['qta_Fascia4'],0,',','.')."</td>
                          <td align='right' width='12%' ".$tdclass.">".number_format($row_areeA['qta_Fascia5'],0,',','.')."</td>
                         </tr>";
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioA .=  '</tbody></table>';
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

