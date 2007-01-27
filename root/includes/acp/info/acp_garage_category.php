<?php

/***************************************************************************
 *                      includes/acp/info/acp_garage_category.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: admin_garage_business.php 124 2006-05-13 14:57:36Z poyntesm $
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

class acp_garage_category_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_category',
			'title'		=> 'ACP_GARAGE_CATEGORY_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'categories'	=> array('title' => 'ACP_GARAGE_CATEGORIES', 	'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
