<?php
/** 
*
* @package ucp
* @version $Id: ucp_prefs.php,v 1.45 2006/11/27 16:05:23 acydburn Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* ucp_prefs
* Changing user preferences
* @package ucp
*/
class ucp_garage
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx;
		global $garage_config;

		$submit = (isset($_POST['submit'])) ? true : false;
		$error = $data = array();
		$s_hidden_fields = '';

		$user->add_lang('mods/garage');

		switch ($mode)
		{
			case 'options':

				$data = array(
					'index_columns'=> request_var('index_columns', $user->data['user_garage_index_columns']),
				);

				if ($submit)
				{
					$sql_ary = array(
						'user_garage_index_columns'	=> $data['index_columns'],
					);

					$sql = 'UPDATE ' . USERS_TABLE . '
						SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
						WHERE user_id = ' . $user->data['user_id'];
					$db->sql_query($sql);

					meta_refresh(3, $this->u_action);
					$message = $user->lang['PREFERENCES_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
					trigger_error($message);
				}

				$template->assign_vars(array(
					'L_TITLE'		=> $user->lang['UCP_GARAGE_OPTIONS'],
					'S_INDEX_DISPLAY'	=> ($garage_config['enable_user_index_columns']) ? 1 : 0,
					'S_INDEX_COLUMNS'	=> $data['index_columns'],
					'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
					'S_UCP_ACTION'		=> $this->u_action)
				);

				$this->tpl_name = 'ucp_garage_' . $mode;
				$this->page_title = 'UCP_GARAGE_' . strtoupper($mode);
			break;

			case 'notify':

				$data = array(
					'guestbook_email_notify'=> request_var('guestbook_email_notify', $user->data['user_garage_guestbook_email_notify']),
					'guestbook_pm_notify'	=> request_var('guestbook_pm_notify', $user->data['user_garage_guestbook_pm_notify']),
					'email_optout'		=> request_var('email_optout', $user->data['user_garage_mod_email_optout']),
					'pm_optout'		=> request_var('pm_optout', $user->data['user_garage_mod_pm_optout']),
				);

				if ($submit)
				{
					$sql_ary = array(
						'user_garage_guestbook_email_notify'	=> $data['guestbook_email_notify'],
						'user_garage_guestbook_pm_notify'	=> $data['guestbook_pm_notify'],
						'user_garage_mod_email_optout'		=> $data['email_optout'],
						'user_garage_mod_pm_optout'		=> $data['pm_optout'],
					);

					$sql = 'UPDATE ' . USERS_TABLE . '
						SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
						WHERE user_id = ' . $user->data['user_id'];
					$db->sql_query($sql);

					meta_refresh(3, $this->u_action);
					$message = $user->lang['PREFERENCES_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
					trigger_error($message);
				}

				$template->assign_vars(array(
					'L_TITLE'			=> $user->lang['UCP_GARAGE_NOTIFY'],
					'S_DISPLAY_EMAIL_OPTOUT'	=> ($garage_config['enable_email_pending_notify'] && $garage_config['enable_email_pending_notify_optout'] && $auth->acl_get('m_garage')) ? 1 : 0,
					'S_DISPLAY_PM_OPTOUT'		=> ($garage_config['enable_pm_pending_notify'] && $garage_config['enable_pm_pending_notify_optout'] && $auth->acl_get('m_garage')) ? 1 : 0,
					'S_GUESTBOOK_EMAIL_NOTIFY'	=> $data['guestbook_email_notify'],
					'S_GUESTBOOK_PM_NOTIFY'		=> $data['guestbook_pm_notify'],
					'S_EMAIL_OPTOUT'		=> $data['email_optout'],
					'S_PM_OPTOUT'			=> $data['pm_optout'],
					'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
					'S_UCP_ACTION'			=> $this->u_action)
				);
				$this->tpl_name = 'ucp_garage_notify';
				$this->page_title = 'UCP_GARAGE_NOTIFY';

			break;
		}
	}
}

?>
