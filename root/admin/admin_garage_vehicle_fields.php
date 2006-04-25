<?php

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Garage']['Vehicle Fields'] = $filename;
	return;
}

define('IN_PHPBB', 1);
// Include files
$phpbb_root_path = './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require('pagestart.' . $phpEx);

//Include One File To Add phpBB3 functions required to try get this working
require($phpbb_root_path . 'includes/class_garage_custom_fields.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);

//Setup Some Capaturing Of POST vars..
$mode = (isset($HTTP_POST_VARS['add'])) ? 'create' : request_var('mode', '');
$mode = (empty($mode)) ? 'manage': $mode;
$submit = (isset($HTTP_POST_VARS['submit'])) ? true : false;
$create = (isset($HTTP_POST_VARS['create'])) ? true : false;
$error = $notify = array();

// Define some default values for each field type
$default_values = array(
	FIELD_STRING	=> array('field_length' => 10, 'field_minlen' => 0, 'field_maxlen' => 20, 'field_validation' => '.*', 'field_novalue' => '', 'field_default_value' => ''),
	FIELD_TEXT	=> array('field_length' => '5|80', 'field_minlen' => 0, 'field_maxlen' => 1000, 'field_validation' => '.*', 'field_novalue' => '', 'field_default_value' => ''),
	FIELD_INT	=> array('field_length' => 5, 'field_minlen' => 0, 'field_maxlen' => 100, 'field_validation' => '', 'field_novalue' => 0, 'field_default_value' => 0),
	FIELD_DATE	=> array('field_length' => 10, 'field_minlen' => 10, 'field_maxlen' => 10, 'field_validation' => '', 'field_novalue' => ' 0- 0-   0', 'field_default_value' => ' 0- 0-   0'),
	FIELD_BOOL		=> array('field_length' => 1, 'field_minlen' => 0, 'field_maxlen' => 0, 'field_validation' => '', 'field_novalue' => 0, 'field_default_value' => 0),
	FIELD_DROPDOWN	=> array('field_length' => 0, 'field_minlen' => 0, 'field_maxlen' => 5, 'field_validation' => '', 'field_novalue' => 0, 'field_default_value' => 0),
);

$cp = new custom_vehicle_fields_admin();

// Build Language array
// Based on this, we decide which elements need to be edited later and which language items are missing
$lang_defs = array();

if ($mode == '')
{
	echo "Invalid Mode";
}

if ($mode == 'create' || $mode == 'edit')
{
	$field_id = request_var('field_id', 0);
	$step = request_var('step', 1);
	$error = array();
	
	$submit = (isset($_REQUEST['next']) || isset($_REQUEST['prev'])) ? true : false;
	$update = (isset($_REQUEST['update'])) ? true : false;
	$save = (isset($_REQUEST['save'])) ? true : false;

	// We are editing... we need to grab basic things
	if ($mode == 'edit')
	{
		if (!$field_id)
		{
			message_die(GENERAL_ERROR, 'NO_FIELD_ID');
		}

		$sql = "SELECT l.*, f.*
			FROM " . GARAGE_VEHICLE_LANG_TABLE . " l, " . GARAGE_VEHICLE_FIELDS_TABLE . " f 
			WHERE l.lang_id = 1 
				AND f.field_id = $field_id
				AND l.field_id = f.field_id";
		$result = $db->sql_query($sql);
		$field_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$field_row)
		{
			trigger_error('FIELD_NOT_FOUND');
		}
		$field_type = $field_row['field_type'];

		// Get language entries
		$sql = "SELECT * FROM " . GARAGE_VEHICLE_FIELDS_LANG_TABLE . " 
			WHERE lang_id = 1
				AND field_id = $field_id
				ORDER BY option_id ASC";
		$result = $db->sql_query($sql);

		$lang_options = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$lang_options[$row['option_id']] = $row['value'];
		}
		$db->sql_freeresult($result);

		$field_row['pf_preview'] = '';

		$s_hidden_fields = '<input type="hidden" name="field_id" value="' . $field_id . '" />';
	}
	else
	{
		// We are adding a new field, define basic params
		$lang_options = $field_row = array();
	
		$field_type = request_var('field_type', 0);
		
		if (!$field_type)
		{
			trigger_error('NO_FIELD_TYPE');
		}

		$field_row = array_merge($default_values[$field_type], array(
			'field_ident'		=> request_var('field_ident', ''),
			'field_required'	=> 0,
			'field_hide'		=> 0,
			'field_no_view'		=> 0,
			'field_show_on_reg'	=> 0,
			'lang_name'		=> '',
			'lang_explain'		=> '',
			'lang_default_value'	=> '',
			'pf_preview'		=> '')
		);

		$s_hidden_fields = '<input type="hidden" name="field_type" value="' . $field_type . '" />';
	}

	// $exclude contains the data that we gather in each step
	$exclude = array(
		1	=> array('field_ident', 'lang_name', 'lang_explain'),
		2	=> array('field_length', 'pf_preview', 'field_maxlen', 'field_minlen', 'field_validation', 'field_novalue', 'field_default_value', 'field_required', 'field_show_on_reg', 'field_hide', 'field_no_view'),
		3	=> array('l_lang_name', 'l_lang_explain', 'l_lang_default_value', 'l_lang_options')
	);

	// Text-based fields require the lang_default_value to be excluded
	if ($field_type == FIELD_STRING || $field_type == FIELD_TEXT)
	{
		$exclude[1][] = 'lang_default_value';
	}

	// option-specific fields require lang_options to be excluded
	if ($field_type == FIELD_BOOL || $field_type == FIELD_DROPDOWN)
	{
		$exclude[1][] = 'lang_options';
	}

	$cp->vars['field_ident']	= request_var('field_ident', $field_row['field_ident']);
	$cp->vars['lang_name']		= request_var('field_ident', $field_row['lang_name']);
	$cp->vars['lang_explain']	= request_var('lang_explain', $field_row['lang_explain']);
	$cp->vars['lang_default_value']	= request_var('lang_default_value', $field_row['lang_default_value']);

	$options = request_var('lang_options', '');

	// If the user has submitted a form with options (i.e. dropdown field)
	if (!empty($options))
	{
		if (sizeof(explode("\n", $options)) == sizeof($lang_options) || $mode == 'create')
		{
			// The number of options in the field is equal to the number of options already in the database
			// Or we are creating a new dropdown list.
			$cp->vars['lang_options']	= explode("\n", $options);
		}
		else if ($mode == 'edit')
		{
			// Changing the number of options? (We remove and re-create the option fields)
			$cp->vars['lang_options']	= explode("\n", $options);
		}
	}
	else
	{
		$cp->vars['lang_options']	= $lang_options;
	}

	// step 2
	foreach ($exclude[2] as $key)
	{
		if ($key == 'field_required' || $key == 'field_show_on_reg' || $key == 'field_hide' || $key == 'field_no_view')
		{
			// Are we creating or editing a field?
			$var = (!$submit && $step == 1) ? $field_row[$key] : request_var($key, 0);
			
			// Damn checkboxes...
			if (!$submit && $step == 1)
			{
				$_REQUEST[$key] = $var;
			}
		}
		else
		{
			$var = request_var($key, $field_row[$key]);
		}

		// Manipulate the intended variables a little bit if needed
		if ($field_type == FIELD_DROPDOWN && $key == 'field_maxlen')
		{
			// Get the number of options if this key is 'field_maxlen'
			$var = sizeof(explode("\n", request_var('lang_options', '')));
		}

		if ($field_type == FIELD_TEXT && $key == 'field_length')
		{
			if (isset($_REQUEST['rows']))
			{
				$cp->vars['rows'] = request_var('rows', 0);
				$cp->vars['columns'] = request_var('columns', 0);
				$var = $cp->vars['rows'] . '|' . $cp->vars['columns'];
			}
			else
			{
				$row_col = explode('|', $var);
				$cp->vars['rows'] = $row_col[0];
				$cp->vars['columns'] = $row_col[1];
			}
		}

		if ($field_type == FIELD_DATE && $key == 'field_default_value')
		{
			if (isset($_REQUEST['always_now']) || $var == 'now')
			{
				$now = getdate();

				$cp->vars['field_default_value_day'] = $now['mday'];
				$cp->vars['field_default_value_month'] = $now['mon'];
				$cp->vars['field_default_value_year'] = $now['year'];
				$var = $_POST['field_default_value'] = 'now';
			}
			else
			{
				if (isset($_REQUEST['field_default_value_day']))
				{
					$cp->vars['field_default_value_day'] = request_var('field_default_value_day', 0);
					$cp->vars['field_default_value_month'] = request_var('field_default_value_month', 0);
					$cp->vars['field_default_value_year'] = request_var('field_default_value_year', 0);
					$var = $_POST['field_default_value'] = sprintf('%2d-%2d-%4d', $cp->vars['field_default_value_day'], $cp->vars['field_default_value_month'], $cp->vars['field_default_value_year']);
				}
				else
				{
					list($cp->vars['field_default_value_day'], $cp->vars['field_default_value_month'], $cp->vars['field_default_value_year']) = explode('-', $var);
				}
			}	
		}

		$cp->vars[$key] = $var;
	}

	// step 3 - all arrays
	if ($mode == 'edit')
	{
		// Get language entries
		$sql = 'SELECT * FROM ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . ' 
			WHERE lang_id <> ' . $lang_defs['iso'][$config['default_lang']] . "
				AND field_id = $field_id
			ORDER BY option_id ASC";
		$result = $db->sql_query($sql);

		$l_lang_options = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$l_lang_options[$row['lang_id']][$row['option_id']] = $row['value'];
		}
		$db->sql_freeresult($result);

	
		$sql = 'SELECT lang_id, lang_name, lang_explain, lang_default_value FROM ' . GARAGE_VEHICLE_LANG_TABLE . ' 
			WHERE lang_id <> ' . $lang_defs['iso'][$config['default_lang']] . "
				AND field_id = $field_id
			ORDER BY lang_id ASC";
		$result = $db->sql_query($sql);

		$l_lang_name = $l_lang_explain = $l_lang_default_value = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$l_lang_name[$row['lang_id']] = $row['lang_name'];
			$l_lang_explain[$row['lang_id']] = $row['lang_explain'];
			$l_lang_default_value[$row['lang_id']] = $row['lang_default_value'];
		}
		$db->sql_freeresult($result);
	}
	
	foreach ($exclude[3] as $key)
	{
		$cp->vars[$key] = request_var($key, '');

		if (!$cp->vars[$key] && $mode == 'edit')
		{
			$cp->vars[$key] = $$key;
		}
		else if ($key == 'l_lang_options' && sizeof($cp->vars[$key]) > 1)
		{
			foreach ($cp->vars[$key] as $lang_id => $options)
			{
				$cp->vars[$key][$lang_id] = explode("\n", $options);
			}
		}
	}

	if ($submit && $step == 1)
	{
		// Check values for step 1
		if ($cp->vars['field_ident'] == '')
		{
			$error[] = $lang['EMPTY_FIELD_IDENT'];
		}

		if (!preg_match('/^[a-z_]+$/', $cp->vars['field_ident']))
		{
			$error[] = $lang['INVALID_CHARS_FIELD_IDENT'];
		}

		if ($cp->vars['lang_name'] == '')
		{
			$error[] = $lang['EMPTY_USER_FIELD_IDENT'];
		}

		if ($field_type == FIELD_BOOL || $field_type == FIELD_DROPDOWN)
		{
			if (!sizeof($cp->vars['lang_options']))
			{
				$error[] = $lang['NO_FIELD_ENTRIES'];
			}
		}	
	}

	$user_error = false;
	if ($update && $step == 2)
	{
		// Validate Field
		$user_error = $cp->validate_profile_field($field_type, $cp->vars['pf_preview'], $cp->vars);
	}

	$step = (isset($_REQUEST['next'])) ? $step + 1 : ((isset($_REQUEST['prev'])) ? $step - 1 : $step);

	if (sizeof($error))
	{
		$step--;
		$submit = false;
	}

	if (isset($_REQUEST['prev']) || isset($_REQUEST['next']))
	{
		$update = false;
		$pf_preview = '';
		unset($_REQUEST['pf_preview']);
	}

	// Build up the specific hidden fields
	foreach ($exclude as $num => $key_ary)
	{
		if ($num == $step)
		{
			continue;
		}

		$s_hidden_fields .= build_hidden_fields($key_ary);
	}

	if (!sizeof($error))
	{
		if ($step == 3 || $save)
		{
			save_profile_field($field_type, $mode);
		}
	}

?>
	
	<p><?php echo $lang['STEP_' . $step . '_EXPLAIN_' . strtoupper($mode)]; ?></p>

	<form name="add_profile_field" method="post" action="admin_garage_vehicle_fields.<?php echo "$phpEx?$SID&amp;mode=$mode&amp;step=$step"; ?>">
	<table class="forumline" cellspacing="1" cellpadding="4" border="0" align="center" width="99%">
	<tr>
		<th align="center" colspan="2"><?php echo $lang['STEP_' . $step . '_TITLE_' . strtoupper($mode)]; ?></th>
	</tr>
<?php

	if (sizeof($error))
	{

?>
	<tr>
		<td class="row3" colspan="2" align="center"><span style="color:red"><?php echo implode('<br />', $error); ?></span></td>
	</tr>
<?php

	}

	// Now go through the steps
	switch ($step)
	{
		// Create basic options - only small differences between field types
		case 1: 
	
			// Build common create options
?>
			<tr>
				<td class="row1"><b><?php echo $lang['FIELD_TYPE']; ?>: </b><br /><span class="gensmall"><?php echo $lang['FIELD_TYPE_EXPLAIN']; ?></span></td>
				<td class="row2"><b><?php echo $lang['FIELD_' . strtoupper($cp->profile_types[$field_type])]; ?></b></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['FIELD_IDENT']; ?>: </b><br /><span class="gensmall"><?php echo $lang['FIELD_IDENT_EXPLAIN']; ?></span></td>
				<td class="row2"><input class="post" type="text" name="field_ident" size="20" value="<?php echo $cp->vars['field_ident']; ?>" /></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['USER_FIELD_NAME']; ?>: </b></td>
				<td class="row2"><input class="post" type="text" name="lang_name" size="20" value="<?php echo $cp->vars['lang_name']; ?>" /></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['FIELD_DESCRIPTION']; ?>: </b><br /><span class="gensmall"><?php echo $lang['FIELD_DESCRIPTION_EXPLAIN']; ?></span></td>
				<td class="row2"><textarea name="lang_explain" rows="3" cols="80"><?php echo $cp->vars['lang_explain']; ?></textarea></td>
			</tr>
<?php
			// String and Text needs to set default values here...
			if ($field_type == FIELD_STRING || $field_type == FIELD_TEXT)
			{
?>
				<tr>
					<td class="row1"><b><?php echo $lang['DEFAULT_VALUE']; ?>: </b><br /><span class="gensmall"><?php echo $lang[strtoupper($cp->profile_types[$field_type]) . '_DEFAULT_VALUE_EXPLAIN']; ?></span></td>
					<td class="row2"><?php echo ($field_type == FIELD_STRING) ? '<input class="post" type="text" name="lang_default_value" size="20" value="' . $cp->vars['lang_default_value'] . '" />' : '<textarea name="lang_default_value" rows="5" cols="80">' . $cp->vars['lang_default_value'] . '</textarea>'; ?></td>
				</tr>
<?php
			}
			
			if ($field_type == FIELD_BOOL || $field_type == FIELD_DROPDOWN)
			{
				// Initialize these array elements if we are creating a new field
				if (!sizeof($cp->vars['lang_options']))
				{
					if ($field_type == FIELD_BOOL)
					{
						// No options have been defined for a boolean field.
						$cp->vars['lang_options'][0] = '';
						$cp->vars['lang_options'][1] = '';
					}
					else
					{
						// No options have been defined for the dropdown menu
						$cp->vars['lang_options'] = array();
					}
				}
?>
				<tr>
					<td class="row1"><b><?php echo $lang['ENTRIES']; ?>: </b><br /><span class="gensmall"><?php echo $lang[strtoupper($cp->profile_types[$field_type]) . '_ENTRIES_EXPLAIN']; ?></span></td>
					<td class="row2"><?php echo ($field_type == FIELD_DROPDOWN) ? '<textarea name="lang_options" rows="5" cols="80">' . implode("\n", $cp->vars['lang_options']) . '</textarea>' : '<table border=0><tr><td><input name="lang_options[0]" size="20" value="' . $cp->vars['lang_options'][0] . '" class="post" /></td><td>[ ' . $lang['FIRST_OPTION'] . ' ]</td></tr><tr><td><input name="lang_options[1]" size="20" value="' . $cp->vars['lang_options'][1] . '" class="post" /></td><td>[ ' . $lang['SECOND_OPTION'] . ' ]</td></tr></table>'; ?></td>
				</tr>
<?php
			}
?>
			<tr>
				<td width="100%" colspan="2" class="cat" align="right"><input class="btnlite" type="submit" name="next" value="<?php echo $lang['PROFILE_TYPE_OPTIONS']; ?>" /></td>
			</tr>
			<?php echo $s_hidden_fields; ?>
			</table>
			</form>
<?php
			break;

		case 2:
?>
			<tr>
				<td class="row1"><b><?php echo $lang['REQUIRED_FIELD']; ?></b><br /><span class="gensmall"><?php echo $lang['REQUIRED_FIELD_EXPLAIN']; ?></span></td>
				<td class="row2"><input type="checkbox" name="field_required" value="1"<?php echo (($cp->vars['field_required']) ? ' checked="checked"' : ''); ?> /></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['DISPLAY_AT_REGISTRATION']; ?></b></td>
				<td class="row2"><input type="checkbox" name="field_show_on_reg" value="1"<?php echo (($cp->vars['field_show_on_reg']) ? ' checked="checked"' : ''); ?> /></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['HIDE_PROFILE_FIELD']; ?></b><br /><span class="gensmall"><?php echo $lang['HIDE_PROFILE_FIELD_EXPLAIN']; ?></span></td>
				<td class="row2"><input type="checkbox" name="field_hide" value="1"<?php echo (($cp->vars['field_hide']) ? ' checked="checked"' : ''); ?> /></td>
			</tr>
			<tr>
				<td class="row1"><b><?php echo $lang['EXCLUDE_FROM_VIEW']; ?></b><br /><span class="gensmall"><?php echo $lang['EXCLUDE_FROM_VIEW_EXPLAIN']; ?></span></td>
				<td class="row2"><input type="checkbox" name="field_no_view" value="1"<?php echo (($cp->vars['field_no_view']) ? ' checked="checked"' : ''); ?> /></td>
			</tr>
			
<?php
			// Build options based on profile type
			$function = 'get_' . $cp->profile_types[$field_type] . '_options';
			$options = $cp->$function();
			foreach ($options as $num => $option_ary)
			{
?>
				<tr>
					<td class="row1"><b><?php echo $option_ary['TITLE']; ?>: </b><?php echo (isset($option_ary['EXPLAIN'])) ? '<br /><span class="gensmall">' . $option_ary['EXPLAIN'] . '</span>' : ''; ?></td>
					<td class="row2"><?php echo $option_ary['FIELD']; ?></td>
				</tr>
<?php
			}
?>
			<tr>
				<td width="100%" colspan="2" class="cat"><table border="0" width="100%"><tr><td align="left"><input class="btnlite" type="submit" name="prev" value="<?php echo $lang['PROFILE_BASIC_OPTIONS']; ?>" /></td><td align="right"><input class="btnlite" type="submit" name="update" value="<?php echo $lang['UPDATE_PREVIEW']; ?>" />&nbsp;<input class="btnmain" type="submit" name="next" value="<?php echo $lang['SAVE']; ?>" /></td></tr></table></td>
			</tr>
			<?php echo $s_hidden_fields; ?>
			</table>

			<br /><br />
			<table class="forumline" cellspacing="1" cellpadding="4" border="0" align="center" width="99%">
			<tr>
				<th align="center" colspan="2"><?php echo $lang['PREVIEW_PROFILE_FIELD']; ?></th>
			</tr>
<?php 
			if (!empty($user_error) || $update) 
			{
				// If not and only showing common error messages, use this one
				switch ($user_error)
				{
					case 'FIELD_INVALID_DATE':
					case 'FIELD_REQUIRED':
						$user_error = sprintf($lang[$user_error], $cp->vars['lang_name']);
						break;
					case 'FIELD_TOO_SHORT':
					case 'FIELD_TOO_SMALL':
						$user_error = sprintf($lang[$user_error], $cp->vars['lang_name'], $cp->vars['field_minlen']);
						break;
					case 'FIELD_TOO_LONG':
					case 'FIELD_TOO_LARGE':
						$user_error = sprintf($lang[$user_error], $cp->vars['lang_name'], $cp->vars['field_maxlen']);
						break;
					case 'FIELD_INVALID_CHARS':
						switch ($cp->vars['field_validation'])
						{
							case '[0-9]+':
								$user_error = sprintf($lang[$user_error . '_NUMBERS_ONLY'], $cp->vars['lang_name']);
								break;
							case '[\w]+':
								$user_error = sprintf($lang[$user_error . '_ALPHA_ONLY'], $cp->vars['lang_name']);
								break;
							case '[\w_\+\. \-\[\]]+':
								$user_error = sprintf($lang[$user_error . '_SPACERS_ONLY'], $cp->vars['lang_name']);
								break;
						}

					default:
						$user_error = '';
				}

?>				<tr>
					<td class="row3" colspan="2"><?php echo (!empty($user_error)) ? '<span style="color:red">' . $user_error . '</span>' : '<span style="color:green">' . $lang['EVERYTHING_OK'] . '</span>'; ?></td>
				</tr>
<?php
			}
			
			$field_data = array(
				'lang_name'		=> $cp->vars['lang_name'],
				'lang_explain'		=> $cp->vars['lang_explain'],
				'lang_id'		=> 1,
				'field_id'		=> 1,

				'lang_default_value'	=> $cp->vars['lang_default_value'],
				'field_default_value'	=> $cp->vars['field_default_value'],
				'field_ident'		=> 'preview',
				'field_type'		=> $field_type,

				'field_length'		=> $cp->vars['field_length'],
				'field_maxlen'		=> $cp->vars['field_maxlen'],
				'lang_options'		=> $cp->vars['lang_options']
			);

			preview_field($field_data);
?>			
			<tr>
				<td width="100%" colspan="2" class="cat"><input class="btnlite" type="submit" name="update" value="<?php echo $lang['SUBMIT']; ?>" /></td>
			</tr>
			</table>
			</form>
<?php
			break;
		//End Of Cases
	}
}

// Delete field
if ($mode == 'delete')
{
	$confirm = (isset($_POST['confirm'])) ? true : false;
	$cancel = (isset($_POST['cancel'])) ? true : false;
	$field_id = request_var('field_id', 0);

	if (!$field_id)
	{
		trigger_error('INVALID_MODE');
	}

	if ($confirm)
	{
		$sql = 'SELECT field_ident 
			FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . " 
			WHERE field_id = $field_id";
		$result = $db->sql_query($sql);
		$field_ident = $db->sql_fetchfield('field_ident', 0, $result);
		$db->sql_freeresult($result);

		$db->sql_query('DELETE FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . " WHERE field_id = $field_id");
		$db->sql_query('DELETE FROM ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . " WHERE field_id = $field_id");
		$db->sql_query('DELETE FROM ' . GARAGE_VEHICLE_LANG_TABLE . " WHERE field_id = $field_id");
		$db->sql_query('ALTER TABLE ' . GARAGE_VEHICLE_FIELDS_DATA_TABLE . " DROP $field_ident");

		$order = 0;

		$sql = 'SELECT *
			FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . '
			ORDER BY field_order';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$order++;
			if ($row['field_order'] != $order)
			{
				$sql = 'UPDATE ' . GARAGE_VEHICLE_FIELDS_TABLE . " 
					SET field_order = $order 
					WHERE field_id = {$row['field_id']}";
				$db->sql_query($sql);
			}
		}

		trigger_error('REMOVED_PROFILE_FIELD');
	}
	else if (!$cancel)
	{
		$l_message = '<form method="post" action="admin_garage_vehicle_fields.' . $phpEx .'?'. $SID . '&amp;mode=delete&amp;field_id=' . $field_id . '">' . $lang['CONFIRM_DELETE_PROFILE_FIELD'] . '<br /><br /><input class="btnlite" type="submit" name="confirm" value="' . $lang['YES'] . '" />&nbsp;&nbsp;<input class="btnlite" type="submit" name="cancel" value="' . $lang['NO'] . '" /></form>';

		$template->set_filenames(array(
			'body' => 'admin/garage_message.tpl')
		);

		$text = "<br /><b>$l_message</b><br />";

		$template->assign_vars(array(
			'ALIGN' => 'center',
			'TEXT' => "<br /><br /><span class=\"gen\" align=\"center\">$text</span><br /><br />")
		);

		$template->pparse('body');

		adm_page_footer();

	}
	
	$mode = 'manage';
}

if ($mode == 'activate')
{
	$field_id = request_var('field_id', 0);

	if (!$field_id)
	{
		trigger_error('INVALID_MODE');
	}
	
	$sql = 'UPDATE ' . GARAGE_VEHICLE_FIELDS_TABLE . " 
		SET field_active = 1 
		WHERE field_id = $field_id";
	$db->sql_query($sql);

	$sql = 'SELECT field_ident 
		FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . " 
		WHERE field_id = $field_id";
	$result = $db->sql_query($sql);
	$field_ident = $db->sql_fetchfield('field_ident', 0, $result);
	$db->sql_freeresult($result);

	$template->set_filenames(array(
		'body' => 'admin/garage_message.tpl')
	);

	$text = "<br /><b>".$lang['PROFILE_FIELD_ACTIVATED']."</b><br />";

	$template->assign_vars(array(
		'ALIGN' => 'center',
		'META' => '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_vehicle_fields.$phpEx") . '">',
		'TEXT' => "<br /><br /><span class=\"gen\" >$text</span><br /><br />")
	);

	$template->pparse('body');
}

if ($mode == 'deactivate')
{
	$field_id = request_var('field_id', 0);

	if (!$field_id)
	{
		trigger_error('INVALID_MODE');
	}
	
	$sql = 'UPDATE ' . GARAGE_VEHICLE_FIELDS_TABLE . "
		SET field_active = 0 
		WHERE field_id = $field_id";
	$db->sql_query($sql);

	$sql = 'SELECT field_ident 
		FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . " 
		WHERE field_id = $field_id";
	$result = $db->sql_query($sql);
	$field_ident = $db->sql_fetchfield('field_ident', 0, $result);
	$db->sql_freeresult($result);

	$template->set_filenames(array(
		'body' => 'admin/garage_message.tpl')
	);

	$text = "<br /><b>".$lang['PROFILE_FIELD_DEACTIVATED']."</b><br />";

	$template->assign_vars(array(
		'ALIGN' => 'center',
		'META' => '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_vehicle_fields.$phpEx") . '">',
		'TEXT' => "<br /><br /><span class=\"gen\" align=\"center\">$text</span><br /><br />")
	);

	$template->pparse('body');
}

if ($mode == 'move_up' || $mode == 'move_down')
{
	$field_order = request_var('order', 0);
	$order_total = $field_order * 2 + (($mode == 'move_up') ? -1 : 1);

	$sql = 'UPDATE ' . GARAGE_VEHICLE_FIELDS_TABLE . "
		SET field_order = $order_total - field_order
		WHERE field_order IN ($field_order, " . (($mode == 'move_up') ? $field_order - 1 : $field_order + 1) . ')';
	$db->sql_query($sql);

	$mode = 'manage';
}

if ($mode == 'manage')
{
?>
	<form name="profile_fields" method="post" action="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>">
	<table class="forumline" cellspacing="1" cellpadding="4" border="0" align="center" width="99%">
	<tr> 
		<th nowrap="nowrap"><?php echo $lang['FIELD_IDENT']; ?></th>
		<th nowrap="nowrap"><?php echo $lang['FIELD_TYPE']; ?></th>
		<th colspan="3" nowrap="nowrap"><?php echo $lang['OPTIONS']; ?></th>
		<th nowrap="nowrap"><?php echo $lang['REORDER']; ?></th>
	</tr>
<?php
	$sql = 'SELECT *
		FROM ' . GARAGE_VEHICLE_FIELDS_TABLE . '
		ORDER BY field_order';
	$result = $db->sql_query($sql);

	$row_class = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$row_class = ($row_class == 'row1') ? 'row2' : 'row1';

		$active_lang = (!$row['field_active']) ? 'ACTIVATE' : 'DEACTIVATE';
		$active_value = (!$row['field_active']) ? 'activate' : 'deactivate';
		$id = $row['field_id'];
?>
	<tr>
		<td class="<?php echo $row_class; ?>"><?php echo $row['field_ident']; ?></td>
		<td class="<?php echo $row_class; ?>"><?php echo $lang['FIELD_' . strtoupper($cp->profile_types[$row['field_type']])]; ?></td>
		<td class="<?php echo $row_class; ?>"><a href="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>&amp;mode=<?php echo $active_value; ?>&amp;field_id=<?php echo $id; ?>"><?php echo $lang[$active_lang]; ?></a></td>
		<td class="<?php echo $row_class; ?>"><a href="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>&amp;mode=edit&amp;field_id=<?php echo $id; ?>"><?php echo ((sizeof($lang_defs['diff'][$row['field_id']])) ? '<span style="color:red">' . $lang['EDIT'] . '</span>' : $lang['EDIT']) . '</a>' . ((sizeof($lang_defs['diff'][$row['field_id']])) ? '</span>' : ''); ?></td>
		<td class="<?php echo $row_class; ?>"><a href="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>&amp;mode=delete&amp;field_id=<?php echo $id; ?>"><?php echo $lang['DELETE']; ?></a></td>
		<td class="<?php echo $row_class; ?>" align="center"><a href="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>&amp;mode=move_up&amp;order=<?php echo $row['field_order']; ?>"><?php echo $lang['MOVE_UP']; ?></a> | <a href="admin_garage_vehicle_fields.<?php echo $phpEx .'?'. $SID; ?>&amp;mode=move_down&amp;order=<?php echo $row['field_order']; ?>"><?php echo $lang['MOVE_DOWN']; ?></a></td>
	</tr>
<?php
	}
	$db->sql_freeresult($result);

	$s_select_type = '';
	foreach ($cp->profile_types as $key => $value)
	{
		$s_select_type .= '<option value="' . $key . '">' . $lang['FIELD_' . strtoupper($value)] . '</option>';
	}
?>
	<tr>
		<td class="cat" colspan="7"><input class="post" type="text" name="field_ident" size="20" /> <select name="field_type"><?php echo $s_select_type; ?></select> <input class="btnlite" type="submit" name="add" value="<?php echo $lang['CREATE_NEW_FIELD']; ?>" /></td>
	</tr>
	</table>
	</form>
<?php
}

adm_page_footer();


function preview_field($field_data)
{
	global $cp;

	$field = $cp->process_field_row('preview', $field_data);

?>
	<tr> 
		<td class="row1"><b><?php echo $field_data['lang_name']; ?>: </b><?php echo (!empty($field_data['lang_explain'])) ? '<br /><span class="gensmall">' . $field_data['lang_explain'] . '</span>' : ''; ?></td> 
		<td class="row2"><?php echo $field; ?></td> 
	</tr>
<?php
}

function save_profile_field($field_type, $mode = 'create')
{
	global $cp, $db,  $lang, $lang_defs, $template, $phpEx;

	$field_id = request_var('field_id', 0);

	// Collect all informations, if something is going wrong, abort the operation
	$profile_sql = $profile_lang = $empty_lang = $profile_lang_fields = array();

	$default_lang_id = 1;

	if ($mode == 'create')
	{
		$result = $db->sql_query('SELECT MAX(field_order) as max_field_order FROM ' . GARAGE_VEHICLE_FIELDS_TABLE);
		$new_field_order = (int) $db->sql_fetchfield('max_field_order', 0, $result);
		$db->sql_freeresult($result);
		
		$field_ident = $cp->vars['field_ident'];
	}

	// Save the field
	$profile_fields = array(
		'field_length'		=> $cp->vars['field_length'],
		'field_minlen'		=> $cp->vars['field_minlen'],
		'field_maxlen'		=> $cp->vars['field_maxlen'],
		'field_novalue'		=> $cp->vars['field_novalue'],
		'field_default_value'	=> $cp->vars['field_default_value'],
		'field_validation'	=> $cp->vars['field_validation'],
		'field_required'	=> $cp->vars['field_required'],
		'field_show_on_reg'	=> $cp->vars['field_show_on_reg'],
		'field_hide'		=> $cp->vars['field_hide'],
		'field_no_view'		=> $cp->vars['field_no_view']
	);

	if ($mode == 'create')
	{
		$profile_fields += array(
			'field_type'		=> $field_type,
			'field_ident'		=> $field_ident,
			'field_order'		=> $new_field_order + 1,
			'field_active'		=> 1
		);

		$sql = "INSERT INTO " . GARAGE_VEHICLE_FIELDS_TABLE . " SET field_length = '".$profile_fields['field_length']."', field_minlen = '".$profile_fields['field_minlen']."', field_maxlen = '".$profile_fields['field_maxlen']."', field_novalue = '".$profile_fields['field_novalue']."', field_default_value = '".$profile_fields['field_default_value']."', field_validation = '".$profile_fields['field_validation']."', field_required = '".$profile_fields['field_required']."', field_show_on_reg = '".$profile_fields['field_show_on_reg']."', field_hide = '".$profile_fields['field_hide']."', field_no_view = '".$profile_fields['field_no_view']."', field_type = '".$profile_fields['field_type']."', field_ident = '".$profile_fields['field_ident']."', field_order = '".$profile_fields['field_order']."', field_active = '".$profile_fields['field_active']."'";

		$db->sql_query($sql);

		$field_id = $db->sql_nextid();
	}
	else
	{
		$sql = "UPDATE " . GARAGE_VEHICLE_FIELDS_TABLE . " SET field_length = '".$profile_fields['field_length']."', field_minlen = '".$profile_fields['field_minlen']."', field_maxlen = '".$profile_fields['field_maxlen']."', field_novalue = '".$profile_fields['field_novalue']."', field_default_value = '".$profile_fields['field_default_value']."', field_validation = '".$profile_fields['field_validation']."', field_required = '".$profile_fields['field_required']."', field_show_on_reg = '".$profile_fields['field_show_on_reg']."', field_hide = '".$profile_fields['field_hide']."', field_no_view = '".$profile_fields['field_no_view']."' 
			WHERE field_id = ".$field_id."";
		$db->sql_query();
	}
		
	if ($mode == 'create')
	{
		// We are defining the biggest common value, because of the possibility to edit the min/max values of each field.
		$sql = 'ALTER TABLE ' . GARAGE_VEHICLE_FIELDS_DATA_TABLE . " ADD $field_ident ";
		switch ($field_type)
		{
			case FIELD_STRING:
				$sql .= ' VARCHAR(255) DEFAULT NULL NULL';
				break;

			case FIELD_DATE:
				$sql .= 'VARCHAR(10) DEFAULT NULL NULL';
				break;

			case FIELD_TEXT:
				$sql .= "TEXT NULL,
					ADD {$field_ident}_bbcode_uid VARCHAR(5) NOT NULL,
					ADD {$field_ident}_bbcode_bitfield INT(11) UNSIGNED";
				break;

			case FIELD_BOOL:
				$sql .= 'TINYINT(2) DEFAULT NULL NULL';
				break;
		
			case FIELD_DROPDOWN:
				$sql .= 'MEDIUMINT(8) DEFAULT NULL NULL';
				break;

			case FIELD_INT:
				$sql .= 'BIGINT(20) DEFAULT NULL NULL';
				break;
		}
		$profile_sql[] = $sql;
	}

	$sql_ary = array(
		'lang_name'		=> $cp->vars['lang_name'],
		'lang_explain'		=> $cp->vars['lang_explain'],
		'lang_default_value'	=> $cp->vars['lang_default_value']
	);

	if ($mode == 'create')
	{
		$sql_ary['field_id'] = $field_id;
		$sql_ary['lang_id'] = $default_lang_id;
	
		$profile_sql[] = "INSERT INTO " . GARAGE_VEHICLE_LANG_TABLE . " SET lang_name = '".$sql_ary['lang_name'].", lang_explain = '".$sql_ary['lang_explain'].", lang_default_value = '".$sql_ary['lang_default_value'].", field_id = '".$sql_ary['field_id'].", lang_id = '".$sql_ary['defatul_lang_id'].";";
	}
	else
	{
		update_insert(GARAGE_VEHICLE_LANG_TABLE, $sql_ary, array('field_id' => $field_id, 'lang_id' => $default_lang_id));
	}

	if (sizeof($cp->vars['l_lang_name']))
	{
		foreach ($cp->vars['l_lang_name'] as $lang_id => $data)
		{
			if (($cp->vars['lang_name'] != '' && $cp->vars['l_lang_name'][$lang_id] == '')
				|| ($cp->vars['lang_explain'] != '' && $cp->vars['l_lang_explain'][$lang_id] == '')
				|| ($cp->vars['lang_default_value'] != '' && $cp->vars['l_lang_default_value'][$lang_id] == ''))
			{
				$empty_lang[$lang_id] = true;
				break;
			}

			if (!isset($empty_lang[$lang_id]))
			{
				$profile_lang[] = array(
					'field_id'		=> $field_id,
					'lang_id'		=> $lang_id,
					'lang_name'		=> $cp->vars['l_lang_name'][$lang_id],
					'lang_explain'	=> (isset($cp->vars['l_lang_explain'][$lang_id])) ? $cp->vars['l_lang_explain'][$lang_id] : '',
					'lang_default_value'	=> (isset($cp->vars['l_lang_default_value'][$lang_id])) ? $cp->vars['l_lang_default_value'][$lang_id] : ''
				);
			}
		}

		foreach ($empty_lang as $lang_id => $NULL)
		{
			$sql = 'DELETE FROM ' . GARAGE_VEHICLE_LANG_TABLE . " 
				WHERE field_id = $field_id
				AND lang_id = " . (int) $lang_id;
			$db->sql_query($sql);
		}
	}

	$cp->vars['l_lang_name']		= request_var('l_lang_name', '');
	$cp->vars['l_lang_explain']		= request_var('l_lang_explain', '');
	$cp->vars['l_lang_default_value']	= request_var('l_lang_default_value', '');
	$cp->vars['l_lang_options']		= request_var('l_lang_options', '');

	if (!empty($cp->vars['lang_options']))
	{
		if (!is_array($cp->vars['lang_options']))
		{
			$cp->vars['lang_options'] = explode("\n", $cp->vars['lang_options']);
		}

		if ($mode != 'create')
		{
			$sql = 'DELETE FROM ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . " 
				WHERE field_id = $field_id
					AND lang_id = " . (int) $default_lang_id;
			$db->sql_query($sql);
		}
		
		foreach ($cp->vars['lang_options'] as $option_id => $value)
		{
			$sql_ary = array(
				'field_type'	=> (int) $field_type,
				'value'			=> $value
			);

			if ($mode == 'create')
			{
				$sql_ary['field_id'] = $field_id;
				$sql_ary['lang_id'] = $default_lang_id;
				$sql_ary['option_id'] = (int) $option_id;

				$profile_sql[] = 'INSERT INTO ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			}
			else
			{
				update_insert(GARAGE_VEHICLE_FIELDS_LANG_TABLE, $sql_ary, array(
					'field_id' => $field_id,
					'lang_id' => (int) $default_lang_id,
					'option_id' => (int) $option_id)
				);
			}
		}
	}

	if (is_array($cp->vars['l_lang_options']) && sizeof($cp->vars['l_lang_options']))
	{
		$empty_lang = array();

		foreach ($cp->vars['l_lang_options'] as $lang_id => $lang_ary)
		{
			if (!is_array($lang_ary))
			{
				$lang_ary = explode("\n", $lang_ary);
			}

			if (sizeof($lang_ary) != sizeof($cp->vars['lang_options']))
			{
				$empty_lang[$lang_id] = true;
			}

			if (!isset($empty_lang[$lang_id]))
			{
				if ($mode != 'create')
				{
					$sql = 'DELETE FROM ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . " 
						WHERE field_id = $field_id
						AND lang_id = " . (int) $lang_id;
					$db->sql_query($sql);
				}

				foreach ($lang_ary as $option_id => $value)
				{
					$profile_lang_fields[] = array(
						'field_id'		=> (int) $field_id,
						'lang_id'		=> (int) $lang_id,
						'option_id'		=> (int) $option_id,
						'field_type'	=> (int) $field_type,
						'value'			=> $value
					);
				}
			}
		}

		foreach ($empty_lang as $lang_id => $NULL)
		{
			$sql = 'DELETE FROM ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . " 
				WHERE field_id = $field_id
				AND lang_id = " . (int) $lang_id;
			$db->sql_query($sql);
		}
	}

	foreach ($profile_lang as $sql)
	{
		if ($mode == 'create')
		{
			$profile_sql[] = 'INSERT INTO ' . GARAGE_VEHICLE_LANG_TABLE . ' ' . $db->sql_build_array('INSERT', $sql);
		}
		else
		{
			$lang_id = $sql['lang_id'];
			unset($sql['lang_id'], $sql['field_id']);
			update_insert(GARAGE_VEHICLE_LANG_TABLE, $sql, array('lang_id' => (int) $lang_id, 'field_id' => $field_id));
		}
	}

	if (sizeof($profile_lang_fields))
	{
		foreach ($profile_lang_fields as $sql)
		{
			if ($mode == 'create')
			{
				$profile_sql[] = 'INSERT INTO ' . GARAGE_VEHICLE_FIELDS_LANG_TABLE . ' ' . $db->sql_build_array('INSERT', $sql);
			}
			else
			{
				$lang_id = $sql['lang_id'];
				$option_id = $sql['option_id'];
				unset($sql['lang_id'], $sql['field_id'], $sql['option_id']);
				update_insert(GARAGE_VEHICLE_FIELDS_LANG_TABLE, $sql, array(
					'lang_id'	=> $lang_id, 
					'field_id'	=> $field_id,
					'option_id'	=> $option_id)
				);
			}
		}
	}

	if ($mode == 'create')
	{
		foreach ($profile_sql as $sql)
		{
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Error Running SQL Query', '', __LINE__, __FILE__, $sql);
			}
		}
	}

	if ($mode == 'edit')
	{
		$text = "<br /><b>".$lang['CHANGED_PROFILE_FIELD']."</b><br />";
	}
	else
	{
		$text = "<br /><b>".$lang['ADDED_PROFILE_FIELD']."</b><br />";
	}

	$template->set_filenames(array(
		'body' => 'admin/garage_message.tpl')
	);

	$template->assign_vars(array(
		'ALIGN' => 'center',
		'META' => '<meta http-equiv="refresh" content="2;url=' . append_sid("admin_garage_vehicle_fields.$phpEx") . '">',
		'TEXT' => "<br /><br /><span class=\"gen\" align=\"center\">$text</span><br /><br />")
	);

	$template->pparse('body');

	adm_page_footer();
}

// Update, then insert if not successfull
function update_insert($table, $sql_ary, $where_fields)
{
	global $db;

	$where_sql = array();
	$check_key = '';
	foreach ($where_fields as $key => $value)
	{
		$check_key = (!$check_key) ? $key : $check_key;
		$where_sql[] = $key . ' = ' . ((is_string($value)) ? "'" . $db->sql_escape($value) . "'" : $value);
	}

	$sql = "SELECT $check_key 
		FROM $table
		WHERE " . implode(' AND ', $where_sql);
	$result = $db->sql_query($sql);
	
	if (!$db->sql_fetchrow($result))
	{
		$db->sql_freeresult($result);

		$sql_ary = array_merge($where_fields, $sql_ary);
		$db->sql_query("INSERT INTO $table " . $db->sql_build_array('INSERT', $sql_ary));
	}
	else
	{
		$db->sql_freeresult($result);
	
		$sql = "UPDATE $table SET " . $db->sql_build_array('UPDATE', $sql_ary) . ' 
			WHERE ' . implode(' AND ', $where_sql);
		$db->sql_query($sql);
	}
}

function build_hidden_fields($key_ary)
{
	$hidden_fields = '';

	foreach ($key_ary as $key)
	{
		$var = isset($_REQUEST[$key]) ? $_REQUEST[$key] : false;

		if ($var === false)
		{
			continue;
		}

		if (is_array($var))
		{
			foreach ($var as $num => $__var)
			{
				if (is_array($__var))
				{
					foreach ($__var as $_num => $___var)
					{
						$hidden_fields .= '<input type="hidden" name="' . $key . '[' . $num . '][' . $_num . ']" value="' . stripslashes(htmlspecialchars($___var)) . '" />' . "\n";
					}
				}
				else
				{
					$hidden_fields .= '<input type="hidden" name="' . $key . '[' . $num . ']" value="' . stripslashes(htmlspecialchars($__var)) . '" />' . "\n";
				}
			}
		}
		else
		{
			$hidden_fields .= '<input type="hidden" name="' . $key . '" value="' . stripslashes(htmlspecialchars($var)) . '" />' . "\n";
		}
	}
	return $hidden_fields;
}

?>
