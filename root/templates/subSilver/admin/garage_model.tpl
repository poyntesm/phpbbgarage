<h1>{L_GARAGE_MODELS_TITLE}</h1>

<p>{L_GARAGE_MODELS_EXPLAIN}</p>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_MAKE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2"><input name="make" type="text" class="post" size="35" value="{S_MAKE}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="add_make" name="mode" /><input name="submit" type="submit" value="{L_ADD_MAKE_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_MODIFY_MAKE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2" align="left" nowrap="nowrap"><span class="gen">{L_VECHILE_MAKE}&nbsp;<select name="id" class="post">{MAKE_LIST}</select>&nbsp;</span></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_CHANGE_TO}</span></td>
		<td class="row2"><input name="make" type="text" class="post" size="35" value="{S_MAKE}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="modify_make" name="mode" /><input name="submit" type="submit" value="{L_MODIFY_MAKE_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_DELETE_MAKE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2" align="left" nowrap="nowrap"><span class="gen">{L_VECHILE_MAKE}&nbsp;<select name="id" class="post">{MAKE_LIST}</select>&nbsp;</span></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="delete_make" name="mode" /><input name="submit" type="submit" value="{L_DELETE_MAKE_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_MODEL}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2" align="left" nowrap="nowrap"><span class="gen">{L_VECHILE_MAKE}&nbsp;<select name="make_id" class="post">{MAKE_LIST}</select>&nbsp;</span></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MODEL}</span></td>
		<td class="row2"><input name="model" type="text" class="post" size="35" value="{S_MODEL}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="add_model" name="mode" /><input name="submit" type="submit" value="{L_ADD_MODEL_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_MODIFY_MODEL}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2" align="left" nowrap="nowrap"><span class="gen">{L_VECHILE_MAKE}&nbsp;<select name="id" class="post">{MAKE_LIST}</select>&nbsp;</span></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="modify_model_choice" name="mode" /><input name="submit" type="submit" value="{L_CHOOSE_MODIFY_MODEL_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_DELETE_MODEL}</th>
	</tr>
		<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2" align="left" nowrap="nowrap"><span class="gen">{L_VECHILE_MAKE}&nbsp;<select name="id" class="post">{MAKE_LIST}</select>&nbsp;</span></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="delete_model_choice" name="mode" /><input name="submit" type="submit" value="{L_CHOOSE_DELETE_MODEL_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>

<br />
