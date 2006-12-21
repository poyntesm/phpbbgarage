<?php
/***************************************************************************
 *                              class_garage_model.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_model.php 137 2006-06-07 09:53:18Z poyntesm $
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
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

		return;
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
			'WHERE'		=>  "mk.make = $make"
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

		return;
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
	// Select All Make Data From DB
	// Usage: get_all_makes();
	/*========================================================================*/
	function get_all_makes()
	{
		global $db;

		$data = null;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'mk.id, mk.make',
			'FROM'		=> array(
				GARAGE_MAKES_TABLE	=> 'mk',
			)
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
			'SELECT'	=> 'md.id, md.model, md.make_id',
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

	/*========================================================================*/
	// Build Search Data
	// Usage: build_search_for_user_make_model();
	/*========================================================================*/
	function build_search_for_user_make_model()
	{
		global $template, $user, $garage, $phpEx, $phpbb_root_path;

		$params = array('make_id', 'model_id', 'username');
		$data = $garage->process_post_vars($params);

		//Check If This Is A Search Including User
		if (!empty($data['username']))
		{
			$data['where'] = "AND username = '".$data['username']."'" ;
			$data['search_message'] = $user->lang['SEARCH_RESULTS_FOR_MEMBER'] . $data['username'];

			$template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $user->lang['USERNAME_RESULTS'],
				'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_username&username=" . $data['username'], true))
			);
		}

		//Check If This Is A Search Including Make
		if (!empty($data['make_id']))
		{
			$template->assign_vars(array(
				'MAKE_ID' => $data['make_id'])
			);
			
			$make_data = $this->get_make($data['make_id']);

			//If No Model Then Results Are Make Only...So Set Navlinks Now.
			if (empty($data['model_id']))
			{
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['MAKE_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_vehicle&make_id=" . $data['make_id'], true))
				);
				$data['model_pagination'] = '';
			}


			if ( (empty($data['where'])) AND (!empty($data['make_id'])) )
			{
				$data['where'] = "AND make = '".$make_data['make']."'" ;
				$data['search_message'] = $user->lang['SEARCH_RESULTS_FOR_MAKE'] . $make_data['make'];
				$data['make_pagination'] =';make_id='.$data['make_id'].'&amp';
			}
		}

		//Check If This Is A Search Including Model
		if (!empty($data['model_id']))
		{
			$template->assign_vars(array(
				'MODEL_ID' => $data['model_id'])
			);

			$model_data = $this->get_model($data['model_id']);

			if ( (empty($data['where'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] = "AND model = '".$model_data['model']."'" ;
				$data['search_message'] = $user->lang['SEARCH_RESULTS_FOR_MODEL'] . $model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['MODEL_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_vehicle&model_id=" . $data['model_id'], true))
				);
			}
			else if ( (!empty($model_data['model'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] .= "AND model = '".$model_data['model']."'";
				$data['search_message'] .= ", " . $user->lang['MODEL'] . " " .$model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['MODEL_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_vehicle&make_id=" . $data['make_id']. "&model_id=" . $data['model_id'], true))
				);
			}
		}
	
		return $data;
	}

	/*========================================================================*/
	// Build Search Data
	// Usage: build_insurance_search_for_make_model();
	/*========================================================================*/
	function build_insurance_search_for_make_model()
	{
		global $template, $user, $garage, $phpEx, $phpbb_root_path;

		$params = array('make_id', 'model_id');
		$data = $garage->process_post_vars($params);

		//Check If This Is A Search Including Make
		if (!empty($data['make_id']))
		{
			$template->assign_vars(array(
				'MAKE_ID' => $data['make_id'])
			);
			
			$make_data = $this->get_make($data['make_id']);

			//If No Model Then Results Are Make Only...So Set Navlinks Now.
			if (empty($data['model_id']))
			{
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['INSURANCE_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_insurance&make_id=" . $data['make_id'], true))
				);
				$data['model_pagination'] = '';
			}


			if ( (empty($data['where'])) AND (!empty($data['make_id'])) )
			{
				$data['where'] = "AND make = '".$make_data['make']."'" ;
				$data['search_message'] = $user->lang['SEARCH_RESULTS_FOR_MAKE'] . $make_data['make'];
				$data['make_pagination'] =';make_id='.$data['make_id'].'&amp';
			}
		}

		//Check If This Is A Search Including Model
		if (!empty($data['model_id']))
		{
			$template->assign_vars(array(
				'MODEL_ID' => $data['model_id'])
			);

			$model_data = $this->get_model($data['model_id']);

			if ( (empty($data['where'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] = "AND model = '".$model_data['model']."'" ;
				$data['search_message'] = $user->lang['SEARCH_RESULTS_FOR_MODEL'] . $model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['MODEL_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_insurance&model_id=" . $data['model_id'], true))
				);
			}
			else if ( (!empty($model_data['model'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] .= "AND model = '".$model_data['model']."'";
				$data['search_message'] .= ", " . $user->lang['MODEL'] . " " .$model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_block_vars('navlinks', array(
					'FORUM_NAME'	=> $user->lang['INSURANCE_RESULTS'],
					'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=search_insurance&make_id=" . $data['make_id']. "&model_id=" . $data['model_id'], true))
				);
			}
		}
	
		return $data;
	}
}

$garage_model = new garage_model();

?>
