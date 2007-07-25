<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/

/**
*  If you want to add new constants of any type DO NOT delete the original ones.
*  If you do not want some constants available in dropdowns etc..edit the array in
*  the function that produces them in class_garage_template.
*/

//Define Business Types Constants
define('BUSINESS_PRODUCT', 1);
define('BUSINESS_INSURANCE', 2);
define('BUSINESS_GARAGE', 3);
define('BUSINESS_RETAIL', 4);
define('BUSINESS_DYNOCENTRE', 5);

//Define Insurance Types Constants For Insurance Premiums
define('COMP', 1);
define('CLAS', 2);
define('COMP_RED', 3);
define('TP', 4);
define('TPFT', 5);

//Define Track Conditions Constants For Lap Times
define('TRACK_DRY', 1);
define('TRACK_INTERMEDIATE', 2);
define('TRACK_WET', 3);

//Define Lap Types Constants For Lap Times
define('LAP_QUALIFING', 1);
define('LAP_RACE', 2);
define('LAP_TRACKDAY', 3);

//Define Service Types Constants For Service History
define('SERVICE_MAJOR', 1);
define('SERVICE_MINOR', 2);


//Define Engien Type Constants For Vehicle Engine Type
define('2_CYLINDER_FI', 1);
define('2_CYLINDER_NA', 2);
define('3_CYLINDER_FI', 3);
define('3_CYLINDER_NA', 4);
define('4_CYLINDER_FI', 5);
define('4_CYLINDER_NA', 6);
define('5_CYLINDER_FI', 7);
define('5_CYLINDER_NA', 8);
define('6_CYLINDER_FI', 9);
define('6_CYLINDER_NA', 10);
define('8_CYLINDER_FI', 11);
define('8_CYLINDER_NA', 12);
define('10_CYLINDER_FI', 13);
define('10_CYLINDER_NA', 14);
define('12_CYLINDER_FI', 15);
define('12_CYLINDER_NA', 16);
define('16_CYLINDER_FI', 17);
define('16_CYLINDER_NA', 18);

//Define Garage Tables Constants
define('GARAGE_VEHICLES_TABLE', $table_prefix . 'garage_vehicles');
define('GARAGE_CONFIG_TABLE', $table_prefix . 'garage_config');
define('GARAGE_CATEGORIES_TABLE', $table_prefix . 'garage_categories');
define('GARAGE_VEHICLE_GALLERY_TABLE', $table_prefix . 'garage_vehicles_gallery');
define('GARAGE_MODIFICATION_GALLERY_TABLE', $table_prefix . 'garage_modifications_gallery');
define('GARAGE_QUARTERMILE_GALLERY_TABLE', $table_prefix . 'garage_quartermiles_gallery');
define('GARAGE_LAP_GALLERY_TABLE', $table_prefix . 'garage_laps_gallery');
define('GARAGE_DYNORUN_GALLERY_TABLE', $table_prefix . 'garage_dynoruns_gallery');
define('GARAGE_GUESTBOOKS_TABLE', $table_prefix . 'garage_guestbooks');
define('GARAGE_IMAGES_TABLE', $table_prefix . 'garage_images');
define('GARAGE_MAKES_TABLE', $table_prefix . 'garage_makes');
define('GARAGE_MODELS_TABLE', $table_prefix . 'garage_models');
define('GARAGE_MODIFICATIONS_TABLE', $table_prefix . 'garage_modifications');
define('GARAGE_QUARTERMILES_TABLE', $table_prefix . 'garage_quartermiles');
define('GARAGE_DYNORUNS_TABLE', $table_prefix . 'garage_dynoruns');
define('GARAGE_BUSINESS_TABLE', $table_prefix . 'garage_business');
define('GARAGE_PREMIUMS_TABLE', $table_prefix . 'garage_premiums');
define('GARAGE_RATINGS_TABLE', $table_prefix . 'garage_ratings');
define('GARAGE_CUSTOM_FIELDS_TABLE', $table_prefix . 'garage_custom_fields');
define('GARAGE_CUSTOM_FIELDS_DATA_TABLE', $table_prefix . 'garage_custom_fields_data');
define('GARAGE_CUSTOM_FIELDS_LANG_TABLE', $table_prefix . 'garage_custom_fields_lang');
define('GARAGE_FIELDS_LANG_TABLE', $table_prefix . 'garage_fields_lang');
define('GARAGE_PRODUCTS_TABLE', $table_prefix . 'garage_products');
define('GARAGE_TRACKS_TABLE', $table_prefix . 'garage_tracks');
define('GARAGE_LAPS_TABLE', $table_prefix . 'garage_laps');
define('GARAGE_SERVICE_HISTORY_TABLE', $table_prefix . 'garage_service_history');
define('GARAGE_BLOGS_TABLE', $table_prefix . 'garage_blog');

//Define Location Constants
define('GARAGE_UPLOAD_PATH', 'garage/upload/');
define('GARAGE_WATERMARK_PATH', 'garage/');
?>
