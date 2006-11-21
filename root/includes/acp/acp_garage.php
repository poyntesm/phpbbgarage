<?php
/** 
*
* @package acp
* @version $Id: acp_board.php,v 1.35 2006/06/16 16:54:37 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_garage
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		//Build All Garage Classes e.g $garage_images->
		require($phpbb_root_path . 'includes/mods/class_garage.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_admin.' . $phpEx);


		$user->add_lang('acp/board');
		$user->add_lang('acp/garage');

		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		switch ($mode)
		{
			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_GARAGE_SETTINGS',
					'vars'	=> array(
						'legend1'			=> 'ACP_GARAGE_GENERAL_CONFIG',
						'cars_per_page'			=> array('lang' => 'VEHICLES_PER_PAGE', 'type' => 'text:3:4', 'explain' => true),
						'year_start'			=> array('lang' => 'YEAR_RANGE_BEGINNING', 'type' => 'text:3:4', 'explain' => true),
						'year_end'			=> array('lang' => 'YEAR_RANGE_END', 'type' => 'text:3:4', 'explain' => true),
						'enable_user_submit_make'	=> array('lang' => 'USER_SUBMIT_MAKE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_user_submit_model'	=> array('lang' => 'USER_SUBMIT_MODEL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_latestmain_vehicle' 	=> array('lang' => 'ENABLE_LATESTMAIN_VEHICLE', 'type' => 'radio:yes_no', 'explain' => true),
						'latestmain_vehicle_limit' 	=> array('lang' => 'LATESTMAIN_VEHCILE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'garage_dateformat'		=> array('lang' => 'GARAGE_DATE_FORMAT', 'type' => 'custom', 'method' => 'dateformat_select', 'explain' => true),
						'profile_integration'		=> array('lang' => 'PROFILE_INTEGRATION', 'type' => 'radio:yes_no', 'explain' => true),

						'legend2'			=> 'ACP_GARAGE_MENU_CONFIG',
						'enable_browse_menu' 		=> array('lang' => 'ENABLE_BROWSE_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_search_menu' 		=> array('lang' => 'ENABLE_SEARCH_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_insurance_review_menu' 	=> array('lang' => 'ENABLE_INSURANCE_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_garage_review_menu' 	=> array('lang' => 'ENABLE_GARAGE_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_shop_review_menu' 	=> array('lang' => 'ENABLE_SHOP_REVIEW_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_menu' 	=> array('lang' => 'ENABLE_QUARTERMILE_MENU', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_menu' 		=> array('lang' => 'ENABLE_DYNORUN_MENU', 'type' => 'radio:yes_no', 'explain' => true),

						'legend3'			=> 'ACP_GARAGE_INDEX_CONFIG',
						'enable_featured_vehicle' 	=> array('lang' => 'ENABLE_FEATURED_VEHICLE', 'type' => 'radio:yes_no', 'explain' => true),
						'default_style'			=> array('lang' => 'DEFAULT_STYLE', 'type' => 'select', 'function' => 'style_select', 'params' => array('{CONFIG_VALUE}', true), 'explain' => false),
						'enable_newest_vehicle' 	=> array('lang' => 'ENABLE_NEWEST_VEHCILE', 'type' => 'radio:yes_no', 'explain' => true),
						'newest_vehicle_limit'		=> array('lang' => 'NEWEST_VEHICLE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_updated_vehicle'	=> array('lang' => 'ENABLE_UPDATED_VEHICLE', 'type' => 'radio:yes_no', 'explain' => true),
						'updated_vehicle_limit'		=> array('lang' => 'UPDATED_VEHICLE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_newest_modification'	=> array('lang' => 'ENABLE_NEWEST_MODIFICATION', 'type' => 'radio:yes_no', 'explain' => true),
						'newest_modification_limit'	=> array('lang' => 'NEWEST_MODIFICATION_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_updated_modification'	=> array('lang' => 'ENABLE_UPDATED_MODIFICATION', 'type' => 'radio:yes_no', 'explain' => true),
						'updated_modification_limit'	=> array('lang' => 'UPDATED_MODIFICATION_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_modified'		=> array('lang' => 'ENABLE_MOST_MODIFIED', 'type' => 'radio:yes_no', 'explain' => true),
						'most_modified_limit'		=> array('lang' => 'MOST_MODIFIED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_spent'		=> array('lang' => 'ENABLE_MOST_SPENT', 'type' => 'radio:yes_no', 'explain' => true),
						'most_spent_limit'		=> array('lang' => 'MOST_SPENT_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_most_viewed'		=> array('lang' => 'ENABLE_MOST_VIEWED', 'type' => 'radio:yes_no', 'explain' => true),
						'most_viewed_limit'		=> array('lang' => 'MOST_VIEWED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_last_commented'		=> array('lang' => 'ENABLE_LAST_COMMENTED', 'type' => 'radio:yes_no', 'explain' => true),
						'last_commented_limit'		=> array('lang' => 'LAST_COMMENTED_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_dynorun'		=> array('lang' => 'ENABLE_TOP_DYNORUN', 'type' => 'radio:yes_no', 'explain' => true),
						'top_dynorun_limit'		=> array('lang' => 'TOP_DYNORUN_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_quartermile'	=> array('lang' => 'ENABLE_TOP_QUARTERMILE', 'type' => 'radio:yes_no', 'explain' => true),
						'top_quartermile_limit'		=> array('lang' => 'TOP_QUARTERMILE_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'enable_top_rating'		=> array('lang' => 'ENABLE_TOP_RATING', 'type' => 'radio:yes_no', 'explain' => true),
						'top_rating_limit'		=> array('lang' => 'TOP_RATING_LIMIT', 'type' => 'text:3:4', 'explain' => true),

						'legend4'			=> 'ACP_GARAGE_IMAGE_CONFIG',
						'enable_images'			=> array('lang' => 'ENABLE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_vehicle_images'		=> array('lang' => 'ENABLE_VEHICLE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_modification_images'	=> array('lang' => 'ENABLE_MODIFICATION_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_images'	=> array('lang' => 'ENABLE_QUARTERMILE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_images'		=> array('lang' => 'ENABLE_DYNORUN_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_uploaded_images'	=> array('lang' => 'ENABLE_UPLOADED_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_remote_images'		=> array('lang' => 'ENABLE_REMOTE_IMAGES', 'type' => 'radio:yes_no', 'explain' => true),
						'remote_timeout'		=> array('lang' => 'REMOTE_TIMEOUT', 'type' => 'text:3:4', 'explain' => true),
						'enable_mod_gallery'		=> array('lang' => 'ENABLE_MODIFICATION_GALLERY', 'type' => 'radio:yes_no', 'explain' => true),
						'mod_gallery_limit'		=> array('lang' => 'MODIFICATION_GALLERY_LIMIT', 'type' => 'text:3:4', 'explain' => true),
						'max_kbytes'			=> array('lang' => 'IMAGE_MAX_SIZE', 'type' => 'text:3:4', 'explain' => true),
						'max_resolution'		=> array('lang' => 'IMAGE_MAX_RESOLUTION', 'type' => 'text:3:4', 'explain' => true),
						'thumbnail_resolution'		=> array('lang' => 'THUMBNAIL_RESOLUTION', 'type' => 'text:3:4', 'explain' => true),

						'legend5'			=> 'ACP_GARAGE_QUARTERMILE_CONFIG',
						'enable_quartermile'		=> array('lang' => 'ENABLE_QUARTERMILE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_approval'	=> array('lang' => 'ENABLE_QUARTERMILE_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_quartermile_image_required'=> array('lang' => 'ENABLE_QUARTERMILE_IMAGE_REQUIRED', 'type' => 'radio:yes_no', 'explain' => true),
						'quartermile_image_required_limit'=> array('lang' => 'QUARTERMILE_IMAGE_REQUIRED_LIMIT', 'type' => 'text:3:4', 'explain' => true),

						'legend6'			=> 'ACP_GARAGE_DYNORUN_CONFIG',
						'enable_dynorun'		=> array('lang' => 'ENABLE_DYNORUN', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_approval'	=> array('lang' => 'ENABLE_DYNORUN_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_dynorun_image_required'	=> array('lang' => 'ENABLE_DYNORUN_IMAGE_REQUIRED', 'type' => 'radio:yes_no', 'explain' => true),
						'dynorun_image_required_limit'	=> array('lang' => 'DYNORUN_IMAGE_REQUIRED_LIMIT', 'type' => 'text:3:4', 'explain' => true),

						'legend7'			=> 'ACP_GARAGE_INSURANCE_CONFIG',
						'enable_insurance'		=> array('lang' => 'ENABLE_INSURANCE', 'type' => 'radio:yes_no', 'explain' => true),
						'enable_insurance_search'	=> array('lang' => 'ENABLE_INSURANCE_SEARCH', 'type' => 'radio:yes_no', 'explain' => true),

						'legend8'			=> 'ACP_GARAGE_BUSINESS_CONFIG',
						'enable_business_approval'	=> array('lang' => 'BUSINESS_APPROVAL', 'type' => 'radio:yes_no', 'explain' => true),

						'legend9'			=> 'ACP_GARAGE_VEHICLE_RATING_CONFIG',
						'ratings_permanent'		=> array('lang' => 'RATING_PERMANENT', 'type' => 'radio:yes_no', 'explain' => true),
						'ratings_always_updateable'	=> array('lang' => 'RATING_ALWAYS_UPDATEABLE', 'type' => 'radio:yes_no', 'explain' => true),
						'magic_number'			=> array('lang' => 'RATING_MINIMUM_REQUIRED', 'type' => 'text:3:4', 'explain' => true),

						'legend10'			=> 'ACP_GARAGE_GUESTBOOK_CONFIG',
						'enable_guestbook'		=> array('lang' => 'ENABLE_GUESTBOOK', 'type' => 'radio:yes_no', 'explain' => true),

					)
				);

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
					add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

					trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
				}

				$this->tpl_name = 'acp_board';
				$this->page_title = $display_vars['title'];

				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang[$display_vars['title']],
					'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],
					'U_ACTION'			=> $this->u_action)
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
						'KEY'			=> $config_key,
						'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
						'S_EXPLAIN'		=> $vars['explain'],
						'TITLE_EXPLAIN'		=> $l_explain,
						'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
						)
					);
		
					unset($display_vars['vars'][$config_key]);
				}
			break;

			default:
				trigger_error('NO_MODE');
		}


	}


	/**
	* Select captcha pixel noise
	*/
	function captcha_pixel_noise_select($value, $key = '')
	{
		global $user;

		return '<option value="0"' . (($value == 0) ? ' selected="selected"' : '') . '>' . $user->lang['NONE'] . '</option><option value="1"' . (($value == 1) ? ' selected="selected"' : '') . '>' . $user->lang['LIGHT'] . '</option><option value="2"' . (($value == 2) ? ' selected="selected"' : '') . '>' . $user->lang['MEDIUM'] . '</option><option value="3"' . (($value == 3) ? ' selected="selected"' : '') . '>' . $user->lang['HEAVY'] . '</option>';
	}

	/**
	* Select ip validation
	*/
	function select_ip_check($value, $key = '')
	{
		$radio_ary = array(4 => 'ALL', 3 => 'CLASS_C', 2 => 'CLASS_B', 0 => 'NONE');

		return h_radio('config[ip_check]', $radio_ary, $value, $key);
	}

	/**
	* Select account activation method
	*/
	function select_acc_activation($value, $key = '')
	{
		global $user, $config;

		$radio_ary = array(USER_ACTIVATION_DISABLE => 'ACC_DISABLE', USER_ACTIVATION_NONE => 'ACC_NONE');
		if ($config['email_enable'])
		{
			$radio_ary += array(USER_ACTIVATION_SELF => 'ACC_USER', USER_ACTIVATION_ADMIN => 'ACC_ADMIN');
		}

		return h_radio('config[require_activation]', $radio_ary, $value, $key);
	}

	/**
	* Maximum/Minimum username length
	*/
	function username_length($value, $key = '')
	{
		global $user;

		return '<input id="' . $key . '" type="text" size="3" maxlength="3" name="config[min_name_chars]" value="' . $value . '" /> ' . $user->lang['MIN_CHARS'] . '&nbsp;&nbsp;<input type="text" size="3" maxlength="3" name="config[max_name_chars]" value="' . $this->new_config['max_name_chars'] . '" /> ' . $user->lang['MAX_CHARS'];
	}


	/**
	* Board disable option and message
	*/
	function board_disable($value, $key)
	{
		global $user;

		$radio_ary = array(1 => 'YES', 0 => 'NO');

		return h_radio('config[board_disable]', $radio_ary, $value) . '<br /><input id="' . $key . '" type="text" name="config[board_disable_msg]" maxlength="255" size="40" value="' . $this->new_config['board_disable_msg'] . '" />';
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
