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
class acp_garage_category_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_category',
			'title'		=> 'ACP_GARAGE_CATEGORY_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'categories'	=> array('title' => 'ACP_GARAGE_CATEGORIES', 'auth' => 'acl_a_garage_category', 'cat' => array('ACP_GARAGE_MANAGEMENT')),
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
