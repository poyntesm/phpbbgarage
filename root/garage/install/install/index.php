<?php

if (!defined('IN_PHPBB'))
{
	exit;
}

// Set install info with file structure to update
$install_info = array(
	'version'	=> array(
		'install' => '2.0.B5-DEV',
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
		'includes/acp/acp_logs.php',
		'includes/acp/acp_styles.php',
		'includes/mcp/mcp_logs.php',
	),
	'binary'	=> array(
	),
);

?>
