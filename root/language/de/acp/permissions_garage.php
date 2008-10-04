<?php
/** 
*
* acp_garage_permissions (phpBB Garage Permission Set) [German]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* Adds the new permission category 'garage'
*/
$lang['permission_cat']['garage'] = 'Garage';

/**
* Adds thew new permission types 'ug_', 'mg_' & 'ag'
*/
//$lang['permission_type']['ug_'] = 'Garage user permissions';
//$lang['permission_type']['mg_'] = 'Garage moderator permissions';
//$lang['permission_type']['ag_'] = 'Garage admin permissions';

/**
* Adds the new user permission for phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_u_garage_browse'			=> array('lang' => 'Kann die Garage durchstöbern', 'cat' => 'garage'),
	'acl_u_garage_search'			=> array('lang' => 'Kann die Garage durchsuchen',	'cat' => 'garage'),
	'acl_u_garage_add_vehicle'		=> array('lang' => 'Kann Fahrzeuge hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'		=> array('lang' => 'Kann eigene Fahrzeuge löschen', 'cat' => 'garage'),
	'acl_u_garage_add_make_model'		=> array('lang' => 'Kann neue Hersteller und Fabrikate hinzufügen','cat' => 'garage'),
	'acl_u_garage_add_modification'		=> array('lang' => 'Kann Modifikationen zu eigenen Fahrzeugen hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Kann eigene Modifikationen löschen', 'cat' => 'garage'),
	'acl_u_garage_add_product'		=> array('lang' => 'Kann Tuningteile hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Kann &frac14; Meilenzeiten zu eigenen Fahrzeugen hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Kann &frac14; Meilenzeiten von eigenen Fahrzeugen löschen',	'cat' => 'garage'),
	'acl_u_garage_add_dynorun'		=> array('lang' => 'Kann Leistungstests zu eigenen Fahrzeugen hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_dynorun'		=> array('lang' => 'Kann Leistungstests von eigenen Fahrzeugen löschen', 'cat' => 'garage'),
	'acl_u_garage_add_lap'			=> array('lang' => 'Kann Rundenzeiten hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_lap'		=> array('lang' => 'Kann eigene Rundenzeiten löschen', 'cat' => 'garage'),
	'acl_u_garage_add_track'		=> array('lang' => 'Kann neue Strecken hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_track'		=> array('lang' => 'Kann Strecken löschen', 'cat' => 'garage'),
	'acl_u_garage_add_insurance'		=> array('lang' => 'Kann Versicherungen hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_insurance'		=> array('lang' => 'Kann Versicherungen löschen', 'cat' => 'garage'),
	'acl_u_garage_add_service'		=> array('lang' => 'Kann Kundendienst hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_service'		=> array('lang' => 'Kann Kundendienst löschen', 'cat' => 'garage'),
	'acl_u_garage_add_blog'			=> array('lang' => 'Kann Blog hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_delete_blog'		=> array('lang' => 'Kann Blog löschen', 'cat' => 'garage'),
	'acl_u_garage_add_business'		=> array('lang' => 'Kann Unternehmen hinzufügen', 'cat' => 'garage'),
	'acl_u_garage_rate'			=> array('lang' => 'Kann Einträge bewerten', 'cat' => 'garage'),
	'acl_u_garage_comment'			=> array('lang' => 'Kann kommentieren', 'cat' => 'garage'),
	'acl_u_garage_upload_image'		=> array('lang' => 'Kann Bilder zur Garage hochladen', 'cat' => 'garage'),
	'acl_u_garage_remote_image'		=> array('lang' => 'Kann externe Bilder verwenden', 'cat' => 'garage'),
	'acl_u_garage_delete_image'		=> array('lang' => 'Kann eigene Bilder aus den Einträgen löschen', 'cat' => 'garage'),
	'acl_u_garage_deny'			=> array('lang' => 'Verweigere Benutzerzugriff auf die Garage', 'cat' => 'garage'),
));


/**
* Adds the new moderator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_m_garage_edit'			=> array('lang' => 'Kann Fahrzeuge und zugehörige Einträge ändern', 'cat' => 'garage'),
	'acl_m_garage_delete'			=> array('lang' => 'Kann Fahrzeuge und zugehörige Einträge löschen', 'cat' => 'garage'),
	'acl_m_garage_rating'			=> array('lang' => 'Kann Bewertungen löschen/zurücksetzen', 'cat' => 'garage'),
	'acl_m_garage_approve_vehicle'		=> array('lang' => 'Kann Fahrzeuge genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_make'		=> array('lang' => 'Kann Hersteller genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_model'		=> array('lang' => 'Kann Fabrikate genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_business'		=> array('lang' => 'Kann Unternehmen genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_quartermile'	=> array('lang' => 'Kann &frac14; Meilen genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_dynorun'		=> array('lang' => 'Kann Leistungstests genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_guestbook'	=> array('lang' => 'Kann Gästebuch-Kommentare genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_lap'		=> array('lang' => 'Kann Rundenzeiten genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_track'		=> array('lang' => 'Kann Strecken genehmigen', 'cat' => 'garage'),
	'acl_m_garage_approve_product'		=> array('lang' => 'Kann Tuningteile genehmigen', 'cat' => 'garage'),
));

/**
* Adds the new administrator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
 	'acl_a_garage_update'	=> array('lang' => 'Kann Garage-Version prüfen', 'cat' => 'garage'),
 	'acl_a_garage_setting'	=> array('lang' => 'Kann Garage-Einstellungen ändern', 'cat' => 'garage'),
 	'acl_a_garage_business'	=> array('lang' => 'Kann Unternehmen verwalten', 'cat' => 'garage'),
 	'acl_a_garage_category'	=> array('lang' => 'Kann Garage-Kategorien verwalten', 'cat' => 'garage'),
 	'acl_a_garage_field'	=> array('lang' => 'Kann benutzerdefinierte Felder verwalten', 'cat' => 'garage'),
 	'acl_a_garage_model'	=> array('lang' => 'Kann Fabrikate verwalten', 'cat' => 'garage'),
 	'acl_a_garage_product'	=> array('lang' => 'Kann Tuningteile verwalten', 'cat' => 'garage'),
 	'acl_a_garage_quota'	=> array('lang' => 'Kann Garage-Kontingente verwalten', 'cat' => 'garage'),
 	'acl_a_garage_tool'	=> array('lang' => 'Kann Garage-Werkzeuge verwalten', 'cat' => 'garage'),
 	'acl_a_garage_track'	=> array('lang' => 'Kann Strecken verwalten', 'cat' => 'garage'),
));

?>
