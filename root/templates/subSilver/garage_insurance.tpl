		<td width="72%" valign="top">
			<form enctype="multipart/form-data" method="post" name="insurance" action="{S_MODE_ACTION}">
			<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" nowrap="nowrap" colspan="2">{VEHICLE_DATA}</th>
				</tr>
				<tr>
					<td class="catBottom" align="center" height="28" colspan="2"><span class="gen">{L_TITLE}</span></td>
				</tr>
				<tr>
					<td class="row1" width="35%"><span class="gen"><b>{L_INSURANCE_COMPANY}</b></span></td>
	  				<td class="row2">{INSURANCE_LIST}&nbsp;<span class="gensmall" style="color=:#FF0000">[{L_REQUIRED}]</span><span class="gensmall"> {L_NOT_LISTED_YET}<a href="{U_SUBMIT_BUSINESS}">{L_HERE}</a></span></td>
				</tr>
				<tr>
					<td class="row1" width="30%"><span class="gen"><b>{L_PREMIUM_PRICE}</b></span></td>
					<td class="row2"><input name="premium" type="text" class="post" size="15" value="{PREMIUM}" /> <span class="gensmall" style="color:#FF0000">[{L_REQUIRED}]</span></td>

				</tr>
				<tr>
					<td class="row1" width="35%"><span class="gen"><b>{L_COVER_TYPE}</b></span></td>
				  	<td class="row2">{COVER_TYPE_LIST} <span class="gensmall" style="color:#FF0000">[{L_REQUIRED}]</span></td>
				</tr>
				<tr>
					<td class="row1" width="30%"><span class="gen"><b>{L_COMMENTS}</b></span></td>
					<td class="row2"><textarea name="comments" cols="60" rows="5" wrap="soft"   class="multitext">{COMMENTS}</textarea></td>
				</tr>
				<tr>
					<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="{CID}" name="CID" /><input type="hidden" value="{INS_ID}" name="INS_ID" /><input name="submit" type="submit" value="{L_BUTTON}" class="liteoption" /></td>
				</tr>
			</form>
			</table>
		</td>
