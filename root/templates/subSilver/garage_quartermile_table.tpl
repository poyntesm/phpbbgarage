{JAVASCRIPT}

<form enctype="multipart/form-data" method="post" name="filter_quartermile_table" action="{S_MODE_ACTION}">
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tr>
   		<td><span class="nav"><a href="{U_INDEX}" class="nav">{L_INDEX}</a> -> <a href="{U_GARAGE}" class="nav">{L_GARAGE}</a> -> <a href="{U_QUARTERMILE}" class="nav">{L_QUARTERMILE}</a></span></td>
	</tr>
</table>

<table width="100%" cellspacing="2" cellpadding="2" border="0" align="center"> 
	<tr>
		<td align="left" nowrap="nowrap"><span class="genmed">{L_MAKE}:&nbsp;<select name="make_id" onchange="updateModelSelect(this.form.model_id, this.options[this.selectedIndex].text, '');" class="forminput"><option value="">{L_SELECT_MODEL}</option></select>&nbsp;{L_MODEL}:&nbsp;<select name="model_id" class="forminput"><option value="">{L_ANY_MODEL}</option></select>&nbsp;{L_SELECT_SORT_METHOD}:&nbsp;{S_MODE_SELECT}&nbsp;&nbsp;{L_ORDER}&nbsp;{S_ORDER_SELECT}&nbsp;&nbsp;<input type="submit" name="submit" value="{L_SUBMIT}" class="liteoption" /></span></td>
	</tr>
</table>

<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th height="25" class="thCornerL" nowrap="nowrap">#</th>
	  	<th class="thTop" nowrap="nowrap"></th>
	  	<th class="thTop" nowrap="nowrap">{L_USERNAME}</th>
		<th class="thTop" nowrap="nowrap">{L_VEHICLE}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_RT}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_SIXTY}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_THREE}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_EIGTH}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_EIGTHM}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_THOU}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_QUART}</th>
		<th class="thTop" nowrap="nowrap">{L_CAR_QUARTM}</th>
		<th class="thTop" nowrap="nowrap">{L_BHP}</th>
		<th class="thTop" nowrap="nowrap">{L_BHP_UNIT}</th>
		<th class="thCornerR" nowrap="nowrap">{L_NITROUS}</th>
	</tr>
	<!-- BEGIN memberrow -->
	<tr> 
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gen">{memberrow.ROW_NUMBER}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gen">{memberrow.IMAGE_LINK}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gen"><a href="{memberrow.U_VIEWPROFILE}" class="gen">{memberrow.USERNAME}</a></span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gen"><a href="{memberrow.U_VIEWVEHICLE}" class="gen">{memberrow.VEHICLE}</a></span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.RT}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.SIXTY}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.THREE}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.EIGTH}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.EIGHTM}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.THOU}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.QUART}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.QUARTM}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.BHP}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.BHP_UNIT}</span></td>
		<td class="{memberrow.ROW_CLASS}" align="center"><span class="gensmall">{memberrow.NITROUS}</span></td>
	</tr>
	<!-- END memberrow -->
	<tr> 
		<td class="catBottom" colspan="16" height="28">&nbsp;</td>
	</tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr> 
		<td><span class="nav">{PAGE_NUMBER}</span></td>
		<td align="right"><span class="gensmall">{S_TIMEZONE}</span><br /><span class="nav">{PAGINATION}</span></td>
	</tr>
</table>
</form>

<script language="JavaScript1.1" type="text/javascript">
<!--
// Update the make dropdown.
updateMakeSelect(document.filter_quartermile_table.make_id, '{MAKE}');
updateModelSelect(document.filter_quartermile_table.model_id, '{MAKE}', '{MODEL}');
//-->
</script>

<table width="100%" cellspacing="2" border="0" align="center">
	<tr> 
		<td valign="top" align="right">{JUMPBOX}</td>
