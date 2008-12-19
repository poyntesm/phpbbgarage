<?php
/**
*
* @package install
* @version $Id$
* @copyright (c) 2006 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

$updates_to_version = '2.0.B4';

// Return if we "just include it" to find out for which version the database update is responsuble for
if (defined('IN_PHPBB') && defined('IN_INSTALL'))
{
	return;
}

/**
*/
define('IN_PHPBB', true);
define('IN_INSTALL', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

// Report all errors, except notices
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);

@set_time_limit(0);

// Include essential scripts
include($phpbb_root_path . 'config.' . $phpEx);

if (!isset($dbms))
{
	die("Please read: <a href='../docs/INSTALL.html'>INSTALL.html</a> before attempting to update.");
}

// Load Extensions
if (!empty($load_extensions))
{
	$load_extensions = explode(',', $load_extensions);

	foreach ($load_extensions as $extension)
	{
		@dl(trim($extension));
	}
}

// Include files
require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
require($phpbb_root_path . 'includes/cache.' . $phpEx);
require($phpbb_root_path . 'includes/template.' . $phpEx);
require($phpbb_root_path . 'includes/session.' . $phpEx);
require($phpbb_root_path . 'includes/auth.' . $phpEx);

require($phpbb_root_path . 'includes/functions.' . $phpEx);

if (file_exists($phpbb_root_path . 'includes/functions_content.' . $phpEx))
{
	require($phpbb_root_path . 'includes/functions_content.' . $phpEx);
}

require($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
require($phpbb_root_path . 'includes/constants.' . $phpEx);
require($phpbb_root_path . 'includes/mods/constants_garage.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);
require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);

// If we are on PHP >= 6.0.0 we do not need some code
if (version_compare(PHP_VERSION, '6.0.0-dev', '>='))
{
	/**
	* @ignore
	*/
	define('STRIP', false);
}
else
{
	set_magic_quotes_runtime(0);
	define('STRIP', (get_magic_quotes_gpc()) ? true : false);
}

$user = new user();
$cache = new cache();
$db = new $sql_db();

// Add own hook handler, if present. :o
if (file_exists($phpbb_root_path . 'includes/hooks/index.' . $phpEx))
{
	require($phpbb_root_path . 'includes/hooks/index.' . $phpEx);
	$phpbb_hook = new phpbb_hook(array('exit_handler', 'phpbb_user_session_handler', 'append_sid', array('template', 'display')));

	foreach ($cache->obtain_hooks() as $hook)
	{
		@include($phpbb_root_path . 'includes/hooks/' . $hook . '.' . $phpEx);
	}
}
else
{
	$phpbb_hook = false;
}
require($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
$mod_db = new phpbb_db_tools($db);

// Connect to DB
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);

// We do not need this any longer, unset for safety purposes
unset($dbpasswd);

$user->ip = (!empty($_SERVER['REMOTE_ADDR'])) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '';

$sql = "SELECT config_value
	FROM " . CONFIG_TABLE . "
	WHERE config_name = 'default_lang'";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$language = basename(request_var('language', ''));

if (!$language)
{
	$language = $row['config_value'];
}

if (!file_exists($phpbb_root_path . 'language/' . $language))
{
	die('No language found!');
}

// And finally, load the relevant language files
include($phpbb_root_path . 'language/' . $language . '/common.' . $phpEx);
include($phpbb_root_path . 'language/' . $language . '/acp/common.' . $phpEx);
include($phpbb_root_path . 'language/' . $language . '/install.' . $phpEx);

// Set PHP error handler to ours
//set_error_handler('msg_handler');

// Define some variables for the database update
$inline_update = (request_var('type', 0)) ? true : false;

// Define outdates possible with routine functions
$database_update_info = array(
	// Changes from 2.0.B3 to the next version
	'2.0.B3'			=> array(
		// Change the following columns
		'change_columns'		=> array(
			GARAGE_BLOGS_TABLE	=> array(
				'bbcode_uid'		=> array('VCHAR:8', ''),
			),
			GARAGE_VEHICLES_TABLE	=> array(
				'price'			=> array('DECIMAL:10', 0),
				'bbcode_uid'		=> array('VCHAR:8', ''),
			),
			GARAGE_GUESTBOOKS_TABLE	=> array(
				'bbcode_uid'		=> array('VCHAR:8', ''),
			),
			GARAGE_PREMIUMS_TABLE	=> array(
				'premium'		=> array('DECIMAL:10', 0),
			),
			GARAGE_SERVICE_HISTORY_TABLE	=> array(
				'price'			=> array('DECIMAL:10', 0),
			),
			GARAGE_MODIFICATIONS_TABLE	=> array(
				'price'			=> array('DECIMAL:10', 0),
				'install_price'		=> array('DECIMAL:10', 0),
				'bbcode_uid'		=> array('VCHAR:8', ''),
			),
		),
		// Add the following columns
		'add_columns'				=> array(
			GARAGE_MODIFICATIONS_TABLE	=> array(
				'bbcode_options'	=> array('UINT', '7'),
			),
		),
		// Drop the following columns
		'drop_columns'				=> array(
			GARAGE_MODIFICATIONS_TABLE	=> array(
				'manufacturer_id'
			),
			GARAGE_BLOGS_TABLE	=> array(
				'bbcode_flags'
			),
		),
	),
);

$error_ary = array();
$errored = false;

header('Content-type: text/html; charset=UTF-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $lang['DIRECTION']; ?>" lang="<?php echo $lang['USER_LANG']; ?>" xml:lang="<?php echo $lang['USER_LANG']; ?>">
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="<?php echo $lang['USER_LANG']; ?>" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="imagetoolbar" content="no" />

<title><?php echo $lang['UPDATING_TO_LATEST_STABLE']; ?></title>

<link href="./../../adm/style/admin.css" rel="stylesheet" type="text/css" media="screen" />

</head>

<body>
<div id="wrap">
	<div id="page-header">&nbsp;</div>

	<div id="page-body">
		<div id="acp">
		<div class="panel">
			<span class="corners-top"><span></span></span>
				<div id="content">
					<div id="main">

	<h1><?php echo $lang['UPDATING_TO_LATEST_STABLE']; ?></h1>

	<br />

	<p><?php echo $lang['DATABASE_TYPE']; ?> :: <strong><?php echo $db->sql_layer; ?></strong><br />
<?php

// To let $garage_admin->set_config() calls succeed, we need to make the config array available globally
$garage_config = array();
$sql = 'SELECT *
	FROM ' . GARAGE_CONFIG_TABLE;
$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{
	$garage_config[$row['config_name']] = $row['config_value'];
}
$db->sql_freeresult($result);

echo $lang['PREVIOUS_VERSION'] . ' :: <strong>' . $garage_config['version'] . '</strong><br />';
echo $lang['UPDATED_VERSION'] . ' :: <strong>' . $updates_to_version . '</strong>';

$current_version = str_replace('b', 'B', str_replace('rc', 'RC', strtolower($garage_config['version'])));
$latest_version = str_replace('b', 'B', str_replace('rc', 'RC', strtolower($updates_to_version)));
$orig_version = $garage_config['version'];

// If the latest version and the current version are 'unequal', we will update the version_update_from, else we do not update anything.
if ($inline_update)
{
	if ($current_version !== $latest_version)
	{
		$garage_admin->set_config('version_update_from', $orig_version, $garage_config);
	}
}
else
{
	// If not called from the update script, we will actually remove the traces
	$db->sql_query('DELETE FROM ' . GARAGE_CONFIG_TABLE . " WHERE config_name = 'version_update_from'");
}

// Schema updates
?>
	<br /><br />

	<h1><?php echo $lang['UPDATE_DATABASE_SCHEMA']; ?></h1>

	<br />
	<p><?php echo $lang['PROGRESS']; ?> :: <strong>

<?php

flush();

// We go through the schema changes from the lowest to the highest version
// We try to also include versions 'in-between'...
$no_updates = true;
$versions = array_keys($database_update_info);
for ($i = 0; $i < sizeof($versions); $i++)
{
	$version = $versions[$i];
	$schema_changes = $database_update_info[$version];

	$next_version = (isset($versions[$i + 1])) ? $versions[$i + 1] : $updates_to_version;

	// If the installed version to be updated to is < than the current version, and if the current version is >= as the version to be updated to next, we will skip the process
	//
	if (version_compare($version, $current_version, '<') && version_compare($current_version, $next_version, '>='))
	{
		continue;
	}

	if (!sizeof($schema_changes))
	{
		continue;
	}

	$no_updates = false;

	$mod_db->perform_schema_changes($schema_changes);
}

_write_result($no_updates, $errored, $error_ary);

// Data updates
$error_ary = array();
$errored = $no_updates = false;

?>

<br /><br />
<h1><?php echo $lang['UPDATING_DATA']; ?></h1>
<br />
<p><?php echo $lang['PROGRESS']; ?> :: <strong>

<?php

flush();

$no_updates = true;
$versions = array_keys($database_update_info);

// some code magic
for ($i = 0; $i < sizeof($versions); $i++)
{
	$version = $versions[$i];
	$next_version = (isset($versions[$i + 1])) ? $versions[$i + 1] : $updates_to_version;

	// If the installed version to be updated to is < than the current version, and if the current version is >= as the version to be updated to next, we will skip the process
	if (version_compare($version, $current_version, '<') && version_compare($current_version, $next_version, '>='))
	{
		continue;
	}


	change_database_data($no_updates, $version);
}

_write_result($no_updates, $errored, $error_ary);

$error_ary = array();
$errored = $no_updates = false;

?>

<br /><br />
<h1><?php echo $lang['UPDATE_VERSION_OPTIMIZE']; ?></h1>
<br />
<p><?php echo $lang['PROGRESS']; ?> :: <strong>

<?php

flush();

// update the version
$sql = "UPDATE " . GARAGE_CONFIG_TABLE . "
	SET config_value = '$updates_to_version'
	WHERE config_name = 'version'";
_sql($sql, $errored, $error_ary);

// Reset permissions
$sql = 'UPDATE ' . USERS_TABLE . "
	SET user_permissions = '',
		user_perm_from = 0";
_sql($sql, $errored, $error_ary);

/* Optimize/vacuum analyze the tables where appropriate 
// this should be done for each version in future along with 
// the version number update
switch ($db->sql_layer)
{
	case 'mysql':
	case 'mysqli':
	case 'mysql4':
		$sql = 'OPTIMIZE TABLE ' . $table_prefix . 'auth_access, ' . $table_prefix . 'banlist, ' . $table_prefix . 'categories, ' . $table_prefix . 'config, ' . $table_prefix . 'disallow, ' . $table_prefix . 'forum_prune, ' . $table_prefix . 'forums, ' . $table_prefix . 'groups, ' . $table_prefix . 'posts, ' . $table_prefix . 'posts_text, ' . $table_prefix . 'privmsgs, ' . $table_prefix . 'privmsgs_text, ' . $table_prefix . 'ranks, ' . $table_prefix . 'search_results, ' . $table_prefix . 'search_wordlist, ' . $table_prefix . 'search_wordmatch, ' . $table_prefix . 'sessions_keys' . $table_prefix . 'smilies, ' . $table_prefix . 'themes, ' . $table_prefix . 'themes_name, ' . $table_prefix . 'topics, ' . $table_prefix . 'topics_watch, ' . $table_prefix . 'user_group, ' . $table_prefix . 'users, ' . $table_prefix . 'vote_desc, ' . $table_prefix . 'vote_results, ' . $table_prefix . 'vote_voters, ' . $table_prefix . 'words';
		_sql($sql, $errored, $error_ary);
	break;

	case 'postgresql':
		_sql("VACUUM ANALYZE", $errored, $error_ary);
	break;
}
*/

_write_result($no_updates, $errored, $error_ary);

?>

<br />
<h1><?php echo $lang['UPDATE_COMPLETED']; ?></h1>

<br />

<?php

if (!$inline_update)
{
	// Purge the cache...
	$cache->purge();
?>

	<p style="color:red"><?php echo $lang['UPDATE_FILES_NOTICE']; ?></p>

	<p><?php echo $lang['COMPLETE_LOGIN_TO_BOARD']; ?></p>

<?php
}
else
{
?>

	<p><?php echo ((isset($lang['INLINE_UPDATE_SUCCESSFUL'])) ? $lang['INLINE_UPDATE_SUCCESSFUL'] : 'The database update was successful. Now you need to continue the update process.'); ?></p>

	<p><a href="<?php echo append_sid("{$phpbb_root_path}garage/install/index.{$phpEx}", "mode=update&amp;sub=file_check&amp;lang=$language"); ?>" class="button1"><?php echo (isset($lang['CONTINUE_UPDATE_NOW'])) ? $lang['CONTINUE_UPDATE_NOW'] : 'Continue the update process now'; ?></a></p>

<?php
}

// Add database update to log
add_log('admin', 'LOG_GARAGE_UPDATE_DATABASE', $orig_version, $updates_to_version);

// Now we purge the session table as well as all cache files
$cache->purge();

?>

					</div>
				</div>
			<span class="corners-bottom"><span></span></span>
		</div>
		</div>
	</div>
	
	<div id="page-footer">
		Powered by phpBB &copy; 2000, 2002, 2005, 2007 <a href="http://www.phpbb.com/">phpBB Group</a>
	</div>
</div>

</body>
</html>

<?php

garbage_collection();

if (function_exists('exit_handler'))
{
	exit_handler();
}

/**
* Function where all data changes are executed
*/
function change_database_data(&$no_updates, $version)
{
	global $db, $map_dbms, $errored, $error_ary, $config, $phpbb_root_path;

	switch ($version)
	{
		case '2.0.B3':
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_blogs_smilies', '1')", $errored, $error_ary);
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_blogs_url', '1')", $errored, $error_ary);
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_make_approval', '1')", $errored, $error_ary);
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_model_approval', '1')", $errored, $error_ary);
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_guestbooks_url', '1')", $errored, $error_ary);
			_sql('INSERT INTO ' . GARAGE_CONFIG_TABLE . " (config_name, config_value) VALUES ('enable_guestbooks_smilies', '1')", $errored, $error_ary);
			$no_updates = false;
		break;
	}
}
/**
* Function for triggering an sql statement
*/
function _sql($sql, &$errored, &$error_ary, $echo_dot = true)
{
	global $db;

	if (defined('DEBUG_EXTRA'))
	{
		echo "<br />\n{$sql}\n<br />";
	}

	$db->sql_return_on_error(true);

	$result = $db->sql_query($sql);
	if ($db->sql_error_triggered)
	{
		$errored = true;
		$error_ary['sql'][] = $db->sql_error_sql;
		$error_ary['error_code'][] = $db->_sql_error();
	}

	$db->sql_return_on_error(false);

	if ($echo_dot)
	{
		echo ". \n";
		flush();
	}

	return $result;
}

function _write_result($no_updates, $errored, $error_ary)
{
	global $lang;

	if ($no_updates)
	{
		echo ' ' . $lang['NO_UPDATES_REQUIRED'] . '</strong></p>';
	}
	else
	{
		echo ' <span class="success">' . $lang['DONE'] . '</span></strong><br />' . $lang['RESULT'] . ' :: ';

		if ($errored)
		{
			echo ' <strong>' . $lang['SOME_QUERIES_FAILED'] . '</strong> <ul>';

			for ($i = 0; $i < sizeof($error_ary['sql']); $i++)
			{
				echo '<li>' . $lang['ERROR'] . ' :: <strong>' . htmlspecialchars($error_ary['error_code'][$i]['message']) . '</strong><br />';
				echo $lang['SQL'] . ' :: <strong>' . htmlspecialchars($error_ary['sql'][$i]) . '</strong><br /><br /></li>';
			}

			echo '</ul> <br /><br />' . $lang['SQL_FAILURE_EXPLAIN'] . '</p>';
		}
		else
		{
			echo '<strong>' . $lang['NO_ERRORS'] . '</strong></p>';
		}
	}
}

?>
