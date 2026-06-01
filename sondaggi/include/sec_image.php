<?php
// PHP Survey, https://www.netartmedia.net/php-survey
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
   require('security_image.php');
   
   session_start();
    
   $oSecurityImage = new SecurityImage(150, 30);
   if ($oSecurityImage->Create()) 
   {
          $_SESSION['code'] = md5($oSecurityImage->GetCode());
   }
    else 
	{
      echo 'Image GIF library is not installed.';
   }
?>