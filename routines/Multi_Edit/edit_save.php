<?php
include('dbcon.php');
$Id=$_POST['Id'];
$Note=$_POST['Note'];
$OperatoreMutua=$_POST['OperatoreMutua'];

$N = count($Id);
for($i=0; $i < $N; $i++)
{
	$result = mysql_query("	UPDATE tab_protocollo SET note='$Note[$i]', OperatoreMutua='$OperatoreMutua[$i]', DataRicezioneMutua=now()													where Id='$Id[$i]'") or die(mysql_error());
}
header("location: index.php");

?>