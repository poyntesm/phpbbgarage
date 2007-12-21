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
*/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

/**
* phpBB Garage Admin Class
* @package garage
*/
class garage_admin
{
	var $classname = "garage_admin";

	/**
	* Insert new modification category
	*
	* @param array $data sinlge-dimension array holding data to insert
	*
	*/
	function insert_category($data)
	{
		global $db;

		$sql = 'INSERT INTO ' . GARAGE_CATEGORIES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'title'		=> $data['title'],
			'field_order'	=> $data['field_order'])
		);

		$db->sql_query($sql);

		return;
	}

	/**
	* Count existing modification categories
	* 
	* @return int
	*/
	function count_categories()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(c.id) as total',
			'FROM'		=> array(
				GARAGE_CATEGORIES_TABLE	=> 'c',
			)
		));

		$result = $db->sql_query($sql);
        	$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];

		return $data['total'];
	}


	/**
	* Remove complete category
	*/
	function delete_category($category_id, $action_modifications = 'delete', $modifications_to_id = 0)
	{

		global $db, $user, $cache, $garage;

		$category_data = $garage->get_category($category_id);

		$errors = array();
		$log_action_modifications = $modifications_to_name = '';

		if ($action_modifications == 'delete')
		{
			$log_action_modifications = 'MODIFICATIONS';
			$errors = array_merge($errors, $this->delete_category_content($category_id));
		}
		else if ($action_modifications == 'move')
		{
			if (!$modifications_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_CATEGORY'];
			}
			else
			{
				$log_action_modifications = 'MOVE_MODIFICATIONS';

				$row = $garage->get_category($modifications_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_CATEGORY'];
				}
				else
				{
					$modifications_to_name = $row['title'];
					$errors = array_merge($errors, $this->move_category_content($category_id, $modifications_to_id));
					$errors = array_merge($errors, $this->delete_category_content($category_id));
				}
			}
		}

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete category content
	*/
	function delete_category_content($category_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage;

		include_once($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);

		$modifications = $garage_modification->get_modifications_by_category_id($category_id);

		for ($i = 0, $count = sizeof($modifications);$i < $count; $i++)
		{
			$garage_modification->delete_modification($modifications[$i]['id']);
		}

		$garage->delete_rows(GARAGE_CATEGORIES_TABLE, 'id', $category_id);

		return array();
	}

	/**
	* Move category content from one to another category
	*/
	function move_category_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODIFICATIONS_TABLE, 'category_id', $to_id, 'category_id', $from_id);

		return array();
	}

	/**
	* Update/Create configuration setting
	* Taken from phpBB3 standard code and table changed 
	*
	* @param string $config_name config option to create or update
	* @param string $config_value value to use for creation or update
	* @param array $garage_config  single-dimensional array holding current garage configuratin
	*
	*/
	function set_config($config_name, $config_value, $garage_config = null)
	{
		global $db ;

		if (empty($garage_config))
		{
			$sql = $db->sql_build_query('SELECT', 
				array(
				'SELECT'	=> 'c.config_name, c.config_value',
				'FROM'		=> array(
					GARAGE_CONFIG_TABLE	=> 'c',
				),
				'WHERE'		=> "c.config_name = '$config_name'",
			));

			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$garage_config[$row['config_name']] = $row['config_value'];
			$db->sql_freeresult($result);
		}

		$sql = 'UPDATE ' . GARAGE_CONFIG_TABLE . "
			SET config_value = '" . $db->sql_escape($config_value) . "'
			WHERE config_name = '" . $db->sql_escape($config_name) . "'";
		$db->sql_query($sql);

		if (!$db->sql_affectedrows() && !isset($garage_config[$config_name]))
		{
			$sql = 'INSERT INTO ' . GARAGE_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'config_name'	=> $config_name,
				'config_value'	=> $config_value));
			$db->sql_query($sql);
		}

		$garage_config[$config_name] = $config_value;
	}

	/**
	* Re-reading garage configuration option
	* 
	* @return array
	*/
	function sync_config()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'c.config_name, c.config_value',
			'FROM'		=> array(
				GARAGE_CONFIG_TABLE	=> 'c',
			)
		));

		$result = $db->sql_query($sql);
		while( $row = $db->sql_fetchrow($result) )
		{
			$garage_config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		return $garage_config;
	}
}
$garage_admin = new garage_admin();
?>
