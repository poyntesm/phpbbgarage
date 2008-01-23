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
* Build All Garage Classes e.g $garage_images->
*/
require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_dynorun.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_image.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_quartermile.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$vid = request_var('VID', '');
$did = request_var('DID', '');
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
	* Display page to create new dynorun
	*/
	case 'add_dynorun':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_dynorun.$phpEx?mode=add_dynorun&amp;VID=$vid");
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$garage_config['enable_dynorun'] || !$auth->acl_get('u_garage_add_dynorun'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get dynocentres & vehicle data from DB
		*/
		$dynocentres = $garage_business->get_business_by_type(BUSINESS_DYNOCENTRE);
		$vehicle = $garage_vehicle->get_vehicle($vid);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_dynorun.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['ADD_DYNORUN'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=add_dynorun&amp;VID=$vid"))
		);
		$garage_template->attach_image('dynorun');
		$garage_template->nitrous_dropdown();
		$garage_template->power_dropdown('bhp_unit');
		$garage_template->power_dropdown('torque_unit');
		$garage_template->boost_dropdown();
		$garage_template->dynocentre_dropdown($dynocentres);
		$template->assign_vars(array(
			'L_TITLE'  			=> $user->lang['ADD_NEW_RUN'],
			'L_BUTTON'  			=> $user->lang['ADD_NEW_RUN'],
			'U_SUBMIT_BUSINESS_DYNOCENTRE'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=user_submit_business&amp;VID=$vid&amp;redirect=add_dynorun&amp;BUSINESS=" . BUSINESS_DYNOCENTRE ),
			'VID' 				=> $vid,
			'S_MODE_ACTION' 		=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=insert_dynorun"))
         	);
		$garage_template->sidemenu();
	break;

	/**
	* Insert new dynorun
	*/
	case 'insert_dynorun':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/		
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_dynorun.$phpEx?mode=add_dynorun&amp;VID=$vid");
		}

		//Let Check That Rollingroad Runs Are Allowed...If Not Redirect
		if (!$garage_config['enable_dynorun'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=18"));
		}

		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_add_dynorun'))
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
		$params = array('dynocentre_id' => '', 'bhp' => '', 'bhp_unit' => '', 'torque' => 0, 'torque_unit' => '', 'boost' => 0, 'boost_unit' => 'PSI', 'nitrous' => 0, 'peakpoint' => 0);
		$data 	= $garage->process_vars($params);
		$params = array('dynocentre_id', 'bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to create dynorun
		*/
		$did = $garage_dynorun->insert_dynorun($data);

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
				$image_id = $garage_image->process_image_attached('dynorun', $did);
				$hilite = $garage_dynorun->hilite_exists($vid, $did);
				$garage_image->insert_dynorun_gallery_image($image_id, $hilite);
			}
			else if ($garage_image->above_image_quotas())
			{
				redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=4"));
			}
		}
		else if (($garage_config['enable_dynorun_image_required'] == '1') AND ($data['bhp'] >= $garage_config['dynorun_image_required_limit']))
		{
			$garage_dynorun->delete_dynorun($did);
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=26"));
		}

		/**
		* Perform notification if required
		*/
		if ($garage_config['enable_dynorun_approval'])
		{
			$garage->pending_notification('unapproved_dynoruns');
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Display page to edit existing dynorun
	*/
	case 'edit_dynorun':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_dynorun.$phpEx?mode=edit_dynorun&amp;DID=$did&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get vehicle, dynorun, dynocentres & gallery data from DB
		*/
		$vehicle	= $garage_vehicle->get_vehicle($vid);
		$data		= $garage_dynorun->get_dynorun($did);
		$dynocentres 	= $garage_business->get_business_by_type(BUSINESS_DYNOCENTRE);
		$gallery_data 	= $garage_image->get_dynorun_gallery($did);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_dynorun.html')
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $vehicle['vehicle'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"))
		);
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['EDIT_DYNORUN'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=edit_vehicle&amp;VID=$vid&amp;DID=$did"))
		);
		$garage_template->attach_image('dynorun');
		$garage_template->nitrous_dropdown($data['nitrous']);
		$garage_template->power_dropdown('bhp_unit', $data['bhp_unit']);
		$garage_template->power_dropdown('torque_unit', $data['torque_unit']);
		$garage_template->boost_dropdown($data['boost_unit']);
		$garage_template->dynocentre_dropdown($dynocentres, $data['dynocentre_id']);
		$template->assign_vars(array(
			'L_TITLE'  		=> $user->lang['EDIT_RUN'],
			'L_BUTTON'  		=> $user->lang['EDIT_RUN'],
			'U_EDIT_DATA' 		=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID=$did"),
			'U_MANAGE_GALLERY' 	=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID=$did#images"),
			'VID' 			=> $vid,
			'DID' 			=> $did,
			'BHP' 			=> $data['bhp'],
			'TORQUE' 		=> $data['torque'],
			'BOOST' 		=> $data['boost'],
			'NITROUS' 		=> $data['nitrous'],
			'PEAKPOINT' 		=> $data['peakpoint'],
			'PENDING_REDIRECT'	=> request_var('PENDING', ''),
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=update_dynorun"),
			'S_IMAGE_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=insert_dynorun_image"),
		));
		for ($i = 0, $count = sizeof($gallery_data);$i < $count; $i++)
		{
			$template->assign_block_vars('pic_row', array(
				'U_IMAGE'	=> (($gallery_data[$i]['attach_id']) AND ($gallery_data[$i]['attach_is_image']) AND (!empty($gallery_data[$i]['attach_thumb_location'])) AND (!empty($gallery_data[$i]['attach_location']))) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_image&amp;image_id=" . $gallery_data[$i]['attach_id']) : '',
				'U_REMOVE_IMAGE'=> append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=remove_dynorun_image&amp;&amp;VID=$vid&amp;DID=$did&amp;image_id=" . $gallery_data[$i]['attach_id']),
				'U_SET_HILITE'	=> ($gallery_data[$i]['hilite'] == 0) ? append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=set_dynorun_hilite&amp;image_id=" . $gallery_data[$i]['attach_id'] . "&amp;VID=$vid&amp;DID=$did") : '',
				'IMAGE' 	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'],
				'IMAGE_TITLE' 	=> $gallery_data[$i]['attach_file'])
			);
		}
		$garage_template->sidemenu();
	break;

	/**
	* Update existing dynorun
	*/
	case 'update_dynorun':
		/**
		* Check user logged in, else redirecting to login with return address to get them back
		*/
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_dynorun.$phpEx?mode=edit_dynorun&amp;DID=$did&amp;VID=$vid");
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('dynocentre_id' => '', 'bhp' => '', 'bhp_unit' => '', 'torque' => '', 'torque_unit' => '', 'boost' => '', 'boost_unit' => '', 'nitrous' => '', 'peakpoint' => '', 'editupload' => '', 'image_id' => '', 'pending_redirect' => '');
		$data 	= $garage->process_vars($params);
		$params = array('dynocentre_id', 'bhp', 'bhp_unit');
		$garage->check_required_vars($params);

		/**
		* Perform required DB work to update dynorun
		*/
		$garage_dynorun->update_dynorun($data);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* Perform notification if required
		*/
		if ($garage_config['enable_dynorun_approval'])
		{
			$garage->pending_notification('unapproved_dynoruns');
		}

		/**
		* If editted by MCP redirect back to MCP
		*/
		if ($data['pending_redirect'] == 'MCP')
		{
			redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_dynoruns"));
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));
	break;

	/**
	* Delete existing dynorun
	*/
	case 'delete_dynorun':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_delete_dynorun'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete dynorun
		*/
		$garage_dynorun->delete_dynorun($did);

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
	* Insert image into dynorun
	*/
	case 'insert_dynorun_image':
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
				$image_id = $garage_image->process_image_attached('dynorun', $did);
				$hilite = $garage_dynorun->hilite_exists($did);
				$garage_image->insert_dynorun_gallery_image($image_id, $hilite);
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
		redirect(append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID=$did#images"));
	break;

	/**
	* Set highlight image for dynorun
	*/
	case 'set_dynorun_hilite':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to set hightlight image
		*/
		$garage->update_single_field(GARAGE_DYNORUN_GALLERY_TABLE, 'hilite', 0, 'dynorun_id', $did);
		$garage->update_single_field(GARAGE_DYNORUN_GALLERY_TABLE, 'hilite', 1, 'image_id', $image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID=$did#images"));
	break;

	/**
	* Delete dynorun image
	*/
	case 'remove_dynorun_image':
		/**
		* Check vehicle ownership, only owners & moderators with correct permissions get past here
		*/
		$garage_vehicle->check_ownership($vid);

		/**
		* Perform required DB work to delete dynorun image
		*/
		$garage_image->delete_dynorun_image($image_id);

		/**
		* Updates timestamp on vehicle, indicating it has been updated.
		* Updated vehicles are displayed on statistics page
		*/
		$garage_vehicle->update_vehicle_time($vid);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_dynorun.$phpEx", "mode=edit_dynorun&amp;VID=$vid&amp;DID=$did#images"));
	break;

	/**
	* Display page to view dynorun
	*/
	case 'view_dynorun':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		/**
		* Get dynorun & gallery data from DB
		*/
		$data = $garage_dynorun->get_dynorun($did);
		$gallery_data = $garage_image->get_dynorun_gallery($did);

		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_dynorun.html')
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

				$template->assign_block_vars('dynorun_image', array(
					'U_IMAGE' 	=> append_sid('garage.'.$phpEx.'?mode=view_image&amp;image_id='. $gallery_data[$i]['attach_id']),
					'IMAGE_NAME'	=> $gallery_data[$i]['attach_file'],
					'IMAGE_SOURCE'	=> $phpbb_root_path . GARAGE_UPLOAD_PATH . $gallery_data[$i]['attach_thumb_location'])
				);
               		} 
	       	}
		$template->assign_vars(array(
			'U_VIEW_PROFILE' 	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $data['user_id']),
			'YEAR' 			=> $data['made_year'],
			'MAKE' 			=> $data['make'],
			'MODEL' 		=> $data['model'],
			'USERNAME' 		=> $data['username'],
			'USERNAME_COLOUR'	=> get_username_string('colour', $data['user_id'], $data['username'], $data['user_colour']),
            		'AVATAR_IMG' 		=> ($user->optionget('viewavatars')) ? get_user_avatar($data['user_avatar'], $data['user_avatar_type'], $data['user_avatar_width'], $data['user_avatar_height']) : '',
            		'DYNOCENTRE' 		=> $data['title'],
            		'BHP' 			=> $data['bhp'],
            		'BHP_UNIT'	 	=> $data['bhp_unit'],
            		'TORQUE' 		=> $data['torque'],
            		'TORQUE_UNIT' 		=> $data['torque_unit'],
            		'NITROUS' 		=> $data['nitrous'],
            		'BOOST' 		=> $data['boost'],
            		'BOOST_UNIT' 		=> $data['boost_unit'],
            		'PEAKPOINT' 		=> $data['peakpoint'],
         	));

		$garage_template->sidemenu();
	break;
}
$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>
