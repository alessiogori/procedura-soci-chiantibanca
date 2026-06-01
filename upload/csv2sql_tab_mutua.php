<?php
// *****************************************************************************
// Portale ChiantiBanca Soci
// Sviluppo e realizzazione: Alessio Fedi (2021)
// *****************************************************************************
// 
/* --------------------------------------------------------------------- */
// VARIABILI IN USO NEL FILE
$titolo  = "Caricamento SOCI - CSV Mutua";
$tabella = "tab_mutua";
$nomefile= "mutua.csv";
$istruzioni = '	1. Recupera il file csv ricevuto settimanalmente da WTech via mail<br>
				2. Caricalo su questa pagina (sfoglia...)<br>
                <br>
				';

$mysql = "localhost";
$username = "3qa25raa3f";
$password = "8ynDHEuDkMhM63dy";
$db = "soci";

function Pulisci($dato) {
    
    $dato_pulito = str_replace('"','',$dato);
    
    return $dato_pulito;
    
}

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Connessione a MYSQL
$connection = mysqli_connect($mysql, $username, $password, $db);

set_time_limit(0);

/* ------------------------------------------------------------------------ */
?>

<html>
<head>
<title><?php echo $titolo; ?></title>
<style type="text/css">
	@import "../css/bootstrap.min.css";
</style> 
</head>
<body>
<br>
<h1 align="center"><?php echo $titolo; ?></h1>

</br>
<table align="center" border="0" width="80%">
    <tr><td>
<form enctype="multipart/form-data" class="form-horizontal" action="csv2sql_<?php echo $tabella; ?>.php" method="post">
	<div class="form-group">
		<div class="col-xs-3">
        <input type="hidden" value="<?php echo $username; ?>" class="form-control" name="username" id="username" placeholder="">
        <input type="hidden" value="<?php echo $mysql; ?>" class="form-control" name="mysql" id="mysql" placeholder="">
        <input type="hidden" value="<?php echo $password; ?>" class="form-control" name="password" id="password" placeholder="">
        <input type="hidden" value="<?php echo $db; ?>" class="form-control" name="db" id="db" placeholder="">
		</div>
    </div>
	<div class="form-group">
        <label for="attach_file" class="control-label col-xs-2">File da allegare</label>
		<div class="col-xs-3">
        <input type="file" class="form-control" name="attach_file" id="attach_file">
		</div>
    </div>	
    <!--
	<div class="form-group">
        <label for="periodo" class="control-label col-xs-2">Periodo</label>
		<div class="col-xs-3">
        <input type="name" value="AAAAMM" class="form-control" name="periodo" id="periodo">
		</div>
    </div>
	-->
	<div class="form-group">
        <label for="table" class="control-label col-xs-2">Nome Tabella</label>
		<div class="col-xs-3">
        <input type="name" value="<?php echo $tabella; ?>" class="form-control" name="table" id="table" readonly>
		</div>
    </div>
	<div class="form-group">
        <label for="csvfile" class="control-label col-xs-2">Nome del file</label>
		<div class="col-xs-3">
        <input type="name" value="<?php echo $nomefile; ?>"  class="form-control" name="csv" id="csv" readonly>
		</div>
    </div>
	<div class="form-group">
	<label for="login" class="control-label col-xs-2"></label>
    <div class="col-xs-3">
    <button type="submit" class="btn btn-primary">CARICA</button>
	<input type="hidden" name="action" value="upload" />
	</div>
	</div>
</form>
</div>
    </td></tr>
</table>


</body>

<?php 
// SPOSTO IL FILE SOTTO UPLOAD
define("_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT']."/soci/upload/");

if(isset($_POST['action']) and $_POST['action'] == 'upload')
{
    if(isset($_FILES['attach_file']))
    {
        $file = $_FILES['attach_file'];
        if($file['error'] == UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']))
        {
            move_uploaded_file($file['tmp_name'], _UPLOAD_DIR.$file['name']);
        }
    }
}


if(isset($_POST['username'])&&isset($_POST['mysql'])&&isset($_POST['db'])&&isset($_POST['username']))
{
$sqlname=$_POST['mysql'];
$username=$_POST['username'];
$table=$_POST['table'];
//$periodo=$_POST['periodo'];
if(isset($_POST['password']))
{
$password=$_POST['password'];
}
else
{
$password= '';
}
$db=$_POST['db'];

//$file=$_POST['csv'];
$file = preg_replace('/[^(\x20-\x7F)]*/','', $_POST['csv']);

$cons= mysqli_connect("$sqlname", "$username","$password","$db") or die(mysql_error());

// Riga aggiunta post aggiornamento server
mysqli_query($cons,"SET SESSION sql_mode = 'TRADITIONAL'");

$result1=mysqli_query($cons,"select count(*) count from $table");
$r1=mysqli_fetch_array($result1);
$count1=(int)$r1['count'];

// ---- Sego via tutti i records nella tabella 
$truncatetabella=mysqli_query($cons,"TRUNCATE ".$table) or die(mysql_error());;

//If the fields in CSV are not seperated by comma(,)  replace comma(,) in the below query with that  delimiting character 
//If each tuple in CSV are not seperated by new line.  replace \n in the below query  the delimiting character which seperates two tuples in csv
// for more information about the query http://dev.mysql.com/doc/refman/5.1/en/load-data.html

$handler=fopen($file, "r");
    while($data=fgetcsv($handler, 0, ',')){

        //print_r($data);

        if ($data[0] != 'Cognome') {

          $result_select_SDS = odbc_exec($connect,"SELECT FILIALE_CAPOFILA, NAG FROM ANAG_NAG WHERE TIPO_NAG = 'PF' AND CODICE_FISCALE = '".$data[2]."'"); 
          while($dati_select_SDS = odbc_fetch_object($result_select_SDS)) {
            $filiale_mutua = $dati_select_SDS->FILIALE_CAPOFILA ;
            $nag_mutua = $dati_select_SDS->NAG ;
        
        mysqli_query($cons, "INSERT INTO ".$table." 
                             VALUES 
                             (
                             '".mysqli_real_escape_string($cons, $data[0])."',     
                             '".mysqli_real_escape_string($cons, $data[1])."',  
                             '".$data[2]."',
                             '".$data[3]."',
                             '".$data[4]."',
                             '".$data[5]."',
                             '".$filiale_mutua."',
                             '".$nag_mutua."'
                             )
                            "
                    ) 
                    or die("INSERT --- ".mysqli_error($cons));;

            }
        }
    }


$result2=mysqli_query($cons,"select count(*) count from $table");
$r2=mysqli_fetch_array($result2);
$count2=(int)$r2['count'];

$count=$count2-$count1;
if($count>0)
echo "<b style=color:red;> Fatto!";
echo "<b style=color:red;> Sono stati aggiunti nella tabella <i>$table</i> nr. $count records  </b> ";


// ---- Aggiorno la tabella ULTIMO_CARICAMENTO
$updcaricamento=mysqli_query($cons,"update tab_ultimo_caricamento set caricamento=now(), fonte='".$table."' WHERE fonte='".$table."'") or die(mysql_error());;
//$r4=mysqli_fetch_array($updcaricamento);

}
else{
//echo "Mysql Server address/Host name ,Username , Database name ,Table name , File name are the Mandatory Fields";
}

?>
<br><br>
<table align="center" border="0" width="80%">
    <tr><td>
<h3> Istruzioni </h3>
<p style="color:gray;padding-left: 10;"><?php echo $istruzioni; ?></p>
    </td></tr>
</table>

</html>
