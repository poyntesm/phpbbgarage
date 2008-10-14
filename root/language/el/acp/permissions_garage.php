<?php
/** 
*
* acp_garage_permissions (phpBB Garage Permission Set) [Greek]
*
* @package language
* @version $Id: garage.php 587 2008-10-10 chrizathens $
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
$lang['permission_cat']['garage'] = 'Γκαράζ';

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
	'acl_u_garage_browse'				=> array('lang' => 'Μπορεί να περιηγηθεί στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_search'				=> array('lang' => 'Μπορεί να κάνει αναζήτηση στο Γκαράζ',	'cat' => 'garage'),
	'acl_u_garage_add_vehicle'			=> array('lang' => 'Μπορεί να προσθέσει οχήματα', 'cat' => 'garage'),
	'acl_u_garage_delete_vehicle'		=> array('lang' => 'Μπορεί να διαγράψει τα οχήματά του', 'cat' => 'garage'),
	'acl_u_garage_add_make_model'		=> array('lang' => 'Μπορεί να προσθέσει νέες μάρκες και μοντέλα','cat' => 'garage'),
	'acl_u_garage_add_modification'		=> array('lang' => 'Μπορεί να προσθέσει μετατροπές στα οχήματά του', 'cat' => 'garage'),
	'acl_u_garage_delete_modification'	=> array('lang' => 'Μπορεί να διαγράψει τις μετατροπές του', 'cat' => 'garage'),
	'acl_u_garage_add_product'			=> array('lang' => 'Μπορεί να προσθέσει προϊόντα στις μετατροπές', 'cat' => 'garage'),
	'acl_u_garage_add_quartermile'		=> array('lang' => 'Μπορεί να προσθέσει χρόνους 0-400μ. στα οχήματά του', 'cat' => 'garage'),
	'acl_u_garage_delete_quartermile'	=> array('lang' => 'Μπορεί να διαγράψει χρόνους 0-400μ. από τα οχήματά του',	'cat' => 'garage'),
	'acl_u_garage_add_dynorun'			=> array('lang' => 'Μπορεί να προσθέσει δυναμομετρήσεις στα οχήματά του', 'cat' => 'garage'),
	'acl_u_garage_delete_dynorun'		=> array('lang' => 'Μπορεί να διαγράψει τις δυναμομετρήσεις του', 'cat' => 'garage'),
	'acl_u_garage_add_lap'				=> array('lang' => 'Μπορεί να προσθέσει γύρους πίστας στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_delete_lap'			=> array('lang' => 'Μπορεί να διαγράψει τους δικούς του γύρους πίστας', 'cat' => 'garage'),
	'acl_u_garage_add_track'			=> array('lang' => 'Μπορεί να προσθέσει νέες πίστες', 'cat' => 'garage'),
	'acl_u_garage_delete_track'			=> array('lang' => 'Μπορεί να διαγράψει πίστες από το Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_add_insurance'		=> array('lang' => 'Μπορεί να προσθέσει ασφάλεια στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_delete_insurance'		=> array('lang' => 'Μπορεί να διαγράψει ασφάλειες από το Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_add_service'			=> array('lang' => 'Μπορεί να προσθέσει σέρβις στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_delete_service'		=> array('lang' => 'Μπορεί να διαγράψει σέρβις από το Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_add_blog'				=> array('lang' => 'Μπορεί να προσθέσει blog στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_delete_blog'			=> array('lang' => 'Μπορεί να διαγράψει blog από το Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_add_business'			=> array('lang' => 'Μπορεί να προσθέσει επιχείριση στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_rate'					=> array('lang' => 'Μπορεί να αξιολογήσει αντικείμενα στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_comment'				=> array('lang' => 'Μπορεί να αφήσει σχόλια στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_upload_image'			=> array('lang' => 'Μπορεί να ανεβάσει εικόνες στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_remote_image'			=> array('lang' => 'Μπορεί να χρησιμοποιήσει απομακρυσμένες εικόνες στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_delete_image'			=> array('lang' => 'Μπορεί να διαγράψει τις εικόνες του από αντικείμενα στο Γκαράζ', 'cat' => 'garage'),
	'acl_u_garage_deny'					=> array('lang' => 'Άρνηση πρόσβασης του μέλους στο Γκαράζ', 'cat' => 'garage'),
));


/**
* Adds the new moderator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
	'acl_m_garage_edit'					=> array('lang' => 'Μπορεί να επεξεργαστεί οχήματα και αντικείμενα σχετικά με αυτά', 'cat' => 'garage'),
	'acl_m_garage_delete'				=> array('lang' => 'Μπορεί να διαγράψει οχήματα και αντικείμενα σχετικά με αυτά', 'cat' => 'garage'),
	'acl_m_garage_rating'				=> array('lang' => 'Μπορεί να διαγράψει/μηδενίσει αξιολογήσεις', 'cat' => 'garage'),
	'acl_m_garage_approve_vehicle'		=> array('lang' => 'Μπορεί να εγκρίνει οχήματα', 'cat' => 'garage'),
	'acl_m_garage_approve_make'			=> array('lang' => 'Μπορεί να εγκρίνει μάρκες', 'cat' => 'garage'),
	'acl_m_garage_approve_model'		=> array('lang' => 'Μπορεί να εγκρίνει μοντέλα', 'cat' => 'garage'),
	'acl_m_garage_approve_business'		=> array('lang' => 'Μπορεί να εγκρίνει επιχειρήσεις', 'cat' => 'garage'),
	'acl_m_garage_approve_quartermile'	=> array('lang' => 'Μπορεί να εγκρίνει χρόνους 0-400μ.', 'cat' => 'garage'),
	'acl_m_garage_approve_dynorun'		=> array('lang' => 'Μπορεί να εγκρίνει δυναμομετρήσεις', 'cat' => 'garage'),
	'acl_m_garage_approve_guestbook'	=> array('lang' => 'Μπορεί να εγκρίνει σχόλια στο guestbook', 'cat' => 'garage'),
	'acl_m_garage_approve_lap'			=> array('lang' => 'Μπορεί να εγκρίνει γύρους πίστας', 'cat' => 'garage'),
	'acl_m_garage_approve_track'		=> array('lang' => 'Μπορεί να εγκρίνει πίστες', 'cat' => 'garage'),
	'acl_m_garage_approve_product'		=> array('lang' => 'Μπορεί να εγκρίνει προϊόντα', 'cat' => 'garage'),
));

/**
* Adds the new administrator permission for phpBB Garage
*/
$lang = array_merge($lang, array(
 	'acl_a_garage_update'	=> array('lang' => 'Μπορεί να ελέγξει την έκδοση του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_setting'	=> array('lang' => 'Μπορεί να αλλάξει τις ρυθμίσεις του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_business'	=> array('lang' => 'Μπορεί να διαχειριστεί τις επιχειρήσεις του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_category'	=> array('lang' => 'Μπορεί να διαχειριστεί τις κατηγορίες του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_field'	=> array('lang' => 'Μπορεί να διαχειριστεί τα προσαρμοσμένα πεδία του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_model'	=> array('lang' => 'Μπορεί να διαχειριστεί τα μοντέλα του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_product'	=> array('lang' => 'Μπορεί να διαχειριστεί τα προϊόντα του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_quota'	=> array('lang' => 'Μπορεί να διαχειριστεί τα όρια του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_tool'		=> array('lang' => 'Μπορεί να διαχειριστεί τα εργαλεία του Γκαράζ', 'cat' => 'garage'),
 	'acl_a_garage_track'	=> array('lang' => 'Μπορεί να διαχειριστεί τις πίστες του Γκαράζ', 'cat' => 'garage'),
));

?>
