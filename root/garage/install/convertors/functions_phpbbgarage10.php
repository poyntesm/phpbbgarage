<?php
/** 
*
* @package install
* @version $Id: functions_phpbbgarage10.php,v 1.56 2007/07/27 17:33:15 acydburn Exp $
* @copyright (c) 2006 phpBB Group
* @copyright (c) 2008 Esmond Poynton
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* Helper functions for phpBB Garage 1.0.5/6 to phpBB 2.0.x conversion
 */

/**
* Get old config value
*/
function get_phpbb_config_value($config_name)
{
	static $convert_phpbb_config;

	if (!isset($convert_phpbb_config))
	{
		$convert_phpbb_config = get_phpbb_config();
	}

	if (!isset($convert_phpbb_config[$config_name]))
	{
		return false;
	}

	return (empty($convert_phpbb_config[$config_name])) ? '' : $convert_phpbb_config[$config_name];
}


/**
* Function for recoding text with the default language
*
* @param string $text text to recode to utf8
* @param bool $grab_user_lang if set to true the function tries to use $convert_row['user_lang'] (and falls back to $convert_row['poster_id']) instead of the boards default language
*/
function phpbb_set_encoding($text, $grab_user_lang = true)
{
	global $lang_enc_array, $convert_row;
	global $convert, $phpEx;

	/*static $lang_enc_array = array(
		'korean'						=> 'euc-kr',
		'serbian'						=> 'windows-1250',
		'polish'						=> 'iso-8859-2',
		'kurdish'						=> 'windows-1254',
		'slovak'						=> 'Windows-1250',
		'russian'						=> 'windows-1251',
		'estonian'						=> 'iso-8859-4',
		'chinese_simplified'			=> 'gb2312',
		'macedonian'					=> 'windows-1251',
		'azerbaijani'					=> 'UTF-8',
		'romanian'						=> 'iso-8859-2',
		'romanian_diacritice'			=> 'iso-8859-2',
		'lithuanian'					=> 'windows-1257',
		'turkish'						=> 'iso-8859-9',
		'ukrainian'						=> 'windows-1251',
		'japanese'						=> 'shift_jis',
		'hungarian'						=> 'ISO-8859-2',
		'romanian_no_diacritics'		=> 'iso-8859-2',
		'mongolian'						=> 'UTF-8',
		'slovenian'						=> 'windows-1250',
		'bosnian'						=> 'windows-1250',
		'czech'							=> 'Windows-1250',
		'farsi'							=> 'Windows-1256',
		'croatian'						=> 'windows-1250',
		'greek'							=> 'iso-8859-7',
		'russian_tu'					=> 'windows-1251',
		'sakha'							=> 'UTF-8',
		'serbian_cyrillic'				=> 'windows-1251',
		'bulgarian'						=> 'windows-1251',
		'chinese_traditional_taiwan'	=> 'big5',
		'chinese_traditional'			=> 'big5',
		'arabic'						=> 'windows-1256',
		'hebrew'						=> 'WINDOWS-1255',
		'thai'							=> 'windows-874',
		//'chinese_traditional_taiwan'	=> 'utf-8' // custom modified, we may have to do an include :-(
	);*/

	if (empty($lang_enc_array))
	{
		$lang_enc_array = array();
	}

	$get_lang = trim(get_phpbb_config_value('default_lang'));

	// Do we need the users language encoding?
	if ($grab_user_lang && !empty($convert_row))
	{
		if (!empty($convert_row['user_lang']))
		{
			$get_lang = trim($convert_row['user_lang']);
		}
		else if (!empty($convert_row['poster_id']))
		{
			global $src_db, $same_db;

			if ($convert->mysql_convert && $same_db)
			{
				$src_db->sql_query("SET NAMES 'binary'");
			}

			$sql = 'SELECT user_lang
				FROM ' . $convert->src_table_prefix . 'users
				WHERE user_id = ' . (int) $convert_row['poster_id'];
			$result = $src_db->sql_query($sql);
			$get_lang = (string) $src_db->sql_fetchfield('user_lang');
			$src_db->sql_freeresult($result);

			if ($convert->mysql_convert && $same_db)
			{
				$src_db->sql_query("SET NAMES 'utf8'");
			}

			$get_lang = (!trim($get_lang)) ? trim(get_phpbb_config_value('default_lang')) : trim($get_lang);
		}
	}

	if (!isset($lang_enc_array[$get_lang]))
	{
		$filename = $convert->options['forum_path'] . '/language/lang_' . $get_lang . '/lang_main.' . $phpEx;

		if (!file_exists($filename))
		{
			$get_lang = trim(get_phpbb_config_value('default_lang'));
		}

		if (!isset($lang_enc_array[$get_lang]))
		{
			include($convert->options['forum_path'] . '/language/lang_' . $get_lang . '/lang_main.' . $phpEx);
			$lang_enc_array[$get_lang] = $lang['ENCODING'];
			unset($lang);
		}
	}

	$encoding = $lang_enc_array[$get_lang];

	return utf8_recode($text, $lang_enc_array[$get_lang]);
}

/**
* Same as phpbb_set_encoding, but forcing boards default language
*/
function phpbb_set_default_encoding($text)
{
	return phpbb_set_encoding($text, false);
}

/**
* Convert authentication
* Will only bring across private if group names match from source to destination
*/
function phpbbgarage_convert_authentication_quota()
{
	global $db, $src_db, $same_db, $convert, $user, $config, $cache;

	//Get Source Permissions
	$sql = "SELECT *
		FROM {$convert->src_table_prefix}garage_config
		WHERE config_name = 'browse_perms'
			OR config_name = 'interact_perms'
			OR config_name = 'add_perms'
			OR config_name = 'upload_perms'
			OR config_name = 'private_browse_perms'
			OR config_name = 'private_interact_perms'
			OR config_name = 'private_add_perms'
			OR config_name = 'private_upload_perms'
			OR config_name = 'private_deny_perms'
			OR config_name = 'private_add_quota'
			OR config_name = 'private_upload_quota'
			OR config_name = 'private_remote_quota'";
	$result = $src_db->sql_query($sql);

	$forum_access = array();
	while ($row = $src_db->sql_fetchrow($result))
	{
		$src_permissions[$row['config_name']] = $row['config_value'];
	}
	$src_db->sql_freeresult($result);

	//Do Mapping Work To 
	/*	'add_groups'				=>
		'add_groups_quotas'			=>
		'upload_groups'				=>
		'upload_groups_quotas'			=>
		'remote_groups'				=>
		'remote_groups_quotas'			=> */


}

/**
* Convert authentication
* Will only bring across private if group names match from source to destination
*/
function phpbb_replace_size($matches)
{
	return '[size=' . min(200, ceil(100.0 * (((double) $matches[1])/12.0))) . ':' . $matches[2] . ']';
}

/**
* Reparse the message stripping out the bbcode_uid values and adding new ones and setting the bitfield
* @todo What do we want to do about HTML in messages - currently it gets converted to the entities, but there may be some objections to this
*/
function phpbb_guestbook_prepare_message($message)
{
	global $phpbb_root_path, $phpEx, $db, $convert, $user, $config, $cache, $convert_row, $message_parser;

	if (!$message)
	{
		$convert->row['mp_bbcode_bitfield'] = $convert_row['mp_bbcode_bitfield'] = 0;
		return '';
	}

	// Decode phpBB 2.0.x Message
	if (isset($convert->row['old_bbcode_uid']) && $convert->row['old_bbcode_uid'] != '')
	{
		// Adjust size...
		if (strpos($message, '[size=') !== false)
		{
			$message = preg_replace_callback('/\[size=(\d*):(' . $convert->row['old_bbcode_uid'] . ')\]/', 'phpbb_replace_size', $message);
		}

		$message = preg_replace('/\:(([a-z0-9]:)?)' . $convert->row['old_bbcode_uid'] . '/s', '', $message);
	}

	if (strpos($message, '[quote=') !== false)
	{
		$message = preg_replace('/\[quote="(.*?)"\]/s', '[quote=&quot;\1&quot;]', $message);
		$message = preg_replace('/\[quote=\\\"(.*?)\\\"\]/s', '[quote=&quot;\1&quot;]', $message);
		
		// let's hope that this solves more problems than it causes. Deal with escaped quotes.
		$message = str_replace('\"', '&quot;', $message);
		$message = str_replace('\&quot;', '&quot;', $message);
	}

	// Already the new user id ;)
	$user_id = $convert->row['author_id'];

	$message = str_replace('<', '&lt;', $message);
	$message = str_replace('>', '&gt;', $message);
	$message = str_replace('<br />', "\n", $message);

	// make the post UTF-8
	$message = phpbb_set_encoding($message);

	$message_parser->warn_msg = array(); // Reset the errors from the previous message
	$message_parser->bbcode_uid = make_uid($convert->row['post_date']);
	$message_parser->message = $message;
	unset($message);

	// Make sure options are set.
//	$enable_html = (!isset($row['enable_html'])) ? false : $row['enable_html'];
	$enable_bbcode = (!isset($convert->row['enable_bbcode'])) ? true : $convert->row['enable_bbcode'];
	$enable_smilies = (!isset($convert->row['enable_smilies'])) ? true : $convert->row['enable_smilies'];
	$enable_magic_url = (!isset($convert->row['enable_magic_url'])) ? true : $convert->row['enable_magic_url'];

	// parse($allow_bbcode, $allow_magic_url, $allow_smilies, $allow_img_bbcode = true, $allow_flash_bbcode = true, $allow_quote_bbcode = true, $allow_url_bbcode = true, $update_this_message = true, $mode = 'post')
	$message_parser->parse($enable_bbcode, $enable_magic_url, $enable_smilies);
	
	if (sizeof($message_parser->warn_msg))
	{
		$msg_id = isset($convert->row['post_id']) ? $convert->row['post_id'] : $convert->row['privmsgs_id'];
		$convert->p_master->error('<span style="color:red">' . $user->lang['POST_ID'] . ': ' . $msg_id . ' ' . $user->lang['CONV_ERROR_MESSAGE_PARSER'] . ': <br /><br />' . implode('<br />', $message_parser->warn_msg), __LINE__, __FILE__, true);
	}

	$convert->row['mp_bbcode_bitfield'] = $convert_row['mp_bbcode_bitfield'] = $message_parser->bbcode_bitfield;

	$message = $message_parser->message;
	unset($message_parser->message);

	return $message;
}

/**
* Return the bitfield calculated by the previous function
*/
function get_bbcode_bitfield()
{
	global $convert_row;

	return (empty($convert_row['mp_bbcode_bitfield'])) ? '' : $convert_row['mp_bbcode_bitfield'];
}

/**
* Return the vehicle weighted rating
*/
function get_weighted_rating()
{
	global $src_db, $same_db, $convert, $user, $config, $cache;

	return "0.0";

	$sql = $src_db->sql_build_query('SELECT', 
		array(
		'SELECT'	=> 'COUNT(g.id) AS votes_recieved, AVG(rating) AS average_rating',
		'FROM'		=> array(
			GARAGE_RATINGS_TABLE	=> 'g',
		),
		'WHERE'		=> "g.id = $vid"
	));

	$result = $src_db->sql_query($sql);
        $row = $src_db->sql_fetchrow($result);
	$src_db->sql_freeresult($result);

	$sql = $src_db->sql_build_query('SELECT', 
		array(
		'SELECT'	=> 'AVG(rating) AS site_average',
		'FROM'		=> array(
			GARAGE_RATINGS_TABLE	=> 'g',
		)
	));

	$result = $src_db->sql_query($sql);
        $row1 = $src_db->sql_fetchrow($result);
	$src_db->sql_freeresult($result);

	//Weighted Rating Formula We Use 'WR=(V/(V+M)) * R + (M/(V+M)) * C'
	// WR=Weighted Rating (The new rating)
	// R=Average Rating (arithmetic mean) so far
	// V=Number of ratings given
	// M=Minimum number of ratings needed
	// C=Arithmetic mean rating across the whole site
	$weighted_rating = ( $row['votes_recieved'] / ($row['votes_recieved'] + $garage_config['minimum_ratings_required']) ) * $row['average_rating'] + ($garage_config['minimum_ratings_required']/($row['votes_recieved']+$garage_config['minimum_ratings_required'])) * $row1['site_average'];

	return $weighted_rating;
}

/**
* Obtain the path to uploaded files on the 2.0.x forum
* This is only used if the Attachment MOD was installed
*/
function phpbb_get_files_dir()
{
	if (!defined('MOD_ATTACHMENT'))
	{
		return;
	}

	global $src_db, $same_db, $convert, $user, $config, $cache;

	if ($convert->mysql_convert && $same_db)
	{
		$src_db->sql_query("SET NAMES 'binary'");
	}
	$sql = 'SELECT config_value AS upload_dir
		FROM ' . $convert->src_table_prefix . "attachments_config
		WHERE config_name = 'upload_dir'";
	$result = $src_db->sql_query($sql);
	$upload_path = $src_db->sql_fetchfield('upload_dir');
	$src_db->sql_freeresult($result);

	$sql = 'SELECT config_value AS ftp_upload
		FROM ' . $convert->src_table_prefix . "attachments_config
		WHERE config_name = 'allow_ftp_upload'";
	$result = $src_db->sql_query($sql);
	$ftp_upload = (int) $src_db->sql_fetchfield('ftp_upload');
	$src_db->sql_freeresult($result);

	if ($convert->mysql_convert && $same_db)
	{
		$src_db->sql_query("SET NAMES 'utf8'");
	}

	if ($ftp_upload)
	{
		$convert->p_master->error($user->lang['CONV_ERROR_ATTACH_FTP_DIR'], __LINE__, __FILE__);
	}

	return $upload_path;
}

/**
* Just undos the replacing of '<' and '>'
*/
function  phpbb_smilie_html_decode($code)
{
	$code = str_replace('&lt;', '<', $code);
	return str_replace('&gt;', '>', $code);
}

/*
*
* Retrieves configuration information from the source forum and caches it as an array
* Both database and file driven configuration formats can be handled
* (the type used is specified in $garage_config_schema, see convert_phpbb20.php for more details)
*/
function get_garage_config()
{
	static $convert_garage_config;
	global $user;

	if (isset($convert_garage_config))
	{
		return $convert_garage_config;
	}

	global $src_db, $same_db, $phpbb_root_path, $config;
	global $convert;

	if ($convert->garage_config_schema['table_format'] != 'file')
	{
		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'binary'");
		}

		$sql = 'SELECT * FROM ' . $convert->src_table_prefix . $convert->garage_config_schema['table_name'];
		$result = $src_db->sql_query($sql);
		$row = $src_db->sql_fetchrow($result);

		if (!$row)
		{
			$convert->p_master->error($user->lang['CONV_ERROR_GET_CONFIG'], __LINE__, __FILE__);
		}
	}

	if (is_array($convert->garage_config_schema['table_format']))
	{
		$convert_garage_config = array();
		list($key, $val) = each($convert->garage_config_schema['table_format']);

		do
		{
			$convert_garage_config[$row[$key]] = $row[$val];
		}
		while ($row = $src_db->sql_fetchrow($result));
		$src_db->sql_freeresult($result);

		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'utf8'");
		}
	}
	else if ($convert->garage_config_schema['table_format'] == 'file')
	{
		$filename = $convert->options['forum_path'] . '/' . $convert->garage_config_schema['filename'];
		if (!file_exists($filename))
		{
			$convert->p_master->error($user->lang['FILE_NOT_FOUND'] . ': ' . $filename, __LINE__, __FILE__);
		}

		$convert_garage_config = extract_variables_from_file($filename);
		if (!empty($convert->garage_config_schema['array_name']))
		{
			$convert_garage_config = $convert_garage_config[$convert->garage_config_schema['array_name']];
		}
	}
	else
	{
		$convert_garage_config = $row;
		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'utf8'");
		}
	}

	if (!sizeof($convert_garage_config))
	{
		$convert->p_master->error($user->lang['CONV_ERROR_CONFIG_EMPTY'], __LINE__, __FILE__);
	}

	return $convert_garage_config;
}

/**
* Transfers the relevant configuration information from the source forum
* The mapping of fields is specified in $garage_config_schema, see convert_phpbb20.php for more details
*/
function restore_garage_config($schema)
{
	global $db, $config, $phpbb_root_path, $phpEx;

	require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);

	$convert_garage_config = get_garage_config();
	foreach ($schema['settings'] as $config_name => $src)
	{
		if (preg_match('/(.*)\((.*)\)/', $src, $m))
		{
			$var = (empty($m[2]) || empty($convert_config[$m[2]])) ? "''" : "'" . addslashes($convert_config[$m[2]]) . "'";
			$exec = '$config_value = ' . $m[1] . '(' . $var . ');';
			eval($exec);
		}
		else
		{
			$config_value = (isset($convert_config[$src])) ? $convert_config[$src] : '';
		}

		if ($config_value !== '')
		{
			// Most are...
			if (is_string($config_value))
			{
				$config_value = utf8_htmlspecialchars($config_value);
			}

			$garage_admin->set_config($config_name, $config_value, '');
		}
	}
}

/**
* Get old config value
*/
function get_garage_config_value($config_name)
{
	static $convert_garage_config;

	if (!isset($convert_garage_config))
	{
		$convert_garage_config = get_garage_config();
	}
	
	if (!isset($convert_garage_config[$config_name]))
	{
		return false;
	}

	return (empty($convert_garage_config[$config_name])) ? '' : $convert_garage_config[$config_name];
}

function phpbbgarage_browse_menu()
{
	return 1;
}

function phpbbgarage_index_menu()
{
	return 1;
}

function phpbbgarage_search_menu()
{
	return 1;
}

function phpbbgarage_insurance_review_menu()
{
	return 1;
}

function phpbbgarage_garage_review_menu()
{
	return 1;
}

function phpbbgarage_shop_review_menu()
{
	return 1;
}

function phpbbgarage_quartermile_menu()
{
	return 1;
}

function phpbbgarage_dynorun_menu()
{
	return 1;
}


function phpbbgarage_featured_vehicle()
{
	return 1;
}

function phpbbgarage_feature_from_block ()
{
	return 0;
}


function import_garage_gallery()
{
	global $config, $convert, $phpbb_root_path, $user;

	$relative_path = empty($convert->convertor['source_path_absolute']);

	$convert->convertor['garage_image_path'] = './garage/upload/';
	$config['garage_image_path'] = './garage/upload/';

	$src_path = relative_base(path($convert->convertor['garage_image_path'], $relative_path), $relative_path);

	if (is_dir($src_path))
	{
		// Do not die on failure... safe mode restrictions may be in effect.
		copy_dir($convert->convertor['garage_image_path'], path($config['garage_image_path']), false, false, false, $relative_path);
	}
}

function category_order()
{
	global $db, $src_db, $same_db, $convert, $user, $config;

	$sql = "SELECT COUNT(*) as total
		FROM " . GARAGE_CATEGORIES_TABLE;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row['total'] + 1;
}

function is_business_retail($business_id)
{
	global $db, $src_db, $same_db, $convert, $user, $config;

	$sql = "SELECT web_shop, retail_shop
		FROM {$convert->src_table_prefix}garage_business
		WHERE id = '{$business_id}'";
	$result = $src_db->sql_query($sql);
	$row = $src_db->sql_fetchrow($result);
	$src_db->sql_freeresult($result);

	if (($row['web_shop']) OR ($row['retail_shop']))
	{
		return 1;
	}

	return 0;
}

function import_dynocentre($dynocentre)
{
	global $db, $src_db, $same_db, $convert, $user, $config;

	$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'b.title, b.id',
			'FROM'		=> array(
				GARAGE_BUSINESS_TABLE	=> 'b',
			),
			'WHERE'		=> "dynocentre = 1 AND title = '".$db->sql_escape($dynocentre)."'"
	));

	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	//No Business We Need To Create It
	if (empty($row))
	{
		$sql = 'INSERT INTO ' . GARAGE_BUSINESS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title'		=> utf8_htmlspecialchars(phpbb_set_encoding($dynocentre)),
			'address'	=> '',
			'telephone'	=> '',
			'fax'		=> '',
			'website'	=> '',
			'email'		=> '',
			'opening_hours'	=> '',
			'insurance'	=> 0,
			'garage'	=> 0,
			'retail'	=> 0,
			'product'	=> 0,
			'dynocentre'	=> 1,
			'pending'	=> 0,
		));

		$db->sql_query($sql);
		return $db->sql_nextid();
	}
	//Dynocentre Exists Already Just Return ID
	else
	{
		return $row['id'];
	}
}

function change_anonymous($user_id)
{
	if($user_id == -1 or $user_id < -1)
	{
		return ANONYMOUS;
	}
	return $user_id;
}

function import_cover_type($cover_type)
{
	global $user;

	if ($cover_type == $user->lang['COMPREHENSIVE'] || $cover_type == 'Comprehensive') 
	{
		return COMP;
	}
	else if ($cover_type == $user->lang['COMPREHENSIVE_CLASSIC'] || $cover_type == 'Comprehensive - Classic') 
	{
		return CLAS;
	}
	else if ($cover_type == $user->lang['COMPREHENSIVE_REDUCED'] || $cover_type == 'Comprehensive - Reduced Mileage') 
	{
		return COMP_RED;
	}
	else if ($cover_type == $user->lang['THIRD_PARTY'] || $cover_type == 'Third Party') 
	{
		return TP;
	}
	else if ($cover_type == $user->lang['THIRD_PARTY_FIRE_THEFT'] || $cover_type == 'Thired Party, Fire & Theft') 
	{
		return TPFT;
	}

	return 0;
}

/**
* Retrieves configuration information from the source forum and caches it as an array
* Both database and file driven configuration formats can be handled
* (the type used is specified in $forum_config_schema, see convert_phpbb20.php for more details)
*/
function get_phpbb_config()
{
	static $convert_phpbb_config;
	global $user;

	if (isset($convert_phpbb_config))
	{
		return $convert_phpbb_config;
	}

	global $src_db, $same_db, $phpbb_root_path, $config;
	global $convert;

	if ($convert->forum_config_schema['table_format'] != 'file')
	{
		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'binary'");
		}

		$sql = 'SELECT * FROM ' . $convert->src_table_prefix . $convert->forum_config_schema['table_name'];
		$result = $src_db->sql_query($sql);
		$row = $src_db->sql_fetchrow($result);

		if (!$row)
		{
			$convert->p_master->error($user->lang['CONV_ERROR_GET_CONFIG'], __LINE__, __FILE__);
		}
	}

	if (is_array($convert->forum_config_schema['table_format']))
	{
		$convert_phpbb_config = array();
		list($key, $val) = each($convert->forum_config_schema['table_format']);

		do
		{
			$convert_phpbb_config[$row[$key]] = $row[$val];
		}
		while ($row = $src_db->sql_fetchrow($result));
		$src_db->sql_freeresult($result);

		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'utf8'");
		}
	}
	else if ($convert->forum_config_schema['table_format'] == 'file')
	{
		$filename = $convert->options['forum_path'] . '/' . $convert->forum_config_schema['filename'];
		if (!file_exists($filename))
		{
			$convert->p_master->error($user->lang['FILE_NOT_FOUND'] . ': ' . $filename, __LINE__, __FILE__);
		}

		$convert_phpbb_config = extract_variables_from_file($filename);
		if (!empty($convert->forum_config_schema['array_name']))
		{
			$convert_phpbb_config = $convert_phpbb_config[$convert->forum_config_schema['array_name']];
		}
	}
	else
	{
		$convert_phpbb_config = $row;
		if ($convert->mysql_convert && $same_db)
		{
			$src_db->sql_query("SET NAMES 'utf8'");
		}
	}

	if (!sizeof($convert_phpbb_config))
	{
		$convert->p_master->error($user->lang['CONV_ERROR_CONFIG_EMPTY'], __LINE__, __FILE__);
	}

	return $convert_phpbb_config;
}

function get_placeholder_manufacturer_id()
{

	global $db, $src_db, $same_db, $convert, $user, $config;

	$sql = "SELECT title, id
		FROM " . GARAGE_BUSINESS_TABLE . "
		WHERE product = 1
			AND title = 'Converted Modifications'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (empty($row['id']))
	{
		$sql = 'INSERT INTO ' . GARAGE_BUSINESS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title'		=> 'Converted Modifications',
			'address'	=> '',
			'telephone'	=> '',
			'fax'		=> '',
			'website'	=> '',
			'email'		=> '',
			'opening_hours'	=> '',
			'insurance'	=> 0,
			'garage'	=> 0,
			'retail'	=> 0,
			'product'	=> 1,
			'dynocentre'	=> 0,
			'pending'	=> 0,
		));

		$db->sql_query($sql);
		return $db->sql_nextid();
	}
	else
	{
		return $row['id'];
	}
}

function attach_thumb_filesize($image_id)
{

	global $config, $convert, $phpbb_root_path, $user;

	$relative_path = empty($convert->convertor['source_path_absolute']);
	$convert->convertor['garage_image_path'] = './garage/upload/';
	$src_path = relative_base(path($convert->convertor['garage_image_path'], $relative_path), $relative_path);
	return @filesize($src_path . $row['attach_thumb_location']);
}

function insert_modification_product($src_modification_id)
{
	global $db, $src_db, $same_db, $convert, $user, $config;

	//Get source modification data
	$sql = 'SELECT *
		FROM ' . $convert->src_table_prefix . 'garage_mods
		WHERE id = '.$src_modification_id;
	$result = $src_db->sql_query($sql);
	$row = $src_db->sql_fetchrow($result);
	$src_db->sql_freeresult($result);

	//Get Modification Business
	$business_id = get_placeholder_manufacturer_id();

	//Check for duplicate modification product
	$sql = "SELECT title, id
		FROM " . GARAGE_PRODUCTS_TABLE . "
		WHERE title = '" . str_replace("'", "\'", $row['title'])."'";
	$result = $db->sql_query($sql);
	$prow = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);


	if (empty($prow['id']))
	{
		$sql = 'INSERT INTO ' . GARAGE_PRODUCTS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title'		=> utf8_htmlspecialchars(phpbb_set_encoding($row['title'])),
			'business_id'	=> $business_id,
			'category_id'	=> $row['category_id'],
			'pending'	=> 0,
		));

		$db->sql_query($sql);
		return $db->sql_nextid();
	}
	else
	{
		return $prow['id'];
	}
}

function attach_thumb_width($attach_thumb_location)
{
	$relative_path = empty($convert->convertor['source_path_absolute']);
	$convert->convertor['garage_image_path'] = './garage/upload/';
	$src_path = relative_base(path($convert->convertor['garage_image_path'], $relative_path), $relative_path);
	if (file_exists($src_path . $attach_thumb_location))
	{
		$attach_thumb_imagesize = @getimagesize($src_path . $attach_thumb_location);
		return $attach_thumb_imagesize[0];
	}
	return 0;
}

function attach_thumb_height($attach_thumb_location)
{
	$relative_path = empty($convert->convertor['source_path_absolute']);
	$convert->convertor['garage_image_path'] = './garage/upload/';
	$src_path = relative_base(path($convert->convertor['garage_image_path'], $relative_path), $relative_path);
	if (file_exists($src_path . $attach_thumb_location))
	{
		$attach_thumb_imagesize = @getimagesize($src_path . $attach_thumb_location);
		return $attach_thumb_imagesize[1];
	}
	return 0;
}

function determine_image_vehicle_id($attach_id)
{
	global $db, $src_db, $same_db, $convert, $user, $config;

	//Workout If Image Is Attached To Vehicle Gallery
	$sql = "SELECT gallery.garage_id
		FROM " . $convert->src_table_prefix . "garage_gallery gallery
        		LEFT JOIN " . $convert->src_table_prefix . "garage_images images ON images.attach_id = gallery.image_id 
		WHERE images.attach_id = " . $attach_id;
	if ( !($result = $src_db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql);
	}
	$row = $src_db->sql_fetchrow($result);
	if (!empty($row['garage_id']))
	{
		return $row['garage_id'];
	}

	//Workout If Image Is Attached To Vehicle Modification
	$sql = "SELECT mods.garage_id
		FROM " . $convert->src_table_prefix . "garage_mods mods
        		LEFT JOIN " . $convert->src_table_prefix . "garage_images images ON images.attach_id = mods.image_id 
        	WHERE images.attach_id = " . $attach_id;
	if ( !($result = $src_db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql);
	}
	$row = $src_db->sql_fetchrow($result);
	if (!empty($row['garage_id']))
	{
		return $row['garage_id'];
	}

	//Workout If Image Is Attached To Vehicle Quartermile
	$sql = "SELECT qm.garage_id
		FROM " . $convert->src_table_prefix . "garage_quartermile qm
        		LEFT JOIN " . $convert->src_table_prefix . "garage_images images ON images.attach_id = qm.image_id 
		WHERE images.attach_id = " . $attach_id;
	if ( !($result = $src_db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql);
	}
	$row = $src_db->sql_fetchrow($result);
	if (!empty($row['garage_id']))
	{
		return $row['garage_id'];
	}

	//Workout If Image Is Attached To Vehicle Dynorun
	$sql = "SELECT rr.garage_id
		FROM " . $convert->src_table_prefix . "garage_rollingroad rr
        		LEFT JOIN " . $convert->src_table_prefix . "garage_images images ON images.attach_id = rr.image_id 
        	WHERE images.attach_id = " . $attach_id;
	if ( !($result = $src_db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could Select Image Data', '', __LINE__, __FILE__, $sql);
	}
	$row = $src_db->sql_fetchrow($result);
	if (!empty($row['garage_id']))
	{
		return $row['garage_id'];
	}
}

?>
