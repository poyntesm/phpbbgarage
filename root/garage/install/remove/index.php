<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

// Set install info with file structure to update
$remove_info = array(
	'version'	=> array(
		'remove' => '2.0.B3',
	),
	'files'		=> array(
		'common.php',
		'memberlist.php',
		'viewonline.php',
		'viewtopic.php',
		'adm/index.php',
		'includes/functions.php',
		'includes/functions_user.php',
		'includes/session.php',
		'includes/acp/acp_language.php',
		'includes/acp/acp_styles.php',
	),
);

?>
