<?php
/** 
*
* @package garage_install
* @version $Id: garage_install.php 643 2008-11-07 16:28:54Z poyntesm $
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/garage_install');


if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'PHPBB_GARAGE';
$version_config_name = 'phpbbgarage_version';
$language_file = 'mods/garage_install';

/*
* Options to display to the user (this is purely optional, if you do not need the options you do not have to set up this variable at all)
* Uses the acp_board style of outputting information, with some extras (such as the 'default' and 'select_user' options)
*/
$options = array(
	'install_makes'		=> array('lang' => 'INSERT_MAKES', 'type' => 'radio:yes_no', 'default' => true),
	'install_categories'	=> array('lang' => 'INSERT_CATEGORIES', 'type' => 'radio:yes_no', 'default' => true),
);

/*
* The array of versions
*/
$versions = array(
	// 2.0.B5
	'2.0.B5'	=> array(
		// Now to add a table (this uses the layout from develop/create_schema_files.php and from phpbb_db_tools)
		'table_add' => array(
			array('phpbb_garage_vehicles', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'user_id'		=> array('UINT', 0),
					'made_year'		=> array('UINT', '2007'),
					'engine_type'		=> array('TINT:2', 0),
					'colour'		=> array('XSTEXT_UNI', ''),
					'mileage'		=> array('UINT', 0),
					'mileage_unit'		=> array('VCHAR:32', 'Miles'),
					'price'			=> array('DECIMAL:10', 0),
					'currency'		=> array('VCHAR:32', 'EUR'),
					'comments'		=> array('MTEXT_UNI', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_options'	=> array('UINT', 7),
					'views'			=> array('UINT', 0),
					'date_created'		=> array('TIMESTAMP', 0),
					'date_updated'		=> array('TIMESTAMP', 0),
					'make_id'		=> array('UINT', 0),
					'model_id'		=> array('UINT', 0),
					'main_vehicle'		=> array('BOOL', 0),
					'weighted_rating'	=> array('DECIMAL:4', 0),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'date_created'		=> array('INDEX', 'date_created'),
					'date_updated'		=> array('INDEX', 'date_updated'),
					'user_id'		=> array('INDEX', 'user_id'),
					'views'			=> array('INDEX', 'views'),
				),
			)),
			array('phpbb_garage_business', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'title'			=> array('XSTEXT_UNI', ''),
					'address'		=> array('VCHAR', ''),
					'telephone'		=> array('VCHAR:100', ''),
					'fax'			=> array('VCHAR:100', ''),
					'website'		=> array('VCHAR', ''),
					'email'			=> array('VCHAR:100', ''),
					'opening_hours'		=> array('VCHAR', ''),
					'insurance'		=> array('BOOL', 0),
					'garage'		=> array('BOOL', 0),
					'retail'		=> array('BOOL', 0),
					'product'		=> array('BOOL', 0),
					'dynocentre'		=> array('BOOL', 0),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'insurance'		=> array('INDEX', 'insurance'),
					'garage'		=> array('INDEX', 'garage'),
					'retail'		=> array('INDEX', 'retail'),
					'product'		=> array('INDEX', 'product'),
					'dynocentre'		=> array('INDEX', 'dynocentre'),
				),
			)),
			array('phpbb_garage_categories', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'title'			=> array('TEXT_UNI', ''),
					'field_order'		=> array('USINT', 0),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_config', array(
				'COLUMNS'		=> array(
					'config_name'		=> array('VCHAR', ''),
					'config_value'		=> array('VCHAR_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'config_name',
			)),
			array('phpbb_garage_vehicles_gallery', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'image_id'		=> array('UINT', 0),
					'hilite'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'image_id'		=> array('INDEX', 'image_id'),
				),
			)),
			array('phpbb_garage_modifications_gallery', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'modification_id'	=> array('UINT', 0),
					'image_id'		=> array('UINT', 0),
					'hilite'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'image_id'		=> array('INDEX', 'image_id'),
				),
			)),
			array('phpbb_garage_quartermiles_gallery', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'quartermile_id'	=> array('UINT', 0),
					'image_id'		=> array('UINT', 0),
					'hilite'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'image_id'		=> array('INDEX', 'image_id'),
				),
			)),
			array('phpbb_garage_dynoruns_gallery', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'dynorun_id'		=> array('UINT', 0),
					'image_id'		=> array('UINT', 0),
					'hilite'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'image_id'		=> array('INDEX', 'image_id'),
				),
			)),
			array('phpbb_garage_laps_gallery', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'lap_id'		=> array('UINT', 0),
					'image_id'		=> array('UINT', 0),
					'hilite'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'image_id'		=> array('INDEX', 'image_id'),
				),
			)),
			array('phpbb_garage_guestbooks', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'author_id'		=> array('UINT', 0),
					'post_date'		=> array('TIMESTAMP', 0),
					'ip_address'		=> array('VCHAR:40', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_options'	=> array('UINT', 7),
					'pending'		=> array('BOOL', 0),
					'post'			=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'author_id'		=> array('INDEX', 'author_id'),
					'post_date'		=> array('INDEX', 'post_date'),
				),
			)),
			array('phpbb_garage_images', array(
				'COLUMNS'		=> array(
					'attach_id'		=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'attach_location'	=> array('VCHAR', ''),
					'attach_hits'		=> array('UINT', 0),
					'attach_ext'		=> array('VCHAR:100', ''),
					'attach_file'		=> array('VCHAR', ''),
					'attach_thumb_location'	=> array('VCHAR', ''),
					'attach_thumb_width'	=> array('USINT', 0),
					'attach_thumb_height'	=> array('USINT', 0),
					'attach_is_image'	=> array('BOOL', 0),
					'attach_date'		=> array('TIMESTAMP', 0),
					'attach_filesize'	=> array('UINT:20', 0),
					'attach_thumb_filesize'	=> array('UINT:20', 0),
				),
				'PRIMARY_KEY'	=> 'attach_id',
			)),
			array('phpbb_garage_premiums', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'business_id'		=> array('UINT', 0),
					'cover_type_id'		=> array('UINT', 0),
					'premium'		=> array('DECIMAL:10', 0),
					'comments'		=> array('MTEXT_UNI', ''),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_makes', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'make'			=> array('VCHAR', ''),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'make'			=> array('INDEX', 'make'),
				),
			)),
			array('phpbb_garage_models', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'make_id'		=> array('UINT', 0),
					'model'			=> array('VCHAR', ''),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'make_id'		=> array('INDEX', 'make_id'),
				),
			)),
			array('phpbb_garage_modifications', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'user_id'		=> array('UINT', 0),
					'category_id'		=> array('UINT', 0),
					'product_id'		=> array('UINT', 0),
					'price'			=> array('DECIMAL:10', 0),
					'install_price'		=> array('DECIMAL:10', 0),
					'product_rating'	=> array('TINT:2', 0),
					'purchase_rating'	=> array('TINT:2', 0),
					'install_rating'	=> array('TINT:2', 0),
					'shop_id'		=> array('UINT', 0),
					'installer_id'		=> array('UINT', 0),
					'comments'		=> array('MTEXT_UNI', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_options'	=> array('UINT', 7),
					'install_comments'	=> array('MTEXT_UNI', ''),
					'date_created'		=> array('TIMESTAMP', 0),
					'date_updated'		=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'user_id'		=> array('INDEX', 'user_id'),
					'vehicle_id_2'		=> array('INDEX', array('vehicle_id', 'category_id')),
					'category_id'		=> array('INDEX', 'category_id'),
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'date_created'		=> array('INDEX', 'date_created'),
					'date_updated'		=> array('INDEX', 'date_updated'),
				),
			)),
			array('phpbb_garage_products', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'business_id'		=> array('UINT', 0),
					'category_id'		=> array('UINT', 0),
					'title'			=> array('VCHAR', ''),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'business_id'		=> array('INDEX', 'business_id'),
					'category_id'		=> array('INDEX', 'category_id'),
				),
			)),
			array('phpbb_garage_quartermiles', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'rt'			=> array('PDECIMAL', 0),
					'sixty'			=> array('PDECIMAL', 0),
					'three'			=> array('PDECIMAL', 0),
					'eighth'		=> array('PDECIMAL', 0),
					'eighthmph'		=> array('PDECIMAL', 0),
					'thou'			=> array('PDECIMAL', 0),
					'quart'			=> array('PDECIMAL', 0),
					'quartmph'		=> array('PDECIMAL', 0),
					'pending'		=> array('BOOL', 0),
					'dynorun_id'		=> array('UINT', 0),
					'date_created'		=> array('TIMESTAMP', 0),
					'date_updated'		=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_dynoruns', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'dynocentre_id'		=> array('UINT', 0),
					'bhp'			=> array('DECIMAL:6', 0),
					'bhp_unit'		=> array('VCHAR:32', ''),
					'torque'		=> array('DECIMAL:6', 0),
					'torque_unit'		=> array('VCHAR:32', ''),
					'boost'			=> array('DECIMAL:6', 0),
					'boost_unit'		=> array('VCHAR:32', ''),
					'nitrous'		=> array('UINT', 0),
					'peakpoint'		=> array('PDECIMAL:7', 0),
					'date_created'		=> array('TIMESTAMP', 0),
					'date_updated'		=> array('TIMESTAMP', 0),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_ratings', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'rating'		=> array('TINT:2', 0),
					'user_id'		=> array('UINT', 0),
					'rate_date'		=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_tracks', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'title'			=> array('VCHAR', ''),
					'length'		=> array('VCHAR:32', ''),
					'mileage_unit'		=> array('VCHAR:32', ''),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
			)),
			array('phpbb_garage_laps', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'track_id'		=> array('UINT', 0),
					'condition_id'		=> array('UINT', 0),
					'type_id'		=> array('UINT', 0),
					'minute'		=> array('UINT:2', 0),
					'second'		=> array('UINT:2', 0),
					'millisecond'		=> array('UINT:2', 0),
					'pending'		=> array('BOOL', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'track_id'		=> array('INDEX', 'track_id'),
				),
			)),
			array('phpbb_garage_service_history', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'garage_id'		=> array('UINT', 0),
					'type_id'		=> array('UINT', 0),
					'price'			=> array('DECIMAL:10', 0),
					'rating'		=> array('TINT:2', 0),
					'mileage'		=> array('UINT', 0),
					'date_created'		=> array('TIMESTAMP', 0),
					'date_updated'		=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'garage_id'		=> array('INDEX', 'garage_id'),
				),
			)),
			array('phpbb_garage_blog', array(
				'COLUMNS'		=> array(
					'id'			=> array('UINT', NULL, 'auto_increment'),
					'vehicle_id'		=> array('UINT', 0),
					'user_id'		=> array('UINT', 0),
					'blog_title'		=> array('XSTEXT_UNI', ''),
					'blog_text'		=> array('MTEXT_UNI', ''),
					'blog_date'		=> array('TIMESTAMP', 0),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_options'	=> array('UINT', 7),
				),
				'PRIMARY_KEY'	=> 'id',
				'KEYS'			=> array(
					'vehicle_id'		=> array('INDEX', 'vehicle_id'),
					'user_id'		=> array('INDEX', 'user_id'),
				),
			)),
		),

		// Now to add some permission settings
		'permission_add' => array(
			array('u_garage_browse', true),
			array('u_garage_search', true),
			array('u_garage_add_vehicle', true),
			array('u_garage_delete_vehicle', true),
			array('u_garage_add_modification', true),
			array('u_garage_delete_modification', true),
			array('u_garage_add_quartermile', true),
			array('u_garage_delete_quartermile', true),
			array('u_garage_add_lap', true),
			array('u_garage_delete_lap', true),
			array('u_garage_add_track', true),
			array('u_garage_delete_track', true),
			array('u_garage_add_dynorun', true),
			array('u_garage_delete_dynorun', true),
			array('u_garage_add_insurance', true),
			array('u_garage_delete_insurance', true),
			array('u_garage_add_service', true),
			array('u_garage_delete_service', true),
			array('u_garage_add_blog', true),
			array('u_garage_delete_blog', true),
			array('u_garage_add_business', true),
			array('u_garage_add_make_model', true),
			array('u_garage_add_product', true),
			array('u_garage_rate', true),
			array('u_garage_comment', true),
			array('u_garage_upload_image', true),
			array('u_garage_remote_image', true),
			array('u_garage_delete_image', true),
			array('u_garage_deny', true),
			array('m_garage_edit', true),
			array('m_garage_delete', true),
			array('m_garage_rating', true),
			array('m_garage_approve_vehicle', true),
			array('m_garage_approve_make', true),
			array('m_garage_approve_model', true),
			array('m_garage_approve_business', true),
			array('m_garage_approve_quartermile', true),
			array('m_garage_approve_dynorun', true),
			array('m_garage_approve_guestbook', true),
			array('m_garage_approve_lap', true),
			array('m_garage_approve_track', true),
			array('m_garage_approve_product', true),
		 	array('a_garage_update', true),
		 	array('a_garage_setting', true),
		 	array('a_garage_business', true),
		 	array('a_garage_category', true),
		 	array('a_garage_field', true),
		 	array('a_garage_model', true),
		 	array('a_garage_product', true),
		 	array('a_garage_quota', true),
		 	array('a_garage_tool', true),
		 	array('a_garage_track', true),
		),

		// How about we give some default permissions then as well?
		'permission_set' => array(
			// Global Role permissions
			array('ROLE_ADMIN_STANDARD', array('a_garage_update', 'a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'), 'role', true),
			array('ROLE_ADMIN_FULL', array('a_garage_update', 'a_garage_setting', 'a_garage_business', 'a_garage_category', 'a_garage_field', 'a_garage_model', 'a_garage_product', 'a_garage_quota', 'a_garage_tool', 'a_garage_track'), 'role', true),
			array('ROLE_MOD_QUEUE', array('m_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'), 'role', true),
			array('ROLE_MOD_STANDARD', array('m_garage_edit', 'm_garage_delete', 'm_garage_rating', 'm_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'), 'role', true),
			array('ROLE_MOD_FULL', array('m_garage_edit', 'm_garage_delete', 'm_garage_rating', 'm_garage_approve_vehicle', 'm_garage_approve_make', 'm_garage_approve_model', 'm_garage_approve_business', 'm_garage_approve_quartermile', 'm_garage_approve_dynorun', 'm_garage_approve_guestbook', 'm_garage_approve_lap', 'm_garage_approve_track', 'm_garage_approve_product'), 'role', true),
			array('ROLE_USER_STANDARD', array('u_garage_browse', 'u_garage_search', 'u_garage_add_vehicle', 'u_garage_delete_vehicle', 'u_garage_add_modification', 'u_garage_delete_modification', 'u_garage_add_quartermile', 'u_garage_delete_quartermile', 'u_garage_add_lap', 'u_garage_delete_lap', 'u_garage_add_track', 'u_garage_delete_track', 'u_garage_add_dynorun', 'u_garage_delete_dynorun', 'u_garage_add_insurance', 'u_garage_delete_insurance', 'u_garage_add_service', 'u_garage_delete_service', 'u_garage_add_blog', 'u_garage_delete_blog', 'u_garage_add_business', 'u_garage_add_make_model', 'u_garage_add_product', 'u_garage_rate', 'u_garage_comment', 'u_garage_upload_image', 'u_garage_remote_image', 'u_garage_delete_image', 'u_garage_deny'), 'role', true),
			array('ROLE_USER_FULL', array('u_garage_browse', 'u_garage_search', 'u_garage_add_vehicle', 'u_garage_delete_vehicle', 'u_garage_add_modification', 'u_garage_delete_modification', 'u_garage_add_quartermile', 'u_garage_delete_quartermile', 'u_garage_add_lap', 'u_garage_delete_lap', 'u_garage_add_track', 'u_garage_delete_track', 'u_garage_add_dynorun', 'u_garage_delete_dynorun', 'u_garage_add_insurance', 'u_garage_delete_insurance', 'u_garage_add_service', 'u_garage_delete_service', 'u_garage_add_blog', 'u_garage_delete_blog', 'u_garage_add_business', 'u_garage_add_make_model', 'u_garage_add_product', 'u_garage_rate', 'u_garage_comment', 'u_garage_upload_image', 'u_garage_remote_image', 'u_garage_delete_image', 'u_garage_deny'), 'role', true),

			// Global Group permissions
			array('GUESTS', 'u_garage_browse', 'group', true),
		),

		// Alright, now lets add some modules
		'module_add' => array(
			// First, lets add our parent categories
			array('ucp', 0, 'UCP_GARAGE'),
			array('mcp', 0, 'MCP_GARAGE'),
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_GARAGE_SETTINGS'),
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_GARAGE_MANAGEMENT'),

			// Now we will add the modules
			array('ucp', 'UCP_GARAGE', array(
					'module_basename'	=> 'garage',
				),
			),
			array('mcp', 'MCP_GARAGE', array(
					'module_basename'	=> 'garage',
				),
			),
			array('acp', 'ACP_GARAGE_SETTINGS', array(
					'module_basename'	=> 'garage_setting',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_update',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_business',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_category',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_model',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_product',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_quota',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_tool',
				),
			),
			array('acp', 'ACP_GARAGE_MANAGEMENT', array(
					'module_basename'	=> 'garage_track',
				),
			),
		),

		// Lets add a new column to the phpbb_test table named test_time
		'table_column_add' => array(
			array('phpbb_users', 'user_garage_index_columns', array('BOOL', 2)),
			array('phpbb_users', 'user_garage_guestbook_email_notify', array('BOOL', 1)),
			array('phpbb_users', 'user_garage_guestbook_pm_notify', array('BOOL', 1)),
			array('phpbb_users', 'user_garage_mod_email_optout', array('BOOL', 0)),
			array('phpbb_users', 'user_garage_mod_pm_optout', array('BOOL', 0)),
		),

		/*
		* Now we need to insert some data.  The easiest way to do that is through a custom function
		* Enter 'custom' for the array key and the name of the function for the value.
		*/
		'custom'	=> 'garage_data_entry',

		/*
		* Now we need to purge some cache items
		*/
		'cache_purge'	=> array(
			array('auth'),
			array('template', 0),
			array('imageset', 0),
			array('theme', 0),
		),
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function garage_config_entries($action, $version)
{
	global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx;

	$config_data = array(
		'version' 				=> '2.0.B5-DEV',
		'cars_per_page' 			=> '30',
		'year_start' 				=> '1980',
		'year_end' 				=> '1',
		'enable_user_submit_make' 		=> '1',
		'enable_user_submit_model' 		=> '1',
		'dateformat' 				=> 'dMYH:i',
		'default_make_id' 			=> '',
		'default_model_id' 			=> '',
		'integrate_memberlist' 			=> '1',
		'integrate_viewtopic' 			=> '1',
		'integrate_profile' 			=> '1',
		'profile_thumbs' 			=> '1',
		'enable_pm_pending_notify' 		=> '1',
		'enable_email_pending_notify' 		=> '1',
		'enable_pm_pending_notify_optout' 	=> '1',
		'enable_email_pending_notify_optout' 	=> '1',
		'enable_vehicle_approval'		=> '0',
		'enable_index_menu' 			=> '1',
		'enable_browse_menu' 			=> '1',
		'enable_search_menu' 			=> '1',
		'enable_insurance_review_menu' 		=> '1',
		'enable_garage_review_menu' 		=> '1',
		'enable_shop_review_menu' 		=> '1',
		'enable_quartermile_menu' 		=> '1',
		'enable_dynorun_menu' 			=> '1',
		'enable_lap_menu' 			=> '1',
		'enable_garage_header' 			=> '1',
		'enable_quartermile_header' 		=> '1',
		'enable_dynorun_header' 		=> '1',
		'enable_latest_vehicle_index'		=> '1',
		'latest_vehicle_index_limit' 		=> '10',
		'enable_featured_vehicle' 		=> '1',
		'index_columns' 			=> '2',
		'enable_user_index_columns' 		=> '1',
		'featured_vehicle_id' 			=> '1',
		'featured_vehicle_random' 		=> '0',
		'featured_vehicle_from_block' 		=> '',
		'featured_vehicle_description' 		=> '',
		'enable_newest_vehicle' 		=> '1',
		'newest_vehicle_limit' 			=> '5',
		'enable_updated_vehicle' 		=> '1',
		'updated_vehicle_limit' 		=> '5',
		'enable_newest_modification' 		=> '1',
		'newest_modification_limit' 		=> '5',
		'enable_updated_modification' 		=> '1',
		'updated_modification_limit' 		=> '5',
		'enable_most_modified' 			=> '1',
		'most_modified_limit' 			=> '5',
		'enable_most_spent' 			=> '1',
		'most_spent_limit' 			=> '5',
		'enable_most_viewed' 			=> '1',
		'most_viewed_limit' 			=> '5',
		'enable_last_commented' 		=> '1',
		'last_commented_limit' 			=> '5',
		'enable_top_dynorun'			=> '1',
		'top_dynorun_limit'			=> '5',
		'enable_top_quartermile'		=> '1',
		'top_quartermile_limit'			=> '5',
		'enable_top_rating'			=> '1',
		'top_rating_limit'			=> '5',
		'enable_top_lap' 			=> '1',
		'top_lap_limit' 			=> '5',
		'enable_images' 			=> '1',
		'enable_vehicle_images' 		=> '1',
		'enable_modification_images' 		=> '1',
		'enable_quartermile_images' 		=> '1',
		'enable_dynorun_images' 		=> '1',
		'enable_lap_images' 			=> '1',
		'enable_uploaded_images' 		=> '1',
		'enable_remote_images' 			=> '1',
		'remote_timeout' 			=> '60',
		'enable_mod_gallery' 			=> '1',
		'enable_quartermile_gallery' 		=> '1',
		'enable_dynorun_gallery' 		=> '1',
		'enable_lap_gallery' 			=> '1',
		'gallery_limit' 			=> '10',
		'max_image_kbytes' 			=> '1024',
		'max_image_resolution' 			=> '1024',
		'thumbnail_resolution' 			=> '150',
		'enable_watermark' 			=> '0',
		'watermark_type' 			=> 'non_permanent',
		'watermark_source' 			=> 'watermark.png',
		'enable_quartermile' 			=> '1',
		'enable_quartermile_approval' 		=> '0',
		'enable_quartermile_image_required' 	=> '1',
		'quartermile_image_required_limit' 	=> '13',
		'enable_dynorun' 			=> '1',
		'enable_dynorun_approval' 		=> '0',
		'enable_dynorun_image_required' 	=> '1',
		'dynorun_image_required_limit' 		=> '300',
		'enable_tracktime' 			=> '1',
		'enable_user_add_track' 		=> '1',
		'enable_track_approval' 		=> '0',
		'enable_lap_approval' 			=> '0',
		'enable_insurance' 			=> '1',
		'enable_insurance_search' 		=> '1',
		'enable_user_submit_business' 		=> '1',
		'enable_business_approval' 		=> '0',
		'rating_permanent' 			=> '0',
		'rating_always_updateable' 		=> '1',
		'minimum_ratings_required' 		=> '5',
		'enable_guestbooks' 			=> '1',
		'enable_guestbooks_bbcode' 		=> '1',
		'enable_guestbooks_comment_approval' 	=> '0',
		'enable_user_submit_product' 		=> '1',
		'enable_product_approval' 		=> '0',
		'enable_product_search' 		=> '1',
		'enable_service' 			=> '1',
		'enable_blogs' 				=> '1',
		'enable_blogs_bbcode' 			=> '1',
		'default_vehicle_quota' 		=> '1',
		'default_upload_quota' 			=> '5',
		'default_remote_quota' 			=> '5',
		'add_groups' 				=> '',
		'add_groups_quotas' 			=> '',
		'upload_groups' 			=> '',
		'upload_groups_quotas' 			=> '',
		'remote_groups' 			=> '',
		'remote_groups_quotas' 			=> '',
		'enable_blogs_smilies' 			=> '1',
		'enable_blogs_url' 			=> '1',
		'enable_make_approval' 			=> '0',
		'enable_model_approval' 		=> '0',
		'enable_guestbooks_url' 		=> '1',
		'enable_guestbooks_smilies' 		=> '1',
	);

	switch ($action)
	{
		case 'update' :
			// Run this when updating
		break;

		case 'install' :

			// Run this when installing/updating
			if ($umil->table_exists(GARAGE_CONFIG_TABLE))
			{
				foreach ($config_data as $config_name => $config_value)
				{
					$sql_ary[] = array(
						'config_name' => $config_name,
						'config_value' => $config_value,
					);
				}

				$db->sql_multi_insert(GARAGE_CONFIG_TABLE, $sql_ary);
			}
		break;

		case 'uninstall' :
			// Run this when uninstalling
		break;
	}
}

/*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function garage_category_entries($action, $version)
{
	global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx;

	$category_data = array(
		'Engine'		=> 1,
		'Transmission'		=> 2,
		'Suspension'		=> 3,
		'Brakes'		=> 4,
		'Interior'		=> 5,
		'Exterior'		=> 6,
		'Audio'			=> 7,
		'Alloys &amp; Tyres'	=> 8,
		'Security'		=> 9,
	);

	switch ($action)
	{
		case 'update' :
			// Run this when updating
		break;

		case 'install' :

			// Run this when installing/updating
			if ($umil->table_exists(GARAGE_CATEGORIES_TABLE))
			{
				foreach ($category_data as $category => $order)
				{
					$sql_ary[] = array(
						'title'		=> $category,
						'field_order'	=> $order,
					);
				}

				$db->sql_multi_insert(GARAGE_CATEGORIES_TABLE, $sql_ary);
			}
		break;

		case 'uninstall' :
			// Run this when uninstalling
		break;
	}
}

/*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function garage_model_entries($action, $version)
{
	global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx;

	$model_data = array(
		'AC' 		=> array(
				'Ace',
				'Cobra',
				'Superblower',
			),
		'Acura' 	=> array(
				'CL',
				'CL Type S',
				'Integra',
				'Legend',
				'MDX',
				'NSX',
				'RL',
				'RL-Series',
				'RSX',
				'SLX',
				'TL',
				'TL Type S',
				'TSX',
				'Vigor',
			),
		'Aixam'		=> array(
				'400',
				'500',
			),
		'Alfa-Romeo' 	=> array(
				'145',
				'146',
				'147',
				'155',
				'156',
				'164',
				'166',
				'33',
				'75',
				'Alfasud',
				'Giulietta',
				'GTV',
				'Spider',
				'Sportwagon',
				'Sprint',
				'S2',	
			),
		'Asia' 		=> array(
				'Rocsta',	
			),
		'Aston-Martin' 	=> array(
				'DB2',
				'DB4',
				'DB5',
				'DB6',
				'DB7',
				'DB9',
				'DBS',
				'Lagonda',
				'V8',
				'Vanquish',
				'Vantage',
				'Virage',
				'Volante',
			),
		'Audi' 		=> array(
				'100',
				'100 Avant',
				'200',
				'80',
				'90',
				'A2',
				'A3',
				'A4',
				'A4 Avant',
				'A6',
				'A6 Avant',
				'A8',
				'Allroad',
				'Avant',
				'Cabriolet',
				'Convertible',
				'Coupe',
				'Quattro',
				'RS2',
				'RS4 Avant',
				'S2',
				'S3',
				'S4',
				'S4 Avant',
				'S6',
				'S6 Avant',
				'S8',
				'TT',
				'V8',		
				'R8',		
			),
		'Austin' 	=> array(
				'Allegro',
				'Healy',
				'Maestro',
				'Maxi',
				'Metro',
				'Mini',
				'Montego',
				'Princess',		
			),
		'Bentley' 	=> array(
				'Arnage',
				'Azure',
				'Brooklands',
				'Continental',
				'Corniche',
				'Eight',
				'Mulsanne',
				'Series II',
				'T Series',
				'Turbo R',		
			),
		'BMW' 		=> array(
				'1 Series',
				'3 Series',
				'5 Series',
				'6 Series',
				'7 Series',
				'8 Series',
				'Alpina',
				'M',
				'M3',
				'M5',
				'X3',
				'X5',
				'Z3',
				'Z4',
				'Z8',
			),
		'Bristol'	=> array(
				'411',
				'412',
				'Blenheim',
			),
		'Cadillac'	=> array(
				'Brougham',
				'Eldorado',
				'Escalade',
				'Fleetwood',
				'Seville',
			),
		'Caterham' 	=> array(
				'Super 7',
				'Super Sprint',		
			),
		'Chevrolet' 	=> array(
				'210',
				'Astro',
				'Blazer',
				'Camaro',
				'Corvette',
				'GMC',
				'S10',
				'Silverado',
				'Suburban',
				'Tahoe',		
			),
		'Chrysler' 	=> array(
				'Cherokee',
				'Grand Cherokee',
				'Grand Voyager',
				'Jeep',
				'Neon',
				'PT Cruiser',
				'Sebring',
				'Viper',
				'Voyager',
				'Wrangler',		
			),
		'Citroen' 	=> array(
				'2CV',
				'AX',
				'Berlingo',
				'BX',
				'C3',
				'C5',
				'C8',
				'CX',
				'DE',
				'Reflex',
				'Saxo',
				'Synergie',
				'Visa',
				'Xantia',
				'XM',
				'Xsara',
				'Xsara Picasso',
				'ZX',		
			),
		'Daewoo' 	=> array(
				'Espero',
				'Kalos',
				'Korando',
				'Lanos',
				'Leganza',
				'Matiz',
				'Musso',
				'Nexia',
				'Nubira',
				'Tacuma',		
			),
		'Daihatsu' 	=> array(
				'Applause',
				'Charade',
				'Cuore',
				'Domino',
				'Fourtrak',
				'Grand Move',
				'Hijet',
				'Mira',
				'Move',
				'Sirion',
				'Sportrak',
				'Terios',
				'YRV',		
			),
		'Daimler' 	=> array(
				'Double Six',
				'Empress',
				'Limousine',
				'Saloon',
				'Sovereign',
				'Super V8',
				'V8',
				'XJ Series',
				'XJ12',	
			),
		'Datsun'	=> array(
				'Patrol',		
			),
		'Delorian' 	=> array(
				'DMZ',	
			),
		'Dodge' 	=> array(
				'Dakota',
				'Durango',
				'Ram',		
			),
		'Ferrari' 	=> array(
				'246',
				'250',
				'308',
				'328',
				'330',
				'348',
				'355',
				'360',
				'365',
				'400',
				'430',
				'412',
				'456',
				'512',
				'550',
				'575M',
				'579',
				'612',
				'Daytona',
				'Dino',
				'Enzo',
				'F355',
				'F40',
				'Mondial',
				'Testarossa',
				'F50',		
			),
		'Fiat' 		=> array(
				'124',
				'126',
				'130',
				'500',
				'Barchetta',
				'Bravo',
				'Brava',
				'Cinquecento',
				'Coupe',
				'Croma',
				'Doblo',
				'Marea',
				'Marea Weekend',
				'Multipla',
				'Panda',
				'Punto',
				'Regato',
				'Seicento',
				'Spider',
				'Stilo',
				'Tempra',
				'Tipo',
				'Ulysse',
				'Uno',
				'X19',		
			),
		'Ford' 		=> array(
				'Capri',
				'Consul',
				'Cortina',
				'Cougar',
				'Escort',
				'Explorer',
				'F150',
				'F350',
				'Falcon',
				'Fiesta',
				'Focus',
				'Fusion',
				'Galaxy',
				'Granada',
				'Ka',
				'Maverick',
				'Mondeo',
				'Mustang',
				'Orion',
				'Probe',
				'Puma',
				'Ranger',
				'Sapphire',
				'Scorpio',
				'Sierra',
				'Streetka',
				'Taurus',		
			),
		'FSO' 		=> array(
				'Caro',		
			),
		'Ginetta' 	=> array(
				'G Series',	
			),
		'Griffon' 	=> array(
				'110',	
			),
		'Hillman' 	=> array(
				'Imp',
				'Minx',		
			),
		'HMC' 		=> array(
				'Mark IV SE',		
			),
		'Honda' 	=> array(
				'Accord',
				'Aerodeck',
				'Ballade',
				'Beat',
				'Civic',
				'Concerto',
				'CR-V',
				'CR-X',
				'HR-V',
				'Insight',
				'Integra',
				'Jazz',
				'Legend',
				'Logo',
				'NSX',
				'Prelude',
				'S2000',
				'Shuttle',
				'Stream',		
			),
		'Hummer' 	=> array(
				'H1',	
				'H2',	
				'H3',	
			),
		'Hyundai' 	=> array(
				'Accent',
				'Amica',
				'Atoz',
				'Coupe',
				'Elantra',
				'Getz',
				'Lantra',
				'Matrix',
				'Pony',
				'S-Coupe',
				'Santa Fe',
				'Sonata',
				'Stellar',
				'Trajet',
				'X2',
				'XG30',		
			),
		'ISO' 		=> array(
				'Lele',		
			),
		'Isuzu' 	=> array(
				'Piazza',
				'TF',
				'Trooper',	
			),
		'Jaguar' 	=> array(
				'E-Type',
				'Mark I',
				'Mark II',
				'S-Type',
				'Sovereign',
				'V8',
				'X-Type',
				'XJ Series',
				'XJS',
				'XK',		
			),
		'Jeep' 		=> array(
				'Cherokee',
				'Grand Cherokee',
				'Renegade',
				'Wrangler',		
			),
		'Jensen' 	=> array(
				'Interceptor',
				'S-V8',		
			),
		'Kia' 		=> array(
				'Carens',
				'Clarus',
				'Magentis',
				'Mentor',
				'Pride',
				'Rio',
				'Sedona',
				'Shuma',
				'Sorento',
				'Sportage',		
			),
		'Lada' 		=> array(
				'1500',
				'Niva',
				'Riva',
				'Samara',		
			),
		'Lamborghini' 	=> array(
				'Countach',
				'Diablo',
				'LM',
				'Murcielago',
				'urraco',		
			),
		'Lancia' 	=> array(
				'Beta',
				'Dedra',
				'Delta',
				'Monte Carlo',
				'Prisma',
				'Thema',
				'Y10',		
			),
		'Land Rover' 	=> array(
				'Defender',
				'Discovery',
				'Freelander',
				'Lightweight',
				'Range Rover',
				'Series II',
				'Series III',		
			),
		'Lexus' 	=> array(
				'GS 300',
				'IS 300',
				'LS 430',
				'RX 300',
				'SC 430',
				'Soarer',
				'ES 300',
				'GS 430',
				'GX 470',
				'LX 470',		
			),
		'Ligier' 	=> array(
				'Ambra',		
			),
		'Lincoln' 	=> array(
				'Blackwood',
				'Navigator',
				'Towncar',		
			),
		'Lotus' 	=> array(
				'340R',
				'Carlton',
				'Eclat',
				'Elan',
				'Elise',
				'Elite',
				'Esprit',
				'Excel',
				'Exige',		
			),
		'Marcos' 	=> array(
				'LM',
				'Mantara',
				'Mantaray',
				'Mantis',
				'Mantula',
				'Martina',		
			),
		'Maserati' 	=> array(
				'222',
				'320',
				'3200',
				'BiTurbo',
				'Convertible',
				'Ghibli',
				'Kharif',
				'Quaddroporte',
				'Spyder',		
			),
		'Maybach' 	=> array(
				'57',
				'62',		
			),
		'Mazda' 	=> array(
				'121',
				'323',
				'626',
				'Demio',
				'Eunos',
				'Mazda 2',
				'Mazda 6',
				'MPV',
				'MX-3',
				'MX-5',
				'MX-6',
				'Premacy',
				'RX-7',
				'Tribute',
				'Xedos',		
			),
		'McLaren' 	=> array(
				'M6',
				'F1',		
				'SLR',		
			),
		'Mercedes-Benz' => array(
				'180',
				'190',
				'200',
				'220',
				'230',
				'240',
				'260',
				'280',
				'300',
				'310',
				'320',
				'350',
				'380',
				'400',
				'410',
				'420',
				'450',
				'500',
				'560',
				'600',
				'A Class',
				'AMG',
				'C Class',
				'CE Class',
				'CL',
				'CLK',
				'E Class',
				'G Class',
				'M Class',
				'S Class',
				'SE Class',
				'SL Class',
				'SLK',
				'V Class',
				'Vaneo',		
			),
		'MG' 		=> array(
				'MGB',
				'MGB GT',
				'MGF',
				'Midget',
				'RV8',
				'TF',
				'ZR',
				'ZS',
				'ZT',
				'ZT-T',		
			),
		'Microcar' 	=> array(
				'Virgo',		
			),
		'Mini' 		=> array(
				'Cooper',
				'Mini',
				'One',		
			),
		'Mitsubishi' 	=> array(
				'3000GT',
				'Carisma',
				'Challenger',
				'Chariot',
				'Colt',
				'Cordia',
				'Delcia',
				'FTO',
				'Galant',
				'L200',
				'Lancer',
				'Legnum',
				'Pajero',
				'Ralliart',
				'RVR',
				'Shogun',
				'Shogun Pinin',
				'Sigma',
				'Space Runner',
				'Space Star',
				'Space Wagon',
				'Starion',
				'Strada',		
			),
		'Morgan' 	=> array(
				'4/4',
				'Aero',
				'Plus 4',
				'Plus 8',		
			),
		'Morris' 	=> array(
				'Ital',
				'Mini',
				'Minor',		
			),
		'Noble' 	=> array(
				'M12',		
			),
		'Opel' 		=> array(
				'Commodore',
				'Corsa',
				'Kadette',
				'Manta',
				'Monza',
				'Omega',
				'Zafira',		
			),
		'Pagani' 	=> array(
				'Zonda',		
			),
		'Panther' 	=> array(
				'Kallista',		
			),
		'Perodua' 	=> array(
				'Kelisa',
				'Kenari',
				'Nippa',		
			),
		'Peugeot' 	=> array(
				'104',
				'106',
				'205',
				'206',
				'305',
				'306',
				'307',
				'309',
				'405',
				'406',
				'504',
				'505',
				'605',
				'607',
				'806',
				'807',		
			),
		'Pontiac' 	=> array(
				'Firebird',
				'Trans Am',		
			),
		'Porsche'	=> array(
				'356',
				'911',
				'912',
				'924',
				'928',
				'944',
				'968',
				'Boxster',
				'Carrera GT',
				'Cayenne',		
			),
		'Proton' 	=> array(
				'Compact',
				'Coupe',
				'GL',
				'GLS',
				'Impian',
				'Persona',
				'Satria',
				'Wira',		
			),
		'Reliant' 	=> array(
				'Rialto',
				'Robin',
				'Sabre',
				'Scimitar',		
			),
		'Renault' 	=> array(
				'4',
				'5',
				'6',
				'9',
				'11',
				'12',
				'14',
				'15',
				'16',
				'17',
				'18',
				'19',
				'20',
				'21',
				'25',
				'30',
				'A610',
				'Avantime',
				'Clio',
				'Espace',
				'Fuego',
				'Grand Espace',
				'GTA',
				'Kangoo',
				'Laguna',
				'Megane',
				'Safrane',
				'Scenic',
				'Scenic RX4',
				'Sport Spider',
				'Vel Satis',		
			),
		'Riley' 	=> array(
				'Elf',
				'RM Series',		
			),
		'Rolls Royce' 	=> array(
				'20/25',
				'25/30',
				'Corniche',
				'Pink Ward',
				'Phantom',
				'Silver Cloud',
				'Silver Dawn',
				'Silver Seraph',
				'Silver Shadow',
				'Silver Spirit',
				'Silver Spur',
				'Silver Wraith',		
			),
		'Rover' 	=> array(
				'100',
				'200',
				'2000',
				'2200',
				'2300',
				'25',
				'3500',
				'400',
				'45',
				'600',
				'75',
				'800',
				'90',
				'Cabriolet',
				'Coupe',
				'Maestro',
				'Metro',
				'Mini',
				'Montego',
				'Sterling',
				'Tourer',
				'Vitesse',		
			),
		'Saab' 		=> array(
				'9-3',
				'9-5',
				'90',
				'900',
				'9000',
				'96',
				'99',		
			),
		'Sao' 		=> array(
				'Penza',		
			),
		'Seat' 		=> array(
				'Alhambra',
				'Arosa',
				'Cordoba',
				'Ibiza',
				'Leon',
				'Malaga',
				'Marbella',
				'Toledo',
				'Vario',		
			),
		'Singer' 	=> array(
				'Gazelle',		
			),
		'Skoda' 	=> array(
				'Fabia',
				'Favorit',
				'Felicia',
				'Octavia',
				'Superb',		
			),
		'Smart' 	=> array(
				'Car',
				'CDI',
				'City Coupe',
				'Edition',
				'Passion',
				'Pulse',
				'Pure',		
			),
		'SsangYong' 	=> array(
				'Korando',
				'Musso',		
			),
		'Subaru' 	=> array(
				'1600',
				'1800',
				'Forester',
				'Impreza',
				'Justy',
				'Legacy',
				'SVX',
				'Vivio',
				'XT',		
			),
		'Sunbeam' 	=> array(
				'Alpine',	
			),
		'Suzuki' 	=> array(
				'Alto',
				'Baleno',
				'cappucino',
				'Grand Vitara',
				'Ignis',
				'Jimny',
				'Liana',
				'Samurai',
				'SJ',
				'Swift',
				'Vitara',
				'Wagon-R',
				'X-90',	
			),
		'Talbot' 	=> array(
				'Alpine',
				'BA75',
				'Horizon',
				'Samba',
				'Solara',
				'Sunbeam',		
			),
		'Tata' 		=> array(
				'Safari',	
			),
		'Toyota' 	=> array(
				'4 Runner',
				'Altezza',
				'Avensis',
				'Camry',
				'Carina',
				'Celica',
				'Corolla',
				'Corona',
				'Cressida',
				'Crown',
				'Estima',
				'Harrier',
				'Hiace',
				'Hilux',
				'Landcruiser',
				'Liteace',
				'Lucida',
				'MR2',
				'Paseo',
				'Picnic',
				'Prado',
				'Previa',
				'Prius',
				'Rav 4',
				'Sera',
				'Soarer',
				'Space Cruiser',
				'starlet',
				'Supra',
				'Surf',
				'Tercel',
				'Townace',
				'Yaris',		
			),
		'Triumph' 	=> array(
				'Dolomite',
				'Spitfire',
				'Stag',
				'Toledo',
				'TR4',
				'TR6',
				'TR7',
				'TR8',		
			),
		'TVR' 		=> array(
				'280I',
				'3000M',
				'350I',
				'450',
				'Cerbera',
				'Chimera',
				'Griffith',
				'S Convertible',
				'S2',
				'S3',
				'T350',
				'Taimor',
				'Tamora',
				'Tasmin',
				'Tuscan',
				'Tuscan S',		
			),
		'Ultima' 	=> array(
				'Sport',
				'Spyder',	
			),
		'Vauxhall' 	=> array(
				'Agila',
				'Astra',
				'Belmont',
				'Calibra',
				'Carlton',
				'Cavalier',
				'Chevette',
				'Corsa',
				'Frontera',
				'Monterey',
				'Nova',
				'Omega',
				'Royale',
				'Senator',
				'Sintra',
				'Tigra',
				'Vectra',
				'Viva',
				'VX220',
				'Zafira',		
			),
		'Volkswagen' 	=> array(
				'Beetle',
				'Bora',
				'Caravelle',
				'Corrado',
				'Derby',
				'Fastback',
				'Golf',
				'Jetta',
				'K70',
				'Karmann',
				'Lupo',
				'Passat',
				'Phaeton',
				'Polo',
				'Santana',
				'Scirocco',
				'Sharan',
				'Touareg',
				'Vento',		
			),
		'Volvo' 	=> array(
				'121',
				'122',
				'164',
				'240',
				'244',
				'245',
				'260',
				'264',
				'340',
				'360',
				'440',
				'460',
				'480',
				'740',
				'760',
				'850',
				'940',
				'960',
				'C70',
				'P1800',
				'S40',
				'S60',
				'S70',
				'S80',
				'S90',
				'Torslanda',
				'V40',
				'V70',
				'V90',
				'XC70',
				'XC90',		
			),
		'Westfield' 	=> array(
				'1600',
				'1800',
				'7',
				'Mega',
				'Megabird',
				'Megablade',
				'Sei',		
			),
		'Yugo' 		=> array(
				'45',
				'Tempo',		
			),
		'Nissan' 	=> array(
				'DC-3', '100NX', '1200', '210', '310', '180SX', '200SX', 'B10', 'B110', 'B-210', '240SX', '240Z', '280ZX', '300C', '300ZX', '350Z', '370Z', '510', '720', '810', 'Almera', 'Almera Tino', 'Altima', 'Aprio', 'Armada', 'Avenir', 'Be-1', 'Bluebird', 'Caravan', 'Cedric', 'Cefiro', 'Cherry', 'Cima', 'Crew', 'Cube', 'Elgrand', 'Fairlady', 'Figaro', 'Frontier', 'Fuga', 'Gazelle', 'Gloria', 'GT-R', 'Hardbody Truck', 'Hypermini', 'Laurel', 'Leopard', 'Lafesta', 'Liberty', 'Livina Geniss', 'Maxima', 'March', 'Multi', 'Murano', 'Navara', 'Note', 'NX', 'Paladin', 'Pao', 'Pathfinder', 'Patrol', 'Pintara', 'Platina', 'Prairie', 'Presage', 'Presea', 'Primera', 'President', 'Pulsar', 'Pulsar GTI-R', 'Qashqai', 'Quest', 'R390 GT1', 'R\'nessa', 'Rasheen', 'Roadster-Road Star', 'Rogue', 'S-Cargo', 'Saurus Jr', 'Sentra', 'Serena', 'Silvia', 'Skyline', 'Skyline GT-R', 'Stanza', 'Stagea', 'Sunny', 'Teana', 'Terrano', 'Terrano II', 'Tiida', 'Titan', 'Urvan', 'Versa', 'Wingroad', 'X-Trail', 'Xterra'
			),
	);

	switch ($action)
	{
		case 'update' :
			// Run this when updating
		break;

		case 'install' :
			// Run this when installing/updating
			if ($umil->table_exists(GARAGE_MAKES_TABLE) && $umil->table_exists(GARAGE_MODELS_TABLE))
			{
				$make_id = 1; 
				foreach ($model_data as $make => $model_ary)
				{
					$make_ary[] = array(
						'id'		=> $make_id,
						'make'		=> $make,
						'pending'	=> 0,
					);
					foreach ($model_ary as $model)
					{
						$mdl_ary[] = array(
							'make_id'	=> $make_id,
							'model'		=> $model,
							'pending'	=> 0,
						);
					}
					$make_id++;
				}

				$db->sql_multi_insert(GARAGE_MAKES_TABLE, $make_ary);
				$db->sql_multi_insert(GARAGE_MODELS_TABLE, $mdl_ary);
			}
		break;

		case 'uninstall' :
			// Run this when uninstalling
		break;
	}
}

/*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function garage_data_entry($action, $version)
{
	$install_makes = request_var('install_makes', true);
	$install_categories = request_var('install_categories', true);

	garage_config_entries($action, $version);

	if ($install_makes)
	{
		garage_model_entries($action, $version);
	}
	if ($install_makes)
	{
		garage_category_entries($action, $version);
	}

	return 'INSERT_REQUIRED_DATA';
}

?>
