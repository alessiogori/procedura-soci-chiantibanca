<?php
/*
// -------------------------------------
// Prendo i valori dell'UTENTE e FILIALE
// -------------------------------------
$usr_id 	= ltrim($_GET['u'],'0');
$filiale_id	= $_GET['f'];
$usr_mail 	= $_GET['e'];

// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

// UTENTE
$select_user = "
				SELECT *
				FROM TAB_UTENTI
				WHERE TAB_UTENTI.COD_USE_NUMERICO = ".$usr_id."";
$result_user = odbc_exec($connect, $select_user);
while ($dati_user = odbc_fetch_object($result_user)) {

	$user 			= 'LN'.$usr_id;
	$user_nag 		= $dati_user->NAG;
	$user_nome 		= $dati_user->NOME_UTENTE;
	$user_mansione 	= $dati_user->DESCR_MANSIONE_WPROF;
}
*/
/*
$hostname = '{imap.pec.actalis.it:993/imap/ssl}INBOX';
$username = 'soci@pecchiantibanca.it';
$password = 'ChiantiBanca!S@ci23';

// Connect to the mailbox
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to mailbox: ' . imap_last_error());

// Search for all emails
$emails = imap_search($inbox, 'ALL');

if ($emails) {
    // Loop through each email
    foreach ($emails as $email_number) {
        $email_header = imap_headerinfo($inbox, $email_number);
        $subject = $email_header->subject;
        $from = $email_header->from[0]->mailbox . "@" . $email_header->from[0]->host;
        $date = date("Y-m-d H:i:s", strtotime($email_header->date));
        
        echo "Subject: $subject<br>";
        echo "From: $from<br>";
        echo "Date: $date<br>";
        
        // Fetch email body
        $email_body = imap_fetchbody($inbox, $email_number, 1.2);
        // echo "Body: $email_body<br>";
        
        echo "--------------------------<br>";
    }
} else {
    echo "No emails found.";
}

// Close the mailbox connection
imap_close($inbox);
*/

phpinfo();

?>
