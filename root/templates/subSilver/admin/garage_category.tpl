<h1>{L_GARAGE_CAT_TITLE}</h1>

<p>{L_GARAGE_CAT_EXPLAIN}</p>


<script language="JavaScript" type="text/javascript">
<!--
function rename ( selected )
{
	formErrors = false;    

	if (document.manage_categories.elements['title_'+selected].value.length < 2)
	{
		formErrors = "{L_EMPTY_TITLE}";
	}

	if (formErrors) 
	{
		alert(formErrors);
	} 
	else 
	{
		document.manage_categories.id.value = selected;
		document.manage_categories.title.value = document.manage_categories.elements['title_'+selected].value;
		document.manage_categories.submit() ;
	}
}

function checkForm() 
{

	formErrors = false;    

	if (document.new_category.title.value.length < 2) 
	{
		formErrors = "{L_EMPTY_TITLE}";
	}

	if (formErrors) 
	{
		alert(formErrors);
		return false;
	} 
	else 
	{
		return true;
	}
}

-->
</script>

<form name="new_category" action="{S_GARAGE_MODE_NEW}" method="post" onsubmit="return checkForm(this)">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_PANEL_TITLE}</th>
	</tr>
	<tr>
		<td class="row1" width="30%"><span class="gen">{L_CAT_TITLE}:</span></td>
		<td class="row2"><input name="title" type="text" class="post" size="35" value="{S_CAT_TITLE}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input name="submit" type="submit" value="{L_PANEL_TITLE}" class="liteoption" /></td>
	</tr>
</table>
</form>

<form name="manage_categories" action="{S_GARAGE_MODE_RENAME}" method="post">
<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" colspan="4">{L_GARAGE_CAT_TITLE}</th>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" ><span class="gen"><b>{L_NAME}</b></span></td>
		<td class="catBottom" align="center" height="28" ><span class="gen"><b>{L_RENAME}</b></span></td>
		<td class="catBottom" align="center" height="28" ><span class="gen"><b>{L_DELETE}</b></span></td>
		<td class="catBottom" align="center" height="28" ><span class="gen"><b>{L_REORDER}</b></span></td>
	</tr>
	<!-- BEGIN catrow -->
	<tr>
		<td class="{catrow.COLOR}" width="50%" nowrap=nowrap><span class="gen">{catrow.TITLE}</span></td>
		<td class="{catrow.COLOR}" align="center" nowrap=nowrap><input name="title_{catrow.ID}" type="text" class="post" size="25" value="{}" /><span class="genmed">&nbsp;&nbsp;<a href="javascript:rename('{catrow.U_RENAME}')">{catrow.RENAME}</a></span></td>
		<td class="{catrow.COLOR}" align="center" nowrap="nowrap"><span class="genmed"><a href="{catrow.U_DELETE}">{catrow.DELETE}</a></span></td>
		<td class="{catrow.COLOR}" align="center" nowrap="nowrap"><span class="genmed"><a href="{catrow.U_MOVE_UP}">{catrow.MOVE_UP}</a>&nbsp;<a href="{catrow.U_MOVE_DOWN}">{catrow.MOVE_DOWN}</a></span></td>
	</tr>
	<!-- END catrow -->
	<tr>
		<td class="catBottom" height="18" align="center" valign="middle" colspan="5"><input type="hidden" value="" name="id" /><input type="hidden" value="" name="title" /></td>
	</tr>
</table>
</form>


