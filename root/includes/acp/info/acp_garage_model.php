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
class acp_garage_model_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_model',
			'title'		=> 'ACP_GARAGE_MODEL_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'makes' => array('title' => 'ACP_GARAGE_MODELS', 'auth' => 'acl_a_garage_model', 'cat' => array('ACP_GARAGE_MANAGEMENT')),
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
