<?php
/** 
*
* acp_garage_permissions (phpBB Garage Permission Set) [English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

//Adding New Category & Type
$lang = array_merge($lang, array(
	'permission_cat'	=> array(
		'garage'	=> 'Garage'
	),
	'permission_type'	=> array(
		'ug_'			=> 'Garage user permissions',
		'ag_'			=> 'Garage admin permissions',
		'mg_'			=> 'Garage moderator permissions',
	),
));

//Adding The Garage Permissions
$lang = array_merge($lang, array(

	//User General Garage Permissions
	'acl_u_garage_browse'		=> array('lang' => 'Can browse garage',	'cat' => 'garage'),
	'acl_u_garage_search'		=> array('lang' => 'Can search garage',	'cat' => 'garage'),

	//User Vehicle Permissions
	'acl_u_garage_add_vehicle'	=> array('lang' => 'Can add vehicles', 'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'	=> array('lang' => 'Can delete their vehicles', 'cat' => 'garage'),
	'acl_u_garage_add_make_model'	=> array('lang' => 'Can add new makes and models','cat' => 'garage'),

	//User Modification Permissions
	'acl_u_garage_add_modification'		=> array('lang' => 'Can add modifications to their vehciles', 'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Can delete their modifications', 	'cat' => 'garage'),
	'acl_u_garage_add_product'		=> array('lang' => 'Can add modification products',	'cat' => 'garage'),

	//User Quartermile Permissions
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Can add quartermile times to their vehicles',	'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Can delete quartermile times from their vehicles ',	'cat' => 'garage'),

	//User Dynorun Permissions
	'acl_u_garage_add_dynorun'	=> array('lang' => 'Can add dynoruns to their vehicles','cat' => 'garage'),
	'acl_u_garage_delete_dynorun'	=> array('lang' => 'Can delete their dynoruns', 	'cat' => 'garage'),

	//User Track Times Permissions
	'acl_u_garage_add_lap'		=> array('lang' => 'Can add laps to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_lap'	=> array('lang' => 'Can delete their laps',	 	'cat' => 'garage'),
	'acl_u_garage_add_track'	=> array('lang' => 'Can add new tracks', 		'cat' => 'garage'),
	'acl_u_garage_delete_track'	=> array('lang' => 'Can delete track from garage', 	'cat' => 'garage'),

	//User Insurance Permissions
	'acl_u_garage_add_insurance'	=> array('lang' => 'Can add insurance to garage', 	'cat' => 'garage'),
	'acl_u_garage_delete_insurance'	=> array('lang' => 'Can delete insurance from garage', 	'cat' => 'garage'),

	//User Service History Permissions
	'acl_u_garage_add_service'	=> array('lang' => 'Can add service to garage', 	'cat' => 'garage'),
	'acl_u_garage_delete_service'	=> array('lang' => 'Can delete service from garage', 	'cat' => 'garage'),

	//User Blog Permissions
	'acl_u_garage_add_blog'		=> array('lang' => 'Can add blog to garage', 		'cat' => 'garage'),
	'acl_u_garage_delete_blog'	=> array('lang' => 'Can delete blog from garage', 	'cat' => 'garage'),

	//User Business Permissions
	'acl_u_garage_add_business'	=> array('lang' => 'Can add business to garage', 	'cat' => 'garage'),

	//User Rating Permissions	
	'acl_u_garage_rate'		=> array('lang' => 'Can rate items in garage', 		'cat' => 'garage'),

	//User Guestbook Permissions
	'acl_u_garage_comment'		=> array('lang' => 'Can comment in garage', 		'cat' => 'garage'),

	//Image Permissions
	'acl_u_garage_upload_image'	=> array('lang' => 'Can upload images to the garage', 		'cat' => 'garage'),
	'acl_u_garage_remote_image'	=> array('lang' => 'Can use remote images in the garage', 	'cat' => 'garage'),
	'acl_u_garage_delete_image'	=> array('lang' => 'Can delete their images from items in the garage', 		'cat' => 'garage'),

	//Deny Permissions
	'acl_u_garage_deny'		=> array('lang' => 'Deny user access to the garage', 	'cat' => 'garage'),

	//Moderator Garage Permissions
	'acl_m_garage'			=> array('lang' => 'Can moderate the garage', 		'cat' => 'garage'),

	//Administrator Garage Permissions
 	'acl_a_garage'			=> array('lang' => 'Can alter garage settings', 	'cat' => 'garage'),
));

?>
