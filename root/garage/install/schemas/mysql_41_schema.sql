#
# $Id$
#

# Table: 'phpbb_garage_vehicles'
CREATE TABLE phpbb_garage_vehicles (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	made_year mediumint(8) UNSIGNED DEFAULT '2007' NOT NULL,
	engine_type tinyint(2) DEFAULT '0' NOT NULL,
	colour varchar(100) DEFAULT '' NOT NULL,
	mileage mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	mileage_unit varchar(32) DEFAULT 'Miles' NOT NULL,
	price decimal(10,2) DEFAULT '0' NOT NULL,
	currency varchar(32) DEFAULT 'EUR' NOT NULL,
	comments mediumtext NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_business'
CREATE TABLE phpbb_garage_business (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title varchar(100) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	telephone varchar(100) DEFAULT '' NOT NULL,
	fax varchar(100) DEFAULT '' NOT NULL,
	website varchar(255) DEFAULT '' NOT NULL,
	email varchar(100) DEFAULT '' NOT NULL,
	opening_hours varchar(255) DEFAULT '' NOT NULL,
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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_categories'
CREATE TABLE phpbb_garage_categories (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title text NOT NULL,
	field_order smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY title (title(255)),
	KEY id (id, title(255))
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_config'
CREATE TABLE phpbb_garage_config (
	config_name varchar(255) DEFAULT '' NOT NULL,
	config_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (config_name)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_vehicles_gallery'
CREATE TABLE phpbb_garage_vehicles_gallery (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	image_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	hilite tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY image_id (image_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_guestbooks'
CREATE TABLE phpbb_garage_guestbooks (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	author_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	post_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	ip_address varchar(40) DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	post mediumtext NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY author_id (author_id),
	KEY post_date (post_date)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_images'
CREATE TABLE phpbb_garage_images (
	attach_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	attach_location varchar(255) DEFAULT '' NOT NULL,
	attach_hits mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	attach_ext varchar(100) DEFAULT '' NOT NULL,
	attach_file varchar(255) DEFAULT '' NOT NULL,
	attach_thumb_location varchar(255) DEFAULT '' NOT NULL,
	attach_thumb_width smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	attach_thumb_height smallint(4) UNSIGNED DEFAULT '0' NOT NULL,
	attach_is_image tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	attach_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	attach_filesize int(20) UNSIGNED DEFAULT '0' NOT NULL,
	attach_thumb_filesize int(20) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (attach_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_premiums'
CREATE TABLE phpbb_garage_premiums (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	business_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	cover_type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	premium decimal(10,2) DEFAULT '0' NOT NULL,
	comments mediumtext NOT NULL,
	PRIMARY KEY (id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_makes'
CREATE TABLE phpbb_garage_makes (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	make varchar(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY make (make)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_models'
CREATE TABLE phpbb_garage_models (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	make_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	model varchar(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY make_id (make_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_modifications'
CREATE TABLE phpbb_garage_modifications (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	category_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	product_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	price decimal(10,2) DEFAULT '0' NOT NULL,
	install_price decimal(10,2) DEFAULT '0' NOT NULL,
	product_rating tinyint(2) DEFAULT '0' NOT NULL,
	purchase_rating tinyint(2) DEFAULT '0' NOT NULL,
	install_rating tinyint(2) DEFAULT '0' NOT NULL,
	shop_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	installer_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	comments mediumtext NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	install_comments mediumtext NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY user_id (user_id),
	KEY vehicle_id_2 (vehicle_id, category_id),
	KEY category_id (category_id),
	KEY vehicle_id (vehicle_id),
	KEY date_created (date_created),
	KEY date_updated (date_updated)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_products'
CREATE TABLE phpbb_garage_products (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	business_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	category_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY business_id (business_id),
	KEY category_id (category_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_dynoruns'
CREATE TABLE phpbb_garage_dynoruns (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	dynocentre_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	bhp decimal(6,2) DEFAULT '0' NOT NULL,
	bhp_unit varchar(32) DEFAULT '' NOT NULL,
	torque decimal(6,2) DEFAULT '0' NOT NULL,
	torque_unit varchar(32) DEFAULT '' NOT NULL,
	boost decimal(6,2) DEFAULT '0' NOT NULL,
	boost_unit varchar(32) DEFAULT '' NOT NULL,
	nitrous mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	peakpoint decimal(7,3) DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_ratings'
CREATE TABLE phpbb_garage_ratings (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rating tinyint(2) DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	rate_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_tracks'
CREATE TABLE phpbb_garage_tracks (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	title varchar(255) DEFAULT '' NOT NULL,
	length varchar(32) DEFAULT '' NOT NULL,
	mileage_unit varchar(32) DEFAULT '' NOT NULL,
	pending tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


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
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_service_history'
CREATE TABLE phpbb_garage_service_history (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	garage_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	type_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	price decimal(10,2) DEFAULT '0' NOT NULL,
	rating tinyint(2) DEFAULT '0' NOT NULL,
	mileage mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	date_created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	date_updated int(11) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY garage_id (garage_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_blog'
CREATE TABLE phpbb_garage_blog (
	id mediumint(8) UNSIGNED NOT NULL auto_increment,
	vehicle_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	blog_title varchar(100) DEFAULT '' NOT NULL,
	blog_text mediumtext NOT NULL,
	blog_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options mediumint(8) UNSIGNED DEFAULT '7' NOT NULL,
	PRIMARY KEY (id),
	KEY vehicle_id (vehicle_id),
	KEY user_id (user_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_custom_fields'
CREATE TABLE phpbb_garage_custom_fields (
	field_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	field_name varchar(255) DEFAULT '' NOT NULL,
	field_type tinyint(4) DEFAULT '0' NOT NULL,
	field_ident varchar(20) DEFAULT '' NOT NULL,
	field_length varchar(20) DEFAULT '' NOT NULL,
	field_minlen varchar(255) DEFAULT '' NOT NULL,
	field_maxlen varchar(255) DEFAULT '' NOT NULL,
	field_novalue varchar(255) DEFAULT '' NOT NULL,
	field_default_value varchar(255) DEFAULT '' NOT NULL,
	field_validation varchar(20) DEFAULT '' NOT NULL,
	field_required tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_show_on_reg tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_hide tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_no_view tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_active tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
	field_order mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (field_id),
	KEY fld_type (field_type),
	KEY fld_ordr (field_order)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_custom_fields_data'
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	PRIMARY KEY (user_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_custom_fields_lang'
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	option_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	field_type tinyint(4) DEFAULT '0' NOT NULL,
	lang_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id, option_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


# Table: 'phpbb_garage_lang'
CREATE TABLE phpbb_garage_lang (
	field_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
	lang_name varchar(255) DEFAULT '' NOT NULL,
	lang_explain text NOT NULL,
	lang_default_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


