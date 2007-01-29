<?php

/***************************************************************************
 *                      includes/acp/info/acp_garage_product.php
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

class acp_garage_product_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_product',
			'title'		=> 'ACP_GARAGE_PRODUCT_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'products'	=> array('title' => 'ACP_GARAGE_PRODUCTS', 'auth' => 'acl_a_garage', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
