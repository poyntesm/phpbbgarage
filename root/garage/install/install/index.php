<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

// Set install info with file structure to update
$install_info = array(
	'version'	=> array(
		'install' => '2.0.B3',
	),
	'files'		=> array(
		'common.php',
		'memberlist.php',
		'viewonline.php',
		'vietopic.php',
		'adm/index.php',
		'includes/functions.php',
		'includes/functions_user.php',
		'includes/session.php',
		'includes/acp/acp_styles.php',
	),
	'binary'	=> array(
	),
);

?>
