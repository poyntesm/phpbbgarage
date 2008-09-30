<?php

$schema_data = array();

$schema_data['phpbb_garage_vehicles'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'user_id'		=> array('UINT', 0),
		'made_year'		=> array('UINT', '2007'),
		'engine_type'		=> array('TINT:2', 0),
		'colour'		=> array('XSTEXT_UNI', ''),
		'mileage'		=> array('UINT', 0),
		'mileage_unit'		=> array('VCHAR:32', 'Miles'),
		'price'			=> array('UINT', 0),
		'currency'		=> array('VCHAR:32', 'EUR'),
		'comments'		=> array('MTEXT_UNI', ''),
		'bbcode_bitfield'	=> array('VCHAR:255', ''),
		'bbcode_uid'		=> array('VCHAR:5', ''),
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
);

$schema_data['phpbb_garage_business'] = array(
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
);

$schema_data['phpbb_garage_categories'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'title'			=> array('TEXT_UNI', ''),
		'field_order'		=> array('USINT', 0),
	),
	'PRIMARY_KEY'	=> 'id',
	'KEYS'			=> array(
		'title'		=> array('INDEX', 'title'),
		'id'		=> array('INDEX', array('id', 'title')),
	),
);

$schema_data['phpbb_garage_config'] = array(
	'COLUMNS'		=> array(
		'config_name'		=> array('VCHAR', ''),
		'config_value'		=> array('VCHAR_UNI', ''),
	),
	'PRIMARY_KEY'	=> 'config_name',
);

$schema_data['phpbb_garage_vehicles_gallery'] = array(
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
);

$schema_data['phpbb_garage_modifications_gallery'] = array(
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
);

$schema_data['phpbb_garage_quartermiles_gallery'] = array(
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
);

$schema_data['phpbb_garage_dynoruns_gallery'] = array(
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
);

$schema_data['phpbb_garage_laps_gallery'] = array(
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
);

$schema_data['phpbb_garage_guestbooks'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'author_id'		=> array('UINT', 0),
		'post_date'		=> array('TIMESTAMP', 0),
		'ip_address'		=> array('VCHAR:40', ''),
		'bbcode_bitfield'	=> array('VCHAR:255', ''),
		'bbcode_uid'		=> array('VCHAR:5', ''),
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
);

$schema_data['phpbb_garage_images'] = array(
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
);

$schema_data['phpbb_garage_premiums'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'business_id'		=> array('UINT', 0),
		'cover_type_id'		=> array('UINT', 0),
		'premium'		=> array('UINT', 0),
		'comments'		=> array('MTEXT_UNI', ),
	),
	'PRIMARY_KEY'	=> 'id',
);

$schema_data['phpbb_garage_makes'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'make'			=> array('VCHAR', ''),
		'pending'		=> array('BOOL', 0),
	),
	'PRIMARY_KEY'	=> 'id',
	'KEYS'			=> array(
		'make'			=> array('INDEX', 'make'),
	),
);

$schema_data['phpbb_garage_models'] = array(
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
);

$schema_data['phpbb_garage_modifications'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'user_id'		=> array('UINT', 0),
		'category_id'		=> array('UINT', 0),
		'manufacturer_id'	=> array('UINT', 0),
		'product_id'		=> array('UINT', 0),
		'price'			=> array('UINT', 0),
		'install_price'		=> array('UINT', 0),
		'product_rating'	=> array('TINT:2', 0),
		'purchase_rating'	=> array('TINT:2', 0),
		'install_rating'	=> array('TINT:2', 0),
		'shop_id'		=> array('UINT', 0),
		'installer_id'		=> array('UINT', 0),
		'comments'		=> array('MTEXT_UNI', ''),
		'bbcode_bitfield'	=> array('VCHAR:255', ''),
		'bbcode_uid'		=> array('VCHAR:5', ''),
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
);

$schema_data['phpbb_garage_products'] = array(
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
);

$schema_data['phpbb_garage_quartermiles'] = array(
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
);

$schema_data['phpbb_garage_dynoruns'] = array(
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
);

$schema_data['phpbb_garage_ratings'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'rating'		=> array('TINT:2', 0),
		'user_id'		=> array('UINT', 0),
		'rate_date'		=> array('TIMESTAMP', 0),
	),
	'PRIMARY_KEY'	=> 'id',
);

$schema_data['phpbb_garage_tracks'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'title'			=> array('VCHAR', ''),
		'length'		=> array('VCHAR:32', ''),
		'mileage_unit'		=> array('VCHAR:32', ''),
		'pending'		=> array('BOOL', 0),
	),
	'PRIMARY_KEY'	=> 'id',
);

$schema_data['phpbb_garage_laps'] = array(
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
);

$schema_data['phpbb_garage_service_history'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'garage_id'		=> array('UINT', 0),
		'type_id'		=> array('UINT', 0),
		'price'			=> array('UINT', 0),
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
);

$schema_data['phpbb_garage_blog'] = array(
	'COLUMNS'		=> array(
		'id'			=> array('UINT', NULL, 'auto_increment'),
		'vehicle_id'		=> array('UINT', 0),
		'user_id'		=> array('UINT', 0),
		'blog_title'		=> array('XSTEXT_UNI', ''),
		'blog_text'		=> array('MTEXT_UNI', ''),
		'blog_date'		=> array('TIMESTAMP', 0),
		'bbcode_bitfield'	=> array('VCHAR:255', ''),
		'bbcode_uid'		=> array('VCHAR:5', ''),
		'bbcode_options'	=> array('UINT', 7),
	),
	'PRIMARY_KEY'	=> 'id',
	'KEYS'			=> array(
		'vehicle_id'		=> array('INDEX', 'vehicle_id'),
		'user_id'		=> array('INDEX', 'user_id'),
	),
);

$schema_data['phpbb_garage_custom_fields'] = array(
	'COLUMNS'		=> array(
		'field_id'		=> array('UINT', NULL, 'auto_increment'),
		'field_name'		=> array('VCHAR_UNI', ''),
		'field_type'		=> array('TINT:4', 0),
		'field_ident'		=> array('VCHAR:20', ''),
		'field_length'		=> array('VCHAR:20', ''),
		'field_minlen'		=> array('VCHAR', ''),
		'field_maxlen'		=> array('VCHAR', ''),
		'field_novalue'		=> array('VCHAR_UNI', ''),
		'field_default_value'	=> array('VCHAR_UNI', ''),
		'field_validation'	=> array('VCHAR_UNI:20', ''),
		'field_required'	=> array('BOOL', 0),
		'field_show_on_reg'	=> array('BOOL', 0),
		'field_hide'		=> array('BOOL', 0),
		'field_no_view'		=> array('BOOL', 0),
		'field_active'		=> array('BOOL', 0),
		'field_order'		=> array('UINT', 0),
	),
	'PRIMARY_KEY'	=> 'field_id',
	'KEYS'			=> array(
		'fld_type'		=> array('INDEX', 'field_type'),
		'fld_ordr'		=> array('INDEX', 'field_order'),
	),
);

$schema_data['phpbb_garage_custom_fields_data'] = array(
	'COLUMNS'		=> array(
		'user_id'		=> array('UINT', 0),
	),
	'PRIMARY_KEY'	=> 'user_id',
);

$schema_data['phpbb_garage_custom_fields_lang'] = array(
	'COLUMNS'		=> array(
		'field_id'		=> array('UINT', 0),
		'lang_id'		=> array('UINT', 0),
		'option_id'		=> array('UINT', 0),
		'field_type'		=> array('TINT:4', 0),
		'lang_value'		=> array('VCHAR_UNI', ''),
	),
	'PRIMARY_KEY'	=> array('field_id', 'lang_id', 'option_id'),
);

$schema_data['phpbb_garage_lang'] = array(
	'COLUMNS'		=> array(
		'field_id'		=> array('UINT', 0),
		'lang_id'		=> array('UINT', 0),
		'lang_name'		=> array('VCHAR_UNI', ''),
		'lang_explain'		=> array('TEXT_UNI', ''),
		'lang_default_value'	=> array('VCHAR_UNI', ''),
	),
	'PRIMARY_KEY'	=> array('field_id', 'lang_id'),
);

?>
