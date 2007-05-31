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
* phpBB Garage Makes & Models Class
* @package garage
*/
class garage_model
{
	var $classname = "garage_model";

	/**
	* Insert new vehicle make
	*
	* @param array $data single-dimension array holding the data for new make
	*
	*/
	function insert_make($data)
	{
		global $db;

		$sql = 'INSERT INTO ' . GARAGE_MAKES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'make'	=> $data['make'])
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/**
	* Updates a existing make
	*
	* @param array $data single-deminsion array holding the data to update the make with
	*
	*/
	function update_make($data)
	{
		global $db;

		$update_sql = array(
			'make'	=> $data['make'],
		);

		$sql = 'UPDATE ' . GARAGE_MAKES_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['id'];


		$db->sql_query($sql);

		return;
	}

	/**
	* Return count of make. Used to make sure makes are unique
	*/
	function count_make($make)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(mk.id) as total',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'WHERE'		=>  "mk.make = '$make'"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/**
	* Return count of model associated with a make. Used to make sure models unique
	*
	* @param string $model model name
	* @param int $make_id make id for models
	*
	*/
	function count_model_in_make($model, $make_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'COUNT(md.id) as total',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
			),
			'WHERE'		=>  "md.model = '$model' AND md.make_id = $make_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$data['total'] = (empty($data['total'])) ? 0 : $data['total'];
		return $data['total'];
	}

	/**
	* Insert new vehicle model
	*
	* @param array $data single-dimension array holding the data for new model
	*
	*/
	function insert_model($data)
	{
		global $db;

		$sql = 'INSERT INTO ' . GARAGE_MODELS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'make_id'	=> $data['make_id'],
			'model'		=> $data['model'])
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/**
	* Updates a existing model
	*
	* @param array $data single-deminsion array holding the data to update the model with
	*
	*/
	function update_model($data)
	{
		global $db;

		$update_sql = array(
			'model'	=> $data['model'],
		);

		$sql = 'UPDATE ' . GARAGE_MODELS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = " . $data['id'];

		$db->sql_query($sql);

		return;
	}

	/**
	* Return data for specific make by id
	*
	* @param int $make_id make id to get data for
	*
	*/
	function get_make($make_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mk.id, mk.make',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'WHERE'		=>  "mk.id = $make_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return data for specif make by name
	*
	* @param int $make name to get data for
	*
	*/
	function get_make_by_name($make)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mk.id, mk.make',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'WHERE'		=>  "mk.make = '$make'"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of all makes
	*/
	function get_all_makes()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mk.id, mk.make, mk.pending',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'ORDER_BY'	=> 'mk.make'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of models from specific make
	*
	* @param int $make_id make id to get models for
	*
	*/
	function get_all_models_from_make($make_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.id, md.model, md.make_id, md.pending',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
			),
			'WHERE'		=>  "md.make_id = $make_id"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Get data for specific model
	*
	* @param int $model_id model id to get data for
	*
	*/
	function get_model($model_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.id, md.model, md.make_id',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
			),
			'WHERE'		=>  "md.id = $model_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of models from specific make
	*
	* @param int $make_id make id to get models for
	*
	*/
	function get_models_by_make($make_id)
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.id, md.model, md.make_id',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
			),
			'WHERE'		=>  "md.make_id = $make_id"
		));

		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of all pending makes
	*/
	function get_pending_makes()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mk.*',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'WHERE'		=>  "mk.pending = 1"
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/**
	* Return array of all pending models
	*/
	function get_pending_models()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.*, mk.make',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
				GARAGE_MAKES_TABLE		=> 'mk',
			),
			'WHERE'	=> 'md.pending = 1
					AND md.make_id = mk.id'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}


	/**
	* Delete a make
	*
	* @param int $make_id make id to delete
	* @param string $action_make delete|move linked vehicles
	* @param int $make_to_id move to make id
	*
	*/
	function delete_make($make_id, $action_make = 'delete', $make_to_id = 0)
	{
		global $db, $user, $cache, $garage;

		$make = $this->get_make($make_id);

		$errors = array();

		if ($action_make == 'delete')
		{
			$this->delete_make_content($make_id);
			add_log('admin', 'LOG_GARAGE_DELETE_MAKE_MODELS', $make['make']);
		}
		else if ($action_make == 'move')
		{
			if (!$make_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_MAKE'];
			}
			else
			{
				$row = $this->get_make($make_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_MAKE'];
				}
				else
				{
					$make_to_name = $row['make'];
					$from_name = $make['make'];
					$this->move_make_content($make_id, $make_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_MAKE', $from_name, $make_to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_MAKES_TABLE, 'id', $make_id);
		add_log('admin', 'LOG_GARAGE_MAKE', $make['make']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete make & linked vehicles
	*
	* @param int $make_id make id to delete all vehicles
	*
	*/
	function delete_make_content($make_id)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $garage, $garage_vehicle;

		$vehicles = $garage_vehicle->get_vehicles_by_make_id($make_id);
		for ($i = 0, $count = sizeof($vehicles);$i < $count; $i++)
		{
			$garage_vehicle->delete_vehicle($vehicles[$i]['id']);
		}
		$garage->delete_rows(GARAGE_MODELS_TABLE, 'make_id', $make_id);

		return;
	}

	/**
	* Move vehicles to new make
	*
	* @param int $from_id make id to move from
	* @param int $to_id make id to move to
	*
	*/
	function move_make_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODELS_TABLE, 'make_id', $to_id, 'make_id', $from_id);
		$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'make_id', $to_id, 'make_id', $from_id);

		return;
	}

	/**
	* Delete a model
	*
	* @param int $model_id model id to delete
	* @param string $action_model delete|move linked vehicles
	* @param int $model_to_id move to model id
	*
	*/
	function delete_model($model_id, $action_model = 'delete', $model_to_id = 0)
	{
		global $db, $user, $cache, $garage;

		$model = $this->get_model($model_id);

		$errors = array();

		if ($action_model == 'delete')
		{
			$this->delete_model_content($model_id);
			add_log('admin', 'LOG_GARAGE_DELETE_MODELS_MODELS', $model['model']);
		}
		else if ($action_model == 'move')
		{
			if (!$model_to_id)
			{
				$errors[] = $user->lang['NO_DESTINATION_MODEL'];
			}
			else
			{
				$row = $this->get_model($model_to_id);

				if (!$row)
				{
					$errors[] = $user->lang['NO_MODEL'];
				}
				else
				{
					$model_to_name = $row['model'];
					$from_name = $model['model'];
					$this->move_model_content($model_id, $model_to_id);
					add_log('admin', 'LOG_GARAGE_MOVED_MODEL', $from_name, $model_to_name);
				}
			}
		}

		$garage->delete_rows(GARAGE_MODELS_TABLE, 'id', $model_id);
		add_log('admin', 'LOG_GARAGE_MODEL', $model['model']);

		if (sizeof($errors))
		{
			return $errors;
		}
	}

	/**
	* Delete model & linked vehicles
	*
	* @param int $model_id model id to delete all vehicles
	*
	*/
	function delete_model_content($model_id)
	{
		global $garage, $garage_vehicle;

		$vehicles = $garage_vehicle->get_vehicles_by_model_id($model_id);
		for ($i = 0, $count = sizeof($vehicles);$i < $count; $i++)
		{
			$garage_vehicle->delete_vehicle($vehicles[$i]['id']);
		}
		$garage->delete_rows(GARAGE_MODELS_TABLE, 'id', $model_id);

		return;
	}

	/**
	* Move vehicles to new model
	*
	* @param int $from_id model id to move from
	* @param int $to_id model id to move to
	*
	*/
	function move_model_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'model_id', $to_id, 'model_id', $from_id);

		return;
	}


	/**
	* Approve makes
	*
	* @param array $id_list single-dimension array holding the make ids to approve
	*
	*/
	function approve_make($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_MAKES_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_makes"));
	}

	/**
	* Approve models
	*
	* @param array $id_list single-dimension array holding the model ids to approve
	*
	*/
	function approve_model($id_list)
	{
		global $phpbb_root_path, $phpEx, $garage;

		for($i = 0; $i < count($id_list); $i++)
		{
			$garage->update_single_field(GARAGE_MODELS_TABLE, 'pending', 0, 'id', $id_list[$i]);
		}

		redirect(append_sid("{$phpbb_root_path}mcp.$phpEx", "i=garage&amp;mode=unapproved_models"));
	}

}

$garage_model = new garage_model();

?>
