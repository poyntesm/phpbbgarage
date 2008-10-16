<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class acp_garage_quota_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_quota',
			'title'		=> 'ACP_GARAGE_QUOTA_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'quotas'	=> array('title' => 'ACP_GARAGE_QUOTAS', 'auth' => 'acl_a_garage_quota', 'cat' => array('ACP_GARAGE_MANAGEMENT')),
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
