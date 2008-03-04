<?php
/** 
*
* garage [English]
*
* @package language
* @version $Id: garage_common.php 451 2007-07-25 14:12:04Z poyntesm $
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

$lang = array_merge($lang, array(
	'GARAGE'			=> 'Garage',
	'QUARTERMILE'			=> '&frac14; Mile',
	'DYNORUN'			=> 'Dynorun',
	'PENDING_GARAGE_ITEMS'		=> 'Pending Garage Items',
	'VIEWING_GARAGE'		=> 'Viewing Garage',
	'USERS_GARAGE'			=> 'View user\'s Garage',
	'REMOVE_GARAGE_INSTALL'		=> 'Please delete, move or rename the garage install directory before you use your garage. If this directory is still presentthe garage will be unavailable',
	'GARAGE_DISABLE'		=> 'Sorry but this garage is currently unavailable while the admin is performing work on it.',
));

?>
