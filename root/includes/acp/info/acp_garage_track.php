<?php
/** 
*
* @package acp
* @version $Id: $
* @copyright (c) 2006 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class acp_garage_track_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_garage_track',
			'title'		=> 'ACP_GARAGE_TRACK_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'track'	=> array('title' => 'ACP_GARAGE_TRACK', 'auth' => 'acl_a_garage_track', 'cat' => array('ACP_GARAGE_CONFIGURATION')),
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
