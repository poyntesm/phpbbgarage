<?php
/** 
*
* @package garage
* @version $Id: memberlist.php,v 1.207 2007/01/26 16:05:14 acydburn Exp $
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

class garage_model
{
	var $classname = "garage_model";

	/*========================================================================*/
	// Inserts Make Into DB
	// Usage: insert_make(array());
	/*========================================================================*/
	function insert_make($data)
	{
		global $db;

		$sql = 'INSERT INTO ' . GARAGE_MAKES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'make'	=> $data['make'])
		);

		$db->sql_query($sql);

		return $db->sql_nextid();
	}

	/*========================================================================*/
	// Update Model Into DB
	// Usage: update_make(array());
	/*========================================================================*/
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

	/*========================================================================*/
	// Count Makes With Certain Name
	// Usage: count_make('make');
	/*========================================================================*/
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

	/*========================================================================*/
	// Count Makes With Certain Name
	// Usage: count_make('make');
	/*========================================================================*/
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

	/*========================================================================*/
	// Inserts Model Into DB
	// Usage: insert_model(array());
	/*========================================================================*/
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

	/*========================================================================*/
	// Update Model Into DB
	// Usage: update_model(array());
	/*========================================================================*/
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

	/*========================================================================*/
	// Select Make Data From DB
	// Usage: get_make('make id');
	/*========================================================================*/
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

	/*========================================================================*/
	// Select Make Data From DB
	// Usage: get_make_by_name('make');
	/*========================================================================*/
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

	/*========================================================================*/
	// Select All Make Data From DB
	// Usage: get_all_makes();
	/*========================================================================*/
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

	/*========================================================================*/
	// Select All Model From One Make Data From DB
	// Usage: get_all_models_from_make();
	/*========================================================================*/
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

	/*========================================================================*/
	// Select All Model Data From DB
	// Usage: get_all_models();
	/*========================================================================*/
	function get_all_models()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.id as model_id, md.model, mk.id as make_id, mk.make, md.pending as model_pending, mk.pending as make_pending',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'mk.id = md.make_id'
				)
			),
			'ORDER_BY'	=>	'mk.make, md.model'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}

	/*========================================================================*/
	// Select Model Data From DB
	// Usage: get_model('model id');
	/*========================================================================*/
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

	/*========================================================================*/
	// Select Model Data From DB
	// Usage: get_models_by_make('make');
	/*========================================================================*/
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

	/*========================================================================*/
	// Build Pending Make Table
	// Usage: get_pending_makes();
	/*========================================================================*/
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

	/*========================================================================*/
	// Build Pending Model Table
	// Usage: get_pending_models();
	/*========================================================================*/
	function get_pending_models()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'md.*, mk.make',
			'FROM'		=> array(
				GARAGE_MODELS_TABLE	=> 'md',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'md.make_id = mk.id'
				)
			),
			'ORDER_BY'	=>	'md.pending = 1'
		));

		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}


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

	function move_make_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_MODELS_TABLE, 'make_id', $to_id, 'make_id', $from_id);
		$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'make_id', $to_id, 'make_id', $from_id);

		return;
	}

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

	function delete_model_content($model_id)
	{
		global $db, $config, $garage, $garage_vehicle;

		$vehicles = $garage_vehicle->get_vehicles_by_model_id($model_id);
		for ($i = 0, $count = sizeof($vehicles);$i < $count; $i++)
		{
			$garage_vehicle->delete_vehicle($vehicles[$i]['id']);
		}
		$garage->delete_rows(GARAGE_MODELS_TABLE, 'id', $model_id);

		return;
	}

	function move_model_content($from_id, $to_id)
	{
		global $garage;

		$garage->update_single_field(GARAGE_VEHICLES_TABLE, 'model_id', $to_id, 'model_id', $from_id);

		return;
	}

}

$garage_model = new garage_model();

?>
