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

//Define Engine Type Constants For Vehicle Engine Type
define('FI_2_CYLINDER', 1);
define('NA_2_CYLINDER', 2);
define('FI_3_CYLINDER', 3);
define('NA_3_CYLINDER', 4);
define('FI_4_CYLINDER', 5);
define('NA_4_CYLINDER', 6);
define('FI_5_CYLINDERI', 7);
define('NA_5_CYLINDERA', 8);
define('FI_6_CYLINDER', 9);
define('NA_6_CYLINDER', 10);
define('FI_8_CYLINDER', 11);
define('NA_8_CYLINDER', 12);
define('FI_10_CYLINDER', 13);
define('NA_10_CYLINDER', 14);
define('FI_12_CYLINDER', 15);
define('NA_12_CYLINDER', 16);
define('FI_16_CYLINDER', 17);
define('NA_16_CYLINDER', 18);

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
