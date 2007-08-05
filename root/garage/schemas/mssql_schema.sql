/*

 $Id: $

*/

BEGIN TRANSACTION
GO

/*
	Table: 'phpbb_garage_vehicles'
*/
CREATE TABLE [phpbb_garage_vehicles] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[made_year] [int] DEFAULT (2007) NOT NULL ,
	[engine_type] [int] DEFAULT (0) NOT NULL ,
	[colour] [varchar] (100) DEFAULT ('') NOT NULL ,
	[mileage] [int] DEFAULT (0) NOT NULL ,
	[mileage_unit] [varchar] (32) DEFAULT ('Miles') NOT NULL ,
	[price] [int] DEFAULT (0) NOT NULL ,
	[currency] [varchar] (32) DEFAULT ('EUR') NOT NULL ,
	[comments] [text] DEFAULT ('') NOT NULL ,
	[views] [int] DEFAULT (0) NOT NULL ,
	[date_created] [int] DEFAULT (0) NOT NULL ,
	[date_updated] [int] DEFAULT (0) NOT NULL ,
	[make_id] [int] DEFAULT (0) NOT NULL ,
	[model_id] [int] DEFAULT (0) NOT NULL ,
	[main_vehicle] [int] DEFAULT (0) NOT NULL ,
	[weighted_rating] [float] DEFAULT (0) NOT NULL ,
	[bbcode_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[bbcode_uid] [varchar] (5) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_vehicles] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_vehicles] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [date_created] ON [phpbb_garage_vehicles]([filetime]) ON [PRIMARY]
GO

CREATE  INDEX [date_updated] ON [phpbb_garage_vehicles]([post_msg_id]) ON [PRIMARY]
GO

CREATE  INDEX [user_id] ON [phpbb_garage_vehicles]([topic_id]) ON [PRIMARY]
GO

CREATE  INDEX [views] ON [phpbb_garage_vehicles]([poster_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_business'
*/
CREATE TABLE [phpbb_garage_business] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[title] [varchar] (100) DEFAULT ('') NOT NULL ,
	[address] [varchar] (255) DEFAULT ('') NOT NULL ,
	[telephone]  DEFAULT ('') NOT NULL ,
	[fax]  DEFAULT ('') NOT NULL ,
	[website]  DEFAULT ('') NOT NULL ,
	[email]  DEFAULT ('') NOT NULL ,
	[opening_hours] [varchar] (255) DEFAULT ('') NOT NULL ,
	[insurance] [int] DEFAULT (0) NOT NULL ,
	[garage] [int] DEFAULT (0) NOT NULL ,
	[retail] [int] DEFAULT (0) NOT NULL ,
	[product] [int] DEFAULT (0) NOT NULL ,
	[dynocentre] [int] DEFAULT (0) NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL ,
	[comments] [varchar] (4000) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_business] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_business] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [insurance] ON [phpbb_garage_business]([group_id]) ON [PRIMARY]
GO

CREATE  INDEX [garage] ON [phpbb_garage_business]([group_id]) ON [PRIMARY]
GO

CREATE  INDEX [retail] ON [phpbb_garage_business]([group_id]) ON [PRIMARY]
GO

CREATE  INDEX [product] ON [phpbb_garage_business]([group_id]) ON [PRIMARY]
GO

CREATE  INDEX [dynocentre] ON [phpbb_garage_business]([group_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_categories'
*/
CREATE TABLE [phpbb_garage_categories] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[title] [varchar] (4000) DEFAULT ('') NOT NULL ,
	[field_order] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_categories] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_categories] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [title] ON [phpbb_garage_categories]([auth_option]) ON [PRIMARY]
GO

CREATE  INDEX [id] ON [phpbb_garage_categories]([id], [title]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_config'
*/
CREATE TABLE [phpbb_garage_config] (
	[config_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[config_value] [varchar] (255) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_config] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_config] PRIMARY KEY  CLUSTERED 
	(
		[config_name]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_vehicles_gallery'
*/
CREATE TABLE [phpbb_garage_vehicles_gallery] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[hilite] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_vehicles_gallery] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_vehicles_gallery] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_vehicles_gallery]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_garage_vehicles_gallery]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_modifications_gallery'
*/
CREATE TABLE [phpbb_garage_modifications_gallery] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[modification_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[hilite] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_modifications_gallery] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_modifications_gallery] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_modifications_gallery]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_garage_modifications_gallery]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_quartermiles_gallery'
*/
CREATE TABLE [phpbb_garage_quartermiles_gallery] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[quartermile_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[hilite] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_quartermiles_gallery] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_quartermiles_gallery] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_quartermiles_gallery]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_garage_quartermiles_gallery]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_dynoruns_gallery'
*/
CREATE TABLE [phpbb_dynoruns_gallery] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[dynorun_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[hilite] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_dynoruns_gallery] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_dynoruns_gallery] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_dynoruns_gallery]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_dynoruns_gallery]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_laps_gallery'
*/
CREATE TABLE [phpbb_garage_laps_gallery] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[lap_id] [int] DEFAULT (0) NOT NULL ,
	[image_id] [int] DEFAULT (0) NOT NULL ,
	[hilite] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_laps_gallery] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_laps_gallery] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_laps_gallery]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [image_id] ON [phpbb_garage_laps_gallery]([image_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_guestbooks'
*/
CREATE TABLE [phpbb_garage_guestbooks] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[author_id] [int] DEFAULT (0) NOT NULL ,
	[post_date] [int] DEFAULT (0) NOT NULL ,
	[ip_address] [varchar] (40) DEFAULT ('') NOT NULL ,
	[bbcode_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[bbcode_uid] [varchar] (5) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL ,
	[post] [text] DEFAULT ('') NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_guestbooks] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_guestbooks] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_guestbooks]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [author_id] ON [phpbb_garage_guestbooks]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [post_date] ON [phpbb_garage_guestbooks]([vehicle_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_images'
*/
CREATE TABLE [phpbb_garage_images] (
	[attach_id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[attach_location] [varchar] (255) DEFAULT ('') NOT NULL ,
	[attach_hits] [int] DEFAULT (0) NOT NULL ,
	[attach_ext] [varchar] (100) DEFAULT ('') NOT NULL ,
	[attach_file] [varchar] (255) DEFAULT ('') NOT NULL ,
	[attach_thumb_location] [varchar] (255) DEFAULT ('') NOT NULL ,
	[attach_thumb_width] [int] DEFAULT (0) NOT NULL ,
	[attach_thumb_height] [int] DEFAULT (0) NOT NULL ,
	[attach_is_image] [int] DEFAULT (0) NOT NULL ,
	[attach_date] [int] DEFAULT (0) NOT NULL ,
	[attach_filesize] [int] DEFAULT (0) NOT NULL ,
	[attach_thumb_filesize] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_images] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_images] PRIMARY KEY  CLUSTERED 
	(
		[attach_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_premiums'
*/
CREATE TABLE [phpbb_garage_premiums] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[business_id] [int] DEFAULT (0) NOT NULL ,
	[cover_type_id] [int] DEFAULT (0) NOT NULL ,
	[premium] [int] DEFAULT (0) NOT NULL ,
	[comments] [text] NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_premiums] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_premiums] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_makes'
*/
CREATE TABLE [phpbb_garage_makes] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[make] [varchar] (255) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_makes] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_makes] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [make] ON [phpbb_garage_makes]([make]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_models'
*/
CREATE TABLE [phpbb_garage_models] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[make_id] [int] DEFAULT (0) NOT NULL ,
	[model] [varchar] (255) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_models] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_models] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [make_id] ON [phpbb_garage_models]([make_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_modifications'
*/
CREATE TABLE [phpbb_garage_modifications] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[category_id] [int] DEFAULT (0) NOT NULL ,
	[manufacturer_id] [int] DEFAULT (0) NOT NULL ,
	[product_id] [int] DEFAULT (0) NOT NULL ,
	[price]  NOT NULL ,
	[install_price]  NOT NULL ,
	[product_rating]  NOT NULL ,
	[purchase_rating]  NOT NULL ,
	[install_rating]  NOT NULL ,
	[shop_id] [int] DEFAULT (0) NOT NULL ,
	[installer_id] [int] DEFAULT (0) NOT NULL ,
	[comments] [text] DEFAULT ('') NOT NULL ,
	[install_comments] [text] DEFAULT ('') NOT NULL ,
	[date_created] [int] DEFAULT (0) NOT NULL ,
	[date_updated] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_modifications] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_modifications] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [user_id] ON [phpbb_garage_modifications]([make_id]) ON [PRIMARY]
GO

CREATE  INDEX [vehicle_id_2] ON [phpbb_garage_modifications]([vehicle_id], [category_id]) ON [PRIMARY]
GO

CREATE  INDEX [category_id] ON [phpbb_garage_modifications]([make_id]) ON [PRIMARY]
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_modifications]([make_id]) ON [PRIMARY]
GO

CREATE  INDEX [date_created] ON [phpbb_garage_modifications]([make_id]) ON [PRIMARY]
GO

CREATE  INDEX [date_updated] ON [phpbb_garage_modifications]([make_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_products'
*/
CREATE TABLE [phpbb_garage_products] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[business_id] [int] DEFAULT (0) NOT NULL ,
	[category_id] [int] DEFAULT (0) NOT NULL ,
	[title] [varchar] (255) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_products] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_products] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [business_id] ON [phpbb_garage_products]([business_id]) ON [PRIMARY]
GO

CREATE  INDEX [category_id] ON [phpbb_garage_products]([category_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_quartermiles'
*/
CREATE TABLE [phpbb_garage_quartermiles] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[rt] [float] DEFAULT (0) NOT NULL ,
	[sixty] [float] DEFAULT (0) NOT NULL ,
	[three] [float] DEFAULT (0) NOT NULL ,
	[eighth] [float] DEFAULT (0) NOT NULL ,
	[eighthmph] [float] DEFAULT (0) NOT NULL ,
	[thou] [float] DEFAULT (0) NOT NULL ,
	[quart] [float] DEFAULT (0) NOT NULL ,
	[quartmph] [float] DEFAULT (0) NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL ,
	[dynorun_id] [int] DEFAULT (0) NOT NULL ,
	[date_created] [int] DEFAULT (0) NOT NULL ,
	[date_updated] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_quartermiles] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_quartermiles] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_dynoruns'
*/
CREATE TABLE [phpbb_garage_dynoruns] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[dynocentre_id] [int] DEFAULT (0) NOT NULL ,
	[bhp] [float] DEFAULT (0) NOT NULL ,
	[bhp_unit] [varchar] (32) DEFAULT ('') NOT NULL ,
	[torque] [float] DEFAULT (0) NOT NULL ,
	[torque_unit] [varchar] (32) DEFAULT ('') NOT NULL ,
	[boost] [float] DEFAULT (0) NOT NULL ,
	[boost_unit] [varchar] (32) DEFAULT ('') NOT NULL ,
	[nitrous] [int] DEFAULT (0) NOT NULL ,
	[peakpoint] [float] DEFAULT (0) NOT NULL ,
	[date_created] [int] DEFAULT (0) NOT NULL ,
	[date_updated] [int] DEFAULT (0) NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_dynoruns] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_dynoruns] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_ratings'
*/
CREATE TABLE [phpbb_garage_ratings] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[rating] [int] DEFAULT (0) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[rate_date] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_ratings] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_ratings] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_tracks'
*/
CREATE TABLE [phpbb_garage_tracks] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[title] [varchar] (255) DEFAULT ('') NOT NULL ,
	[length] [varchar] (32) DEFAULT ('') NOT NULL ,
	[mileage_unit] [varchar] (32) DEFAULT ('') NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_tracks] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_tracks] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_laps'
*/
CREATE TABLE [phpbb_garage_laps] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[track_id] [int] DEFAULT (0) NOT NULL ,
	[condition_id] [int] DEFAULT (0) NOT NULL ,
	[type_id] [int] DEFAULT (0) NOT NULL ,
	[minute] [int] DEFAULT (0) NOT NULL ,
	[second] [int] DEFAULT (0) NOT NULL ,
	[millisecond] [int] DEFAULT (0) NOT NULL ,
	[pending] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_laps] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_laps] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_laps]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [track_id] ON [phpbb_garage_laps]([track_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_service_history'
*/
CREATE TABLE [phpbb_garage_service_history] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[garage_id] [int] DEFAULT (0) NOT NULL ,
	[type_id] [int] DEFAULT (0) NOT NULL ,
	[price] [int] DEFAULT (0) NOT NULL ,
	[rating] [int] DEFAULT (0) NOT NULL ,
	[mileage] [int] DEFAULT (0) NOT NULL ,
	[date_created] [int] DEFAULT (0) NOT NULL ,
	[date_updated] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_service_history] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_service_history] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_service_history]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [garage_id] ON [phpbb_garage_service_history]([garage_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_blog'
*/
CREATE TABLE [phpbb_garage_blog] (
	[id] [int] IDENTITY (1, 1) NOT NULL ,
	[vehicle_id] [int] DEFAULT (0) NOT NULL ,
	[user_id] [int] DEFAULT (0) NOT NULL ,
	[blog_title] [varchar] (100) DEFAULT ('') NOT NULL ,
	[blog_text] [text] DEFAULT ('') NOT NULL ,
	[blog_date] [int] DEFAULT (0) NOT NULL ,
	[bbcode_bitfield] [varchar] (255) DEFAULT ('') NOT NULL ,
	[bbcode_uid] [varchar] (5) DEFAULT ('') NOT NULL 
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_blog] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_blog] PRIMARY KEY  CLUSTERED 
	(
		[id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [vehicle_id] ON [phpbb_garage_blog]([vehicle_id]) ON [PRIMARY]
GO

CREATE  INDEX [user_id] ON [phpbb_garage_blog]([user_id]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_custom_fields'
*/
CREATE TABLE [phpbb_garage_custom_fields] (
	[field_id] [int] IDENTITY (1, 1) NOT NULL ,
	[field_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[field_type] [int] DEFAULT (0) NOT NULL ,
	[field_ident] [varchar] (20) DEFAULT ('') NOT NULL ,
	[field_length] [varchar] (20) DEFAULT ('') NOT NULL ,
	[field_minlen] [varchar] (255) DEFAULT ('') NOT NULL ,
	[field_maxlen] [varchar] (255) DEFAULT ('') NOT NULL ,
	[field_novalue] [varchar] (255) DEFAULT ('') NOT NULL ,
	[field_default_value] [varchar] (255) DEFAULT ('') NOT NULL ,
	[field_validation] [varchar] (20) DEFAULT ('') NOT NULL ,
	[field_required] [int] DEFAULT (0) NOT NULL ,
	[field_show_on_reg] [int] DEFAULT (0) NOT NULL ,
	[field_hide] [int] DEFAULT (0) NOT NULL ,
	[field_no_view] [int] DEFAULT (0) NOT NULL ,
	[field_active] [int] DEFAULT (0) NOT NULL ,
	[field_order] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_custom_fields] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_custom_fields] PRIMARY KEY  CLUSTERED 
	(
		[field_id]
	)  ON [PRIMARY] 
GO

CREATE  INDEX [fld_type] ON [phpbb_garage_custom_fields]([field_type]) ON [PRIMARY]
GO

CREATE  INDEX [fld_ordr] ON [phpbb_garage_custom_fields]([field_order]) ON [PRIMARY]
GO


/*
	Table: 'phpbb_garage_custom_fields_data'
*/
CREATE TABLE [phpbb_garage_custom_fields_data] (
	[user_id] [int] DEFAULT (0) NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_custom_fields_data] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_custom_fields_data] PRIMARY KEY  CLUSTERED 
	(
		[user_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_custom_fields_lang'
*/
CREATE TABLE [phpbb_garage_custom_fields_lang] (
	[field_id] [int] DEFAULT (0) NOT NULL ,
	[lang_id] [int] DEFAULT (0) NOT NULL ,
	[option_id] [int] DEFAULT (0) NOT NULL ,
	[field_type] [int] DEFAULT (0) NOT NULL ,
	[lang_value] [varchar] (255) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_custom_fields_lang] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_custom_fields_lang] PRIMARY KEY  CLUSTERED 
	(
		[field_id],
		[lang_id],
		[option_id]
	)  ON [PRIMARY] 
GO


/*
	Table: 'phpbb_garage_lang'
*/
CREATE TABLE [phpbb_garage_lang] (
	[field_id] [int] DEFAULT (0) NOT NULL ,
	[lang_id] [int] DEFAULT (0) NOT NULL ,
	[lang_name] [varchar] (255) DEFAULT ('') NOT NULL ,
	[lang_explain] [varchar] (4000) DEFAULT ('') NOT NULL ,
	[lang_default_value] [varchar] (255) DEFAULT ('') NOT NULL 
) ON [PRIMARY]
GO

ALTER TABLE [phpbb_garage_lang] WITH NOCHECK ADD 
	CONSTRAINT [PK_phpbb_garage_lang] PRIMARY KEY  CLUSTERED 
	(
		[field_id],
		[lang_id]
	)  ON [PRIMARY] 
GO



COMMIT
GO

