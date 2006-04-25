<?php
/***************************************************************************
 *                              functions_garage.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: functions_garage.php 94 2006-04-17 16:06:10Z poyntesm $
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

		$sql = "INSERT INTO ". GARAGE_MAKES_TABLE ." (make)
			VALUES ('".$data['make']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Make', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Inserts Model Into DB
	// Usage: insert_model(array());
	/*========================================================================*/
	function insert_model($data)
	{
		global $db;

		$sql = "INSERT INTO ". GARAGE_MODELS_TABLE ." (make_id, model)
			VALUES ('".$data['make_id']."', '".$data['model']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Make', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Select Make Data From DB
	// Usage: select_make_data('make id');
	/*========================================================================*/
	function select_make_data($make_id)
	{
		global $db;

		$sql = "SELECT make, id FROM " . GARAGE_MAKES_TABLE . " WHERE id = '$make_id' ";

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
	// Usage: select_all_make_data();
	/*========================================================================*/
	function select_all_make_data()
	{
		global $db;

		$sql = "SELECT make, id FROM " . GARAGE_MAKES_TABLE;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Make', '', __LINE__, __FILE__, $sql);
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
	// Usage: select_all_make_data();
	/*========================================================================*/
	function select_all_model_from_make_data($make_id)
	{
		global $db;

		$sql = "SELECT model, id, make_id FROM " . GARAGE_MODELS_TABLE. " WHERE make_id = $make_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Make', '', __LINE__, __FILE__, $sql);
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
	// Usage: select_all_make_data();
	/*========================================================================*/
	function select_complete_model_list()
	{
		global $db;

		$sql = "SELECT mdl.id as model_id, mdl.model, mk.id as make_id, mk.make 
			FROM " . GARAGE_MODELS_TABLE. " mdl
				LEFT JOIN " . GARAGE_MAKES_TABLE . " mk ON mdl.make_id = mk.id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Make', '', __LINE__, __FILE__, $sql);
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
	// Usage: select_model_data('model id');
	/*========================================================================*/
	function select_model_data($model_id)
	{
		global $db;

		$sql = "SELECT model FROM " . GARAGE_MODELS_TABLE . " WHERE id = $model_id";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Model', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select Model Data From DB
	// Usage: build_make_table('model id');
	/*========================================================================*/
	function build_make_table($pending)
	{
		global $db, $template, $theme;

		$pending = ($pending == 'YES') ? 1 : 0;

		$sql = "SELECT make.* 
			FROM " . GARAGE_MAKES_TABLE ." AS make
			WHERE make.pending = 1";

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($result);

		if ($count >= 1)
		{
			$template->assign_block_vars('make_pending', array());
		}

		// loop through users
		$i = 1;
		while ( $row = $db->sql_fetchrow($result) )
		{
			// setup user row template varibles
			$template->assign_block_vars('make_pending.row', array(
				'ROW_CLASS' => ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'],
				'MAKE_ID' => $row['id'],
				'MAKE' => $row['make'])
			);
			$i++;
		}
		$db->sql_freeresult($result);

		//Return Count Of Pending Items
		return $count;
	}

	/*========================================================================*/
	// Select Model Data From DB
	// Usage: build_model_table('model id');
	/*========================================================================*/
	function build_model_table($pending)
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

		if ($count >= 1)
		{
			$template->assign_block_vars('model_pending', array());
		}

		// loop through users
		$i = 1;
		while ( $row = $db->sql_fetchrow($result) )
		{
			// setup user row template varibles
			$template->assign_block_vars('model_pending.row', array(
				'ROW_CLASS' => ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'],
				'MODEL_ID' => $row['id'],
				'MAKE' => $row['make'],
				'MODEL' => $row['model'])
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
		global $template, $lang, $garage;

		$params = array('make_id', 'model_id', 'user');
		$data = $garage->process_post_vars($params);

		//Check If This Is A Search Including User
		if (!empty($data['user']))
		{
			$data['where'] = "AND username = '".$data['user']."'" ;
			$data['search_message'] = $lang['Search_Results_For_Member'] . $data['user'];
			$template->assign_vars(array(
				'L_LEVEL3' => $lang['Username_Results'])
			);
		}

		//Check If This Is A Search Including Make
		if (!empty($data['make_id']))
		{
			$template->assign_vars(array(
				'MAKE_ID' => $data['make_id'])
			);
			
			$make_data = $this->select_make_data($data['make_id']);

			if ( (empty($data['where'])) AND (!empty($data['make_id'])) )
			{
				$data['where'] = "AND make = '".$make_data['make']."'" ;
				$data['search_message'] = $lang['Search_Results_For_Make'] . $make_data['make'];
				$data['make_pagination'] =';make_id='.$data['make_id'].'&amp';
				$template->assign_vars(array(
					'L_LEVEL3' => $lang['Make_Results'])
				);
			}
		}

		//Check If This Is A Search Including Model
		if (!empty($data['model_id']))
		{
			$template->assign_vars(array(
				'MODEL_ID' => $data['model_id'])
			);

			$model_data = $this->select_model_data($data['model_id']);

			if ( (empty($data['where'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] = "AND model = '".$model_data['model']."'" ;
				$data['search_message'] = $lang['Search_Results_For_Model'] . $model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_vars(array(
					'L_LEVEL3' => $lang['Model_Results'])
				);
			}
			else if ( (!empty($model_data['model'])) AND (!empty($data['model_id'])) )
			{
				$data['where'] .= "AND model = '".$model_data['model']."'";
				$data['search_message'] .= ", " . $lang['Model'] . " " .$model_data['model'];
				$data['model_pagination'] =';model_id='.$data['model_id'].'&amp';
				$template->assign_vars(array(
					'L_LEVEL3' => $lang['Make_Model_Results'])
				);
			}
		}
	
		return $data;
	}
}

$garage_model = new garage_model();

?>
