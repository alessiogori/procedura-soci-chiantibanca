<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
// PF PG
// -------------------------------------------------------------------------------
// -------------------------------------------------------------------------------
$strQuery_pfpg = "SELECT count(*) as qta, 
                  CASE WHEN TipoControp = 'PERSONA FISICA' THEN 'PF'
                  ELSE 'PG'
                  END AS TipoControparte
                  FROM tab_soci_as37 
                  WHERE StatoVAL not in ('E','S','N')
                  GROUP BY TipoControparte ";
//                $strQueryCondition;

$result_pfpg = $dbhandle->query($strQuery_pfpg) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

if ($result_pfpg) {
  // creating an associative array to store the chart attributes        
  $arrData1 = array(
    "chart" => array(
      "theme" => "candy",
      "caption" => "Tipo Controparte",
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
 
  $arrData1["data"] = array();
 
  // iterating over each data and pushing it into $arrData array
  while ($row_pfpg = mysqli_fetch_array($result_pfpg)) {
    array_push($arrData1["data"], array(
      "label" => $row_pfpg["TipoControparte"],
      "value" => $row_pfpg["qta"]
    ));
    //print_r($arrData);
  }
  $jsonEncodedData = json_encode($arrData1);
}

$Chartpie2d = new FusionCharts("pie2d", "myChart1" , "50%", "450", "pfpg", "json", $jsonEncodedData);
$Chartpie2d->render();
