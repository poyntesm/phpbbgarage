/*

 $Id$

*/

/*
	Table: 'phpbb_garage_vehicles'
*/
CREATE TABLE phpbb_garage_vehicles (
	id number(8) NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	made_year number(8) DEFAULT '2007' NOT NULL,
	engine_type number(2) DEFAULT '0' NOT NULL,
	colour varchar2(300) DEFAULT '' ,
	mileage number(8) DEFAULT '0' NOT NULL,
	mileage_unit varchar2(32) DEFAULT 'Miles' NOT NULL,
	price number(8) DEFAULT '0' NOT NULL,
	currency varchar2(32) DEFAULT 'EUR' NOT NULL,
	comments clob DEFAULT '' ,
	bbcode_bitfield varchar2(255) DEFAULT '' ,
	bbcode_uid varchar2(8) DEFAULT '' ,
	bbcode_options number(8) DEFAULT '7' NOT NULL,
	views number(8) DEFAULT '0' NOT NULL,
	date_created number(11) DEFAULT '0' NOT NULL,
	date_updated number(11) DEFAULT '0' NOT NULL,
	make_id number(8) DEFAULT '0' NOT NULL,
	model_id number(8) DEFAULT '0' NOT NULL,
	main_vehicle number(1) DEFAULT '0' NOT NULL,
	weighted_rating number(4, 2) DEFAULT '0' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_vehicles PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_vehicles_date_created ON phpbb_garage_vehicles (date_created)
/
CREATE INDEX phpbb_garage_vehicles_date_updated ON phpbb_garage_vehicles (date_updated)
/
CREATE INDEX phpbb_garage_vehicles_user_id ON phpbb_garage_vehicles (user_id)
/
CREATE INDEX phpbb_garage_vehicles_views ON phpbb_garage_vehicles (views)
/

CREATE SEQUENCE phpbb_garage_vehicles_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_vehicles
BEFORE INSERT ON phpbb_garage_vehicles
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_vehicles_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_business'
*/
CREATE TABLE phpbb_garage_business (
	id number(8) NOT NULL,
	title varchar2(300) DEFAULT '' ,
	address varchar2(255) DEFAULT '' ,
	telephone varchar2(100) DEFAULT '' ,
	fax varchar2(100) DEFAULT '' ,
	website varchar2(255) DEFAULT '' ,
	email varchar2(100) DEFAULT '' ,
	opening_hours varchar2(255) DEFAULT '' ,
	insurance number(1) DEFAULT '0' NOT NULL,
	garage number(1) DEFAULT '0' NOT NULL,
	retail number(1) DEFAULT '0' NOT NULL,
	product number(1) DEFAULT '0' NOT NULL,
	dynocentre number(1) DEFAULT '0' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_business PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_business_insurance ON phpbb_garage_business (insurance)
/
CREATE INDEX phpbb_garage_business_garage ON phpbb_garage_business (garage)
/
CREATE INDEX phpbb_garage_business_retail ON phpbb_garage_business (retail)
/
CREATE INDEX phpbb_garage_business_product ON phpbb_garage_business (product)
/
CREATE INDEX phpbb_garage_business_dynocentre ON phpbb_garage_business (dynocentre)
/

CREATE SEQUENCE phpbb_garage_business_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_business
BEFORE INSERT ON phpbb_garage_business
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_business_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_categories'
*/
CREATE TABLE phpbb_garage_categories (
	id number(8) NOT NULL,
	title clob DEFAULT '' ,
	field_order number(4) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_categories PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_categories_title ON phpbb_garage_categories (title)
/
CREATE INDEX phpbb_garage_categories_id ON phpbb_garage_categories (id, title)
/

CREATE SEQUENCE phpbb_garage_categories_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_categories
BEFORE INSERT ON phpbb_garage_categories
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_categories_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_config'
*/
CREATE TABLE phpbb_garage_config (
	config_name varchar2(255) DEFAULT '' ,
	config_value varchar2(765) DEFAULT '' ,
	CONSTRAINT pk_phpbb_garage_config PRIMARY KEY (config_name)
)
/


/*
	Table: 'phpbb_garage_vehicles_gallery'
*/
CREATE TABLE phpbb_garage_vehicles_gallery (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	hilite number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_vehicles_gallery PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_vehicles_gallery_vehicle_id ON phpbb_garage_vehicles_gallery (vehicle_id)
/
CREATE INDEX phpbb_garage_vehicles_gallery_image_id ON phpbb_garage_vehicles_gallery (image_id)
/

CREATE SEQUENCE phpbb_garage_vehicles_gallery_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_vehicles_gallery
BEFORE INSERT ON phpbb_garage_vehicles_gallery
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_vehicles_gallery_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_modifications_gallery'
*/
CREATE TABLE phpbb_garage_modifications_gallery (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	modification_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	hilite number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_modifications_gallery PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_modifications_gallery_vehicle_id ON phpbb_garage_modifications_gallery (vehicle_id)
/
CREATE INDEX phpbb_garage_modifications_gallery_image_id ON phpbb_garage_modifications_gallery (image_id)
/

CREATE SEQUENCE phpbb_garage_modifications_gallery_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_modifications_gallery
BEFORE INSERT ON phpbb_garage_modifications_gallery
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_modifications_gallery_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_quartermiles_gallery'
*/
CREATE TABLE phpbb_garage_quartermiles_gallery (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	quartermile_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	hilite number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_quartermiles_gallery PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_quartermiles_gallery_vehicle_id ON phpbb_garage_quartermiles_gallery (vehicle_id)
/
CREATE INDEX phpbb_garage_quartermiles_gallery_image_id ON phpbb_garage_quartermiles_gallery (image_id)
/

CREATE SEQUENCE phpbb_garage_quartermiles_gallery_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_quartermiles_gallery
BEFORE INSERT ON phpbb_garage_quartermiles_gallery
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_quartermiles_gallery_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_dynoruns_gallery'
*/
CREATE TABLE phpbb_garage_dynoruns_gallery (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	dynorun_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	hilite number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_dynoruns_gallery PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_dynoruns_gallery_vehicle_id ON phpbb_garage_dynoruns_gallery (vehicle_id)
/
CREATE INDEX phpbb_garage_dynoruns_gallery_image_id ON phpbb_garage_dynoruns_gallery (image_id)
/

CREATE SEQUENCE phpbb_garage_dynoruns_gallery_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_dynoruns_gallery
BEFORE INSERT ON phpbb_garage_dynoruns_gallery
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_dynoruns_gallery_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_laps_gallery'
*/
CREATE TABLE phpbb_garage_laps_gallery (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	lap_id number(8) DEFAULT '0' NOT NULL,
	image_id number(8) DEFAULT '0' NOT NULL,
	hilite number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_laps_gallery PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_laps_gallery_vehicle_id ON phpbb_garage_laps_gallery (vehicle_id)
/
CREATE INDEX phpbb_garage_laps_gallery_image_id ON phpbb_garage_laps_gallery (image_id)
/

CREATE SEQUENCE phpbb_garage_laps_gallery_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_laps_gallery
BEFORE INSERT ON phpbb_garage_laps_gallery
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_laps_gallery_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_guestbooks'
*/
CREATE TABLE phpbb_garage_guestbooks (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	author_id number(8) DEFAULT '0' NOT NULL,
	post_date number(11) DEFAULT '0' NOT NULL,
	ip_address varchar2(40) DEFAULT '' ,
	bbcode_bitfield varchar2(255) DEFAULT '' ,
	bbcode_uid varchar2(8) DEFAULT '' ,
	bbcode_options number(8) DEFAULT '7' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	post clob DEFAULT '' ,
	CONSTRAINT pk_phpbb_garage_guestbooks PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_guestbooks_vehicle_id ON phpbb_garage_guestbooks (vehicle_id)
/
CREATE INDEX phpbb_garage_guestbooks_author_id ON phpbb_garage_guestbooks (author_id)
/
CREATE INDEX phpbb_garage_guestbooks_post_date ON phpbb_garage_guestbooks (post_date)
/

CREATE SEQUENCE phpbb_garage_guestbooks_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_guestbooks
BEFORE INSERT ON phpbb_garage_guestbooks
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_guestbooks_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_images'
*/
CREATE TABLE phpbb_garage_images (
	attach_id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	attach_location varchar2(255) DEFAULT '' ,
	attach_hits number(8) DEFAULT '0' NOT NULL,
	attach_ext varchar2(100) DEFAULT '' ,
	attach_file varchar2(255) DEFAULT '' ,
	attach_thumb_location varchar2(255) DEFAULT '' ,
	attach_thumb_width number(4) DEFAULT '0' NOT NULL,
	attach_thumb_height number(4) DEFAULT '0' NOT NULL,
	attach_is_image number(1) DEFAULT '0' NOT NULL,
	attach_date number(11) DEFAULT '0' NOT NULL,
	attach_filesize number(20) DEFAULT '0' NOT NULL,
	attach_thumb_filesize number(20) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_images PRIMARY KEY (attach_id)
)
/


CREATE SEQUENCE phpbb_garage_images_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_images
BEFORE INSERT ON phpbb_garage_images
FOR EACH ROW WHEN (
	new.attach_id IS NULL OR new.attach_id = 0
)
BEGIN
	SELECT phpbb_garage_images_seq.nextval
	INTO :new.attach_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_premiums'
*/
CREATE TABLE phpbb_garage_premiums (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	business_id number(8) DEFAULT '0' NOT NULL,
	cover_type_id number(8) DEFAULT '0' NOT NULL,
	premium number(8) DEFAULT '0' NOT NULL,
	comments clob NOT NULL,
	CONSTRAINT pk_phpbb_garage_premiums PRIMARY KEY (id)
)
/


CREATE SEQUENCE phpbb_garage_premiums_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_premiums
BEFORE INSERT ON phpbb_garage_premiums
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_premiums_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_makes'
*/
CREATE TABLE phpbb_garage_makes (
	id number(8) NOT NULL,
	make varchar2(255) DEFAULT '' ,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_makes PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_makes_make ON phpbb_garage_makes (make)
/

CREATE SEQUENCE phpbb_garage_makes_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_makes
BEFORE INSERT ON phpbb_garage_makes
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_makes_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_models'
*/
CREATE TABLE phpbb_garage_models (
	id number(8) NOT NULL,
	make_id number(8) DEFAULT '0' NOT NULL,
	model varchar2(255) DEFAULT '' ,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_models PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_models_make_id ON phpbb_garage_models (make_id)
/

CREATE SEQUENCE phpbb_garage_models_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_models
BEFORE INSERT ON phpbb_garage_models
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_models_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_modifications'
*/
CREATE TABLE phpbb_garage_modifications (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	category_id number(8) DEFAULT '0' NOT NULL,
	manufacturer_id number(8) DEFAULT '0' NOT NULL,
	product_id number(8) DEFAULT '0' NOT NULL,
	price number(8) DEFAULT '0' NOT NULL,
	install_price number(8) DEFAULT '0' NOT NULL,
	product_rating number(2) DEFAULT '0' NOT NULL,
	purchase_rating number(2) DEFAULT '0' NOT NULL,
	install_rating number(2) DEFAULT '0' NOT NULL,
	shop_id number(8) DEFAULT '0' NOT NULL,
	installer_id number(8) DEFAULT '0' NOT NULL,
	comments clob DEFAULT '' ,
	bbcode_bitfield varchar2(255) DEFAULT '' ,
	bbcode_uid varchar2(8) DEFAULT '' ,
	bbcode_options number(8) DEFAULT '7' NOT NULL,
	install_comments clob DEFAULT '' ,
	date_created number(11) DEFAULT '0' NOT NULL,
	date_updated number(11) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_modifications PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_modifications_user_id ON phpbb_garage_modifications (user_id)
/
CREATE INDEX phpbb_garage_modifications_vehicle_id_2 ON phpbb_garage_modifications (vehicle_id, category_id)
/
CREATE INDEX phpbb_garage_modifications_category_id ON phpbb_garage_modifications (category_id)
/
CREATE INDEX phpbb_garage_modifications_vehicle_id ON phpbb_garage_modifications (vehicle_id)
/
CREATE INDEX phpbb_garage_modifications_date_created ON phpbb_garage_modifications (date_created)
/
CREATE INDEX phpbb_garage_modifications_date_updated ON phpbb_garage_modifications (date_updated)
/

CREATE SEQUENCE phpbb_garage_modifications_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_modifications
BEFORE INSERT ON phpbb_garage_modifications
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_modifications_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_products'
*/
CREATE TABLE phpbb_garage_products (
	id number(8) NOT NULL,
	business_id number(8) DEFAULT '0' NOT NULL,
	category_id number(8) DEFAULT '0' NOT NULL,
	title varchar2(255) DEFAULT '' ,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_products PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_products_business_id ON phpbb_garage_products (business_id)
/
CREATE INDEX phpbb_garage_products_category_id ON phpbb_garage_products (category_id)
/

CREATE SEQUENCE phpbb_garage_products_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_products
BEFORE INSERT ON phpbb_garage_products
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_products_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_quartermiles'
*/
CREATE TABLE phpbb_garage_quartermiles (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	rt number(6, 3) DEFAULT '0' NOT NULL,
	sixty number(6, 3) DEFAULT '0' NOT NULL,
	three number(6, 3) DEFAULT '0' NOT NULL,
	eighth number(6, 3) DEFAULT '0' NOT NULL,
	eighthmph number(6, 3) DEFAULT '0' NOT NULL,
	thou number(6, 3) DEFAULT '0' NOT NULL,
	quart number(6, 3) DEFAULT '0' NOT NULL,
	quartmph number(6, 3) DEFAULT '0' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	dynorun_id number(8) DEFAULT '0' NOT NULL,
	date_created number(11) DEFAULT '0' NOT NULL,
	date_updated number(11) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_quartermiles PRIMARY KEY (id)
)
/


CREATE SEQUENCE phpbb_garage_quartermiles_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_quartermiles
BEFORE INSERT ON phpbb_garage_quartermiles
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_quartermiles_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_dynoruns'
*/
CREATE TABLE phpbb_garage_dynoruns (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	dynocentre_id number(8) DEFAULT '0' NOT NULL,
	bhp number(6, 2) DEFAULT '0' NOT NULL,
	bhp_unit varchar2(32) DEFAULT '' ,
	torque number(6, 2) DEFAULT '0' NOT NULL,
	torque_unit varchar2(32) DEFAULT '' ,
	boost number(6, 2) DEFAULT '0' NOT NULL,
	boost_unit varchar2(32) DEFAULT '' ,
	nitrous number(8) DEFAULT '0' NOT NULL,
	peakpoint number(7, 3) DEFAULT '0' NOT NULL,
	date_created number(11) DEFAULT '0' NOT NULL,
	date_updated number(11) DEFAULT '0' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_dynoruns PRIMARY KEY (id)
)
/


CREATE SEQUENCE phpbb_garage_dynoruns_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_dynoruns
BEFORE INSERT ON phpbb_garage_dynoruns
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_dynoruns_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_ratings'
*/
CREATE TABLE phpbb_garage_ratings (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	rating number(2) DEFAULT '0' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	rate_date number(11) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_ratings PRIMARY KEY (id)
)
/


CREATE SEQUENCE phpbb_garage_ratings_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_ratings
BEFORE INSERT ON phpbb_garage_ratings
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_ratings_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_tracks'
*/
CREATE TABLE phpbb_garage_tracks (
	id number(8) NOT NULL,
	title varchar2(255) DEFAULT '' ,
	length varchar2(32) DEFAULT '' ,
	mileage_unit varchar2(32) DEFAULT '' ,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_tracks PRIMARY KEY (id)
)
/


CREATE SEQUENCE phpbb_garage_tracks_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_tracks
BEFORE INSERT ON phpbb_garage_tracks
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_tracks_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_laps'
*/
CREATE TABLE phpbb_garage_laps (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	track_id number(8) DEFAULT '0' NOT NULL,
	condition_id number(8) DEFAULT '0' NOT NULL,
	type_id number(8) DEFAULT '0' NOT NULL,
	minute number(2) DEFAULT '0' NOT NULL,
	second number(2) DEFAULT '0' NOT NULL,
	millisecond number(2) DEFAULT '0' NOT NULL,
	pending number(1) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_laps PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_laps_vehicle_id ON phpbb_garage_laps (vehicle_id)
/
CREATE INDEX phpbb_garage_laps_track_id ON phpbb_garage_laps (track_id)
/

CREATE SEQUENCE phpbb_garage_laps_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_laps
BEFORE INSERT ON phpbb_garage_laps
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_laps_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_service_history'
*/
CREATE TABLE phpbb_garage_service_history (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	garage_id number(8) DEFAULT '0' NOT NULL,
	type_id number(8) DEFAULT '0' NOT NULL,
	price number(8) DEFAULT '0' NOT NULL,
	rating number(2) DEFAULT '0' NOT NULL,
	mileage number(8) DEFAULT '0' NOT NULL,
	date_created number(11) DEFAULT '0' NOT NULL,
	date_updated number(11) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_service_history PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_service_history_vehicle_id ON phpbb_garage_service_history (vehicle_id)
/
CREATE INDEX phpbb_garage_service_history_garage_id ON phpbb_garage_service_history (garage_id)
/

CREATE SEQUENCE phpbb_garage_service_history_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_service_history
BEFORE INSERT ON phpbb_garage_service_history
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_service_history_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_blog'
*/
CREATE TABLE phpbb_garage_blog (
	id number(8) NOT NULL,
	vehicle_id number(8) DEFAULT '0' NOT NULL,
	user_id number(8) DEFAULT '0' NOT NULL,
	blog_title varchar2(300) DEFAULT '' ,
	blog_text clob DEFAULT '' ,
	blog_date number(11) DEFAULT '0' NOT NULL,
	bbcode_bitfield varchar2(255) DEFAULT '' ,
	bbcode_uid varchar2(8) DEFAULT '' ,
	bbcode_options number(8) DEFAULT '7' NOT NULL,
	CONSTRAINT pk_phpbb_garage_blog PRIMARY KEY (id)
)
/

CREATE INDEX phpbb_garage_blog_vehicle_id ON phpbb_garage_blog (vehicle_id)
/
CREATE INDEX phpbb_garage_blog_user_id ON phpbb_garage_blog (user_id)
/

CREATE SEQUENCE phpbb_garage_blog_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_blog
BEFORE INSERT ON phpbb_garage_blog
FOR EACH ROW WHEN (
	new.id IS NULL OR new.id = 0
)
BEGIN
	SELECT phpbb_garage_blog_seq.nextval
	INTO :new.id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_custom_fields'
*/
CREATE TABLE phpbb_garage_custom_fields (
	field_id number(8) NOT NULL,
	field_name varchar2(765) DEFAULT '' ,
	field_type number(4) DEFAULT '0' NOT NULL,
	field_ident varchar2(20) DEFAULT '' ,
	field_length varchar2(20) DEFAULT '' ,
	field_minlen varchar2(255) DEFAULT '' ,
	field_maxlen varchar2(255) DEFAULT '' ,
	field_novalue varchar2(765) DEFAULT '' ,
	field_default_value varchar2(765) DEFAULT '' ,
	field_validation varchar2(60) DEFAULT '' ,
	field_required number(1) DEFAULT '0' NOT NULL,
	field_show_on_reg number(1) DEFAULT '0' NOT NULL,
	field_hide number(1) DEFAULT '0' NOT NULL,
	field_no_view number(1) DEFAULT '0' NOT NULL,
	field_active number(1) DEFAULT '0' NOT NULL,
	field_order number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_custom_fields PRIMARY KEY (field_id)
)
/

CREATE INDEX phpbb_garage_custom_fields_fld_type ON phpbb_garage_custom_fields (field_type)
/
CREATE INDEX phpbb_garage_custom_fields_fld_ordr ON phpbb_garage_custom_fields (field_order)
/

CREATE SEQUENCE phpbb_garage_custom_fields_seq
/

CREATE OR REPLACE TRIGGER t_phpbb_garage_custom_fields
BEFORE INSERT ON phpbb_garage_custom_fields
FOR EACH ROW WHEN (
	new.field_id IS NULL OR new.field_id = 0
)
BEGIN
	SELECT phpbb_garage_custom_fields_seq.nextval
	INTO :new.field_id
	FROM dual;
END;
/


/*
	Table: 'phpbb_garage_custom_fields_data'
*/
CREATE TABLE phpbb_garage_custom_fields_data (
	user_id number(8) DEFAULT '0' NOT NULL,
	CONSTRAINT pk_phpbb_garage_custom_fields_data PRIMARY KEY (user_id)
)
/


/*
	Table: 'phpbb_garage_custom_fields_lang'
*/
CREATE TABLE phpbb_garage_custom_fields_lang (
	field_id number(8) DEFAULT '0' NOT NULL,
	lang_id number(8) DEFAULT '0' NOT NULL,
	option_id number(8) DEFAULT '0' NOT NULL,
	field_type number(4) DEFAULT '0' NOT NULL,
	lang_value varchar2(765) DEFAULT '' ,
	CONSTRAINT pk_phpbb_garage_custom_fields_lang PRIMARY KEY (field_id, lang_id, option_id)
)
/


/*
	Table: 'phpbb_garage_lang'
*/
CREATE TABLE phpbb_garage_lang (
	field_id number(8) DEFAULT '0' NOT NULL,
	lang_id number(8) DEFAULT '0' NOT NULL,
	lang_name varchar2(765) DEFAULT '' ,
	lang_explain clob DEFAULT '' ,
	lang_default_value varchar2(765) DEFAULT '' ,
	CONSTRAINT pk_phpbb_garage_lang PRIMARY KEY (field_id, lang_id)
)
/


