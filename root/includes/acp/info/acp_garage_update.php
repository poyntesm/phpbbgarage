<?php
/**
*
* @package acp
* @version $Id: acp_update.php,v 1.3 2007/10/04 15:05:50 acydburn Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_garage_update_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_update',
			'title'		=> 'ACP_GARAGE_UPDATE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'version_check'		=> array('title' => 'ACP_GARAGE_VERSION_CHECK', 'auth' => 'acl_a_garage_update', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
