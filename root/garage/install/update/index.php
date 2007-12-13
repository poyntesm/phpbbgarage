<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

// Set update info with file structure to update
$update_info = array(
	'version'	=> array(
		'from' => '2.0.B2',
	       	'to' => '2.0.B3'
	),
	'files'		=> array(
		'garage.php',
		'garage_dynorun.php',
		'adm/images/phpbbgarage_logo.gif'
	),
	'binary'	=> array(
		'adm/images/phpbbgarage_logo.gif'
	),
);

?>
