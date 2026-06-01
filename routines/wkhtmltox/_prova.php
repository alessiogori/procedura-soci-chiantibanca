  <?php
    $webpageURLWithChart = "http://10.119.192.46:8080/soci/stats/repcda_ammissioni.php?annomese=";
    $outputImageFileName = "savedImage.png";
    $delay = 500;
    $shellout = shell_exec("E:\WWW\SOCI\ROUTINES\WKHTMLTOX\wkhtmltoimage --title 'PROVA DI STAMPA' $webpageURLWithChart $outputImageFileName" );
    echo $shellout;
    ?>