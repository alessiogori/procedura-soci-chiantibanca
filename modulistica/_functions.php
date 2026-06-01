<?php 
function logquery_modelli ($modello, $cag, $filiale)
{

$dbname = 'soci';
$dbuser = '3qa25raa3f';
$dbpass = '8ynDHEuDkMhM63dy';
$dbhost = 'localhost';
$connect = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Unable to Connect to '$dbhost'");
mysqli_select_db($connect,$dbname) or die("Could not open the db '$dbname'");

	$ip_provenienza = $_SERVER['REMOTE_ADDR'];
	$data_query = date('YmdHis');

    /*
	$update_log="INSERT INTO `tab_log`(`id`, `ip`, `data_query`, `testo_query`, `nomefile`, `filiale`) VALUES
				 (null, '".$ip_provenienza."', '".$data_query."', '".$testo_query."', '".$nomefile."', '".$filiale."')";
	*/			 
	$update_log="INSERT INTO `tab_log_modelli`(`id`, `ipstampa`, `datastampa`, `modellostampato`, `cag`, `filiale`) VALUES
				 (null, '".$ip_provenienza."', '".$data_query."', '".$modello."', '".$cag."', '".$filiale."')";
				 
//echo $update_log;
mysqli_query($connect, $update_log);
}

function data_ita($data_usa)
{
	$data_corretta = substr($data_usa, 6, 2).'/'.substr($data_usa, 4, 2).'/'.substr($data_usa, 0, 4);

	return $data_corretta;

}
?>