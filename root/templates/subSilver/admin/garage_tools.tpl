<h1>{L_GARAGE_TOOLS_TITLE}</h1>

<p>{L_GARAGE_TOOLS_EXPLAIN}</p>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_GARAGE_TOOLS_REBUILD}</th>
	</tr>
	<tr>
		<td class="row1" width="50%"><span class="gen">{L_GARAGE_TOOLS_REBUILD_ALL}</span></td>
	  	<td class="row2"><input class="post" type="text" maxlength="12" size="12" name="cycle" value="{CYCLE}" />&nbsp;{L_PER_CYCLE}</td>

	</tr>
	<tr>
		<td class="row1" width="50%"><span class="gen">{L_GARAGE_TOOLS_CREATE_LOG}</span></td>
		 <td class="row2">{L_BASE_DIRECTORY}&nbsp;<input class="post" type="text" maxlength="12" size="12" name="file" value="{FILE}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="rebuild_thumbs" name="mode" /><input name="submit" type="submit" value="{L_GARAGE_TOOLS_REBUILD}" class="liteoption" /></td>
	</tr>
</table>
</form>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="1">{L_GARAGE_TOOLS_ORPHANED_TITLE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_GARAGE_TOOLS_ORPHANED}</span></td>
	</tr>

	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="orphan_search" name="mode" /><input name="submit" type="submit" value="{L_GARAGE_TOOLS_ORPHANED_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="1">{L_GARAGE_TOOLS_RESET_RATINGS_TITLE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_GARAGE_TOOLS_RESET_RATINGS}</span></td>
	</tr>

	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="reset_ratings" name="mode" /><input name="submit" type="submit" value="{L_GARAGE_TOOLS_RESET_RATINGS_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>


<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_GARAGE_DB_BACKUP}</th>
	</tr>
	<tr>
		<td class="row2" width="25%">{L_FULL_BACKUP}</td>
		<td class="row2"><input type="radio" name="backup_type" value="full" checked /></td>
	</tr>
	<tr>
		<td class="row1" width="25%">{L_STRUCTURE_BACKUP}</td>
		<td class="row1"><input type="radio" name="backup_type" value="structure" /></td>
	</tr>
	<tr>
		<td class="row2" width="25%">{L_DATA_BACKUP}</td>
		<td class="row2"><input type="radio" name="backup_type" value="data" /></td>
	</tr>
	<tr>
		<td class="row1" width="25%">{L_GZIP_COMPRESS}</td>
		<td class="row1">{L_NO} <input type="radio" name="gzipcompress" value="0" checked /> &nbsp;{L_YES} <input type="radio" name="gzipcompress" value="1" /></td>
	</tr>
	<tr>
		<td class="catBottom" colspan="2" align="center"><input type="hidden" name="mode" value="backup" /><input type="hidden" name="drop" value="1" /><input type="submit" name="backupstart" value="{L_START_BACKUP}" class="liteoption" /></td>
	</tr>
</table>
</form>

<form enctype="multipart/form-data" method="post" action="{S_GARAGE_ACTION}">
<table width="100%" cellspacing="1" cellpadding="4" border="0" align="center" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_GARAGE_DB_RESTORE}</th>
	</tr>
	<tr>
		<td class="row1" width="25%">{L_SELECT_FILE}</td>
		<td class="row1">&nbsp;<input type="file" name="backup_file" /></td>
	</tr>
	<tr>
		<td class="catBottom" colspan="2" align="center"><input type="hidden" name="mode" value="restore" /><input type="submit" name="restore_start" value="{L_START_RESTORE}" class="liteoption" />&nbsp;</td>
	</tr>
</table>
</form>


