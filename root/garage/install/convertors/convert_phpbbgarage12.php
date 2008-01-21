<?php
/** 
*
* @package install
* @version $Id: convert_phpbb20.php,v 1.43 2007/07/28 15:06:16 acydburn Exp $
* @copyright (c) 2006 phpBB Group 
* @copyright (c) 2007 Esmond Poynton
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* NOTE to potential convertor authors. Please use this file to get
* familiar with the structure since we added some bare explanations here.
*
* Since this file gets included more than once on one page you are not able to add functions to it.
* Instead use a functions_ file.
*
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

include($phpbb_root_path . 'config.' . $phpEx);
unset($dbpasswd);

/**
* $convertor_data provides some basic information about this convertor which is
* used on the initial list of convertors and to populate the default settings
*/
$convertor_data = array(
	'forum_name'		=> 'phpBB Garage 1.2.0',
	'version'		=> '1.0.B1',
	'phpbbgarage_version'	=> '2.0.0',
	'author'		=> '<a href="http://www.phpbbgarage.com/">phpBB Garage</a>',
	'dbms'			=> $dbms,
	'dbhost'		=> $dbhost,
	'dbport'		=> $dbport,
	'dbuser'		=> $dbuser,
	'dbpasswd'		=> '',
	'dbname'		=> $dbname,
	'table_prefix'		=> 'phpbb_',
	'forum_path'		=> '../forums',
	'author_notes'		=> '',
);

/**
* $tables is a list of the tables (minus prefix) which we expect to find in the
* source forum. It is used to guess the prefix if the specified prefix is incorrect
*/
$tables = array(
	'garage',
	'garage_business',
	'garage_categories',
	'garage_config',
	'garage_gallery',
	'garage_guestbooks',
	'garage_images',
	'garage_insurance',
	'garage_makes',
	'garage_models',
	'garage_mods',
	'garage_quartermile',
	'garage_rollingroad',
	'garage_rating',
);

/**
* $config_schema details how the garage configuration information is stored in the source forum.
*
* 'table_format' can take the value 'file' to indicate a config file. In this case array_name
* is set to indicate the name of the array the config values are stored in
* 'table_format' can be an array if the values are stored in a table which is an assosciative array
* (as per phpBB Garage 1.x.x)
* If left empty, values are assumed to be stored in a table where each config setting is
* a column
*
* In either of the latter cases 'table_name' indicates the name of the table in the database
*
* 'settings' is an array which maps the name of the config directive in the source forum
* to the config directive in phpBB3. It can either be a direct mapping or use a function.
* Please note that the contents of the old config value are passed to the function, therefore
* an in-built function requiring the variable passed by reference is not able to be used. Since
* empty() is such a function we created the function is_empty() to be used instead.
 */

$forum_config_schema = array(
	'table_name'	=>	'config',
	'table_format'	=>	array(
		'config_name' => 'config_value'
	),
	'settings'	=>	array(
		'default_lang'	=> 'default_lang',
	),
);

$garage_config_schema = array(
	'table_name'	=>	'garage_config',
	'table_format'	=>	array(
		'config_name' => 'config_value'
	),
	'settings'	=>	array(
		'cars_per_page'				=> 'cars_per_page',
		'year_start'				=> 'year_start',
		'year_end'				=> 'year_end',
		'enable_user_submit_make'		=> 'enable_user_submit_make', 
		'enable_user_submit_model'		=> 'enable_user_submit_model', 
		'profile_thumbs'			=> 'profile_thumbs', 
		'enable_index_menu'			=> 'phpbbgarage_index_menu(menu_selection)',
		'enable_browse_menu'			=> 'phpbbgarage_browse_menu(menu_selection)',
		'enable_search_menu'			=> 'phpbbgarage_search_menu(menu_selection)',
		'enable_insurance_review_menu'		=> 'phpbbgarage_insurance_review_menu(menu_selection)',
		'enable_garage_review_menu'		=> 'phpbbgarage_garage_review_menu(menu_selection)',
		'enable_shop_review_menu'		=> 'phpbbgarage_shop_review_menu(menu_selection)',
		'enable_quartermile_menu'		=> 'phpbbgarage_quartermile_menu(menu_selection)',
		'enable_dynorun_menu'			=> 'phpbbgarage_dynorun_menu(menu_selection)',
		'enable_latest_vehicle_index'		=> 'lastupdatedvehiclesmain_on', 
		'latest_vehicle_index_limit'		=> 'lastupdatedvehiclesmain_limit', 
		'enable_featured_vehicle'		=> 'phpbbgarage_featured_vehicle()',
		'featured_vehicle_id'			=> 'featured_vehicle_id',
		'featured_vehicle_from_block'		=> 'phpbbgarage_feature_from_block(featured_vehicle_from_block)',
		'featured_vehicle_description'		=> 'featured_vehicle_description',
		'enable_newest_vehicle'			=> 'newestvehicles_on',
		'newest_vehicle_limit'			=> 'newestvehicles_limit', 
		'enable_updated_vehicle'		=> 'lastupdatedvehicles_on', 
		'updated_vehicle_limit'			=> 'lastupdatedvehicles_limit', 
		'enable_newest_modification'		=> 'newestmods_on',
		'newest_modification_limit'		=> 'newestmods_limit', 
		'enable_updated_modification'		=> 'lastupdatedmods_on', 
		'updated_modification_limit'		=> 'lastupdatedmods_limit', 
		'enable_most_modified'			=> 'mostmodded_on', 
		'most_modified_limit'			=> 'mostmodded_limit', 
		'enable_most_spent'			=> 'mostmoneyspent_on', 
		'most_spent_limit'			=> 'mostmoneyspent_limit', 
		'enable_most_viewed'			=> 'mostviewed_on', 
		'most_viewed_limit'			=> 'mostviewed_limit', 
		'enable_last_commented'			=> 'lastcommented_on', 
		'last_commented_limit'			=> 'lastcommented_limit', 
		'enable_top_dynorun'			=> 'topdynorun_on', 
		'top_dynorun_limit'			=> 'topdynorun_limit', 
		'enable_top_quartermile'		=> 'topquartermile_on', 
		'top_quartermile_limit'			=> 'topquartermile_limit', 
		'enable_top_rating'			=> 'toprated_on', 
		'top_rating_limit'			=> 'toprated_limit', 
		'enable_images'				=> 'garage_images', 
		'enable_modification_images'		=> 'allow_mod_image', 
		'enable_uploaded_images'		=> 'allow_image_upload', 
		'enable_remote_images'			=> 'allow_image_url', 
		'remote_timeout'			=> 'remote_timeout',
		'enable_mod_gallery'			=> 'show_mod_gallery', 
		'gallery_limit'				=> 'limit_mod_gallery', 
		'max_image_kbytes'			=> 'max_image_kbytes',
		'max_image_resolution'			=> 'max_image_resolution',
		'thumbnail_resolution'			=> 'thumbnail_resolution',
		'enable_quartermile'			=> 'enable_quartermile', 
		'enable_quartermile_approval'		=> 'enable_quartermile_approval', 
		'enable_quartermile_image_required'	=> 'quartermile_image_required', 
		'quartermile_image_required_limit'	=> 'quartermile_image_required_limit', 
		'enable_dynorun'			=> 'enable_rollingroad', 
		'enable_dynorun_approval'		=> 'enable_rollingroad_approval', 
		'enable_dynorun_image_required'		=> 'dynorun_image_required', 
		'dynorun_image_required_limit'		=> 'dynorun_image_required_limit', 
		'enable_insurance'			=> 'enable_insurance', 
		'enable_business_approval'		=> 'enable_business_approval', 
		'rating_permanent'			=> 'rating_permanent', 
		'rating_always_updateable'		=> 'rating_always_updateable', 
		'minimum_ratings_required'		=> 'minimum_ratings_required',
		'enable_guestbooks'			=> 'enable_guestbooks',
		'default_vehicle_quota'			=> 'max_user_cars', 
		'default_upload_quota'			=> 'max_upload_images', 
		'default_remote_quota'			=> 'max_remote_images', 
	),
);

/**
* $test_file is the name of a file which is present on the source
* forum which can be used to check that the path specified by the 
* user was correct
*/
$test_file = 'garage.php';

/**
* If this is set then we are not generating the first page of information but getting the conversion information.
*/
if (!$get_info)
{
/**
*	Description on how to use the convertor framework.
*
*	'schema' Syntax Description
*		-> 'target'		=> Target Table. If not specified the next table will be handled
*		-> 'primary'		=> Primary Key. If this is specified then this table is processed in batches
*		-> 'query_first'	=> array('target' or 'src', Query to execute before beginning the process
*								(if more than one then specified as array))
*		-> 'function_first'	=> Function to execute before beginning the process (if more than one then specified as array)
*								(This is mostly useful if variables need to be given to the converting process)
*		-> 'test_file'		=> This is not used at the moment but should be filled with a file from the old installation
*
*		// DB Functions
*		'distinct'	=> Add DISTINCT to the select query
*		'where'		=> Add WHERE to the select query
*		'group_by'	=> Add GROUP BY to the select query
*		'left_join'	=> Add LEFT JOIN to the select query (if more than one joins specified as array)
*		'having'	=> Add HAVING to the select query
*
*		// DB INSERT array
*		This one consist of three parameters
*		First Parameter: 
*							The key need to be filled within the target table
*							If this is empty, the target table gets not assigned the source value
*		Second Parameter:
*							Source value. If the first parameter is specified, it will be assigned this value.
*							If the first parameter is empty, this only gets added to the select query
*		Third Parameter:
*							Custom Function. Function to execute while storing source value into target table. 
*							The functions return value get stored.
*							The function parameter consist of the value of the second parameter.
*
*							types:
*								- empty string == execute nothing
*								- string == function to execute
*								- array == complex execution instructions
*		
*		Complex execution instructions:
*		@todo test complex execution instructions - in theory they will work fine
*
*							By defining an array as the third parameter you are able to define some statements to be executed. The key
*							is defining what to execute, numbers can be appended...
*
*							'function' => execute function
*							'execute' => run code, whereby all occurrences of {VALUE} get replaced by the last returned value.
*										The result *must* be assigned/stored to {RESULT}.
*							'typecast'	=> typecast value
*
*							The returned variables will be made always available to the next function to continue to work with.
*
*							example (variable inputted is an integer of 1):
*
*							array(
*								'function1'		=> 'increment_by_one',		// returned variable is 2
*								'typecast'		=> 'string',				// typecast variable to be a string
*								'execute'		=> '{RESULT} = {VALUE} . ' is good';', // returned variable is '2 is good'
*								'function2'		=> 'replace_good_with_bad',				// returned variable is '2 is bad'
*							),
*
*/

$convertor = array(
	'test_file'		=> 'garage.php',

	'avatar_path'		=> get_garage_config_value('upload_path') . '/',

	'query_first'		=> array(
	),
		
	'execute_first'		=> '
		import_garage_gallery();
		phpbbgarage_insert_categories();
	',

	'execute_last'	=> array(
		'phpbbgarage_convert_authentication_quota();',
	),

	'schema' => array(
		array(
			'target'	=> GARAGE_VEHICLES_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_VEHICLES_TABLE),

			array('id',			'garage.id',				''),
			array('user_id',		'garage.member_id',			''),
			array('made_year',		'garage.made_year',			''),
			array('engine_type',		'garage.engine_type',			'import_cover_type'),
			array('colour',			'garage.color',				''),
			array('mileage',		'garage.mileage',			''),
			array('mileage_unit',		'garage.mileage_units',			''),
			array('price',			'garage.price',				''),
			array('currency',		'garage.currency',			''),
			array('comments',		'garage.comments',			array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('views',			'garage.views',				''),
			array('date_created',		'garage.date_created',			''),
			array('date_updated',		'garage.date_updated',			''),
			array('make_id',		'garage.make_id',			''),
			array('model_id',		'garage.model_id',			''),
			array('main_vehicle',		'garage.main_vehicle',			''),
			array('weighted_rating',	'garage.weighted_rating',		''),
			array('bbcode_bitfield',	'',					'get_bbcode_bitfield'),
			array('bbcode_uid',		'garage.date_updated',			'make_uid'),
			array('bbcode_options',		'',					''),
			array('pending',		'0',					''),
		),
		array(
			'target'	=> GARAGE_BUSINESS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_BUSINESS_TABLE),

			array('id',			'garage_business.id',			''),
			array('title',			'garage_business.title',		array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('address',		'garage_business.address',		array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('telephone',		'garage_business.telephone',		''),
			array('fax',			'garage_business.fax',			''),
			array('website',		'garage_business.website',		''),
			array('email',			'garage_business.email',		''),
			array('opening_hours',		'garage_business.opening_hours',	array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('insurance',		'garage_business.insurance',		''),
			array('garage',			'garage_business.garage',		''),
			array('retail',			'garage_business.id',			'is_business_retail'),
			array('pending',		'garage_business.pending',		''),
		),
		array(
			'target'	=> GARAGE_CATEGORIES_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_CATEGORIES_TABLE),

			array('id',			'garage_categories.id',			''),
			array('title',			'garage_categories.title',		array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('field_order',		'garage_categories.field_order',	''),
		),
		array(
			'target'	=> GARAGE_MODIFICATIONS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_MODIFICATIONS_TABLE),
			'function_first'=> 'create_placeholder_manufacturer',

			array('id',			'garage_mods.id',			''),
			array('vehicle_id',		'garage_mods.garage_id',		''),
			array('user_id',		'garage_mods.member_id',		''),
			array('category_id',		'garage_mods.category_id',		''),
			array('manufacturer_id',	'',					'get_placeholder_manufacturer_id'),
			array('product_id',		'garage_mods.id',			'insert_modification_product'),
			array('price',			'garage_mods.price',			''),
			array('install_price',		'garage_mods.install_price',		''),
			array('product_rating',		'garage_mods.product_rating',		''),
			array('purchase_rating',	'garage_mods.purchase_rating',		''),
			array('install_rating',		'garage_mods.install_rating',		''),
			array('shop_id',		'garage_mods.business_id',		''),
			array('installer_id',		'garage_mods.install_business_id',	''),
			array('comments',		'garage_mods.comments',			array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('install_comments',	'garage_mods.install_comments',		array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('date_created',		'garage_mods.date_created',		''),
			array('date_updated',		'garage_mods.date_updated',		''),
		),
		array(
			'target'	=> GARAGE_DYNORUNS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_DYNORUNS_TABLE),

			array('id',			'garage_rollingroad.id',		''),
			array('vehicle_id',		'garage_rollingroad.garage_id',		''),
			array('dynocentre_id',		'garage_rollingroad.dynocenter',	'import_dynocentre'),
			array('bhp',			'garage_rollingroad.bhp',		''),
			array('bhp_unit',		'garage_rollingroad.bhp_unit',		''),
			array('torque',			'garage_rollingroad.torque',		''),
			array('torque_unit',		'garage_rollingroad.torque_unit',	''),
			array('boost',			'garage_rollingroad.boost',		''),
			array('boost_unit',		'garage_rollingroad.boost_unit',	''),
			array('nitrous',		'garage_rollingroad.nitrous',		''),
			array('peakpoint',		'garage_rollingroad.peakpoint',		''),
			array('date_created',		'garage_rollingroad.date_created',	''),
			array('date_updated',		'garage_rollingroad.date_updated',	''),
			array('pending',		'garage_rollingroad.pending',		''),

		),
		array(
			'target'	=> GARAGE_MAKES_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_MAKES_TABLE),

			array('id',			'garage_makes.id',			''),
			array('make',			'garage_makes.make',			array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('pending',		'garage_makes.pending',			''),
		),
		array(
			'target'	=> GARAGE_MODELS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_MODELS_TABLE),

			array('id',			'garage_models.id',			''),
			array('make_id',		'garage_models.make_id',		''),
			array('model',			'garage_models.model',			array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
			array('pending',		'garage_models.pending',		''),
		),
		array(
			'target'	=> GARAGE_PREMIUMS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_PREMIUMS_TABLE),

			array('id',			'garage_insurance.id',			''),
			array('vehicle_id',		'garage_insurance.garage_id',		''),
			array('business_id',		'garage_insurance.business_id',		''),
			array('cover_type_id',		'garage_insurance.cover_type',		'import_cover_type'),
			array('premium',		'garage_insurance.premium',		''),
			array('comments',		'garage_insurance.comments',		array('function1' => 'phpbb_set_encoding', 'function2' => 'utf8_htmlspecialchars')),
		),
		array(
			'target'	=> GARAGE_QUARTERMILES_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_QUARTERMILES_TABLE),

			array('id',			'garage_quartermile.id',		''),
			array('vehicle_id',		'garage_quartermile.garage_id',		''),
			array('rt',			'garage_quartermile.rt',		''),
			array('sixty',			'garage_quartermile.sixty',		''),
			array('three',			'garage_quartermile.three',		''),
			array('eighth',			'garage_quartermile.eight',		''),
			array('eighthmph',		'garage_quartermile.eightmph',		''),
			array('thou',			'garage_quartermile.thou',		''),
			array('quart',			'garage_quartermile.quart',		''),
			array('quartmph',		'garage_quartermile.quartmph',		''),
			array('pending',		'garage_quartermile.pending',		''),
			array('dynorun_id',		'garage_quartermile.rr_id',		''),
			array('date_created',		'garage_quartermile.date_created',	''),
			array('date_updated',		'garage_quartermile.date_updated',	''),
		),
		array(
			'target'	=> GARAGE_RATINGS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_RATINGS_TABLE),

			array('id',			'garage_rating.id',			''),
			array('vehicle_id',		'garage_rating.garage_id',		''),
			array('rating',			'garage_rating.rating',			''),
			array('user_id',		'garage_rating.user_id',		''),
			array('rate_date',		'garage_rating.rate_date',		''),
		),
		array(
			'target'	=> GARAGE_IMAGES_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_IMAGES_TABLE),

			array('attach_id',		'garage_images.attach_id',		''),
			array('vehicle_id',		'garage_images.garage_id',		''),
			array('attach_location',	'garage_images.attach_location',	''),
			array('attach_hits',		'garage_images.attach_hits',		''),
			array('attach_ext',		'garage_images.attach_ext',		''),
			array('attach_file',		'garage_images.attach_file',		''),
			array('attach_thumb_location',	'garage_images.attach_thumb_location',	''),
			array('attach_thumb_width',	'garage_images.attach_thumb_width',	''),
			array('attach_thumb_height',	'garage_images.attach_thumb_height',	''),
			array('attach_is_image',	'garage_images.attach_is_image',	''),
			array('attach_date',		'garage_images.attach_date',		''),
			array('attach_filesize',	'garage_images.attach_filesize',	''),
			array('attach_thumb_filesize',	'garage_images.attach_thumb_filesize',	''),
		),
		array(
			'target'	=> GARAGE_VEHICLE_GALLERY_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_VEHICLE_GALLERY_TABLE),

			array('vehicle_id',		'garage_gallery.garage_id',		''),
			array('image_id',		'garage_gallery.image_id',		''),
			array('hilite',			'1',					''),

			'where'		=>	'garage_gallery.garage_id = garage.id AND garage_gallery.image_id = garage.image_id'
		),
		array(
			'target'	=> GARAGE_VEHICLE_GALLERY_TABLE,

			array('vehicle_id',		'garage_gallery.garage_id',		''),
			array('image_id',		'garage_gallery.image_id',		''),
			array('hilite',			'0',					''),

			'where'		=>	'garage_gallery.garage_id = garage.id AND garage_gallery.image_id != garage.image_id',
			'group_by'	=>	'garage_gallery.image_id'
		),
		array(
			'target'	=> GARAGE_MODIFICATION_GALLERY_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_MODIFICATION_GALLERY_TABLE),

			array('vehicle_id',		'garage_mods.garage_id',		''),
			array('modification_id',	'garage_mods.id',			''),
			array('image_id',		'garage_mods.image_id',			''),
			array('hilite',			'1',					''),

			'where'		=>	'garage_mods.image_id IS NOT NULL'
		),
		array(
			'target'	=> GARAGE_QUARTERMILE_GALLERY_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_QUARTERMILE_GALLERY_TABLE),

			array('vehicle_id',		'garage_quartermile.garage_id',		''),
			array('quartermile_id',		'garage_quartermile.id',		''),
			array('image_id',		'garage_quartermile.image_id',		''),
			array('hilite',			'1',					''),

			'where'		=>	'garage_quartermile.image_id IS NOT NULL'
		),
		array(
			'target'	=> GARAGE_DYNORUN_GALLERY_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_DYNORUN_GALLERY_TABLE),

			array('vehicle_id',		'garage_rollingroad.garage_id',		''),
			array('dynorun_id',		'garage_rollingroad.id',		''),
			array('image_id',		'garage_rollingroad.image_id',		''),
			array('hilite',			'1',					''),

			'where'		=>	'garage_rollingroad.image_id IS NOT NULL'
		),
		array(
			'target'	=> GARAGE_GUESTBOOKS_TABLE,
			'query_first'	=> array('target', $convert->truncate_statement . GARAGE_GUESTBOOKS_TABLE),

			array('id',			'garage_guestbooks.id',			''),
			array('vehicle_id',		'garage_guestbooks.garage_id',		''),
			array('author_id',		'garage_guestbooks.author_id',		''),
			array('post_date',		'garage_guestbooks.post_date',		''),
			array('ip_address',		'garage_guestbooks.ip_address',		'decode_ip'),
			array('bbcode_bitfield',	'',					'get_bbcode_bitfield'),
			array('bbcode_uid',		'garage_guestbooks.post_date',		'make_uid'),
			array('bbcode_options',		'',					''),
			array('pending',		'0',					''),
			array('post',			'garage_guestbooks.post',		'phpbb_prepare_message'),
		),
	),
);
}

?>
