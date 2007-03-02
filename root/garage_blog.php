<?php
/** 
*
* @package garage
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

define('IN_PHPBB', true);

//Let's Set The Root Dir For phpBB And Load Normal phpBB Required Files
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

//Start Session Management
$user->session_begin();
$auth->acl($user->data);

//Setup Lang Files
$user->setup(array('mods/garage'));

//Build All Garage Classes e.g $garage_images->
require($phpbb_root_path . 'includes/mods/class_garage_blog.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);

//Set The Page Title
$page_title = $user->lang['GARAGE'];

//Get All String Parameters And Make Safe
$params = array('mode' => 'mode', 'sort' => 'sort', 'start' => 'start', 'order' => 'order');
while(list($var, $param) = @each($params))
{
	$$var = request_var($param, '');
}

//Get All Non-String Parameters
$params = array('vid' => 'VID', 'bid' => 'BID', 'eid' => 'EID');
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
	case 'insert_blog':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_add_blog') || !$garage_config['enable_blogs'])
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		$text = utf8_normalize_nfc(request_var('blog_text', '', true));
		$uid = $bitfield = $flags = ''; // will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);

		$data = array(
			'blog_title'		=> request_var('blog_title', ''),
			'blog_text'		=> $text,
			'bbcode_uid'        	=> $uid,
		    	'bbcode_bitfield'   	=> $bitfield,
		    	'bbcode_flags'      	=> $flags,
		);

		//Insert Blog With Data Acquired
		$garage_blog->insert_blog($data);

		//Update The Time Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

		break;

	case 'edit_blog':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_blog.$phpEx?mode=edit_blog&amp;BID=$bid&amp;VID=$vid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Build Page Header ;)
		page_header($page_title);

		//Set Template Files In Use For This Mode
		$template->set_filenames(array(
			'header' => 'garage_header.html',
			'body'   => 'garage_blog.html')
		);

		//Pull Required Blog Data From DB
		$data = $garage_blog->get_blog($bid);

		//Build All HTML Parts
		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang['EDIT_BLOG'],
			'L_BUTTON'		=> $user->lang['EDIT_BLOG'],
			'VID'			=> $vid,
			'BID'			=> $bid,
			'BLOG_TITLE'		=> $data['blog_title'],
			'BLOG_TEXT'		=> $data['blog_text'],
			'S_MODE_ACTION' 	=> append_sid("{$phpbb_root_path}garage_blog.$phpEx", "mode=update_blog"))
		);

		//Display Page...In Order Header->Menu->Body->Footer (Foot Gets Parsed At The Bottom)
		$garage_template->sidemenu();

		break;

	case 'update_blog':

		//Check The User Is Logged In...Else Send Them Off To Do So......And Redirect Them Back!!!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box("garage_blog.$phpEx?mode=edit_blog&amp;BID=$bid&amp;VID=$vid");
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Get All Data Posted And Make It Safe To Use
		$params = array('blog_title' => '', 'blog_text' => '');
		$data = $garage->process_vars($params);

		//Checks All Required Data Is Present
		$params = array('blog_title', 'blog_text');
		$garage->check_required_vars($params);

		//Update The Blog With Data Acquired
		$garage_blog->update_blog($data);

		//Update The Vehicle Timestamp Now...In Case We Get Redirected During Image Processing
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

		break;

	case 'delete_blog':

		//Let Check The User Is Allowed Perform This Action
		if (!$auth->acl_get('u_garage_delete_blog'))
		{
			redirect(append_sid("{$phpbb_root_path}garage.$phpEx", "mode=error&amp;EID=14"));
		}

		//Check Vehicle Ownership
		$garage_vehicle->check_ownership($vid);

		//Delete The Quartermie Time
		$garage_blog->delete_blog($bid);

		//Update Timestamp For Vehicle
		$garage_vehicle->update_vehicle_time($vid);

		redirect(append_sid("{$phpbb_root_path}garage_vehicle.$phpEx", "mode=view_own_vehicle&amp;VID=$vid"));

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
