<?php
/** 
*
* @package ucp
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class ucp_garage_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_garage',
			'title'		=> 'UCP_GARAGE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'options'	=> array('title' => 'UCP_GARAGE_OPTIONS', 'auth' => '', 'cat' => array('UCP_GARAGE')),
				'notify'	=> array('title' => 'UCP_GARAGE_NOTIFY', 'auth' => '', 'cat' => array('UCP_GARAGE')),
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
