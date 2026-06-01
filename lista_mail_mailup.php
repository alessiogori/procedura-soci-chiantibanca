<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script
if ($_GET['scelta'] == 'full') 
{
    $TITOLO = 'Elenco Mail su Soci attivi (per MailUp)';
    $condizione = '';
}
else
{
    $TITOLO = 'Elenco Mail su Soci Under 35 (per MailUp)';
    $condizione = " AND ETA <= 35 AND TIPO_NAG = 'PF' ";
}


// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

echo '<html>
        <head>
        <title>'.$TITOLO.'</title>
        </head>
        <style type="text/css">
          @import "css/bootstrap.css";
          @import "css/bootstrap.min.css";
          @import "css/fontawesome-free/css/all.min.css";
        </style> 

        <body><br><br>
        ';

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// Mi connetto al database MYSQL
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


?>
<script>
function selectElementContents(el) {
    var body = document.body, range, sel;
    if (document.createRange && window.getSelection) {
        range = document.createRange();
        sel = window.getSelection();
        sel.removeAllRanges();
        try {
            range.selectNodeContents(el);
            sel.addRange(range);
        } catch (e) {
            range.selectNode(el);
            sel.addRange(range);
        }
    } else if (body.createTextRange) {
        range = body.createTextRange();
        range.moveToElementText(el);
        range.select();
    }
}
</script>

<?php

echo '
      <div class="alert alert-dismissible alert-warning">
            <h2 class="alert-heading">'.$TITOLO.'</h2>
      </div>

';

      // QUERY DI RICERCA
      $select_query =   "
            SELECT procedura as FONTE, c.NAG, concat(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B) as NOMINATIVO, VALORE_DATO_CNT as MAIL
            FROM sds_soci_daticontatto as c, sds_soci as s
            WHERE SOCIO_ISTITUTO = '1'
            and tipo_dato_cnt = 'MAIL'
            and VALORE_DATO_CNT <> 'nomail@nomail.it'
            ".$condizione."
            and c.NAG = s.NAG
            group by 
            procedura, c.NAG, concat(s.INTESTAZIONE_A,' ',s.INTESTAZIONE_B), VALORE_DATO_CNT
            ORDER BY 3
                        "; 

        $result = mysqli_query($connection, $select_query);
        $contenutofile = '';

        $myfile = fopen("tmp/mailup.csv", "w");
        $contenutofile .= "Fonte;NAG;Nominativo;Mail\n";
    
        while($cnt_file = mysqli_fetch_array($result))
        {  
            $contenutofile .= 
            $cnt_file['FONTE'].";".
            $cnt_file['NAG'].";".
            $cnt_file['NOMINATIVO'].";".
            $cnt_file['MAIL']."\n";
        }

    fwrite($myfile, $contenutofile);
    fclose($myfile);

?>

<center>Attendi la comparsa del pulsante per scaricare il tracciato</center>

    <script>
        // Funzione per mostrare il pulsante dopo un certo numero di secondi
        function mostraPulsanteDopoTempo() {
            var pulsante = document.getElementById('pulsante');
            pulsante.style.display = 'block'; // Cambia il display in 'block' per renderlo visibile
        }

        // Imposta un ritardo di 5 secondi (5000 millisecondi) prima di chiamare la funzione mostraPulsanteDopoTempo
        setTimeout(mostraPulsanteDopoTempo, 5000); // Cambia '5000' al numero di millisecondi desiderato
    </script>

    <br><center><a class="btn btn-outline-warning" id="pulsante" style="display: none;" href="tmp/mailup.csv">Scarica il dettaglio completo</a>

<?php
// closing database connection      
$dbhandle->close();             
?>

</body>
</html>
