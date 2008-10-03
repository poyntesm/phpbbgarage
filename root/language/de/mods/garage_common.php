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
	'QUARTERMILE'			=> '&frac14; Meile',
	'DYNORUN'			=> 'Leistungstest',
	'PENDING_GARAGE_ITEMS'		=> 'Garage-Einträge in der Warteliste',
	'VIEWING_GARAGE'		=> 'Garage betrachten',
	'USERS_GARAGE'			=> 'Garage des Benutzers ansehen',
	'REMOVE_GARAGE_INSTALL' 		=> 'Bevor du die Garage benutzt, lösche, verschiebe oder benenne das Garage-Installationsverzeichnis bitte um. Solange das Verzeichnis existiert, ist die Garage nicht verfügbar.',
	'GARAGE_DISABLE'		=> 'Sorry, aber die Garage ist vorübergehend nicht verfügbar, da der Administrator diese gerade überarbeitet.',
));

?>
