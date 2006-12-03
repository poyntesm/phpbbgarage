<?php
/** 
*
* @package mcp
* @version $Id: mcp_garage.php,v 1.5 2006/06/10 16:09:46 naderman Exp $
* @copyright (c) 2005 phpBB Group 
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
				'unapproved_makes'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_MAKES', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_models'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_MODELS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_business'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_BUSINESS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_quartermiles'	=> array('title' => 'MCP_GARAGE_UNAPPROVED_QUARTERMILES', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
				'unapproved_dynoruns'		=> array('title' => 'MCP_GARAGE_UNAPPROVED_DYNORUNS', 'auth' => 'acl_m_garage', 'cat' => array('MCP_GARAGE')),
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
