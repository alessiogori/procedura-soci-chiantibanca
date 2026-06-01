<?php
    /*
    * para ver el ejemplo crear su config.php con los nombres que usa el contructor y cambiar 
    * "repovisita" por una tabla de su base de datos
    */
    include("../config/_config.php");
    include("CMySqldbHTML.php");
    $objnotbl = new CMySqldbHTML("", $database, $server, $db_user, $db_pass);
    // Cambiar por la tabla propia
    $tblnm = "tab_bisogni";
    $obj = new CMySqldbHTML($tblnm, $database, $server, $db_user, $db_pass);
    // Opcional solo si se quiere usar exepciones
    /*$arexp = array('Marca_temporal','Ord');
    $obj->setArcolno($arexp);
    $obj->setTstmp('Marca_temporal');
    $obj->setOrd('Ord');
    $namesend = 'Luis Gabiel Hern&aacute;ndez Valderrama';
    */
    $objnotbl->showrCObjeto();
    $obj->showrCObjeto();
    $namesend = '';
    $obj->drawForm($namesend);    
?>