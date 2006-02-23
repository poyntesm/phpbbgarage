  		<td width="72%" valign="top">

			<!-- BEGIN switch_search --> 
			<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
	  			<tr>
					<td valign="top" class="row1" align="center"><span class="gen"><b>{SEARCH_MESSAGE}</b></span><br /></td>
	  			</tr>
			</table>
			<br />
			<!-- END switch_search -->

			<form method="post" action="{S_MODE_ACTION}">
			<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
				<tr> 
	  				<th class="thCornerL" nowrap="nowrap"></th>
					<th class="thTop" nowrap="nowrap">{L_YEAR}</th>
					<th class="thTop" nowrap="nowrap">{L_MAKE}</th>
					<th class="thTop" nowrap="nowrap">{L_MODEL}</th>
					<th class="thTop" >{L_COLOUR}</th>
					<th class="thTop" nowrap="nowrap">{L_OWNER}</th>
					<th class="thTop" nowrap="nowrap">{L_VIEWS}</th>
					<th class="thTop" nowrap="nowrap">{L_MODS}</th>
					<th class="thCornerR" nowrap="nowrap">{L_UPDATED}</th>
				</tr>
				<!-- BEGIN vehiclerow -->
				<tr> 
	  				<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gen">{vehiclerow.IMAGE_ATTACHED}</span></td>
	  				<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.YEAR}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.MAKE}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall"><a href="{vehiclerow.U_VIEW_VEHICLE}">{vehiclerow.MODEL}</a></span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.COLOUR}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gen"><a href="{vehiclerow.U_VIEW_VEHICLE}">{vehiclerow.OWNER}</a></span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.VIEWS}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.MODS}</span></td>
					<td class="{vehiclerow.ROW_CLASS}" align="center"><span class="gensmall">{vehiclerow.UPDATED}</span></td>
				</tr>
				<!-- END vehiclerow -->
				<tr>
					<td class="catBottom" align="center" height="28" colspan="9"><span class="genmed">{L_SORTED_BY}&nbsp;{S_SORT_SELECT}&nbsp;&nbsp;{L_IN}&nbsp;{S_ORDER_SELECT}&nbsp;&nbsp;<input type="hidden" name="make_id" value="{MAKE_ID}" /><input type="hidden" name="model_id" value="{MODEL_ID}" class="liteoption" /><input type="hidden" name="search" value="{SEARCH}" class="liteoption" /><input type="submit" name="submit" value="{L_GO}" class="liteoption" /></td>
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
