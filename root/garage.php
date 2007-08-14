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

/**
* Setup user session, authorisation & language 
*/
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('mods/garage'));

/**
* Build All Garage Classes e.g $garage_images->
*/
require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_insurance.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_track.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_service.' . $phpEx);

//Set The Page Title
$page_title = $user->lang['GARAGE'];

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$sort = request_var('sort', '');
$order = request_var('order', '');
$start = request_var('start', '');
$vid = request_var('VID', '');
$eid = request_var('EID', '');
$bid = request_var('BID', '');
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
	* Display search options page
	*/
	case 'search':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_search'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' 	=> 'garage_header.html',
			'body'   	=> 'garage_search.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['SEARCH'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search"))
		);

		//Get Years As Defined By Admin In ACP
		$years 		= $garage->year_list();
		$manufacturers 	= $garage_business->get_business_by_type(BUSINESS_PRODUCT);
		$makes 		= $garage_model->get_all_makes();
		$categories 	= $garage->get_categories();

		//Build All Required Javascript And Arrays
		$garage_template->category_dropdown($categories);
		$garage_template->year_dropdown($years);
		$garage_template->make_dropdown($makes);
		$garage_template->manufacturer_dropdown($manufacturers);
		$template->assign_vars(array(
			'U_FIND_USERNAME'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=search_garage&amp;field=username&amp;select_single=true'),
			'UA_FIND_USERNAME'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&form=search_garage&field=username&select_single=true', false),
			'S_DISPLAY_SEARCH_INSURANCE'	=> $garage_config['enable_insurance'],
			'S_MODE_ACTION_SEARCH' 		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_results"),
			'S_SEARCH_TAB_ACTIVE'		=> true,
		));

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

	break;

	/**
	* Browse, quartermile table, dynorun table & lap table pages Are really just a search, how cool is that :)
	*/
	case 'browse':
	case 'quartermile_table':
	case 'dynorun_table':
	case 'lap_table':
	case 'search_results':

		//Handle Some Mode Specific Things Like Navlinks & Display Defaults
		if ($mode == 'browse')
		{
			//Build Navlinks
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['BROWSE'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=$mode"))
			);
			$template->assign_vars(array(
				'S_BROWSE_TAB_ACTIVE'	=> true,
			));
			$default_display = 'vehicles';
		}
		else if ($mode == 'quartermile_table')
		{
			//Build Navlinks
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['QUARTERMILE_TABLE'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=$mode"))
			);
			$template->assign_vars(array(
				'S_QUARTERMILE_TABLE_TAB_ACTIVE'	=> true,
			));
			$default_display = 'quartermiles';
		}
		elseif ($mode == 'dynorun_table')
		{
			//Build Navlinks
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['DYNORUN_TABLE'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=$mode"))
			);
			$template->assign_vars(array(
				'S_DYNORUN_TABLE_TAB_ACTIVE'	=> true,
			));
			$default_display = 'dynoruns';
		}
		elseif ($mode == 'lap_table')
		{
			//Build Navlinks
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['LAP_TABLE'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=$mode"))
			);
			$template->assign_vars(array(
				'S_LAP_TABLE_TAB_ACTIVE'	=> true,
			));
			$default_display = 'laps';
		}
		else
		{
			//Build Navlinks
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['SEARCH'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search"))
			);
			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['RESULTS'])
			);
		}

		//Accept default display if already set, else default to vehicles
		$default_display = (empty($default_display)) ? 'vehicles' : $default_display;

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_search'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params	= array('search_year' => '', 'search_make' => '', 'search_model' => '', 'search_category' => '', 'search_manufacturer' => '', 'search_product' => '', 'search_username' => '', 'display_as' => $default_display, 'made_year' => '', 'make_id' => '', 'model_id' => '', 'category_id' => '', 'manufacturer_id' => '', 'product_id' => '');
		$data 	= $garage->process_vars($params);
		$params	= array('username' => '');
		$data 	+= $garage->process_mb_vars($params);

		//Set Required Values To Defaults If They Are Empty
		$start	= (empty($start)) ? '0' : $start;

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_search_results.html')
		);

		//Build Page Header ;)
		page_header($page_title);

		//Depending On Search Results Required We Have Different Data To Pass To Template Engine
		if ($data['display_as'] == 'vehicles')
		{
			$pagination_url = $total_vehicles = null;
			$results_data = $garage->perform_search($data, $total_vehicles, $pagination_url);
			$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}".$pagination_url), $total_vehicles, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_VEHICLE_RESULTS' 	=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_vehicles, $garage_config['cars_per_page'], $start),
				'TOTAL_VEHICLES'		=> ($total_vehicles == 1) ? $user->lang['VIEW_VEHICLE'] : sprintf($user->lang['VIEW_VEHICLES'], $total_vehicles),
			));
			$garage_template->vehicle_assignment($results_data, 'vehicle');
		}
		//Display Results As Modifications
		else if ($data['display_as'] == 'modifications')
		{
			$pagination_url = $total_modifications = null;
			$results_data = $garage->perform_search($data, $total_modifications, $pagination_url);
			$pagination = generate_pagination($pagination_url, $total_modifications, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_MODIFICATION_RESULTS'=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_modifications, $garage_config['cars_per_page'], $start),
				'TOTAL_MODIFICATIONS'		=> ($total_modifications == 1) ? $user->lang['VIEW_MODIFICATION'] : sprintf($user->lang['VIEW_MODIFICATIONS'], $total_modifications),
			));
			for ($i = 0, $count = sizeof($results_data); $i < $count; $i++)
			{
				//Provide Results To Template Engine
				$template->assign_block_vars('modification', array(
					'U_IMAGE'		=> ($results_data[$i]['attach_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $results_data[$i]['attach_id']) : '',
					'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $results_data[$i]['vehicle_id']),
					'U_VIEW_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $results_data[$i]['user_id']),
					'U_VIEW_MODIFICATION'	=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=view_modification&amp;VID=" . $results_data[$i]['vehicle_id'] . "&amp;MID=" . $results_data[$i]['modification_id']),
					'IMAGE'			=> $user->img('garage_img_attached', 'MODIFICATION_IMAGE_ATTACHED'),
					'VEHICLE'		=> $results_data[$i]['vehicle'],
					'MODIFICATION'		=> $results_data[$i]['modification_title'],
					'CATEGORY'		=> $results_data[$i]['category_title'],
					'USERNAME'		=> $results_data[$i]['username'],
					'PRICE'			=> $results_data[$i]['price'],
					'RATING'		=> $results_data[$i]['product_rating'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $results_data[$i]['user_id'], $results_data[$i]['username'], $results_data[$i]['user_colour']),
				));
			}
		}
		//Display Results As Premiums
		else if ($data['display_as'] == 'premiums')
		{
			$pagination_url = $total_premiums = null;
			$results_data = $garage->perform_search($data, $total_premiums, $pagination_url);
			$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}".$pagination_url), $total_premiums, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_PREMIUM_RESULTS' 	=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_premiums, $garage_config['cars_per_page'], $start),
				'TOTAL_PREMIUMS'		=> ($total_premiums == 1) ? $user->lang['VIEW_PREMIUM'] : sprintf($user->lang['VIEW_PREMIUMS'], $total_premiums),
			));
			//How about Something like
			//$garage_template->assign_premium_block($results_data);
			for ($i = 0, $count = sizeof($results_data); $i < $count; $i++)
			{
				//Provide Results To Template Engine
				$template->assign_block_vars('premium', array(
					'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $results_data[$i]['id']),
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $results_data[$i]['user_id']),
					'U_VIEW_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_insurance_business&amp;BID=" . $results_data[$i]['business_id']),
					'VEHICLE' 		=> $results_data[$i]['vehicle'],
					'USERNAME' 		=> $results_data[$i]['username'],
					'BUSINESS' 		=> $results_data[$i]['title'],
					'PRICE' 		=> $results_data[$i]['price'],
					'MOD_PRICE' 		=> $results_data[$i]['total_spent'],
					'PREMIUM' 		=> $results_data[$i]['premium'],
					'COVER_TYPE' 		=> $results_data[$i]['cover_type'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $results_data[$i]['user_id'], $results_data[$i]['username'], $results_data[$i]['user_colour']),
				));
			}
		}
		//Display Results As Quartermiles
		else if ($data['display_as'] == 'quartermiles')
		{
			$pagination_url = $total_quartermiles = null;
			$results_data = $garage->perform_search($data, $total_quartermiles, $pagination_url);
			$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}".$pagination_url), $total_quartermiles, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_QUARTERMILE_RESULTS'	=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_quartermiles, $garage_config['cars_per_page'], $start),
				'TOTAL_QUARTERMILES'		=> ($total_quartermiles == 1) ? $user->lang['VIEW_QUARTERMILE'] : sprintf($user->lang['VIEW_QUARTERMILES'], $total_quartermiles),
			));
			for ($i = 0, $count = sizeof($results_data); $i < $count; $i++)
			{
				//Provide Results To Template Engine
				$template->assign_block_vars('quartermile', array(
					'U_IMAGE'		=> ($results_data[$i]['attach_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $results_data[$i]['attach_id']) : '',
					'U_VIEWPROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $results_data[$i]['user_id']),
					'U_VIEWVEHICLE'		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $results_data[$i]['id']),
					'U_QUART'		=> append_sid("garage_quartermile.$phpEx?mode=view_quartermile&amp;QMID=".$results_data[$i]['qmid']."&amp;VID=".$results_data[$i]['id']),
					'VEHICLE'		=> $results_data[$i]['vehicle'],
					'USERNAME'		=> $results_data[$i]['username'],
					'IMAGE'			=> $user->img('garage_img_attached', 'QUARTEMILE_IMAGE_ATTACHED'),
					'RT'			=> $results_data[$i]['rt'],
					'SIXTY'			=> $results_data[$i]['sixty'],
					'THREE'			=> $results_data[$i]['three'],
					'EIGHTH'		=> $results_data[$i]['eighth'],
					'EIGHTHMPH'		=> $results_data[$i]['eighthmph'],
					'THOU'			=> $results_data[$i]['thou'],
					'QUART'			=> $results_data[$i]['quart'],
					'QUARTMPH'		=> $results_data[$i]['quartmph'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $results_data[$i]['user_id'], $results_data[$i]['username'], $results_data[$i]['user_colour']),
				));
			}
		}
		//Display Results As Dynoruns
		else if ($data['display_as'] == 'dynoruns')
		{
			$pagination_url = $total_dynoruns = null;
			$results_data = $garage->perform_search($data, $total_dynoruns, $pagination_url);
			$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}".$pagination_url), $total_dynoruns, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_DYNORUN_RESULTS' 	=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_dynoruns, $garage_config['cars_per_page'], $start),
				'TOTAL_DYNORUNS'		=> ($total_dynoruns == 1) ? $user->lang['VIEW_DYNORUN'] : sprintf($user->lang['VIEW_DYNORUNS'], $total_dynoruns),
			));
			for ($i = 0, $count = sizeof($results_data); $i < $count; $i++)
			{
				//Provide Results To Template Engine
				$template->assign_block_vars('dynorun', array(
					'U_IMAGE'		=> ($results_data[$i]['attach_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $results_data[$i]['attach_id']) : '',
					'U_VIEWPROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $results_data[$i]['user_id']),
					'U_VIEWVEHICLE'		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $results_data[$i]['vehicle_id']),
					'U_BHP'			=> append_sid("garage_dynorun.$phpEx?mode=view_dynorun&amp;DID=".$results_data[$i]['did']."&amp;VID=".$results_data[$i]['vehicle_id']),
					'IMAGE'			=> $user->img('garage_img_attached', 'QUARTEMILE_IMAGE_ATTACHED'),
					'USERNAME'		=> $results_data[$i]['username'],
					'VEHICLE'		=> $results_data[$i]['vehicle'],
					'DYNOCENTRE'		=> $results_data[$i]['title'],
					'BHP'			=> $results_data[$i]['bhp'],
					'BHP_UNIT'		=> $results_data[$i]['bhp_unit'],
					'TORQUE'		=> $results_data[$i]['torque'],
					'TORQUE_UNIT'		=> $results_data[$i]['torque_unit'],
					'BOOST'			=> $results_data[$i]['boost'],
					'BOOST_UNIT'		=> $results_data[$i]['boost_unit'],
					'NITROUS'		=> $results_data[$i]['nitrous'],
					'PEAKPOINT'		=> $results_data[$i]['peakpoint'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $results_data[$i]['user_id'], $results_data[$i]['username'], $results_data[$i]['user_colour']),
				));
			}
		}
		//Display Results As Track Times
		else if ($data['display_as'] == 'laps')
		{
			$pagination_url = $total_laps = null;
			$results_data = $garage->perform_search($data, $total_laps, $pagination_url);
			$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}".$pagination_url), $total_laps, $garage_config['cars_per_page'], $start);
			$template->assign_vars(array(
				'S_DISPLAY_LAP_RESULTS' 	=> true,
				'PAGINATION' 			=> $pagination,
				'PAGE_NUMBER' 			=> on_page($total_laps, $garage_config['cars_per_page'], $start),
				'TOTAL_LAPS'			=> ($total_laps == 1) ? $user->lang['VIEW_LAP'] : sprintf($user->lang['VIEW_LAPS'], $total_laps),
			));
			for ($i = 0, $count = sizeof($results_data); $i < $count; $i++)
			{
				//Provide Results To Template Engine
				$template->assign_block_vars('lap', array(
					'TRACK'			=> $results_data[$i]['title'],
					'CONDITION'		=> $garage_track->get_track_condition($results_data[$i]['condition_id']),
					'TYPE'			=> $garage_track->get_lap_type($results_data[$i]['type_id']),
					'MINUTE'		=> $results_data[$i]['minute'],
					'SECOND'		=> $results_data[$i]['second'],
					'MILLISECOND'		=> $results_data[$i]['millisecond'],
					'IMAGE'			=> $user->img('garage_img_attached', 'IMAGE_ATTACHED'),
					'USERNAME'		=> $results_data[$i]['username'],
					'VEHICLE'		=> $results_data[$i]['vehicle'],
					'U_VIEWPROFILE'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $results_data[$i]['user_id']),
					'U_VIEWVEHICLE'		=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $results_data[$i]['vehicle_id']),
					'U_IMAGE'		=> ($results_data[$i]['attach_id']) ? append_sid("garage.$phpEx", "mode=view_image&amp;image_id=". $results_data[$i]['attach_id']) : '',
					'U_TRACK'		=> append_sid("garage_track.$phpEx?mode=view_track&amp;TID=".$results_data[$i]['track_id']."&amp;VID=". $results_data[$i]['vehicle_id']),
					'U_LAP'			=> append_sid("garage_track.$phpEx?mode=view_lap&amp;LID=".$results_data[$i]['lid']."&amp;VID=". $results_data[$i]['vehicle_id']),
					'USERNAME_COLOUR'	=> get_username_string('colour', $results_data[$i]['user_id'], $results_data[$i]['username'], $results_data[$i]['user_colour']),
				));
			}
		}
		//Pass Selected Options So On Sort We Now What We Are Sorting ;)
		$template->assign_vars(array(
			'SEARCH_YEAR'		=> $data['search_year'],
			'SEARCH_MAKE'		=> $data['search_make'],
			'SEARCH_MODEL'		=> $data['search_model'],
			'SEARCH_CATEGORY'	=> $data['search_category'],
			'SEARCH_MANUFACTURER'	=> $data['search_manufacturer'],
			'SEARCH_PRODUCT'	=> $data['search_product'],
			'SEARCH_USERNAME'	=> $data['search_username'],
			'DISPLAY_AS'		=> $data['display_as'],
			'MADE_YEAR'		=> $data['made_year'],
			'MAKE_ID'		=> $data['make_id'],
			'MODEL_ID'		=> $data['model_id'],
			'CATEGORY_ID'		=> $data['category_id'],
			'MANUFACTURER_ID'	=> $data['manufacturer_id'],
			'PRODUCT_ID'		=> $data['product_id'],
			'USERNAME'		=> $data['username'],
			'S_MODE_ACTION'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=$mode"),
		));

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* View a iamge contained in the garage
	*/
	case 'view_image':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Increment View Counter For This Image
		$garage->update_view_count(GARAGE_IMAGES_TABLE, 'attach_hits', 'attach_id', $image_id);

		//Pull Required Image Data From DB
		$data = $garage_image->get_image($image_id);

		//Check To See If This Is A Remote Image
		if (preg_match( "/^http:\/\//i", $data['attach_location']))
		{
			//Redirect Them To The Remote Image
			header("Location: " . $data['attach_location']);
			exit;
		}
		//Looks Like It's A Local Image...So Lets Display It
		else
		{
			//Let Handle Watermarking... ;)
			$watermark_ok = 0;
			if ($garage_config['enable_watermark'] == 1 && $garage_config['watermark_type'] == 'non_permanent')
			{
				$data['watermark_ext'] = strtolower( preg_replace( "/^.*\.(\S+)$/", "\\1", $garage_config['watermark_source'] ) );
			        switch ( $data['watermark_ext'] )
				{
			                case 'png':
						$watermark = imagecreatefrompng( $phpbb_root_path . GARAGE_WATERMARK_PATH . $garage_config['watermark_source']);
						imageAlphaBlending($watermark, false);
						imageSaveAlpha($watermark, true);
					break;

			                default:
						$watermark = false;
					break;
			        }

			        if ( $watermark )
			        {
					$data['width'] = $garage_image->get_image_width($data['attach_location']);
					$data['height'] = $garage_image->get_image_height($data['attach_location']);
			                $data['watermark_width'] = imagesx($watermark);
					$data['watermark_height'] = imagesy($watermark);
					$data['dest_x'] = $data['width'] - $data['watermark_width'] - 5;  
					$data['dest_y'] = $data['height'] - $data['watermark_height'] - 5; 

                			switch ( $data['attach_ext'] )
			                {
			                        case '.png':
			                                $source = imagecreatefrompng($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
			                                break;
			                        case '.gif':
			                                $source = imagecreatefromgif($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
			                                break;
			                        case '.jpg':
			                        case '.jpeg':
			                                $source = imagecreatefromjpeg($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
			                                break;
			                        default:
			                                $img_src = false;
			                }

                			if ( $source )
					{
			                        imagecopymerge($source, $watermark, $data['dest_x'], $data['dest_y'], 0, 0, $data['watermark_width'], $data['watermark_height'], 60);
			                        $watermark_ok = 1;
			                }
			        }
			}

			//Lets Display The Watermarked Image
			if ($watermark_ok)
			{
			        switch ( $data['attach_ext'] )
			        {
			                case '.gif':
			                case '.png':
			                        header('Content-type: image/png');
						imagepng($source);
						imagedestroy($source);  
						imagedestroy($watermark);
					break;

					case '.jpg':
					case '.jpeg':
			                        header('Content-type: image/jpeg');
			                        imagejpeg($source);
						imagedestroy($source);  
						imagedestroy($watermark);
					break;

			                default:
						trigger_error('UNSUPPORTED_FILE_TYPE');
					break;
			        }
			}
			//Looks Like We Need To Just Show The Original Image
			else
			{
			
			        switch ( $data['attach_ext'] )
			        {
			                case '.png':
			                        header('Content-type: image/png');
			                        break;
			                case '.gif':
			                        header('Content-type: image/gif');
			                        break;
			                case '.jpg':
			                        header('Content-type: image/jpeg');
			                        break;
			                default:
						trigger_error('UNSUPPORTED_FILE_TYPE');
			        }
				readfile($phpbb_root_path . GARAGE_UPLOAD_PATH . $data['attach_location']);
			}
        		exit;
		}

	/**
	* View all iamges contained in the garage
	*/
	case 'view_all_images':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Required Values To Defaults If They Are Empty
		$start = (empty($start)) ? '0' : $start;

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_images.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['IMAGES'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_all_images"))
		);

		//Pull Required Image Data From DB
		$data = $garage_image->get_all_images($start, '100');

		//Process Each Image
		for ($i = 0, $count = sizeof($data); $i < $count; $i++)
		{
			//Produce Actual Image Thumbnail And Link It To Full Size Version..
			if (($data[$i]['attach_id']) AND ($data[$i]['attach_is_image']) AND (!empty($data[$i]['attach_thumb_location'])) AND (!empty($data[$i]['attach_location'])))
			{
				$template->assign_block_vars('pic_row', array(
					'U_VIEW_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" .$data[$i]['user_id']),
					'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" .$data[$i]['garage_id']),
					'U_IMAGE'		=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $data[$i]['attach_id']),
					'IMAGE_TITLE'		=> $data[$i]['attach_file'],
					'IMAGE'			=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $data[$i]['attach_thumb_location'],
					'VEHICLE' 		=> $data[$i]['vehicle'],
					'USERNAME' 		=> $data[$i]['username'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $data[$i]['user_id'], $data[$i]['username'], $data[$i]['user_colour']),
				));
			}
			//Cleanup For Next Image
			$thumb_image = '';
			$image = '';
		}

		$template->assign_vars(array(
			'S_MODE_ACTION' => append_sid("garage.$phpEx?mode=view_all_images"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();		

		break;

	/**
	* Insurer review page
	*/
	case 'insurance_review':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_insurance_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params		= array('start' => 0);
		$data 		= $garage->process_vars($params);
		$data['where']	= (!empty($bid)) ? "AND b.id = $bid" : '';

		//Get All Insurance Business Data
		$business = $garage_business->get_insurance_business($data['where'], $data['start']);

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insurance_review"),
			'FORUM_NAME' 	=> $user->lang['INSURANCE_SUMMARY'])
		);

		//Display Correct Breadcrumb Links..
		if (!empty($bid))
		{
			$template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insurance_review&amp;BID=" . $business[0]['id']),
				'FORUM_NAME' 	=> $business[0]['title'])
			);
		}

      		//Loop Processing All Insurance Business's Returned From First Select
		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
            			'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insurance_review&amp;BID=" . $business[$i]['id']),
            			'TITLE' 		=> $business[$i]['title'],
	            		'ADDRESS' 		=> $business[$i]['address'],
        	    		'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
	            		'EMAIL' 		=> $business[$i]['email'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
			);

			//Setup Template Block For Detail Being Displayed...
			$detail = (empty($bid)) ? 'business_row.more_detail' : 'business_row.insurance_detail';
        	 	$template->assign_block_vars($detail, array());

			//Now Loop Through All Insurance Cover Types...
			$cover_types = array($user->lang['THIRD_PARTY'], $user->lang['THIRD_PARTY_FIRE_THEFT'], $user->lang['COMPREHENSIVE'], $user->lang['COMPREHENSIVE_CLASSIC'], $user->lang['COMPREHENSIVE_REDUCED']);
			for($j = 0, $count2 = sizeof($cover_types);$j < $count2; $j++)
			{
				//Pull MIN/MAX/AVG Of Specific Cover Type By Business ID
				$premium_data = $garage_insurance->get_premiums_stats_by_business_and_covertype($business[$i]['id'], $cover_types[$j]);
        	    		$template->assign_block_vars('business_row.cover_row', array(
               				'COVER_TYPE'	=> $cover_types[$j],
               				'MINIMUM' 	=> $premium_data['min'],
               				'AVERAGE' 	=> $premium_data['avg'],
               				'MAXIMUM' 	=> $premium_data['max'])
	            		);
			}
			
			//If Display Single Insurance Company We Then Need To Get All Premium Data
			if  (!empty($bid))
			{
				//Pull All Insurance Premiums Data For Specific Insurance Company
				$insurance_data = $garage_insurance->get_all_premiums_by_business($business[$i]['id']);
				for($k = 0, $count3 = sizeof($insurance_data);$k < $count3; $k++)
				{
					$template->assign_block_vars('business_row.insurance_detail.premiums', array(
						'U_VIEW_PROFILE'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $insurance_data[$k]['user_id']),
						'U_VIEW_VEHICLE'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $insurance_data[$k]['vehicle_id']),
						'USERNAME'		=> $insurance_data[$k]['username'],
						'USERNAME_COLOUR'	=> get_username_string('colour', $insurance_data[$i]['user_id'], $insurance_data[$i]['username'], $insurance_data[$i]['user_colour']),
						'VEHICLE' 		=> $insurance_data[$k]['vehicle'],
						'PREMIUM' 		=> $insurance_data[$k]['premium'],
						'COVER_TYPE' 		=> $insurance_data[$k]['cover_type'])
					);
				}
			}
      		}


		if  (empty($bid))
		{
		// Get Insurance Business Data For Pagination
		$count = $garage_business->count_insurance_business_data($data['where']);
		$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}"), $count, $garage_config['cars_per_page'], $start);
		$template->assign_vars(array(
			'PAGINATION' 			=> $pagination,
			'PAGE_NUMBER' 			=> on_page($count, $garage_config['cars_per_page'], $start),
			'TOTAL_BUSINESS'		=> ($count == 1) ? $user->lang['VIEW_BUSINESS'] : sprintf($user->lang['VIEW_BUSINESS\'S'], $count),
			'S_INSURANCE_REVIEW_TAB_ACTIVE'	=> true,
		));
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Garage review page
	*/
	case 'garage_review':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'=> 'garage_header.html',
			'body' 	=> 'garage_view_garage_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('start' => 0);
		$data = $garage->process_vars($params);
		$data['where'] = (!empty($bid)) ? " AND b.id = $bid" : '';

		//Get Required Garage Business Data
		$business = $garage_business->get_garage_business($data['where'], $data['start']);

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review"),
			'FORUM_NAME' 	=> $user->lang['GARAGE_REVIEW'])
		);

		//Setup Breadcrumb Trail Correctly...
		if (!empty($bid))
		{
			$template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review&amp;BID=" . $business[0]['id']),
				'FORUM_NAME' 	=> $business[0]['title'])
			);
		}

      		//Process All Garages......
      		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=garage_review&amp;BID=" . $business[$i]['id']),
				'RATING' 		=> (empty($business[$i]['rating'])) ? 0 : $business[$i]['rating'],
            			'TITLE' 		=> $business[$i]['title'],
            			'ADDRESS' 		=> $business[$i]['address'],
            			'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
            			'EMAIL' 		=> $business[$i]['email'],
				'MAX_RATING' 		=> $business[$i]['total_rating'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());

			if (empty($bid))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get Mods Business Has Installed & Services Performed
			$bus_mod_data = $garage_modification->get_modifications_by_install_business($business[$i]['id']);
			$bus_srv_data = $garage_service->get_services_by_business($business[$i]['id']);

			$comments = null;
			for($j = 0, $count2 = sizeof($bus_mod_data);$j < $count2; $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' 	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $bus_mod_data[$j]['vehicle_id']),
					'U_VIEW_ITEM'		=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=view_modification&amp;VID=" . $bus_mod_data[$j]['vehicle_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'USERNAME' 		=> $bus_mod_data[$j]['username'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $bus_mod_data[$j]['user_id'], $bus_mod_data[$j]['username'], $bus_mod_data[$j]['user_colour']),
					'VEHICLE' 		=> $bus_mod_data[$j]['vehicle'],
					'ITEM' 			=> $bus_mod_data[$j]['mod_title'],
					'RATING' 		=> $bus_mod_data[$j]['install_rating'])
				);

				//Setup Comments For Installation Of Modification...	
				if (!empty($bus_mod_data[$j]['install_comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $bus_mod_data[$j]['username'] . ' -> ' . $bus_mod_data[$j]['install_comments'])
					);
				}
			}
			for($j = 0, $count3 = sizeof($bus_srv_data);$j < $count3; $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $bus_srv_data[$j]['user_id']),
					'U_VIEW_VEHICLE' 	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $bus_srv_data[$j]['vehicle_id']),
					'USERNAME' 		=> $bus_srv_data[$j]['username'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $bus_srv_data[$j]['user_id'], $bus_srv_data[$j]['username'], $bus_srv_data[$j]['user_colour']),
					'VEHICLE' 		=> $bus_srv_data[$j]['vehicle'],
					'ITEM' 			=> $garage_service->get_service_type($bus_srv_data[$j]['type_id']),
					'RATING' 		=> $bus_srv_data[$j]['rating'])
				);
			}

			//Reset Comments For Next Business..
			$comments = '';
		}

		if  (empty($bid))
		{
		//Get Count & Perform Pagination...
		$count = $garage_business->count_garage_business_data($data['where']);
		$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}"), $count, $garage_config['cars_per_page'], $start);
		$template->assign_vars(array(
			'PAGINATION' 			=> $pagination,
			'PAGE_NUMBER' 			=> on_page($count, $garage_config['cars_per_page'], $start),
			'TOTAL_BUSINESS'		=> ($count == 1) ? $user->lang['VIEW_BUSINESS'] : sprintf($user->lang['VIEW_BUSINESS\'S'], $count),
			'S_GARAGE_REVIEW_TAB_ACTIVE'	=> true,
		));
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Shop review page
	*/
	case 'shop_review':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'=> 'garage_header.html',
			'body' 	=> 'garage_view_shop_business.html')
		);

		//Get All Data Posted And Make It Safe To Use
		$params		= array('start' => 0);
		$data 		= $garage->process_vars($params);
		$data['where']	= (!empty($bid)) ? " AND b.id = $bid" : '';

		//Get Required Shop Business Data
		$business = $garage_business->get_shop_business($data['where'], $data['start']);

		//Build Page Header ;)
		page_header($page_title);

		//Display Correct Breadcrumb Links..
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review"),
			'FORUM_NAME' 	=> $user->lang['SHOP_REVIEW'])
		);

		//Display Correct Breadcrumb Links..
		if (!empty($bid))
		{
			$template->assign_block_vars('navlinks', array(
				'U_VIEW_FORUM' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review&amp;BID=" . $business[0]['id']),
				'FORUM_NAME'	=> $business[0]['title'])
			);
		}

      		//Process All Shops......
      		for ($i = 0, $count = sizeof($business);$i < $count; $i++)
      		{
         		$template->assign_block_vars('business_row', array(
				'U_VIEW_BUSINESS'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=shop_review&amp;BID=" . $business[$i]['id']),
				'RATING' 		=> (empty($business[$i]['rating'])) ? 0 : $business[$i]['rating'],
            			'TITLE' 		=> $business[$i]['title'],
            			'ADDRESS' 		=> $business[$i]['address'],
            			'TELEPHONE' 		=> $business[$i]['telephone'],
            			'FAX' 			=> $business[$i]['fax'],
            			'WEBSITE' 		=> $business[$i]['website'],
            			'EMAIL' 		=> $business[$i]['email'],
				'MAX_RATING' 		=> $business[$i]['total_rating'],
				'OPENING_HOURS' 	=> $business[$i]['opening_hours'])
         		);
			$template->assign_block_vars('business_row.customers', array());
			
			if (empty($bid))
			{
         			$template->assign_block_vars('business_row.more_detail', array());
			}

			//Now Lets Go Get All Mods All Business's Have Sold
			$bus_mod_data = $garage_modification->get_modifications_by_business($business[$i]['id']);

			$comments = null;
			for ($j = 0, $count2 = sizeof($bus_mod_data);$j < $count2; $j++)
			{
				$template->assign_block_vars('business_row.mod_row', array(
					'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $bus_mod_data[$j]['user_id']),
					'U_VIEW_VEHICLE' 	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_vehicle&amp;VID=" . $bus_mod_data[$j]['vehicle_id']),
					'U_VIEW_MODIFICATION'	=> append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=view_modification&amp;VID=" . $bus_mod_data[$j]['vehicle_id'] . "&amp;MID=" . $bus_mod_data[$j]['id']),
					'USERNAME' 		=> $bus_mod_data[$j]['username'],
					'USERNAME_COLOUR'	=> get_username_string('colour', $bus_mod_data[$j]['user_id'], $bus_mod_data[$j]['username'], $bus_mod_data[$j]['user_colour']),
					'VEHICLE' 		=> $bus_mod_data[$j]['vehicle'],
					'MODIFICATION' 		=> $bus_mod_data[$j]['mod_title'],
					'PURCHASE_RATING' 	=> $bus_mod_data[$j]['purchase_rating'],
					'PRODUCT_RATING' 	=> $bus_mod_data[$j]['product_rating'],
					'PRICE' 		=> $bus_mod_data[$j]['price'])
				);
					
				if (!empty($bus_mod_data[$j]['comments']))
				{
					if ( $comments != 'SET')
					{
						$template->assign_block_vars('business_row.comments', array());
					}
					$comments = 'SET';
					$template->assign_block_vars('business_row.customer_comments', array(
						'COMMENTS' => $bus_mod_data[$j]['username'] . ' -> ' . $bus_mod_data[$j]['comments'])
					);
				}
			}

			//Reset Comments For Next Business..
			$comments = '';
		}

		if  (empty($bid))
		{
		//Get Count & Perform Pagination...
		$count = $garage_business->count_shop_business_data($data['where']);
		$pagination = generate_pagination(append_sid("{$phpbb_root_path}garage.php", "mode={$mode}"), $count, $garage_config['cars_per_page'], $start);
		$template->assign_vars(array(
			'PAGINATION' 			=> $pagination,
			'PAGE_NUMBER' 			=> on_page($count, $garage_config['cars_per_page'], $start),
			'TOTAL_BUSINESS'		=> ($count == 1) ? $user->lang['VIEW_BUSINESS'] : sprintf($user->lang['VIEW_BUSINESS\'S'], $count),
			'S_SHOP_REVIEW_TAB_ACTIVE'	=> true,
		));
		}

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Page allowing users to submit new business's
	*/
	case 'user_submit_business':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_business");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_business'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_business.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_NEW_BUSINESS'])
		);

		//Get All Data Posted And Make It Safe To Use
		$params = array('BUSINESS' => '', 'redirect' => '');
		$data = $garage->process_vars($params);

		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['ADD_NEW_BUSINESS'],
			'L_BUTTON'		=> $user->lang['ADD_NEW_BUSINESS'],
			'VID' 			=> $vid,
			'REDIRECT'		=> $data['redirect'],
			'S_DISPLAY_PENDING' 	=> $garage_config['enable_business_approval'],
			'S_BUSINESS_INSURANCE' 	=> ($data['BUSINESS'] == BUSINESS_INSURANCE) ? true : false,
			'S_BUSINESS_GARAGE' 	=> ($data['BUSINESS'] == BUSINESS_GARAGE) ? true : false,
			'S_BUSINESS_RETAIL' 	=> ($data['BUSINESS'] == BUSINESS_RETAIL) ? true : false,
			'S_BUSINESS_PRODUCT' 	=> ($data['BUSINESS'] == BUSINESS_PRODUCT) ? true : false,
			'S_BUSINESS_DYNOCENTRE' => ($data['BUSINESS'] == BUSINESS_DYNOCENTRE) ? true : false,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=insert_business"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Insert new business into database
	*/
	case 'insert_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_business'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('redirect' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'product' => '', 'insurance' => '', 'garage' => '', 'retail' => '', 'dynocentre' => '');
		$data 	= $garage->process_vars($params);
		$params = array('title' => '', 'address' => '', 'opening_hours' => '');
		$data 	+= $garage->process_mb_vars($params);

		//Check They Entered http:// In The Front Of The Link
		if ((!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])))
		{
			$data['website'] = "http://".$data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('title');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ($garage_config['enable_business_approval'])
		{
			//Perform Any Pending Notifications Requried
			$garage->pending_notification('unapproved_business');
		}

		//Create The Business Now...
		$garage_business->insert_business($data);

		//Send Them Back To Whatever Page Them Came From..Now With Their Required Business :)
		if ($data['redirect'] == 'add_modification' || $data['redirect'] == 'edit_modification')
		{
			redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=" . $data['redirect'] . "&amp;VID=$vid"));
		}
		else if ($data['redirect'] == 'add_dynorun' || $data['redirect'] == 'edit_dynorun')
		{
			redirect(append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=" . $data['redirect'] . "&amp;VID=$vid"));
		}
		elseif ($data['redirect'] == 'add_premium' || $data['redirect'] == 'edit_premium')
		{
			redirect(append_sid("{$phpbb_root_path}garage_premium.$phpEx", "mode=" . $data['redirect'] . "&amp;VID=$vid"));
		}

		break;

	/**
	* Page allowing users to edit business's
	* 
	* @todo Move to MCP?
	*/
	case 'edit_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage_edit'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_business.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_BUSINESS'])
		);

		//Pull Required Business Data From DB
		$data = $garage_business->get_business($bus_id);

		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['EDIT_BUSINESS'],
			'L_BUTTON' 		=> $user->lang['EDIT_BUSINESS'],
			'TITLE' 		=> $data['title'],
			'ADDRESS'		=> $data['address'],
			'TELEPHONE'		=> $data['telephone'],
			'FAX'			=> $data['fax'],
			'WEBSITE'		=> $data['website'],
			'EMAIL'			=> $data['email'],
			'OPENING_HOURS'		=> $data['opening_hours'],
			'BUSINESS_ID'		=> $data['id'],
			'S_DISPLAY_PENDING' 	=> $garage_config['enable_business_approval'],
			'S_BUSINESS_INSURANCE' 	=> ($data['insurance']) ? true : false,
			'S_BUSINESS_GARAGE' 	=> ($data['garage']) ? true : false,
			'S_BUSINESS_RETAIL' 	=> ($data['retail']) ? true : false,
			'S_BUSINESS_PRODUCT' 	=> ($data['product']) ? true : false,
			'S_BUSINESS_DYNOCENTRE'	=> ($data['dynocentre']) ? true : false,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=update_business"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Update business
	*/
	case 'update_business':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('m_garage_edit'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('id' => '', 'telephone' => '', 'fax' => '', 'website' => '', 'email' => '', 'product' => '', 'insurance' => '', 'garage' => '', 'retail' => '', 'dynocentre' => '');
		$data 	= $garage->process_vars($params);
		$params = array('title' => '', 'address' => '', 'opening_hours' => '');
		$data 	+= $garage->process_mb_vars($params);

		//Check They Entered http:// In The Front Of The Link
		if ( (!preg_match( "/^http:\/\//i", $data['website'])) AND (!empty($data['website'])) )
		{
			$data['website'] = "http://" . $data['website'];
		}

		//Checks All Required Data Is Present
		$params = array('title');
		$garage->check_required_vars($params);

		//Update The Business With Data Acquired
		$garage_business->update_business($data);

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_business"));

		break;

	/**
	* Page allowing users to submit new makes
	*/
	case 'user_submit_make':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_make");
		}

		//Check This Feature Is Enabled
		if (!$garage_config['enable_user_submit_make'] || !$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('made_year' => '');
		$data = $garage->process_vars($params);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_make.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_MAKE'])
		);

		$template->assign_vars(array(
			'YEAR' 			 => $data['made_year'],
			'S_GARAGE_MODELS_ACTION' => append_sid("{$phpbb_root_path}admin_garage_models.$phpEx"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_make':

		//User Is Annoymous...So Not Allowed To Create A Vehicle
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_make");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('made_year' => '');
		$data = $garage->process_vars($params);
		$params = array('make' => '');
		$data += $garage->process_mb_vars($params);

		//Checks All Required Data Is Present
		$params = array('make', 'made_year');
		$garage->check_required_vars($params);

		//Check Make Does Not Already Exist
		if ($garage_model->count_make($data['make']) > 0)
		{
			redirect(append_sid("garage.$phpEx?mode=error&amp;EID=27", true));
		}

		//Create The Make
		$make_id = $garage_model->insert_make($data);

		//Perform Any Pending Notifications Requried
		$garage->pending_notification('unapproved_makes');

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_vehicle&amp;MAKE_ID=$make_id&amp;YEAR=" . $data['made_year']));

		break;

	/**
	* Page allowing users to submit new models
	*/
	case 'user_submit_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_model");
		}

		//Check This Feature Is Enabled & User Authorised
		if (!$garage_config['enable_user_submit_model'] || !$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('make_id' => '', 'made_year' => '');
		$data = $garage->process_vars($params);
		$year = $data['made_year'];

		//Check If User Owns Vehicle
		if (empty($data['make_id']))
		{
			redirect(append_sid("garage.$phpEx?mode=error&amp;EID=23", true));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_model.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_MODEL'])
		);

		//Checks All Required Data Is Present
		$params = array('make_id');
		$garage->check_required_vars($params);

		//Pull Required Make Data From DB
		$data = $garage_model->get_make($data['make_id']);

		$template->assign_vars(array(
			'YEAR' 		=> $year,
			'MAKE_ID' 	=> $data['id'],
			'MAKE' 		=> $data['make'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	/**
	* Page allowing users to submit new modification products
	*/
	case 'user_submit_product':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ( $user->data['user_id'] == ANONYMOUS )
		{
			login_box("garage.$phpEx?mode=user_submit_product");
		}

		//Check This Feature Is Enabled & User Authorised
		if (!$garage_config['enable_user_submit_product'] || !$auth->acl_get('u_garage_add_product'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('category_id' => '', 'manufacturer_id' => '', 'VID' => '');
		$data = $garage->process_vars($params);
		$params = array('category_id', 'manufacturer_id', 'VID');
		$garage->check_required_vars($params);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_user_submit_product.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_PRODUCT'])
		);

		$category = $garage->get_category($data['category_id']);
		$manufacturer = $garage_business->get_business($data['manufacturer_id']);

		$template->assign_vars(array(
			'VID' 			=> $data['VID'],
			'CATEGORY_ID' 		=> $data['category_id'],
			'MANUFACTURER_ID'	=> $data['manufacturer_id'],
			'CATEGORY' 		=> $category['title'],
			'MANUFACTURER' 		=> $manufacturer['title'])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_model':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage.$phpEx?mode=user_submit_model");
		}

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_make_model'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('make_id' => '', 'made_year' => '');
		$data = $garage->process_vars($params);
		$params = array('make' => '', 'model' => '');
		$data += $garage->process_mb_vars($params);

		//Checks All Required Data Is Present
		$params = array('make', 'make_id', 'model');
		$garage->check_required_vars($params);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		$garage->pending_notification('unapproved_models');

		//Create The Model
		$model_id = $garage_model->insert_model($data);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_vehicle&amp;MAKE_ID=" . $data['make_id'] . "&amp;MODEL_ID=$model_id&amp;YEAR=" . $data['made_year']));

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
		$params = array('category_id' => '', 'manufacturer_id' => '', 'vehicle_id' => '');
		$data = $garage->process_vars($params);
		$params = array('title' => '');
		$data += $garage->process_mb_vars($params);

		//Checks All Required Data Is Present
		$params = array('title', 'category_id', 'manufacturer_id', 'vehicle_id');
		$garage->check_required_vars($params);

		//If Needed Perform Notifications If Configured
		if ($garage_config['enable_product_approval'])
		{
			$garage->pending_notification('unapproved_products');
		}

		//Create The Product
		$data['product_id'] = $garage_modification->insert_product($data);

		//Head Back To Page Updating Dropdowns With New Item ;)
		redirect(append_sid("{$phpbb_root_path}garage_modification.$phpEx", "mode=add_modification&amp;VID=".$data['vehicle_id']."&amp;category_id=" . $data['category_id'] . "&amp;manufacturer_id=" . $data['manufacturer_id'] ."&amp;product_id=" . $data['product_id']));

		break;

	/**
	* Page to display an error nicely to the user
	*/
	case 'error':

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_error.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ERROR'])
		);

		$template->assign_vars(array(
			'ERROR_MESSAGE' => $user->lang['GARAGE_ERROR_' . $eid])
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

	break;

	/**
	* Called by AJAX toolkit to build product model options
	*/
	case 'get_model_list':
		//Get All Data Posted And Make It Safe To Use
		$params = array('make_id' => '', 'model_id' => '');
		$data = $garage->process_vars($params);

		echo "obj.options[obj.options.length] = new Option('".$user->lang['SELECT_MODEL']."', '', false, false);\n";
		echo "obj.options[obj.options.length] = new Option('------', '', false, false);\n";

		if (!empty($data['make_id']))
		{
			//Get Models Belonging To This Make
			$models = $garage_model->get_all_models_from_make($data['make_id']);

			//Populate Options For Dropdown
			for ($i = 0, $count = sizeof($models);$i < $count; $i++)
			{
				if ($data['model_id'] == $models[$i]['id'])
				{
					echo "obj.options[obj.options.length] = new Option('".$models[$i]['model']."','".$models[$i]['id']."', true, true);\n";
				}
				else
				{
					echo "obj.options[obj.options.length] = new Option('".$models[$i]['model']."','".$models[$i]['id']."', false, false);\n";
				}
			}
		}
	exit;

	/**
	* Called by AJAX toolkit to build product dropdown options
	*/
	case 'get_product_list':
		//Get All Data Posted And Make It Safe To Use
		$params = array('manufacturer_id' => '' , 'category_id' => '', 'product_id' => '');
		$data = $garage->process_vars($params);

		echo "obj.options[obj.options.length] = new Option('".$user->lang['SELECT_PRODUCT']."', '', false, false);\n";
		echo "obj.options[obj.options.length] = new Option('------', '', false, false);\n";

		if (!empty($data['manufacturer_id']))
		{
			//Get Products Belonging To This Manufacturer With Filtering On Category For Modification Page
			if (!empty($data['category_id']))
				$products = $garage_modification->get_products_by_manufacturer($data['manufacturer_id'], $data['category_id']);
			//Get Products Belonging To This Manufacturer With No Filtering On Category For Search Page
			else
			{
				$products = $garage_modification->get_products_by_manufacturer($data['manufacturer_id']);
			}

			//Populate Options For Dropdown
			for ($i = 0, $count = sizeof($products);$i < $count; $i++)
			{
				if ($data['product_id'] == $products[$i]['id'])
				{
					echo "obj.options[obj.options.length] = new Option('".$products[$i]['title']."','".$products[$i]['id']."', true, true);\n";
				}
				else
				{
					echo "obj.options[obj.options.length] = new Option('".$products[$i]['title']."','".$products[$i]['id']."', false, false);\n";
				}
			}
		}
	exit;

	/**
	* Page to display a users personal vehicles and option to create new one if authorised
	*/
	case 'user_garage':
		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_users_garage.html')
		);

		//Build Navlinks
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['YOUR_GARAGE'])
		);

		$template->assign_vars(array(
			'S_USER_GARAGE_TAB_ACTIVE'	=> true,
		));

		/*$user_vehicles = $garage_vehicle->get_vehicles_by_user($user->data['user_id']);
		for ($i = 0; $i < count($user_vehicles); $i++)
		{
		      	$template->assign_block_vars('user_vehicles', array(
       				'U_VIEW_VEHICLE'=> append_sid("garage_vehicle.$phpEx?mode=view_own_vehicle&amp;VID=" . $user_vehicles[$i]['id']),
       				'VEHICLE' 	=> $user_vehicles[$i]['vehicle'],
			));
		}*/

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();
	break;

	/**
	* Default statistics page
	*/
	default:
		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			//If Not Logged In Send Them To Login & Back, Maybe They Have Permission As A User 
			if ( $user->data['user_id'] == ANONYMOUS )
			{
				login_box("garage.$phpEx");
			}
			//They Are Logged In But Not Allowed So Error Nicely Now...
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Display Page...In Order Header->Menu->Body->Footer
		$garage_template->sidemenu();

		//Display If Needed Featured Vehicle
		$garage_vehicle->show_featuredvehicle();
		
		$required_position = 1;
		//Display All Boxes Required
		$garage_vehicle->show_newest_vehicles();
		$garage_vehicle->show_updated_vehicles();
		$garage_modification->show_newest_modifications();
		$garage_modification->show_updated_modifications();
		$garage_modification->show_most_modified();
		$garage_vehicle->show_most_spent();
		$garage_vehicle->show_most_viewed();
		$garage_guestbook->show_lastcommented();
		$garage_quartermile->show_topquartermile();
		$garage_dynorun->show_topdynorun();
		$garage_vehicle->show_toprated();
		$garage_track->show_toplap();

		//Show Top Rated Month Vehicle
		$garage_vehicle->show_month_toprated_vehicle();

		$template->assign_vars(array(
			'S_INDEX_COLUMNS' 	=> ($garage_config['enable_user_index_columns'] && ($user->data['user_garage_index_columns'] != $garage_config['index_columns'])) ? $user->data['user_garage_index_columns'] : $garage_config['index_columns'],
			'S_MAIN_TAB_ACTIVE'	=> true,
			'TOTAL_VEHICLES' 	=> $garage_vehicle->count_total_vehicles(),
			'TOTAL_VIEWS' 		=> $garage->count_total_views(),
			'TOTAL_MODIFICATIONS' 	=> $garage_modification->count_total_modifications(),
			'TOTAL_COMMENTS'  	=> $garage_guestbook->count_total_comments(),
		));

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header'	=> 'garage_header.html',
			'menu' 		=> 'garage_menu.html',
			'body' 		=> 'garage.html',
		));
	break;
}

$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>
