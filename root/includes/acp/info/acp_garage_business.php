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
class acp_garage_business_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_business',
			'title'		=> 'ACP_GARAGE_BUSINESS_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'business'	=> array('title' => 'ACP_GARAGE_BUSINESS', 'auth' => 'acl_a_garage_business', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
