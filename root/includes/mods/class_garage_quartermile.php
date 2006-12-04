<?php
/***************************************************************************
 *                              class_garage_quartermile.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id: class_garage_quartermile.php 138 2006-06-07 15:55:46Z poyntesm $
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
		global $cid, $db, $garage_config;

		$sql = 'INSERT INTO ' . GARAGE_QUARTERMILE_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'garage_id'	=> $cid,
			'rt'		=> $data['rt'],
			'sixty'		=> $data['sixty'],
			'three'		=> $data['three'],
			'eighth'	=> $data['eighth'],
			'eighthmph'	=> $data['eighthmph'],
			'thou'		=> $data['thou'],
			'quart'		=> $data['quart'],
			'quartmph'	=> $data['quartmph'],
			'date_created'	=> time(),
			'date_updated'	=> time(),
			'rr_id'		=> $data['rr_id'],
			'pending'	=> ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0)
		);

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert Quartermile', '', __LINE__, __FILE__, $sql);
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
		global $db, $cid, $qmid, $garage_config;

		$update_sql = array(
			'garage_id'	=> $cid,
			'rt'		=> $data['rt'],
			'sixty'		=> $data['sixty'],
			'three'		=> $data['three'],
			'eighth'	=> $data['eighth'],
			'eighthmph'	=> $data['eighthmph'],
			'thou'		=> $data['thou'],
			'quart'		=> $data['quart'],
			'quartmph'	=> $data['quartmph'],
			'date_updated'	=> time(),
			'rr_id'		=> $data['rr_id'],
			'pending'	=> ($garage_config['enable_quartermile_approval'] == '1') ? 1 : 0
		);

		$sql = 'UPDATE ' . GARAGE_QUARTERMILE_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $update_sql) . "
			WHERE id = $qmid AND garage_id = $cid";

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Update Quartermile', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Delete Quartermile Entry Including Image 
	// Usage: delete_quartermile('quartermile id');
	/*========================================================================*/
	function delete_quartermile($qmid)
	{
		global $garage, $garage_image;
	
		//Let Get All Info For Run, Including Image Info
		$data = $this->get_quartermile($qmid);
	
		//Lets See If There Is An Image Associated With This Run
		if (!empty($data['image_id']))
		{
			//Seems To Be An Image To Delete, Let Call The Function
			$garage_image->delete_image($data['image_id']);
		}

		//Time To Delete The Actual Quartermile Time Now
		$garage->delete_rows(GARAGE_QUARTERMILE_TABLE, 'id', $qmid);

		return ;
	}

	/*========================================================================*/
	// Select Top Quartermiles Data By Vehicle From DB
	// Usage: get_top_quartermiles('vehicle id');
	/*========================================================================*/
	function get_top_quartermiles($pending, $sort, $order, $start = 0, $limit = 30, $addtional_where = NULL)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.garage_id, MIN(q.quart) as quart',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
			),
			'WHERE'		=>  "(q.sixty IS NOT NULL OR q.three IS NOT NULL OR q.eighth IS NOT NULL OR q.eighthmph IS NOT NULL OR q.thou IS NOT NULL OR q.rt IS NOT NULL OR q.quartmph IS NOT NULL) AND ( q.pending = $pending ) AND ( mk.pending = 0 AND md.pending = 0 ) $addtional_where",
			'GROUP_BY'	=> 'q.garage_id',
			'ORDER_BY'	=> "$sort $order"
		));

		if( !($result = $db->sql_query_limit($sql, $limit, $start)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Quartermiles', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Select Quartermile Data From DB By Vehicle ID And Quart Value
	// Usage: get_quartermile_by_vehicle_quart('garage id', 'quart');
	/*========================================================================*/
	function get_quartermile_by_vehicle_quart($garage_id, $quart)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id, g.user_id, q.id as qmid, q.image_id, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, q.rt, q.sixty, q.three, q.eighth, q.eighthmph, q.thou, q.quart, q.quartmph, q.rr_id, d.bhp, d.bhp_unit, d.torque, d.torque_unit, d.boost, d.boost_unit, d.nitrous',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_DYNORUN_TABLE => 'd'),
					'ON'	=> 'q.rr_id = d.id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = q.image_id'
				)
			),
			'WHERE'		=>  "q.quart = $quart AND q.garage_id = $garage_id"
		));

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Dynorun Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (empty($row))
		{
			return;
		}

		return $row;
	}

	/*========================================================================*/
	// Select Quartermile Data From DB By Vehicle ID And Quart Value
	// Usage: get_quartermile_by_vehicle_quart('garage id', 'quart');
	/*========================================================================*/
	function get_pending_quartermiles()
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'g.id as garage_id, u.user_id, g.user_id, q.id as qmid, q.image_id, u.username, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle, q.rt, q.sixty, q.three, q.eighth, q.eighthmph, q.thou, q.quart, q.quartmph, q.rr_id',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'g.user_id = u.user_id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = q.image_id'
				)
			),
			'WHERE'		=>  "q.pending = 1"
		));

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could Not Select Pending Quartermile Data', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Select Quartermile Data By Quartermile ID
	// Usage: get_quartermile('quartermile id');
	/*========================================================================*/
	function get_quartermile($qmid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.*, d.id, d.bhp, d.bhp_unit, i.*, g.made_year, mk.make, md.model, CONCAT_WS(\' \', g.made_year, mk.make, md.model) AS vehicle',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_TABLE => 'g'),
					'ON'	=> 'q.garage_id =g.id'
				)
				,array(
					'FROM'	=> array(GARAGE_MAKES_TABLE => 'mk'),
					'ON'	=> 'g.make_id = mk.id and mk.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_MODELS_TABLE => 'md'),
					'ON'	=> 'g.model_id = md.id and md.pending = 0'
				)
				,array(
					'FROM'	=> array(GARAGE_DYNORUN_TABLE => 'd'),
					'ON'	=> 'q.rr_id = d.id'
				)
				,array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = q.image_id'
				)
			),
			'WHERE'		=>  "q.id = $qmid"
		));

      		if ( !($result = $db->sql_query($sql)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data', '', __LINE__, __FILE__, $sql);
      		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row;
	}

	/*========================================================================*/
	// Select Quartermile Data By Vehicle ID
	// Usage: get_quartermile_by_vehicle('garage id');
	/*========================================================================*/
	function get_quartermile_by_vehicle($cid)
	{
		global $db;

		$sql = $db->sql_build_query('SELECT', 
			array(
			'SELECT'	=> 'q.*, i.attach_id, i.attach_hits, i.attach_ext, i.attach_file, i.attach_thumb_location, i.attach_is_image, i.attach_location',
			'FROM'		=> array(
				GARAGE_QUARTERMILE_TABLE	=> 'q',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GARAGE_IMAGES_TABLE => 'i'),
					'ON'	=> 'i.attach_id = q.image_id'
				)
			),
			'WHERE'		=> 	"q.garage_id = $cid",
			'ORDER_BY'	=>	'q.id'
		));
	
	       	if( !($result = $db->sql_query($sql)) )
	       	{
	        	message_die(GENERAL_ERROR, 'Could Not Select Quartermile Data For Vehicle', '', __LINE__, __FILE__, $sql);
		}

		while ($row = $db->sql_fetchrow($result) )
		{
			$data[] = $row;
		}

		$db->sql_freeresult($result);

		if (empty($data))
		{
			return;
		}
		return $data;
	}

	/*========================================================================*/
	// Build Top Quartermile Runs HTML If Required 
	// Usage: show_topquartermile();
	/*========================================================================*/
	function show_topquartermile()
	{
		global $required_position, $user, $template, $db, $SID, $phpEx, $phpbb_root_path, $garage_config, $board_config;
	
		if ( $garage_config['enable_top_quartermile'] != true )
		{
			return;
		}

		$template_block = 'block_'.$required_position;
		$template_block_row = 'block_'.$required_position.'.row';
		$template->assign_block_vars($template_block, array(
			'BLOCK_TITLE'	=> $user->lang['TOP_QUARTERMILE_RUNS'],
			'COLUMN_1_TITLE'=> $user->lang['VEHICLE'],
			'COLUMN_2_TITLE'=> $user->lang['OWNER'],
			'COLUMN_3_TITLE'=> $user->lang['QUARTERMILE'])
		);
	
	        // What's the count? Default to 10
		$limit = $garage_config['top_quartermile_limit'] ? $garage_config['top_quartermile_limit'] : 10;

		//Get Top Quartermile Times
		$times = $this->get_top_quartermiles(0, 'quart', 'DESC', 0, $limit);

		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($times); $i++)
		{
			//Get Vehicle Info For This Dynorun
			$vehicle_data = $this->get_quartermile_by_vehicle_quart($times[$i]['garage_id'], $times[$i]['quart']);
	
			$mph = (empty($vehicle_data['quartmph'])) ? 'N/A' : $vehicle_data['quartmph'];
	            	$quartermile = $vehicle_data['quart'] .' @ ' . $mph . ' '. $user->lang['QUARTERMILE_SPEED_UNIT'];
	
			$template->assign_block_vars($template_block_row, array(
				'U_COLUMN_1' 	=> append_sid("{$phpbb_root_path}garage.$phpEx?mode=view_vehicle&amp;CID=".$vehicle_data['id']),
				'U_COLUMN_2' 	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=".$vehicle_data['user_id']),
				'COLUMN_1_TITLE'=> $vehicle_data['vehicle'],
				'COLUMN_2_TITLE'=> $vehicle_data['username'],
				'COLUMN_3' 	=> $quartermile)
			);
	 	}
	
		$required_position++;
		return ;
	}

	/*========================================================================*/
	// Build Quartermile Table With/Without Pending Itesm
	// Usage: build_quartermile_table('YES|NO');
	/*========================================================================*/
	function build_quartermile_table($pending)
	{
		global $db, $template, $sort, $phpEx, $order, $garage_config, $garage_template, $user, $garage, $phpbb_root_path;

		$pending= ($pending == 'YES') ? 1 : 0;
		$start 	= (empty($start)) ? 0 : $start;
		$sort 	= (empty($sort)) ? 'quart' : $sort;

		// Sorting Via QuarterMile
		$sort_text = array($user->lang['RT'], $user->lang['SIXTY'], $user->lang['THREE'], $user->lang['EIGHTH'], $user->lang['EIGHTHMPH'], $user->lang['THOU'],  $user->lang['QUART'], $user->lang['QUARTMPH']);
		$sort_values = array('qm.rt', 'qm.sixty', 'qm.three', 'qm.eighth', 'qm.eighthmph', 'qm.thou', 'quart', 'qm.quartmph');

		//Get All Data Posted And Make It Safe To Use
		$addtional_where = '';
		$params = array('make_id', 'model_id');
		$data = $garage->process_post_vars($params);

		if (!empty($data['make_id']))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_make($data['make_id']);
			$addtional_where .= "AND g.make_id = '$make_id'";
			$template->assign_vars(array(
				'MAKE'	=> $data['make'])
			);
		}

		if (!empty($model_id))
		{
			//Pull Required Data From DB
			$data = $garage_model->get_model($data['model_id']);
			$addtional_where .= "AND g.model_id = '$model_id'";
			$template->assign_vars(array(
				'MODEL'	=> $data['model'])
			);
		}

		//First Query To Return Top Time For All Or For Selected Filter...
		$rows = $this->get_top_quartermiles($pending, $sort, $order, $start, $garage_config['cars_per_page'], $addtional_where);

		if ( $pending == 1 AND !empty($rows))
		{
			$template->assign_block_vars('quartermile_pending', array());
		}
	
		//Now Process All Rows Returned And Get Rest Of Required Data	
		for($i = 0; $i < count($rows); $i++)
		{
			//Second Query To Return All Other Data For Top Quartermile Run
			$data = $this->get_quartermile_by_vehicle_quart($rows[$i]['garage_id'], $rows[$i]['quart']);

			$assign_block = ($pending == 1) ? 'quartermile_pending.row' : 'quartermile';
			$template->assign_block_vars($assign_block, array(
				'ROW_NUMBER' 	=> $i + ( $start + 1 ),
				'QMID' 		=> $data['qmid'],
				'U_IMAGE'	=> ($data['image_id']) ? append_sid("{$phpbb_root_path}garage.$phpEx", "mode=view_gallery_item&amp;image_id=". $data['image_id']) : '',
				'U_EDIT'	=> append_sid("{$phpbb_root_path}garage.$phpEx", "mode=edit_quartermile&amp;QMID=" . $data['qmid'] . "&amp;CID=" . $data['id'] . "&amp;PENDING=YES"),
				'IMAGE'		=> $user->img('garage_slip_img_attached', 'SLIP_IMAGE_ATTACHED'),
				'USERNAME' 	=> $data['username'],
				'VEHICLE' 	=> $data['vehicle'],
				'RT' 		=> $data['rt'],
				'SIXTY' 	=> $data['sixty'],
				'THREE' 	=> $data['three'],
				'EIGHTH' 	=> $data['eighth'],
				'EIGHTHMPH' 	=> $data['eighthmph'],
				'THOU' 		=> $data['thou'],
				'QUART' 	=> $data['quart'],
				'QUARTM' 	=> $data['quartmph'],
				'BHP' 		=> $data['bhp'],
				'BHP_UNIT' 	=> $data['bhp_unit'],
				'TORQUE' 	=> $data['torque'],
				'TORQUE_UNIT' 	=> $data['torque_unit'],
				'BOOST' 	=> $data['boost'],
				'BOOST_UNIT' 	=> $data['boost_unit'],
				'NITROUS' 	=> $data['nitrous'],
				'U_VIEWVEHICLE' => append_sid("{$phpbb_root_path}garage.$phpEx?mode=view_vehicle&amp;CID=".$data['id']),
				'U_VIEWPROFILE' => append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=".$data['user_id']))
			);
		}

		$count = count($this->get_top_quartermiles($pending, $sort, $order, 0, 10000000, $addtional_where));
		$pagination = generate_pagination("garage.$phpEx?mode=dynorun&amp;order=$order", $count, $garage_config['cars_per_page'], $start);
		
		$template->assign_vars(array(
            		'EDIT' 		=> ($garage_config['enable_images']) ? $user->img('garage_edit', 'EDIT') : $user->lang['EDIT'],
			'S_MODE_SELECT'	=> $garage_template->dropdown('sort', $sort_text, $sort_values),
			'PAGINATION' 	=> $pagination,
			'PAGE_NUMBER' 	=> sprintf($user->lang['PAGE_OF'], ( floor( $start / $garage_config['cars_per_page'] ) + 1 ), ceil( $count / $garage_config['cars_per_page'] )))
		);

		//Reset Sort Order For Pending Page
		$sort='';
		return $count;
	}

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function approve_quartermile($post_id_list, $mode)
	{
		global $db, $template, $user, $config;
		global $phpEx, $phpbb_root_path;

		$redirect = request_var('redirect', $user->data['session_page']);
		$success_msg = '';

		$s_hidden_fields = build_hidden_fields(array(
			'i'		=> 'garage',
			'mode'		=> $mode,
			'post_id_list'	=> $post_id_list,
			'f'				=> $forum_id,
			'action'		=> 'approve',
			'redirect'		=> $redirect)
		);

		if (confirm_box(true))
		{
			$notify_poster = (isset($_REQUEST['notify_poster'])) ? true : false;

			$post_info = get_post_data($post_id_list, 'm_approve');

			// If Topic -> total_topics = total_topics+1, total_posts = total_posts+1, forum_topics = forum_topics+1, forum_posts = forum_posts+1
			// If Post -> total_posts = total_posts+1, forum_posts = forum_posts+1, topic_replies = topic_replies+1

			$total_topics = $total_posts = $forum_topics = $forum_posts = 0;
			$topic_approve_sql = $topic_replies_sql = $post_approve_sql = $topic_id_list = array();

			foreach ($post_info as $post_id => $post_data)
			{
				$topic_id_list[$post_data['topic_id']] = 1;
	
				// Topic or Post. ;)
				if ($post_data['topic_first_post_id'] == $post_id)
				{
					if ($post_data['forum_id'])
					{
						$total_topics++;
						$forum_topics++;
					}

					$topic_approve_sql[] = $post_data['topic_id'];
				}
				else
				{
					if (!isset($topic_replies_sql[$post_data['topic_id']]))
					{
						$topic_replies_sql[$post_data['topic_id']] = 1;
					}
					else
					{
						$topic_replies_sql[$post_data['topic_id']]++;
					}
				}
	
				if ($post_data['forum_id'])
				{
					$total_posts++;
					$forum_posts++;
				}
	
				$post_approve_sql[] = $post_id;
			}

			if (sizeof($topic_approve_sql))
			{
				$sql = 'UPDATE ' . TOPICS_TABLE . '
					SET topic_approved = 1
						WHERE ' . $db->sql_in_set('topic_id', $topic_approve_sql);
				$db->sql_query($sql);
			}

			if (sizeof($post_approve_sql))
			{
				$sql = 'UPDATE ' . POSTS_TABLE . '
					SET post_approved = 1
					WHERE ' . $db->sql_in_set('post_id', $post_approve_sql);
				$db->sql_query($sql);
			}

			if (sizeof($topic_replies_sql))
			{
				foreach ($topic_replies_sql as $topic_id => $num_replies)
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . "
						SET topic_replies = topic_replies + $num_replies
						WHERE topic_id = $topic_id";
					$db->sql_query($sql);
				}
			}
	
			if ($forum_topics || $forum_posts)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . '
					SET ';
				$sql .= ($forum_topics) ? "forum_topics = forum_topics + $forum_topics" : '';
				$sql .= ($forum_topics && $forum_posts) ? ', ' : '';
				$sql .= ($forum_posts) ? "forum_posts = forum_posts + $forum_posts" : '';
				$sql .= " WHERE forum_id = $forum_id";

				$db->sql_query($sql);
			}

			if ($total_topics)
			{
				set_config('num_topics', $config['num_topics'] + $total_topics, true);
			}
	
			if ($total_posts)
			{
				set_config('num_posts', $config['num_posts'] + $total_posts, true);
			}
			unset($topic_approve_sql, $topic_replies_sql, $post_approve_sql);
	
			update_post_information('topic', array_keys($topic_id_list));
			update_post_information('forum', $forum_id);
			unset($topic_id_list);

			$messenger = new messenger();

			// Notify Poster?
			if ($notify_poster)
			{
				$email_sig = str_replace('<br />', "\n", "-- \n" . $config['board_email_sig']);

				foreach ($post_info as $post_id => $post_data)
				{
					if ($post_data['poster_id'] == ANONYMOUS)
					{
						continue;
					}

					$email_template = ($post_data['post_id'] == $post_data['topic_first_post_id'] && $post_data['post_id'] == $post_data['topic_last_post_id']) ? 'topic_approved' : 'post_approved';

					$messenger->template($email_template, $post_data['user_lang']);

					$messenger->replyto($config['board_email']);
					$messenger->to($post_data['user_email'], $post_data['username']);
					$messenger->im($post_data['user_jabber'], $post_data['username']);

					$messenger->assign_vars(array(
						'EMAIL_SIG'		=> $email_sig,
						'SITENAME'		=> $config['sitename'],
						'USERNAME'		=> html_entity_decode($post_data['username']),
						'POST_SUBJECT'	=> html_entity_decode(censor_text($post_data['post_subject'])),
						'TOPIC_TITLE'	=> html_entity_decode(censor_text($post_data['topic_title'])),
		
						'U_VIEW_TOPIC'	=> generate_board_url() . "/viewtopic.$phpEx?f=$forum_id&t={$post_data['topic_id']}&e=0",
						'U_VIEW_POST'	=> generate_board_url() . "/viewtopic.$phpEx?f=$forum_id&t={$post_data['topic_id']}&p=$post_id&e=$post_id")
					);

					$messenger->send($post_data['user_notify_type']);
					$messenger->reset();
				}

				$messenger->save_queue();
			}

			// Send out normal user notifications
			$email_sig = str_replace('<br />', "\n", "-- \n" . $config['board_email_sig']);
	
			foreach ($post_info as $post_id => $post_data)
			{
				if ($post_id == $post_data['topic_first_post_id'] && $post_id == $post_data['topic_last_post_id'])
				{
					// Forum Notifications
					user_notification('post', $post_data['topic_title'], $post_data['topic_title'], $post_data['forum_name'], $forum_id, $post_data['topic_id'], $post_id);
				}
				else
				{
					// Topic Notifications
					user_notification('reply', $post_data['post_subject'], $post_data['topic_title'], $post_data['forum_name'], $forum_id, $post_data['topic_id'], $post_id);
				}
			}
			unset($post_info);

			if ($forum_topics)
			{
				$success_msg = ($forum_topics == 1) ? 'TOPIC_APPROVED_SUCCESS' : 'TOPICS_APPROVED_SUCCESS';
			}
			else
			{
				$success_msg = (sizeof($post_id_list) == 1) ? 'POST_APPROVED_SUCCESS' : 'POSTS_APPROVED_SUCCESS';
			}
		}
		else
		{
			$template->assign_vars(array(
				'S_NOTIFY_POSTER'	=> true,
				'S_APPROVE'			=> true)
			);

			confirm_box(false, 'APPROVE_POST' . ((sizeof($post_id_list) == 1) ? '' : 'S'), $s_hidden_fields, 'mcp_approve.html');
		}

		$redirect = request_var('redirect', "index.$phpEx");
		$redirect = reapply_sid($redirect);

		if (!$success_msg)
		{
			redirect($redirect);
		}
		else
		{
			meta_refresh(3, $redirect);
			trigger_error($user->lang[$success_msg] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>'));
		}
	}

	/*========================================================================*/
	// Approve Quartermile Times
	// Usage: approve_quartermile(array(), 'mode');
	/*========================================================================*/
	function disapprove_quartermile($post_id_list, $mode)
	{
		global $db, $template, $user, $config;
		global $phpEx, $phpbb_root_path;

		$redirect = request_var('redirect', build_url(array('t', 'mode')) . '&amp;mode=unapproved_quartermiles');
		$reason = request_var('reason', '', true);
		$reason_id = request_var('reason_id', 0);
		$success_msg = $additional_msg = '';

		$s_hidden_fields = build_hidden_fields(array(
			'i'			=> 'queue',
			'mode'			=> $mode,
			'id_list'		=> $post_id_list,
			'action'		=> 'disapprove_quartermile',
			'redirect'		=> $redirect)
		);

		$notify_poster = (isset($_REQUEST['notify_poster'])) ? true : false;
		$disapprove_reason = '';

		if ($reason_id)
		{
			$sql = 'SELECT reason_title, reason_description
				FROM ' . REPORTS_REASONS_TABLE . "
				WHERE reason_id = $reason_id";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$row || (!$reason && $row['reason_title'] == 'other'))
			{
				$additional_msg = $user->lang['NO_REASON_DISAPPROVAL'];
				unset($_POST['confirm']);
			}
			else
			{
				// If the reason is defined within the language file, we will use the localized version, else just use the database entry...
				$disapprove_reason = ($row['reason_title'] != 'other') ? ((isset($user->lang['report_reasons']['DESCRIPTION'][strtoupper($row['reason_title'])])) ? $user->lang['report_reasons']['DESCRIPTION'][strtoupper($row['reason_title'])] : $row['reason_description']) : '';
				$disapprove_reason .= ($reason) ? "\n\n" . $reason : '';
			}
		}

		if (confirm_box(true))
		{
			$post_info = get_post_data($post_id_list, 'm_approve');
	

			$forum_topics_real = 0;
			$topic_replies_real_sql = $post_disapprove_sql = $topic_id_list = array();

			foreach ($post_info as $post_id => $post_data)
			{
				$topic_id_list[$post_data['topic_id']] = 1;

				// Topic or Post. ;)
				if ($post_data['topic_first_post_id'] == $post_id && $post_data['topic_last_post_id'] == $post_id)
				{
					if ($post_data['forum_id'])
					{
						$forum_topics_real++;
					}
				}
				else
				{
					if (!isset($topic_replies_real_sql[$post_data['topic_id']]))
					{
						$topic_replies_real_sql[$post_data['topic_id']] = 1;
					}
					else
					{
						$topic_replies_real_sql[$post_data['topic_id']]++;
					}
				}

				$post_disapprove_sql[] = $post_id;
			}

			if ($forum_topics_real)
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . "
					SET forum_topics_real = forum_topics_real - $forum_topics_real
					WHERE forum_id = $forum_id";
				$db->sql_query($sql);
			}

			if (sizeof($topic_replies_real_sql))
			{
				foreach ($topic_replies_real_sql as $topic_id => $num_replies)
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . "
						SET topic_replies_real = topic_replies_real - $num_replies
						WHERE topic_id = $topic_id";
					$db->sql_query($sql);
				}
			}

			if (sizeof($post_disapprove_sql))
			{
				if (!function_exists('delete_posts'))
				{
					include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
				}

				// We do not check for permissions here, because the moderator allowed approval/disapproval should be allowed to delete the disapproved posts
				delete_posts('post_id', $post_disapprove_sql);
			}
			unset($post_disapprove_sql, $topic_replies_real_sql);

			update_post_information('topic', array_keys($topic_id_list));
			update_post_information('forum', $forum_id);
			unset($topic_id_list);

			$messenger = new messenger();

			// Notify Poster?
			if ($notify_poster)
			{
				$email_sig = str_replace('<br />', "\n", "-- \n" . $config['board_email_sig']);

				foreach ($post_info as $post_id => $post_data)
				{
					if ($post_data['poster_id'] == ANONYMOUS)
					{
						continue;
					}

					$email_template = ($post_data['post_id'] == $post_data['topic_first_post_id'] && $post_data['post_id'] == $post_data['topic_last_post_id']) ? 'topic_disapproved' : 'post_disapproved';

					$messenger->template($email_template, $post_data['user_lang']);

					$messenger->replyto($config['board_email']);
					$messenger->to($post_data['user_email'], $post_data['username']);
					$messenger->im($post_data['user_jabber'], $post_data['username']);

					$messenger->assign_vars(array(
						'EMAIL_SIG'	=> $email_sig,
						'SITENAME'	=> $config['sitename'],
						'USERNAME'	=> html_entity_decode($post_data['username']),
						'REASON'	=> html_entity_decode($disapprove_reason),
						'POST_SUBJECT'	=> html_entity_decode(censor_text($post_data['post_subject'])),
						'TOPIC_TITLE'	=> html_entity_decode(censor_text($post_data['topic_title'])))
					);
	
					$messenger->send($post_data['user_notify_type']);
					$messenger->reset();
				}	

				$messenger->save_queue();
			}
			unset($post_info, $disapprove_reason);
	
			if ($forum_topics_real)
			{
				$success_msg = ($forum_topics_real == 1) ? 'TOPIC_DISAPPROVED_SUCCESS' : 'TOPICS_DISAPPROVED_SUCCESS';
			}
			else
			{
				$success_msg = (sizeof($post_id_list) == 1) ? 'POST_DISAPPROVED_SUCCESS' : 'POSTS_DISAPPROVED_SUCCESS';
			}
		}
		else
		{
			include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

			display_reasons($reason_id);

			$template->assign_vars(array(
				'S_NOTIFY_POSTER'	=> true,
				'S_APPROVE'		=> false,
				'REASON'		=> $reason,
				'ADDITIONAL_MSG'	=> $additional_msg)
			);

			confirm_box(false, 'DISAPPROVE_QUARTERMILE' . ((sizeof($post_id_list) == 1) ? '' : 'S'), $s_hidden_fields, 'mcp_approve.html');
		}

		$redirect = request_var('redirect', "index.$phpEx");
		$redirect = reapply_sid($redirect);

		if (!$success_msg)
		{
			redirect($redirect);
		}
		else
		{
			meta_refresh(3, $redirect);
			trigger_error($user->lang[$success_msg] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>'));
		}
	}
}

$garage_quartermile = new garage_quartermile();

?>
