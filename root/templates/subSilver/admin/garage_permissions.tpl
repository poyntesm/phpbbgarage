<h1>{L_GARAGE_PERMISSIONS_TITLE}</h1>

<p>{L_GARAGE_PERMISSIONS_EXPLAIN}</p>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="5">{L_PERMISSION_ACCESS_LEVELS}</th>
	</tr>
	<tr>
		<td class="catBottom" width="40%"><span class="gen">{L_NAME}</span></td>
		<td class="catBottom" width="15%"><span class="gen">{L_BROWSE}</span></td>
		<td class="catBottom" width="15%"><span class="gen">{L_INTERACT}</span></td>
		<td class="catBottom" width="15%"><span class="gen">{L_ADD}</span></td>
		<td class="catBottom" width="15%"><span class="gen">{L_UPLOAD}</span></td>
	</tr>
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_GLOBAL_ALL_MASKS}</td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_ALL_MASKS}&nbsp;</div></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" onClick="check_all("BROW")" name="BROWSE_ALL" value="1" {BROWSE_ALL_CHECKED} /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" onClick="check_all("INTE")" name="INTERACT_ALL" value="1" {INTERACT_ALL_CHECKED} /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" onClick="check_all("ADD_")" name="ADD_ALL" value="1"{ADD_ALL_CHECKED} /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" onClick="check_all("UPLO")" name="UPLOAD_ALL" value="1" {UPLOAD_ALL_CHECKED} /></td>
	</tr>
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_GRANULAR_PERMISSIONS}</td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_ADMINISTRATORS}&nbsp;</div></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="BROWSE_ADMIN" value="1" {BROWSE_ADMIN_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="INTERACT_ADMIN" value="1" {INTERACT_ADMIN_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="ADD_ADMIN" value="1" {ADD_ADMIN_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="UPLOAD_ADMIN" value="1" {UPLOAD_ADMIN_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_MODERATORS}&nbsp;</div></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="BROWSE_MOD" value="1" {BROWSE_MOD_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="INTERACT_MOD" value="1" {INTERACT_MOD_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="ADD_MOD" value="1" {ADD_MOD_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="UPLOAD_MOD" value="1" {UPLOAD_MOD_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_REGISTERED_USERS}&nbsp;</div></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="BROWSE_USER" value="1" {BROWSE_USER_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="INTERACT_USER" value="1" {INTERACT_USER_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="ADD_USER" value="1" {ADD_USER_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="UPLOAD_USER" value="1" {UPLOAD_USER_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_GUEST_USERS}&nbsp;</div></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="BROWSE_GUEST" value="1" {BROWSE_GUEST_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="INTERACT_GUEST" value="1" {INTERACT_GUEST_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="ADD_GUEST" value="1" {ADD_GUEST_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="UPLOAD_GUEST" value="1" {UPLOAD_GUEST_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2"  width="40%"  valign="middle"><div align="right" style="font-weight:bold">{L_PRIVATE}&nbsp;</div></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="BROWSE_PRIVATE" value="1" {BROWSE_PRIVATE_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="INTERACT_PRIVATE" value="1" {INTERACT_PRIVATE_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="ADD_PRIVATE" value="1" {ADD_PRIVATE_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1"  width="15%"  valign="middle"><input type="checkbox" name="UPLOAD_PRIVATE" value="1" {UPLOAD_PRIVATE_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<!-- BEGIN private -->
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_PRIVATE_PERMISSIONS}</td>
	</tr>
	<!-- END private -->
	<!-- BEGIN usergroup -->
	<tr>
		<td class="row2" width="40%" valign="middle"><div align="right" style="font-weight:bold">{usergroup.GROUP_NAME}&nbsp;</div></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" name="browse[]" value="{usergroup.GROUP_ID}" {usergroup.BROWSE_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" name="interact[]" value="{usergroup.GROUP_ID}" {usergroup.INTERACT_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" name="add[]" value="{usergroup.GROUP_ID}" {usergroup.ADD_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="15%" valign="middle"><input type="checkbox" name="upload[]" value="{usergroup.GROUP_ID}" {usergroup.UPLOAD_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<!-- END usergroup -->
	<tr>
		<td class="catBottom" align="center" height="28" colspan="5"><input type="hidden" value="update_permissions" name="mode" /><input name="submit" type="submit" value="{L_SAVE}" class="liteoption" /></td>
	</tr>
</table>
</form>

