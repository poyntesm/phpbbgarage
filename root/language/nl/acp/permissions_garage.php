<?php
/** 
*
* acp_garage_permissions (phpBB Garage Permission Set) [Dutch]
*
* @package language 
* @translated by Roblom from www.deCRXgarage.nl
* @version $Id: permissions_garage.php 419 2007-05-22 14:41:58Z poyntesm $
* @copyright (c) 2005 phpBB Garage
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* NIET VERANDEREN !
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* Voegt de nieuwe permissie categorie 'garage' toe
*/
$lang['permission_cat']['garage'] = 'Garage';

/**
* Voegt de nieuwe permissie types 'ug_', 'mg_' & 'ag' toe
*/
//$lang['permission_type']['ug_'] = 'Garage gebruiker permissies';
//$lang['permission_type']['mg_'] = 'Garage moderator permissies';
//$lang['permission_type']['ag_'] = 'Garage admin permissies';

/**
* Voegt de nieuwe gebruiker permissies toe voor phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_u_garage_browse'				=> array('lang' => 'Kan de garage verkennen', 'cat' => 'garage'),
	'acl_u_garage_search'				=> array('lang' => 'Kan zoeken in de garage',	'cat' => 'garage'),
	'acl_u_garage_add_vehicle'			=> array('lang' => 'Kan voertuigen toevoegen', 'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'		=> array('lang' => 'Kan eigen voertuig verwijderen', 'cat' => 'garage'),
	'acl_u_garage_add_make_model'		=> array('lang' => 'Kan nieuwe merken en modellen toevoegen','cat' => 'garage'),
	'acl_u_garage_add_modification'		=> array('lang' => 'Kan modificaties toevoegen aan eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Kan modificaties verwijderen van eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_add_product'			=> array('lang' => 'Kan modificatie produkten toevoegen aan eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Kan quartermile tijden toevoegen aan eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Kan quartermile tijden verwijderen van eigen voertuig',	'cat' => 'garage'),
	'acl_u_garage_add_dynorun'			=> array('lang' => 'Kan dynoruns toevoegen aan eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_delete_dynorun'		=> array('lang' => 'Kan dynoruns verwijderen van eigen voertuig', 'cat' => 'garage'),
	'acl_u_garage_add_lap'				=> array('lang' => 'Kan ronden toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_lap'			=> array('lang' => 'Kan eigen ronden verwijderen uit de garage ', 'cat' => 'garage'),
	'acl_u_garage_add_track'			=> array('lang' => 'Kan nieuwe circuits toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_track'			=> array('lang' => 'Kan circuits verwijderen uit de garage', 'cat' => 'garage'),
	'acl_u_garage_add_insurance'		=> array('lang' => 'Kan verzekeringen toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_insurance'		=> array('lang' => 'Kan verzekeringen verwijderen uit de garage', 'cat' => 'garage'),
	'acl_u_garage_add_service'			=> array('lang' => 'Kan diensten toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_service'		=> array('lang' => 'Kan diensten verwijderen uit de garage', 'cat' => 'garage'),
	'acl_u_garage_add_blog'				=> array('lang' => 'Kan een blog toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_blog'			=> array('lang' => 'Kan een blog verwijderen uit de garage', 'cat' => 'garage'),
	'acl_u_garage_add_business'			=> array('lang' => 'Kan bedrijven toevoegen aan de garage', 'cat' => 'garage'),
	'acl_u_garage_rate'					=> array('lang' => 'Kan op items stemmen in de garage', 'cat' => 'garage'),
	'acl_u_garage_comment'				=> array('lang' => 'Kan commenaren schrijven in de garage', 'cat' => 'garage'),
	'acl_u_garage_upload_image'			=> array('lang' => 'Kan afbeeldingen uploaden in de garage', 'cat' => 'garage'),
	'acl_u_garage_remote_image'			=> array('lang' => 'Kan gelinkte afbeeldingen gebruiken in de garage', 'cat' => 'garage'),
	'acl_u_garage_delete_image'			=> array('lang' => 'Kan afbeeldingen verwijderen van eigen items in de garage', 'cat' => 'garage'),
	'acl_u_garage_deny'					=> array('lang' => 'Blokkeer toegang tot de garage', 'cat' => 'garage'),

));


/**
* Voegt de nieuwe moderator permissies toe voor phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_m_garage_edit'					=> array('lang' => 'Kan voertuigen en gerelateerde items bewerken', 'cat' => 'garage'),
	'acl_m_garage_delete'				=> array('lang' => 'Kan voertuigen en gerelateerde items verwijderen', 'cat' => 'garage'),
	'acl_m_garage_rating'				=> array('lang' => 'Kan waarderingen verwijderen/resetten', 'cat' => 'garage'),
	'acl_m_garage_approve_vehicle'		=> array('lang' => 'Kan voertuigen goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_make'			=> array('lang' => 'Kan merken goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_model'		=> array('lang' => 'Kan modellen goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_business'		=> array('lang' => 'Kan bedrijven goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_quartermile'	=> array('lang' => 'Kan quartermiles goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_dynorun'		=> array('lang' => 'Kan dynoruns goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_guestbook'	=> array('lang' => 'Kan gastenboek commentaren goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_lap'			=> array('lang' => 'Kan ronden goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_track'		=> array('lang' => 'Kan circuits goedkeuren', 'cat' => 'garage'),
	'acl_m_garage_approve_product'		=> array('lang' => 'Kan produkten goedkeuren', 'cat' => 'garage'),
));

/**
* Voegt de nieuwe administrator permissies toe voor phpBB Garage
*/
$lang = array_merge($lang, array(
 	'acl_a_garage_setting'	=> array('lang' => 'Kan garageinstellingen veranderen', 'cat' => 'garage'),
 	'acl_a_garage_business'	=> array('lang' => 'Kan garage bedrijven beheren', 'cat' => 'garage'),
 	'acl_a_garage_category'	=> array('lang' => 'Kan garage categorie&eumln beheren', 'cat' => 'garage'),
 	'acl_a_garage_field'	=> array('lang' => 'Kan garage custom velden beheren', 'cat' => 'garage'),
 	'acl_a_garage_model'	=> array('lang' => 'Kan garage modellen beheren', 'cat' => 'garage'),
 	'acl_a_garage_product'	=> array('lang' => 'Kan garage produkten beheren', 'cat' => 'garage'),
 	'acl_a_garage_quota'	=> array('lang' => 'Kan garage aantallen beheren', 'cat' => 'garage'),
 	'acl_a_garage_tool'		=> array('lang' => 'Kan garage hulpmiddelen beheren', 'cat' => 'garage'),
 	'acl_a_garage_track'	=> array('lang' => 'Kan garage circuits beheren', 'cat' => 'garage'),
));

?>
