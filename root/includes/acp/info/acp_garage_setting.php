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
class acp_garage_setting_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_setting',
			'title'		=> 'ACP_GARAGE_SETTINGS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'general'	=> array('title' => 'ACP_GARAGE_GENERAL_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'menu'		=> array('title' => 'ACP_GARAGE_MENU_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'index'		=> array('title' => 'ACP_GARAGE_INDEX_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'images'	=> array('title' => 'ACP_GARAGE_IMAGES_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'quartermile'	=> array('title' => 'ACP_GARAGE_QUARTERMILE_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'dynorun'	=> array('title' => 'ACP_GARAGE_DYNORUN_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'track'		=> array('title' => 'ACP_GARAGE_TRACK_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'insurance'	=> array('title' => 'ACP_GARAGE_INSURANCE_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'business'	=> array('title' => 'ACP_GARAGE_BUSINESS_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'rating'	=> array('title' => 'ACP_GARAGE_RATING_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'guestbook'	=> array('title' => 'ACP_GARAGE_GUESTBOOK_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'product'	=> array('title' => 'ACP_GARAGE_PRODUCT_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'service'	=> array('title' => 'ACP_GARAGE_SERVICE_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
				'blog'		=> array('title' => 'ACP_GARAGE_BLOG_SETTINGS', 'auth' => 'acl_a_garage_setting', 'cat' => array('ACP_GARAGE_SETTINGS')),
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
