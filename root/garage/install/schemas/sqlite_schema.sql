#
# $Id$
#

BEGIN TRANSACTION;

# Table: 'phpbb_garage_vehicles'
CREATE TABLE phpbb_garage_vehicles (
	id INTEGER PRIMARY KEY NOT NULL ,
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	made_year INTEGER UNSIGNED NOT NULL DEFAULT '2007',
	engine_type tinyint(2) NOT NULL DEFAULT '0',
	colour text(65535) NOT NULL DEFAULT '',
	mileage INTEGER UNSIGNED NOT NULL DEFAULT '0',
	mileage_unit varchar(32) NOT NULL DEFAULT 'Miles',
	price INTEGER UNSIGNED NOT NULL DEFAULT '0',
	currency varchar(32) NOT NULL DEFAULT 'EUR',
	comments mediumtext(16777215) NOT NULL DEFAULT '',
	views INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_created INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_updated INTEGER UNSIGNED NOT NULL DEFAULT '0',
	make_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	model_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	main_vehicle INTEGER UNSIGNED NOT NULL DEFAULT '0',
	weighted_rating decimal(4,2) NOT NULL DEFAULT '0',
	bbcode_bitfield varchar(255) NOT NULL DEFAULT '',
	bbcode_uid varchar(5) NOT NULL DEFAULT '',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_vehicles_date_created ON phpbb_garage_vehicles (date_created);
CREATE INDEX phpbb_garage_vehicles_date_updated ON phpbb_garage_vehicles (date_updated);
CREATE INDEX phpbb_garage_vehicles_user_id ON phpbb_garage_vehicles (user_id);
CREATE INDEX phpbb_garage_vehicles_views ON phpbb_garage_vehicles (views);

# Table: 'phpbb_garage_business'
CREATE TABLE phpbb_garage_business (
	id INTEGER PRIMARY KEY NOT NULL ,
	title text(65535) NOT NULL DEFAULT '',
	address varchar(255) NOT NULL DEFAULT '',
	telephone varchar(100) NOT NULL DEFAULT '',
	fax varchar(100) NOT NULL DEFAULT '',
	website varchar(255) NOT NULL DEFAULT '',
	email varchar(100) NOT NULL DEFAULT '',
	opening_hours varchar(255) NOT NULL DEFAULT '',
	insurance INTEGER UNSIGNED NOT NULL DEFAULT '0',
	garage INTEGER UNSIGNED NOT NULL DEFAULT '0',
	retail INTEGER UNSIGNED NOT NULL DEFAULT '0',
	product INTEGER UNSIGNED NOT NULL DEFAULT '0',
	dynocentre INTEGER UNSIGNED NOT NULL DEFAULT '0',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comments text(65535) NOT NULL DEFAULT ''
);

CREATE INDEX phpbb_garage_business_insurance ON phpbb_garage_business (insurance);
CREATE INDEX phpbb_garage_business_garage ON phpbb_garage_business (garage);
CREATE INDEX phpbb_garage_business_retail ON phpbb_garage_business (retail);
CREATE INDEX phpbb_garage_business_product ON phpbb_garage_business (product);
CREATE INDEX phpbb_garage_business_dynocentre ON phpbb_garage_business (dynocentre);

# Table: 'phpbb_garage_categories'
CREATE TABLE phpbb_garage_categories (
	id INTEGER PRIMARY KEY NOT NULL ,
	title text(65535) NOT NULL DEFAULT '',
	field_order INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_categories_title ON phpbb_garage_categories (title);
CREATE INDEX phpbb_garage_categories_id ON phpbb_garage_categories (id, title);

# Table: 'phpbb_garage_config'
CREATE TABLE phpbb_garage_config (
	config_name varchar(255) NOT NULL DEFAULT '',
	config_value varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (config_name)
);


# Table: 'phpbb_garage_vehicles_gallery'
CREATE TABLE phpbb_garage_vehicles_gallery (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	hilite INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_vehicles_gallery_vehicle_id ON phpbb_garage_vehicles_gallery (vehicle_id);
CREATE INDEX phpbb_garage_vehicles_gallery_image_id ON phpbb_garage_vehicles_gallery (image_id);

# Table: 'phpbb_garage_modifications_gallery'
CREATE TABLE phpbb_garage_modifications_gallery (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	modification_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	hilite INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_modifications_gallery_vehicle_id ON phpbb_garage_modifications_gallery (vehicle_id);
CREATE INDEX phpbb_garage_modifications_gallery_image_id ON phpbb_garage_modifications_gallery (image_id);

# Table: 'phpbb_garage_quartermiles_gallery'
CREATE TABLE phpbb_garage_quartermiles_gallery (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	quartermile_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	hilite INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_quartermiles_gallery_vehicle_id ON phpbb_garage_quartermiles_gallery (vehicle_id);
CREATE INDEX phpbb_garage_quartermiles_gallery_image_id ON phpbb_garage_quartermiles_gallery (image_id);

# Table: 'phpbb_garage_dynoruns_gallery'
CREATE TABLE phpbb_garage_dynoruns_gallery (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	dynorun_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	hilite INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_dynoruns_gallery_vehicle_id ON phpbb_garage_dynoruns_gallery (vehicle_id);
CREATE INDEX phpbb_garage_dynoruns_gallery_image_id ON phpbb_garage_dynoruns_gallery (image_id);

# Table: 'phpbb_garage_laps_gallery'
CREATE TABLE phpbb_garage_laps_gallery (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	lap_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	image_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	hilite INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_laps_gallery_vehicle_id ON phpbb_garage_laps_gallery (vehicle_id);
CREATE INDEX phpbb_garage_laps_gallery_image_id ON phpbb_garage_laps_gallery (image_id);

# Table: 'phpbb_garage_guestbooks'
CREATE TABLE phpbb_garage_guestbooks (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	author_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	post_date INTEGER UNSIGNED NOT NULL DEFAULT '0',
	ip_address varchar(40) NOT NULL DEFAULT '',
	bbcode_bitfield varchar(255) NOT NULL DEFAULT '',
	bbcode_uid varchar(5) NOT NULL DEFAULT '',
	bbcode_options INTEGER UNSIGNED NOT NULL DEFAULT '7',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0',
	post mediumtext(16777215) NOT NULL DEFAULT ''
);

CREATE INDEX phpbb_garage_guestbooks_vehicle_id ON phpbb_garage_guestbooks (vehicle_id);
CREATE INDEX phpbb_garage_guestbooks_author_id ON phpbb_garage_guestbooks (author_id);
CREATE INDEX phpbb_garage_guestbooks_post_date ON phpbb_garage_guestbooks (post_date);

# Table: 'phpbb_garage_images'
CREATE TABLE phpbb_garage_images (
	attach_id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_location varchar(255) NOT NULL DEFAULT '',
	attach_hits INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_ext varchar(100) NOT NULL DEFAULT '',
	attach_file varchar(255) NOT NULL DEFAULT '',
	attach_thumb_location varchar(255) NOT NULL DEFAULT '',
	attach_thumb_width INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_thumb_height INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_is_image INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_date INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_filesize INTEGER UNSIGNED NOT NULL DEFAULT '0',
	attach_thumb_filesize INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_garage_premiums'
CREATE TABLE phpbb_garage_premiums (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	business_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	cover_type_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	premium INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comments mediumtext(16777215) NOT NULL 
);


# Table: 'phpbb_garage_makes'
CREATE TABLE phpbb_garage_makes (
	id INTEGER PRIMARY KEY NOT NULL ,
	make varchar(255) NOT NULL DEFAULT '',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_makes_make ON phpbb_garage_makes (make);

# Table: 'phpbb_garage_models'
CREATE TABLE phpbb_garage_models (
	id INTEGER PRIMARY KEY NOT NULL ,
	make_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	model varchar(255) NOT NULL DEFAULT '',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_models_make_id ON phpbb_garage_models (make_id);

# Table: 'phpbb_garage_modifications'
CREATE TABLE phpbb_garage_modifications (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	category_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	manufacturer_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	product_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	price INTEGER UNSIGNED NOT NULL DEFAULT '0',
	install_price INTEGER UNSIGNED NOT NULL DEFAULT '0',
	product_rating tinyint(2) NOT NULL DEFAULT '0',
	purchase_rating tinyint(2) NOT NULL DEFAULT '0',
	install_rating tinyint(2) NOT NULL DEFAULT '0',
	shop_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	installer_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	comments mediumtext(16777215) NOT NULL DEFAULT '',
	install_comments mediumtext(16777215) NOT NULL DEFAULT '',
	date_created INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_updated INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_modifications_user_id ON phpbb_garage_modifications (user_id);
CREATE INDEX phpbb_garage_modifications_vehicle_id_2 ON phpbb_garage_modifications (vehicle_id, category_id);
CREATE INDEX phpbb_garage_modifications_category_id ON phpbb_garage_modifications (category_id);
CREATE INDEX phpbb_garage_modifications_vehicle_id ON phpbb_garage_modifications (vehicle_id);
CREATE INDEX phpbb_garage_modifications_date_created ON phpbb_garage_modifications (date_created);
CREATE INDEX phpbb_garage_modifications_date_updated ON phpbb_garage_modifications (date_updated);

# Table: 'phpbb_garage_products'
CREATE TABLE phpbb_garage_products (
	id INTEGER PRIMARY KEY NOT NULL ,
	business_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	category_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	title varchar(255) NOT NULL DEFAULT '',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_products_business_id ON phpbb_garage_products (business_id);
CREATE INDEX phpbb_garage_products_category_id ON phpbb_garage_products (category_id);

# Table: 'phpbb_garage_quartermiles'
CREATE TABLE phpbb_garage_quartermiles (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	rt decimal(6,3) NOT NULL DEFAULT '0',
	sixty decimal(6,3) NOT NULL DEFAULT '0',
	three decimal(6,3) NOT NULL DEFAULT '0',
	eighth decimal(6,3) NOT NULL DEFAULT '0',
	eighthmph decimal(6,3) NOT NULL DEFAULT '0',
	thou decimal(6,3) NOT NULL DEFAULT '0',
	quart decimal(6,3) NOT NULL DEFAULT '0',
	quartmph decimal(6,3) NOT NULL DEFAULT '0',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0',
	dynorun_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_created INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_updated INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_garage_dynoruns'
CREATE TABLE phpbb_garage_dynoruns (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	dynocentre_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	bhp decimal(6,2) NOT NULL DEFAULT '0',
	bhp_unit varchar(32) NOT NULL DEFAULT '',
	torque decimal(6,2) NOT NULL DEFAULT '0',
	torque_unit varchar(32) NOT NULL DEFAULT '',
	boost decimal(6,2) NOT NULL DEFAULT '0',
	boost_unit varchar(32) NOT NULL DEFAULT '',
	nitrous INTEGER UNSIGNED NOT NULL DEFAULT '0',
	peakpoint decimal(7,3) NOT NULL DEFAULT '0',
	date_created INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_updated INTEGER UNSIGNED NOT NULL DEFAULT '0',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_garage_ratings'
CREATE TABLE phpbb_garage_ratings (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	rating tinyint(2) NOT NULL DEFAULT '0',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	rate_date INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_garage_tracks'
CREATE TABLE phpbb_garage_tracks (
	id INTEGER PRIMARY KEY NOT NULL ,
	title varchar(255) NOT NULL DEFAULT '',
	length varchar(32) NOT NULL DEFAULT '',
	mileage_unit varchar(32) NOT NULL DEFAULT '',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);


# Table: 'phpbb_garage_laps'
CREATE TABLE phpbb_garage_laps (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	track_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	condition_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	type_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	minute INTEGER UNSIGNED NOT NULL DEFAULT '0',
	second INTEGER UNSIGNED NOT NULL DEFAULT '0',
	millisecond INTEGER UNSIGNED NOT NULL DEFAULT '0',
	pending INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_laps_vehicle_id ON phpbb_garage_laps (vehicle_id);
CREATE INDEX phpbb_garage_laps_track_id ON phpbb_garage_laps (track_id);

# Table: 'phpbb_garage_service_history'
CREATE TABLE phpbb_garage_service_history (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	garage_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	type_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	price INTEGER UNSIGNED NOT NULL DEFAULT '0',
	rating tinyint(2) NOT NULL DEFAULT '0',
	mileage INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_created INTEGER UNSIGNED NOT NULL DEFAULT '0',
	date_updated INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_service_history_vehicle_id ON phpbb_garage_service_history (vehicle_id);
CREATE INDEX phpbb_garage_service_history_garage_id ON phpbb_garage_service_history (garage_id);

# Table: 'phpbb_garage_blog'
CREATE TABLE phpbb_garage_blog (
	id INTEGER PRIMARY KEY NOT NULL ,
	vehicle_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	blog_title text(65535) NOT NULL DEFAULT '',
	blog_text mediumtext(16777215) NOT NULL DEFAULT '',
	blog_date INTEGER UNSIGNED NOT NULL DEFAULT '0',
	bbcode_bitfield varchar(255) NOT NULL DEFAULT '',
	bbcode_uid varchar(5) NOT NULL DEFAULT ''
);

CREATE INDEX phpbb_garage_blog_vehicle_id ON phpbb_garage_blog (vehicle_id);
CREATE INDEX phpbb_garage_blog_user_id ON phpbb_garage_blog (user_id);

# Table: 'phpbb_garage_custom_fields'
CREATE TABLE phpbb_garage_custom_fields (
	field_id INTEGER PRIMARY KEY NOT NULL ,
	field_name varchar(255) NOT NULL DEFAULT '',
	field_type tinyint(4) NOT NULL DEFAULT '0',
	field_ident varchar(20) NOT NULL DEFAULT '',
	field_length varchar(20) NOT NULL DEFAULT '',
	field_minlen varchar(255) NOT NULL DEFAULT '',
	field_maxlen varchar(255) NOT NULL DEFAULT '',
	field_novalue varchar(255) NOT NULL DEFAULT '',
	field_default_value varchar(255) NOT NULL DEFAULT '',
	field_validation varchar(20) NOT NULL DEFAULT '',
	field_required INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_show_on_reg INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_hide INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_no_view INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_active INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_order INTEGER UNSIGNED NOT NULL DEFAULT '0'
);

CREATE INDEX phpbb_garage_custom_fields_fld_type ON phpbb_garage_custom_fields (field_type);
CREATE INDEX phpbb_garage_custom_fields_fld_ordr ON phpbb_garage_custom_fields (field_order);

# Table: 'phpbb_garage_custom_fields_data'
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (user_id)
);


# Table: 'phpbb_garage_custom_fields_lang'
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	lang_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	option_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	field_type tinyint(4) NOT NULL DEFAULT '0',
	lang_value varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (field_id, lang_id, option_id)
);


# Table: 'phpbb_garage_lang'
CREATE TABLE phpbb_garage_lang (
	field_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	lang_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
	lang_name varchar(255) NOT NULL DEFAULT '',
	lang_explain text(65535) NOT NULL DEFAULT '',
	lang_default_value varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (field_id, lang_id)
);



COMMIT;