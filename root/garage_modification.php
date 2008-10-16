<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);

/**
* Set root path & include standard phpBB files required
*/
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
require($phpbb_root_path . 'includes/functions_display.' . $phpEx);

/**
* Setup user session, authorisation & language 
*/
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('mods/garage'));

/**
* Check For Garage Install Files
*/
$garage->check_installation_files();

/**
* Build All Garage Classes e.g $garage_images->
*/
require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$vid = request_var('VID', '');
$mid = request_var('MID', '');
$image_id = request_var('image_id', '');

/**
* Build inital navlink..we use the standard phpBB3 breadcrumb process
*/
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['GARAGE'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx"))
);

/**
* Display the moderator control panel link if authorised
*/
if ($garage->mcp_access())
{
	$template->assign_vars(array(
		'U_MCP'	=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=garage', true, $user->session_id),
	));
}

/**
* Perform a set action based on value for $mode
*/
switch( $mode )
{
	/**
	* Display page to create modification
	*/
	case 'add_modification':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=add_modification&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_add_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get vehicle, catgories, shops, garages & manufacturers data from DB
		*/
		$vehicle	= $garage_vehicle->get_vehicle($vid);
		$categories 	= $garage->get_categories();
		$shops	 	= $garage_business->get_business_by_type(BUSINESS_RETAIL);
		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('category_id' => '' , 'manufacturer_id' => '', 'product_id' =>'', 'price' => '', 'shop_id' => '', 'installer_id' => '', 'install_price' => '', 'install_rating' => '', 'product_rating' => '', 'purchase_rating' => '', 'url_image' => '');
		$data	= $garage->process_vars($params);
		$params = array('comments' => '', 'install_comments' => '');
		$data	+= $garage->process_mb_vars($params);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_MODIFICATION'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_modification&amp;VID=$vid"))
		);
		$garage_template->attach_image('modification');
		$garage_template->category_dropdown($categories, $data['category_id']);
		$garage_template->manufacturer_dropdown($manufacturers, $data['manufacturer_id']);
		$garage_template->retail_dropdown($shops, $data['shop_id']);
		$garage_template->garage_dropdown($garages, $data['installer_id']);
		$garage_template->rating_dropdown('product_rating', $data['product_rating']);
		$garage_template->rating_dropdown('purchase_rating', $data['purchase_rating']);
		$garage_template->rating_dropdown('install_rating', $data['install_rating']);
		$template->assign_vars(array(
			'L_BUTTON' 			=> $user->lang['ADD_MODIFICATION'],
			'L_TITLE' 			=> $user->lang['ADD_MODIFICATION'],
			'U_SUBMIT_PRODUCT'		=> "javascript:add_product('')",
			'U_SUBMIT_BUSINESS_SHOP'	=> "javascript:add_shop('')",
			'U_SUBMIT_BUSINESS_GARAGE'	=> "javascript:add_garage('')",
			'U_SUBMIT_BUSINESS_PRODUCT'	=> "javascript:add_manufacturer('')",
			'VID' 				=> $vid,
			'PRICE' 			=> $data['price'],
			'INSTALL_PRICE' 		=> $data['install_price'],
			'MANUFACTURER_ID' 		=> $data['manufacturer_id'],
			'PRODUCT_ID' 			=> $data['product_id'],
			'CATEGORY_ID' 			=> $data['category_id'],
			'MANUFACTURER_ID' 		=> $data['manufacturer_id'],
			'PRODUCT_ID' 			=> $data['product_id'],
			'COMMENTS' 			=> $data['comments'],
			'INSTALL_COMMENTS' 		=> $data['install_comments'],
			'URL_IMAGE'			=> $data['url_image'],
			'CURRENCY'			=> $vehicle['currency'],
			'S_DISPLAY_SUBMIT_BUSINESS'	=> ($garage_config['enable_user_submit_business'] && $auth->acl_get('u_garage_add_business')) ? true : false,
			'S_MODE_USER_SUBMIT' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_data"),
			'S_MODE_ACTION'			=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=insert_modification&amp;VID=$vid"),
		));
		$garage_template->sidemenu();
	break;

	/**
	* Insert new modification
	*/
	case 'insert_modification':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=add_modification&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_add_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('category_id' => '' , 'manufacturer_id' => '', 'product_id' =>'', 'price' => 0, 'shop_id' => '', 'installer_id' => '', 'install_price' => 0, 'install_rating' => 0, 'product_rating' => 0, 'purchase_rating' => 0);
		$data	= $garage->process_vars($params);
		$params = array('comments' => '', 'install_comments' => '');
		$data	+= $garage->process_mb_vars($params);
		$params = array('category_id', 'manufacturer_id', 'product_id');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to create modification
		*/
		$mid = $garage_modification->insert_modification($data);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* Handle any images
		*/
		if ($garage_image->image_attached())
		{
			if ($garage_image->below_image_quotas())
			{
				$image_id = $garage_image->process_image_attached('modification', $mid);
				$hilite = $garage_modification->hilite_exists($mid);
				$garage_image->insert_modification_gallery_image($image_id, $hilite);
			}
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Display page to edit existing modification
	*/
	case 'edit_modification':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_modification.$phpEx?mode=edit_modification&amp;MID=$mid&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get any changed data incase we are arriving from creating a manufacturer, product, shop or garage
		*/
		$params = array('category_id' => '' , 'manufacturer_id' => '', 'product_id' =>'', 'price' => 0, 'shop_id' => '', 'installer_id' => '', 'install_price' => 0, 'install_rating' => 0, 'product_rating' => 0, 'purchase_rating' => 0);
		$store	= $garage->process_vars($params);
		$params = array('comments' => '', 'install_comments' => '');
		$store	+= $garage->process_mb_vars($params);

		/**
		* Get vehicle, modification, catgories, gallery, shops, garages & manufacturers data from DB
		*/
		$vehicle_data 	= $garage_vehicle->get_vehicle($vid);
		$data 		= $garage_modification->get_modification($mid);
		$categories 	= $garage->get_categories();
		$gallery_data 	= $garage_image->get_modification_gallery($mid);
		$shops 		= $garage_business->get_business_by_type(BUSINESS_RETAIL);
		$garages 	= $garage_business->get_business_by_type(BUSINESS_GARAGE);
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_modification.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_MODIFICATION'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID=$vid&amp;MID=$mid"))
		);
		$garage_template->category_dropdown($categories, (!empty($store['category_id'])) ? $store['category_id'] : $data['category_id']);
		$garage_template->manufacturer_dropdown($manufacturers, (!empty($store['manufacturer_id'])) ? $store['manufacturer_id'] : $data['manufacturer_id']);
		$garage_template->retail_dropdown($shops, (!empty($store['shop_id'])) ? $store['shop_id'] : $data['shop_id']);
		$garage_template->garage_dropdown($garages, (!empty($store['installer_id'])) ? $store['installer_id'] : $data['installer_id']);
		$garage_template->rating_dropdown('product_rating', (!empty($store['product_rating'])) ? $store['product_rating'] : $data['product_rating']);
		$garage_template->rating_dropdown('purchase_rating', (!empty($store['purchase_rating'])) ? $store['purchase_rating'] : $data['purchase_rating']);
		$garage_template->rating_dropdown('install_rating', (!empty($store['install_rating'])) ? $store['install_rating'] : $data['install_rating']);
		$garage_template->attach_image('modification');
		$template->assign_vars(array(
       			'L_TITLE' 			=> $user->lang['MODIFY_MOD'],
			'L_BUTTON' 			=> $user->lang['MODIFY_MOD'],
			'U_EDIT_DATA' 			=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid"),
			'U_MANAGE_GALLERY' 		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=manage_modification_gallery&amp;VID=$vid&amp;MID=$mid"),
			'U_SUBMIT_PRODUCT'		=> "javascript:add_product('edit')",
			'U_SUBMIT_BUSINESS_SHOP'	=> "javascript:add_shop('edit')",
			'U_SUBMIT_BUSINESS_GARAGE'	=> "javascript:add_garage('edit')",
			'U_SUBMIT_BUSINESS_PRODUCT'	=> "javascript:add_manufacturer('edit')",
			'MID' 				=> $mid,
			'VID' 				=> $vid,
			'PRICE' 			=> (!empty($store['price'])) ? $store['price'] : $data['price'],
			'INSTALL_PRICE'	 		=> (!empty($store['install_price'])) ? $store['install_price'] : $data['install_price'],
			'PRODUCT_ID' 			=> (!empty($store['product_id'])) ? $store['product_id'] : $data['product_id'],
			'CATEGORY_ID' 			=> (!empty($store['category_id'])) ? $store['category_id'] : $data['category_id'],
			'MANUFACTURER_ID' 		=> (!empty($store['manufacturer_id'])) ? $store['manufacturer_id'] : $data['manufacturer_id'],
			'PRODUCT_ID' 			=> (!empty($store['product_id'])) ? $store['product_id'] : $data['product_id'],
			'COMMENTS' 			=> (!empty($store['comments'])) ? $store['comments'] : $data['comments'],
			'INSTALL_COMMENTS' 		=> (!empty($store['install_comments'])) ? $store['install_comments'] : $data['install_comments'],
			'CURRENCY'			=> $vehicle_data['currency'],
			'S_DISPLAY_SUBMIT_BUS'		=> $garage_config['enable_user_submit_business'],
			'S_MODE_USER_SUBMIT' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_data"),
			'S_MODE_ACTION' 		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=update_modification"),
			'S_IMAGE_MODE_ACTION' 		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=insert_modification_image"),
		));
		for ($i = 0, $count = sizeof($gallery_data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($gallery_data[$i]['attach_id']) AND ($gallery_data[$i]['attach_is_image']) AND (!empty($gallery_data[$i]['attach_thumb_location'])) AND (!empty($gallery_data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $gallery_data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=remove_modification_image&amp;VID=$vid&amp;MID=$mid&amp;image_id=" . $gallery_data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($gallery_data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=set_modification_hilite&amp;image_id=" . $gallery_data[$i]['attach_id'] . "&amp;VID=$vid&amp;MID=$mid") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $gallery_data[$i]['attach_file'])
			);
		}
		$garage_template->sidemenu();		
	break;

	/**
	* Update existing modification
	*/
	case 'update_modification':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage_modification.$phpEx?mode=edit_modification&amp;MID=$mid&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('category_id' => '', 'manufacturer_id' => '', 'product_id' => '', 'price' => 0, 'shop_id' => '', 'installer_id' => '', 'install_price' => 0, 'install_rating' => 0, 'product_rating' => 0, 'editupload' => '', 'image_id' => '', 'purchase_rating' => 0);
		$data	= $garage->process_vars($params);
		$params = array('comments' => '', 'install_comments' => '');
		$data	+= $garage->process_mb_vars($params);
		$params = array('category_id', 'manufacturer_id', 'product_id', 'shop_id', 'installer_id');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to update modification
		*/
		$garage_modification->update_modification($data);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Delete existing modification
	*/
	case 'delete_modification':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_delete_modification'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete modification
		*/
		$garage_modification->delete_modification($mid);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Display page to view modification
	*/
	case 'view_modification':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		/**
		* Get modification & gallery data from DB
		*/
		$data = $garage_modification->get_modification($mid);
		$gallery_data = $garage_image->get_modification_gallery($mid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_modification.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=$vid"))
		);
       		for ( $i = 0; $i < count($gallery_data); $i++ )
        	{
			if ( (empty($gallery_data[$i]['attach_thumb_location']) == false) AND ($gallery_data[$i]['attach_thumb_location'] != $gallery_data[$i]['attach_location']) )
			{
				$template->assign_vars(array(
					'S_DISPLAY_GALLERIES' 	=> true,
				));

				$template->assign_block_vars('modification_image', array(
					'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $gallery_data[$i]['attach_id']),
					'IMAGE_NAME'	=> $gallery_data[$i]['attach_file'],
					'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'])
				);
               		} 
		}

		$template->assign_vars(array(
			'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data['user_id']),
			'U_VIEW_GARAGE_BUSINESS'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review&amp;business_id=" . $data['installer_id']),
			'U_VIEW_SHOP_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review&amp;business_id=" . $data['shop_id']),
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
            		'PURCHASE_RATING' 	=> $data['purchase_rating'],
            		'PRODUCT_RATING' 	=> $data['product_rating'],
            		'INSTALL_RATING' 	=> $data['install_rating'],
            		'BUSINESS_NAME' 	=> $data['business_title'],
			'BUSINESS' 		=> $data['install_business_title'],
			'USERNAME' 		=> $data['username'],
			'USERNAME_COLOUR'	=> get_username_string('colour', $data['user_id'], $data['username'], $data['user_colour']),
            		'AVATAR_IMG' 		=> ($user->optionget('viewavatars')) ? get_user_avatar($data['user_avatar'], $data['user_avatar_type'], $data['user_avatar_width'], $data['user_avatar_height']) : '',
            		'DATE_UPDATED' 		=> $user->format_date($data['date_updated']),
            		'MANUFACTURER' 		=> $data['manufacturer'],
            		'TITLE' 		=> $data['title'],
            		'PRICE' 		=> $data['price'],
            		'INSTALL_PRICE' 	=> $data['install_price'],
            		'INSTALL_COMMENTS' 	=> $data['install_comments'],
            		'CURRENCY' 		=> $data['currency'],
            		'CATEGORY' 		=> $data['category_title'],
            		'COMMENTS' 		=> $data['comments'])
         	);
		$garage_template->sidemenu();
	break;

	/**
	* Insert image into modification
	*/
	case 'insert_modification_image':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if ((!$auth->acl_get('u_garage_upload_image')) OR (!$auth->acl_get('u_garage_remote_image')))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=16"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Handle any images
		*/
		if ($garage_image->image_attached())
		{
			if ($garage_image->below_image_quotas())
			{
				$image_id = $garage_image->process_image_attached('modification', $mid);
				$hilite = $garage_modification->hilite_exists($mid);
				$garage_image->insert_modification_gallery_image($image_id, $hilite);
			}
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));
	break;

	/**
	* Set highlight image for modification
	*/
	case 'set_modification_hilite':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to set hightlight image
		*/
		$garage->update_single_field(GARAGE_MODIFICATION_GALLERY_TABLE, 'hilite', 0, 'modification_id', $mid);
		$garage->update_single_field(GARAGE_MODIFICATION_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));
	break;

	/**
	* Delete modification image
	*/
	case 'remove_modification_image':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete modification image
		*/
		$garage_image->delete_modification_image($image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID=$vid&amp;MID=$mid#images"));
		break;

	case 'insert_product':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_product");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_product'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('VID' => '', 'MID' => '', 'category_id' => '', 'manufacturer_id' => '', 'product_id' => '', 'price' => 0, 'shop_id' => '', 'installer_id' => '', 'install_price' => 0, 'install_rating' => 0, 'product_rating' => 0, 'editupload' => '', 'image_id' => '', 'purchase_rating' => 0, 'url_image' => '', 'primary' => '', 'secondary' => '', 'tertiary' => '');
		$data	= $garage->process_vars($params);
		$params = array('title'=> '', 'comments' => '', 'install_comments' => '');
		$data	+= $garage->process_mb_vars($params);

		//Checks All Required Data Is Present
		$params = array('title', 'category_id', 'manufacturer_id');
		$garage->check_required_vars($params);

		//If Needed Perform Notifications If Configured
		if ($garage_config['enable_product_approval'])
		{
			$garage->pending_notification('unapproved_products');
		}

		//Create The Product
		$data['product_id'] = $garage_modification->insert_product($data);

		//Now rather than redirect.. we build a page with all data
		page_header($user->lang['GARAGE']);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_user_submit_data.html')
		);

		$user_submit_action = append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=add_modification");
		if ($data['tertiary'] == "edit")
		{
			$user_submit_action = append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=edit_modification&amp;VID={$data['VID']}&amp;MID={$data['MID']}");
		}

		$template->assign_vars(array(
			'L_BUTTON_LABEL'		=> $user->lang['RETURN_TO_ITEM'],
			'S_USER_SUBMIT_SUCCESS'		=> true,
			'S_USER_SUBMIT_ACTION'		=> $user_submit_action,
		));

		foreach ($data as $key => $value)
		{
			if (empty($value))
			{
				continue;
			}
			$template->assign_block_vars('hidden_data', array(
				'VALUE'	=> $value,
				'NAME'	=> $key,
			));
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

	break;
}
$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>
