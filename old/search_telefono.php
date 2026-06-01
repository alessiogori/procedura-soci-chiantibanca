<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);

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

if (!isset($_GET['telefono'])) {echo '<center>NON HAI INSERITO NESSUN TELEFONO</center>';}
	else
{

$select = "	SELECT *
			FROM tab_soci_as37
			WHERE telefono like '%".$_GET[telefono]."%'
			ORDER BY cag, int1Socio, int2Socio
			";

//logquery ($select);  

$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Ricerca Socio per numero di telefono</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:left; font-size:13px;">CAG</th>
				<th style="text-align:left; font-size:13px;">Nominativo</th>
				<th style="text-align:left; font-size:13px;">Filiale</th>
				<th style="text-align:left; font-size:13px;">Telefono</th>
			</tr>
		</thead>
		<tbody>
';	

while($dati=mysqli_fetch_array($querydati)){ 

echo "	<tr class='table-secondary'>
			<td align='center'>".$dati['cag']."</td>
			<td align='left'><a class='text-success' href='sqldati_schedasocio.php?id=".$dati['prot']."'>".$dati['int1Socio']." ".$dati['int2Socio']."</a></td>
			<td align='center'>".$dati['codFil']."</td>
			<td align='left'>".$dati['telefono']."</td>
		</tr>";

}

echo '		</tbody>
	</table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


}

	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';

?>