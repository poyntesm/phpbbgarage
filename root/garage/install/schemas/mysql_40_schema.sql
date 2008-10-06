#
# $Id$
#

# Table: 'phpbb_garage_vehicles'
CREATE TABLE phpbb_garage_vehicles (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	made_year mediumint(8) UNSIGNED DEFAULT '2007' NOT NULL,
	engine_type tinyint(2) DEFAULT '0' NOT NULL,
	colour blob NOT NULL,
	mileage mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	mileage_unit varbinary(32) DEFAULT 'Miles' NOT NULL,
	price mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	currency varbinary(32) DEFAULT 'EUR' NOT NULL,
	comments mediumblob NOT NULL,
	bbcode_bitfield varbinary(255) DEFAULT '' NOT NULL,
	bbcode_uid varbinary(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	views mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	make_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	model_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	main_vehicle tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	weighted_rating decimal(4,2) DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY date_created (date_created),
	KEY date_updated (date_updated),
	KEY user_id (user_id),
	KEY views (views)
);


# Table: 'phpbb_garage_business'
CREATE TABLE phpbb_garage_business (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title blob NOT NULL,
	address varbinary(255) DEFAULT '' NOT NULL,
	telephone varbinary(100) DEFAULT '' NOT NULL,
	fax varbinary(100) DEFAULT '' NOT NULL,
	website varbinary(255) DEFAULT '' NOT NULL,
	email varbinary(100) DEFAULT '' NOT NULL,
	opening_hours varbinary(255) DEFAULT '' NOT NULL,
	insurance tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	garage tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	retail tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	product tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	dynocentre tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY insurance (insurance),
	KEY garage (garage),
	KEY retail (retail),
	KEY product (product),
	KEY dynocentre (dynocentre)
);


# Table: 'phpbb_garage_categories'
CREATE TABLE phpbb_garage_categories (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title blob NOT NULL,
	field_order smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY title (title(255)),
	KEY id (id, title(255))
);


# Table: 'phpbb_garage_config'
CREATE TABLE phpbb_garage_config (
	config_name varbinary(255) DEFAULT '' NOT NULL,
	config_value blob NOT NULL,
	PRIMARY KEY (config_name)
);


# Table: 'phpbb_garage_vehicles_gallery'
CREATE TABLE phpbb_garage_vehicles_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_garage_modifications_gallery'
CREATE TABLE phpbb_garage_modifications_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	modification_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_garage_quartermiles_gallery'
CREATE TABLE phpbb_garage_quartermiles_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	quartermile_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_garage_dynoruns_gallery'
CREATE TABLE phpbb_garage_dynoruns_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	dynorun_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_garage_laps_gallery'
CREATE TABLE phpbb_garage_laps_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lap_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
);


# Table: 'phpbb_garage_guestbooks'
CREATE TABLE phpbb_garage_guestbooks (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	author_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	post_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	ip_address varbinary(40) DEFAULT '' NOT NULL,
	bbcode_bitfield varbinary(255) DEFAULT '' NOT NULL,
	bbcode_uid varbinary(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	post mediumblob NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY author_id (author_id),
	KEY post_date (post_date)
);


# Table: 'phpbb_garage_images'
CREATE TABLE phpbb_garage_images (
	attach_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	attach_location varbinary(255) DEFAULT '' NOT NULL,
	attach_hits mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	attach_ext varbinary(100) DEFAULT '' NOT NULL,
	attach_file varbinary(255) DEFAULT '' NOT NULL,
	attach_thumb_location varbinary(255) DEFAULT '' NOT NULL,
	attach_thumb_width smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	attach_thumb_height smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	attach_is_image tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	attach_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	attach_filesize int(20) UNSIGNED DEFAULT '0' NOT NULL,
	attach_thumb_filesize int(20) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (attach_id)
);


# Table: 'phpbb_garage_premiums'
CREATE TABLE phpbb_garage_premiums (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	business_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	cover_type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	premium mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	comments mediumblob NOT NULL,
	PRIMARY KEY (id)
);


# Table: 'phpbb_garage_makes'
CREATE TABLE phpbb_garage_makes (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	make varbinary(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY make (make)
);


# Table: 'phpbb_garage_models'
CREATE TABLE phpbb_garage_models (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	make_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	model varbinary(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY make_id (make_id)
);


# Table: 'phpbb_garage_modifications'
CREATE TABLE phpbb_garage_modifications (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	category_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	manufacturer_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	product_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	price mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	install_price mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	product_rating tinyint(2) DEFAULT '0' NOT NULL,
	purchase_rating tinyint(2) DEFAULT '0' NOT NULL,
	install_rating tinyint(2) DEFAULT '0' NOT NULL,
	shop_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	installer_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	comments mediumblob NOT NULL,
	bbcode_bitfield varbinary(255) DEFAULT '' NOT NULL,
	bbcode_uid varbinary(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	install_comments mediumblob NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY user_id (user_id),
	KEY vehicle_id_2 (vehicle_id, category_id),
	KEY category_id (category_id),
	KEY vehicle_id (vehicle_id),
	KEY date_created (date_created),
	KEY date_updated (date_updated)
);


# Table: 'phpbb_garage_products'
CREATE TABLE phpbb_garage_products (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	business_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	category_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	title varbinary(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY business_id (business_id),
	KEY category_id (category_id)
);


# Table: 'phpbb_garage_quartermiles'
CREATE TABLE phpbb_garage_quartermiles (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rt decimal(6,3) DEFAULT '0' NOT NULL,
	sixty decimal(6,3) DEFAULT '0' NOT NULL,
	three decimal(6,3) DEFAULT '0' NOT NULL,
	eighth decimal(6,3) DEFAULT '0' NOT NULL,
	eighthmph decimal(6,3) DEFAULT '0' NOT NULL,
	thou decimal(6,3) DEFAULT '0' NOT NULL,
	quart decimal(6,3) DEFAULT '0' NOT NULL,
	quartmph decimal(6,3) DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	dynorun_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
);


# Table: 'phpbb_garage_dynoruns'
CREATE TABLE phpbb_garage_dynoruns (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	dynocentre_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	bhp decimal(6,2) DEFAULT '0' NOT NULL,
	bhp_unit varbinary(32) DEFAULT '' NOT NULL,
	torque decimal(6,2) DEFAULT '0' NOT NULL,
	torque_unit varbinary(32) DEFAULT '' NOT NULL,
	boost decimal(6,2) DEFAULT '0' NOT NULL,
	boost_unit varbinary(32) DEFAULT '' NOT NULL,
	nitrous mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	peakpoint decimal(7,3) DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
);


# Table: 'phpbb_garage_ratings'
CREATE TABLE phpbb_garage_ratings (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rating tinyint(2) DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rate_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
);


# Table: 'phpbb_garage_tracks'
CREATE TABLE phpbb_garage_tracks (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title varbinary(255) DEFAULT '' NOT NULL,
	length varbinary(32) DEFAULT '' NOT NULL,
	mileage_unit varbinary(32) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
);


# Table: 'phpbb_garage_laps'
CREATE TABLE phpbb_garage_laps (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	track_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	condition_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	minute int(2) UNSIGNED DEFAULT '0' NOT NULL,
	second int(2) UNSIGNED DEFAULT '0' NOT NULL,
	millisecond int(2) UNSIGNED DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY track_id (track_id)
);


# Table: 'phpbb_garage_service_history'
CREATE TABLE phpbb_garage_service_history (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	garage_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	price mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rating tinyint(2) DEFAULT '0' NOT NULL,
	mileage mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY garage_id (garage_id)
);


# Table: 'phpbb_garage_blog'
CREATE TABLE phpbb_garage_blog (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	blog_title blob NOT NULL,
	blog_text mediumblob NOT NULL,
	blog_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	bbcode_bitfield varbinary(255) DEFAULT '' NOT NULL,
	bbcode_uid varbinary(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY user_id (user_id)
);


# Table: 'phpbb_garage_custom_fields'
CREATE TABLE phpbb_garage_custom_fields (
	field_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	field_name blob NOT NULL,
	field_type tinyint(4) DEFAULT '0' NOT NULL,
	field_ident varbinary(20) DEFAULT '' NOT NULL,
	field_length varbinary(20) DEFAULT '' NOT NULL,
	field_minlen varbinary(255) DEFAULT '' NOT NULL,
	field_maxlen varbinary(255) DEFAULT '' NOT NULL,
	field_novalue blob NOT NULL,
	field_default_value blob NOT NULL,
	field_validation varbinary(60) DEFAULT '' NOT NULL,
	field_required tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_show_on_reg tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_hide tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_no_view tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_active tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_order mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (field_id),
	KEY fld_type (field_type),
	KEY fld_ordr (field_order)
);


# Table: 'phpbb_garage_custom_fields_data'
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_garage_custom_fields_lang'
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	option_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	field_type tinyint(4) DEFAULT '0' NOT NULL,
	lang_value blob NOT NULL,
	PRIMARY KEY (field_id, lang_id, option_id)
);


# Table: 'phpbb_garage_lang'
CREATE TABLE phpbb_garage_lang (
	field_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_name blob NOT NULL,
	lang_explain blob NOT NULL,
	lang_default_value blob NOT NULL,
	PRIMARY KEY (field_id, lang_id)
);


