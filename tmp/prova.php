<?php 


function vero_ip() {
  if (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}


	$ip_provenienza = $_SERVER['REMOTE_ADDR'];
	$data_query = date('YmdHis');

echo $ip_provenienza;
echo '<br>';
echo $data_query;
echo '<br>';

  if (!empty($_SERVER['HTTP_CLIENT_IP']))
  {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
  {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
echo $ip;
?>