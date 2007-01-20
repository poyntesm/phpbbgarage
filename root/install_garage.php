<?php
/***************************************************************************
 *                              install_garage.php
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

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/garage');

//Get Mode Required For Page
$mode	= request_var('mode', '');

$page_title = 'Installing Vehicle Garage Version 2.0.0';

// Output the page
page_header($page_title);

$template->set_filenames(array(
	'body' =>  'message_body.html')
);

//Setup All The Required SQL Arrays
$required_sql = array();
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_vehicles (
		`id` int(10) unsigned NOT NULL auto_increment,
		`user_id` int(10) NOT NULL default '0',
		`made_year` varchar(4) NOT NULL default '2003',
		`engine_type` varchar(32) NOT NULL default '',
		`colour` varchar(128) default NULL,
		`mileage` int(10) unsigned NOT NULL default '0',
		`mileage_unit` varchar(32) NOT NULL default 'Miles',
		`price` int(10) unsigned default NULL,
		`currency` varchar(32) NOT NULL default 'USD',
		`comments` varchar(255) default NULL,
		`views` int(10) unsigned NOT NULL default '0',
		`date_created` int(10) default NULL,
		`date_updated` int(10) default NULL,
		`make_id` int(10) unsigned NOT NULL default '0',
		`model_id` int(10) unsigned NOT NULL default '0',
		`main_vehicle` tinyint(1) NOT NULL default '0',
		`weighted_rating` double NOT NULL default '0',
		`pending` tinyint(1) NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `date_created` (`date_created`),
		KEY `date_updated` (`date_updated`),
		KEY `user_id` (`user_id`),
		KEY `views` (`views`)
	)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_business (
		`id` int(10) unsigned NOT NULL auto_increment,
		`title` varchar(255) default NULL,
		`address` varchar(255) default NULL,
		`telephone` varchar(32) default NULL,
		`fax` varchar(32) default NULL,
		`website` varchar(255) default NULL,
		`email` varchar(32) default NULL,
		`opening_hours` varchar(255) default NULL,
		`type` varchar(32) default NULL,
		`pending` tinyint(1) NOT NULL default '0',
		`comments` text,
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_categories (
		`id` int(10) unsigned NOT NULL auto_increment,
		`title` varchar(255) NOT NULL default '',
		`field_order` tinyint(4) unsigned default NULL,
		PRIMARY KEY  (`id`),
		KEY `title` (`title`(100)),
		KEY `id` (`id`,`title`(100))
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_config (
		`config_name` varchar(255) NOT NULL default '',
		`config_value` varchar(255) NOT NULL default '',
		PRIMARY KEY  (`config_name`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_vehicles_gallery (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`image_id` int(10) unsigned NOT NULL default '0',
		`hilite` tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_modifications_gallery (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`modification_id` int(10) unsigned NOT NULL default '0',
		`image_id` int(10) unsigned NOT NULL default '0',
		`hilite` tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_quartermiles_gallery (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`quartermile_id` int(10) unsigned NOT NULL default '0',
		`image_id` int(10) unsigned NOT NULL default '0',
		`hilite` tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_dynoruns_gallery (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`dynorun_id` int(10) unsigned NOT NULL default '0',
		`image_id` int(10) unsigned NOT NULL default '0',
		`hilite` tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_laps_gallery (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`lap_id` int(10) unsigned NOT NULL default '0',
		`image_id` int(10) unsigned NOT NULL default '0',
		`hilite` tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_guestbooks (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`author_id` mediumint(8) NOT NULL default '0',
		`post_date` int(10) NOT NULL default '0',
		`ip_address` varchar(16) NOT NULL default '',
		`bbcode_bitfield` varchar(255) NOT NULL default '',
		`bbcode_uid` varchar(5) NOT NULL default '',
		`post` text,
		PRIMARY KEY  (`id`),
		KEY `vehicle_id` (`vehicle_id`),
		KEY `author_id` (`author_id`),
		KEY `post_date` (`post_date`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_images (
		`attach_id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`attach_location` varchar(255) NOT NULL default '',
		`attach_hits` int(10) unsigned NOT NULL default '0',
		`attach_ext` varchar(10) NOT NULL default '',
		`attach_file` varchar(255) NOT NULL default '',
		`attach_thumb_location` varchar(128) NOT NULL default '',
		`attach_thumb_width` smallint(5) NOT NULL default '0',
		`attach_thumb_height` smallint(5) NOT NULL default '0',
		`attach_is_image` tinyint(1) NOT NULL default '0',
		`attach_date` int(10) NOT NULL default '0',
		`attach_filesize` int(10) NOT NULL default '0',
		`attach_thumb_filesize` int(10) NOT NULL default '0',
		PRIMARY KEY  (`attach_id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_premiums (
		`id` int(10) NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned default NULL,
		`business_id` int(10) unsigned default NULL,
		`premium` int(10) unsigned default NULL,
		`cover_type` varchar(255) default NULL,
		`comments` text,
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_makes (
		`id` int(10) unsigned NOT NULL auto_increment,
		`make` varchar(255) NOT NULL default '',
		`pending` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`),
		KEY `make` (`make`(64))
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_models (
		`id` int(10) unsigned NOT NULL auto_increment,
		`make_id` int(10) unsigned NOT NULL default '0',
		`model` varchar(255) NOT NULL default '',
		`pending` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`),
		KEY `make_id` (`make_id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_modifications (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`user_id` int(10) NOT NULL default '0',
		`category_id` int(10) unsigned NOT NULL default '0',
		`manufacturer_id` int(10) unsigned NOT NULL default '0',
		`product_id` int(10) unsigned NOT NULL default '0',
		`price` int(10) unsigned NOT NULL default '0',
		`install_price` int(10) unsigned NOT NULL default '0',
		`product_rating` tinyint(2) default NULL,
		`purchase_rating` tinyint(2) default NULL,
		`install_rating` tinyint(2) default NULL,
		`shop_id` int(10) default NULL,
		`installer_id` int(10) default NULL,
		`comments` text,
		`install_comments` text,
		`date_created` int(10) default NULL,
		`date_updated` int(10) default NULL,
		PRIMARY KEY  (`id`),
		KEY `user_id` (`user_id`),
		KEY `vehicle_id_2` (`vehicle_id`,`category_id`),
		KEY `category_id` (`category_id`),
		KEY `vehicle_id` (`vehicle_id`),
		KEY `date_created` (`date_created`),
		KEY `date_updated` (`date_updated`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_products (
		`id` int(10) unsigned NOT NULL auto_increment,
		`business_id` int(10) default NULL,
		`category_id` int(10) unsigned NOT NULL default '0',
		`title` varchar(255) NOT NULL default '',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_quartermiles (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`rt` decimal(6,3) default NULL,
		`sixty` decimal(6,3) default NULL,
		`three` decimal(6,3) default NULL,
		`eighth` decimal(6,3) default NULL,
		`eighthmph` decimal(6,3) default NULL,
		`thou` decimal(6,3) default NULL,
		`quart` decimal(6,3) default NULL,
		`quartmph` decimal(6,3) default NULL,
		`pending` tinyint(1) NOT NULL default '1',
		`dynorun_id` int(10) unsigned default NULL,
		`date_created` int(10) default NULL,
		`date_updated` int(10) default NULL,
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_dynoruns (
		`id` int(10) unsigned NOT NULL auto_increment,
		`vehicle_id` int(10) unsigned NOT NULL default '0',
		`dynocentre_id` int(10) unsigned NOT NULL default '0',
		`bhp` decimal(6,2) default NULL,
		`bhp_unit` varchar(32) default NULL,
		`torque` decimal(6,2) default NULL,
		`torque_unit` varchar(32) default NULL,
		`boost` decimal(6,2) default NULL,
		`boost_unit` varchar(32) default NULL,
		`nitrous` int(10) default NULL,
		`peakpoint` decimal(7,3) default NULL,
		`date_created` int(10) default NULL,
		`date_updated` int(10) default NULL,
		`pending` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`)
		)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_ratings (
		`id` int(10) NOT NULL auto_increment,
		`vehicle_id` int(10) NOT NULL default '0',
		`rating` int(10) NOT NULL default '0',
		`user_id` int(10) NOT NULL default '0',
		`rate_date` int(10) default NULL,
		PRIMARY KEY  (`id`)
	)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_tracks (
		`id` int(10) NOT NULL auto_increment,
		`title` varchar(255) NOT NULL default '',
		`length` varchar(32) default NULL,
		`mileage_unit` varchar(32) default NULL,
		`pending` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`)
	)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_laps (
		`id` int(10) NOT NULL auto_increment,
		`vehicle_id` int(10) NOT NULL default '0',
		`track_id` int(10) NOT NULL default '0',
		`condition_id` int(10) NOT NULL default '0',
		`type_id` int(10) NOT NULL default '0',
		`minute` int(2) NOT NULL default '0',
		`second` int(2) NOT NULL default '0',
		`millisecond` int(2) NOT NULL default '0',
		`pending` tinyint(1) NOT NULL default '1',
		PRIMARY KEY  (`id`)
	)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_service_history (
		`id` int(10) NOT NULL auto_increment,
		`vehicle_id` int(10) NOT NULL default '0',
		`garage_id` int(10) NOT NULL default '0',
		`type_id` int(10) NOT NULL default '0',
		`price` int(10) unsigned NOT NULL default '0',
		`rating` int(10) NOT NULL default '0',
		`mileage` int(10) NOT NULL default '0',
		`date_created` int(10) default NULL,
		`date_updated` int(10) default NULL,
		PRIMARY KEY  (`id`)
	)";
$required_sql[] = "CREATE TABLE " . $table_prefix . "garage_blog (
		`id` int(10) NOT NULL auto_increment,
		`vehicle_id` int(10) NOT NULL default '0',
		`user_id` int(10) NOT NULL default '0',
		`bbcode_bitfield` varchar(255) NOT NULL default '',
		`bbcode_uid` varchar(5) NOT NULL default '',
		`blog_text` medium NOT NULL default '',
		PRIMARY KEY  (`id`)
	)";
//Required Configuration Options
$params = array(
//General Config Data
	'cars_per_page' 			=> '30',
	'year_start' 				=> '1980',
	'year_end' 				=> '1',
	'enable_user_submit_make' 		=> '1',
	'enable_user_submit_model' 		=> '1',
	'dateformat' 				=> 'd M Y H:i',
	'version' 				=> '2.0.0',
	'default_make' 				=> '',
       	'default_model' 			=> '',
       	'integrate_memberlist' 			=> '1',
       	'integrate_viewtopic' 			=> '1',
	'integrate_profile' 			=> '1',
	'profile_thumbs' 			=> '1',
	'enable_pm_pending_notify'		=> '1',
	'enable_email_pending_notify'		=> '1',
	'enable_pm_pending_notify_optout'	=> '1',
	'enable_email_pending_notify_optout'	=> '1',
	'enable_vehicle_approval'		=> '1',

//Menu Config Data
	'enable_index_menu' 			=> '1',
	'enable_browse_menu' 			=> '1',
	'enable_search_menu' 			=> '1',
	'enable_insurance_review_menu' 		=> '1',
	'enable_garage_review_menu' 		=> '1',
	'enable_shop_review_menu' 		=> '1',
	'enable_quartermile_menu' 		=> '1',
	'enable_dynorun_menu' 			=> '1',
	'enable_garage_header' 			=> '1',
	'enable_quartermile_header' 		=> '1',
	'enable_dynorun_header' 		=> '1',
	'enable_latest_vehicle_index' 		=> '1',
	'latest_vehicle_index_limit' 		=> '10',

//Index Config Data
	'enable_featured_vehicle' 		=> '1',
	'index_columns'				=> '2',
	'enable_user_index_columns'		=> '1',
	'featured_vehicle_id'			=> '1',
	'featured_vehicle_random' 		=> '0',
	'featured_vehicle_from_block'		=> '',
	'featured_vehicle_description'		=> '',
	'enable_newest_vehicle' 		=> '1',
	'newest_vehicle_limit'			=> '5',
	'enable_updated_vehicle'		=> '1',
	'updated_vehicle_limit'			=> '5',
	'enable_newest_modification'		=> '1',
	'newest_modification_limit'		=> '5',
	'enable_updated_modification'		=> '1',
	'updated_modification_limit'		=> '5',
	'enable_most_modified'			=> '1',
	'most_modified_limit'			=> '5',
	'enable_most_spent'			=> '1',
	'most_spent_limit'			=> '5',
	'enable_most_viewed'			=> '1',
	'most_viewed_limit'			=> '5',
	'enable_last_commented'			=> '1',
	'last_commented_limit'			=> '5',
	'enable_top_dynorun'			=> '1',
	'top_dynorun_limit'			=> '5',
	'enable_top_quartermile'		=> '1',
	'top_quartermile_limit'			=> '5',
	'enable_top_rating'			=> '1',
	'top_rating_limit'			=> '5',

//Image Config Data
	'enable_images'				=> '1',
	'enable_vehicle_images'			=> '1',
	'enable_modification_images'		=> '1',
	'enable_quartermile_images'		=> '1',
	'enable_dynorun_images'			=> '1',
	'enable_lap_images'			=> '1',
	'enable_uploaded_images' 		=> '1',
	'enable_remote_images' 			=> '1',
	'remote_timeout' 			=> '60',	
	'enable_mod_gallery' 			=> '1',
	'mod_gallery_limit' 			=> '12',
	'max_image_kbytes' 			=> '1024',
	'max_image_resolution' 			=> '1024',
	'thumbnail_resolution' 			=> '150',

//Quartermile Config Data
	'enable_quartermile' 			=> '1',
	'enable_quartermile_approval' 		=> '1',
	'enable_quartermile_image_required'	=> '1',
	'quartermile_image_required_limit'	=> '13',

//Dynorun Config Data
	'enable_dynorun' 			=> '1',
	'enable_dynorun_approval' 		=> '1',
	'enable_dynorun_image_required' 	=> '1',
	'dynorun_image_required_limit' 		=> '300',

//Track & Lap Config Data
	'enable_tracktime' 			=> '1',
	'enable_user_add_track'			=> '1',
	'enable_track_approval' 		=> '1',
	'enable_lap_approval' 			=> '1',

//Insurance Config Data
	'enable_insurance' 			=> '1',
	'enable_insurance_search' 		=> '1',

//Business Config Data
	'enable_user_submit_business' 		=> '1',
	'enable_business_approval' 		=> '1',

//Ratings Config Data
	'rating_permanent' 			=> '0',
       	'rating_always_updateable' 		=> '1',
	'minimum_ratings_required' 		=> '5',

//Guestbook Config Data
	'enable_guestbooks' 			=> '1',
	'enable_guestbooks_bbcode' 		=> '1',
	'enable_guestbooks_comment_approval' 	=> '1',

//Product Config Data
	'enable_user_submit_product'		=> '1',
	'enable_product_approval'		=> '1',
	'enable_product_search'			=> '1',

//Quota Config Data
	'max_user_cars' 			=> '1',	
	'max_upload_images' 			=> '5',
	'max_remote_images' 			=> '5',
	'private_add_quota' 			=> '',
	'private_upload_quota' 			=> '',
	'private_remote_quota' 			=> '',
);
while( list($config_name, $config_value) = @each($params) )
{
	$required_sql[] = "INSERT INTO " . $table_prefix . "garage_config (config_name, config_value) VALUES ('" . $config_name . "', '" . $config_value . "')";
}
//Required SQL For User Preferences
$required_sql[] = "ALTER TABLE " . $table_prefix . "users ADD `user_garage_index_columns` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '2'";
$required_sql[] = "ALTER TABLE " . $table_prefix . "users ADD `user_garage_guestbook_email_notify` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1'";
$required_sql[] = "ALTER TABLE " . $table_prefix . "users ADD `user_garage_guestbook_pm_notify` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1'";
$required_sql[] = "ALTER TABLE " . $table_prefix . "users ADD `user_garage_mod_email_optout` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
$required_sql[] = "ALTER TABLE " . $table_prefix . "users ADD `user_garage_mod_pm_optout` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
//Required SQL For Images
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_vehicle_img_attached` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_slip_img_attached` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_icon_garage` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_main_menu` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_browse` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_search` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_quartermile_table` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_lap_table` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_dynorun_table` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_garage_review` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_shop_review` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_insurance_review` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_create_vehicle` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_edit_vehicle` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_delete_vehicle` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_view_vehicle` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_modification` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_insurance` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_dynorun` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_quartermile` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_lap` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_add_service` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_manage_gallery` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_edit` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_delete` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_main_vehicle` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_no_thumb` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_show_details` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_hide_details` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_move_up` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_move_down` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_set_approved` text NOT NULL";
$required_sql[] = "ALTER TABLE " . $table_prefix . "styles_imageset ADD `garage_set_pending` text NOT NULL";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_vehicle_img_attached = 'garage_camera.gif*13*18'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_slip_img_attached = 'garage_slip.gif*13*12'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_icon_garage = '{LANG}/icon_garage.gif*18*59'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_main_menu = '{LANG}/garage_main_menu.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_browse = '{LANG}/garage_browse.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_search = '{LANG}/garage_search.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_lap_table = '{LANG}/garage_lap_table.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_dynorun_table = '{LANG}/garage_dynorun_table.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_garage_review = '{LANG}/garage_garage_review.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_shop_review = '{LANG}/garage_shop_review.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_insurance_review = '{LANG}/garage_insurance_review.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_create_vehicle = '{LANG}/garage_create_vehicle.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_edit_vehicle = '{LANG}/garage_edit_vehicle.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_delete_vehicle = '{LANG}/garage_delete_vehicle.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_view_vehicle = '{LANG}/garage_view_vehicle.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_modification = '{LANG}/garage_add_modification.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_insurance = '{LANG}/garage_add_insurance.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_dynorun = '{LANG}/garage_add_dynorun.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_quartermile = '{LANG}/garage_add_quartermile.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_lap = '{LANG}/garage_add_lap.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_add_service = '{LANG}/garage_add_service.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_manage_gallery = '{LANG}/garage_manage_gallery.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_edit = '{LANG}/garage_edit.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_delete = '{LANG}/garage_delete.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_main_vehicle = '{LANG}/garage_main_vehicle.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_no_thumb = '{LANG}/garage_no_thumb.gif*33*130'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_show_details = '{LANG}/garage_show_details.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_hide_details = '{LANG}/garage_hide_details.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_move_up = '{LANG}/garage_move_up.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_move_down = '{LANG}/garage_move_down.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_set_approved = '{LANG}/garage_set_approved.gif*21*34'";
$required_sql[] = "UPDATE " . $table_prefix . "styles_imageset SET garage_set_pending = '{LANG}/garage_set_pending.gif*21*34'";

$categories_sql = array();
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (1, 'Engine', 1)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (2, 'Transmission', 2)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (3, 'Suspension', 3)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (4, 'Brakes', 4)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (5, 'Interior', 5)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (6, 'Exterior', 6)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (7, 'Audio', 7)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (8, 'Alloys &amp; Tyres', 8)";
$categories_sql[] = "INSERT INTO " . $table_prefix . "garage_categories VALUES (9, 'Security', 9)";

//Setup Default Make List
$makes_models_sql = array();
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (1, 'AC', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (2, 'Acura', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (3, 'Aixam', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (4, 'Alfa-Romeo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (5, 'Asia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (6, 'Aston-Martin', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (7, 'Audi', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (8, 'Austin', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (9, 'Bentley', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (10, 'BMW', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (11, 'Bristol', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (12, 'Cadillac', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (13, 'Caterham', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (14, 'Chevrolet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (15, 'Chrysler', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (16, 'Citroen', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (17, 'Daewoo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (18, 'Daihatsu', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (19, 'Daimler', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (20, 'Datsun', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (21, 'Delorian', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (22, 'Dodge', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (23, 'Ferrari', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (24, 'Fiat', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (25, 'Ford', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (26, 'FSO', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (27, 'Ginetta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (28, 'Griffon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (29, 'Hillman', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (30, 'HMC', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (31, 'Honda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (32, 'Hummer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (33, 'Hyundai', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (34, 'ISO', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (35, 'Isuzu', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (36, 'Jaguar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (37, 'Jeep', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (38, 'Jensen', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (39, 'Kia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (40, 'Lada', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (41, 'Lamborghini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (42, 'Lancia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (43, 'Land Rover', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (44, 'Lexus', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (45, 'Ligier', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (46, 'Lincoln', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (47, 'Lotus', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (48, 'Marcos', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (49, 'Maserati', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (50, 'Maybach', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (51, 'Mazda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (52, 'McLaren', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (53, 'Mercedes-Benz', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (54, 'MG', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (55, 'Microcar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (56, 'Mini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (57, 'Mitsubishi', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (58, 'Morgan', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (59, 'Morris', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (60, 'Noble', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (61, 'Opel', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (62, 'Pagani', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (63, 'Panther', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (64, 'Perodua', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (65, 'Peugeot', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (66, 'Pontiac', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (67, 'Porsche', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (68, 'Proton', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (69, 'Reliant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (70, 'Renault', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (71, 'Riley', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (72, 'Rolls Royce', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (73, 'Rover', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (74, 'Saab', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (75, 'Sao', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (76, 'Seat', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (77, 'Singer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (78, 'Skoda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (79, 'Smart', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (80, 'SsangYong', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (81, 'Subaru', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (82, 'Sunbeam', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (83, 'Suzuki', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (84, 'Talbot', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (85, 'Tata', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (86, 'Toyota', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (87, 'Triumph', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (88, 'TVR', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (89, 'Ultima', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (90, 'Vauxhall', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (91, 'Volkswagen', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (92, 'Volvo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (93, 'Westfield', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (94, 'Yugo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_makes VALUES (95, 'Nissan', 0)";

//Setup Default Model List
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (1, 1, 'Ace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (2, 1, 'Cobra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (3, 1, 'Superblower', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (4, 2, 'CL', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (5, 2, 'CL Type S', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (6, 2, 'Integra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (7, 2, 'Legend', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (8, 2, 'MDX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (9, 2, 'NSX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (10, 2, 'RL', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (11, 2, 'RL-Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (12, 2, 'RSX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (13, 2, 'SLX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (14, 2, 'TL', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (15, 2, 'TL Type S', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (16, 2, 'TSX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (17, 2, 'Vigor', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (18, 3, '400', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (19, 3, '500', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (20, 4, '145', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (21, 4, '146', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (22, 4, '147', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (23, 4, '155', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (24, 4, '156', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (25, 4, '164', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (26, 4, '166', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (27, 4, '33', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (28, 4, '75', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (29, 4, 'Alfasud', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (30, 4, 'Giulietta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (31, 4, 'GTV', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (32, 4, 'Spider', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (33, 4, 'Sportwagon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (34, 4, 'Sprint', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (35, 4, 'S2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (36, 5, 'Rocsta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (37, 6, 'DB2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (38, 6, 'DB4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (39, 6, 'DB5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (40, 6, 'DB6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (41, 6, 'DB7', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (42, 6, 'DBS', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (43, 6, 'Lagonda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (44, 6, 'V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (45, 6, 'Vanquish', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (46, 6, 'Vantage', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (47, 6, 'Virage', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (48, 6, 'Volante', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (49, 7, '100', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (50, 7, '100 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (51, 7, '200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (52, 7, '80', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (53, 7, '90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (54, 7, 'A2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (55, 7, 'A3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (56, 7, 'A4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (57, 7, 'A4 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (58, 7, 'A6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (59, 7, 'A6 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (60, 7, 'A8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (61, 7, 'Allroad', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (62, 7, 'Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (63, 7, 'Cabriolet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (64, 7, 'Convertible', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (65, 7, 'Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (66, 7, 'Quattro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (67, 7, 'RS2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (68, 7, 'RS4 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (69, 7, 'S2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (70, 7, 'S3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (71, 7, 'S4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (72, 7, 'S4 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (73, 7, 'S6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (74, 7, 'S6 Avant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (75, 7, 'S8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (76, 7, 'TT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (77, 7, 'V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (78, 8, 'Allegro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (79, 8, 'Healy', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (80, 8, 'Maestro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (81, 8, 'Maxi', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (82, 8, 'Metro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (83, 8, 'Mini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (84, 8, 'Montego', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (85, 8, 'Princess', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (86, 9, 'Arnage', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (87, 9, 'Azure', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (88, 9, 'Brooklands', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (89, 9, 'Continental', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (90, 9, 'Corniche', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (91, 9, 'Eight', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (92, 9, 'Mulsanne', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (93, 9, 'Series II', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (94, 9, 'T Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (95, 9, 'Turbo R', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (96, 10, '1 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (97, 10, '3 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (98, 10, '5 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (99, 10, '6 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (100, 10, '7 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (101, 10, '8 Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (102, 10, 'Alpina', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (103, 10, 'M', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (104, 10, 'M3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (105, 10, 'M5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (106, 10, 'X3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (107, 10, 'X5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (108, 10, 'Z3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (109, 10, 'Z4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (110, 10, 'Z8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (111, 11, '411', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (112, 11, '412', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (113, 11, 'Blenheim', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (114, 12, 'Brougham', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (115, 12, 'Eldorado', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (116, 12, 'Escalade', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (117, 12, 'Fleetwood', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (118, 12, 'Seville', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (119, 13, 'Super 7', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (120, 13, 'Super Sprint', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (121, 14, '210', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (122, 14, 'Astro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (123, 14, 'Blazer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (124, 14, 'Camaro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (125, 14, 'Corvette', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (126, 14, 'GMC', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (127, 14, 'S10', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (128, 14, 'Silverado', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (129, 14, 'Suburban', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (130, 14, 'Tahoe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (131, 15, 'Cherokee', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (132, 15, 'Grand Cherokee', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (133, 15, 'Grand Voyager', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (134, 15, 'Jeep', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (135, 15, 'Neon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (136, 15, 'PT Cruiser', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (137, 15, 'Sebring', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (138, 15, 'Viper', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (139, 15, 'Voyager', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (140, 15, 'Wrangler', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (141, 16, '2CV', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (142, 16, 'AX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (143, 16, 'Berlingo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (144, 16, 'BX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (145, 16, 'C3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (146, 16, 'C5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (147, 16, 'C8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (148, 16, 'CX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (149, 16, 'DE', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (150, 16, 'Reflex', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (151, 16, 'Saxo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (152, 16, 'Synergie', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (153, 16, 'Visa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (154, 16, 'Xantia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (155, 16, 'XM', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (156, 16, 'Xsara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (157, 16, 'Xsara Picasso', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (158, 16, 'ZX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (159, 17, 'Espero', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (160, 17, 'Kalos', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (161, 17, 'Korando', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (162, 17, 'Lanos', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (163, 17, 'Leganza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (164, 17, 'Matiz', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (165, 17, 'Musso', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (166, 17, 'Nexia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (167, 17, 'Nubira', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (168, 17, 'Tacuma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (169, 18, 'Applause', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (170, 18, 'Charade', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (171, 18, 'Cuore', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (172, 18, 'Domino', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (173, 18, 'Fourtrak', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (174, 18, 'Grand Move', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (175, 18, 'Hijet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (176, 18, 'Mira', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (177, 18, 'Move', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (178, 18, 'Sirion', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (179, 18, 'Sportrak', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (180, 18, 'Terios', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (181, 18, 'YRV', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (182, 19, 'Double Six', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (183, 19, 'Empress', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (184, 19, 'Limousine', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (185, 19, 'Saloon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (186, 19, 'Sovereign', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (187, 19, 'Super V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (188, 19, 'V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (189, 19, 'XJ Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (190, 19, 'XJ12', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (191, 20, 'Patrol', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (192, 21, 'DMZ', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (193, 22, 'Dakota', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (194, 22, 'Durango', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (195, 22, 'Ram', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (196, 23, '246', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (197, 23, '250', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (198, 23, '308', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (199, 23, '328', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (200, 23, '330', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (201, 23, '348', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (202, 23, '355', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (203, 23, '360', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (204, 23, '365', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (205, 23, '400', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (206, 23, '412', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (207, 23, '456', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (208, 23, '512', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (209, 23, '550', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (210, 23, '575M', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (211, 23, 'Daytona', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (212, 23, 'Dino', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (213, 23, 'Enzo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (214, 23, 'F355', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (215, 23, 'F40', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (216, 23, 'Mondial', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (217, 23, 'Testarossa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (218, 23, 'F50', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (219, 24, '124', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (220, 24, '126', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (221, 24, '130', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (222, 24, '500', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (223, 24, 'Barchetta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (224, 24, 'Bravo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (225, 24, 'Brava', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (226, 24, 'Cinquecento', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (227, 24, 'Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (228, 24, 'Croma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (229, 24, 'Doblo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (230, 24, 'Marea', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (231, 24, 'Marea Weekend', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (232, 24, 'Multipla', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (233, 24, 'Panda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (234, 24, 'Punto', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (235, 24, 'Regato', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (236, 24, 'Seicento', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (237, 24, 'Spider', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (238, 24, 'Stilo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (239, 24, 'Tempra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (240, 24, 'Tipo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (241, 24, 'Ulysse', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (242, 24, 'Uno', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (243, 24, 'X19', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (244, 25, 'Capri', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (245, 25, 'Consul', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (246, 25, 'Cortina', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (247, 25, 'Cougar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (248, 25, 'Escort', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (249, 25, 'Explorer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (250, 25, 'F150', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (251, 25, 'F350', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (252, 25, 'Falcon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (253, 25, 'Fiesta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (254, 25, 'Focus', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (255, 25, 'Fusion', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (256, 25, 'Galaxy', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (257, 25, 'Granada', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (258, 25, 'Ka', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (259, 25, 'Maverick', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (260, 25, 'Mondeo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (261, 25, 'Mustang', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (262, 25, 'Orion', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (263, 25, 'Probe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (264, 25, 'Puma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (265, 25, 'Ranger', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (266, 25, 'Sapphire', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (267, 25, 'Scorpio', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (268, 25, 'Sierra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (269, 25, 'Streetka', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (270, 25, 'Taurus', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (271, 26, 'Caro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (272, 27, 'G Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (273, 28, '110', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (274, 29, 'Imp', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (275, 29, 'Minx', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (276, 30, 'Mark IV SE', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (277, 31, 'Accord', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (278, 31, 'Aerodeck', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (279, 31, 'Ballade', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (280, 31, 'Beat', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (281, 31, 'Civic', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (282, 31, 'Concerto', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (283, 31, 'CR-V', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (284, 31, 'CR-X', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (285, 31, 'HR-V', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (286, 31, 'Insight', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (287, 31, 'Integra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (288, 31, 'Jazz', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (289, 31, 'Legend', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (290, 31, 'Logo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (291, 31, 'NSX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (292, 31, 'Prelude', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (293, 31, 'S2000', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (294, 31, 'Shuttle', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (295, 31, 'Stream', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (296, 32, 'H2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (297, 33, 'Accent', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (298, 33, 'Amica', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (299, 33, 'Atoz', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (300, 33, 'Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (301, 33, 'Elantra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (302, 33, 'Getz', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (303, 33, 'Lantra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (304, 33, 'Matrix', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (305, 33, 'Pony', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (306, 33, 'S-Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (307, 33, 'Santa Fe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (308, 33, 'Sonata', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (309, 33, 'Stellar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (310, 33, 'Trajet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (311, 33, 'X2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (312, 33, 'XG30', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (313, 34, 'Lele', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (314, 35, 'Piazza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (315, 35, 'TF', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (316, 35, 'Trooper', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (317, 36, 'E-Type', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (318, 36, 'Mark I', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (319, 36, 'Mark II', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (320, 36, 'S-Type', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (321, 36, 'Sovereign', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (322, 36, 'V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (323, 36, 'X-Type', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (324, 36, 'XJ Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (325, 36, 'XJS', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (326, 36, 'XK', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (327, 37, 'Cherokee', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (328, 37, 'Grand Cherokee', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (329, 37, 'Renegade', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (330, 37, 'Wrangler', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (331, 38, 'Interceptor', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (332, 38, 'S-V8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (333, 39, 'Carens', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (334, 39, 'Clarus', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (335, 39, 'Magentis', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (336, 39, 'Mentor', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (337, 39, 'Pride', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (338, 39, 'Rio', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (339, 39, 'Sedona', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (340, 39, 'Shuma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (341, 39, 'Sorento', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (342, 39, 'Sportage', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (343, 40, '1500', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (344, 40, 'Niva', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (345, 40, 'Riva', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (346, 40, 'Samara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (347, 41, 'Countach', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (348, 41, 'Diablo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (349, 41, 'LM', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (350, 41, 'Murcielago', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (351, 41, 'urraco', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (352, 42, 'Beta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (353, 42, 'Dedra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (354, 42, 'Delta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (355, 42, 'Monte Carlo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (356, 42, 'Prisma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (357, 42, 'Thema', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (358, 42, 'Y10', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (359, 43, 'Defender', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (360, 43, 'Discovery', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (361, 43, 'Freelander', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (362, 43, 'Lightweight', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (363, 43, 'Range Rover', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (364, 43, 'Series II', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (365, 43, 'Series III', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (366, 44, 'GS 300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (367, 44, 'IS 300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (368, 44, 'LS 430', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (369, 44, 'RX 300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (370, 44, 'SC 430', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (371, 44, 'Soarer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (372, 45, 'Ambra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (373, 46, 'Blackwood', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (374, 46, 'Navigator', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (375, 46, 'Towncar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (376, 47, '340R', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (377, 47, 'Carlton', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (378, 47, 'Eclat', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (379, 47, 'Elan', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (380, 47, 'Elise', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (381, 47, 'Elite', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (382, 47, 'Esprit', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (383, 47, 'Excel', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (384, 47, 'Exige', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (385, 48, 'LM', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (386, 48, 'Mantara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (387, 48, 'Mantaray', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (388, 48, 'Mantis', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (389, 48, 'Mantula', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (390, 48, 'Martina', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (391, 49, '222', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (392, 49, '320', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (393, 49, '3200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (394, 49, 'BiTurbo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (395, 49, 'Convertible', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (396, 49, 'Ghibli', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (397, 49, 'Kharif', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (398, 49, 'Quaddroporte', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (399, 49, 'Spyder', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (400, 50, '57', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (401, 50, '62', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (402, 51, '121', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (403, 51, '323', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (404, 51, '626', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (405, 51, 'Demio', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (406, 51, 'Eunos', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (407, 51, 'Mazda 2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (408, 51, 'Mazda 6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (409, 51, 'MPV', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (410, 51, 'MX-3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (411, 51, 'MX-5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (412, 51, 'MX-6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (413, 51, 'Premacy', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (414, 51, 'RX-7', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (415, 51, 'Tribute', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (416, 51, 'Xedos', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (417, 52, 'M6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (418, 52, 'F1', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (419, 53, '180', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (420, 53, '190', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (421, 53, '200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (422, 53, '220', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (423, 53, '230', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (424, 53, '240', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (425, 53, '260', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (426, 53, '280', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (427, 53, '300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (428, 53, '310', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (429, 53, '320', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (430, 53, '350', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (431, 53, '380', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (432, 53, '400', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (433, 53, '410', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (434, 53, '420', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (435, 53, '450', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (436, 53, '500', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (437, 53, '560', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (438, 53, '600', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (439, 53, 'A Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (440, 53, 'AMG', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (441, 53, 'C Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (442, 53, 'CE Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (443, 53, 'CL', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (444, 53, 'CLK', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (445, 53, 'E Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (446, 53, 'G Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (447, 53, 'M Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (448, 53, 'S Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (449, 53, 'SE Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (450, 53, 'SL Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (451, 53, 'SLK', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (452, 53, 'V Class', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (453, 53, 'Vaneo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (454, 54, 'MGB', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (455, 54, 'MGB GT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (456, 54, 'MGF', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (457, 54, 'Midget', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (458, 54, 'RV8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (459, 54, 'TF', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (460, 54, 'ZR', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (461, 54, 'ZS', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (462, 54, 'ZT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (463, 54, 'ZT-T', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (464, 55, 'Virgo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (465, 56, 'Cooper', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (466, 56, 'Mini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (467, 56, 'One', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (468, 57, '3000GT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (469, 57, 'Carisma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (470, 57, 'Challenger', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (471, 57, 'Chariot', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (472, 57, 'Colt', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (473, 57, 'Cordia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (474, 57, 'Delcia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (475, 57, 'FTO', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (476, 57, 'Galant', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (477, 57, 'L200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (478, 57, 'Lancer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (479, 57, 'Legnum', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (480, 57, 'Pajero', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (481, 57, 'Ralliart', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (482, 57, 'RVR', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (483, 57, 'Shogun', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (484, 57, 'Shogun Pinin', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (485, 57, 'Sigma', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (486, 57, 'Space Runner', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (487, 57, 'Space Star', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (488, 57, 'Space Wagon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (489, 57, 'Starion', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (490, 57, 'Strada', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (491, 58, '4/4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (492, 58, 'Aero', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (493, 58, 'Plus 4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (494, 58, 'Plus 8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (495, 59, 'Ital', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (496, 59, 'Mini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (497, 59, 'Minor', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (498, 60, 'M12', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (499, 61, 'Commodore', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (500, 61, 'Corsa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (501, 61, 'Kadette', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (502, 61, 'Manta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (503, 61, 'Monza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (504, 61, 'omega', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (505, 61, 'Zafira', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (506, 62, 'Zonda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (507, 63, 'Kallista', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (508, 64, 'Kelisa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (509, 64, 'Kenari', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (510, 64, 'Nippa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (511, 65, '104', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (512, 65, '106', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (513, 65, '205', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (514, 65, '206', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (515, 65, '305', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (516, 65, '306', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (517, 65, '307', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (518, 65, '309', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (519, 65, '405', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (520, 65, '406', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (521, 65, '504', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (522, 65, '505', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (523, 65, '605', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (524, 65, '607', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (525, 65, '806', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (526, 65, '807', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (527, 66, 'Firebird', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (528, 66, 'Trans Am', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (529, 67, '356', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (530, 67, '911', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (531, 67, '912', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (532, 67, '924', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (533, 67, '928', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (534, 67, '944', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (535, 67, '968', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (536, 67, 'Boxster', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (537, 67, 'Carrera GT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (538, 67, 'Cayenne', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (539, 68, 'Compact', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (540, 68, 'Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (541, 68, 'GL', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (542, 68, 'GLS', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (543, 68, 'Impian', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (544, 68, 'Persona', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (545, 68, 'Satria', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (546, 68, 'Wira', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (547, 69, 'Rialto', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (548, 69, 'Robin', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (549, 69, 'Sabre', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (550, 69, 'Scimitar', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (551, 70, '4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (552, 70, '5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (553, 70, '6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (554, 70, '9', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (555, 70, '11', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (556, 70, '12', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (557, 70, '14', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (558, 70, '15', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (559, 70, '16', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (560, 70, '17', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (561, 70, '18', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (562, 70, '19', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (563, 70, '20', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (564, 70, '21', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (565, 70, '25', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (566, 70, '30', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (567, 70, 'A610', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (568, 70, 'Avantime', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (569, 70, 'Clio', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (570, 70, 'Espace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (571, 70, 'Fuego', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (572, 70, 'Grand Espace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (573, 70, 'GTA', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (574, 70, 'Kangoo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (575, 70, 'Laguna', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (576, 70, 'Megane', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (577, 70, 'Safrane', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (578, 70, 'Scenic', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (579, 70, 'Scenic RX4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (580, 70, 'Sport Spider', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (581, 70, 'Vel Satis', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (582, 71, 'Elf', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (583, 71, 'RM Series', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (584, 72, '20/25', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (585, 72, '25/30', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (586, 72, 'Corniche', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (587, 72, 'Pink Ward', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (588, 72, 'Phantom', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (589, 72, 'Silver Cloud', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (590, 72, 'Silver Dawn', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (591, 72, 'Silver Seraph', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (592, 72, 'Silver Shadow', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (593, 72, 'Silver Spirit', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (594, 72, 'Silver Spur', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (595, 72, 'Silver Wraith', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (596, 73, '100', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (597, 73, '200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (598, 73, '2000', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (599, 73, '2200', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (600, 73, '2300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (601, 73, '25', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (602, 73, '3500', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (603, 73, '400', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (604, 73, '45', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (605, 73, '600', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (606, 73, '75', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (607, 73, '800', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (608, 73, '90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (609, 73, 'Cabriolet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (610, 73, 'Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (611, 73, 'Maestro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (612, 73, 'Metro', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (613, 73, 'Mini', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (614, 73, 'Montego', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (615, 73, 'Sterling', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (616, 73, 'Tourer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (617, 73, 'Vitesse', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (618, 74, '9-3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (619, 74, '9-5', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (620, 74, '90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (621, 74, '900', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (622, 74, '9000', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (623, 74, '96', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (624, 74, '99', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (625, 75, 'Penza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (626, 76, 'Alhambra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (627, 76, 'Arosa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (628, 76, 'Cordoba', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (629, 76, 'Ibiza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (630, 76, 'Leon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (631, 76, 'Malaga', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (632, 76, 'Marbella', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (633, 76, 'Toledo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (634, 76, 'Vario', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (635, 77, 'Gazelle', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (636, 78, 'Fabia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (637, 78, 'Favorit', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (638, 78, 'Felicia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (639, 78, 'Octavia', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (640, 78, 'Superb', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (641, 79, 'Car', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (642, 79, 'CDI', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (643, 79, 'City Coupe', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (644, 79, 'Edition', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (645, 79, 'Passion', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (646, 79, 'Pulse', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (647, 79, 'Pure', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (648, 80, 'Korando', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (649, 80, 'Musso', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (650, 81, '1600', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (651, 81, '1800', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (652, 81, 'Forester', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (653, 81, 'Impreza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (654, 81, 'Justy', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (655, 81, 'Legacy', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (656, 81, 'SVX', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (657, 81, 'Vivio', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (658, 81, 'XT', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (659, 82, 'Alpine', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (660, 83, 'Alto', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (661, 83, 'Baleno', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (662, 83, 'cappucino', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (663, 83, 'Grand Vitara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (664, 83, 'Ignis', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (665, 83, 'Jimny', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (666, 83, 'Liana', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (667, 83, 'Samurai', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (668, 83, 'SJ', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (669, 83, 'Swift', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (670, 83, 'Vitara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (671, 83, 'Wagon-R', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (672, 83, 'X-90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (673, 84, 'Alpine', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (674, 84, 'BA75', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (675, 84, 'Horizon', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (676, 84, 'Samba', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (677, 84, 'Solara', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (678, 84, 'Sunbeam', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (679, 85, 'Safari', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (680, 86, '4 Runner', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (681, 86, 'Altezza', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (682, 86, 'Avensis', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (683, 86, 'Camry', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (684, 86, 'Carina', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (685, 86, 'Celica', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (686, 86, 'Corolla', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (687, 86, 'Corona', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (688, 86, 'Cressida', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (689, 86, 'Crown', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (690, 86, 'Estima', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (691, 86, 'Harrier', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (692, 86, 'Hiace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (693, 86, 'Hilux', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (694, 86, 'Landcruiser', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (695, 86, 'Liteace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (696, 86, 'Lucida', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (697, 86, 'MR2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (698, 86, 'Paseo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (699, 86, 'Picnic', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (700, 86, 'Prado', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (701, 86, 'Previa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (702, 86, 'Prius', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (703, 86, 'Rav 4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (704, 86, 'Sera', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (705, 86, 'Soarer', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (706, 86, 'Space Cruiser', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (707, 86, 'starlet', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (708, 86, 'Supra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (709, 86, 'Surf', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (710, 86, 'Tercel', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (711, 86, 'Townace', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (712, 86, 'Yaris', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (713, 87, 'Dolomite', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (714, 87, 'Spitfire', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (715, 87, 'Stag', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (716, 87, 'Toledo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (717, 87, 'TR4', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (718, 87, 'TR6', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (719, 87, 'TR7', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (720, 87, 'TR8', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (721, 88, '280I', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (722, 88, '3000M', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (723, 88, '350I', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (724, 88, '450', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (725, 88, 'Cerbera', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (726, 88, 'Chimera', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (727, 88, 'Griffith', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (728, 88, 'S Convertible', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (729, 88, 'S2', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (730, 88, 'S3', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (731, 88, 'T350', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (732, 88, 'Taimor', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (733, 88, 'Tamora', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (734, 88, 'Tasmin', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (735, 88, 'Tuscan', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (736, 88, 'Tuscan S', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (737, 89, 'Sport', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (738, 89, 'Spyder', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (739, 90, 'Agila', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (740, 90, 'Astra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (741, 90, 'Belmont', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (742, 90, 'Calibra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (743, 90, 'Carlton', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (744, 90, 'Cavalier', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (745, 90, 'Chevette', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (746, 90, 'Corsa', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (747, 90, 'Frontera', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (748, 90, 'Monterey', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (749, 90, 'Nova', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (750, 90, 'Omega', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (751, 90, 'Royale', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (752, 90, 'Senator', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (753, 90, 'Sintra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (754, 90, 'Tigra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (755, 90, 'Vectra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (756, 90, 'Viva', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (757, 90, 'VX220', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (758, 90, 'Zafira', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (759, 91, 'Beetle', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (760, 91, 'Bora', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (761, 91, 'Caravelle', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (762, 91, 'Corrado', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (763, 91, 'Derby', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (764, 91, 'Fastback', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (765, 91, 'Golf', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (766, 91, 'Jetta', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (767, 91, 'K70', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (768, 91, 'Karmann', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (769, 91, 'Lupo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (770, 91, 'Passat', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (771, 91, 'Phaeton', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (772, 91, 'Polo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (773, 91, 'Santana', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (774, 91, 'Scirocco', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (775, 91, 'Sharan', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (776, 91, 'Touareg', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (777, 91, 'Vento', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (778, 92, '121', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (779, 92, '122', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (780, 92, '164', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (781, 92, '240', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (782, 92, '244', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (783, 92, '245', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (784, 92, '260', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (785, 92, '264', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (786, 92, '340', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (787, 92, '360', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (788, 92, '440', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (789, 92, '460', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (790, 92, '480', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (791, 92, '740', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (792, 92, '760', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (793, 92, '850', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (794, 92, '940', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (795, 92, '960', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (796, 92, 'C70', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (797, 92, 'P1800', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (798, 92, 'S40', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (799, 92, 'S60', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (800, 92, 'S70', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (801, 92, 'S80', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (802, 92, 'S90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (803, 92, 'Torslanda', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (804, 92, 'V40', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (805, 92, 'V70', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (806, 92, 'V90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (807, 92, 'XC70', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (808, 92, 'XC90', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (809, 93, '1600', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (810, 93, '1800', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (811, 93, '7', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (812, 93, 'Mega', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (813, 93, 'Megabird', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (814, 93, 'Megablade', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (815, 93, 'Sei', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (816, 94, '45', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (817, 94, 'Tempo', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (818, 95, 'Sentra', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (819, 95, 'Altima', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (820, 95, 'Maxima', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (821, 44, 'ES 300', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (822, 44, 'GS 430', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (823, 44, 'GX 470', 0)";
$makes_models_sql[] = "INSERT INTO " . $table_prefix . "garage_models VALUES (824, 44, 'LX 470', 0)";

//Setup URLs 
$u_step1 = append_sid("{$phpbb_root_path}install_garage.$phpEx", "mode=step1");
$u_step2 = append_sid("{$phpbb_root_path}install_garage.$phpEx", "mode=step2");
$u_step3 = append_sid("{$phpbb_root_path}install_garage.$phpEx", "mode=step3");
$u_step4 = append_sid("{$phpbb_root_path}install_garage.$phpEx", "mode=step4");
$u_garage = append_sid("{$phpbb_root_path}garage.$phpEx");

//Decide What Mode The User Is Doing
switch( $mode )
{

	//Try Create Tables & Default Configuration Data, If Succesfull Allow User To Proceed To Step2
	case 'step1':

		//Execute Required SQL For Table Creation
		$errored = false;
		for( $i = 0; $i < count($required_sql); $i++ )
		{
			if( !$result = $db->sql_query ($required_sql[$i]) )
			{
				$errored = true;
				$error = $db->sql_error();
				$error_message .= '<li>' . $required_sql[$i] . '<br /> +++ <font color="#FF0000"><b>Error:</b></font> ' . $error['message'] . '</li><br />';
			}
		}

		//Setup $auth_admin class so we can add permission options
		include($phpbb_root_path . '/includes/acp/auth.' . $phpEx);
		$auth_admin = new auth_admin();

		//Lets Add The Required New Permissions
		$phpbbgarage_permissions = array(
			'local'		=> array(),
			'global'	=> array(
				'u_garage_browse',
				'u_garage_search',
				'u_garage_add_vehicle',
				'u_garage_delete_vehicle',
				'u_garage_add_modification',
				'u_garage_delete_modification',
				'u_garage_add_quartermile',
				'u_garage_delete_quartermile',
				'u_garage_add_lap',
				'u_garage_delete_lap',
				'u_garage_add_track',
				'u_garage_delete_track',
				'u_garage_add_dynorun',
				'u_garage_delete_dynorun',
				'u_garage_add_insurance',
				'u_garage_delete_insurance',
				'u_garage_add_service',
				'u_garage_delete_service',
				'u_garage_add_business',
				'u_garage_add_make_model',
				'u_garage_add_product',
				'u_garage_rate',
				'u_garage_comment',
				'u_garage_upload_image',
				'u_garage_remote_image',
				'u_garage_delete_image',
				'u_garage_deny',
				'm_garage',
			       	'a_garage')
		);
		$auth_admin->acl_add_option($phpbbgarage_permissions);

		//If Any Errors During Table Creation Step1 Display Error Message
		if ($errored)
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP1_FAILED'],
				'MESSAGE_TEXT'	=> $error_message,
			));
			break;
		}
		else
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP1_SUCCESS'],
				'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_STEP1_SUCCESS_EXPLAIN'], $u_step2, $u_step3, $u_step4)
			));
			break;
		}

	//Try Create The Default Modification Categories
	case 'step2':

		//Execute Optional SQL For Category Creation
		$errored = false;
		for( $i = 0; $i < count($categories_sql); $i++ )
		{
			if( !$result = $db->sql_query ($categories_sql[$i]) )
			{
				$errored = true;
				$error = $db->sql_error();
				$error_message .= '<li>' . $categories_sql[$i] . '<br /> +++ <font color="#FF0000"><b>Error:</b></font> ' . $error['message'] . '</li><br />';
			}
		}

		//If Any Errors During Step2 Display Error Message
		if ($errored)
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP2_FAILED'],
				'MESSAGE_TEXT'	=> $error_message,
			));
			break;
		}
		else
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP2_SUCCESS'],
				'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_STEP2_SUCCESS_EXPLAIN'], $u_step3, $u_step4)
			));
			break;
		}
		
	//Try Create The Default Modification Categories
	case 'step3':

		//Execute Optional SQL For Default Makes & Models
		$errored = false;
		for( $i = 0; $i < count($makes_models_sql); $i++ )
		{
			if( !$result = $db->sql_query ($makes_models_sql[$i]) )
			{
				$errored = true;
				$error = $db->sql_error();
				$error_message .= '<li>' . $makes_models_sql[$i] . '<br /> +++ <font color="#FF0000"><b>Error:</b></font> ' . $error['message'] . '</li><br />';
			}
		}

		//If Any Errors During Step3 Display Error Message
		if ($errored)
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP3_FAILED'],
				'MESSAGE_TEXT'	=> $error_message,
			));
			break;
		}
		else
		{
			$template->assign_vars(array(
				'MESSAGE_TITLE'	=> $user->lang['INSTALL_STEP3_SUCCESS'],
				'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_STEP3_SUCCESS_EXPLAIN'], $u_step4)
			));
			break;
		}
		
	//Finish Install
	case 'step4':

		$template->assign_vars(array(
			'MESSAGE_TITLE'	=> $user->lang['INSTALL_COMPLETE'],
			'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_COMPLETE_EXPLAIN'], $u_garage)
		));

		break;

	//Display Install Welcome Screen	
	default:

		$template->assign_vars(array(
			'MESSAGE_TITLE'	=> $user->lang['INSTALL_PHPBBGARAGE'],
			'MESSAGE_TEXT'	=> sprintf($user->lang['INSTALL_PHPBBGARAGE_EXPLAIN'], $u_step1),
		));

		break;
}


/*$sql[] = "CREATE TABLE `phpbb_garage_vehicle_fields` (
  `field_id` mediumint(8) unsigned NOT NULL auto_increment,
  `field_name` varchar(50) NOT NULL default '',
  `field_desc` varchar(255) NOT NULL default '',
  `field_type` mediumint(8) unsigned NOT NULL default '0',
  `field_ident` varchar(20) NOT NULL default '',
  `field_length` varchar(20) NOT NULL default '',
  `field_minlen` varchar(255) NOT NULL default '',
  `field_maxlen` varchar(255) NOT NULL default '',
  `field_novalue` varchar(255) NOT NULL default '',
  `field_default_value` varchar(255) NOT NULL default '0',
  `field_validation` varchar(20) NOT NULL default '',
  `field_required` tinyint(1) unsigned NOT NULL default '0',
  `field_show_on_reg` tinyint(1) unsigned NOT NULL default '0',
  `field_hide` tinyint(1) unsigned NOT NULL default '0',
  `field_no_view` tinyint(1) unsigned NOT NULL default '0',
  `field_active` tinyint(1) unsigned NOT NULL default '0',
  `field_order` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`field_id`),
  KEY `field_type` (`field_type`),
  KEY `field_order` (`field_order`)
)";

$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields` VALUES (1, '', '', 5, 'dropdown', '0', '0', '6', '1', '1', '', 1, 0, 0, 0, 1, 1)";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields` VALUES (2, '', '', 2, 'textfield', '10', '0', '20', '', '', '.*', 0, 0, 0, 0, 1, 2)";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields` VALUES (3, '', '', 1, 'numbers', '5', '0', '100', '0', '0', '', 0, 0, 0, 0, 1, 3)";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields` VALUES (4, '', '', 3, 'textarea', '5|80', '0', '1000', '', '', '.*', 0, 0, 0, 0, 1, 4)";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields` VALUES (5, '', '', 6, 'date', '10', '10', '10', ' 0- 0-   0', ' 0- 0-   0', '', 0, 0, 0, 0, 1, 5)";

$sql[] = "CREATE TABLE `phpbb_garage_vehicle_fields_data` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `rating` mediumint(8) default NULL,
  `textfield` varchar(255) default NULL,
  `numbers` bigint(20) default NULL,
  `textarea` text,
  `textarea_bbcode_uid` varchar(5) NOT NULL default '',
  `textarea_bbcode_bitfield` int(11) unsigned default NULL,
  `date` varchar(10) default NULL,
  `sdfs_bbcode_uid` varchar(5) NOT NULL default '',
  `sdfs_bbcode_bitfield` int(11) unsigned default NULL,
  PRIMARY KEY  (`user_id`)
)";

$sql[] = "CREATE TABLE `phpbb_garage_vehicle_fields_lang` (
  `field_id` mediumint(8) unsigned NOT NULL default '0',
  `lang_id` mediumint(8) unsigned NOT NULL default '0',
  `option_id` mediumint(8) unsigned NOT NULL default '0',
  `field_type` tinyint(4) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`field_id`,`lang_id`,`option_id`)
)";

$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 5, 5, '5')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 4, 5, '4')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 3, 5, '3')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 2, 5, '2')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 1, 5, '1')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_fields_lang` VALUES (1, 1, 0, 5, '0')";

$sql[] = "CREATE TABLE `phpbb_garage_vehicle_lang` (
  `field_id` mediumint(8) unsigned NOT NULL default '0',
  `lang_id` tinyint(4) unsigned NOT NULL default '0',
  `lang_name` varchar(255) NOT NULL default '',
  `lang_explain` text NOT NULL,
  `lang_default_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`field_id`,`lang_id`)
)";

$sql[] = "INSERT INTO `phpbb_garage_vehicle_lang` VALUES (1, 1, 'dropdown', 'dropdown', '')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_lang` VALUES (2, 1, 'textfield', 'This is just a simple textfield2', 'text')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_lang` VALUES (3, 1, 'numbers', 'numbers', '')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_lang` VALUES (4, 1, 'textarea', 'textarea', 'textarea')";
$sql[] = "INSERT INTO `phpbb_garage_vehicle_lang` VALUES (5, 1, 'date', 'date', '')";*/

page_footer();

?>
