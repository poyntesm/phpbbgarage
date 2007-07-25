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
$phpbb_root_path = './';
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
require($phpbb_root_path . 'includes/mods/class_garage_insurance.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_model.' . $phpEx);

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$vid = request_var('VID', '');
$ins_id = request_var('INS_ID', '');

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
	* Display page to create new premium
	*/
	case 'add_premium':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_insurance'] || !$auth->acl_get('u_garage_add_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get insurer & vehicle data from DB
		*/
		$insurance_business 	= $garage_business->get_business_by_type(BUSINESS_INSURANCE);
		$vehicle 		= $garage_vehicle->get_vehicle($vid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_PREMIUM'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_premium&amp;VID=$vid"))
		);
		$garage_template->insurance_dropdown($insurance_business);
		$garage_template->cover_dropdown();
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['ADD_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['ADD_PREMIUM'],
			'CURRENCY'		=> $vehicle['currency'],
			'U_SUBMIT_BUSINESS' 	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_premium&amp;BUSINESS=" . BUSINESS_INSURANCE),
			'VID' 			=> $vid,
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_premium.$phpEx", "mode=insert_premium"))
		);
		$garage_template->sidemenu();
	break;

	/**
	* Insert new premium
	*/
	case 'insert_premium':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_insurance'] || !$auth->acl_get('u_garage_add_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '');
		$data 	= $garage->process_vars($params);
		$params = array('comments' => '');
		$data 	+= $garage->process_mb_vars($params);
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to create premium
		*/
		$garage_insurance->insert_premium($data);

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
	* Display page to edit an existing premium
	*/
	case 'edit_premium':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_premium.$phpEx?mode=edit_premium&amp;INS_ID=$ins_id&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get premium, insurer & vehicle data from DB
		*/
		$data = $garage_insurance->get_premium($ins_id);
		$insurance_business = $garage_business->get_business_by_type(BUSINESS_INSURANCE);
		$vehicle_data 	= $garage_vehicle->get_vehicle($vid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_insurance.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle_data['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_PREMIUM'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID=$vid&amp;INS_ID=$ins_id"))
		);
		$garage_template->insurance_dropdown($insurance_business, $data['business_id']);
		$garage_template->cover_dropdown($data['cover_type']);
		$template->assign_vars(array(
			'L_TITLE' 		=> $user->lang['EDIT_PREMIUM'],
			'L_BUTTON' 		=> $user->lang['EDIT_PREMIUM'],
			'CURRENCY'		=> $vehicle_data['currency'],
			'INS_ID' 		=> $ins_id,
			'VID' 			=> $vid,
			'PREMIUM' 		=> $data['premium'],
			'COMMENTS' 		=> $data['comments'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_premium.$phpEx", "mode=update_premium"))
		);
		$garage_template->sidemenu();
	break;

	/**
	* Update existing premium
	*/
	case 'update_premium':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('business_id' => '', 'premium' => '', 'cover_type' => '');
		$data = $garage->process_vars($params);
		$params = array('comments' => '');
		$data += $garage->process_mb_vars($params);
		$params = array('business_id', 'premium', 'cover_type');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to update premium
		*/
		$garage_insurance->update_premium($data);

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
	* Delete existing premium
	*/
	case 'delete_premium':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_delete_insurance'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete premium
		*/
		$garage_insurance->delete_premium($ins_id);

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
}
$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>
