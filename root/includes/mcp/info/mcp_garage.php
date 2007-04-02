<?php
/** 
*
* @package mcp
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class mcp_garage_info
{
	function module()
	{
		return array(
			'filename'	=> 'mcp_garage',
			'title'		=> 'MCP_GARAGE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'unapproved_vehicles'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_VEHICLES', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_makes'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_MAKES', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_models'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_MODELS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_business'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_BUSINESS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_quartermiles'	=> array('title' => 'MCP_GARAGE_UNAPPROVED_QUARTERMILES', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_dynoruns'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_DYNORUNS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_guestbook_comments'	=> array('title' => 'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_laps'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_LAPS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_tracks'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_TRACKS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_products'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_PRODUCTS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
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
