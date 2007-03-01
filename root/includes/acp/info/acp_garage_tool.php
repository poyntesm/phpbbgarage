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
class acp_garage_tool_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_tool',
			'title'		=> 'ACP_GARAGE_TOOL_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'tools'	=> array('title' => 'ACP_GARAGE_TOOLS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
