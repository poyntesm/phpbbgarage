<?php
/***************************************************************************
 *                              language/en/acp/permissions_garage.php [English]
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: permissions_garage.php 165 2006-06-19 07:46:52Z poyntesm $
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

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

//Adding New Category
$lang['permission_cat']['garage'] = 'Garage';

//Adding The Garage Permissions
$lang = array_merge($lang, array(
	//User Garage Permissions
	'acl_u_garage_browse'			=> array('lang' => 'Can browse garage', 			'cat' => 'garage'),
	'acl_u_garage_search'			=> array('lang' => 'Can search garage', 			'cat' => 'garage'),
	'acl_u_garage_add_vehicle'		=> array('lang' => 'Can add vehicle to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'		=> array('lang' => 'Can delete vehicle from garage', 		'cat' => 'garage'),
	'acl_u_garage_add_modification'		=> array('lang' => 'Can add modification to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Can delete modification from garage', 	'cat' => 'garage'),
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Can add quartermile time to garage',	'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Can delete quartermile time from garage',	'cat' => 'garage'),
	'acl_u_garage_add_dynorun'		=> array('lang' => 'Can add dynorun to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_dynorun'		=> array('lang' => 'Can delete dynorun from garage', 		'cat' => 'garage'),
	'acl_u_garage_add_insurance'		=> array('lang' => 'Can add insurance to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_insurance'		=> array('lang' => 'Can delete insurance from garage', 		'cat' => 'garage'),
	'acl_u_garage_add_business'		=> array('lang' => 'Can add business to garage', 		'cat' => 'garage'),
	'acl_u_garage_add_make_model'		=> array('lang' => 'Can add makes and models to garage',	'cat' => 'garage'),
	'acl_u_garage_add_product'		=> array('lang' => 'Can add products to garage',	'cat' => 'garage'),
	'acl_u_garage_rate'			=> array('lang' => 'Can rate items in garage', 			'cat' => 'garage'),
	'acl_u_garage_comment'			=> array('lang' => 'Can comment in garage', 			'cat' => 'garage'),
	'acl_u_garage_upload_image'		=> array('lang' => 'Can upload image to garage', 		'cat' => 'garage'),
	'acl_u_garage_remote_image'		=> array('lang' => 'Can use remote image in garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_image'		=> array('lang' => 'Can delete image from garage', 		'cat' => 'garage'),
	'acl_u_garage_deny'			=> array('lang' => 'Deny user access to the garage', 		'cat' => 'garage'),

	//Moderator Garage Permissions
	'acl_m_garage'				=> array('lang' => 'Can moderate garage', 			'cat' => 'garage'),

	//Administrator Garage Permissions
 	'acl_a_garage'				=> array('lang' => 'Can alter garage settings', 		'cat' => 'garage'),
));

?>
