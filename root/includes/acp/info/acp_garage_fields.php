<?php
/** 
*
* @package acp
* @version $Id: $
* @copyright (c) 2006 phpBB Garage
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
				'fields'	=> array('title' => 'ACP_GARAGE_FIELDS', 'auth' => 'acl_a_garage_field', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
