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

		$sql = "INSERT INTO ". GARAGE_MAKES_TABLE ." 
			(make)
			VALUES 
			('".$data['make']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Make', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Update Model Into DB
	// Usage: update_make(array());
	/*========================================================================*/
	function update_make($data)
	{
		global $db;

		$sql = "UPDATE ". GARAGE_MAKES_TABLE ." 
			SET make='".$data['make']."'
			WHERE id = ".$data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Make', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count Makes With Certain Name
	// Usage: count_make(array());
	/*========================================================================*/
	function count_make($data)
	{
		global $db;

		$sql = "SELECT count(*) as total 
			FROM ". GARAGE_MAKES_TABLE ." 
			WHERE make = '$data'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Count Makes', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$row['total'] = (empty($row['total'])) ? 0 : $row['total'];
		return $row['total'];
	}

	/*========================================================================*/
	// Inserts Model Into DB
	// Usage: insert_model(array());
	/*========================================================================*/
	function insert_model($data)
	{
		global $db;

		$sql = "INSERT INTO ". GARAGE_MODELS_TABLE ." 
			(make_id, model)
			VALUES 
			('".$data['make_id']."', '".$data['model']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Model', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Update Model Into DB
	// Usage: update_model(array());
	/*========================================================================*/
	function update_model($data)
	{
		global $db;

		$sql = "UPDATE ". GARAGE_MODELS_TABLE ."
			SET model = '".$data['model']."'
			WHERE id = ".$data['id'];

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Model', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Make Data From DB
	// Usage: get_make('make id');
	/*========================================================================*/
	function get_make($make_id)
	{
		global $db;

		$sql = "SELECT make, id 
			FROM " . GARAGE_MAKES_TABLE . " 
			WHERE id = $make_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Make', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select All Make Data From DB
	// Usage: get_all_makes();
	/*========================================================================*/
	function get_all_makes()
	{
		global $db;

		$sql = "SELECT make, id 
			FROM " . GARAGE_MAKES_TABLE;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not All Select Make Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
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

		$sql = "SELECT model, id, make_id 
			FROM " . GARAGE_MODELS_TABLE. " 
			WHERE make_id = $make_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Models From Make', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
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

		$sql = "SELECT mdl.id as model_id, mdl.model, mk.id as make_id, mk.make, mdl.pending as model_pending, mk.pending as make_pending
			FROM " . GARAGE_MAKES_TABLE. " mk 
				LEFT JOIN " . GARAGE_MODELS_TABLE . " mdl ON mk.id = mdl.make_id
			ORDER BY mk.make, mdl.model";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select All Makes And Models', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
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

		$sql = "SELECT model 
			FROM " . GARAGE_MODELS_TABLE . " 
			WHERE id = $model_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Model', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Build Pending Make Table
	// Usage: build_make_table();
	/*========================================================================*/
	function build_make_table()
	{
		global $db, $template, $theme;

		$sql = "SELECT make.* 
			FROM " . GARAGE_MAKES_TABLE ." AS make
			WHERE make.pending = 1";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Makes', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($result);

		if ( $count >= 1 )
		{
			$template->assign_block_vars('make_pending', array());
		}

		$i = 1;
		while ( $row = $db->sql_fetchrow($result) )
		{
			$template->assign_block_vars('make_pending.row', array(
				'MAKE_ID' 	=> $row['id'],
				'MAKE' 		=> $row['make'])
			);
			$i++;
		}
		$db->sql_freeresult($result);

		//Return Count Of Pending Items
		return $count;
	}

	/*========================================================================*/
	// Build Pending Model Table
	// Usage: build_model_table();
	/*========================================================================*/
	function build_model_table()
	{
		global $db, $template, $theme;

		$sql = "SELECT model.* , make.make
			FROM " . GARAGE_MODELS_TABLE ." AS model
	        		LEFT JOIN " . GARAGE_MAKES_TABLE . " AS make ON model.make_id = make.id
			WHERE model.pending = 1";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($result);

		if ( $count >= 1 )
		{
			$template->assign_block_vars('model_pending', array());
		}

		$i = 1;
		while ( $row = $db->sql_fetchrow($result) )
		{
			$template->assign_block_vars('model_pending.row', array(
				'MODEL_ID'	=> $row['id'],
				'MAKE' 		=> $row['make'],
				'MODEL' 	=> $row['model'])
			);
			$i++;
		}
		$db->sql_freeresult($result);

		//Return Count Of Pending Items
		return $count;

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
