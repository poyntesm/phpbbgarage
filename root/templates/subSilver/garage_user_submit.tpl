		<td width="72%" valign="top" >
			<form enctype="multipart/form-data" method="post" name="user_submit" action="{S_MODE_ACTION}">
			<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_NEW_BUSINESS}</th>
				</tr>
				<tr>
					<td class="row1" colspan="2" width="20%" align="center"><span class="gen"><br />{L_BUSINESS_NOTICE}<br /><br /></span></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_NAME}</b></span></td>
					<td class="row2"><input name="name" type="text" class="post" size="35" value="{NAME}" /> <span class="gensmall" style="color:#FF0000">[{L_REQUIRED}]</span></td>
				</tr>
				<tr>
					<td class="row1" width="35%"><span class="gen"><b>{L_ADDRESS}</b></span></td>
					<td class="row2"><textarea name="address" cols="60" rows="5" wrap="soft"   class="post">{ADDRESS}</textarea></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_TELEPHONE}</b></span></td>
					<td class="row2"><input name="telephone" type="text" class="post" size="35" value="{TELEPHONE}" /></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_FAX}</b></span></td>
					<td class="row2"><input name="fax" type="text" class="post" size="35" value="{FAX}" /></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_WEBSITE}</b></span></td>
					<td class="row2"><input name="website" type="text" class="post" size="35" value="{WEBSITE}" /></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_EMAIL}</b></span></td>
					<td class="row2"><input name="email" type="text" class="post" size="35" value="{EMAIL}" /></td>
				</tr>
				<tr>
					<td class="row1" width="35%"><span class="gen"><b>{L_OPENING_HOURS}</b></span></td>
					<td class="row2"><textarea name="opening_hours" cols="60" rows="5" wrap="soft" class="post">{OPENING_HOURS}</textarea></td>
				</tr>
				<tr>
					<td class="row1" width="20%"><span class="gen"><b>{L_TYPE}</b></span></td>
					<td class="row2">{L_INSURANCE} : <input type="checkbox" name="insurance" {INSURANCE_CHECKED} /><br />{L_GARAGE} : <input type="checkbox" name="garage" {GARAGE_CHECKED} /><br />{L_RETAIL_SHOP} : <input type="checkbox" name="retail_shop" {RETAIL_CHECKED} /><br />{L_WEB_SHOP} : <input type="checkbox" name="web_shop" {WEBSHOP_CHECKED} /></td>
				</tr>
				<tr>
					<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="{CID}" name="CID" /><input type="hidden" value="{BUSINESS_ID}" name="id" /><input type="hidden" value="{MODE_REDIRECT}" name="mode_redirect" /><input name="submit" type="submit" value="{L_ADD_NEW_BUSINESS}" class="liteoption" /></td>
				</tr>
			</table>
			</form>
		</td>
