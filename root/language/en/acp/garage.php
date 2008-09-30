<?php
/** 
*
* acp_garage [English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_GARAGE_SETTINGS'				=> 'General Settings',
	'ACP_GARAGE_SETTINGS_EXPLAIN'			=> 'phpBB Garage Settings',
	'ACP_GARAGE_GENERAL_CONFIG' 			=> 'General Configuration',
	'ACP_GARAGE_MENU_CONFIG' 			=> 'Menu Configuration',
	'ACP_GARAGE_INDEX_CONFIG' 			=> 'Index Page Configuration',
	'ACP_GARAGE_IMAGE_CONFIG' 			=> 'Image Configuration',
	'ACP_GARAGE_QUARTERMILE_CONFIG' 		=> 'Quartermile Configuration',
	'ACP_GARAGE_DYNORUN_CONFIG' 			=> 'Dynorun Configuration',
	'ACP_GARAGE_TRACK_CONFIG'			=> 'Track Time Config',
	'ACP_GARAGE_INSURANCE_CONFIG' 			=> 'Insurance Configuration',
	'ACP_GARAGE_BUSINESS_CONFIG' 			=> 'Business Configuration',
	'ACP_GARAGE_VEHICLE_RATING_CONFIG'		=> 'Vehicle Rating Configuration',
	'ACP_GARAGE_GUESTBOOK_CONFIG'			=> 'Guestbook Configuration',
	'ACP_GARAGE_PRODUCT_CONFIG'			=> 'Product Configuration',
	'ENABLE_QUARTERMILE' 				=> 'Enable Quartermile',
	'ENABLE_QUARTERMILE_EXPLAIN' 			=> 'This will allow users to add quartermile data to vehicles',
	'ENABLE_QUARTERMILE_APPROVAL' 			=> 'Quartermile Approval',
	'ENABLE_QUARTERMILE_APPROVAL_EXPLAIN' 		=> 'This will require quartermile times to be approved before being listed on garage table.',
	'ENABLE_QUARTERMILE_IMAGE_REQUIRED' 		=> 'Quartermile Image Required',
	'ENABLE_QUARTERMILE_IMAGE_REQUIRED_EXPLAIN' 	=> 'This require all times above a limit to require a image.',
	'QUARTERMILE_IMAGE_REQUIRED_LIMIT' 		=> 'Quartermile Image Required Limit',
	'QUARTERMILE_IMAGE_REQUIRED_LIMIT_EXPLAIN' 	=> 'This is the 1/4 mile time below which a image is required for submission to the garage.',
	'ENABLE_DYNORUN' 				=> 'Enable Dynorun',
	'ENABLE_DYNORUN_EXPLAIN' 			=> 'This will allow users to add dynorun data to vehicles',
	'ENABLE_DYNORUN_APPROVAL' 			=> 'Dynorun Approval',
	'ENABLE_DYNORUN_APPROVAL_EXPLAIN' 		=> 'This will require dynorun\'s to be approved before being listed on garage table.',
	'ENABLE_DYNORUN_IMAGE_REQUIRED' 		=> 'Dynorun Image Required',
	'ENABLE_DYNORUN_IMAGE_REQUIRED_EXPLAIN' 	=> 'This require all runs above a limit to require a image.',
	'DYNORUN_IMAGE_REQUIRED_LIMIT' 			=> 'Dynorun Image Required Limit',
	'DYNORUN_IMAGE_REQUIRED_LIMIT_EXPLAIN' 		=> 'This is the BHP value above which a image is required for submission to the garage.',
	'ENABLE_INSURANCE' 				=> 'Enable Insurance',
	'ENABLE_INSURANCE_EXPLAIN' 			=> 'This will allow users to add insurance data to vehicles',
	'ENABLE_INSURANCE_SEARCH' 			=> 'Enable Insurance Search',
	'ENABLE_INSURANCE_SEARCH_EXPLAIN' 		=> 'This will enable searching for insurance data.',
	'BUSINESS_APPROVAL' 				=> 'Business\'s Need Approval',
	'BUSINESS_APPROVAL_EXPLAIN' 			=> 'This will make all business\'s need approval from a moderator or administrator before appearing.',
	'RATING_PERMANENT' 				=> 'Ratings Permanent',
	'RATING_PERMANENT_EXPLAIN' 			=> 'Allows you to set the inital rating as a permanent unchangable value.',
	'RATING_ALWAYS_UPDATEABLE' 			=> 'Rating Always Updateable',
	'RATING_ALWAYS_UPDATEABLE_EXPLAIN' 		=> 'If ratings not permanent, allows you to set if a rating can be changed anytime, or only if vehicle has been updated since last rating.',
	'RATING_MINIMUM_REQUIRED' 			=> 'Minimum Ratings Required',
	'RATING_MINIMUM_REQUIRED_EXPLAIN' 		=> 'Minimum number of ratings needed.',
	'ENABLE_IMAGES' 				=> 'Enable Images',
	'ENABLE_IMAGES_EXPLAIN' 			=> 'Enable images to be used for buttons within the garage.',
	'ENABLE_VEHICLE_IMAGES' 			=> 'Enable Vehicle Images',
	'ENABLE_VEHICLE_IMAGES_EXPLAIN' 		=> 'This will allow users with correct permissions to add images to vehicles.',
	'ENABLE_MODIFICATION_IMAGES' 			=> 'Enable Modifcation Images',
	'ENABLE_MODIFICATION_IMAGES_EXPLAIN' 		=> 'This will allow users with correct permissions to add images to modifications.',
	'ENABLE_QUARTERMILE_IMAGES' 			=> 'Enable Quartermile Images',
	'ENABLE_QUARTERMILE_IMAGES_EXPLAIN' 		=> 'This will allow users with correct permissions to add images to quartermile times.',
	'ENABLE_DYNORUN_IMAGES' 			=> 'Enable Dynorun Images',
	'ENABLE_DYNORUN_IMAGES_EXPLAIN' 		=> 'This will allow users with correct permissions to add images to dynoruns.',
	'ENABLE_LAP_IMAGES' 				=> 'Enable Lap Images',
	'ENABLE_LAP_IMAGES_EXPLAIN' 			=> 'This will allow users with correct permissions to add images to laps.',
	'ENABLE_UPLOADED_IMAGES' 			=> 'Enable Uploaded Images',
	'ENABLE_UPLOADED_IMAGES_EXPLAIN' 		=> 'This will users with correct permissions to upload images to items which allow it.',
	'ENABLE_REMOTE_IMAGES' 				=> 'Enable Remote Images',
	'ENABLE_REMOTE_IMAGES_EXPLAIN' 			=> 'This will users with correct permissions to link to remote images for items which allow it.',
	'REMOTE_TIMEOUT' 				=> 'Remote Image Timeout',
	'REMOTE_TIMEOUT_EXPLAIN' 			=> 'Time in seconds phpBB Garage will try to download a remote image for thumbnail creation.',
	'ENABLE_MODIFICATION_GALLERY' 			=> 'Enable Modification Gallery',
	'ENABLE_MODIFICATION_GALLERY_EXPLAIN' 		=> 'Will display a summary of modification images when viewing a vehicle.',
	'ENABLE_QUARTERMILE_GALLERY' 			=> 'Enable Quartermile Gallery',
	'ENABLE_QUARTERMILE_GALLERY_EXPLAIN' 		=> 'Will display a summary of quartermile images when viewing a vehicle.',
	'ENABLE_DYNORUN_GALLERY' 			=> 'Enable Dynorun Gallery',
	'ENABLE_DYNORUN_GALLERY_EXPLAIN' 		=> 'Will display a summary of dynorun images when viewing a vehicle.',
	'ENABLE_LAP_GALLERY'	 			=> 'Enable Lap Gallery',
	'ENABLE_LAP_GALLERY_EXPLAIN'	 		=> 'Will display a summary of lap images when viewing a vehicle.',
	'GALLERY_LIMIT' 				=> 'Gallery Limit',
	'GALLERY_LIMIT_EXPLAIN' 			=> 'Limits the number of images shown in any enabled gallery.',
	'IMAGE_MAX_SIZE' 				=> 'Image Maximum Filesize',
	'IMAGE_MAX_SIZE_EXPLAIN' 			=> 'The maximum kilobtyes an image uploaded can be.',
	'IMAGE_MAX_RESOLUTION' 				=> 'Image Maximum Resolution',
	'IMAGE_MAX_RESOLUTION_EXPLAIN' 			=> 'The maximum resolution in pixels an image uploaded can be.',
	'THUMBNAIL_RESOLUTION' 				=> 'Thumbnail Resolution',
	'THUMBNAIL_RESOLUTION_EXPLAIN'			=> 'The resolution of created thumbnails.',
	'ENABLE_BROWSE_MENU' 				=> 'Enable Browse Menu',
	'ENABLE_BROWSE_MENU_EXPLAIN' 			=> 'Browse option will appear on main menu.',
	'ENABLE_SEARCH_MENU' 				=> 'Enable Search Menu',
	'ENABLE_SEARCH_MENU_EXPLAIN' 			=> 'Search option will appear on main menu.',
	'ENABLE_INSURANCE_REVIEW_MENU' 			=> 'Enable Insurance Review Menu',
	'ENABLE_INSURANCE_REVIEW_MENU_EXPLAIN' 		=> 'Insurance Review option will appear on main menu.',
	'ENABLE_GARAGE_REVIEW_MENU' 			=> 'Enable Garage Review Menu',
	'ENABLE_GARAGE_REVIEW_MENU_EXPLAIN' 		=> 'Garage Review option will appear on main menu.',
	'ENABLE_SHOP_REVIEW_MENU' 			=> 'Enable Shop Review Menu',
	'ENABLE_SHOP_REVIEW_MENU_EXPLAIN' 		=> 'Shop Review option will appear on main menu.',
	'ENABLE_QUARTERMILE_MENU' 			=> 'Enable Quartermile Menu',
	'ENABLE_QUARTERMILE_MENU_EXPLAIN' 		=> 'Quartermile option will appear on main menu.',
	'ENABLE_DYNORUN_MENU' 				=> 'Enable Dynorun Menu',
	'ENABLE_DYNORUN_MENU_EXPLAIN' 			=> 'Dynorun table option will appear on main menu',
	'ENABLE_LAP_MENU' 				=> 'Enable Lap table Menu',
	'ENABLE_LAP_MENU_EXPLAIN' 			=> 'Lap table option will appear on main menu',
	'ENABLE_GARAGE_HEADER' 				=> 'Enable Garage Header',
	'ENABLE_GARAGE_HEADER_EXPLAIN' 			=> 'Garage option will appear on overall header.',
	'ENABLE_QUARTERMILE_HEADER' 			=> 'Enable Quartermile Header',
	'ENABLE_QUARTERMILE_HEADER_EXPLAIN' 		=> 'Quartermile option will appear on overall header.',
	'ENABLE_DYNORUN_HEADER' 			=> 'Enable Dynorun Header',
	'ENABLE_DYNORUN_HEADER_EXPLAIN' 		=> 'Dynorun option will appear on overall header.',
	'ENABLE_FEATURED_VEHICLE' 			=> 'Enable Featured Vehicle',
	'ENABLE_FEATURED_VEHICLE_EXPLAIN' 		=> 'A vehicle will be displayed on the index page. The vehicle will be picked from the selection below.',
	'ENABLE_NEWEST_VEHCILE' 			=> 'Enable Newest Vehicle', 
	'ENABLE_NEWEST_VEHCILE_EXPLAIN' 		=> 'Display the \'Newest Vehicle\' block on the index page.', 
	'NEWEST_VEHICLE_LIMIT' 				=> 'Newest Vehicle Limit',
	'NEWEST_VEHICLE_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Newest Vehicle\' block.',
	'ENABLE_UPDATED_VEHICLE' 			=> 'Enable Updated Vehicle',
	'ENABLE_UPDATED_VEHICLE_EXPLAIN' 		=> 'Display the \'Updated Vehicle\' block on the index page.',
	'UPDATED_VEHICLE_LIMIT' 			=> 'Updated Vehicle Limit',
	'UPDATED_VEHICLE_LIMIT_EXPLAIN' 		=> 'Number of vehicles to display in \'Updated Vehicle\' block.',
	'ENABLE_NEWEST_MODIFICATION' 			=> 'Enable Newest Modification',
	'ENABLE_NEWEST_MODIFICATION_EXPLAIN' 		=> 'Display the \'Newest Modification\' block on the index page.',
	'NEWEST_MODIFICATION_LIMIT' 			=> 'Newest Modification Limit',
	'NEWEST_MODIFICATION_LIMIT_EXPLAIN' 		=> 'Number of vehicles to display in \'Newest Modification\' block.',
	'ENABLE_UPDATED_MODIFICATION' 			=> 'Enable Updated Modification',
	'ENABLE_UPDATED_MODIFICATION_EXPLAIN' 		=> 'Display the \'Updated Modification\' block on the index page.',
	'UPDATED_MODIFICATION_LIMIT' 			=> 'Updated Modification Limit',
	'UPDATED_MODIFICATION_LIMIT_EXPLAIN' 		=> 'Number of vehicles to display in \'Updated Modification\' block.',
	'ENABLE_MOST_MODIFIED' 				=> 'Enable Most Modified',
	'ENABLE_MOST_MODIFIED_EXPLAIN' 			=> 'Display the \'Most Modified\' block on the index page.',
	'MOST_MODIFIED_LIMIT' 				=> 'Most Modified Limit',
	'MOST_MODIFIED_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Most Modified\' block.',
	'ENABLE_MOST_SPENT' 				=> 'Enable Most Spent',
	'ENABLE_MOST_SPENT_EXPLAIN' 			=> 'Display the \'Most Spent\' block on the index page.',
	'MOST_SPENT_LIMIT' 				=> 'Most Spent Limit',
	'MOST_SPENT_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Most Spent\' block.',
	'ENABLE_MOST_VIEWED' 				=> 'Enable Most Viewed',
	'ENABLE_MOST_VIEWED_EXPLAIN' 			=> 'Display the \'Most Viewed\' block on the index page.',
	'MOST_VIEWED_LIMIT' 				=> 'Most Viewed Limit',
	'MOST_VIEWED_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in  \'Most Viewed\' block.',
	'ENABLE_LAST_COMMENTED' 			=> 'Enable Last Commented',
	'ENABLE_LAST_COMMENTED_EXPLAIN' 		=> 'Display the \'Last Commented\' block on the index page.',
	'LAST_COMMENTED_LIMIT' 				=> 'Last Commented Limit',
	'LAST_COMMENTED_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Last Commented\' block.',
	'ENABLE_TOP_DYNORUN' 				=> 'Enable Top Dynorun',
	'ENABLE_TOP_DYNORUN_EXPLAIN' 			=> 'Display the \'Top Dynorun\' block on the index page.',
	'TOP_DYNORUN_LIMIT' 				=> 'Top Dynorun Limit',
	'TOP_DYNORUN_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Top Dynorun\' block.',
	'ENABLE_TOP_QUARTERMILE' 			=> 'Enable Top Quartermile',
	'ENABLE_TOP_QUARTERMILE_EXPLAIN' 		=> 'Display the \'Top Quartermile\' block on the index page.',
	'TOP_QUARTERMILE_LIMIT' 			=> 'Top Quartermile Limit',
	'TOP_QUARTERMILE_LIMIT_EXPLAIN' 		=> 'Number of vehicles to display in \'Top Quartermile\' block.',
	'ENABLE_TOP_RATING' 				=> 'Enable Top Rating',
	'ENABLE_TOP_RATING_EXPLAIN' 			=> 'Display the \'Top Rating\' block on the index page.',
	'TOP_RATING_LIMIT' 				=> 'Top Rating Limit', 
	'TOP_RATING_LIMIT_EXPLAIN' 			=> 'Number of vehicles to display in \'Top Rating\' block.', 
	'VEHICLES_PER_PAGE' 				=> 'Vehicles Per Page',
	'VEHICLES_PER_PAGE_EXPLAIN' 			=> '',
	'YEAR_RANGE_BEGINNING' 				=> 'Year Range Beginning', 
	'YEAR_RANGE_BEGINNING_EXPLAIN' 			=> 'This is the earliest year you want to appear as a selection for a new vehicle. Format CCYY', 
	'YEAR_RANGE_END' 				=> 'Year Range End Offset',
	'YEAR_RANGE_END_EXPLAIN' 			=> 'Amount of years offset from current year for latest year you want to appear for a new vehicle. Set to a positive integer the amount will be added to the current year, set to a negative integer the amount will be subtracted from the current year.',
	'USER_SUBMIT_MAKE' 				=> 'User Make Submission',
	'USER_SUBMIT_MAKE_EXPLAIN' 			=> 'Enable users to submit new makes.',
	'USER_SUBMIT_MODEL' 				=> 'User Model Submission',
	'USER_SUBMIT_MODEL_EXPLAIN' 			=> 'Enable users to submit new models',
	'USER_SUBMIT_BUSINESS' 				=> 'User Business Submission',
	'USER_SUBMIT_BUSINESS_EXPLAIN' 			=> 'Enable users to submit new business\'s',
	'ENABLE_LATESTMAIN_VEHICLE' 			=> 'Enable Latest Vehicles',
	'ENABLE_LATESTMAIN_VEHICLE_EXPLAIN' 		=> 'Enable \'Latest Updated\' block on all pages',
	'LATESTMAIN_VEHCILE_LIMIT' 			=> 'Latest Vehicles Limit ',
	'LATESTMAIN_VEHCILE_LIMIT_EXPLAIN' 		=> 'Number of vehicle to be displayed in \'Latest Updated\' block on all pages',
	'GARAGE_DATE_FORMAT' 				=> 'Date Format',
	'GARAGE_DATE_FORMAT_EXPLAIN' 			=> 'Date format used to display items in the garage.',
	'PROFILE_INTEGRATION' 				=> 'Profile Integration',
	'PROFILE_INTEGRATION_EXPLAIN' 			=> 'Display thumbnails for all vehicle images rather than hilite image',
	'ENABLE_GUESTBOOK' 				=> 'Enable Guestbook',
	'ENABLE_GUESTBOOK_EXPLAIN' 			=> 'Enable guestbook system, allowing users with correct permissions to post comments about vehicles.',
	'FEATURED_VEHICLE_ID' 				=> 'Featured Vehicle ID', 
	'FEATURED_VEHICLE_ID_EXPLAIN' 			=> 'Enter a vehicle ID to be displayed as featured vehicle.', 
	'FEATURED_FROM_BLOCK' 				=> 'Featured Vehicle From Block', 
	'FEATURED_FROM_BLOCK_EXPLAIN' 			=> 'Select index block to chose featured vehicle from. Top entry will be featured.', 
	'RANDOM'					=> 'Random',
	'FROM_BLOCK'					=> 'From block',
	'BY_VEHICLE_ID'					=> 'From vehicle ID',
	'FEATURED_VEHICLE_DESCRIPTION' 			=> 'Featured Vehicle Description',
	'FEATURED_VEHICLE_DESCRIPTION_EXPLAIN' 		=> 'Enter a description to be displayed with featured vehicle.',
	'INTEGRATE_MEMBERLIST' 				=> 'Integrate Memberlist',
	'INTEGRATE_MEMBERLIST_EXPLAIN' 			=> 'Display garage button for users with any vehicles.',
	'INTEGRATE_PROFILE' 				=> 'Integrate Profile',
	'INTEGRATE_PROFILE_EXPLAIN' 			=> 'Display main vehicle info in users profile.',
	'INTEGRATE_VIEWTOPIC' 				=> 'Integrate Viewtopic',
	'INTEGRATE_VIEWTOPIC_EXPLAIN' 			=> 'Display users main vehicle link and garage button for users with any vehicles.',
	'PENDING_PM_NOTIFY' 				=> 'Pending PM Notification',
	'PENDING_PM_NOTIFY_EXPLAIN' 			=> 'Users authorised to moderate the garage will recieve PM\'s on pending items.',
	'PENDING_EMAIL_NOTIFY' 				=> 'Pending Email Notification',
	'PENDING_EMAIL_NOTIFY_EXPLAIN' 			=> 'Users authorised to moderate the garage will recieve email\'s on pending items.',
	'PENDING_PM_NOTIFY_OPTOUT' 			=> 'Pending PM Notification Optout',
	'PENDING_PM_NOTIFY_OPTOUT_EXPLAIN' 		=> 'Users authorised to moderate the garage can optout of recieving PM\'s from their UCP',
	'PENDING_EMAIL_NOTIFY_OPTOUT' 			=> 'Pending Email Notification Optout',
	'PENDING_EMAIL_NOTIFY_OPTOUT_EXPLAIN' 		=> 'Users authorised to moderate the garage can optout of recieving emails\'s from their UCP',
	'ENABLE_VEHICLE_APPROVAL' 			=> 'Enable Vehicle Approval',
	'ENABLE_VEHICLE_APPROVAL_EXPLAIN' 		=> 'Vehicles require moderator approval before being listed.',
	'ENABLE_GUESTBOOK_COMMENT_APPROVAL' 		=> 'Enable Guestbook Comment Approval',
	'ENABLE_GUESTBOOK_COMMENT_APPROVAL_EXPLAIN' 	=> 'Commens require moderator approval before being listed.',
	'GARAGE_INDEX_COLUMNS'				=> 'Columns On Index',
	'GARAGE_INDEX_COLUMNS_EXPLAIN'			=> 'Number of columns used on index page.',
	'ENABLE_USER_INDEX_COLUMNS'			=> 'Enable User Collumn Index',
	'ENABLE_USER_INDEX_COLUMNS_EXPLAIN'		=> 'Allow users override board default.',
	'ENABLE_GUESTBOOK_BBCODE'			=> 'Enable Guestbook BBCode',
	'ENABLE_GUESTBOOK_BBCODE_EXPLAIN'		=> 'Allow users to use BBCodes in guestbook comments.',
	'ENABLE_USER_SUBMIT_PRODUCT'			=> 'User Product Submission',
	'ENABLE_USER_SUBMIT_PRODUCT_EXPLAIN'		=> 'Enable users to submit new products.',
	'ENABLE_PRODUCT_APPROVAL' 			=> 'Enable Product Approval',
	'ENABLE_PRODUCT_APPROVAL_EXPLAIN' 		=> 'Products require moderator approval before being listed.',
	'ENABLE_PRODUCT_SEARCH' 			=> 'Enable Product Search',
	'ENABLE_PRODUCT_SEARCH_EXPLAIN' 		=> 'Allow searching by product &amp; manufacturer.',


	'ENABLE_TRACKTIME'				=> 'Enable Tracktimes',
	'ENABLE_TRACKTIME_EXPLAIN'			=> 'Enable Tracktimes',
	'ENABLE_LAP_APPROVAL'				=> 'Enable Lap Approval',
	'ENABLE_LAP_APPROVAL_EXPLAIN'			=> 'Enable Lap Approval',
	'ENABLE_TRACK_APPROVAL'				=> 'Enable Track Approval',
	'ENABLE_TRACK_APPROVAL_EXPLAIN'			=> 'Enable Track Approval',
	'ENABLE_USER_ADD_LAP'				=> 'User Track Submission',
	'ENABLE_USER_ADD_LAP_EXPLAIN' 			=> 'Enable users to submit new tracks',


//LOG Messages Keys
	'LOG_GARAGE_CONFIG_GENERAL'			=> '<strong>Altered garage general settings</strong>',
	'LOG_GARAGE_CONFIG_MENU'			=> '<strong>Altered garage menu settings</strong>',
	'LOG_GARAGE_CONFIG_INDEX'			=> '<strong>Altered garage index page settings</strong>',
	'LOG_GARAGE_CONFIG_IMAGES'			=> '<strong>Altered garage image settings</strong>',
	'LOG_GARAGE_CONFIG_QUARTERMILE'			=> '<strong>Altered garage quartermile settings</strong>',
	'LOG_GARAGE_CONFIG_DYNORUN'			=> '<strong>Altered garage dynorun settings</strong>',
	'LOG_GARAGE_CONFIG_TRACK'			=> '<strong>Altered garage track &amp; lap settings</strong>',
	'LOG_GARAGE_CONFIG_INSURANCE'			=> '<strong>Altered garage insurance settings</strong>',
	'LOG_GARAGE_CONFIG_BUSINESS'			=> '<strong>Altered garage business settings</strong>',
	'LOG_GARAGE_CONFIG_RATING'			=> '<strong>Altered garage rating settings</strong>',
	'LOG_GARAGE_CONFIG_GUESTBOOK'			=> '<strong>Altered garage guestbook settings</strong>',
	'LOG_GARAGE_CONFIG_PRODUCT'			=> '<strong>Altered garage product settings</strong>',
	'LOG_GARAGE_CONFIG_SERVICE'			=> '<strong>Altered garage service settings</strong>',
	'LOG_GARAGE_CONFIG_BLOG'			=> '<strong>Altered garage blog settings</strong>',

	'NAME' => 'Name',
	'BROWSE' => 'Browse',

//Added For RC5
	'No_Orphaned_Files' => 'You Do Not Appear To Have Any Orphaned Files',
	'Orphaned_Files_Removed' => 'Orphaned Files Removed',
	'No_Orphaned_Files_Selected' => 'No orphaned files were selected, therefore none were removed ;)',
	'Rebuild_Thumbnails_Complete' => 'Rebuild All Thumbnails completed',
	'Permissions_Updated' => 'Garage Permissions Updated.',
	'Shop' => 'Shop',
	'Processing_Attach_ID' => 'Processing attach_id: ',
	'Remote_Image' => 'Remote Image: ',
	'File_Name' => 'file_name: ',
	'Temp_File_Name' => 'tmp_file_name: ',
	'Rebuilt' => 'Rebuilt: ',
	'Thumb_File' => 'Thumb File: ',
	'Source_File' => 'Source File: ',
	'File_Does_Not_Exist' => 'ERROR -- Remote file does not exist!',
	'Source_Unavailable' => 'Rebuild Failed Source Image Unavailable: ',
	'No_Source_File' => 'Thumb Creation Failed No Source File :',
	'Started_At' => 'We started at : ',
	'Ended_At' => 'We ended at : ',
	'Have_Done' => 'We have done : ',
	'Need_To_Process' => 'We need to process in total : ',
	'Log_To' => 'We will log to : ',
	'Out_Of' => 'Out Of',
	'Kbytes' => 'kbytes',

	'PERMANENT' 			=> 'Permanent',
	'NON_PERMANENT'			=> 'Non Permanent',
	'ENABLE_WATERMARK'		=> 'Enable Watermark',
	'ENABLE_WATERMARK_EXPLAIN'	=> 'Enable Watermarking Of Images',
	'WATERMARK_TYPE'		=> 'Watermark Type',
	'WATERMARK_TYPE_EXPLAIN'	=> 'Permanent will save original file with watermark. Non permanent will leave file in original state.',
	'WATERMARK_SOURCE'		=> 'Watermark Sourcefile',
	'WATERMARK_SOURCE_EXPLAIN'	=> 'Source file to be used for watermarking.',

	//CATEGORY KEYS
	'CREATE_CATEGORY'		=> 'Create category',
	'CATEGORY_SETTINGS'		=> 'Category settings',
	'CATEGORY_DELETE'		=> 'Category delete',
	'CATEGORY_UPDATE'		=> 'Category update',
	'CATEGORY_DELETED'		=> 'Category deleted',
	'CATEGORY_UPDATED'		=> 'Category updated',
	'CATEGORY_DELETE_EXPLAIN'	=> 'The form below will allow you to delete a modification category. You are able to decide where you want to put all modifications it contained.',
	'CATEGORY_UPDATE_EXPLAIN'	=> 'The form below will allow you to update a modification category.',
	'CATEGORY_NAME'			=> 'Category name',
	'DELETE_ALL_MODIFICATIONS'	=> 'Delete all modifications',
	'MOVE_MODIFICATIONS_TO'		=> 'Move modifications to',
	'NO_DESTINATION_CATEGORY'	=> 'No destintation category selected',
	'NO_CATEGORY'			=> 'Category does not exists',
	'CATEGORY_NAME_EMPTY'		=> 'No category name entered',
	'GARAGE_CAT_TITLE' 		=> 'Garage Categories Control',
	'GARAGE_CAT_EXPLAIN' 		=> 'On this screen you can manage your categories: create, alter, delete.',

	//QUOTA KEYS
	'QUOTA_TITLE'			=> 'Quota management',
	'QUOTA_EXPLAIN'			=> 'This page controls quotas for how many vehicles and images an user can have.<br /><br />Default quotas are the values given to any user that has the permissions effective by quotas, unless they are within a group granted a quota below.',
	'GROUP_QUOTA_EXPLAIN'		=> 'Groups only get displayed here if they have been granted permissions that allow them to create items which can be controlled via quotas.If a group you want to adjust quotas for is not here you need to manage permissions first and grant them the required permissions.',
	'REMOTE'			=> 'Remote',
	'UPLOADED'			=> 'Uploaded',
	'DEFAULT_QUOTA'			=> 'Default quota',
	'GROUP_QUOTA'			=> 'Group quota',
	'VEHICLE_QUOTA'			=> 'Vehicle quota',
	'UPLOAD_IMAGE_QUOTA'		=> 'Uploaded image quota',
	'REMOTE_IMAGE_QUOTA'		=> 'Remote image quota',
	'ENABLE_GROUP_VEHICLE_QUOTA'	=> 'Enable vehicle quota',
	'ENABLE_GROUP_IMAGES_QUOTA'	=> 'Enable image quotas',
	'QUOTAS_UPDATED'		=> 'Quotas updated',
	'EMPTY_DEFAULT_QUOTA'		=> 'A default quota value has not been entered for all values',
	'EMPTY_GROUP_VEHICLE_QUOTA'	=> 'The vehicle quota value has not been entered for a group that has been selected',
	'EMPTY_GROUP_IMAGE_QUOTA'	=> 'An image quota value has not been entered for a group that has been selected',

	//TOOLS KEYS
	'TOOLS_TITLE' 			=> 'Garage Tool Control',
	'TOOLS_EXPLAIN' 		=> 'On this screen you can run garage tools.',
	'TOOLS_REBUILD' 		=> 'Rebuild All Thumbnails',
	'TOOLS_IMAGES_PER_CYCLE' 	=> 'Images per cycle',
	'TOOLS_IMAGES_PER_CYCLE_EXPLAIN'=> 'Number of images to process each cycle to control CPU usage',
	'TOOLS_CREATE_LOG' 		=> 'Create log',
	'TOOLS_CREATE_LOG_EXPLAIN' 	=> 'Create a logfile detailing each action',
	'TOOLS_ORPHANED_TITLE' 		=> 'Find/Remove Orphan Image Files',
	'TOOLS_ORPHANED_EXPLAIN' 	=> 'This tool is used to locate any abondanded files that the Garage had once created. These abandoned files could be a result of doing any manual work in the database, running the rebuild tool and it failing part way through, or after substantial upgrading to the Garage. Under normal circumstances there should be no orphaned files.<br /><br />The first step of this tool is just to search for files, no action will be taken unless you confirm the findings on the next step.',
	'PER_CYCLE' 			=> 'Per Cycle',
	'TOOLS_LOG_FILE' 		=> 'Log filename ',
	'TOOLS_LOG_FILE_EXPLAIN' 	=> 'Filename of the detailed log being created',


	//BUSINESS KEYS
	'GARAGE_BUSINESS_TITLE' 	=> 'Garage Business Control',
	'GARAGE_BUSINESS_EXPLAIN' 	=> 'On this screen you can manage your business\'s: create, edit, delete.',
	'EDIT_BUSINESS'			=> 'Edit business',
	'CREATE_BUSINESS'		=> 'Create business',
	'BUSINESS_CREATED'		=> 'Business created',
	'BUSINESS_UPDATE'		=> 'Update Business',
	'BUSINESS_UPDATE_EXPLAIN'	=> 'The form below will allow you to customise this business',
	'BUSINESS_UPDATED'		=> 'Business updated',
	'BUSINESS_DELETE'		=> 'Delete business',
	'BUSINESS_DELETE_EXPLAIN'	=> 'The form below will allow you to delete a business. You are able to decide where you want to put all items linked to it. Where a business is of multiple types you will have multiple options',
	'BUSINESS_DELETED'		=> 'Business deleted',
	'BUSINESS_SETTINGS'		=> 'Business settings',
	'BUSINESS_NAME' 		=> 'Business Name',
	'BUSINESS_ADDRESS'		=> 'Address',
	'BUSINESS_TELEPHONE'		=> 'Telephone No.',
	'BUSINESS_FAX'			=> 'Fax No.',
	'BUSINESS_WEBSITE'		=> 'Website',
	'BUSINESS_EMAIL'		=> 'Email',
	'BUSINESS_OPENING_HOURS'	=> 'Opening Hours',
	'BUSINESS_TYPE'			=> 'Type',
	'BUSINESS_NAME_EMPTY'		=> 'Busines name empty',
	'INSURANCE'			=> 'Insurer',
	'GARAGE'			=> 'Garage',
	'RETAIL'			=> 'Shop',
	'PRODUCT_MANUFACTURER'		=> 'Product Manufacturer',
	'MANUFACTURER'			=> 'Manufacturer',
	'DYNOCENTRE'			=> 'Dynocentre',
	'DELETE_ALL_PREMIUMS'		=> 'Delete all premiums from insurer',
	'MOVE_PREMIUMS_TO'		=> 'Move premiums to',
	'DELETE_ALL_DYNORUNS'		=> 'Delete all dynoruns from dynocentre',
	'MOVE_DYNORUNS_TO'		=> 'Move dynoruns to',
	'DELETE_BOUGHT_MODIFICATIONS'	=> 'Delete modifications bought from business',
	'DELETE_INSTALLED_MODIFICATIONS'=> 'Delete modifications installed by business',
	'DELETE_MADE_MODIFICATIONS'	=> 'Delete modifications made by business',

	//MAKE & MODEL KEYS
	'MODELS_TITLE' 			=> 'Garage Model &amp; Makes Control',
	'MODELS_EXPLAIN' 		=> 'On this screen you can manage your models &amp; makes: add, modify, delete.',
	'CREATE_MAKE'			=> 'Create make',
	'CREATE_MODEL'			=> 'Create model',
	'MAKE_INDEX'			=> 'Make Index',
	'DELETE_ALL_VEHICLES'		=> 'Delete all models &amp; vehicles',
	'MAKE'				=> 'Make',
	'MAKE_DELETE'			=> 'Delete make',
	'MAKE_DELETED'			=> 'Make deleted',
	'MAKE_DELETE_EXPLAIN'		=> 'The form below will allow you to delete a make. You are able to decide where you want to put all items linked to it.',
	'MOVE_VEHICLES_TO'		=> 'Move all models &amp; vehicles to',
	'MODEL'				=> 'Model',
	'MODEL_DELETE'			=> 'Delete model',
	'MODEL_DELETED'			=> 'Model deleted',
	'MODEL_DELETE_EXPLAIN'		=> 'The form below will allow you to delete a model. You are able to decide where you want to put all items linked to it.',
	'MAKE_EXISTS'			=> 'Make already exists',
	'MODEL_EXISTS'			=> 'Model already exists for make',
	'MAKE_NAME_EMPTY'		=> 'No make name entered',
	'MODEL_NAME_EMPTY'		=> 'No model name entered',
	'MAKE_UPDATE'			=> 'Update make',
	'MAKE_UPDATED'			=> 'Make updated',
	'MAKE_UPDATE_EXPLAIN'		=> 'The form below will allow you to update this make',
	'MAKE_SETTINGS'			=> 'Make settings',
	'MODEL_UPDATE'			=> 'Update model',
	'MODEL_UPDATED'			=> 'Model updated',
	'MODEL_UPDATE_EXPLAIN'		=> 'The form below will allow you to update this model',
	'MODEL_SETTINGS'		=> 'Model settings',

	//PRODUCT KEYS
	'MANUFACTURER_TITLE'		=> 'Garage Product Control',
	'MANUFACTURER_EXPLAIN'		=> 'On this screen you can manage your products: add, modify, delete. Manufacturers are business\'s and are managed through business management.',
	'MANUFACTURER_INDEX'		=> 'Manufacturer Index',
	'CREATE_PRODUCT'		=> 'Create product',
	'PRODUCTS_TITLE'		=> 'Garage Product Control',
	'PRODUCT_EXPLAIN'		=> 'On this screen you can add, edit, delete, approve, disapprove products',
	'PRODUCT_SETTINGS'		=> 'Product settings',
	'PRODUCT'			=> 'Product',
	'PRODUCT_CREATED'		=> 'Product created',
	'PRODUCT_UPDATED'		=> 'Product updated',
	'PRODUCT_UPDATE'		=> 'Update product',
	'PRODUCT_UPDATE_EXPLAIN'	=> 'The form below will allow you to customise this product',
	'CATEGORY'			=> 'Category',
	'SELECT_CATEGORY'		=> 'Select a category',
	'SELECT_MANUFACTURER'		=> 'Select a manufacturer',
	'PRODUCT_DELETE'		=> 'Delete product',
	'PRODUCT_DELETED'		=> 'Product deleted',
	'PRODUCT_DELETE_EXPLAIN'	=> 'The form below will allow you to delete a product. You are able to decide where you want to put all items linked to it.',


	//TRACK KEYS
	'GARAGE_TRACK_TITLE'		=> 'Garage Track Control',
	'CREATE_TRACK'			=> 'Create track',
	'GARAGE_TRACK_EXPLAIN'		=> 'On this screen you can manage your tracks: add, modify, delete.',
	'TRACK_UPDATE'			=> 'Track update',
	'TRACK_UPDATE_EXPLAIN'		=> 'The form below will allow you to customise this track',
	'TRACK_SETTINGS'		=> 'Track settings',
	'TRACK_NAME'			=> 'Track name',
	'TRACK_LENGTH'			=> 'Length',
	'SELECT_MILEAGE_UNIT'		=> 'Select mileage type',
	'TRACK_DELETE'			=> 'Delete track',
	'TRACK_DELETE_EXPLAIN'		=> 'The form below will allow you to delete a track. You are able to decide where you want to put all items linked to it.',
	'DELETE_ALL_LAPS'		=> 'Delete all laps from track',
	'MOVE_LAPS_TO'			=> 'Move laps to',
	'TRACK_CREATED'			=> 'Track created',
	'TRACK_UPDATED'			=> 'Track updated',
	'TRACK_NAME_EMPTY'		=> 'Track name empty',
	'TRACK_DELETED'			=> 'Track deleted',
	''				=> '',

	//
	'GARAGE_CUSTOM_FIELDS'		=> 'Garage custom fields',

	'ACP_MANAGE_QUOTAS'		=> 'Manage quotas',
	'ACP_MANAGE_BUSINESS'		=> 'Manage business\'s',
	'ACP_MANAGE_CATEGORY'		=> 'Manage categories',
	'ACP_MANAGE_PRODUCTS'		=> 'Manage products',
	'ACP_MANAGE_TRACKS'		=> 'Manage tracks',
	'ACP_MANAGE_CATEGORY'		=> 'Manage categories',
	'ACP_MANAGE_MAKES_MODELS'	=> 'Manage makes and models',

	//Added For B4
	'DEFAULT_MAKE' 			=> 'Default Make Id',
	'DEFAULT_MAKE_EXPLAIN' 		=> 'The make with this ID is shown by default in the \'add vehicle\' list.',
	'DEFAULT_MODEL' 		=> 'Default Model Id',
	'DEFAULT_MODEL_EXPLAIN' 	=> 'The model with this ID is shown by default in the \'add vehicle\' list.',
	'VEHICLES_PER_PAGE_EXPLAIN' 	=> 'The number of vehicles that is shown on a page.',
	'ENABLE_TOP_LAP' 		=> 'Enable Top Lap',
	'ENABLE_TOP_LAP_EXPLAIN' 	=> 'Shows the \'Fastest laptime\' block on the index page.',
	'TOP_LAP_LIMIT' 		=> 'Top Lap Limit',
	'TOP_LAP_LIMIT_EXPLAIN' 	=> 'The number of vehicles that is shown in the \'Fastest laptime\' block.',
	'ACP_GARAGE_SERVICE_CONFIG' 	=> 'Service Configuration',
	'ACP_GARAGE_BLOG_CONFIG' 	=> 'Blog Configuration',
	'ENABLE_SERVICE' 		=> 'Enable Service',
	'ENABLE_SERVICE_EXPLAIN' 	=> 'Enable Service',
	'ENABLE_BLOG' 			=> 'Enable Blog',
	'ENABLE_BLOG_EXPLAIN' 		=> 'Allow users to add a blog to their vehicles.',
	'ENABLE_BLOG_BBCODE' 		=> 'Enable Blog BBCode ',
	'ENABLE_BLOG_BBCODE_EXPLAIN' 	=> 'Enable BBcode usage in blogs.',
	'ACP_MANAGE_QUOTAS' 		=> 'Manage quotas',
	'ACP_MANAGE_PRODUCTS' 		=> 'Manage products',
	'ACP_MANAGE_MAKES_MODELS' 	=> 'Manage makes &amp; models',
	'ACP_MANAGE_BUSINESS' 		=> 'Bussines configuration',
	'ACP_MANAGE_CATEGORY' 		=> 'Category management',
	'EDIT_PRODUCT' 			=> 'Modify Product',
	'SELECT_MANUFACTURER' 		=> 'Select manufacturer',
	'ACP_MANAGE_TRACKS' 		=> 'Track management',
	'EDIT_TRACK' 			=> 'Modify track',
	'ADD_INSURANCE' 		=> 'Add insurance',
	'GARAGE_ORPHANS_TITLE' 		=> 'Garage Orphan Locator',
	'GARAGE_ORPHANS_EXPLAIN' 	=> 'Below are all the orphaned files that were found.  An orphaned file is defined as a file that exists on your local drive that is no longer present in the database.<br />Please check all the applicable orphans you wish to delete.<br /><br /><b>This operation is not undo-able!  Once you choose to remove an orphan it is gone for good.</b>',
	'REMOVE_SELECTED_ORPHANS' 	=> 'Remove Selected Orphans',
	'MOVE_PRODUCT_TO'		=> 'Move products to',
	'DELETE_MADE_PRODUCTS'		=> 'Delete products made by business. (Note: Deletes linked modifications)',

));

?>
