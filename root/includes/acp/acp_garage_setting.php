<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_garage_setting
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $garage_config;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);

		$user->add_lang(array('acp/board', 'mods/garage', 'acp/garage'));

		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'general':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_GENERAL_CONFIG',
						'cars_per_page'				=> array('lang' => 'VEHICLES_PER_PAGE', 'type' => 'text:3:4', 'explain' => true),
						'default_make_id'			=> array('lang' => 'DEFAULT_MAKE', 'type' => 'text:3:4', 'explain' => true),
						'default_model_id'			=> array('lang' => 'DEFAULT_MODEL', 'type' => 'text:3:4', 'explain' => true),
						'year_start'				=> array('lang' => 'YEAR_RANGE_BEGINNING', 'type' => 'text:3:4', 'explain' => true),
						'year_end'				=> array('lang' => 'YEAR_RANGE_END', 'type' => 'text:3:4', 'explain' => true),
						'enable_user_submit_make'		=> array('lang' => 'USER_SUBMIT_MAKE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_user_submit_model'		=> array('lang' => 'USER_SUBMIT_MODEL', 'type' => 'radio:yes_no', 'explain' => true),
						'dateformat'				=> array('lang' => 'GARAGE_DATE_FORMAT', 'type' => 'custom', 'method' => 'dateformat_select', 'explain' => true),
						'integrate_viewtopic'			=> array('lang' => 'INTEGRATE_VIEWTOPIC', 'type' => 'radio:yes_no', 'explain' => true),
						'integrate_memberlist'			=> array('lang' => 'INTEGRATE_MEMBERLIST', 'type' => 'radio:yes_no', 'explain' => true),
						'integrate_profile'			=> array('lang' => 'INTEGRATE_PROFILE', 'type' => 'radio:yes_no', 'explain' => true),
						'profile_thumbs'			=> array('lang' => 'PROFILE_INTEGRATION', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_pm_pending_notify'		=> array('lang' => 'PENDING_PM_NOTIFY', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_email_pending_notify'		=> array('lang' => 'PENDING_EMAIL_NOTIFY', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_pm_pending_notify_optout'	=> array('lang' => 'PENDING_PM_NOTIFY_OPTOUT', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_email_pending_notify_optout'	=> array('lang' => 'PENDING_EMAIL_NOTIFY_OPTOUT', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_vehicle_approval'		=> array('lang' => 'ENABLE_VEHICLE_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;

			case 'menu':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_MENU_CONFIG',
						'enable_browse_menu' 			=> array('lang' => 'ENABLE_BROWSE_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_search_menu' 			=> array('lang' => 'ENABLE_SEARCH_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_insurance_review_menu' 		=> array('lang' => 'ENABLE_INSURANCE_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_garage_review_menu' 		=> array('lang' => 'ENABLE_GARAGE_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_shop_review_menu' 		=> array('lang' => 'ENABLE_SHOP_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_menu' 		=> array('lang' => 'ENABLE_QUARTERMILE_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_menu' 			=> array('lang' => 'ENABLE_DYNORUN_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_lap_menu' 			=> array('lang' => 'ENABLE_LAP_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_garage_header' 			=> array('lang' => 'ENABLE_GARAGE_HEADER', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_header' 		=> array('lang' => 'ENABLE_QUARTERMILE_HEADER', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_header'			=> array('lang' => 'ENABLE_DYNORUN_HEADER', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_latest_vehicle_index'	 	=> array('lang' => 'ENABLE_LATESTMAIN_VEHICLE', 'type' => 'radio:yes_no', 'explain' => true),
						'latest_vehicle_index_limit' 		=> array('lang' => 'LATESTMAIN_VEHCILE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
					)
				);

				break;
			case 'index':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_INDEX_CONFIG',
						'index_columns' 			=> array('lang' => 'GARAGE_INDEX_COLUMNS', 'type' => 'custom', 'method' => 'index_columns', 'explain' => true),
						'enable_user_index_columns' 		=> array('lang' => 'ENABLE_USER_INDEX_COLUMNS', 'type' => 'radio:yes:no', 'explain' => true),
						'enable_featured_vehicle' 		=> array('lang' => 'ENABLE_FEATURED_VEHICLE', 'type' => 'custom', 'method' => 'select_featured_vehicle', 'explain' => true),
						'featured_vehicle_id'			=> array('lang' => 'FEATURED_VEHICLE_ID', 'type' => 'text:3:4', 'explain' => true),
						'featured_vehicle_from_block'		=> array('lang' => 'FEATURED_FROM_BLOCK', 'type' => 'custom', 'method' => 'featured_block', 'explain' => true),
						'featured_vehicle_description'		=> array('lang' => 'FEATURED_VEHICLE_DESCRIPTION', 'type' => 'text:39:40', 'explain' => true),
						'enable_newest_vehicle' 		=> array('lang' => 'ENABLE_NEWEST_VEHCILE', 'type' => 'radio:yes_no', 'explain' => true),
						'newest_vehicle_limit'			=> array('lang' => 'NEWEST_VEHICLE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_updated_vehicle'		=> array('lang' => 'ENABLE_UPDATED_VEHICLE', 'type' => 'radio:yes_no', 'explain' => true),
						'updated_vehicle_limit'			=> array('lang' => 'UPDATED_VEHICLE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_newest_modification'		=> array('lang' => 'ENABLE_NEWEST_MODIFICATION', 'type' => 'radio:yes_no', 'explain' => true),
						'newest_modification_limit'		=> array('lang' => 'NEWEST_MODIFICATION_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_updated_modification'		=> array('lang' => 'ENABLE_UPDATED_MODIFICATION', 'type' => 'radio:yes_no', 'explain' => true),
						'updated_modification_limit'		=> array('lang' => 'UPDATED_MODIFICATION_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_modified'			=> array('lang' => 'ENABLE_MOST_MODIFIED', 'type' => 'radio:yes_no', 'explain' => true),
						'most_modified_limit'			=> array('lang' => 'MOST_MODIFIED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_spent'			=> array('lang' => 'ENABLE_MOST_SPENT', 'type' => 'radio:yes_no', 'explain' => true),
						'most_spent_limit'			=> array('lang' => 'MOST_SPENT_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_viewed'			=> array('lang' => 'ENABLE_MOST_VIEWED', 'type' => 'radio:yes_no', 'explain' => true),
						'most_viewed_limit'			=> array('lang' => 'MOST_VIEWED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_last_commented'			=> array('lang' => 'ENABLE_LAST_COMMENTED', 'type' => 'radio:yes_no', 'explain' => true),
						'last_commented_limit'			=> array('lang' => 'LAST_COMMENTED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_dynorun'			=> array('lang' => 'ENABLE_TOP_DYNORUN', 'type' => 'radio:yes_no', 'explain' => true),
						'top_dynorun_limit'			=> array('lang' => 'TOP_DYNORUN_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_quartermile'		=> array('lang' => 'ENABLE_TOP_QUARTERMILE', 'type' => 'radio:yes_no', 'explain' => true),
						'top_quartermile_limit'			=> array('lang' => 'TOP_QUARTERMILE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_rating'			=> array('lang' => 'ENABLE_TOP_RATING', 'type' => 'radio:yes_no', 'explain' => true),
						'top_rating_limit'			=> array('lang' => 'TOP_RATING_LIMIT', 'type' => 'text:3:4', 'explain' => true),
					)
				);

				break;
			case 'images':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_IMAGE_CONFIG',
						'enable_images'				=> array('lang' => 'ENABLE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_vehicle_images'			=> array('lang' => 'ENABLE_VEHICLE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_modification_images'		=> array('lang' => 'ENABLE_MODIFICATION_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_images'		=> array('lang' => 'ENABLE_QUARTERMILE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_images'			=> array('lang' => 'ENABLE_DYNORUN_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_lap_images'			=> array('lang' => 'ENABLE_LAP_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_uploaded_images'		=> array('lang' => 'ENABLE_UPLOADED_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_remote_images'			=> array('lang' => 'ENABLE_REMOTE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'remote_timeout'			=> array('lang' => 'REMOTE_TIMEOUT', 'type' => 'text:3:4', 'explain' => true),
						'enable_mod_gallery'			=> array('lang' => 'ENABLE_MODIFICATION_GALLERY', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_gallery'		=> array('lang' => 'ENABLE_QUARTERMILE_GALLERY', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_gallery'		=> array('lang' => 'ENABLE_DYNORUN_GALLERY', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_lap_gallery'			=> array('lang' => 'ENABLE_LAP_GALLERY', 'type' => 'radio:yes_no', 'explain' => true),
						'gallery_limit'				=> array('lang' => 'GALLERY_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'max_image_kbytes'			=> array('lang' => 'IMAGE_MAX_SIZE', 'type' => 'text:3:4', 'explain' => true),
						'max_image_resolution'			=> array('lang' => 'IMAGE_MAX_RESOLUTION', 'type' => 'text:3:4', 'explain' => true),
						'thumbnail_resolution'			=> array('lang' => 'THUMBNAIL_RESOLUTION', 'type' => 'text:3:4', 'explain' => true),
						'enable_watermark'			=> array('lang' => 'ENABLE_WATERMARK', 'type' => 'radio:yes_no', 'explain' => true),
						'watermark_type'			=> array('lang' => 'WATERMARK_TYPE', 'type' => 'custom', 'method' => 'watermark_type', 'explain' => true),
						'watermark_source'			=> array('lang' => 'WATERMARK_SOURCE', 'type' => 'text:39:40', 'explain' => true),
					)
				);

				break;
			case 'quartermile':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_QUARTERMILE_CONFIG',
						'enable_quartermile'			=> array('lang' => 'ENABLE_QUARTERMILE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_approval'		=> array('lang' => 'ENABLE_QUARTERMILE_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_image_required'	=> array('lang' => 'ENABLE_QUARTERMILE_IMAGE_REQUIRED', 'type' => 'radio:yes_no', 'explain' => true),
						'quartermile_image_required_limit'	=> array('lang' => 'QUARTERMILE_IMAGE_REQUIRED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
					)
				);

				break;
			case 'dynorun':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_DYNORUN_CONFIG',
						'enable_dynorun'			=> array('lang' => 'ENABLE_DYNORUN', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_approval'		=> array('lang' => 'ENABLE_DYNORUN_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_image_required'		=> array('lang' => 'ENABLE_DYNORUN_IMAGE_REQUIRED', 'type' => 'radio:yes_no', 'explain' => true),
						'dynorun_image_required_limit'		=> array('lang' => 'DYNORUN_IMAGE_REQUIRED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
					)
				);

				break;
			case 'track':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_TRACK_CONFIG',
						'enable_tracktime'			=> array('lang' => 'ENABLE_TRACKTIME', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_user_add_track'			=> array('lang' => 'ENABLE_USER_ADD_LAP', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_lap_approval'			=> array('lang' => 'ENABLE_LAP_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_track_approval'			=> array('lang' => 'ENABLE_TRACK_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'insurance':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_INSURANCE_CONFIG',
						'enable_insurance'			=> array('lang' => 'ENABLE_INSURANCE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_insurance_search'		=> array('lang' => 'ENABLE_INSURANCE_SEARCH', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'business':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_BUSINESS_CONFIG',
						'enable_user_submit_business'		=> array('lang' => 'USER_SUBMIT_BUSINESS', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_business_approval'		=> array('lang' => 'BUSINESS_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'rating':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_VEHICLE_RATING_CONFIG',
						'rating_permanent'			=> array('lang' => 'RATING_PERMANENT', 'type' => 'radio:yes_no', 'explain' => true),
						'rating_always_updateable'		=> array('lang' => 'RATING_ALWAYS_UPDATEABLE', 'type' => 'radio:yes_no', 'explain' => true),
						'minimum_ratings_required'		=> array('lang' => 'RATING_MINIMUM_REQUIRED', 'type' => 'text:3:4', 'explain' => true),
					)
				);

				break;
			case 'guestbook':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(

						'legend1'				=> 'ACP_GARAGE_GUESTBOOK_CONFIG',
						'enable_guestbooks'			=> array('lang' => 'ENABLE_GUESTBOOK', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_guestbooks_bbcode'		=> array('lang' => 'ENABLE_GUESTBOOK_BBCODE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_guestbooks_comment_approval'	=> array('lang' => 'ENABLE_GUESTBOOK_COMMENT_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'product':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_PRODUCT_CONFIG',
						'enable_user_submit_product'		=> array('lang' => 'ENABLE_USER_SUBMIT_PRODUCT', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_product_approval'		=> array('lang' => 'ENABLE_PRODUCT_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_product_search'			=> array('lang' => 'ENABLE_PRODUCT_SEARCH', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'service':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_SERVICE_CONFIG',
						'enable_service'			=> array('lang' => 'ENABLE_SERVICE', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);

				break;
			case 'blog':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_GARAGE_BLOG_CONFIG',
						'enable_blogs'				=> array('lang' => 'ENABLE_BLOG', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_blogs_bbcode'			=> array('lang' => 'ENABLE_BLOG_BBCODE', 'type' => 'radio:yes_no', 'explain' => true),
					)
				);
				break;

			default:
				trigger_error('NO_MODE');
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $garage_config;
		$cfg_array = (isset($_REQUEST['config'])) ? request_var('config', array('' => '')) : $this->new_config;

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				$garage_admin->set_config($config_name, $config_value, $garage_config);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_GARAGE_CONFIG_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'		=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],
			'U_ACTION'		=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'	=> true,
					'LEGEND'	=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$template->assign_block_vars('options', array(
				'KEY'		=> $config_key,
				'TITLE'		=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'	=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'	=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}

	/**
	* 
	*/
	function select_featured_vehicle($value, $key = '')
	{
		global $user, $config;

		$radio_ary = array('0' => 'DISABLED', '1' => 'BY_VEHICLE_ID', '2' => 'RANDOM', '4' => 'FROM_BLOCK');

		return h_radio('config[enable_featured_vehicle]', $radio_ary, $value, $key);
	}

	/**
	*
	*/
	function index_columns($value, $key = '')
	{
		global $user;

		return '<select name="config[index_columns]" id="index_columns"><option value="1"' . (($value == 1) ? ' selected="selected"' : '') . '>1</option><option value="2"' . (($value == 2) ? ' selected="selected"' : '') . '>2</option><option value="3"' . (($value == 3) ? ' selected="selected"' : '') . '>3</option><option value="4"' . (($value == 4) ? ' selected="selected"' : '') . '>4</option></select>';

	}

	function featured_block($value, $key = '')
	{
		global $user;

		return '<select name="config[featured_vehicle_from_block]" id="featured_vehicle_from_block">
				<option value="1"' . (($value == 1) ? ' selected="selected"' : '') . '>'.$user->lang['NEWEST_VEHICLES'].'</option>
				<option value="2"' . (($value == 2) ? ' selected="selected"' : '') . '>'.$user->lang['LAST_UPDATED_VEHICLES'].'</option>
				<option value="3"' . (($value == 3) ? ' selected="selected"' : '') . '>'.$user->lang['NEWEST_MODIFICATIONS'].'</option>
				<option value="4"' . (($value == 4) ? ' selected="selected"' : '') . '>'.$user->lang['LAST_UPDATED_MODIFICATIONS'].'</option>
				<option value="5"' . (($value == 5) ? ' selected="selected"' : '') . '>'.$user->lang['MOST_MODIFIED_VEHICLE'].'</option>
				<option value="6"' . (($value == 6) ? ' selected="selected"' : '') . '>'.$user->lang['MOST_MONEY_SPENT'].'</option>
				<option value="7"' . (($value == 7) ? ' selected="selected"' : '') . '>'.$user->lang['MOST_VIEWED_VEHICLE'].'</option>
				<option value="8"' . (($value == 8) ? ' selected="selected"' : '') . '>'.$user->lang['LATEST_VEHICLE_COMMENTS'].'</option>
				<option value="9"' . (($value == 9) ? ' selected="selected"' : '') . '>'.$user->lang['TOP_QUARTERMILE_RUNS'].'</option>
				<option value="10"' . (($value == 10) ? ' selected="selected"' : '') . '>'.$user->lang['TOP_DYNO_RUNS'].'</option>
				<option value="11"' . (($value == 11) ? ' selected="selected"' : '') . '>'.$user->lang['TOP_RATED_VEHICLES'].'</option>
			</select>';
	}

	/**
	*
	*/
	function watermark_type($value, $key = '')
	{
		global $user;

		return '<select name="config[watermark_type]" id="watermark_type"><option value="permanent"' . (($value == 'permanent') ? ' selected="selected"' : '') . '>'.$user->lang['PERMANENT'].'</option><option value="non_permanent"' . (($value == 'non_permanent') ? ' selected="selected"' : '') . '>'.$user->lang['NON_PERMANENT'].'</option></select>';
	}

	/**
	* Select default dateformat
	*/
	function dateformat_select($value, $key)
	{
		global $user;

		$dateformat_options = '';

		foreach ($user->lang['dateformats'] as $format => $null)
		{
			$dateformat_options .= '<option value="' . $format . '"' . (($format == $value) ? ' selected="selected"' : '') . '>';
			$dateformat_options .= $user->format_date(time(), $format, true) . ((strpos($format, '|') !== false) ? ' [' . $user->lang['RELATIVE_DAYS'] . ']' : '');
			$dateformat_options .= '</option>';
		}

		$dateformat_options .= '<option value="custom"';
		if (!in_array($value, array_keys($user->lang['dateformats'])))
		{
			$dateformat_options .= ' selected="selected"';
		}
		$dateformat_options .= '>' . $user->lang['CUSTOM_DATEFORMAT'] . '</option>';

		return "<select name=\"dateoptions\" id=\"dateoptions\" onchange=\"if (this.value == 'custom') { document.getElementById('$key').value = '$value'; } else { document.getElementById('$key').value = this.value; }\">$dateformat_options</select>
		<input type=\"text\" name=\"config[$key]\" id=\"$key\" value=\"$value\" maxlength=\"30\" />";
	}
}

?>
