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
?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>
  <script src="js/jquery.min.js"></script>

<style type="text/css">
  table {
    overflow: hidden;
  }

  td, th {
    padding: 10px;
    position: relative;
    outline: 0;
  }

  body:not(.nohover) tbody tr:hover {
    background-color: #52AA7E;
  }

  td:hover::after,
  thead th:not(:empty):hover::after,
  td:focus::after,
  thead th:not(:empty):focus::after { 
    content: '';  
    height: 10000px;
    left: 0;
    position: absolute;  
    top: -5000px;
    width: 100%;
    z-index: -1;
  }

  td:hover::after,
  th:hover::after {
    background-color: #52AA7E;
  }

  td:focus::after,
  th:focus::after {
    background-color: lightblue;
  }

  /* Focus stuff for mobile */
  td:focus::before,
  tbody th:focus::before {
    background-color: lightblue;
    content: '';  
    height: 100%;
    top: 0;
    left: -5000px;
    position: absolute;  
    width: 10000px;
    z-index: -1;
  }
</style>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin  // 
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
          	"order": [[ 1, "asc" ]],
          	"lengthMenu": [ 50 ],
          	"deferRender": true
    } );

		});


$('td').hover(function() {
 $(this).parents('table').find('col:eq('+$(this).index()+')').toggleClass('hover');
});

  </script>   
<?php

$select = "	SELECT * 
            FROM tab_filiali_matricekm 
            WHERE chiusa <> 'S'
			ORDER BY filiale
			";

logquery ($select);  
$querydati = mysqli_query($connection, $select);

/*
echo '
<table align="center" width="95%" border=0>
		<tr>
			<!-- GRIGLIA KM -->
			<td width="70%" valign="top" >';
*/
/*
echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Matrice distanze tra Filiali<i class="fas fa-car fa-1x text-gray-300 col-auto"></i><small>(fonte Google Maps)</small></h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">';

<div class="container h-100 d-flex align-items-center justify-content-center" >

*/
echo '<div class="container-fluid">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Matrice distanze tra Filiali<i class="fas fa-car fa-1x text-gray-300 col-auto"></i><small>(fonte Google Maps)</small></h4>
    </div>
        <div class="row" >
          <div class="col-sm-12">';
      
      //table-sm table-hover
echo '      
      <main>
        <table class="table table-bordered table-striped text-center" id="dataTable" cellspacing="0" style="margin: 0 auto;   width: 80%;">
		<thead>
			<tr >
				<th class="col" style="text-align:left; font-size:11px;">Area</th>
				<th class="col" style="text-align:left; font-size:11px;">Fil</th>
				<th class="col" style="text-align:left; font-size:11px;">Nome Filiale</th>
                <th class="col" style="text-align:right; font-size:13px;">0 </th>
                <th class="col" style="text-align:right; font-size:13px;">1 </th>
                <th class="col" style="text-align:right; font-size:13px;">2 </th>
                <th class="col" style="text-align:right; font-size:13px;">3 </th>
                <th class="col" style="text-align:right; font-size:13px;">4 </th>
                <th class="col" style="text-align:right; font-size:13px;">5 </th>
                <th class="col" style="text-align:right; font-size:13px;">20</th>
                <th class="col" style="text-align:right; font-size:13px;">21</th>
                <th class="col" style="text-align:right; font-size:13px;">22</th>
                <th class="col" style="text-align:right; font-size:13px;">23</th>
                <th class="col" style="text-align:right; font-size:13px;">24</th>
                <th class="col" style="text-align:right; font-size:13px;">25</th>
                <th class="col" style="text-align:right; font-size:13px;">26</th>
                <th class="col" style="text-align:right; font-size:13px;">30</th>
                <th class="col" style="text-align:right; font-size:13px;">32</th>
                <th class="col" style="text-align:right; font-size:13px;">33</th>
                <th class="col" style="text-align:right; font-size:13px;">35</th>
                <th class="col" style="text-align:right; font-size:13px;">36</th>
                <th class="col" style="text-align:right; font-size:13px;">40</th>
                <th class="col" style="text-align:right; font-size:13px;">41</th>
                <th class="col" style="text-align:right; font-size:13px;">43</th>
                <th class="col" style="text-align:right; font-size:13px;">44</th>
                <th class="col" style="text-align:right; font-size:13px;">48</th>
                <th class="col" style="text-align:right; font-size:13px;">50</th>
                <th class="col" style="text-align:right; font-size:13px;">51</th>
                <th class="col" style="text-align:right; font-size:13px;">53</th>
                <th class="col" style="text-align:right; font-size:13px;">54</th>
                <th class="col" style="text-align:right; font-size:13px;">55</th>
                <th class="col" style="text-align:right; font-size:13px;">56</th>
                <th class="col" style="text-align:right; font-size:13px;">60</th>
                <th class="col" style="text-align:right; font-size:13px;">61</th>
                <th class="col" style="text-align:right; font-size:13px;">62</th>
                <th class="col" style="text-align:right; font-size:13px;">63</th>
                <th class="col" style="text-align:right; font-size:13px;">64</th>
                <th class="col" style="text-align:right; font-size:13px;">66</th>
                <th class="col" style="text-align:right; font-size:13px;">67</th>
                <th class="col" style="text-align:right; font-size:13px;">70</th>
                <th class="col" style="text-align:right; font-size:13px;">71</th>
                <th class="col" style="text-align:right; font-size:13px;">73</th>
			</tr>
		</thead>
		<tbody>
';	

while($dati=mysqli_fetch_array($querydati)){ 
    


echo "	<tr >
			<td style='text-align:left; font-size:11px;'>".$dati['area']."</td>
			<td style='text-align:left; font-size:11px;'>".$dati['filiale']."</td>
			<td style='text-align:left; font-size:11px;'>".$dati['nome_filiale']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_0']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_1']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_2']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_3']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_4']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_5']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_20']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_21']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_22']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_23']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_24']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_25']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_26']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_30']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_32']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_33']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_35']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_36']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_40']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_41']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_43']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_44']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_48']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_50']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_51']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_53']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_54']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_55']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_56']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_60']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_61']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_62']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_63']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_64']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_66']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_67']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_70']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_71']."</td>
            <td align='right' title='".$dati['nome_filiale']."'>".$dati['F_73']."</td>
		</tr>";

}

echo '      
    <thead>
      <tr >
        <th class="col" style="text-align:left; font-size:11px;">Area</th>
        <th class="col" style="text-align:left; font-size:11px;">Fil</th>
        <th class="col" style="text-align:left; font-size:11px;">Nome Filiale</th>
                <th class="col" style="text-align:right; font-size:13px;">0 </th>
                <th class="col" style="text-align:right; font-size:13px;">1 </th>
                <th class="col" style="text-align:right; font-size:13px;">2 </th>
                <th class="col" style="text-align:right; font-size:13px;">3 </th>
                <th class="col" style="text-align:right; font-size:13px;">4 </th>
                <th class="col" style="text-align:right; font-size:13px;">5 </th>
                <th class="col" style="text-align:right; font-size:13px;">20</th>
                <th class="col" style="text-align:right; font-size:13px;">21</th>
                <th class="col" style="text-align:right; font-size:13px;">22</th>
                <th class="col" style="text-align:right; font-size:13px;">23</th>
                <th class="col" style="text-align:right; font-size:13px;">24</th>
                <th class="col" style="text-align:right; font-size:13px;">25</th>
                <th class="col" style="text-align:right; font-size:13px;">26</th>
                <th class="col" style="text-align:right; font-size:13px;">30</th>
                <th class="col" style="text-align:right; font-size:13px;">32</th>
                <th class="col" style="text-align:right; font-size:13px;">33</th>
                <th class="col" style="text-align:right; font-size:13px;">35</th>
                <th class="col" style="text-align:right; font-size:13px;">36</th>
                <th class="col" style="text-align:right; font-size:13px;">40</th>
                <th class="col" style="text-align:right; font-size:13px;">41</th>
                <th class="col" style="text-align:right; font-size:13px;">43</th>
                <th class="col" style="text-align:right; font-size:13px;">44</th>
                <th class="col" style="text-align:right; font-size:13px;">48</th>
                <th class="col" style="text-align:right; font-size:13px;">50</th>
                <th class="col" style="text-align:right; font-size:13px;">51</th>
                <th class="col" style="text-align:right; font-size:13px;">53</th>
                <th class="col" style="text-align:right; font-size:13px;">54</th>
                <th class="col" style="text-align:right; font-size:13px;">55</th>
                <th class="col" style="text-align:right; font-size:13px;">56</th>
                <th class="col" style="text-align:right; font-size:13px;">60</th>
                <th class="col" style="text-align:right; font-size:13px;">61</th>
                <th class="col" style="text-align:right; font-size:13px;">62</th>
                <th class="col" style="text-align:right; font-size:13px;">63</th>
                <th class="col" style="text-align:right; font-size:13px;">64</th>
                <th class="col" style="text-align:right; font-size:13px;">66</th>
                <th class="col" style="text-align:right; font-size:13px;">67</th>
                <th class="col" style="text-align:right; font-size:13px;">70</th>
                <th class="col" style="text-align:right; font-size:13px;">71</th>
                <th class="col" style="text-align:right; font-size:13px;">73</th>
      </tr>
    </thead>
';  

echo '		</tbody>
	</table></main>
    
      </div>
    </div>
  </div>
';

/*
echo '    
</div>
<!-- /.container-fluid -->';
*/
/*
// sezione LEGENDA FILIALI
echo '
			</td>

			<!-- LEGENDA FILIALI -->
			<td width="30%" valign="top">

                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-light">Elenco Filiali</h6>
                </div>
                <div class="card-body float-sm-left">';

$selectF = "	SELECT filiale, nome_filiale, indirizzo
                FROM tab_filiali_matricekm 
    			ORDER BY filiale
    			";

logquery ($selectF);  
$querydatiF = mysqli_query($connection, $selectF);

echo '<small>';
while($datiF=mysqli_fetch_array($querydatiF)){ 
    echo $datiF['filiale']." ".$datiF['nome_filiale']." <span style='color:#676767;'>(".$datiF['indirizzo'].")</span><br>";
}
echo "</small>";
*/
// chiudo tabella generale
echo '                
 </table>

</div> ';              

	echo '<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';


?>