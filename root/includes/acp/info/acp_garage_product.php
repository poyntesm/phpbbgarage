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
* @package module_install
*/
class acp_garage_product_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_product',
			'title'		=> 'ACP_GARAGE_PRODUCT_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'products'	=> array('title' => 'ACP_GARAGE_PRODUCTS', 'auth' => 'acl_a_garage_product', 'cat' => array('ACP_GARAGE_MANAGEMENT')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
