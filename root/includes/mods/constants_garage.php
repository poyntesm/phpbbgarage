<?php
/** 
*
* @package phpBB3
* @version $Id: constants.php,v 1.76 2006/10/19 13:54:47 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
*/

//Define Business Types
define('BUSINESS_PRODUCT', 1);
define('BUSINESS_INSURANCE', 2);
define('BUSINESS_GARAGE', 3);
define('BUSINESS_RETAIL', 4);

//Define Insurance Types
define('COMPREHENSIVE', 1);

//Defin Garage Tables
define('GARAGE_TABLE', $table_prefix.'garage_vehicles');
define('GARAGE_CONFIG_TABLE', $table_prefix.'garage_config');
define('GARAGE_CATEGORIES_TABLE', $table_prefix.'garage_categories');
define('GARAGE_VEHICLE_GALLERY_TABLE', $table_prefix.'garage_vehicle_gallery');
define('GARAGE_MODIFICATION_GALLERY_TABLE', $table_prefix.'garage_modification_gallery');
define('GARAGE_QUARTERMILE_GALLERY_TABLE', $table_prefix.'garage_quartermile_gallery');
define('GARAGE_DYNORUN_GALLERY_TABLE', $table_prefix.'garage_vehicle_gallery');
define('GARAGE_GUESTBOOKS_TABLE', $table_prefix.'garage_guestbooks');
define('GARAGE_IMAGES_TABLE', $table_prefix.'garage_images');
define('GARAGE_MAKES_TABLE', $table_prefix.'garage_makes');
define('GARAGE_MODELS_TABLE', $table_prefix.'garage_models');
define('GARAGE_MODS_TABLE', $table_prefix.'garage_modifications');
define('GARAGE_QUARTERMILE_TABLE', $table_prefix.'garage_quartermile');
define('GARAGE_DYNORUN_TABLE', $table_prefix.'garage_dynorun');
define('GARAGE_BUSINESS_TABLE', $table_prefix.'garage_business');
define('GARAGE_INSURANCE_TABLE', $table_prefix.'garage_insurance');
define('GARAGE_RATING_TABLE', $table_prefix.'garage_rating');
define('GARAGE_CUSTOM_FIELDS_TABLE', $table_prefix.'garage_custom_fields');
define('GARAGE_CUSTOM_FIELDS_DATA_TABLE', $table_prefix.'garage_custom_fields_data');
define('GARAGE_CUSTOM_FIELDS_LANG_TABLE', $table_prefix.'garage_custom_fields_lang');
define('GARAGE_CUSTOM_LANG_TABLE', $table_prefix.'garage_custom_lang');
define('GARAGE_PRODUCTS_TABLE', $table_prefix.'garage_products');

//Define Image Upload Location
define('GARAGE_UPLOAD_PATH', 'garage/upload/');

?>
