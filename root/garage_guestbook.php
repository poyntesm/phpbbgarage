<?php
/***************************************************************************
 *                              garage_guestbook.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

define('IN_PHPBB', true);

//Let's Set The Root Dir For phpBB And Load Normal phpBB Required Files
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

//Start Session Management
$user->session_begin();
$auth->acl($user->data);

//Setup Lang Files
$user->setup(array('mods/garage'));

//Build All Garage Classes e.g $garage_images->
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

//Set The Page Title
$page_title = $user->lang['GARAGE'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('cid' => 'CID', 'mid' => 'MID', 'did' => 'DID', 'qmid' => 'QMID', 'ins_id' => 'INS_ID', 'eid' => 'EID', 'image_id' => 'image_id', 'comment_id' => 'CMT_ID', 'bus_id' => 'BUS_ID');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Build Inital Navlink...Yes Forum Name!! We Use phpBB3 Standard Navlink Process!!
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['GARAGE'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx"))
);

//Display MCP Link If Authorised
$template->assign_vars(array(
	'U_MCP'	=> ($auth->acl_get('m_garage')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=garage', true, $user->session_id) : '')
);

//Decide What Mode The User Is Doing
switch( $mode )
{
	case 'view_guestbook':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_guestbook.html')
		);

		//Get Vehicle Data
		$vehicle_data = $garage_vehicle->get_vehicle($cid);

		//Get All Comments Data
		$comment_data = $garage_guestbook->get_vehicle_comments($cid);

		for ($i = 0, $count = sizeof($comment_data);$i < $count; $i++)
		{	
			$username = $comment_data[$i]['username'];
			$temp_url = append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['user_id']);
			$poster = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
			$poster_posts = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $user->lang['POSTS'] . ': ' . $comment_data[$i]['user_posts'] : '';
			$poster_from = ( $comment_data[$i]['user_from'] && $comment_data['user_id'] != ANONYMOUS ) ? $user->lang['Location'] . ': ' . $comment_data[$i]['user_from'] : '';
			$garage_id = $comment_data[$i]['garage_id'];
			$poster_car_year = ( $comment_data[$i]['made_year'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['made_year'] : '';
			$poster_car_mark = ( $comment_data[$i]['make'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ?  ' ' . $comment_data[$i]['make'] : '';
			$poster_car_model = ( $comment_data[$i]['model'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['model'] : '';
			$poster_joined = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $user->lang['JOINED'] . ': ' . $user->format_date($comment_data[$i]['user_regdate']) : '';

			$poster_avatar = '';
			if ( $comment_data[$i]['user_avatar'] AND $user->optionget('viewavatars') )
			{
				$avatar_img = '';
				switch( $comment_data[$i]['user_avatar_type'] )
				{
					case AVATAR_UPLOAD:
						$avatar_img = $config['avatar_path'] . '/' . $comment_data[$i]['user_avatar'];
					break;

					case AVATAR_GALLERY:
						$avatar_img = $config['avatar_gallery_path'] . '/' . $comment_data[$i]['user_avatar'];
					break;
				}
				$poster_avatar = '<img src="' . $avatar_img . '" width="' . $comment_data[$i]['user_avatar_width'] . '" height="' . $comment_data[$i]['user_avatar_height'] . '" alt="" />';
			}

			// Handle anon users posting with usernames
			if ( $comment_data[$i]['user_id'] == ANONYMOUS && $comment_data[$i]['post_username'] != '' )
			{
				$poster = $comment_data[$i]['post_username'];
			}

			$profile = '<a href="' . $temp_url . '">' . $user->lang['READ_PROFILE'] . '</a>';

			$temp_url = append_sid("{$phpbb_root_path}privmsg.$phpEx", "mode=post&amp;u=".$comment_data[$i]['user_id']);
			$pm = '<a href="' . $temp_url . '">' . $user->lang['SEND_PRIVATE_MESSAGE'] . '</a>';

			if ( !empty($comment_data[$i]['user_viewemail']) || $auth->acl_get('m_') )
			{
				$email_uri = ( $config['board_email_form'] ) ? append_sid("{$phpbb_root_path}profile.$phpEx", "mode=email&amp;u=" . $comment_data[$i]['user_id']) : 'mailto:' . $comment_data[$i]['user_email'];

				$email = '<a href="' . $email_uri . '">' . $user->lang['SEND_EMAIL'] . '</a>';
			}
			else
			{
				$email_img = '';
				$email = '';
			}

			$www_img = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $user->lang['Visit_website'] . '" title="' . $user->lang['Visit_website'] . '" border="0" /></a>' : '';
			$www = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww">' . $user->lang['Visit_website'] . '</a>' : '';

			$posted = '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx", "mode=viewprofile&amp;u=".$comment_data[$i]['user_id']) . '">' . $comment_data[$i]['username'] . '</a>';
			$posted = $user->format_date($comment_data[$i]['post_date']);

			$post = $comment_data[$i]['post'];

			// Parse message and/or sig for BBCode if reqd
			if ( $config['allow_bbcode'] )
			{
				
				//if ( $comment_data[$i]['bbcode_uid'] != '' )
				//{
				//	$post = ( $config['allow_bbcode'] ) ? bbencode_second_pass($post, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $post);
				//}
			}

			$post = make_clickable($post);

			// Parse smilies
			if ( $config['allow_smilies'] )
			{
				//$post = smilies_text($post);
			}

			// Replace newlines (we use this rather than nl2br because
			// till recently it wasn't XHTML compliant)
			$post = str_replace("\n", "\n<br />\n", $post);

			$edit_img = '';
			$edit = '';
			$delpost_img = '';
			$delpost = '';

		 	if ( $auth->acl_get('m_garage') )
			{
				$edit_img = $user->img('icon_post_edit', 'EDIT_POST');
				$edit = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['EDIT_POST'] . '</a>';
				$delpost_img = $user->img('icon_post_delete', 'DELETE_POST');
				$delpost = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;CID=$cid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['DELETE_POST'] . '</a>';

			}

			$template->assign_block_vars('comments', array(
				'POSTER_NAME' 		=> $poster,
				'POSTER_JOINED' 	=> $poster_joined,
				'POSTER_POSTS' 		=> $poster_posts,
				'POSTER_FROM' 		=> $poster_from,
				'POSTER_CAR_MARK' 	=> $poster_car_mark,
				'POSTER_CAR_MODEL' 	=> $poster_car_model,
				'POSTER_CAR_YEAR' 	=> $poster_car_year,
				'VIEW_POSTER_CARPROFILE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;CID=$garage_id"),
				'POSTER_AVATAR' 	=> $poster_avatar,
				'PROFILE_IMG' 		=> $user->img('icon_user_profile', 'READ_PROFILE'),
				'PROFILE' 		=> $profile,
				'PM_IMG' 		=> $user->img('icon_contact_pm', 'SEND_PRIVATE_MESSAGE'),
				'PM'			=> $pm,
				'EMAIL_IMG'		=> $user->img('icon_contact_email', 'SEND_EMAIL'),
				'EMAIL'			=> $email,
				'WWW_IMG'		=> $www_img,
				'WWW'			=> $www,
				'EDIT_IMG'		=> $edit_img,
				'EDIT' 			=> $edit,
				'DELETE_IMG' 		=> $delpost_img,
				'DELETE' 		=> $delpost,
				'POSTER' 		=> $poster,
				'POSTED' 		=> $posted,
				'POST' 			=> $post)
			);
		}

		$template->assign_vars(array(
			'L_GUESTBOOK_TITLE' 	=> $vehicle_data['username'] . " - " . $vehicle_data['vehicle'] . " " . $user->lang['GUESTBOOK'],
			'CID' 			=> $cid,
			'S_DISPLAY_LEAVE_COMMENT'=> $auth->acl_get('u_garage_comment'),
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=insert_comment&CID=$cid"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'insert_comment':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_comment'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('comments');
		$garage->check_required_vars($params);

		//Insert The Comment Into Vehicle Guestbook
		$garage_guestbook->insert_vehicle_comment($data);

		//Get Data So We Can Check If We Need To PM Owner Or Email
		$data = $garage_vehicle->get_vehicle($cid);		

		$notify_data = $garage_guestbook->notify_on_comment($data['user_id']);

		//If User Has Requested Notification On Comments Sent Them A PM
		if ($notify_data['user_garage_guestbook_pm_notify'])
		{
			include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
			include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

			$data['vehicle_link'] 	= '<a href="garage_guestbook.'.$phpEx.'?mode=view_guestbook&CID=$cid">' . $user->lang['HERE'] . '</a>';

			$message_parser = new parse_message();
			$message_parser->message = sprintf($user->lang['GUESTBOOK_NOTIFY_TEXT'], $data['vehicle_link']);
			$message_parser->parse(true, true, true, false, false, true, true);

			$pm_data = array(
				'from_user_id'			=> $user->data['user_id'],
				'from_user_ip'			=> $user->data['user_ip'],
				'from_username'			=> $user->data['username'],
				'enable_sig'			=> false,
				'enable_bbcode'			=> true,
				'enable_smilies'		=> true,
				'enable_urls'			=> false,
				'icon_id'			=> 0,
				'bbcode_bitfield'		=> $message_parser->bbcode_bitfield,
				'bbcode_uid'			=> $message_parser->bbcode_uid,
				'message'			=> $message_parser->message,
				'address_list'			=> array('u' => array($data['user_id'] => 'to')),
			);
		}
		//If User Has Requested Notification On Comments Sent Them A Email
		if ($notify_data['user_garage_guestbook_email_notify'])
		{
			//
		}

		//Now We Have All Data Lets Send The PM!!
		submit_pm('post', $user->lang['GUESTBOOK_NOTIFY_SUBJECT'], $pm_data, false, false);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;

	case 'edit_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Pull Required Comment Data From DB
		$data = $garage_guestbook->get_comment($comment_id);	
		
		$template->assign_vars(array(
			'CID' 		 => $cid,
			'COMMENT_ID' 	 => $data['comment_id'],
			'COMMENTS' 	 => $data['post'])
		);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_edit_comment.html')
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comments' => '', 'COMMENT_ID' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('comments', 'COMMENT_ID');
		$garage->check_required_vars($params);

		//Update The Comment In The Vehicle Guestbook
		$garage->update_single_field(GARAGE_GUESTBOOKS_TABLE, 'post', $data['comments'], 'id', $data['COMMENT_ID']);

		//If Needed Update Garage Config Telling Us We Have A Pending Item And Perform Notifications If Configured
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;

	case 'delete_comment':

		//Only Allow Moderators Or Administrators Perform This Action
		if (!$auth->acl_get('m_garage'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		//Get All Data Posted And Make It Safe To Use
		$params = array('comment_id' => '');
		$data = $garage->process_vars($params);

		//Delete The Comment From The Guestbook
		$garage->delete_rows(GARAGE_GUESTBOOKS_TABLE, 'id', $data['comment_id']);

		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;CID=$cid"));

		break;
}

$garage_template->version_notice();

//Set Template Files In Used For Footer
$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

//Generate Page Footer
page_footer();

?>
