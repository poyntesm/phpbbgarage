		<td width="23%" valign="top" >
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" colspan="1">{L_MENU}</th>
				</tr>
				<tr>
					<td class="row1" align="center"><span class="genmed">{MENU}</span></td>
				</tr>
			</table>
			<br />

			<!-- BEGIN show_vehicles -->
			<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" colspan="4">{L_MY_VEHICLES}</th>
				</tr>
				<tr>
					<td class="row1" align="center"><span class="genmed">{L_CREATE_VEHICLE}</span></td>
				</tr>
				<!-- BEGIN user_vehicles -->
				<tr>
					<td class="row1" align="left"><span class="genmed"><a href="{show_vehicles.user_vehicles.U_VIEW_VEHICLE}">{show_vehicles.user_vehicles.VEHICLE}</a></span></td>
				</tr>
				<!-- END user_vehicles -->
			</table>
			<br />
			<!-- END show_vehicles -->

			<!-- BEGIN lastupdatedvehiclesmain_on -->
			<table width="100%" cellpadding="2" cellspacing="1" border="0" class="forumline">
				<tr>
					<th class="thHead" height="25" colspan="4">{L_LATEST_UPDATED}</th>
				</tr>
				<!-- BEGIN updated_vehicles -->
				<tr>
					<td class="row1" align="left" nowrap><span class="genmed"><a href="{lastupdatedvehiclesmain_on.updated_vehicles.U_VIEW_VEHICLE}">{lastupdatedvehiclesmain_on.updated_vehicles.VEHICLE}</a><br />{lastupdatedvehiclesmain_on.updated_vehicles.UPDATED_TIME}<br />{L_OWNER}: <a href="{lastupdatedvehiclesmain_on.updated_vehicles.U_VIEW_PROFILE}">{lastupdatedvehiclesmain_on.updated_vehicles.USERNAME}</a></span></td>
				</tr>
				<!-- END updated_vehicles -->
			</table>
			<br />
			<!-- END lastupdatedvehiclesmain_on -->
		</td>
