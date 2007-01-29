<?php

/***************************************************************************
 *                      includes/acp/info/acp_garage_setting.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

class acp_garage_setting_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_setting',
			'title'		=> 'ACP_GARAGE_SETTINGS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'general'	=> array('title' => 'ACP_GARAGE_GENERAL_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'menu'		=> array('title' => 'ACP_GARAGE_MENU_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'index'		=> array('title' => 'ACP_GARAGE_INDEX_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'images'	=> array('title' => 'ACP_GARAGE_IMAGES_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'quartermile'	=> array('title' => 'ACP_GARAGE_QUARTERMILE_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'dynorun'	=> array('title' => 'ACP_GARAGE_DYNORUN_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'track'		=> array('title' => 'ACP_GARAGE_TRACK_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'insurance'	=> array('title' => 'ACP_GARAGE_INSURANCE_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'business'	=> array('title' => 'ACP_GARAGE_BUSINESS_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'rating'	=> array('title' => 'ACP_GARAGE_RATING_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'guestbook'	=> array('title' => 'ACP_GARAGE_GUESTBOOK_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'product'	=> array('title' => 'ACP_GARAGE_PRODUCT_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'service'	=> array('title' => 'ACP_GARAGE_SERVICE_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
				'blog'		=> array('title' => 'ACP_GARAGE_BLOG_SETTINGS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
