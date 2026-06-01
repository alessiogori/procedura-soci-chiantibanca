<?php

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');
require_once('_functions.php');  

// ---------------------------------------
// VARIABILI PASSATE DA PORTALE SOCI
// ---------------------------------------
$modello = 'SO99';
$socio = $_GET['socio'];
//$motivazione = $_GET['motivazione'];
$cag = $_GET['cag'];
$idsocio = $_GET['idsocio'];
$luogo = $_GET['luogo'];
$oggi = date("d.m.Y");
// ---------------------------------------

        include("../config/_config.php");
        $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

        // Estrazione della data ultimo caricamento
        $select_last = " SELECT  caricamento
                        FROM tab_ultimo_caricamento
                        WHERE fonte = 'tab_soci_as37'
                        ";

        $query_last = mysqli_query($connection, $select_last); 
            while($dati_last=mysqli_fetch_array($query_last)){ 
                $ultimo_aggiornamento = $dati_last['caricamento'];
            }

        // Estrazione dei dati anagrafici necessari
        $selectdati = " SELECT  prot, cag, luogoNasc as LuogoNascita, dataNasc as DataNascita, provNasc,
                                indirSpedIndirizzo as Indirizzo, indirSpedCAP as Cap, indirSpedLocalita as Localita, 
                                indirSpedProvincia, nAzTot, nominaleAzTot, sovrRimbCompl, cagDelegato, int1Delegato,
                                dataAmmiss, dataEntrata, dataUscita, dataEstinzione, causaleUscita, titoloOnorifico,
                                sesso, telefono, indirizzoEmail, indirizzoPEC, tipoContropVAL, codFil
                        FROM tab_soci_as37
                        WHERE cag = ".$cag;

        $querydati = mysqli_query($connection, $selectdati); 

        if (mysqli_num_rows($querydati) <= 0) {

            $NuMSocio = '';
            $LuogoNascita = '';
            $ProvinciaNascita = '';
            $DataNascita = '';
            $Indirizzo = '';
            $Cap = '';
            $Localita = '';
            $Provincia = '';
            $Azioni = '';
            $Nominale = '';
            $CagDelegato = '';
            $IntestazioneDelegato = '';
            $DataAmmissione = '';
            $dataEntrata = '';
            $dataUscita = '';
            $dataEstinzione = '';
            $CausaleUscita = '';
            $TitoloOnorifico = '';
            $Sesso = '';
            $Telefono = '';
            $Mail = '';
            $PEC = '';
            $filiale = '';

            }
            else
            {

            while($dati=mysqli_fetch_array($querydati)){ 

                $NuMSocio = $dati['prot'];
                $LuogoNascita = $dati['LuogoNascita'];
                $DataNascita = $dati['DataNascita'];
                $Indirizzo = $dati['Indirizzo'];
                $Cap = $dati['Cap'];
                $Localita = $dati['Localita'];
                $Provincia = $dati['indirSpedProvincia'];
                $nAzTot = $dati['nAzTot'];
                $Azioni = $dati['nAzTot'];

                if ( $dati['tipoContropVAL'] == 11000 )
                    {$datinascita = 'Nato/a a '.$LuogoNascita.' il '.$DataNascita;}
                else {$datinascita = '';}

                $Nominale = 'Eur '.substr(str_replace(",",".",$dati['nominaleAzTot']),0,-1);
                $Sovrap   = 'Eur '.substr(str_replace(",",".",$dati['sovrRimbCompl']),0,-1);
                $Totale   = floatval(str_replace(",",".",$dati['nominaleAzTot'])) + floatval(str_replace(",",".",$dati['sovrRimbCompl'])); 
                $ValTotale= 'Eur '.$Totale;
                
                $CagDelegato = $dati['cagDelegato'];
                $IntestazioneDelegato = $dati['int1Delegato'];
                $DataAmmissione = $dati['dataAmmiss'];
                $dataEntrata = $dati['dataEntrata'];
                $dataUscita = $dati['dataUscita'];
                $dataEstinzione = $dati['dataEstinzione'];
                
                if ( $dati['causaleUscita'] == "PER MORTE" )
                    {$CausaleUscita = 'Uscito '.$dati['causaleUscita'].' (reg.to Libro Soci il '.$dataUscita.')';}
                else {$CausaleUscita = '';}
                //$CausaleUscita = $dati['causaleUscita'];
                
                $TitoloOnorifico = $dati['titoloOnorifico'];
                $Sesso = $dati['sesso'];
                $Telefono = $dati['telefono'];
                $Mail = $dati['indirizzoEmail'];
                $PEC = $dati['indirizzoPEC'];
                $Filiale = $dati['codFil'];
        
            }
        }
        logquery_modelli ($modello,$cag,$Filiale);        // scrive il LOG del documento prodotto



class Pdf extends Tcpdf
{
    /**
     * "Remembers" the template id of the imported page
     */
    protected $tplId;

    /**
     * Draw an imported PDF logo on every page
     */
    function Header()
    {
        if ($this->tplId === null) {
            $this->setSourceFile('_testdmx.pdf');
            $this->tplId = $this->importPage(1);
        }
        $size = $this->useImportedPage($this->tplId, 130, 5, 60);

    }
}



// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf = new TCPDF();

// set document information
//$pdf->SetCreator(PDF_CREATOR);
//$pdf->SetAuthor('Nicola Asuni');
//$pdf->SetTitle('TCPDF Example 050');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


Header();

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 050', PDF_HEADER_STRING);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// set font
$pdf->SetFont('helvetica', '', 11);

// add a page
$pdf->AddPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// set style for barcode
$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);

// -------------------------------------------------------------------
// DATAMATRIX (ISO/IEC 16022:2006)

$pdf->write2DBarcode('http://www.tcpdf.org', 'DATAMATRIX', 80, 150, 50, 50, $style, 'N');
$pdf->Text(80, 145, 'DATAMATRIX (ISO/IEC 16022:2006)');

// -------------------------------------------------------------------

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_050.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+