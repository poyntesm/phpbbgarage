<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_garage_product
{
	var $u_action;

	function main($id, $mode)
	{
		/**
		* Setup global variables such as $db 
		*/
		global $db, $user, $auth, $template, $cache, $garage, $garage_config, $garage_template, $garage_vehicle;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx, $garage_business, $garage_modification;

		/**
		* Build All Garage Classes e.g $garage_images->
		*/
		require($phpbb_root_path . 'includes/mods/class_garage_template.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_vehicle.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_business.' . $phpEx);
		require($phpbb_root_path . 'includes/mods/class_garage_modification.' . $phpEx);

		/**
		* Setup page variables such as title, template & available language strings
		*/
		$user->add_lang('acp/garage');
		$this->tpl_name = 'acp_garage_product';
		$this->page_title = 'ACP_MANAGE_PRODUCTS';

		/**
		* Setup variables required
		*/
		$action			= request_var('action', '');
		$update			= (isset($_POST['update'])) ? true : false;
		$manufacturer_id	= request_var('manufacturer_id', '');
		$product_id		= request_var('product_id', '');
		$errors 		= array();

		/**
		* Perform a set action based on value for $action
		* An action is normally a DB action such as insert/update/delete
		* An action will only show a page to show success or failure
		*/
		if ($update)
		{
			switch ($action)
			{
				/**
				* Insert product into database
				*/
				case 'add_product':
					$params = array('category_id' => '', 'manufacturer_id' => $manufacturer_id, 'pending' => '0');
					$data = $garage->process_vars($params);
					$params = array('title' => '');
					$data += $garage->process_mb_vars($params);

					$garage_modification->insert_product($data);
					add_log('admin', 'LOG_GARAGE_PRODUCT_CREATED', $data['title']);

					trigger_error($user->lang['PRODUCT_CREATED'] . adm_back_link($this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id"));
				break;

				/**
				* Update an existing product
				*/
				case 'edit_product':
					$params = array('category_id' => '', 'manufacturer_id' => $manufacturer_id, 'product_id' => $product_id);
					$data = $garage->process_vars($params);
					$params = array('title' => '');
					$data += $garage->process_mb_vars($params);

					$garage_modification->update_product($data);
					add_log('admin', 'LOG_GARAGE_PRODUCT_UPDATED', $data['title']);

					trigger_error($user->lang['PRODUCT_UPDATED'] . adm_back_link($this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id"));
				break;

				/**
				* Update an existing product
				*/
				case 'delete_product':
					$action_modifications	= request_var('action_modifications', '');
					$product_to_id		= request_var('modifications_to_id', 0);

					$garage_modification->delete_product($product_id, $action_modifications, $product_to_id);

					if (sizeof($errors))
					{
						break;
					}

					trigger_error($user->lang['PRODUCT_DELETED'] . adm_back_link($this->u_action  . "&amp;action=products&amp;manufacturer_id=$manufacturer_id"));
				break;
			}
		}

		/**
		* Perform a set action based on value for $action
		*/
		switch ($action)
		{
			/**
			* Page to delete an existing product
			* Administrators decides where modifications can be moved to
			*/
			case 'delete_product':
				if (!$product_id)
				{
					trigger_error($user->lang['NO_PRODUCT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$product_data = $garage_modification->get_product($product_id);
				$products_data = $garage_modification->get_products_by_manufacturer($product_data['business_id'], $product_data['category_id']);
				$select_to = $garage_template->build_move_to($products_data, $product_id, 'title');

				$template->assign_vars(array(
					'S_DELETE_PRODUCT'		=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete_product&amp;product_id=$product_id&amp;manufacturer_id=".$product_data['business_id'],
					'U_BACK'			=> $this->u_action . "&amp;action=products&amp;manufacturer_id=".$product_data['business_id'],
					'S_MOVE'			=> (!empty($select_to)) ? true : false ,
					'S_MOVE_OPTIONS'		=> $select_to,
					'PRODUCT'				=> $product_data['title'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));
			break;

			/**
			* Display page to add or edit a product
			*/
			case 'add_product':
			case 'edit_product':
				if ($action == 'edit_product')
				{
					$this->page_title = 'EDIT_PRODUCT';
					$row = $garage_modification->get_product($product_id);

					if (!$update)
					{
						$product_data = $row;
					}
				}
				else
				{
					$this->page_title = 'CREATE_PRODUCT';

					/**
					* Fill producy data with default values
					*/
					if (!$update)
					{
						$product_data = array(
							'title'		=> request_var('product', '', true),
							'category_id'	=> '',
							'business_id'	=> '',
						);
					}
				}

				$categories = $garage->get_categories();
				$manufacturers = $garage_business->get_business_by_type(BUSINESS_PRODUCT);
				$garage_template->category_dropdown($categories, $product_data['category_id']);
				$garage_template->manufacturer_dropdown($manufacturers, $product_data['business_id']);
				$template->assign_vars(array(
					'S_EDIT_PRODUCT'	=> true,
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'U_BACK'		=> $this->u_action . "&amp;action=products&amp;manufacturer_id=$manufacturer_id",
					'U_ACTION'		=> $this->u_action . "&amp;action=$action&amp;manufacturer_id=$manufacturer_id&amp;product_id=$product_id",
					'PRODUCT'		=> $product_data['title'],
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));
				return;
			break;

			/**
			* Page to display products for a specific manufacturer
			* Due to wanting approval & disapproval to appear seamless we have them within this action also
			*/
			case 'approve':
			case 'disapprove':
			case 'products':
				if ($action == 'approve')
				{
					$data = $garage_modification->get_product($product_id);
					$garage->update_single_field(GARAGE_PRODUCTS_TABLE, 'pending', 0, 'id', $product_id);
					add_log('admin', 'LOG_GARAGE_PRODUCT_APPROVED', $data['title']);
				}

				if ($action == 'disapprove')
				{
					$data = $garage_modification->get_product($product_id);
					$garage->update_single_field(GARAGE_PRODUCTS_TABLE, 'pending', 1, 'id', $product_id);
					add_log('admin', 'LOG_GARAGE_PRODUCT_DISAPPROVED', $data['title']);
				}

				$categories = $garage_modification->get_manufacturer_modification_categories($manufacturer_id);
				$manufacturer = $garage_business->get_business($manufacturer_id);

				for ( $i = 0; $i < count($categories); $i++ )
				{

	       				$template->assign_block_vars('category', array(
			           		'CATEGORY_TITLE' => $categories[$i]['title'])
	       				);
					$products = $garage_modification->get_products_by_manufacturer($manufacturer_id, $categories[$i]['id']);
					for ( $j = 0; $j < count($products); $j++ )
					{
						$url = $this->u_action . "&amp;manufacturer_id=$manufacturer_id&amp;product_id={$products[$j]['id']}";
						$template->assign_block_vars('category.product', array(
							'ID' 			=> $products[$j]['id'],
							'PRODUCT' 		=> $products[$j]['title'],
							'S_DISAPPROVED'		=> ($products[$j]['pending'] == 1) ? true : false,
							'S_APPROVED'		=> ($products[$j]['pending'] == 0) ? true : false,
							'U_APPROVE'		=> $url . '&amp;action=approve',
							'U_DISAPPROVE'		=> $url . '&amp;action=disapprove',
							'U_EDIT'		=> $url . '&amp;action=edit_product',
							'U_DELETE'		=> $url . '&amp;action=delete_product',
						));
					}
				}
	
				$template->assign_vars(array(
					'S_ERROR'		=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'MANUFACTURER'		=> $manufacturer['title'],
					'MANUFACTURER_ID'	=> $manufacturer_id,
					'U_LIST_MANUFACTURERS'	=> $url = $this->u_action,
					'S_LIST_PRODUCTS'	=> true,
				));
			break;
		}
		
		/**
		* Display default page to show list of business's
		* Select business to display products
		*/
		$manufacturers = $garage_business->get_business_by_type(BUSINESS_PRODUCT);
		for( $i = 0; $i < count($manufacturers); $i++ )
		{
			$url = $this->u_action . "&amp;manufacturer_id={$manufacturers[$i]['id']}";
			$template->assign_block_vars('manufacturer', array(
				'ID' 			=> $manufacturers[$i]['id'],
				'MANUFACTURER' 		=> $manufacturers[$i]['title'],
				'U_LIST_PRODUCTS'	=> $url . '&amp;action=products',
			));
		}
	
		$template->assign_vars(array(
			'S_ERROR'	=> (sizeof($errors)) ? true : false,
			'ERROR_MSG'	=> (sizeof($errors)) ? implode('<br />', $errors) : '',
		));
	}
}
?>
