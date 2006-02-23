<h1>{L_DELETE}</h1>

<p>{L_DELETE_EXPLAIN}</p>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline" >
	<tr>
		<th colspan="2" class="thHead">{L_REQUIRED}</th>
	</tr>
	<tr>
		<td class="row1" width="50%"><span class="gen">{L_REMOVE}</span></td>
		<td class="row1"><span class="genmed">{S_TITLE}</span></td>
	</tr>
	<tr>
		<td class="row1" width="50%"><span class="genmed">{L_MOVE_DELETE}</span></td>
		<td class="row1">{MOVE_TO_LIST}  <span class="gensmall"><font color="#FF0000">[{L_REQUIRED}]</font></span></td>
	</tr>
	<tr>
		<td class="catBottom" colspan="2" align="center"><input type="hidden" name="mode" value="delete" /><input type="submit" name="submit" value="{L_MOVE_DELETE_BUTTON}" class="mainoption" /></td>
	</tr>
  </table>
</form>

