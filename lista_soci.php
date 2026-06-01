<?php
//////////////////////////////////////////////////////////////////
// SADAS ESEMPIO
// Viene usato l'utente ODBCUSER01 creato per SOCI
// Author: Alessio Fedi - 28.10.2022
//////////////////////////////////////////////////////////////////
// Nome Script

// Execution Time = 0 - No Limit
ini_set('max_execution_time', '0');

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

echo '<html>
        <head>
        <title>Elenco completo Soci in essere (Banca)</title>
        </head>
        <style type="text/css">
          @import "css/bootstrap.css";
          @import "css/bootstrap.min.css";
          @import "css/fontawesome-free/css/all.min.css";
        </style> 

        <body><br><br>
        ';

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
            <h2 class="alert-heading">Elenco completo Soci in essere (Banca)</h2>
      </div>

';

      // QUERY DI RICERCA
      $select_query =   "
            SELECT 
            sds_soci.IDSOCIO,
            FILIALE_CAPOFILA AS FILIALE_ANAGRAFICA,
            sds_soci.NAG,
            CONCAT(INTESTAZIONE_A, ' ', INTESTAZIONE_B) AS INTESTAZIONE,
            DATA_NASCITA AS DATA_NASCITA,
            ETA,
            DATA_ENTRATA,
            procedura as FONTE, 
            CASE WHEN TIPO_DATO_CNT = 'TEL' THEN VALORE_DATO_CNT END AS TELE,
            CASE WHEN TIPO_DATO_CNT = 'CELL' THEN VALORE_DATO_CNT END AS CELL,
            CASE WHEN TIPO_DATO_CNT = 'MAIL' THEN VALORE_DATO_CNT END AS MAIL,
            CASE WHEN TIPO_DATO_CNT = 'PEC' THEN VALORE_DATO_CNT END AS PEC,
            NAG_RAPPR AS NAG_RAPP,
            INTESTAZIONE_RAPPR AS RAPPRESENTANTE,
            NUMERO_AZIONI AS NUM_AZIONI,
            (NUMERO_AZIONI * 30.33) as VAL_AZIONI,
            DATA_USCITA,
            CTIPMOVUSCITA,
            CODICE_FISCALE,
            SETTORISTA,
            STATO_NAG

            FROM
                sds_soci 
                LEFT JOIN sds_soci_certificati ON (sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO)
                LEFT JOIN sds_soci_daticontatto ON (sds_soci.NAG = sds_soci_daticontatto.NAG)
                LEFT JOIN tab_storico_pistoia ON  (sds_soci.IDSOCIO = tab_storico_pistoia.prot)

            WHERE SOCIO_ISTITUTO = '1'
            group by INTESTAZIONE, DATA_NASCITA, sds_soci.IDSOCIO, sds_soci.NAG, FILIALE_CAPOFILA
            ORDER BY 1
                        "; 

        $result = mysqli_query($connection, $select_query);
        $contenutofile = '';

        $myfile = fopen("tmp/lista_soci.csv", "w");
        $contenutofile .= "Socio;Filiale;NAG;Nominativo;DataNascita;Eta;DataEntrata;Fonte;Tel;Cell;Mail;PEC;NagRappr;Rappresentante;NumAzioni;ValoreAzioni;DataUscita;TipoUscita;CodiceFiscale;Settorista;StatoNAG;Mutua\n";
    
        while($cnt_file = mysqli_fetch_array($result))
        {  


            // ********************************************************
            // RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
            // ********************************************************
            $select_mutua     = "   SELECT * FROM TAB_MUTUA
                                    WHERE CODICEFISCALE = '".$cnt_file['CODICE_FISCALE']."'";
            $querydati_mutua = mysqli_query($connection, $select_mutua);
                if(mysqli_num_rows($querydati_mutua) > 0)
                    while($datisociomutua = mysqli_fetch_array($querydati_mutua))
                    {
                       $esistenzasociomutua = 
                        "<i class='fas fa-check fa-2x col-auto' style='color:#9FE2BF;'></i>";
                       $flagsociomutua = 'SI';
                       $modulimutua = 'no';
                    }
                else
                {
                    $esistenzasociomutua = '';
                    $flagsociomutua = 'NO';
                    $modulimutua = 'no';
                }

                if ($cnt_file['STATO_NAG'] == '1') {$statonag = 'Cliente';}
                elseif ($cnt_file['STATO_NAG'] == '2') {$statonag = 'Ex Cliente';}
                else {$statonag = '';}        
                
            $contenutofile .= 
            $cnt_file['IDSOCIO'].";".
            $cnt_file['FILIALE_ANAGRAFICA'].";".
            $cnt_file['NAG'].";".
            $cnt_file['INTESTAZIONE'].";".
            $cnt_file['DATA_NASCITA'].";".
            $cnt_file['ETA'].";".
            $cnt_file['DATA_ENTRATA'].";".
            $cnt_file['FONTE'].";".
            $cnt_file['TELE'].";".
            $cnt_file['CELL'].";".
            $cnt_file['MAIL'].";".
            $cnt_file['PEC'].";".
            $cnt_file['NAG_RAPP'].";".
            $cnt_file['RAPPRESENTANTE'].";".
            $cnt_file['NUM_AZIONI'].";".
            $cnt_file['VAL_AZIONI'].";".
            $cnt_file['DATA_USCITA'].";".
            $cnt_file['CTIPMOVUSCITA'].";".
            $cnt_file['CODICE_FISCALE'].";".
            $cnt_file['SETTORISTA'].";".
            $statonag.";".
            $flagsociomutua."\n";
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

    <br><center><a class="btn btn-outline-warning" id="pulsante" style="display: none;" href="tmp/lista_soci.csv">Scarica il dettaglio completo</a>

<?php
// closing database connection      
$dbhandle->close();             
?>

</body>
</html>
