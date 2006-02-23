		<td width="72%" valign="top">
			<!-- BEGIN switch_search --> 
			<table width="100%" border="0" cellspacing="1" cellpadding="4" class="forumline">
	  			<tr>
					<td valign="top" class="row1" align="center"><span class="gen"><b>{SEARCH_MESSAGE}</b></span><br /></td>
	  			</tr>
			</table>
			<br />
			<!-- END switch_search -->

			<form method="post" action="{S_MODE_ACTION}">
			<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
				<tr> 
	  				<th class="thCornerL" nowrap="nowrap">{L_VEHICLE}</th>
					<th class="thTop" nowrap="nowrap">{L_PRICE}</th>
					<th class="thTop" nowrap="nowrap">{L_MOD_PRICE}</th>
					<th class="thTop" nowrap="nowrap">{L_OWNER}</th>
					<th class="thTop" nowrap="nowrap">{L_PREMIUM}</th>
					<th class="thTop" nowrap="nowrap">{L_COVER_TYPE}</th>
					<th class="thCornerR" nowrap="nowrap">{L_BUSINESS}</th>
				</tr>
				<!-- BEGIN vehiclerow -->
				<tr> 
	  				<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall"><a href="{vehiclerow.U_VIEW_VEHICLE}">{vehiclerow.VEHICLE}</a></span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall">{vehiclerow.PRICE}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall">{vehiclerow.MOD_PRICE}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall"><a href="{vehiclerow.U_VIEW_PROFILE}">{vehiclerow.USERNAME}</a></span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall">{vehiclerow.PREMIUM}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall">{vehiclerow.COVER_TYPE}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center" valign="middle"><span class="gensmall"><a href="{vehiclerow.U_VIEW_BUSINESS}">{vehiclerow.BUSINESS}</a></span></td>
				</tr>
				<!-- END vehiclerow -->
				<tr>
					<td class="catBottom" align="center" height="28" colspan="9"><span class="genmed">{L_SORTED_BY}&nbsp;{S_SORT_SELECT}&nbsp;&nbsp;{L_IN}&nbsp;{S_ORDER_SELECT}&nbsp;&nbsp;<input type="hidden" value="{MODEL_ID}" name="model_id" /><input type="hidden" value="{MAKE_ID}" name="make_id" /><input type="submit" name="submit" value="{L_GO}" class="liteoption" /></td>
				</tr>
			</table>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
  				<tr> 
					<td><span class="nav">{PAGE_NUMBER}</span></td>
					<td align="right"><span class="gensmall">{S_TIMEZONE}</span><br /><span class="nav">{PAGINATION}</span></td>
  				</tr>
			</table>
			</form>
		</td>
