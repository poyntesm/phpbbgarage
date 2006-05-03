<h1>{L_DELETE}</h1>

<p>{L_DELETE_EXPLAIN}</p>

<form name="confirm_delete" action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline" >
	<tr>
		<th colspan="2" class="thHead">{L_REQUIRED}</th>
	</tr>
	<tr>
		<td class="row1" width="50%"><span class="gen"><b>{L_REMOVE}</b></span></td>
		<td class="row1"><span class="genmed">{S_TITLE}</span></td>
	</tr>
	<tr>
		<td class="row1" width="50%"><span class="gen"><b>{L_MOVE_DELETE}</b><br />{L_OR}<br /><b>{L_DELETE_PERMENANTLY}</b></span></td>
		<td class="row1"><select name="target"><option value="">---------</option>{MOVE_TO_LIST}</select><span class="gen"><br /><br /><input type="checkbox" name="permenant" value="1" /></span></td>
	</tr>
	<tr>
		<td class="catBottom" colspan="2" align="center"><input type="submit" name="submit" value="{L_MOVE_DELETE_BUTTON}" class="mainoption" /></td>
	</tr>
  </table>
</form>

