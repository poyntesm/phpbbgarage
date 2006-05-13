<?php
/***************************************************************************
 *                              functions_garage.php
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

class garage_modification
{

	var $classname = "garage_modification";

	/*========================================================================*/
	// Inserts Modification Into DB
	// Usage: insert_modification(array());
	/*========================================================================*/
	function insert_modification($data)
	{
		global $cid, $userdata, $db;

		$sql = "INSERT INTO ". GARAGE_MODS_TABLE ."
			SET garage_id = '$cid', member_id = '".$data['member_id']."', category_id = '".$data['category_id']."', title = '".$data['title']."', price = '".$data['price']."', install_price = '".$data['install_price']."', install_rating = '".$data['install_rating']."', product_rating = '".$data['product_rating']."', comments = '".$data['comments']."', date_created = '".$data['time']."', date_updated = '".$data['time']."', business_id = '".$data['business_id']."', install_business_id = '".$data['install_business_id']."', install_comments = '".$data['install_comments']."', purchase_rating = '".$data['purchase_rating']."'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Vehicle', '', __LINE__, __FILE__, $sql);
		}
	
		$mid = $db->sql_nextid();

		return $mid;
	}

	/*========================================================================*/
	// Updates Modification In DB
	// Usage:  update_modification(array());
	/*========================================================================*/
	function update_modification($data)
	{
		global $db, $cid, $mid;

		$sql = "UPDATE ". GARAGE_MODS_TABLE ."
			SET category_id = '".$data['category_id']."', title = '".$data['title']."', price = '".$data['price']."', install_price = '".$data['install_price']."', install_rating = '".$data['install_rating']."', product_rating = '".$data['product_rating']."', comments = '".$data['comments']."', install_comments = '".$data['install_comments']."' , business_id = '".$data['business_id']."', install_business_id = '".$data['install_business_id']."', date_updated = '".$data['time']."', purchase_rating = '".$data['purchase_rating']."'
			WHERE id = '$mid' and garage_id = '$cid'";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Modification', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count The Total Modifications Within The Garage
	// Usage: count_total_modifications();
	/*========================================================================*/
	function count_total_modifications()
	{
		global $db;

	        // Get the total count of mods in the garage
	        $sql = "SELECT count(*) AS total_mods FROM " . GARAGE_MODS_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Mods', '', __LINE__, __FILE__, $sql);
		}
        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total_mods'];
	}

	/*========================================================================*/
	// Count The Modification Categories Within The Garage
	// Usage: count_modification_categories();
	/*========================================================================*/
	function count_modification_categories()
	{
		global $db;

	        // Get the total count of mods in the garage
	        $sql = "SELECT count(*) AS total FROM " . GARAGE_CATEGORIES_TABLE;
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Total Mods', '', __LINE__, __FILE__, $sql);
		}
        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}
			
	/*========================================================================*/
	// Delete Modification Entry Including image 
	// Usage: delete_modification('modification id');
	/*========================================================================*/
	function delete_modification($mid)
	{
		global $userdata, $template, $db, $SID, $lang, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		//Right They Want To Delete A Modification Time
		if (empty($mid))
		{
	 		message_die(GENERAL_ERROR, 'Modification ID Not Entered', '', __LINE__, __FILE__);
		}
	
		//Let Assign Variables To All Collected Info
		$data = $this->select_modification_data($mid);
	
		//Lets See If There Is An Image Associated With This Modification
		if (!empty($data['image_id']))
		{
			if ( (!empty($data['attach_location'])) OR (!empty($data['attach_thumb_location'])) )
			{
				//Seems To Be An Image To Delete, Let Call The Function
				$this->delete_image($data['image_id']);
			}
		}
	
		//Time To Delete The Actual Modification Now
		$this->delete_rows(GARAGE_MODS_TABLE, 'id', $mid);
	
		return ;
	}
	
	/*========================================================================*/
	// Build Updated Modifications HTML If Required 
	// Usage: show_updated_modifications();
	/*========================================================================*/
	function show_updated_modifications()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['lastupdatedmods_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Last_Updated_Modifications'],
			'COLUMN_1_TITLE' => $lang['Modification'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Updated'])
		);
	 		
	        // What's the count? Default to 10
	        $limit = $garage_config['lastupdatedmods_limit'] ? $garage_config['lastupdatedmods_limit'] : 10;
	
	 	$sql = "SELECT mods.id, mods.garage_id, mods.member_id, mods.title AS mod_title, mods.date_updated AS POI, m.username, mods.garage_id 
	                FROM " . GARAGE_MODS_TABLE . " AS mods 
				LEFT JOIN " . GARAGE_TABLE . " AS g ON mods.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                	LEFT JOIN " . USERS_TABLE . " AS m ON mods.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $row = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_modification&amp;MID=".$row['id']."&amp;CID=".$row['garage_id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$row['member_id']),
				'COLUMN_1_TITLE' => $row['mod_title'],
				'COLUMN_2_TITLE' => $row['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $row['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Most Modified HTML If Required 
	// Usage: show_most_modified();
	/*========================================================================*/
	function show_most_modified()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['mostmodded_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Most_Modified_Vehicle'],
			'COLUMN_1_TITLE' => $lang['Vehicle'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Mods'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['mostmodded_limit'] ? $garage_config['mostmodded_limit'] : 10;
	
	 	$sql = "SELECT g.id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle, 
	                        g.member_id, COUNT(mods.id) AS POI, m.username 
	                FROM " . GARAGE_TABLE . " AS g 
	                	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                        LEFT JOIN " . USERS_TABLE . " AS m ON g.member_id = m.user_id 
	                        LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON mods.garage_id = g.id
			WHERE makes.pending = 0 AND models.pending = 0
	                GROUP BY g.id 
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['vehicle'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => $vehicle_data['POI'])
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Build Newest Modifications HTML If Required 
	// Usage:  show_newest_modifications()
	/*========================================================================*/
	function show_newest_modifications()
	{
		global $required_position, $userdata, $template, $db, $SID, $lang, $phpEx, $garage_config, $board_config;
	
		if ( $garage_config['newestmods_on'] != TRUE )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE' => $lang['Newest_Modifications'],
			'COLUMN_1_TITLE' => $lang['Modification'],
			'COLUMN_2_TITLE' => $lang['Owner'],
			'COLUMN_3_TITLE' => $lang['Created'])
		);
	
	        // What's the count? Default to 10
	        $limit = $garage_config['newestmods_limit'] ? $garage_config['newestmods_limit'] : 10;
	 		 		
	 	$sql = "SELECT mods.id, mods.garage_id, mods.member_id, mods.title AS mod_title, mods.date_created AS POI,
	       			m.username, mods.garage_id 
	                FROM " . GARAGE_MODS_TABLE . " AS mods 
				LEFT JOIN " . GARAGE_TABLE . " AS g ON mods.garage_id = g.id
			        LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id 
	                        LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
	                	LEFT JOIN " . USERS_TABLE . " AS m ON mods.member_id = m.user_id
			WHERE makes.pending = 0 AND models.pending = 0
	                ORDER BY POI DESC LIMIT $limit";
	 		            
	 	if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not query vehicle information", "", __LINE__, __FILE__, $sql);
		}
	 		            
	 	while ( $vehicle_data = $db->sql_fetchrow($result) )
	 	{
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' => append_sid("garage.$phpEx?mode=view_modification&amp;MID=".$vehicle_data['id']."&amp;CID=".$vehicle_data['garage_id']),
				'U_COLUMN_2' => append_sid("profile.$phpEx?mode=viewprofile&amp;".POST_USERS_URL."=".$vehicle_data['member_id']),
				'COLUMN_1_TITLE' => $vehicle_data['mod_title'],
				'COLUMN_2_TITLE' => $vehicle_data['username'],
				'COLUMN_3' => create_date('D M d, Y G:i', $vehicle_data['POI'], $board_config['board_timezone']))
			);
	 	}
	
		$required_position++;
		return ;
	}
	
	/*========================================================================*/
	// Select All Modification Data From DB
	// Usage: select_modification_data('modification id');
	/*========================================================================*/
	function select_modification_data($mid)
	{
		global $db;
	
		$sql = "SELECT mods.*, g.made_year, g.id, images.*, user.username, user.user_avatar_type, user.user_allowavatar, user.user_avatar, images.attach_ext, images.attach_id, images.attach_file, cats.title as category_title, makes.make, models.model, bus.title as business_name, ins.title as install_business_name, ins.id as install_business_id, CONCAT_WS(' ', g.made_year, makes.make, models.model) AS vehicle
     			FROM (" . GARAGE_MODS_TABLE . " AS mods, " . GARAGE_TABLE . " AS g)
				LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
				LEFT JOIN " . GARAGE_CATEGORIES_TABLE . " AS cats ON cats.id = mods.category_id
        			LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = mods.image_id 
                        	LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                        	LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
                        	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS bus ON mods.business_id = bus.id
                        	LEFT JOIN " . GARAGE_BUSINESS_TABLE . " AS ins ON mods.install_business_id = ins.id
        		WHERE mods.id = $mid AND g.id = mods.garage_id";

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Modification Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

}

$garage_modification = new garage_modification();

?>
