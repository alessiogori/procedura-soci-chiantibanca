<?php 
include ("backup.php");

// ($host = 'localhost', $username = 'root', $password = '', $database = 'test', $charset = 'utf-8', $lang = 'en')
$db = new BackUp( 'localhost', 'uasdn93n', 'YFYQDQrldfIycbPS', 'mutua', 'utf8', 'en' );

// To backup DB
$db->backup ();

//To restore from backup
// $db->restore ( __DIR__.'/backup/20121027194215_all_v1.sql')

?>