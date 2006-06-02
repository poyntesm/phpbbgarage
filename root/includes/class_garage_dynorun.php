<?php
/***************************************************************************
 *                              class_garage_dynorun.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
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

class garage_dynorun
{

	var $classname = "garage_dynorun";

	/*========================================================================*/
	// Inserts Dynorun Into DB
	// Usage: insert_dynorun(array());
	/*========================================================================*/
	function insert_dynorun($data)
	{
		global $cid, $db;

		$sql = "INSERT INTO ". GARAGE_ROLLINGROAD_TABLE ."
			(garage_id, dynocenter, bhp, bhp_unit, torque, torque_unit, boost, boost_unit, nitrous, peakpoint, date_created, date_updated, pending)
			VALUES
			('$cid', '".$data['dynocenter']."', '".$data['bhp']."', '".$data['bhp_unit']."', '".$data['torque']."', '".$data['torque_unit']."', '".$data['boost']."', '".$data['boost_unit']."', '".$data['nitrous']."', '".$data['peakpoint']."', '".$data['time']."', '".$data['time']."', '".$data['pending']."')";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Dynorun', '', __LINE__, __FILE__, $sql);
		}

		$id = $db->sql_nextid();

		return $id;
	}

	/*========================================================================*/
	// Updates Dynorun In DB
	// Usage:  update_dynorun(array());
	/*========================================================================*/
	function update_dynorun($data)
	{
		global $db, $rrid, $cid;

		$sql = "UPDATE ". GARAGE_ROLLINGROAD_TABLE ."
			SET dynocenter = '".$data['dynocenter']."', bhp = '".$data['bhp']."', bhp_unit = '".$data['bhp_unit']."', torque = '".$data['torque']."', torque_unit = '".$data['torque_unit']."', boost = '".$data['boost']."', boost_unit = '".$data['boost_unit']."', nitrous = '".$data['nitrous']."', peakpoint = '".$data['peakpoint']."', pending = '".$data['pending']."'
			WHERE id = '$rrid' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Dynorun Data', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Returns Count Of Dynoruns Performed By Vehicle
	// Usage: count_runs('garage id');
	/*========================================================================*/
	function count_runs($cid)
	{
		global $db;

		$sql = "SELECT count(id) AS total 
			FROM " . GARAGE_ROLLINGROAD_TABLE . " 
			WHERE garage_id = $cid";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Counting Dynoruns', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Delete Dynorun Including Image 
	// Usage: delete_dynorun('dynorun id');
	/*========================================================================*/
	function delete_dynorun($rrid)
	{
		global $db, $garage_image, $garage;
	
		//Right They Want To Delete A Dynorun
		if (empty($rrid))
		{
	 		message_die(GENERAL_ERROR, 'Dynorun ID Not Entered', '', __LINE__, __FILE__);
		}
	
		//Get All Required Data
		$data = $this->select_dynorun_data($rrid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
			{
				//Seems To Be An Image To Delete, Let Call The Function
				$garage_images->delete_image($data['image_id']);
			}
		}
	
		//Update Quartermile Table For An Matched Times
		$garage->update_single_field(GARAGE_QUARTERMILE_TABLE, 'rr_id', 'NULL', 'rr_id', $rrid);	
	
		//Time To Delete The Actual RollingRoad Run Now
		$garage->delete_rows(GARAGE_ROLLINGROAD_TABLE, 'id', $rrid);
	
		return ;
	}
	
	/*========================================================================*/
	// Build Top Dyno Runs HTML If Required 
	// Usage: show_topdynorun();
	/*========================================================================*/
	function show_topdynorun()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['topdynorun_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Top_Dyno_Runs'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Bhp-Torque-Nitrous'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['topdyno_limit'] ? $garage_config['topdyno_limit'] : 10;
	
		$sql = "SELECT  rr.garage_id, MAX(rr.bhp) as bhp
			FROM " . GARAGE_ROLLINGROAD_TABLE ." AS rr
				LEFT JOIN " . GARAGE_TABLE ." AS g ON rr.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE rr.pending = 0
				AND makes.pending = 0 AND models.pending = 0 
			GROUP BY rr.garage_id
			ORDER BY bhp DESC LIMIT $limit";

		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Dyno Runs', '', __LINE__, __FILE__, $sql);
		}
		
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
				$sql = "SELECT g.id, g.made_year, g.member_id, makes.make, models.model, user.username, rr.dynocenter, round(rr.bhp,0) as bhp, 
					rr.bhp_unit, round(rr.torque,0) as torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous, round(rr.peakpoint,0) as peakpoint, 
					rr.id as rr_id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, rr.nitrous
				FROM " . GARAGE_ROLLINGROAD_TABLE ." AS rr
					LEFT JOIN " . GARAGE_TABLE ." AS g ON rr.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				WHERE rr.garage_id = " . $row['garage_id'] . " AND rr.bhp = " . $row['bhp'];	
	 		if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
			}
	 		            
		 	$vehicle_data = $db->sql_fetchrow($result);
	
	            	$dynorun = $vehicle_data['bhp'] .' ' . $vehicle_data['bhp_unit'] . ' / ' . $vehicle_data['torque'] .' ' . $vehicle_data['torque_unit'] . ' / '. $vehicle_data['nitrous'];
	
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $dynorun)
			);
	 	}
	
		$required_position++;
		return ;
	}	

	/*========================================================================*/
	// Select All Dynorun Data From DB
	// Usage: select_dynorun_data('dynorun id');
	/*========================================================================*/
	function select_dynorun_data($rrid)
	{
		global $db;

	   	$sql = "SELECT rr.*, images.* , g.made_year, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
                    	FROM " . GARAGE_ROLLINGROAD_TABLE . " AS rr
		          	LEFT JOIN " . GARAGE_TABLE . " AS g ON rr.garage_id = g.id
		          	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = rr.image_id 
                    	WHERE rr.id = $rrid";

		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Build Dynorun Table
	// Usage: build_dynorun_table('YES|NO');
	/*========================================================================*/
	function build_dynorun_table($pending)
	{
		global $db, $template, $images, $start, $sort, $sort_order,$phpEx, $garage_config, $lang, $theme, $mode, $HTTP_POST_VARS, $HTTP_GET_VARS, $garage_model;

		$pending = ($pending == 'YES') ? 1 : 0;

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;
		$order_by = (empty($sort)) ? 'bhp' : $sort;

		if(isset($HTTP_POST_VARS['order']))
		{
			$sort_order = ($HTTP_POST_VARS['order'] == 'ASC') ? 'ASC' : 'DESC';
		}
		else if(isset($HTTP_GET_VARS['order']))
		{
			$sort_order = ($HTTP_GET_VARS['order'] == 'ASC') ? 'ASC' : 'DESC';
		}
		else
		{
			$sort_order = 'DESC';
		}

		// Sorting Via QuarterMile
		$sort_types_text = array($lang['Dynocenter'], $lang['Bhp'], $lang['Bhp_Unit'], $lang['Torque'], $lang['Torque_Unit'], $lang['Boost'],  $lang['Boost_Unit'], $lang['Nitrous'], $lang['Peakpoint']);
		$sort_types = array('rr.dynocenter', 'bhp', 'rr.bhp_unit, bhp', 'rr.torque', 'rr.torque_unit, rr.torque', 'rr.boost', 'rr.boost_unit, rr.boost', 'rr.nitrous', 'peakpoint');

		$select_sort_mode = '<select name="sort">';
		for($i = 0; $i < count($sort_types_text); $i++)
		{
			$selected = ( $sort == $sort_types[$i] ) ? ' selected="selected"' : '';
			$select_sort_mode .= '<option value="' . $sort_types[$i] . '"' . $selected . '>' . $sort_types_text[$i] . '</option>';
		}
		$select_sort_mode .= '</select>';

		if ( isset($HTTP_GET_VARS['make_id']) || isset($HTTP_POST_VARS['make_id']) )
		{
			$make_id = ( isset($HTTP_POST_VARS['make_id']) ) ? htmlspecialchars($HTTP_POST_VARS['make_id']) : htmlspecialchars($HTTP_GET_VARS['make_id']);

			if (!empty($make_id))
			{
				//Pull Required Data From DB
				$data = $garage_model->select_make_data($make_id);
				$addtional_where .= "AND g.make_id = '$make_id'";
			}
		}

		if ( isset($HTTP_GET_VARS['model_id']) || isset($HTTP_POST_VARS['model_id']) )
		{
			$model_id = ( isset($HTTP_POST_VARS['model_id']) ) ? htmlspecialchars($HTTP_POST_VARS['model_id']) : htmlspecialchars($HTTP_GET_VARS['model_id']);

			if (!empty($model_id))
			{
				//Pull Required Data From DB
				$data .= $garage->select_model_data($model_id);
				$addtional_where .= "AND g.model_id = '$model_id'";
			}
		}

		//First Query To Return Top Time For All Or For Selected Filter...
		$sql = "SELECT  rr.garage_id, MAX(rr.bhp) as bhp
			FROM " . GARAGE_ROLLINGROAD_TABLE ." AS rr
				LEFT JOIN " . GARAGE_TABLE ." AS g ON rr.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE rr.pending = $pending 
				AND makes.pending = 0 AND models.pending = 0 
				$addtional_where 
			GROUP BY rr.garage_id
			ORDER BY $order_by $sort_order
		       	LIMIT $start, " . $garage_config['cars_per_page'];

		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Rollingroad', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($first_result);

		if ($count >= 1 AND $pending == 1)
		{
			$template->assign_block_vars('rollingroad_pending', array());
		}

	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$sql = "SELECT g.id, g.made_year, g.member_id, makes.make, models.model, user.username,
				rr.dynocenter, rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous, round(rr.peakpoint,0) as peakpoint, images.attach_id as image_id, rr.id as rr_id
				FROM " . GARAGE_ROLLINGROAD_TABLE ." AS rr
					LEFT JOIN " . GARAGE_TABLE ." AS g ON rr.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
		                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = rr.image_id
				WHERE rr.garage_id = " . $row['garage_id'] . " AND rr.bhp = " . $row['bhp'];

			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
			}
		
			$full_row = $db->sql_fetchrow($result);
			$username = $full_row['username'];
			$user_id = $full_row['member_id'];
			$garage_id = $full_row['id'];
			$temp_url = append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid");
			$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';
			$year =  $full_row['made_year'];
			$make =  $full_row['make'];
			$model =  $full_row['model'];
			$vehicle = "$year $make $model";
			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];
			if ($full_row['image_id'])
			{
				$data['image_link'] ='<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $full_row['image_id'] .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
			}
			else
			{
				$data['image_link'] ='';
			}
			
            		$temp_url = append_sid("garage.$phpEx?mode=edit_rollingroad&amp;RRID=".$full_row['rr_id']."&amp;CID=".$full_row['id']."&amp;PENDING=YES");
	            	$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';


			$assign_block = ($pending == 1) ? 'rollingroad_pending.row' : 'memberrow';

			$template->assign_block_vars($assign_block, array(
				'ROW_NUMBER' => $i + ( $start + 1 ),
				'ROW_COLOR' => '#' . $row_color,
				'RRID' => $full_row['rr_id'],
				'IMAGE_LINK' => $data['image_link'],
				'ROW_CLASS' => $row_class,
				'USERNAME' => $username,
				'PROFILE' => $profile, 
				'VEHICLE' => $vehicle,
				'DYNOCENTER' => $full_row['dynocenter'],
				'BHP' => $full_row['bhp'],
				'BHP_UNIT' => $full_row['bhp_unit'],
				'TORQUE' => $full_row['torque'],
				'TORQUE_UNIT' => $full_row['torque_unit'],
				'BOOST' => $full_row['boost'],
				'BOOST_UNIT' => $full_row['boost_unit'],
				'NITROUS' => $full_row['nitrous'],
				'PEAKPOINT' => $full_row['peakpoint'],
				'EDIT_LINK' => $edit_link,
				'U_VIEWVEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$garage_id"),
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"))
			);
			$i++;
		}
		$db->sql_freeresult($result);

		$sql = "SELECT count(DISTINCT rr.garage_id) AS total
				FROM " . GARAGE_ROLLINGROAD_TABLE . " rr
				LEFT JOIN " . GARAGE_TABLE ." AS g ON rr.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE rr.pending = $pending 
				AND ( makes.pending = 0 AND models.pending = 0 )
				$addtional_where";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Getting Pagination Total', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$pagination = generate_pagination("garage.$phpEx?mode=$mode&amp;order=$sort_order", $count['total'], $garage_config['cars_per_page'], $start). '&nbsp;';
		
		$template->assign_vars(array(
			'S_MODE_SELECT' => $select_sort_mode,
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count['total'] / $garage_config['cars_per_page'] )), 
			'L_GOTO_PAGE' => $lang['Goto_page'])
		);

		return $count['total'];
	}
}

$garage_dynorun = new garage_dynorun();

?>
