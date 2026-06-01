<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// SESSO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery_sex = "SELECT count(*) as qta, sesso
                  FROM tab_soci_as37 
                  WHERE StatoVAL not in ('E','S','N')
                  AND sessoVAL in ('M','F')
                  GROUP BY sesso ";
//                $strQueryCondition;

$result_sex = mysqli_query($connection, $strQuery_sex);
// $result_sex = $dbhandle->query($strQuery_sex) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

if ($result_sex) {
  // creating an associative array to store the chart attributes        
  $arrData2 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Sesso",
      //"subcaption" => "Situazione al ".$adesso,
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",
      //"paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
      //"exportEnabled" => "1"
    )
  );
 
  $arrData2["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row_sex = mysqli_fetch_array($result_sex)) {
    array_push($arrData2["data"], array(
      "label" => $row_sex['sesso'],
      "value" => $row_sex["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData2);
}

$Chartpie2d = new FusionCharts("pie2d", "myChart2" , "50%", "450", "sex", "json", $jsonEncodedData);
$Chartpie2d->render();
?>
