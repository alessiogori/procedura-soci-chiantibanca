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
  <div class="alert alert-dismissible alert-success"><h3>Indici Generali Uscite</h3>
  '.$titolofiliale.'</div>
</div>
';

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
?>
       

<?php
// -------------------------------------------------------------------------------
//  DATI CORRENTI - Dati Contabili
// -------------------------------------------------------------------------------
$select_coge = " SELECT * FROM tab_valorefondo " ;
$qry_coge = $dbhandle->query($select_coge) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($coge = mysqli_fetch_array($qry_coge)){ 
  $capitale_sociale = $coge['capitale'];
  $fondo            = $coge['fondo'];
  $plafond          = $coge['plafond'];
  $sovrapprezzo     = $coge['sovrapprezzo'];
}    

// -------------------------------------------------------------------------------
//  DATI CORRENTI - Numero Totale Soci
// -------------------------------------------------------------------------------
$select_tot = " SELECT count(*) as qta FROM sds_soci
                WHERE DATA_ENTRATA <= NOW()
                AND (DATA_USCITA =  '0' OR DATA_USCITA > NOW())" ;
$qry_tot = $dbhandle->query($select_tot) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($tot = mysqli_fetch_array($qry_tot)){ 
  $qta_soci = $tot['qta'];
}   

// -------------------------------------------------------------------------------
// CESSIONI A BANCA
// -------------------------------------------------------------------------------
$select_cessioni = " SELECT count(*) as qta, sum(Valore_Nominale) as Valore_Nominale 
                     FROM tab_xls_cessionibanca
                     WHERE Rimborsato != 'S'
                    ".$condizionefiliale ; 
$qry_cessioni = $dbhandle->query($select_cessioni) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_cessioni = mysqli_fetch_array($qry_cessioni)){ 
  $CB_qta_totale = $dati_cessioni['qta'];
  $CB_val_totale = $dati_cessioni['Valore_Nominale'];
}  

$CB1 = number_format(($CB_qta_totale / $qta_soci)*100,2,',','.');
$CB2 = number_format(($CB_val_totale / $capitale_sociale)*100,2,',','.');

echo '
  <table border="0" valign="top" width="80%" align="center">
    <tr class="table-primary">
      <td align="center" class="alert alert-dismissible alert-primary" colspan="11">
        <strong>CESSIONI A BANCA (in essere)
       </td>
    </tr>
    <tr>
      <td align="center" width="15%">
        Nr.Cessioni a Banca <hr> Nr.Soci Banca
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($CB_qta_totale,0,',','.').' <hr> '.number_format($qta_soci,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$CB1.' %</td>

      <td align="center" width="4%"><h1 style="color:lightgray;">|</h1></td>

      <td align="center" width="15%">
        &euro; Cessioni a Banca <hr> &euro; Capitale Sociale
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($CB_val_totale,0,',','.').' <hr> '.number_format($capitale_sociale,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$CB2.' %</td>
    </tr>
</table>
';

echo '<br><br>';
// -------------------------------------------------------------------------------
// ESCLUSIONI
// -------------------------------------------------------------------------------
$select_esclusioni = " SELECT count(*) as qta, sum(Valore_Nominale) as Valore_Nominale 
                     FROM tab_xls_esclusioni
                     WHERE Valore_Nominale <> -1
                     AND Escluso_x_Passaggio_a_Sofferenze <> 'S'
                     and MovimentoSIcra not in ('RE','ES')
                    ".$condizionefiliale ; 
$qry_esclusioni = $dbhandle->query($select_esclusioni) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_esclusioni = mysqli_fetch_array($qry_esclusioni)){ 
  $ES_qta_totale = $dati_esclusioni['qta'];
  $ES_val_totale = $dati_esclusioni['Valore_Nominale'];
}  

$ES1 = number_format(($ES_qta_totale / $qta_soci)*100,2,',','.');
$ES2 = number_format(($ES_val_totale / $capitale_sociale)*100,2,',','.');

echo '
  <table border="0" valign="top" width="80%" align="center">
    <tr class="table-primary">
      <td align="center" class="alert alert-dismissible alert-primary" colspan="11">
        <strong>ESCLUSIONI (in essere)
       </td>
    </tr>
    <tr>
      <td align="center" width="15%">
        Nr.Esclusioni <hr> Nr.Soci Banca
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($ES_qta_totale,0,',','.').' <hr> '.number_format($qta_soci,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$ES1.' %</td>

      <td align="center" width="4%"><h1 style="color:lightgray;">|</h1></td>

      <td align="center" width="15%">
        &euro; Esclusioni <hr> &euro; Capitale Sociale
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($ES_val_totale,0,',','.').' <hr> '.number_format($capitale_sociale,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$ES2.' %</td>
    </tr>
</table>
';
  
echo '<br><br>';
// -------------------------------------------------------------------------------
// LIQUIDAZIONI A EREDI
// -------------------------------------------------------------------------------
$select_eredi = "SELECT count(*) as qta,  sum(ValoreTotaleAzioni) as Valore_Nominale 
                  FROM view_decessi
                  WHERE ctipomov_ID = 'ID'
                  AND ctipomov_RS != 'RS'
                  AND ctipomov_RL != 'RL'
                  ".$condizionefiliale2."
                ";   
$qry_eredi = $dbhandle->query($select_eredi) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_eredi = mysqli_fetch_array($qry_eredi)){ 
  $ER_qta_totale = $dati_eredi['qta'];
  $ER_val_totale = $dati_eredi['Valore_Nominale'];
}  

$ER1 = number_format(($ER_qta_totale / $qta_soci)*100,2,',','.');
$ER2 = number_format(($ER_val_totale / $capitale_sociale)*100,2,',','.');

echo '
  <table border="0" valign="top" width="80%" align="center">
    <tr class="table-primary">
      <td align="center" class="alert alert-dismissible alert-primary" colspan="11">
        <strong>LIQUIDAZIONI A EREDI (in essere)
       </td>
    </tr>
    <tr>
      <td align="center" width="15%">
        Nr.Liq.Eredi <hr> Nr.Soci Banca
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($ER_qta_totale,0,',','.').' <hr> '.number_format($qta_soci,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$ER1.' %</td>

      <td align="center" width="4%"><h1 style="color:lightgray;">|</h1></td>

      <td align="center" width="15%">
        &euro; Liq.Eredi <hr> &euro; Capitale Sociale
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($ER_val_totale,0,',','.').' <hr> '.number_format($capitale_sociale,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$ER2.' %</td>
    </tr>
</table>
'; 

echo '<br><br>';
// -------------------------------------------------------------------------------
// SOFFERENZE
// -------------------------------------------------------------------------------
$select_sofferenze = " SELECT count(*) as qta, sum(Valore_Nominale) as Valore_Nominale 
                     FROM tab_xls_esclusioni
                     WHERE Valore_Nominale <> -1
                     AND Escluso_x_Passaggio_a_Sofferenze = 'S'
                     and MovimentoSIcra not in ('RE','ES')
                    ".$condizionefiliale ; 
$qry_sofferenze = $dbhandle->query($select_sofferenze) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
while($dati_sofferenze = mysqli_fetch_array($qry_sofferenze)){ 
  $SO_qta_totale = $dati_sofferenze['qta'];
  $SO_val_totale = $dati_sofferenze['Valore_Nominale'];
}  

$SO1 = number_format(($SO_qta_totale / $qta_soci)*100,2,',','.');
$SO2 = number_format(($SO_val_totale / $capitale_sociale)*100,2,',','.');

echo '
  <table border="0" valign="top" width="80%" align="center">
    <tr class="table-primary">
      <td align="center" class="alert alert-dismissible alert-primary" colspan="11">
        <strong>SOFFERENZE (in essere)
       </td>
    </tr>
    <tr>
      <td align="center" width="15%">
        Nr.Sofferenze <hr> Nr.Soci Banca
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($SO_qta_totale,0,',','.').' <hr> '.number_format($qta_soci,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$SO1.' %</td>

      <td align="center" width="4%"><h1 style="color:lightgray;">|</h1></td>

      <td align="center" width="15%">
        &euro; Sofferenze <hr> &euro; Capitale Sociale
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($SO_val_totale,0,',','.').' <hr> '.number_format($capitale_sociale,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$SO2.' %</td>
    </tr>
</table>
';

echo '<br><br>';
// -------------------------------------------------------------------------------
// TOTALE GENERALE
// -------------------------------------------------------------------------------

$T_qta_totale = $CB_qta_totale + $ES_qta_totale + $ER_qta_totale + $SO_qta_totale  ;
$T_val_totale = $CB_val_totale + $ES_val_totale + $ER_val_totale + $SO_val_totale ;

$T1 = number_format(($T_qta_totale / $qta_soci)*100,2,',','.');
$T2 = number_format(($T_val_totale / $capitale_sociale)*100,2,',','.');

echo '
  <table border="0" valign="top" width="80%" align="center">
    <tr class="table-primary">
      <td align="center" class="alert alert-dismissible alert-primary" colspan="11">
        <strong>TOTALE GENERALE SU USCITE (in essere)
       </td>
    </tr>
    <tr>
      <td align="center" width="15%">
        Nr.Uscite <hr> Nr.Soci Banca
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($T_qta_totale,0,',','.').' <hr> '.number_format($qta_soci,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$T1.' %</td>

      <td align="center" width="4%"><h1 style="color:lightgray;">|</h1></td>

      <td align="center" width="15%">
        &euro; Uscite <hr> &euro; Capitale Sociale
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="15%">
        '.number_format($T_val_totale,0,',','.').' <hr> '.number_format($capitale_sociale,0,',','.').'
      </td>
      <td align="center" width="3%">=</td>
      <td align="center" width="10%"><b>'.$T2.' %</td>
    </tr>
</table>
';


// closing database connection      
$dbhandle->close();       
?>

