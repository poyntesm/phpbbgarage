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

//Defin Garage Tables
define('GARAGE_TABLE', $table_prefix.'garage');
define('GARAGE_CONFIG_TABLE', $table_prefix.'garage_config');
define('GARAGE_CATEGORIES_TABLE', $table_prefix.'garage_categories');
define('GARAGE_GALLERY_TABLE', $table_prefix.'garage_gallery');
define('GARAGE_GUESTBOOKS_TABLE', $table_prefix.'garage_guestbooks');
define('GARAGE_IMAGES_TABLE', $table_prefix.'garage_images');
define('GARAGE_MAKES_TABLE', $table_prefix.'garage_makes');
define('GARAGE_MODELS_TABLE', $table_prefix.'garage_models');
define('GARAGE_MODS_TABLE', $table_prefix.'garage_mods');
define('GARAGE_QUARTERMILE_TABLE', $table_prefix.'garage_quartermile');
define('GARAGE_DYNORUN_TABLE', $table_prefix.'garage_dynorun');
define('GARAGE_BUSINESS_TABLE', $table_prefix.'garage_business');
define('GARAGE_INSURANCE_TABLE', $table_prefix.'garage_insurance');
define('GARAGE_RATING_TABLE', $table_prefix.'garage_rating');
define('GARAGE_VEHICLE_FIELDS_TABLE', $table_prefix.'garage_vehicle_fields');
define('GARAGE_VEHICLE_FIELDS_DATA_TABLE', $table_prefix.'garage_vehicle_fields_data');
define('GARAGE_VEHICLE_FIELDS_LANG_TABLE', $table_prefix.'garage_vehicle_fields_lang');
define('GARAGE_VEHICLE_LANG_TABLE', $table_prefix.'garage_vehicle_lang');
define('GARAGE_PRODUCTS_TABLE', $table_prefix.'garage_products');

//Define Image Upload Location
define('GARAGE_UPLOAD_PATH', 'garage/upload/');

?>
