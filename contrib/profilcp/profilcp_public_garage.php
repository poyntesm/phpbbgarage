<?php 
if ( !defined('IN_PHPBB') ) 
{ 
   die('Hacking attempt'); 
   exit; 
} 

if ( !empty($setmodules) ) 
{ 
   pcp_set_sub_menu('viewprofile', 'profilcp_garage_shortcut', 60, __FILE__, 'profilcp_garage_shortcut', 'profilcp_garage_pagetitle'  ); 
   return; 
} 

// 
// template file 
$template->set_filenames(array( 
   'body' => 'profilcp/profil_garage_body.tpl') 
); 

//-- mod start : Garage 0.1.0
//-- add
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_garage_error.' . $phpEx);
require($phpbb_root_path . 'includes/functions_garage.' . $phpEx);

$sql = "SELECT g.*, images.*, makes.make, models.model, user.username, user.user_avatar_type,
	user.user_allowavatar, user.user_avatar,
        count(mods.id) AS total_mods,  
        ( SUM(mods.price) + SUM(mods.install_price) ) AS total_spent 
        FROM " . GARAGE_TABLE . " AS g  
		LEFT JOIN " . USERS_TABLE ." AS user ON g.member_id = user.user_id
                LEFT JOIN " . GARAGE_MODS_TABLE . " AS mods ON g.id = mods.garage_id
                LEFT JOIN " . GARAGE_MAKES_TABLE . " AS makes ON g.make_id = makes.id
                LEFT JOIN " . GARAGE_MODELS_TABLE . " AS models ON g.model_id = models.id
		LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = g.image_id
        WHERE g.member_id = " . $view_userdata['user_id'] . " AND g.main_vehicle = 1
                GROUP BY g.id";

if ( !($result = $db->sql_query($sql)) )
{
       	message_die(GENERAL_ERROR, 'Could Not Select Vehicle Data', '', __LINE__, __FILE__, $sql);
}

$vehicle_row = $db->sql_fetchrow($result);

if ( $db->sql_numrows($result) > 0 )
{
	$template->assign_block_vars('garage_vehicle', array());
	$total_spent = $vehicle_row['total_spent'] ? $vehicle_row['total_spent'] : 0;

	//Display Just Thumbnails Of All Images Or Just One Main Image
	if ( $garage_config['profile_thumbs'] == 1 )
	{
	       	$gallery_query = "SELECT gallery.id, images.attach_id, images.attach_hits, images.attach_ext, 
                        	        images.attach_file, images.attach_thumb_location, images.attach_is_image,
                	                images.attach_location
                                     FROM " . GARAGE_IMAGES_TABLE . " AS images 
					LEFT JOIN " . GARAGE_GALLERY_TABLE . " AS gallery ON images.attach_id = gallery.image_id 
                                     	LEFT JOIN " . GARAGE_TABLE . " AS garage ON gallery.garage_id = garage.id 
                                     WHERE garage.id = " . $vehicle_row['id'] .";";
		if ( !($result = $db->sql_query($gallery_query)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Image Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      	}
	
        	while ( $gallery_data = $db->sql_fetchrow($result) )
	       	{
            		if ( $gallery_data['attach_is_image'] )
           		{
                		// Do we have a thumbnail?  If so, our job is simple here :)
				if ( (empty($gallery_data['attach_thumb_location']) == FALSE) AND ($gallery_data['attach_thumb_location'] != $gallery_data['attach_location']) AND ( $vehicle_images_found <= 12) )
                		{
                    			// Form the image link
					$thumb_image = GARAGE_UPLOAD_PATH . $gallery_data['attach_thumb_location'];
					$id = $gallery_data['attach_id'];
					$title = $gallery_data['attach_file'];
					$hilite_image .= '<a href=garage.php?mode=view_gallery_item&amp;type=garage_gallery&amp;id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               			} 
			}
	        }

		$mod_query = "SELECT m.*,images.attach_id, images.attach_hits, images.attach_ext, 
                        images.attach_file, images.attach_thumb_location, images.attach_is_image,
                        images.attach_location
         		FROM " . GARAGE_MODS_TABLE . " as m
                        LEFT JOIN " . GARAGE_IMAGES_TABLE . " AS images ON images.attach_id = m.image_id
	       		WHERE garage_id = " . $vehicle_row['id'] .";";
		if ( !($result = $db->sql_query($mod_query)) )
      		{
         		message_die(GENERAL_ERROR, 'Could Not Select Image Data For Vehicle', '', __LINE__, __FILE__, $sql);
	      	}

        	while ( $mod_gallery = $db->sql_fetchrow($result) )
	       	{
            		if ( $mod_gallery['attach_is_image'] )
           		{
                		// Do we have a thumbnail?  If so, our job is simple here :)
				if ( (empty($mod_gallery['attach_thumb_location']) == FALSE) AND ($mod_gallery['attach_thumb_location'] != $gallery_data['attach_location']) AND ( $vehicle_images_found <= 12) )
                		{
                    			// Form the image link
					$thumb_image = GARAGE_UPLOAD_PATH . $mod_gallery['attach_thumb_location'];
					$id = $mod_gallery['attach_id'];
					$title = $mod_gallerya['attach_file'];
					$hilite_image .= '<a href=garage.php?mode=view_gallery_item&amp;type=garage_gallery&amp;id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $thumb_image .'" class="attach"  /></a> ';
               			} 
			}
	        }
	}
	//Looks Like We Only Need To Draw One Main Image
	else
	{
		if ( ($vehicle_row['image_id']) AND ($vehicle_row['attach_is_image']) AND (!empty($vehicle_row['attach_thumb_location'])) AND (!empty($vehicle_row['attach_location'])) )
		{
			// Check to see if this is a remote image
			if ( preg_match( "/^http:\/\//i", $vehicle_row['attach_location']) )
			{
				$image = $vehicle_row['attach_location'];
				$id = $vehicle_row['attach_id'];
				$title = $vehicle_row['attach_file'];
				$total_image_views = $vehicle_row['attach_hits'];
				$hilite_image = '<a href=garage.php?mode=view_gallery_item&amp;type=garage_mod&amp;id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
			}
			else
			{
				$image = GARAGE_UPLOAD_PATH . $vehicle_row['attach_location'];
				$id = $vehicle_row['attach_id'];
				$title = $vehicle_row['attach_file'];
				$total_image_views = $vehicle_row['attach_hits'];
				$hilite_image = '<a href=garage.php?mode=view_gallery_item&amp;type=garage_mod&amp;id='. $id .' title=' . $title .' target="_blank"><img hspace="5" vspace="5" src="' . $image .'" class="attach"  /></a>';
			}
		}
	}

	if ( (empty($gallery_modification_images) == FALSE) AND (empty($gallery_vehicle_images) == FALSE) )
	{
		$template->assign_block_vars('switch_top_block.owned_no.gallery_all', array(
			'VEHICLE_IMAGES' => $gallery_vehicle_images,
			'MODIFICATION_IMAGES' => $gallery_modification_images)
		);
	}
	if ( (empty($gallery_modification_images) == TRUE) AND (empty($gallery_vehicle_images) == FALSE) )
	{
		$template->assign_block_vars('switch_top_block.owned_no.gallery_vehicle', array(
			'VEHICLE_IMAGES' => $gallery_vehicle_images)
		);
	}
	if ( (empty($gallery_modification_images) == FALSE) AND (empty($gallery_vehicle_images) == TRUE) )
	{
		$template->assign_block_vars('switch_top_block.owned_no.gallery_modification', array(
			'MODIFICATION_IMAGES' => $gallery_modification_images)
		);
	}

	$garage_img ='<a href="' . append_sid("garage.$phpEx?mode=browse&search=yes&user=".$view_userdata['username']."") . '"><img src="' . $images['icon_garage'] . '" alt="'.$lang['Garage'].'" title="'.$lang['Garage'].'" border="0" /></a>';

	$template->assign_vars(array(
		'L_VEHICLE' => $lang['Vehicle'],
		'L_GARAGE' => $lang['Garage'],
		'L_COLOUR' => $lang['Colour'],
		'L_MILEAGE' => $lang['Mileage'],
		'L_PRICE' => $lang['Purchased_Price'],
		'L_TOTAL_MODS' => $lang['Total_Mods'],
		'L_TOTAL_SPENT' => $lang['Total_Spent'],
		'L_DESCRIPTION' => $lang['Description'],
		'L_SEARCH_USER_GARAGE' => $lang['Search_User_Garage'],
		'YEAR' => $vehicle_row['year'],
		'MAKE' => $vehicle_row['make'],
		'MODEL' => $vehicle_row['model'],
       		'COLOUR' => $vehicle_row['color'],
	    	'HILITE_IMAGE' => $hilite_image,
        	'MILEAGE' => $vehicle_row['mileage'],
	    	'MILEAGE_UNITS' => $vehicle_row['mileage_unit'],
        	'PRICE' => $vehicle_row['price'],
	    	'CURRENCY' => $vehicle_row['currency'],
        	'TOTAL_MODS' => $vehicle_row['total_mods'],
	   	'TOTAL_SPENT' => $total_spent,
        	'TOTAL_VIEWS' => $vehicle_row['views'],
	    	'DESCRIPTION' => $vehicle_row['comments'],
	    	'GARAGE_IMG' => $garage_img,
		'USERNAME' => $view_userdata['username'],
		'U_SEARCH_USER_GARAGE' => append_sid("garage.$phpEx?mode=browse"))
	);
}



// page 
$template->pparse('body'); 


?>
