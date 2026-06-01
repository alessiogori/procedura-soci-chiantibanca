<?php

require_once('tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		$this->Image('image/intestazione.png', 0, 0, 210, 33, 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
	}

	// Page footer
	public function Footer() {
		$this->Image('image/piedipagina.png', 0, 264, 210, 33, 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
	}
}

	$border=0;
	$dim_caratteri=8;
	$h_cella=4;

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Autore');
	$pdf->SetTitle('Titolo');
	$pdf->SetSubject('Soggetto');
	$pdf->SetKeywords('parole, di, ricerca');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(10, 40, 10);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 33);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	$pdf->AddPage('P','A4');		// 210x297mm

	$pdf->SetFont('Helvetica','B',$dim_caratteri+2);
	$pdf->SetTextColor(0,0,0);

	$pdf->Cell(190,$h_cella,'Lettera di comunicazione '.date("d/m/Y H:i:s"),$border,1,'C');

	$pdf->Cell(190,2,'',$border,1,'C');

	$pdf->SetFont('Helvetica','N',$dim_caratteri);
	$pdf->Cell(15,$h_cella,'CED',$border,0,'L');
	$pdf->SetFont('Helvetica','B',$dim_caratteri);
	$pdf->Cell(20,$h_cella,'123',$border,0,'L');
	$pdf->SetFont('Helvetica','N',$dim_caratteri);
	$pdf->Cell(25,$h_cella,'Dipendente',$border,0,'L');
	$pdf->SetFont('Helvetica','B',$dim_caratteri);
	$pdf->Cell(130,$h_cella,'Pippo',$border,1,'L');

	$pdf->Cell(190,2,'',$border,1,'C');

	$txt='Testo testo molto testo meglio tanto che pochino forse troppo.

Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo. Testo testo molto testo meglio tanto che pochino forse troppo.

Testo testo molto testo meglio tanto che pochino forse troppo.
';
	$pdf->MultiCell(190,6,$txt,$border,'J');

	$nomefilebase="lettera_".date("YmdHis").".pdf";
	$pdf->Output($nomefilebase,'D');

?>
