<?php
/**
*
* @package acp
* @version $Id: acp_update.php,v 1.9 2007/11/19 17:00:13 acydburn Exp $
* @copyright (c) 2005 phpBB Group
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_garage_update
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $garage_config;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang(array('acp/garage', 'mods/garage_install'));

		$this->tpl_name = 'acp_update';
		$this->page_title = 'ACP_GARAGE_VERSION_CHECK';

		// Get current and latest version
		$errstr = '';
		$errno = 0;

		$info = get_remote_file('www.phpbbgarage.com', '/updatecheck', '30x.txt', $errstr, $errno);

		if ($info === false)
		{
			trigger_error($errstr, E_USER_WARNING);
		}

		$info = explode("\n", $info);
		$latest_version = trim($info[0]);

		$announcement_url = trim($info[1]);
		$update_link = append_sid($phpbb_root_path . 'garage/install/index.' . $phpEx, 'mode=update');

		// Determine automatic update...
		$sql = 'SELECT config_value
			FROM ' . GARAGE_CONFIG_TABLE . "
			WHERE config_name = 'version_update_from'";
		$result = $db->sql_query($sql);
		$version_update_from = (string) $db->sql_fetchfield('config_value');
		$db->sql_freeresult($result);

		$current_version = (!empty($version_update_from)) ? $version_update_from : $garage_config['version'];

		$up_to_date_automatic = (version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;
		$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($garage_config['version'])), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;

		$template->assign_vars(array(
			'S_UP_TO_DATE'		=> $up_to_date,
			'S_UP_TO_DATE_AUTO'	=> $up_to_date_automatic,
			'S_VERSION_CHECK'	=> true,
			'U_ACTION'		=> $this->u_action,

			'LATEST_VERSION'	=> $latest_version,
			'CURRENT_VERSION'	=> $garage_config['version'],
			'AUTO_VERSION'		=> $version_update_from,

			'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['UPDATE_INSTRUCTIONS'], $announcement_url, $update_link),
		));
	}
}

?>
