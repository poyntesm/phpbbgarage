		<td width="72%" valign="top">
			<form enctype="multipart/form-data" method="post" name="manage_gallery" action="{S_MODE_ACTION}">
			<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" nowrap="nowrap" colspan="3"></th>
				</tr>
			 	<tr>
					<td class="catBottom" width="32%" align="center"><span class="gen">{L_IMAGE}</span></td>
			      		<td class="catBottom" width="32%" align="center"><span class="gen">{L_OWNER}</span></td>
					<td class="catBottom" width="32%" align="center"><span class="gen">{L_VEHICLE}</span></td>
			   	</tr>
			  	<!-- BEGIN pic_row -->
			   	<tr>
			      		<td class="row1" align="center"><span class="gen">{pic_row.THUMB_IMAGE}</span></td>
			      		<td class="row1" align="center"><span class="gen"><a href="{pic_row.U_VIEW_PROFILE}">{pic_row.USERNAME}</a></span></td>
			      		<td class="row1" align="center"><span class="gen"><a href="{pic_row.U_VIEW_VEHICLE}">{pic_row.VEHICLE}</a></span></td>
			   	</tr>
			  	<!-- END pic_row -->
			</form>
			</table>
		</td>
