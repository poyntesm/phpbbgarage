#
# $Id$
#

# Table: 'phpbb_garage_vehicles'
CREATE TABLE phpbb_garage_vehicles (
	id INTEGER NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	made_year INTEGER DEFAULT 2007 NOT NULL,
	engine_type INTEGER DEFAULT 0 NOT NULL,
	colour VARCHAR(100) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	mileage INTEGER DEFAULT 0 NOT NULL,
	mileage_unit VARCHAR(32) CHARACTER SET NONE DEFAULT 'Miles' NOT NULL,
	price INTEGER DEFAULT 0 NOT NULL,
	currency VARCHAR(32) CHARACTER SET NONE DEFAULT 'EUR' NOT NULL,
	comments BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	bbcode_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_options INTEGER DEFAULT 7 NOT NULL,
	views INTEGER DEFAULT 0 NOT NULL,
	date_created INTEGER DEFAULT 0 NOT NULL,
	date_updated INTEGER DEFAULT 0 NOT NULL,
	make_id INTEGER DEFAULT 0 NOT NULL,
	model_id INTEGER DEFAULT 0 NOT NULL,
	main_vehicle INTEGER DEFAULT 0 NOT NULL,
	weighted_rating DOUBLE PRECISION DEFAULT 0 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_vehicles ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_vehicles_date_created ON phpbb_garage_vehicles(date_created);;
CREATE INDEX phpbb_garage_vehicles_date_updated ON phpbb_garage_vehicles(date_updated);;
CREATE INDEX phpbb_garage_vehicles_user_id ON phpbb_garage_vehicles(user_id);;
CREATE INDEX phpbb_garage_vehicles_views ON phpbb_garage_vehicles(views);;

CREATE GENERATOR phpbb_garage_vehicles_gen;;
SET GENERATOR phpbb_garage_vehicles_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_vehicles FOR phpbb_garage_vehicles
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_vehicles_gen, 1);
END;;


# Table: 'phpbb_garage_business'
CREATE TABLE phpbb_garage_business (
	id INTEGER NOT NULL,
	title VARCHAR(100) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	address VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	telephone VARCHAR(100) CHARACTER SET NONE DEFAULT '' NOT NULL,
	fax VARCHAR(100) CHARACTER SET NONE DEFAULT '' NOT NULL,
	website VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	email VARCHAR(100) CHARACTER SET NONE DEFAULT '' NOT NULL,
	opening_hours VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	insurance INTEGER DEFAULT 0 NOT NULL,
	garage INTEGER DEFAULT 0 NOT NULL,
	retail INTEGER DEFAULT 0 NOT NULL,
	product INTEGER DEFAULT 0 NOT NULL,
	dynocentre INTEGER DEFAULT 0 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_business ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_business_insurance ON phpbb_garage_business(insurance);;
CREATE INDEX phpbb_garage_business_garage ON phpbb_garage_business(garage);;
CREATE INDEX phpbb_garage_business_retail ON phpbb_garage_business(retail);;
CREATE INDEX phpbb_garage_business_product ON phpbb_garage_business(product);;
CREATE INDEX phpbb_garage_business_dynocentre ON phpbb_garage_business(dynocentre);;

CREATE GENERATOR phpbb_garage_business_gen;;
SET GENERATOR phpbb_garage_business_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_business FOR phpbb_garage_business
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_business_gen, 1);
END;;


# Table: 'phpbb_garage_categories'
CREATE TABLE phpbb_garage_categories (
	id INTEGER NOT NULL,
	title BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	field_order INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_categories ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_categories_title ON phpbb_garage_categories(title);;
CREATE INDEX phpbb_garage_categories_id ON phpbb_garage_categories(id, title);;

CREATE GENERATOR phpbb_garage_categories_gen;;
SET GENERATOR phpbb_garage_categories_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_categories FOR phpbb_garage_categories
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_categories_gen, 1);
END;;


# Table: 'phpbb_garage_config'
CREATE TABLE phpbb_garage_config (
	config_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	config_value VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE
);;

ALTER TABLE phpbb_garage_config ADD PRIMARY KEY (config_name);;


# Table: 'phpbb_garage_vehicles_gallery'
CREATE TABLE phpbb_garage_vehicles_gallery (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	hilite INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_vehicles_gallery ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_vehicles_gallery_vehicle_id ON phpbb_garage_vehicles_gallery(vehicle_id);;
CREATE INDEX phpbb_garage_vehicles_gallery_image_id ON phpbb_garage_vehicles_gallery(image_id);;

CREATE GENERATOR phpbb_garage_vehicles_gallery_gen;;
SET GENERATOR phpbb_garage_vehicles_gallery_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_vehicles_gallery FOR phpbb_garage_vehicles_gallery
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_vehicles_gallery_gen, 1);
END;;


# Table: 'phpbb_garage_modifications_gallery'
CREATE TABLE phpbb_garage_modifications_gallery (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	modification_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	hilite INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_modifications_gallery ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_modifications_gallery_vehicle_id ON phpbb_garage_modifications_gallery(vehicle_id);;
CREATE INDEX phpbb_garage_modifications_gallery_image_id ON phpbb_garage_modifications_gallery(image_id);;

CREATE GENERATOR phpbb_garage_modifications_gallery_gen;;
SET GENERATOR phpbb_garage_modifications_gallery_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_modifications_gallery FOR phpbb_garage_modifications_gallery
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_modifications_gallery_gen, 1);
END;;


# Table: 'phpbb_garage_quartermiles_gallery'
CREATE TABLE phpbb_garage_quartermiles_gallery (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	quartermile_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	hilite INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_quartermiles_gallery ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_quartermiles_gallery_vehicle_id ON phpbb_garage_quartermiles_gallery(vehicle_id);;
CREATE INDEX phpbb_garage_quartermiles_gallery_image_id ON phpbb_garage_quartermiles_gallery(image_id);;

CREATE GENERATOR phpbb_garage_quartermiles_gallery_gen;;
SET GENERATOR phpbb_garage_quartermiles_gallery_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_quartermiles_gallery FOR phpbb_garage_quartermiles_gallery
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_quartermiles_gallery_gen, 1);
END;;


# Table: 'phpbb_garage_dynoruns_gallery'
CREATE TABLE phpbb_garage_dynoruns_gallery (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	dynorun_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	hilite INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_dynoruns_gallery ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_dynoruns_gallery_vehicle_id ON phpbb_garage_dynoruns_gallery(vehicle_id);;
CREATE INDEX phpbb_garage_dynoruns_gallery_image_id ON phpbb_garage_dynoruns_gallery(image_id);;

CREATE GENERATOR phpbb_garage_dynoruns_gallery_gen;;
SET GENERATOR phpbb_garage_dynoruns_gallery_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_dynoruns_gallery FOR phpbb_garage_dynoruns_gallery
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_dynoruns_gallery_gen, 1);
END;;


# Table: 'phpbb_garage_laps_gallery'
CREATE TABLE phpbb_garage_laps_gallery (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	lap_id INTEGER DEFAULT 0 NOT NULL,
	image_id INTEGER DEFAULT 0 NOT NULL,
	hilite INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_laps_gallery ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_laps_gallery_vehicle_id ON phpbb_garage_laps_gallery(vehicle_id);;
CREATE INDEX phpbb_garage_laps_gallery_image_id ON phpbb_garage_laps_gallery(image_id);;

CREATE GENERATOR phpbb_garage_laps_gallery_gen;;
SET GENERATOR phpbb_garage_laps_gallery_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_laps_gallery FOR phpbb_garage_laps_gallery
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_laps_gallery_gen, 1);
END;;


# Table: 'phpbb_garage_guestbooks'
CREATE TABLE phpbb_garage_guestbooks (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	author_id INTEGER DEFAULT 0 NOT NULL,
	post_date INTEGER DEFAULT 0 NOT NULL,
	ip_address VARCHAR(40) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_options INTEGER DEFAULT 7 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL,
	post BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL
);;

ALTER TABLE phpbb_garage_guestbooks ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_guestbooks_vehicle_id ON phpbb_garage_guestbooks(vehicle_id);;
CREATE INDEX phpbb_garage_guestbooks_author_id ON phpbb_garage_guestbooks(author_id);;
CREATE INDEX phpbb_garage_guestbooks_post_date ON phpbb_garage_guestbooks(post_date);;

CREATE GENERATOR phpbb_garage_guestbooks_gen;;
SET GENERATOR phpbb_garage_guestbooks_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_guestbooks FOR phpbb_garage_guestbooks
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_guestbooks_gen, 1);
END;;


# Table: 'phpbb_garage_images'
CREATE TABLE phpbb_garage_images (
	attach_id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	attach_location VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	attach_hits INTEGER DEFAULT 0 NOT NULL,
	attach_ext VARCHAR(100) CHARACTER SET NONE DEFAULT '' NOT NULL,
	attach_file VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	attach_thumb_location VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	attach_thumb_width INTEGER DEFAULT 0 NOT NULL,
	attach_thumb_height INTEGER DEFAULT 0 NOT NULL,
	attach_is_image INTEGER DEFAULT 0 NOT NULL,
	attach_date INTEGER DEFAULT 0 NOT NULL,
	attach_filesize INTEGER DEFAULT 0 NOT NULL,
	attach_thumb_filesize INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_images ADD PRIMARY KEY (attach_id);;


CREATE GENERATOR phpbb_garage_images_gen;;
SET GENERATOR phpbb_garage_images_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_images FOR phpbb_garage_images
BEFORE INSERT
AS
BEGIN
	NEW.attach_id = GEN_ID(phpbb_garage_images_gen, 1);
END;;


# Table: 'phpbb_garage_premiums'
CREATE TABLE phpbb_garage_premiums (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	business_id INTEGER DEFAULT 0 NOT NULL,
	cover_type_id INTEGER DEFAULT 0 NOT NULL,
	premium INTEGER DEFAULT 0 NOT NULL,
	comments BLOB SUB_TYPE TEXT CHARACTER SET UTF8 NOT NULL
);;

ALTER TABLE phpbb_garage_premiums ADD PRIMARY KEY (id);;


CREATE GENERATOR phpbb_garage_premiums_gen;;
SET GENERATOR phpbb_garage_premiums_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_premiums FOR phpbb_garage_premiums
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_premiums_gen, 1);
END;;


# Table: 'phpbb_garage_makes'
CREATE TABLE phpbb_garage_makes (
	id INTEGER NOT NULL,
	make VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_makes ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_makes_make ON phpbb_garage_makes(make);;

CREATE GENERATOR phpbb_garage_makes_gen;;
SET GENERATOR phpbb_garage_makes_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_makes FOR phpbb_garage_makes
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_makes_gen, 1);
END;;


# Table: 'phpbb_garage_models'
CREATE TABLE phpbb_garage_models (
	id INTEGER NOT NULL,
	make_id INTEGER DEFAULT 0 NOT NULL,
	model VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_models ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_models_make_id ON phpbb_garage_models(make_id);;

CREATE GENERATOR phpbb_garage_models_gen;;
SET GENERATOR phpbb_garage_models_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_models FOR phpbb_garage_models
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_models_gen, 1);
END;;


# Table: 'phpbb_garage_modifications'
CREATE TABLE phpbb_garage_modifications (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	category_id INTEGER DEFAULT 0 NOT NULL,
	manufacturer_id INTEGER DEFAULT 0 NOT NULL,
	product_id INTEGER DEFAULT 0 NOT NULL,
	price INTEGER DEFAULT 0 NOT NULL,
	install_price INTEGER DEFAULT 0 NOT NULL,
	product_rating INTEGER DEFAULT 0 NOT NULL,
	purchase_rating INTEGER DEFAULT 0 NOT NULL,
	install_rating INTEGER DEFAULT 0 NOT NULL,
	shop_id INTEGER DEFAULT 0 NOT NULL,
	installer_id INTEGER DEFAULT 0 NOT NULL,
	comments BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	bbcode_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_options INTEGER DEFAULT 7 NOT NULL,
	install_comments BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	date_created INTEGER DEFAULT 0 NOT NULL,
	date_updated INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_modifications ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_modifications_user_id ON phpbb_garage_modifications(user_id);;
CREATE INDEX phpbb_garage_modifications_vehicle_id_2 ON phpbb_garage_modifications(vehicle_id, category_id);;
CREATE INDEX phpbb_garage_modifications_category_id ON phpbb_garage_modifications(category_id);;
CREATE INDEX phpbb_garage_modifications_vehicle_id ON phpbb_garage_modifications(vehicle_id);;
CREATE INDEX phpbb_garage_modifications_date_created ON phpbb_garage_modifications(date_created);;
CREATE INDEX phpbb_garage_modifications_date_updated ON phpbb_garage_modifications(date_updated);;

CREATE GENERATOR phpbb_garage_modifications_gen;;
SET GENERATOR phpbb_garage_modifications_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_modifications FOR phpbb_garage_modifications
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_modifications_gen, 1);
END;;


# Table: 'phpbb_garage_products'
CREATE TABLE phpbb_garage_products (
	id INTEGER NOT NULL,
	business_id INTEGER DEFAULT 0 NOT NULL,
	category_id INTEGER DEFAULT 0 NOT NULL,
	title VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_products ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_products_business_id ON phpbb_garage_products(business_id);;
CREATE INDEX phpbb_garage_products_category_id ON phpbb_garage_products(category_id);;

CREATE GENERATOR phpbb_garage_products_gen;;
SET GENERATOR phpbb_garage_products_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_products FOR phpbb_garage_products
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_products_gen, 1);
END;;


# Table: 'phpbb_garage_quartermiles'
CREATE TABLE phpbb_garage_quartermiles (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	rt DOUBLE PRECISION DEFAULT 0 NOT NULL,
	sixty DOUBLE PRECISION DEFAULT 0 NOT NULL,
	three DOUBLE PRECISION DEFAULT 0 NOT NULL,
	eighth DOUBLE PRECISION DEFAULT 0 NOT NULL,
	eighthmph DOUBLE PRECISION DEFAULT 0 NOT NULL,
	thou DOUBLE PRECISION DEFAULT 0 NOT NULL,
	quart DOUBLE PRECISION DEFAULT 0 NOT NULL,
	quartmph DOUBLE PRECISION DEFAULT 0 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL,
	dynorun_id INTEGER DEFAULT 0 NOT NULL,
	date_created INTEGER DEFAULT 0 NOT NULL,
	date_updated INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_quartermiles ADD PRIMARY KEY (id);;


CREATE GENERATOR phpbb_garage_quartermiles_gen;;
SET GENERATOR phpbb_garage_quartermiles_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_quartermiles FOR phpbb_garage_quartermiles
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_quartermiles_gen, 1);
END;;


# Table: 'phpbb_garage_dynoruns'
CREATE TABLE phpbb_garage_dynoruns (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	dynocentre_id INTEGER DEFAULT 0 NOT NULL,
	bhp DOUBLE PRECISION DEFAULT 0 NOT NULL,
	bhp_unit VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
	torque DOUBLE PRECISION DEFAULT 0 NOT NULL,
	torque_unit VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
	boost DOUBLE PRECISION DEFAULT 0 NOT NULL,
	boost_unit VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
	nitrous INTEGER DEFAULT 0 NOT NULL,
	peakpoint DOUBLE PRECISION DEFAULT 0 NOT NULL,
	date_created INTEGER DEFAULT 0 NOT NULL,
	date_updated INTEGER DEFAULT 0 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_dynoruns ADD PRIMARY KEY (id);;


CREATE GENERATOR phpbb_garage_dynoruns_gen;;
SET GENERATOR phpbb_garage_dynoruns_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_dynoruns FOR phpbb_garage_dynoruns
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_dynoruns_gen, 1);
END;;


# Table: 'phpbb_garage_ratings'
CREATE TABLE phpbb_garage_ratings (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	rating INTEGER DEFAULT 0 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	rate_date INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_ratings ADD PRIMARY KEY (id);;


CREATE GENERATOR phpbb_garage_ratings_gen;;
SET GENERATOR phpbb_garage_ratings_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_ratings FOR phpbb_garage_ratings
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_ratings_gen, 1);
END;;


# Table: 'phpbb_garage_tracks'
CREATE TABLE phpbb_garage_tracks (
	id INTEGER NOT NULL,
	title VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	length VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
	mileage_unit VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_tracks ADD PRIMARY KEY (id);;


CREATE GENERATOR phpbb_garage_tracks_gen;;
SET GENERATOR phpbb_garage_tracks_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_tracks FOR phpbb_garage_tracks
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_tracks_gen, 1);
END;;


# Table: 'phpbb_garage_laps'
CREATE TABLE phpbb_garage_laps (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	track_id INTEGER DEFAULT 0 NOT NULL,
	condition_id INTEGER DEFAULT 0 NOT NULL,
	type_id INTEGER DEFAULT 0 NOT NULL,
	minute INTEGER DEFAULT 0 NOT NULL,
	second INTEGER DEFAULT 0 NOT NULL,
	millisecond INTEGER DEFAULT 0 NOT NULL,
	pending INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_laps ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_laps_vehicle_id ON phpbb_garage_laps(vehicle_id);;
CREATE INDEX phpbb_garage_laps_track_id ON phpbb_garage_laps(track_id);;

CREATE GENERATOR phpbb_garage_laps_gen;;
SET GENERATOR phpbb_garage_laps_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_laps FOR phpbb_garage_laps
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_laps_gen, 1);
END;;


# Table: 'phpbb_garage_service_history'
CREATE TABLE phpbb_garage_service_history (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	garage_id INTEGER DEFAULT 0 NOT NULL,
	type_id INTEGER DEFAULT 0 NOT NULL,
	price INTEGER DEFAULT 0 NOT NULL,
	rating INTEGER DEFAULT 0 NOT NULL,
	mileage INTEGER DEFAULT 0 NOT NULL,
	date_created INTEGER DEFAULT 0 NOT NULL,
	date_updated INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_service_history ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_service_history_vehicle_id ON phpbb_garage_service_history(vehicle_id);;
CREATE INDEX phpbb_garage_service_history_garage_id ON phpbb_garage_service_history(garage_id);;

CREATE GENERATOR phpbb_garage_service_history_gen;;
SET GENERATOR phpbb_garage_service_history_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_service_history FOR phpbb_garage_service_history
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_service_history_gen, 1);
END;;


# Table: 'phpbb_garage_blog'
CREATE TABLE phpbb_garage_blog (
	id INTEGER NOT NULL,
	vehicle_id INTEGER DEFAULT 0 NOT NULL,
	user_id INTEGER DEFAULT 0 NOT NULL,
	blog_title VARCHAR(100) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	blog_text BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	blog_date INTEGER DEFAULT 0 NOT NULL,
	bbcode_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
	bbcode_options INTEGER DEFAULT 7 NOT NULL
);;

ALTER TABLE phpbb_garage_blog ADD PRIMARY KEY (id);;

CREATE INDEX phpbb_garage_blog_vehicle_id ON phpbb_garage_blog(vehicle_id);;
CREATE INDEX phpbb_garage_blog_user_id ON phpbb_garage_blog(user_id);;

CREATE GENERATOR phpbb_garage_blog_gen;;
SET GENERATOR phpbb_garage_blog_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_blog FOR phpbb_garage_blog
BEFORE INSERT
AS
BEGIN
	NEW.id = GEN_ID(phpbb_garage_blog_gen, 1);
END;;


# Table: 'phpbb_garage_custom_fields'
CREATE TABLE phpbb_garage_custom_fields (
	field_id INTEGER NOT NULL,
	field_name VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	field_type INTEGER DEFAULT 0 NOT NULL,
	field_ident VARCHAR(20) CHARACTER SET NONE DEFAULT '' NOT NULL,
	field_length VARCHAR(20) CHARACTER SET NONE DEFAULT '' NOT NULL,
	field_minlen VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	field_maxlen VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
	field_novalue VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	field_default_value VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	field_validation VARCHAR(20) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	field_required INTEGER DEFAULT 0 NOT NULL,
	field_show_on_reg INTEGER DEFAULT 0 NOT NULL,
	field_hide INTEGER DEFAULT 0 NOT NULL,
	field_no_view INTEGER DEFAULT 0 NOT NULL,
	field_active INTEGER DEFAULT 0 NOT NULL,
	field_order INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_custom_fields ADD PRIMARY KEY (field_id);;

CREATE INDEX phpbb_garage_custom_fields_fld_type ON phpbb_garage_custom_fields(field_type);;
CREATE INDEX phpbb_garage_custom_fields_fld_ordr ON phpbb_garage_custom_fields(field_order);;

CREATE GENERATOR phpbb_garage_custom_fields_gen;;
SET GENERATOR phpbb_garage_custom_fields_gen TO 0;;

CREATE TRIGGER t_phpbb_garage_custom_fields FOR phpbb_garage_custom_fields
BEFORE INSERT
AS
BEGIN
	NEW.field_id = GEN_ID(phpbb_garage_custom_fields_gen, 1);
END;;


# Table: 'phpbb_garage_custom_fields_data'
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id INTEGER DEFAULT 0 NOT NULL
);;

ALTER TABLE phpbb_garage_custom_fields_data ADD PRIMARY KEY (user_id);;


# Table: 'phpbb_garage_custom_fields_lang'
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id INTEGER DEFAULT 0 NOT NULL,
	lang_id INTEGER DEFAULT 0 NOT NULL,
	option_id INTEGER DEFAULT 0 NOT NULL,
	field_type INTEGER DEFAULT 0 NOT NULL,
	lang_value VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE
);;

ALTER TABLE phpbb_garage_custom_fields_lang ADD PRIMARY KEY (field_id, lang_id, option_id);;


# Table: 'phpbb_garage_lang'
CREATE TABLE phpbb_garage_lang (
	field_id INTEGER DEFAULT 0 NOT NULL,
	lang_id INTEGER DEFAULT 0 NOT NULL,
	lang_name VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
	lang_explain BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
	lang_default_value VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE
);;

ALTER TABLE phpbb_garage_lang ADD PRIMARY KEY (field_id, lang_id);;


