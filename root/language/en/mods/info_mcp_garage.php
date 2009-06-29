<?php
/** 
*
* garage [English]
*
* @package language
* @version $Id: info_mcp_garage.php 451 2007-07-25 14:12:04Z poyntesm $
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/*
* DO NOT CHANGE 
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/*
* Language keys for auto-inclusion for MCP Module titles
*/
$lang = array_merge($lang, array(
	'MCP_GARAGE'					=> 'Garage',
	'MCP_GARAGE_UNAPPROVED_VEHICLES'		=> 'Vehicles awaiting approval',
	'MCP_GARAGE_UNAPPROVED_GUESTBOOK_COMMENTS'	=> 'Comments awaiting approval',
	'MCP_GARAGE_UNAPPROVED_MAKES'			=> 'Makes awaiting approval',
	'MCP_GARAGE_UNAPPROVED_MODELS'			=> 'Models awaiting approval',
	'MCP_GARAGE_UNAPPROVED_BUSINESS'		=> 'Businesses awaiting approval',
	'MCP_GARAGE_UNAPPROVED_QUARTERMILES'		=> '&frac14; miles awaiting approval',
	'MCP_GARAGE_UNAPPROVED_DYNORUNS'		=> 'Dynoruns awaiting approval',
	'MCP_GARAGE_UNAPPROVED_LAPS'			=> 'Laps awaiting approval',
	'MCP_GARAGE_UNAPPROVED_TRACKS'			=> 'Tracks awaiting approval', 
	'MCP_GARAGE_UNAPPROVED_PRODUCTS'		=> 'Products awaiting approval',
));

?>
