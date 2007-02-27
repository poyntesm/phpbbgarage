<?php
/** 
*
* @package acp
* @version $Id: acp_garage_fields.php,v 1.2 2006/05/01 19:45:42 grahamje Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class acp_garage_fields_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_fields',
			'title'		=> 'ACP_GARAGE_FIELDS_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'fields'	=> array('title' => 'ACP_GARAGE_FIELDS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
