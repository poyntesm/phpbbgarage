<?php
/** 
*
* @package ucp
* @version $Id: ucp_garage.php,v 1.2 2006/05/01 19:45:42 grahamje Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class ucp_garage_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_garage',
			'title'		=> 'UCP_GARAGE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'options'	=> array('title' => 'UCP_GARAGE_OPTIONS', 'auth' => 'acl_u_garage_browse', 'cat' => array('UCP_GARAGE')),
				'moderation'	=> array('title' => 'UCP_GARAGE_MODERATION', 'auth' => 'acl_m_garage', 'cat' => array('UCP_GARAGE')),
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
