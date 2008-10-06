/*

 $Id$

*/

BEGIN;

/*
	Table: 'phpbb_garage_vehicles'
*/
CREATE SEQUENCE phpbb_garage_vehicles_seq;

CREATE TABLE phpbb_garage_vehicles (
	id INT4 DEFAULT nextval('phpbb_garage_vehicles_seq'),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	made_year INT4 DEFAULT '2007' NOT NULL CHECK (made_year >= 0),
	engine_type INT2 DEFAULT '0' NOT NULL,
	colour varchar(100) DEFAULT '' NOT NULL,
	mileage INT4 DEFAULT '0' NOT NULL CHECK (mileage >= 0),
	mileage_unit varchar(32) DEFAULT 'Miles' NOT NULL,
	price INT4 DEFAULT '0' NOT NULL CHECK (price >= 0),
	currency varchar(32) DEFAULT 'EUR' NOT NULL,
	comments TEXT DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options INT4 DEFAULT '7' NOT NULL CHECK (bbcode_options >= 0),
	views INT4 DEFAULT '0' NOT NULL CHECK (views >= 0),
	date_created INT4 DEFAULT '0' NOT NULL CHECK (date_created >= 0),
	date_updated INT4 DEFAULT '0' NOT NULL CHECK (date_updated >= 0),
	make_id INT4 DEFAULT '0' NOT NULL CHECK (make_id >= 0),
	model_id INT4 DEFAULT '0' NOT NULL CHECK (model_id >= 0),
	main_vehicle INT2 DEFAULT '0' NOT NULL CHECK (main_vehicle >= 0),
	weighted_rating decimal(4,2) DEFAULT '0' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_vehicles_date_created ON phpbb_garage_vehicles (date_created);
CREATE INDEX phpbb_garage_vehicles_date_updated ON phpbb_garage_vehicles (date_updated);
CREATE INDEX phpbb_garage_vehicles_user_id ON phpbb_garage_vehicles (user_id);
CREATE INDEX phpbb_garage_vehicles_views ON phpbb_garage_vehicles (views);

/*
	Table: 'phpbb_garage_business'
*/
CREATE SEQUENCE phpbb_garage_business_seq;

CREATE TABLE phpbb_garage_business (
	id INT4 DEFAULT nextval('phpbb_garage_business_seq'),
	title varchar(100) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	telephone varchar(100) DEFAULT '' NOT NULL,
	fax varchar(100) DEFAULT '' NOT NULL,
	website varchar(255) DEFAULT '' NOT NULL,
	email varchar(100) DEFAULT '' NOT NULL,
	opening_hours varchar(255) DEFAULT '' NOT NULL,
	insurance INT2 DEFAULT '0' NOT NULL CHECK (insurance >= 0),
	garage INT2 DEFAULT '0' NOT NULL CHECK (garage >= 0),
	retail INT2 DEFAULT '0' NOT NULL CHECK (retail >= 0),
	product INT2 DEFAULT '0' NOT NULL CHECK (product >= 0),
	dynocentre INT2 DEFAULT '0' NOT NULL CHECK (dynocentre >= 0),
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_business_insurance ON phpbb_garage_business (insurance);
CREATE INDEX phpbb_garage_business_garage ON phpbb_garage_business (garage);
CREATE INDEX phpbb_garage_business_retail ON phpbb_garage_business (retail);
CREATE INDEX phpbb_garage_business_product ON phpbb_garage_business (product);
CREATE INDEX phpbb_garage_business_dynocentre ON phpbb_garage_business (dynocentre);

/*
	Table: 'phpbb_garage_categories'
*/
CREATE SEQUENCE phpbb_garage_categories_seq;

CREATE TABLE phpbb_garage_categories (
	id INT4 DEFAULT nextval('phpbb_garage_categories_seq'),
	title varchar(4000) DEFAULT '' NOT NULL,
	field_order INT2 DEFAULT '0' NOT NULL CHECK (field_order >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_categories_title ON phpbb_garage_categories (title);
CREATE INDEX phpbb_garage_categories_id ON phpbb_garage_categories (id, title);

/*
	Table: 'phpbb_garage_config'
*/
CREATE TABLE phpbb_garage_config (
	config_name varchar(255) DEFAULT '' NOT NULL,
	config_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (config_name)
);


/*
	Table: 'phpbb_garage_vehicles_gallery'
*/
CREATE SEQUENCE phpbb_garage_vehicles_gallery_seq;

CREATE TABLE phpbb_garage_vehicles_gallery (
	id INT4 DEFAULT nextval('phpbb_garage_vehicles_gallery_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	hilite INT2 DEFAULT '0' NOT NULL CHECK (hilite >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_vehicles_gallery_vehicle_id ON phpbb_garage_vehicles_gallery (vehicle_id);
CREATE INDEX phpbb_garage_vehicles_gallery_image_id ON phpbb_garage_vehicles_gallery (image_id);

/*
	Table: 'phpbb_garage_modifications_gallery'
*/
CREATE SEQUENCE phpbb_garage_modifications_gallery_seq;

CREATE TABLE phpbb_garage_modifications_gallery (
	id INT4 DEFAULT nextval('phpbb_garage_modifications_gallery_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	modification_id INT4 DEFAULT '0' NOT NULL CHECK (modification_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	hilite INT2 DEFAULT '0' NOT NULL CHECK (hilite >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_modifications_gallery_vehicle_id ON phpbb_garage_modifications_gallery (vehicle_id);
CREATE INDEX phpbb_garage_modifications_gallery_image_id ON phpbb_garage_modifications_gallery (image_id);

/*
	Table: 'phpbb_garage_quartermiles_gallery'
*/
CREATE SEQUENCE phpbb_garage_quartermiles_gallery_seq;

CREATE TABLE phpbb_garage_quartermiles_gallery (
	id INT4 DEFAULT nextval('phpbb_garage_quartermiles_gallery_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	quartermile_id INT4 DEFAULT '0' NOT NULL CHECK (quartermile_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	hilite INT2 DEFAULT '0' NOT NULL CHECK (hilite >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_quartermiles_gallery_vehicle_id ON phpbb_garage_quartermiles_gallery (vehicle_id);
CREATE INDEX phpbb_garage_quartermiles_gallery_image_id ON phpbb_garage_quartermiles_gallery (image_id);

/*
	Table: 'phpbb_garage_dynoruns_gallery'
*/
CREATE SEQUENCE phpbb_garage_dynoruns_gallery_seq;

CREATE TABLE phpbb_garage_dynoruns_gallery (
	id INT4 DEFAULT nextval('phpbb_garage_dynoruns_gallery_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	dynorun_id INT4 DEFAULT '0' NOT NULL CHECK (dynorun_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	hilite INT2 DEFAULT '0' NOT NULL CHECK (hilite >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_dynoruns_gallery_vehicle_id ON phpbb_garage_dynoruns_gallery (vehicle_id);
CREATE INDEX phpbb_garage_dynoruns_gallery_image_id ON phpbb_garage_dynoruns_gallery (image_id);

/*
	Table: 'phpbb_garage_laps_gallery'
*/
CREATE SEQUENCE phpbb_garage_laps_gallery_seq;

CREATE TABLE phpbb_garage_laps_gallery (
	id INT4 DEFAULT nextval('phpbb_garage_laps_gallery_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	lap_id INT4 DEFAULT '0' NOT NULL CHECK (lap_id >= 0),
	image_id INT4 DEFAULT '0' NOT NULL CHECK (image_id >= 0),
	hilite INT2 DEFAULT '0' NOT NULL CHECK (hilite >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_laps_gallery_vehicle_id ON phpbb_garage_laps_gallery (vehicle_id);
CREATE INDEX phpbb_garage_laps_gallery_image_id ON phpbb_garage_laps_gallery (image_id);

/*
	Table: 'phpbb_garage_guestbooks'
*/
CREATE SEQUENCE phpbb_garage_guestbooks_seq;

CREATE TABLE phpbb_garage_guestbooks (
	id INT4 DEFAULT nextval('phpbb_garage_guestbooks_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	author_id INT4 DEFAULT '0' NOT NULL CHECK (author_id >= 0),
	post_date INT4 DEFAULT '0' NOT NULL CHECK (post_date >= 0),
	ip_address varchar(40) DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options INT4 DEFAULT '7' NOT NULL CHECK (bbcode_options >= 0),
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	post TEXT DEFAULT '' NOT NULL,
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_guestbooks_vehicle_id ON phpbb_garage_guestbooks (vehicle_id);
CREATE INDEX phpbb_garage_guestbooks_author_id ON phpbb_garage_guestbooks (author_id);
CREATE INDEX phpbb_garage_guestbooks_post_date ON phpbb_garage_guestbooks (post_date);

/*
	Table: 'phpbb_garage_images'
*/
CREATE SEQUENCE phpbb_garage_images_seq;

CREATE TABLE phpbb_garage_images (
	attach_id INT4 DEFAULT nextval('phpbb_garage_images_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	attach_location varchar(255) DEFAULT '' NOT NULL,
	attach_hits INT4 DEFAULT '0' NOT NULL CHECK (attach_hits >= 0),
	attach_ext varchar(100) DEFAULT '' NOT NULL,
	attach_file varchar(255) DEFAULT '' NOT NULL,
	attach_thumb_location varchar(255) DEFAULT '' NOT NULL,
	attach_thumb_width INT2 DEFAULT '0' NOT NULL CHECK (attach_thumb_width >= 0),
	attach_thumb_height INT2 DEFAULT '0' NOT NULL CHECK (attach_thumb_height >= 0),
	attach_is_image INT2 DEFAULT '0' NOT NULL CHECK (attach_is_image >= 0),
	attach_date INT4 DEFAULT '0' NOT NULL CHECK (attach_date >= 0),
	attach_filesize INT4 DEFAULT '0' NOT NULL CHECK (attach_filesize >= 0),
	attach_thumb_filesize INT4 DEFAULT '0' NOT NULL CHECK (attach_thumb_filesize >= 0),
	PRIMARY KEY (attach_id)
);


/*
	Table: 'phpbb_garage_premiums'
*/
CREATE SEQUENCE phpbb_garage_premiums_seq;

CREATE TABLE phpbb_garage_premiums (
	id INT4 DEFAULT nextval('phpbb_garage_premiums_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	business_id INT4 DEFAULT '0' NOT NULL CHECK (business_id >= 0),
	cover_type_id INT4 DEFAULT '0' NOT NULL CHECK (cover_type_id >= 0),
	premium INT4 DEFAULT '0' NOT NULL CHECK (premium >= 0),
	comments TEXT NOT NULL,
	PRIMARY KEY (id)
);


/*
	Table: 'phpbb_garage_makes'
*/
CREATE SEQUENCE phpbb_garage_makes_seq;

CREATE TABLE phpbb_garage_makes (
	id INT4 DEFAULT nextval('phpbb_garage_makes_seq'),
	make varchar(255) DEFAULT '' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_makes_make ON phpbb_garage_makes (make);

/*
	Table: 'phpbb_garage_models'
*/
CREATE SEQUENCE phpbb_garage_models_seq;

CREATE TABLE phpbb_garage_models (
	id INT4 DEFAULT nextval('phpbb_garage_models_seq'),
	make_id INT4 DEFAULT '0' NOT NULL CHECK (make_id >= 0),
	model varchar(255) DEFAULT '' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_models_make_id ON phpbb_garage_models (make_id);

/*
	Table: 'phpbb_garage_modifications'
*/
CREATE SEQUENCE phpbb_garage_modifications_seq;

CREATE TABLE phpbb_garage_modifications (
	id INT4 DEFAULT nextval('phpbb_garage_modifications_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	category_id INT4 DEFAULT '0' NOT NULL CHECK (category_id >= 0),
	manufacturer_id INT4 DEFAULT '0' NOT NULL CHECK (manufacturer_id >= 0),
	product_id INT4 DEFAULT '0' NOT NULL CHECK (product_id >= 0),
	price INT4 DEFAULT '0' NOT NULL CHECK (price >= 0),
	install_price INT4 DEFAULT '0' NOT NULL CHECK (install_price >= 0),
	product_rating INT2 DEFAULT '0' NOT NULL,
	purchase_rating INT2 DEFAULT '0' NOT NULL,
	install_rating INT2 DEFAULT '0' NOT NULL,
	shop_id INT4 DEFAULT '0' NOT NULL CHECK (shop_id >= 0),
	installer_id INT4 DEFAULT '0' NOT NULL CHECK (installer_id >= 0),
	comments TEXT DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options INT4 DEFAULT '7' NOT NULL CHECK (bbcode_options >= 0),
	install_comments TEXT DEFAULT '' NOT NULL,
	date_created INT4 DEFAULT '0' NOT NULL CHECK (date_created >= 0),
	date_updated INT4 DEFAULT '0' NOT NULL CHECK (date_updated >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_modifications_user_id ON phpbb_garage_modifications (user_id);
CREATE INDEX phpbb_garage_modifications_vehicle_id_2 ON phpbb_garage_modifications (vehicle_id, category_id);
CREATE INDEX phpbb_garage_modifications_category_id ON phpbb_garage_modifications (category_id);
CREATE INDEX phpbb_garage_modifications_vehicle_id ON phpbb_garage_modifications (vehicle_id);
CREATE INDEX phpbb_garage_modifications_date_created ON phpbb_garage_modifications (date_created);
CREATE INDEX phpbb_garage_modifications_date_updated ON phpbb_garage_modifications (date_updated);

/*
	Table: 'phpbb_garage_products'
*/
CREATE SEQUENCE phpbb_garage_products_seq;

CREATE TABLE phpbb_garage_products (
	id INT4 DEFAULT nextval('phpbb_garage_products_seq'),
	business_id INT4 DEFAULT '0' NOT NULL CHECK (business_id >= 0),
	category_id INT4 DEFAULT '0' NOT NULL CHECK (category_id >= 0),
	title varchar(255) DEFAULT '' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_products_business_id ON phpbb_garage_products (business_id);
CREATE INDEX phpbb_garage_products_category_id ON phpbb_garage_products (category_id);

/*
	Table: 'phpbb_garage_quartermiles'
*/
CREATE SEQUENCE phpbb_garage_quartermiles_seq;

CREATE TABLE phpbb_garage_quartermiles (
	id INT4 DEFAULT nextval('phpbb_garage_quartermiles_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	rt decimal(6,3) DEFAULT '0' NOT NULL,
	sixty decimal(6,3) DEFAULT '0' NOT NULL,
	three decimal(6,3) DEFAULT '0' NOT NULL,
	eighth decimal(6,3) DEFAULT '0' NOT NULL,
	eighthmph decimal(6,3) DEFAULT '0' NOT NULL,
	thou decimal(6,3) DEFAULT '0' NOT NULL,
	quart decimal(6,3) DEFAULT '0' NOT NULL,
	quartmph decimal(6,3) DEFAULT '0' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	dynorun_id INT4 DEFAULT '0' NOT NULL CHECK (dynorun_id >= 0),
	date_created INT4 DEFAULT '0' NOT NULL CHECK (date_created >= 0),
	date_updated INT4 DEFAULT '0' NOT NULL CHECK (date_updated >= 0),
	PRIMARY KEY (id)
);


/*
	Table: 'phpbb_garage_dynoruns'
*/
CREATE SEQUENCE phpbb_garage_dynoruns_seq;

CREATE TABLE phpbb_garage_dynoruns (
	id INT4 DEFAULT nextval('phpbb_garage_dynoruns_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	dynocentre_id INT4 DEFAULT '0' NOT NULL CHECK (dynocentre_id >= 0),
	bhp decimal(6,2) DEFAULT '0' NOT NULL,
	bhp_unit varchar(32) DEFAULT '' NOT NULL,
	torque decimal(6,2) DEFAULT '0' NOT NULL,
	torque_unit varchar(32) DEFAULT '' NOT NULL,
	boost decimal(6,2) DEFAULT '0' NOT NULL,
	boost_unit varchar(32) DEFAULT '' NOT NULL,
	nitrous INT4 DEFAULT '0' NOT NULL CHECK (nitrous >= 0),
	peakpoint decimal(7,3) DEFAULT '0' NOT NULL,
	date_created INT4 DEFAULT '0' NOT NULL CHECK (date_created >= 0),
	date_updated INT4 DEFAULT '0' NOT NULL CHECK (date_updated >= 0),
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);


/*
	Table: 'phpbb_garage_ratings'
*/
CREATE SEQUENCE phpbb_garage_ratings_seq;

CREATE TABLE phpbb_garage_ratings (
	id INT4 DEFAULT nextval('phpbb_garage_ratings_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	rating INT2 DEFAULT '0' NOT NULL,
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	rate_date INT4 DEFAULT '0' NOT NULL CHECK (rate_date >= 0),
	PRIMARY KEY (id)
);


/*
	Table: 'phpbb_garage_tracks'
*/
CREATE SEQUENCE phpbb_garage_tracks_seq;

CREATE TABLE phpbb_garage_tracks (
	id INT4 DEFAULT nextval('phpbb_garage_tracks_seq'),
	title varchar(255) DEFAULT '' NOT NULL,
	length varchar(32) DEFAULT '' NOT NULL,
	mileage_unit varchar(32) DEFAULT '' NOT NULL,
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);


/*
	Table: 'phpbb_garage_laps'
*/
CREATE SEQUENCE phpbb_garage_laps_seq;

CREATE TABLE phpbb_garage_laps (
	id INT4 DEFAULT nextval('phpbb_garage_laps_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	track_id INT4 DEFAULT '0' NOT NULL CHECK (track_id >= 0),
	condition_id INT4 DEFAULT '0' NOT NULL CHECK (condition_id >= 0),
	type_id INT4 DEFAULT '0' NOT NULL CHECK (type_id >= 0),
	minute INT4 DEFAULT '0' NOT NULL CHECK (minute >= 0),
	second INT4 DEFAULT '0' NOT NULL CHECK (second >= 0),
	millisecond INT4 DEFAULT '0' NOT NULL CHECK (millisecond >= 0),
	pending INT2 DEFAULT '0' NOT NULL CHECK (pending >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_laps_vehicle_id ON phpbb_garage_laps (vehicle_id);
CREATE INDEX phpbb_garage_laps_track_id ON phpbb_garage_laps (track_id);

/*
	Table: 'phpbb_garage_service_history'
*/
CREATE SEQUENCE phpbb_garage_service_history_seq;

CREATE TABLE phpbb_garage_service_history (
	id INT4 DEFAULT nextval('phpbb_garage_service_history_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	garage_id INT4 DEFAULT '0' NOT NULL CHECK (garage_id >= 0),
	type_id INT4 DEFAULT '0' NOT NULL CHECK (type_id >= 0),
	price INT4 DEFAULT '0' NOT NULL CHECK (price >= 0),
	rating INT2 DEFAULT '0' NOT NULL,
	mileage INT4 DEFAULT '0' NOT NULL CHECK (mileage >= 0),
	date_created INT4 DEFAULT '0' NOT NULL CHECK (date_created >= 0),
	date_updated INT4 DEFAULT '0' NOT NULL CHECK (date_updated >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_service_history_vehicle_id ON phpbb_garage_service_history (vehicle_id);
CREATE INDEX phpbb_garage_service_history_garage_id ON phpbb_garage_service_history (garage_id);

/*
	Table: 'phpbb_garage_blog'
*/
CREATE SEQUENCE phpbb_garage_blog_seq;

CREATE TABLE phpbb_garage_blog (
	id INT4 DEFAULT nextval('phpbb_garage_blog_seq'),
	vehicle_id INT4 DEFAULT '0' NOT NULL CHECK (vehicle_id >= 0),
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	blog_title varchar(100) DEFAULT '' NOT NULL,
	blog_text TEXT DEFAULT '' NOT NULL,
	blog_date INT4 DEFAULT '0' NOT NULL CHECK (blog_date >= 0),
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	bbcode_uid varchar(8) DEFAULT '' NOT NULL,
	bbcode_options INT4 DEFAULT '7' NOT NULL CHECK (bbcode_options >= 0),
	PRIMARY KEY (id)
);

CREATE INDEX phpbb_garage_blog_vehicle_id ON phpbb_garage_blog (vehicle_id);
CREATE INDEX phpbb_garage_blog_user_id ON phpbb_garage_blog (user_id);

/*
	Table: 'phpbb_garage_custom_fields'
*/
CREATE SEQUENCE phpbb_garage_custom_fields_seq;

CREATE TABLE phpbb_garage_custom_fields (
	field_id INT4 DEFAULT nextval('phpbb_garage_custom_fields_seq'),
	field_name varchar(255) DEFAULT '' NOT NULL,
	field_type INT2 DEFAULT '0' NOT NULL,
	field_ident varchar(20) DEFAULT '' NOT NULL,
	field_length varchar(20) DEFAULT '' NOT NULL,
	field_minlen varchar(255) DEFAULT '' NOT NULL,
	field_maxlen varchar(255) DEFAULT '' NOT NULL,
	field_novalue varchar(255) DEFAULT '' NOT NULL,
	field_default_value varchar(255) DEFAULT '' NOT NULL,
	field_validation varchar(20) DEFAULT '' NOT NULL,
	field_required INT2 DEFAULT '0' NOT NULL CHECK (field_required >= 0),
	field_show_on_reg INT2 DEFAULT '0' NOT NULL CHECK (field_show_on_reg >= 0),
	field_hide INT2 DEFAULT '0' NOT NULL CHECK (field_hide >= 0),
	field_no_view INT2 DEFAULT '0' NOT NULL CHECK (field_no_view >= 0),
	field_active INT2 DEFAULT '0' NOT NULL CHECK (field_active >= 0),
	field_order INT4 DEFAULT '0' NOT NULL CHECK (field_order >= 0),
	PRIMARY KEY (field_id)
);

CREATE INDEX phpbb_garage_custom_fields_fld_type ON phpbb_garage_custom_fields (field_type);
CREATE INDEX phpbb_garage_custom_fields_fld_ordr ON phpbb_garage_custom_fields (field_order);

/*
	Table: 'phpbb_garage_custom_fields_data'
*/
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
	PRIMARY KEY (user_id)
);


/*
	Table: 'phpbb_garage_custom_fields_lang'
*/
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id INT4 DEFAULT '0' NOT NULL CHECK (field_id >= 0),
	lang_id INT4 DEFAULT '0' NOT NULL CHECK (lang_id >= 0),
	option_id INT4 DEFAULT '0' NOT NULL CHECK (option_id >= 0),
	field_type INT2 DEFAULT '0' NOT NULL,
	lang_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id, option_id)
);


/*
	Table: 'phpbb_garage_lang'
*/
CREATE TABLE phpbb_garage_lang (
	field_id INT4 DEFAULT '0' NOT NULL CHECK (field_id >= 0),
	lang_id INT4 DEFAULT '0' NOT NULL CHECK (lang_id >= 0),
	lang_name varchar(255) DEFAULT '' NOT NULL,
	lang_explain varchar(4000) DEFAULT '' NOT NULL,
	lang_default_value varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (field_id, lang_id)
);



COMMIT;
