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

/**
* Adds the new permission category 'garage'
*/
$lang['permission_cat']['garage'] = 'Garage';

/**
* Adds thew new permission types 'ug_', 'mg_' & 'ag'
*/
//$lang['permission_type']['ug_'] = 'Garage user permissions';
//$lang['permission_type']['mg_'] = 'Garage moderator permissions';
//$lang['permission_type']['ag_'] = 'Garage admin permissions';

/**
* Adds the new user permission for phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_u_garage_browse'			=> array('lang' => 'Can browse garage', 'cat' => 'garage'),
	'acl_u_garage_search'			=> array('lang' => 'Can search garage',	'cat' => 'garage'),
	'acl_u_garage_add_vehicle'		=> array('lang' => 'Can add vehicles', 'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'		=> array('lang' => 'Can delete their vehicles', 'cat' => 'garage'),
	'acl_u_garage_add_make_model'		=> array('lang' => 'Can add new makes and models','cat' => 'garage'),
	'acl_u_garage_add_modification'		=> array('lang' => 'Can add modifications to their vehciles', 'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Can delete their modifications', 'cat' => 'garage'),
	'acl_u_garage_add_product'		=> array('lang' => 'Can add modification products', 'cat' => 'garage'),
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Can add quartermile times to their vehicles', 'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Can delete quartermile times from their vehicles ',	'cat' => 'garage'),
	'acl_u_garage_add_dynorun'		=> array('lang' => 'Can add dynoruns to their vehicles', 'cat' => 'garage'),
	'acl_u_garage_delete_dynorun'		=> array('lang' => 'Can delete their dynoruns', 'cat' => 'garage'),
	'acl_u_garage_add_lap'			=> array('lang' => 'Can add laps to garage', 'cat' => 'garage'),
	'acl_u_garage_delete_lap'		=> array('lang' => 'Can delete their laps', 'cat' => 'garage'),
	'acl_u_garage_add_track'		=> array('lang' => 'Can add new tracks', 'cat' => 'garage'),
	'acl_u_garage_delete_track'		=> array('lang' => 'Can delete track from garage', 'cat' => 'garage'),
	'acl_u_garage_add_insurance'		=> array('lang' => 'Can add insurance to garage', 'cat' => 'garage'),
	'acl_u_garage_delete_insurance'		=> array('lang' => 'Can delete insurance from garage', 'cat' => 'garage'),
	'acl_u_garage_add_service'		=> array('lang' => 'Can add service to garage', 'cat' => 'garage'),
	'acl_u_garage_delete_service'		=> array('lang' => 'Can delete service from garage', 'cat' => 'garage'),
	'acl_u_garage_add_blog'			=> array('lang' => 'Can add blog to garage', 'cat' => 'garage'),
	'acl_u_garage_delete_blog'		=> array('lang' => 'Can delete blog from garage', 'cat' => 'garage'),
	'acl_u_garage_add_business'		=> array('lang' => 'Can add business to garage', 'cat' => 'garage'),
	'acl_u_garage_rate'			=> array('lang' => 'Can rate items in garage', 'cat' => 'garage'),
	'acl_u_garage_comment'			=> array('lang' => 'Can comment in garage', 'cat' => 'garage'),
	'acl_u_garage_upload_image'		=> array('lang' => 'Can upload images to the garage', 'cat' => 'garage'),
	'acl_u_garage_remote_image'		=> array('lang' => 'Can use remote images in the garage', 'cat' => 'garage'),
	'acl_u_garage_delete_image'		=> array('lang' => 'Can delete their images from items in the garage', 'cat' => 'garage'),
	'acl_u_garage_deny'			=> array('lang' => 'Deny user access to the garage', 'cat' => 'garage'),
));


/**
* Adds the new moderator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_m_garage_edit'			=> array('lang' => 'Can edit vehicles and related items', 'cat' => 'garage'),
	'acl_m_garage_delete'			=> array('lang' => 'Can delete vehicles and related items', 'cat' => 'garage'),
	'acl_m_garage_rating'			=> array('lang' => 'Can delete/reset ratings', 'cat' => 'garage'),
	'acl_m_garage_approve_vehicle'		=> array('lang' => 'Can approve vehicles', 'cat' => 'garage'),
	'acl_m_garage_approve_make'		=> array('lang' => 'Can approve makes', 'cat' => 'garage'),
	'acl_m_garage_approve_model'		=> array('lang' => 'Can approve models', 'cat' => 'garage'),
	'acl_m_garage_approve_business'		=> array('lang' => 'Can approve businesses', 'cat' => 'garage'),
	'acl_m_garage_approve_quartermile'	=> array('lang' => 'Can approve quartermiles', 'cat' => 'garage'),
	'acl_m_garage_approve_dynorun'		=> array('lang' => 'Can approve dynoruns', 'cat' => 'garage'),
	'acl_m_garage_approve_guestbook'	=> array('lang' => 'Can approve guestbook comments', 'cat' => 'garage'),
	'acl_m_garage_approve_lap'		=> array('lang' => 'Can approve laps', 'cat' => 'garage'),
	'acl_m_garage_approve_track'		=> array('lang' => 'Can approve tracks', 'cat' => 'garage'),
	'acl_m_garage_approve_product'		=> array('lang' => 'Can approve products', 'cat' => 'garage'),
));

/**
* Adds the new administrator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
 	'acl_a_garage_update'	=> array('lang' => 'Can check garage version', 'cat' => 'garage'),
 	'acl_a_garage_setting'	=> array('lang' => 'Can alter garage settings', 'cat' => 'garage'),
 	'acl_a_garage_business'	=> array('lang' => 'Can manage garage business\'s', 'cat' => 'garage'),
 	'acl_a_garage_category'	=> array('lang' => 'Can manage garage categories', 'cat' => 'garage'),
 	'acl_a_garage_field'	=> array('lang' => 'Can manage garage custom fields', 'cat' => 'garage'),
 	'acl_a_garage_model'	=> array('lang' => 'Can manage garage models', 'cat' => 'garage'),
 	'acl_a_garage_product'	=> array('lang' => 'Can manage garage products', 'cat' => 'garage'),
 	'acl_a_garage_quota'	=> array('lang' => 'Can manage garage quotas', 'cat' => 'garage'),
 	'acl_a_garage_tool'	=> array('lang' => 'Can manage garage tools', 'cat' => 'garage'),
 	'acl_a_garage_track'	=> array('lang' => 'Can manage garage tracks', 'cat' => 'garage'),
));

?>
