<h1>{L_GARAGE_MODELS_TITLE}</h1>

<p>{L_GARAGE_MODELS_EXPLAIN}</p>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_EDIT_BUSINESS}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_NAME}</b></span></td>
		<td class="row2"><input name="name" type="text" class="post" size="35" value="{NAME}" /></td>
	</tr>
	<tr>
		<td class="row1" width="35%"><span class="gen"><b>{L_BUSINESS_ADDRESS}</b></span></td>
		<td class="row2"><textarea name="address" cols="60" rows="5" wrap="soft" class="multitext">{ADDRESS}</textarea></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_TELEPHONE_NO}</b></span></td>
		<td class="row2"><input name="telephone" type="text" class="post" size="35" value="{TELEPHONE}" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_FAX_NO}</b></span></td>
		<td class="row2"><input name="fax" type="text" class="post" size="35" value="{FAX}" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_WEBSITE}</b></span></td>
		<td class="row2"><input name="website" type="text" class="post" size="35" value="{WEBSITE}" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_EMAIL}</b></span></td>
		<td class="row2"><input name="email" type="text" class="post" size="35" value="{EMAIL}" /></td>
	</tr>
	<tr>
		<td class="row1" width="35%"><span class="gen"><b>{L_BUSINESS_OPENING_HOURS}</b></span></td>
		<td class="row2"><textarea name="opening_hours" cols="60" rows="5" wrap="soft"   class="multitext">{OPENING_HOURS}</textarea></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_TYPE}</b></span></td>
		<td class="row2">{L_INSURANCE} : <input type="checkbox" name="insurance" {INSURANCE_CHECKED} /><br />{L_GARAGE} : <input type="checkbox" name="garage" {GARAGE_CHECKED} /><br />{L_RETAIL_SHOP} : <input type="checkbox" name="retail_shop" {RETAIL_CHECKED} /><br />{L_WEB_SHOP} : <input type="checkbox" name="web_shop" {WEB_CHECKED} /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="update_business" name="mode" /><input type="hidden" value="{ID}" name="BUS_ID" /><input name="submit" type="submit" value="{L_EDIT_BUSINESS}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

