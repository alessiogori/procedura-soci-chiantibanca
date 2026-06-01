<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
//
// Questo script serve a controllare se fossero presenti nel db CESSIONI
// posizioni che in realtà sono già estinte
//
// SEZIONE DA NON MODIFICARE
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("../../config/_config.php");
include("../../config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("../../css/main.php");
include("../../css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>
<!-- 
   <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.min.css"> 
   <link rel="stylesheet" type="text/css" href="css/style.css"> -->
    <link rel="stylesheet" type="text/css" href="css/buttons.dataTables.min.css">
<?php
   
// ********************************************************
// PAC
// ********************************************************

$strQueryP = "  SELECT prot,
                CDA, Filiale, a.CAG, Conto, Manca_DB, Nominativo, PrimoAddebito, Azioni_Sottoscritte, Flag_da_SUCC_CESS, 
                Qta_Azioni_da_Succ_Cess, Qta_Ulteriori_azioni_da_acquistare, Pac, 
                Rata01, Rata02, Rata03, Rata04, Rata05, Rata06, Rata07, Rata08, Rata09, Rata10, Rata11, Rata12,
                (Rata01 + Rata02 + Rata03 + Rata04 + Rata05 + Rata06 + Rata07 + Rata08 + Rata09 + Rata10 + Rata11 + Rata12) as Totale
                FROM tab_xls_ammissioni as a, tab_soci_as37 as s
                WHERE Pac = 'S'
                AND a.CAG = s.cag
                ORDER BY Filiale, Nominativo   ";

$resultP = mysqli_query($connection, $strQueryP);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Controllo situazione PIANO DI ACCUMULO per GIOVANI SOCI</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered"  id="maintable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th rowspan="2" style="text-align:left; font-size:13px;">Filiale</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">CAG</th>
        <th rowspan="2" style="text-align:left; font-size:13px;">Nominativo</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Primo<br>Addebito</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Prossimo<br>Addebito</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Numero<br>Azioni</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Origine</th>
        <th colspan="12" style="text-align:center; font-size:13px;">RATE</th>
        <th rowspan="2" style="text-align:center; font-size:13px;">Totale</th>
    </tr>
    <tr class="table-primary">
        <th style="text-align:center; font-size:13px;">01</th>
        <th style="text-align:center; font-size:13px;">02</th>
        <th style="text-align:center; font-size:13px;">03</th>
        <th style="text-align:center; font-size:13px;">04</th>
        <th style="text-align:center; font-size:13px;">05</th>
        <th style="text-align:center; font-size:13px;">06</th>
        <th style="text-align:center; font-size:13px;">07</th>
        <th style="text-align:center; font-size:13px;">08</th>
        <th style="text-align:center; font-size:13px;">09</th>
        <th style="text-align:center; font-size:13px;">10</th>
        <th style="text-align:center; font-size:13px;">11</th>
        <th style="text-align:center; font-size:13px;">12</th>
    </tr>
    </thead>
    <tbody>
'; 

  // iterating over each data and pushing it into $arrData array
  while ($dati_p = mysqli_fetch_array($resultP)) {

            if    ($dati_p['Rata01'] <> 0)  {$tdstyle_p01 = 'style="background-color:green;" '; $conta01 = 1;}                
                                        else{$tdstyle_p01 = ''; $conta01 = 0; }
            if    ($dati_p['Rata02'] <> 0)  {$tdstyle_p02 = 'style="background-color:green;" '; $conta02 = 1;}
                                        else{$tdstyle_p02 = ''; $conta02 = 0; }
            if    ($dati_p['Rata03'] <> 0)  {$tdstyle_p03 = 'style="background-color:green;" '; $conta03 = 1;}
                                        else{$tdstyle_p03 = ''; $conta03 = 0; }
            if    ($dati_p['Rata04'] <> 0)  {$tdstyle_p04 = 'style="background-color:green;" '; $conta04 = 1;}
                                        else{$tdstyle_p04 = ''; $conta04 = 0; }
            if    ($dati_p['Rata05'] <> 0)  {$tdstyle_p05 = 'style="background-color:green;" '; $conta05 = 1;}
                                        else{$tdstyle_p05 = ''; $conta05 = 0; }
            if    ($dati_p['Rata06'] <> 0)  {$tdstyle_p06 = 'style="background-color:green;" '; $conta06 = 1;}
                                        else{$tdstyle_p06 = ''; $conta06 = 0; }
            if    ($dati_p['Rata07'] <> 0)  {$tdstyle_p07 = 'style="background-color:green;" '; $conta07 = 1;}
                                        else{$tdstyle_p07 = ''; $conta07 = 0; }
            if    ($dati_p['Rata08'] <> 0)  {$tdstyle_p08 = 'style="background-color:green;" '; $conta08 = 1;}
                                        else{$tdstyle_p08 = ''; $conta08 = 0; }
            if    ($dati_p['Rata09'] <> 0)  {$tdstyle_p09 = 'style="background-color:green;" '; $conta09 = 1;}
                                        else{$tdstyle_p09 = ''; $conta09 = 0; }
            if    ($dati_p['Rata10'] <> 0)  {$tdstyle_p10 = 'style="background-color:green;" '; $conta10 = 1;}
                                        else{$tdstyle_p10 = ''; $conta10 = 0; }
            if    ($dati_p['Rata11'] <> 0)  {$tdstyle_p11 = 'style="background-color:green;" '; $conta11 = 1;}
                                        else{$tdstyle_p11 = ''; $conta11 = 0; }
            if    ($dati_p['Rata12'] <> 0)  {$tdstyle_p12 = 'style="background-color:green;" '; $conta12 = 1;}
                                        else{$tdstyle_p12 = ''; $conta12 = 0; }
            

            // CONTEGGIO DEL PROSSIMO ADDEBITO                            
            $conteggio = $conta01 + $conta02 + $conta03 + $conta04 + $conta05 + $conta06 + $conta07 + $conta07 + $conta09 
                       + $conta09 + $conta10 + $conta11 + $conta12 ;     

			if ($dati_p['PrimoAddebito'] <> '') {                       
            $primoaddebito = $dati_p['PrimoAddebito'];
	            list($giorno, $mese, $anno) = explode("/", $primoaddebito);
	            // mesi da sommare alla data
	            $NM = $conteggio;
	            // giorni da sommmare alla data
	            $NG = 0;
	            // anni da sommare alla data
	            $NA = 0;
	            // stampo la nuova data risultato della data impostata e dei valori aggiuntivi
	            $prossimoaddebito = date("d/m/Y",mktime(0,0,0,$mese+$NM,30,$anno));    
			}
			else
			{
				$prossimoaddebito = '';
			}

            switch ($dati_p['Flag_da_SUCC_CESS']) {
              case 'C':
                $origine = "Cessione";
                break;
              case 'S':
                $origine = "Successione";
                break;
              case 'D':
                $origine = "Donazione";
                break;
              default:
                $origine = "";
}
    		echo "	<tr class='table-secondary'>
    		            <td align='left'>".$dati_p['Filiale']."</td>
                        <td align='left'>".$dati_p['CAG']."&nbsp;</td>
                        <td><a class='text-success' href='sqldati_schedasocio.php?id=".$dati_p['prot']."'>".$dati_p['Nominativo']."</a></td>
                        <td align='center'>".$dati_p['PrimoAddebito']."&nbsp;</td>
                        <td align='center'>".$prossimoaddebito."&nbsp;</td>
                        <td align='center'>".$dati_p['Azioni_Sottoscritte']."&nbsp;</td>
                        <td align='center' title=".$origine.">".$dati_p['Flag_da_SUCC_CESS']."&nbsp;</td>
						<td align='center' ".$tdstyle_p01.">".$dati_p['Rata01']."</td>
						<td align='center' ".$tdstyle_p02.">".$dati_p['Rata02']."</td>
						<td align='center' ".$tdstyle_p03.">".$dati_p['Rata03']."</td>
						<td align='center' ".$tdstyle_p04.">".$dati_p['Rata04']."</td>
						<td align='center' ".$tdstyle_p05.">".$dati_p['Rata05']."</td>
						<td align='center' ".$tdstyle_p06.">".$dati_p['Rata06']."</td>
						<td align='center' ".$tdstyle_p07.">".$dati_p['Rata07']."</td>
						<td align='center' ".$tdstyle_p08.">".$dati_p['Rata08']."</td>
						<td align='center' ".$tdstyle_p09.">".$dati_p['Rata09']."</td>
						<td align='center' ".$tdstyle_p10.">".$dati_p['Rata10']."</td>
						<td align='center' ".$tdstyle_p11.">".$dati_p['Rata11']."</td>
						<td align='center' ".$tdstyle_p12.">".$dati_p['Rata12']."</td>
						<td align='center' style='background-color:brown;'>".$dati_p['Totale']."</td>
					</tr>";

  }

echo '  
    <tfoot style="background-color: #c0c0c0; color: #ffffff; font-size: 0.9em; ">
    <tr>
        <th>Name</th>
        <th>Position</th>
        <th>City</th>
        <th>Age</th>
        <th>Start Date</th>
        <th>Salary</th>
    </tr>
    </tfoot>
    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


?>

<!-- Page level plugins -->
	<script type="text/javascript" src="js/jquery-2.2.4.min.js"></script> 
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="js/jszip.min.js"></script>
	<script type="text/javascript" src="js/pdfmake.min.js"></script>
	<script type="text/javascript" src="js/vfs_fonts.js"></script>
	<script type="text/javascript" src="js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="js/buttons.print.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	<script type="text/javascript" src="js/jquery.mark.min.js"></script>
	<script type="text/javascript" src="js/datatables.mark.js"></script>
	<script type="text/javascript" src="js/buttons.colVis.min.js"></script>
