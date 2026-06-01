<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE IN PERCENTUALE
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery_aree1 = "SELECT count(*) as qta, area 
                  FROM tab_soci_as37 as a, tab_psw as p 
                  WHERE a.statoVAL not in ('E','S','N')
                  AND a.codFil = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area 
                  ORDER BY 1 desc   ";
//                $strQueryCondition;

$result_aree1 = $dbhandle->query($strQuery_aree1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

if ($result_aree1) {
  // creating an associative array to store the chart attributes        
  $arrDataX = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Soci in essere per Area",
      "subcaption" => "in percentuale",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",
      //"paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
      "showLegend" => "0",
      //"exportEnabled" => "1"
    )
  );
 
   $arrDataX["data"] = array();

  // iterating over each data and pushing it into $arrData array
  while ($row_aree1 = mysqli_fetch_array($result_aree1)) {
    array_push($arrDataX["data"], array(
      "label" => $row_aree1['area'],
      "value" => $row_aree1["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrDataX);
}

//$Chartcolumn2d = new FusionCharts("column2d", "myChartX" , "100%", "450", "aree", "json", $jsonEncodedData);
//$Chartcolumn2d->render();
$Chartpie2d = new FusionCharts("pie2d", "myChartX" , "100%", "450", "aree1", "json", $jsonEncodedData);
$Chartpie2d->render();


// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// AREE IN VALORE ASSOLUTO
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery_aree1 = "SELECT count(*) as qta, area 
                  FROM tab_soci_as37 as a, tab_psw as p 
                  WHERE a.statoVAL not in ('E','S','N')
                  AND a.codFil = CAST(p.filiale AS UNSIGNED)
                  GROUP BY area 
                  ORDER BY 1 desc   ";
//                $strQueryCondition;

$result_aree1 = $dbhandle->query($strQuery_aree1) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

if ($result_aree1) {
  // creating an associative array to store the chart attributes        
  $arrDataXX = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Soci in essere per Area",
      "subcaption" => "in valore assoluto",
      "captionFontSize" => "24",
      "subcaptionFontSize" => "20",
      "subcaptionFontColor" => "#646464",
      "bgColor" => "#222222",
      "showValues"=> "1",
      //"rotateValues" => "1",      
      "placeValuesInside"=> "1",
      //"paletteColors" => "#A2A5FC, #41CBE3, #EEDA54, #BB423F #,F35685",
      "baseFont" => "Quicksand, sans-serif",
      //"exportEnabled" => "1"
    )
  );
 
   $arrDataXX["data"] = array();

  // iterating over each data and pushing it into $arrData array
  while ($row_aree1 = mysqli_fetch_array($result_aree1)) {
    array_push($arrDataXX["data"], array(
      "label" => $row_aree1['area'],
      "value" => $row_aree1["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrDataXX);
}

$Chartcolumn2d = new FusionCharts("column2d", "myChartXX" , "100%", "450", "aree2", "json", $jsonEncodedData);
$Chartcolumn2d->render();
