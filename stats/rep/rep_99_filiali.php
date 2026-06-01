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
  <div class="alert alert-dismissible alert-warning"><h3>Elenco Filiali</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
       
$dettaglioFil = " 
        select area, filiale, desc_filiale
				from tab_psw
				where area <> '_Chiusa_'
				and filiale not in ('090','099')
        ".$condizionefiliale."
				order by 1,2
			 "; 

$result_areeFil = $dbhandle->query($dettaglioFil) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

$tab_dettaglioFil .= '
            <table border="0" valign="top" width="100%" align="center">
              <thead>
                <tr class="table-light" style="color:black;">
                  <th style="text-align:left;">Area</th>
                  <th style="text-align:left;">Filiale</th>
                  <th style="text-align:left;">Nome Filiale</th>
                </tr>
              </thead>';

  // iterating over each data and pushing it into $arrData array
  while ($row_areeFil = mysqli_fetch_array($result_areeFil)) {

    $tab_dettaglioFil .=  "
      <tr >   
        <td style='text-align:left'>".$row_areeFil['area']."</td>        
        <td style='text-align:left'>".$row_areeFil['filiale']."</td>
        <td style='text-align:left'>".$row_areeFil['desc_filiale']."</td>
      </tr>
          "; 
  // chiudo ciclo WHILE  
  }

// Chiudo la tabella
$tab_dettaglioFil .=  '</table>';
?>


<table border="0" align="center" width="40%">
  <tr>     
       <td valign="top" width="70%"><?php echo $tab_dettaglioFil; ?></td>
  </tr>
</table>
<br>

<?php
    
// closing database connection      
$dbhandle->close();       
?>

