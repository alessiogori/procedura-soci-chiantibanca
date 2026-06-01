<?php
// *****************************************************************************
// Portale ChiantiBanca Soci
// Sviluppo e realizzazione: Alessio Fedi (2023)
// *****************************************************************************
// 
/* --------------------------------------------------------------------- */
// VARIABILI IN USO NEL FILE
$titolo  = "Caricamento SOCI - LOG SOCICN02 FORMADOC";
$tabella = "tmp_formadoc_log";
$nomefile= "tmp_formadoc_log.csv";
$istruzioni = '	1. Prendi il file di LOG fornito dall\'Organizzazione (csv)<br>
                2. Salva il file in O:\COMUNITY BANKING\_Dati\csv e chiamalo <b>tmp_formadoc_log.csv</b><br>
				3. Carica il file <b>tmp_formadoc_log.csv</b> su questa pagina (sfoglia...)<br>

                				';

$mysql = "localhost";
$username = "3qa25raa3f";
$password = "8ynDHEuDkMhM63dy";
$db = "soci";

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

/*
INSERT INTO `sds_sinergiareport_soci` VALUES ('231', '30', 'CHESI GIAMPIERO', '0', '', '', 'Ex socio', 'PF', 'PF', '0', 'CHSGPR38B02F598M', '', 'N', '01/01/1973', '01/01/1973', '01/04/1973', '24/09/2015', '0', '0', '0', '0', '0', '0', '0', '0', '0', '02/02/1938', '84', '0577286695', '', '', '0577286695', '', 'focardi@pietrofocardi.it', '', 'N', '000', 'Ramo non pertinente', '600', 'Famiglie consumatrici', '0595', 'Condizione professionale indeterminata', '', '', 'G030', '011', 'VIA R. MANETTI, 2', '53100', '', 'SIENA', 'SI', '0', '', '0', '0', '', '', '', '', '', '', '', '0');
*/

$handler=fopen($file, "r");
$i=0; //so we can skip first row

    while($data=fgetcsv($handler, 0, ';')){

        //  print_r($data);
        if($i>0) {

        @mysqli_query($cons, "INSERT INTO ".$table." 
                             VALUES 
                             (
                             '".$data[0]."', 
                             '".$data[1]."', 
                             '".mysqli_real_escape_string($cons, $data[2])."',
                             '".mysqli_real_escape_string($cons, strtolower(preg_replace('/\s+/', '.', $data[3])))."',
                             '".mysqli_real_escape_string($cons, $data[4])."',
                             '".$data[5]."', 
                             '".$data[6]."'
                             )
                            "
                    ) 
                    or die("INSERT --- ".mysqli_error($cons));;
        }

        $i++;
                    
    }

/*
mysqli_query($cons, '
    LOAD DATA LOCAL INFILE "'.$file.'"
        INTO TABLE '.$table.'
        FIELDS TERMINATED by \';\'
        OPTIONALLY ENCLOSED BY \'"\'
        LINES TERMINATED BY \'\r\n\'
        IGNORE 1 LINES  
')or die(mysql_error());
*/

$delete=mysqli_query($cons,"DELETE from $table 
                            WHERE TIPOLOGIA = '' OR TIPOLOGIA = 'TIPOLOGIA' ")
        or die(mysql_error());;

$result2=mysqli_query($cons,"select count(*) count from $table");
$r2=mysqli_fetch_array($result2);
$count2=(int)$r2['count'];

$count=$count2-$count1;
if($count>0)
echo "<b style=color:red;> Fatto!";
echo "<b style=color:red;> Sono stati aggiunti nella tabella <i>$table</i> nr. $count records  </b> ";

// ---- Aggiorno il campo PERIODO della tabella 
//$updperiodo=mysqli_query($cons,"update ".$table." set periodo='".$periodo."' WHERE periodo is null") or die(mysql_error());;
//$r3=mysqli_fetch_array($updperiodo);

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
