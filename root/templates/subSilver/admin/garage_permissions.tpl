<h1>{L_GARAGE_PERMISSIONS_TITLE}</h1>

<p>{L_GARAGE_PERMISSIONS_EXPLAIN}</p>

<script language='Javascript1.1'>
<!--

	function __off(n) 
	{ 
		if(n && n.style) 
		{ 
			if('none' != n.style.display) 
			{ 
				n.style.display = 'none'; 
			} 
		} 
	} 

	function __on(n) 
	{ 
		if(n && n.style) 
		{ 
			if('none' == n.style.display) 
			{ 
				n.style.display = ''; 
			} 
		} 
	} 

	function __toggle(n) 
	{ 
		if(n && n.style) 
		{ 
			if('none' == n.style.display) 
			{ 
				n.style.display = ''; 
			} 
			else 
			{ 
				n.style.display = 'none'; 
			} 
		} 
	} 

//&#149; 

	function onoff(objName,bObjState) 
	{ 
		var sVar = ''+objName; 
		var sOn = ''+objName+'_on'; 
		var sOff = ''+objName+'_off'; 
		var sOnStyle = bObjState ? ' style="display:none;" ':''; 
		var sOffStyle = !bObjState ? ' style="display:none;" ':''; 
		var sSymStyle = ' style="text-align: center;width: 13;height: 13;font-family: Arial,Verdana;font-size: 7pt;border-style: solid;border-width: 1;cursor: hand;color: #003344;background-color: #CACACA;" '; 

		//if( (navigator.userAgent.indexOf("MSIE") >= 0) && document && document.body && document.body.style) 
		//{ 
		//	document.write( '<span '+sOnStyle+'onclick="__on('+sVar+');__off('+sOn+');__on('+sOff+');" id="'+sOn+'" title="Click here to show details"'+sSymStyle+'>+<\/span>' + 
		//			'<span '+sOffStyle+'onclick="__off('+sVar+');__off('+sOff+');__on('+sOn+');" id="'+sOff+'" title="Click here to hide details"'+sSymStyle+'>-<\/span>' ); 
		//} 
		//else 
		//{ 
		//	document.write('<span id="' + objName + '_on" onclick="__on(document.getElementById(\'' + objName + '\'));__off(document.getElementById(\'' + objName + '_on\'));__on(document.getElementById(\'' + objName + '_off\'));" title="Click here to show details" style="text-align: center;width: 13;height: 13;cursor: pointer;color: #003344;' + (bObjState ? ' display:none;' : '') + '">&nbsp;{SHOW}&nbsp;</span>'); 
		//	document.write('<span id="' + objName + '_off" onclick="__off(document.getElementById(\'' + objName + '\'));__on(document.getElementById(\'' + objName + '_on\'));__off(document.getElementById(\'' + objName + '_off\'));" title="Click here to show details" style="text-align: center;width: 13;height: 13;cursor: pointer;color: #003344;' + (!bObjState ? ' display:none;' : '') + '">&nbsp;{HIDE}&nbsp;</span>'); 
		//}
      } 

	function check_all(str_part) 
	{
		var f = document.permissions;

		//Here We Process And Turn On Admin,Mod,User,Guest Checkboxs In The Same Row.
		for (var i = 0 ; i < f.elements.length; i++)
		{
			var e = f.elements[i];
						
			if ( (e.name != 'UPLOAD_PRIVATE') && (e.name != 'BROWSE_PRIVATE') && (e.name != 'INTERACT_PRIVATE') && (e.name != 'ADD_PRIVATE') && (e.name != 'UPLOAD_ALL') && (e.name != 'BROWSE_ALL') && (e.name != 'INTERACT_ALL') && (e.name != 'ADD_ALL') && (e.type == 'checkbox') && (! e.disabled) )
			{
				s = e.name;
				a = s.substring(0, 4);
				
				if (a == str_part)
				{
					e.checked = true;
				}
			}
		}

		//Here We Make Sure That All Stays Checked If Admin,Mod,User,Guest Are Selected
		totalboxes = 0;
		total_on   = 0;
					
		for (var i = 0 ; i < f.elements.length; i++)
		{
			var e = f.elements[i];
						
			if ( (e.name != 'UPLOAD_PRIVATE') && (e.name != 'BROWSE_PRIVATE') && (e.name != 'INTERACT_PRIVATE') && (e.name != 'ADD_PRIVATE') && (e.name != 'UPLOAD_ALL') && (e.name != 'BROWSE_ALL') && (e.name != 'INTERACT_ALL') && (e.name != 'ADD_ALL') && (e.type == 'checkbox') )
			{
				s = e.name;
				a = s.substring(0, 4);
							
				if (a == str_part)
				{
					totalboxes++;
						
					if (e.checked)
					{
						total_on++;
					}
				}
			}
		}
				
		if (totalboxes == total_on)
		{
			if (str_part == 'BROW') 
			{
				 f.BROWSE_ALL.checked  = true; 
				 f.BROWSE_PRIVATE.checked  = false; 
			}
			if (str_part == 'BROW') 
			{ 
				f.INTERACT_ALL.checked = true; 
				f.INTERACT_PRIVATE.checked = false; 
			}
			if (str_part == 'ADD_') 
			{ 
				f.ADD_ALL.checked = true; 
				f.ADD_PRIVATE.checked = false; 
			}
			if (str_part == 'UPLO') 
			{ 
				f.UPLOAD_ALL.checked = true; 
				f.UPLOAD_PRIVATE.checked = false; 
			}
		}
	}

	function private_checked(str_part) 
	{
		var f = document.permissions;

		//Here We Process And Turn On Admin,Mod,User,Guest Checkboxs In The Same Row.
		for (var i = 0 ; i < f.elements.length; i++)
		{
			var e = f.elements[i];
						
			if ( (e.name != 'UPLOAD_ADMIN') && (e.name != 'BROWSE_ADMIN') && (e.name != 'INTERACT_ADMIN') && (e.name != 'ADD_ADMIN') && (e.name != 'UPLOAD_MOD') && (e.name != 'BROWSE_MOD') && (e.name != 'INTERACT_MOD') && (e.name != 'ADD_MOD') && (e.name != 'UPLOAD_PRIVATE') && (e.name != 'BROWSE_PRIVATE') && (e.name != 'INTERACT_PRIVATE') && (e.name != 'ADD_PRIVATE') && (e.type == 'checkbox') && (! e.disabled) )
			{
				s = e.name;
				a = s.substring(0, 4);
				
				if (a == str_part)
				{
					e.checked = false;
				}
				
			}
		}
	}

	function obj_checked(IDnumber) 
	{
		var f = document.permissions;
					
		str_part = '';
					
		if (IDnumber == 1) { str_part = 'BROW' }
		if (IDnumber == 2) { str_part = 'INTE' }
		if (IDnumber == 3) { str_part = 'ADD_' }
		if (IDnumber == 4) { str_part = 'UPLO' }
					
		totalboxes = 0;
		total_on   = 0;
					
		for (var i = 0 ; i < f.elements.length; i++)
		{
			var e = f.elements[i];
						
			if ( (e.name != 'UPLOAD_PRIVATE') && (e.name != 'BROWSE_PRIVATE') && (e.name != 'INTERACT_PRIVATE') && (e.name != 'ADD_PRIVATE') && (e.name != 'UPLOAD_ALL') && (e.name != 'BROWSE_ALL') && (e.name != 'INTERACT_ALL') && (e.name != 'ADD_ALL') && (e.type == 'checkbox') )
			{
				s = e.name;
				a = s.substring(0, 4);
							
				if (a == str_part)
				{
					totalboxes++;
						
					if (e.checked)
					{
						total_on++;
					}
				}
			}
		}
				
		if (totalboxes == total_on)
		{
			if (IDnumber == 1) 
			{
				 f.BROWSE_ALL.checked  = true; 
				 f.BROWSE_PRIVATE.checked  = false; 
			}
			if (IDnumber == 2) 
			{ 
				f.INTERACT_ALL.checked = true; 
				f.INTERACT_PRIVATE.checked = false; 
			}
			if (IDnumber == 3) 
			{ 
				f.ADD_ALL.checked = true; 
				f.ADD_PRIVATE.checked = false; 
			}
			if (IDnumber == 4) 
			{ 
				f.UPLOAD_ALL.checked = true; 
				f.UPLOAD_PRIVATE.checked = false; 
			}
		}
		else
		{
			if (IDnumber == 1) { f.BROWSE_ALL.checked  = false; }
			if (IDnumber == 2) { f.INTERACT_ALL.checked = false; }
			if (IDnumber == 3) { f.ADD_ALL.checked = false; }
			if (IDnumber == 4) { f.UPLOAD_ALL.checked = false; }
		}
	}
				
//-->
</script>


<form name='permissions' action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="5">{L_PERMISSION_ACCESS_LEVELS}</th>
	</tr>
	<tr>
		<td class="catBottom" width="30%" align="center"><span class="gen">{L_NAME}</span></td>
		<td class="catBottom" width="17%" align="center"><span class="gen">{L_BROWSE}</span></td>
		<td class="catBottom" width="17%" align="center"><span class="gen">{L_INTERACT}</span></td>
		<td class="catBottom" width="17%" align="center"><span class="gen">{L_ADD}</span></td>
		<td class="catBottom" width="17%" align="center"><span class="gen">{L_UPLOAD}</span></td>
	</tr>
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_GLOBAL_ALL_MASKS}</td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_ALL_MASKS}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" onClick="check_all('BROW')" name="BROWSE_ALL" value="1" {BROWSE_ALL_CHECKED} /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" onClick="check_all('INTE')" name="INTERACT_ALL" value="1" {INTERACT_ALL_CHECKED} /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" onClick="check_all('ADD_')" name="ADD_ALL" value="1"{ADD_ALL_CHECKED} /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" onClick="check_all('UPLO')" name="UPLOAD_ALL" value="1" {UPLOAD_ALL_CHECKED} /></td>
	</tr>
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_GRANULAR_PERMISSIONS}</td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_ADMINISTRATORS}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="BROWSE_ADMIN" value="1" {BROWSE_ADMIN_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="INTERACT_ADMIN" value="1" {INTERACT_ADMIN_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="ADD_ADMIN" value="1" {ADD_ADMIN_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="UPLOAD_ADMIN" value="1" {UPLOAD_ADMIN_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_MODERATORS}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="BROWSE_MOD" value="1" {BROWSE_MOD_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="INTERACT_MOD" value="1" {INTERACT_MOD_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="ADD_MOD" value="1" {ADD_MOD_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="UPLOAD_MOD" value="1" {UPLOAD_MOD_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_REGISTERED_USERS}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="BROWSE_USER" value="1" {BROWSE_USER_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="INTERACT_USER" value="1" {INTERACT_USER_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="ADD_USER" value="1" {ADD_USER_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="UPLOAD_USER" value="1" {UPLOAD_USER_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_GUEST_USERS}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="BROWSE_GUEST" value="1" {BROWSE_GUEST_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="INTERACT_GUEST" value="1" {INTERACT_GUEST_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="ADD_GUEST" value="1" {ADD_GUEST_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="UPLOAD_GUEST" value="1" {UPLOAD_GUEST_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{L_PRIVATE}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="BROWSE_PRIVATE" value="1" {BROWSE_PRIVATE_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="INTERACT_PRIVATE" value="1" {INTERACT_PRIVATE_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="ADD_PRIVATE" value="1" {ADD_PRIVATE_CHECKED} onclick="obj_checked(3)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="UPLOAD_PRIVATE" value="1" {UPLOAD_PRIVATE_CHECKED} onclick="obj_checked(4)" /></td>
	</tr>
	<!-- BEGIN private -->
	<tr>
		<td class="row1" colspan="5"><span class="gen">{L_PRIVATE_PERMISSIONS}</td>
	</tr>
	<!-- END private -->
	<!-- BEGIN usergroup -->
	<tr>
		<td class="row2" width="30%" align="center" valign="middle"><div align="right" style="font-weight:bold">{usergroup.GROUP_NAME}&nbsp;</div></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="browse[]" value="{usergroup.GROUP_ID}" {usergroup.BROWSE_CHECKED} onclick="obj_checked(1)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="interact[]" value="{usergroup.GROUP_ID}" {usergroup.INTERACT_CHECKED} onclick="obj_checked(2)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="add[]" value="{usergroup.GROUP_ID}" {usergroup.ADD_CHECKED} onclick="onoff('group{usergroup.GROUP_ID}_switch',false)" /></td>
		<td class="row1" width="17%" align="center" valign="middle"><input type="checkbox" name="upload[]" value="{usergroup.group_id}" {usergroup.upload_checked} onclick="onoff('group{usergroup.GROUP_ID}_switch',false)" /></td>
	</tr>
	<tr id="group{usergroup.GROUP_ID}_switch" style="display:none;"> 
		<td class="{usergroup.color}" border="1" colspan="4"> 
			<table cellpadding="5" cellspacing="1" border="0"> 
				<!-- begin add_quota -->
				<tr>
					<td>{L_ADD_QUOTA} : <input name="add_quota[]" type="text" class="post" size="3" value="{usergroup.ADD_QUOTA}" /></td>
				</tr>
				<!-- end add_quota -->
			</table> 
		</td> 
	</tr> 
	<!-- END usergroup -->

	<tr>
		<td class="catBottom" align="center" height="28" colspan="5"><input type="hidden" value="update_permissions" name="mode" /><input name="submit" type="submit" value="{L_SAVE}" class="liteoption" /></td>
	</tr>
</table>
</form>

