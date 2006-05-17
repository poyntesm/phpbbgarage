		<td width="72%" valign="top">
			<form action="{S_MODE_ACTION}" method="post" name="reassign_business">
			<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline" >
				<tr>
					<th colspan="2" class="thHead">{L_RESSIGN_BUSINESS}</th>
				</tr>
				<tr>
					<td class="row1" width="50%"><span class="gen"><b>{L_BUSINESS_DELETED}</b></span></td>
					<td class="row1"><span class="genmed">{NAME}</span></td>
				</tr>
				<tr>
					<td class="row1" width="50%"><span class="gen"><b>{L_REASSIGN_TO}</b></span></td>
					<td class="row1">{BUSINESS_SELECT}</td>
				</tr>
				<tr>
					<td class="catBottom" colspan="2" align="center"><input type="hidden" name="business_id" value="{BUSINESS_ID}" /><input type="submit" name="submit" value="{L_REASSIGN_BUTTON}" class="mainoption" /></td>
				</tr>
  			</table>
			</form>
		</td>
