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
	* Update/Create configuration setting
	* Taken from phpBB3 standard code and table changed 
	*
	* @param string $config_name config option to create or update
	* @param string $config_value value to use for creation or update
	* @param array $garage_config  single-dimensional array holding current garage configuratin
	*
	*/
	function set_config($config_name, $config_value, $garage_config)
	{
		global $db ;

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
