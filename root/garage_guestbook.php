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
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
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
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_guestbook.' . $phpEx);

/**
* Setup variables 
*/
$mode = request_var('mode', '');
$vid = request_var('VID', '');
$comment_id = request_var('CMT_ID', '');

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
	case 'view_guestbook':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_browse'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=15"));
		}

		/**
		* Get vehicle & comment data from DB
		*/
		$vehicle_data = $garage_vehicle->get_vehicle($vid);
		$comment_data = $garage_guestbook->get_vehicle_comments($vid);
		
		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_view_guestbook.html')
		);
		for ($i = 0, $count = sizeof($comment_data);$i < $count; $i++)
		{	
			$username = $comment_data[$i]['username'];
			$temp_url = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $comment_data[$i]['user_id']);
			$poster = '<a href="' . $temp_url . '">' . $comment_data[$i]['username'] . '</a>';
			$poster_posts = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $user->lang['POSTS'] . ': ' . $comment_data[$i]['user_posts'] : '';
			$poster_from = ( $comment_data[$i]['user_from'] && $comment_data['user_id'] != ANONYMOUS ) ? $user->lang['Location'] . ': ' . $comment_data[$i]['user_from'] : '';
			$garage_id = $comment_data[$i]['vehicle_id'];
			$poster_car_year = ( $comment_data[$i]['made_year'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['made_year'] : '';
			$poster_car_mark = ( $comment_data[$i]['make'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ?  ' ' . $comment_data[$i]['make'] : '';
			$poster_car_model = ( $comment_data[$i]['model'] && $comment_data[$i]['user_id'] != ANONYMOUS ) ? ' ' . $comment_data[$i]['model'] : '';
			$poster_joined = ( $comment_data[$i]['user_id'] != ANONYMOUS ) ? $user->lang['JOINED'] . ': ' . $user->format_date($comment_data[$i]['user_regdate']) : '';

			if ( $comment_data[$i]['user_id'] == ANONYMOUS && $comment_data[$i]['post_username'] != '' )
			{
				$poster = $comment_data[$i]['post_username'];
			}

			$profile = '<a href="' . $temp_url . '">' . $user->lang['READ_PROFILE'] . '</a>';

			$temp_url = append_sid("{$phpbb_root_path}privmsg.$phpEx", "mode=post&amp;u=".$comment_data[$i]['user_id']);
			$pm = '<a href="' . $temp_url . '">' . $user->lang['SEND_PRIVATE_MESSAGE'] . '</a>';

			if ( !empty($comment_data[$i]['user_viewemail']) || $auth->acl_get('m_') )
			{
				$email_uri = ( $config['board_email_form'] ) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=email&amp;u=" . $comment_data[$i]['user_id']) : 'mailto:' . $comment_data[$i]['user_email'];

				$email = '<a href="' . $email_uri . '">' . $user->lang['SEND_EMAIL'] . '</a>';
			}
			else
			{
				$email_img = '';
				$email = '';
			}

			$www_img = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $user->lang['Visit_website'] . '" title="' . $user->lang['Visit_website'] . '" border="0" /></a>' : '';
			$www = ( $comment_data[$i]['user_website'] ) ? '<a href="' . $comment_data[$i]['user_website'] . '" target="_userwww">' . $user->lang['Visit_website'] . '</a>' : '';

			$posted = '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=".$comment_data[$i]['user_id']) . '">' . $comment_data[$i]['username'] . '</a>';
			$posted = $user->format_date($comment_data[$i]['post_date']);

			$post = generate_text_for_display($comment_data[$i]['post'], $comment_data[$i]['bbcode_uid'], $comment_data[$i]['bbcode_bitfield'], $comment_data[$i]['bbcode_flags']);

			$edit_img = '';
			$edit = '';
			$delpost_img = '';
			$delpost = '';

		 	if ( $auth->acl_get('m_garage_edit') )
			{
				$edit_img = $user->img('icon_post_edit', 'EDIT_POST');
				$edit = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_comment&amp;VID=$vid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['EDIT_POST'] . '</a>';
				$delpost_img = $user->img('icon_post_delete', 'DELETE_POST');
				$delpost = '<a href="'. append_sid("{$phpbb_root_path}garage.$phpEx", "mode=delete_comment&amp;VID=$vid&amp;comment_id=" . $comment_data[$i]['comment_id'] . "&amp;sid=" . $user->data['session_id']) . '">' . $user->lang['DELETE_POST'] . '</a>';

			}

			$template->assign_block_vars('comments', array(
				'POSTER_NAME' 		=> $poster,
				'POST_AUTHOR_COLOUR'	=> get_username_string('colour', $comment_data[$i]['user_id'], $comment_data[$i]['username'], $comment_data[$i]['user_colour']),
				'POSTER_JOINED' 	=> $poster_joined,
				'POSTER_POSTS' 		=> $poster_posts,
				'POSTER_FROM' 		=> $poster_from,
				'POSTER_CAR_MARK' 	=> $poster_car_mark,
				'POSTER_CAR_MODEL' 	=> $poster_car_model,
				'POSTER_CAR_YEAR' 	=> $poster_car_year,
				'VIEW_POSTER_CARPROFILE'=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_vehicle&amp;VID=$garage_id"),
				'POSTER_AVATAR' 	=> ($user->optionget('viewavatars')) ? get_user_avatar($comment_data[$i]['user_avatar'], $comment_data[$i]['user_avatar_type'], $comment_data[$i]['user_avatar_width'], $comment_data[$i]['user_avatar_height']) : '',
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
			'VID' 			=> $vid,
			'S_DISPLAY_LEAVE_COMMENT'=> $auth->acl_get('u_garage_comment'),
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=insert_comment&VID=$vid"))
		);
		$garage_template->sidemenu();
	break;

	case 'insert_comment':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('u_garage_comment'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=17"));
		}

		/**
		* Handle text in the phpBB standard UTF8 way, allowing bbcode, urls & smilies
		*/
		$text = utf8_normalize_nfc(request_var('comments', '', true));
		$uid = $bitfield = $flags = ''; 
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
		$data = array(
		    'post'              => $text,
		    'bbcode_uid'        => $uid,
		    'bbcode_bitfield'   => $bitfield,
		    'bbcode_flags'      => $flags,
		);

		/**
		* Perform required DB work to create new guestbook comment
		*/
		$garage_guestbook->insert_vehicle_comment($data);

		/**
		* Get vehicle & user notification data from DB
		*/
		$data = $garage_vehicle->get_vehicle($vid);		
		$notify_data = $garage_guestbook->notify_on_comment($data['user_id']);

		/**
		* Perform user PM notification if required
		*/
		if ($notify_data['user_garage_guestbook_pm_notify'])
		{
			include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
			include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

			$data['vehicle_link'] 	= '<a href="garage_guestbook.'.$phpEx.'?mode=view_guestbook&VID=$vid">' . $user->lang['HERE'] . '</a>';

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
			submit_pm('post', $user->lang['GUESTBOOK_NOTIFY_SUBJECT'], $pm_data, false, false);
		}

		/**
		* Perform user email/jabber notification if required
		*/
		if ($notify_data['user_garage_guestbook_email_notify'])
		{
			//Guess we need some code here at some point soon ;)
		}

		/**
		* Perform moderator notification if required
		*/
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;VID=$vid"));
	break;

	case 'edit_comment':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('m_garage_edit'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		/**
		* Get comment data from DB
		*/
		$data = $garage_guestbook->get_comment($comment_id);	
		
		/**
		* Handle template declarations & assignments
		*/
		page_header($user->lang['GARAGE']);
		$template->assign_vars(array(
			'VID' 		 => $vid,
			'COMMENT_ID' 	 => $data['comment_id'],
			'COMMENTS' 	 => $data['post'])
		);
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_edit_comment.html')
		);
		$garage_template->sidemenu();
	break;

	case 'update_comment':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('m_garage_edit'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		/**
		* Handle text in the phpBB standard UTF8 way, allowing bbcode, urls & smilies
		*/
		$text = utf8_normalize_nfc(request_var('comments', '', true));
		$uid = $bitfield = $flags = ''; 
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
		$data = array(
		    'post'              => $text,
		    'bbcode_uid'        => $uid,
		    'bbcode_bitfield'   => $bitfield,
		    'bbcode_flags'      => $flags,
		);

		/**
		* Perform required DB work to update comment
		*/
		$garage_guestbook->update_vehicle_comment($data, $comment_id);

		/**
		* Perform notification if required
		*/
		if ( $garage_config['enable_guestbooks_comment_approval'] )
		{
			$garage->pending_notification('unapproved_guestbook_comments');
		}

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;VID=$vid"));
	break;

	case 'delete_comment':
		/**
		* Check authorisation to perform action, redirecting to error screen if not
		*/
		if (!$auth->acl_get('m_garage_delete'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=13"));
		}

		/**
		* Get all required/optional data and check required data is present
		*/
		$params = array('comment_id' => '');
		$data = $garage->process_vars($params);

		/**
		* Perform required DB work to delete comment
		*/
		$garage->delete_rows(GARAGE_GUESTBOOKS_TABLE, 'id', $data['comment_id']);

		/**
		* All work complete for mode, so redirect to correct page
		*/
		redirect(append_sid("{$phpbb_root_path}garage_guestbook.$phpEx", "mode=view_guestbook&amp;VID=$vid"));
	break;
}
$garage_template->version_notice();

$template->set_filenames(array(
	'garage_footer' => 'garage_footer.html')
);

page_footer();
?>
