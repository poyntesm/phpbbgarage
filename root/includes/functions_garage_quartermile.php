<?php
/***************************************************************************
 *                              functions_garage_quartermile.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: functions_garage.php 91 2006-04-07 14:51:14Z poyntesm $
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

class garage_quartermile
{

	var $classname = "garage_quartermile";

	/*========================================================================*/
	// Inserts Quartermile Into DB
	// Usage: insert_quartermile(array());
	/*========================================================================*/
	function insert_quartermile($data)
	{
		global $cid, $db;

		$sql = "INSERT INTO ". GARAGE_QUARTERMILE_TABLE ."
			SET garage_id = '$cid', rt = '".$data['rt']."', sixty = '".$data['sixty']."', three = '".$data['three']."', eight = '".$data['eight']."', eightmph = '".$data['eightmph']."', thou = '".$data['thou']."', quart = '".$data['quart']."', quartmph = '".$data['quartmph']."', date_created = '".$data['time']."', rr_id = '".$data['rr_id']."', date_updated = '".$data[time]."', pending = '".$data['pending']."'";
		
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle', '', __LINE__, __FILE__, $sql);
		}
	
		$qmid = $db->sql_nextid();

		return $qmid;
	}

	/*========================================================================*/
	// Updates Quartermile In DB
	// Usage: update_quartermile(array());
	/*========================================================================*/
	function update_quartermile($data)
	{
		global $db, $cid, $qmid;

		$sql = "UPDATE ". GARAGE_QUARTERMILE_TABLE ."
			SET rt = '".$data['rt']."', sixty = '".$data['sixty']."', three = '".$data['three']."', eight = '".$data['eight']."', eightmph = '".$data['eightmph']."', thou = '".$data['thou']."', quart = '".$data['quart']."', quartmph = '".$data['quartmph']."', rr_id = '".$data['rr_id']."', pending = '".$data['pending']."'
			WHERE id = '$qmid' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Quartermile', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Quartermile Entry Including Image 
	// Usage: delete_quartermile_time('quartermile id');
	/*========================================================================*/
	function delete_quartermile_time($qmid)
	{
		global $userdata, $db, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		//Right They Want To Delete A QuarterMile Time
		if (empty($qmid))
		{
	 		message_die(GENERAL_ERROR, 'Quartermile ID Not Entered', '', __LINE__, __FILE__);
		}
	
		//Let Get All Info For Run, Including Image Info
		$data = $this->select_quartermile_data($qmid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
			{
				//Seems To Be An Image To Delete, Let Call The Function
				$this->delete_image($data['image_id']);
			}
		}

		//Time To Delete The Actual Quartermile Time Now
		$this->delete_rows(GARAGE_QUARTERMILE_TABLE, 'id', $qmid);

		return ;
	}
	
	/*========================================================================*/
	// Build Top Quartermile Runs HTML If Required 
	// Usage: show_topquartermile();
	/*========================================================================*/
	function show_topquartermile()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['topquartermile_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Top_Quartermile_Runs'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Quartermile'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['topquartermile_limit'] ? $garage_config['topquartermile_limit'] : 10;
	
		//First Query To Return Top Time For All Or For Selected Filter...
		$sql = "SELECT  qm.garage_id, MIN(qm.quart) as quart
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
	       			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = 0 )
				AND ( makes.pending = 0 AND models.pending = 0 )
			GROUP BY qm.garage_id
			ORDER BY quart ASC LIMIT $limit ";
	
		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Quartermile Time', '', __LINE__, __FILE__, $sql);
		}
		
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
			$sql = "SELECT g.id, g.member_id, user.username, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle,
					qm.rt, qm.sixty, qm.three, qm.eight, qm.eightmph, qm.thou, qm.quart, qm.quartmph, qm.rr_id,
					rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous
				FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
					LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
					LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON qm.rr_id = rr.id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
	       				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
				WHERE qm.garage_id = " . $row['garage_id'] . " AND qm.quart = " . $row['quart'];
	
	 		if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
			}
	 		            
		 	$vehicle_data = $db->sql_fetchrow($result);
	
			$mph = (empty($vehicle_data['quartmph'])) ? 'N/A' : $vehicle_data['quartmph'];
	            	$quartermile = $vehicle_data['quart'] .' @ ' . $mph . ' '. $lang['Quartermile_Speed_Unit'];
	
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $quartermile)
			);
	 	}
	
		$required_position++;
		return ;
	}

	/*========================================================================*/
	// Select All Quartermile Data
	// Usage: select_quartermile_data('quartermile id');
	/*========================================================================*/
	function select_quartermile_data($qmid)
	{
		global $db;
	
	   	$sql = "SELECT qm.*, rr.id, rr.bhp, rr.bhp_unit, images.*, g.made_year, makes.make, models.model, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
                    	FROM " . GARAGE_QUARTERMILE_TABLE . " AS qm
		          	LEFT JOIN " . GARAGE_TABLE . " AS g ON qm.garage_id = g.id
		          	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id 
	        		LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON rr.id = qm.rr_id 
                    	WHERE qm.id = $qmid";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	function build_quartermile_table($pending)
	{
		global $db, $template, $images, $sort, $phpEx, $sort_order, $garage_config, $lang, $theme, $mode, $HTTP_POST_VARS, $HTTP_GET_VARS;

		$pending = ($pending == 'YES') ? 1 : 0;

		$start = (isset($HTTP_GET_VARS['start'])) ? intval($HTTP_GET_VARS['start']) : 0;

		$order_by = (empty($sort)) ? 'quart' : $sort;

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
			$sort_order = 'ASC';
		}

		// Sorting Via QuarterMile
		$sort_types_text = array($lang['Car_Rt'], $lang['Car_Sixty'], $lang['Car_Three'], $lang['Car_Eigth'], $lang['Car_Eigthm'], $lang['Car_Thou'],  $lang['Car_Quart'], $lang['Car_Quartm']);
		$sort_types = array('qm.rt', 'qm.sixty', 'qm.three', 'qm.eight', 'qm.eightmph', 'qm.thou', 'quart', 'qm.quartmph');

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
				$data = $this->select_make_data($make_id);
				$addtional_where .= "AND g.make_id = '$make_id'";
			}
		}

		if ( isset($HTTP_GET_VARS['model_id']) || isset($HTTP_POST_VARS['model_id']) )
		{
			$model_id = ( isset($HTTP_POST_VARS['model_id']) ) ? htmlspecialchars($HTTP_POST_VARS['model_id']) : htmlspecialchars($HTTP_GET_VARS['model_id']);

			if (!empty($model_id))
			{
				//Pull Required Data From DB
				$data .= $this->select_model_data($model_id);
				$addtional_where .= "AND g.model_id = '$model_id'";
			}
		}

		//First Query To Return Top Time For All Or For Selected Filter...
		$sql = "SELECT  qm.garage_id, MIN(qm.quart) as quart
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = $pending )
				AND ( makes.pending = 0 AND models.pending = 0 )
				$addtional_where 
			GROUP BY qm.garage_id
			ORDER BY $order_by $sort_order
			LIMIT $start, " . $garage_config['cars_per_page'];

		if( !($first_result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error Selecting Top Quartermile Time', '', __LINE__, __FILE__, $sql);
		}

		$count = $db->sql_numrows($first_result);

		if ($count >= 1 AND $pending == 1)
		{
			$template->assign_block_vars('quartermile_pending', array());
		}
	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		$i = 0;
		while ($row = $db->sql_fetchrow($first_result) )
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$sql = "SELECT g.id, g.member_id, g.made_year, makes.make, models.model, user.username, qm.id as qmid,
				qm.rt, qm.sixty, qm.three, qm.eight, qm.eightmph, qm.thou, qm.quart, qm.quartmph, qm.rr_id,
				rr.bhp, rr.bhp_unit, rr.torque, rr.torque_unit, rr.boost, rr.boost_unit, rr.nitrous,
				CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, images.attach_id as image_id
				FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
					LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
					LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
					LEFT JOIN " . GARAGE_ROLLINGROAD_TABLE . " AS rr ON qm.rr_id = rr.id
				        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        				LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
		                	LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = qm.image_id
				WHERE qm.garage_id = " . $row['garage_id'] . " AND qm.quart = " . $row['quart'];

			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Error Selecting Quartermile Time ', '', __LINE__, __FILE__, $sql);
			}
		
			$data = $db->sql_fetchrow($result);

			$temp_url = append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=$cid");
			$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';
			$row_color = ( !($i % 2) ) ? $theme['td_color1'] : $theme['td_color2'];
			$row_class = ( !($i % 2) ) ? $theme['td_class1'] : $theme['td_class2'];

            		$temp_url = append_sid("garage.$phpEx?mode=edit_quartermile&amp;QMID=".$data['qmid']."&amp;CID=".$data['id']."&amp;PENDING=YES");
	            	$edit_link = '<a href="' . $temp_url . '"><img src="' . $images['garage_edit'] . '" alt="'.$lang['Edit'].'" title="'.$lang['Edit'].'" border="0" /></a>';

			if ($data['image_id'])
			{
				$data['image_link'] ='<a href="garage.'. $phpEx .'?mode=view_gallery_item&amp;image_id='. $data['image_id'] .'" target="_blank"><img src="' . $images['slip_image_attached'] . '" alt="'.$lang['Slip_Image_Attached'].'" title="'.$lang['Slip_Image_Attached'].'" border="0" /></a>';
			}
			else
			{
				$data['image_link'] ='';
			}

			if ($pending == 1)
			{
				$assign_block = 'quartermile_pending.row';
			}
			else
			{
				$assign_block = 'memberrow';
			}
			$template->assign_block_vars($assign_block, array(
				'ROW_NUMBER' => $i + ( $start + 1 ),
				'ROW_COLOR' => '#' . $row_color,
				'ROW_CLASS' => $row_class,
				'QMID' => $data['qmid'],
				'IMAGE_LINK' => $data['image_link'],
				'USERNAME' => $data['username'],
				'PROFILE' => $profile, 
				'VEHICLE' => $data['vehicle'],
				'RT' => $data['rt'],
				'SIXTY' => $data['sixty'],
				'THREE' => $data['three'],
				'EIGTH' => $data['eight'],
				'EIGHTM' => $data['eightmph'],
				'THOU' => $data['thou'],
				'QUART' => $data['quart'],
				'QUARTM' => $data['quartmph'],
				'BHP' => $data['bhp'],
				'BHP_UNIT' => $data['bhp_unit'],
				'TORQUE' => $data['torque'],
				'TORQUE_UNIT' => $data['torque_unit'],
				'BOOST' => $data['boost'],
				'BOOST_UNIT' => $data['boost_unit'],
				'NITROUS' => $data['nitrous'],
				'EDIT_LINK' => $edit_link,
				'U_VIEWVEHICLE' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$data['id']),
				'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=".$data['member_id']))
			);
			$i++;
		}
		$db->sql_freeresult($result);

		$sql = "SELECT COUNT(DISTINCT qm.garage_id)as total
			FROM " . GARAGE_QUARTERMILE_TABLE ." AS qm
				LEFT JOIN " . GARAGE_TABLE ." AS g ON qm.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
        			LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
			WHERE	(qm.sixty IS NOT NULL
				OR qm.three IS NOT NULL
				OR qm.eight IS NOT NULL
				OR qm.eightmph IS NOT NULL
				OR qm.thou IS NOT NULL
				OR qm.rt IS NOT NULL
				OR qm.quartmph IS NOT NULL) AND ( qm.pending = $pending )
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

$garage_quartermile = new garage_quartermile();

?>
