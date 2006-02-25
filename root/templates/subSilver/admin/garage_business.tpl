<h1>{L_GARAGE_BUSINESS_TITLE}</h1>

<p>{L_GARAGE_BUSINESS_EXPLAIN}</p>

<script language="JavaScript"> 
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

		if( (navigator.userAgent.indexOf("MSIE") >= 0) && document && document.body && document.body.style) 
		{ 
			document.write( '<span '+sOnStyle+'onclick="__on('+sVar+');__off('+sOn+');__on('+sOff+');" id="'+sOn+'" title="Click here to show details"'+sSymStyle+'>+<\/span>' + 
					'<span '+sOffStyle+'onclick="__off('+sVar+');__off('+sOff+');__on('+sOn+');" id="'+sOff+'" title="Click here to hide details"'+sSymStyle+'>-<\/span>' ); 
		} 
		else 
		{ 
			document.write('<span id="' + objName + '_on" onclick="__on(document.getElementById(\'' + objName + '\'));__off(document.getElementById(\'' + objName + '_on\'));__on(document.getElementById(\'' + objName + '_off\'));" title="Click here to show details" style="text-align: center;width: 13;height: 13;cursor: pointer;color: #003344;' + (bObjState ? ' display:none;' : '') + '">&nbsp;{SHOW}&nbsp;</span>'); 
			document.write('<span id="' + objName + '_off" onclick="__off(document.getElementById(\'' + objName + '\'));__on(document.getElementById(\'' + objName + '_on\'));__off(document.getElementById(\'' + objName + '_off\'));" title="Click here to show details" style="text-align: center;width: 13;height: 13;cursor: pointer;color: #003344;' + (!bObjState ? ' display:none;' : '') + '">&nbsp;{HIDE}&nbsp;</span>'); 
		}
      } 
// --> 
</script>
</head>

<form method="post" name="manage_business" action="{S_GARAGE_MODE_UPDATE}">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline"> 
	<tr> 
		<th class="thCornerL" width ="60%" height="20" valign="middle" nowrap="nowrap">{L_NAME}</th> 
		<th class="thTop" height="20" valign="middle" nowrap="nowrap">{L_EDIT}</th> 
		<th class="thTop" height="20" valign="middle" nowrap="nowrap">{L_STATUS}</th> 
		<th class="thCornerR" height="20" valign="middle" nowrap="nowrap">{L_DELETE}</th> 
	</tr> 
<!-- BEGIN business --> 
	<tr>
		<td class="{business.COLOR}" align="left"><span class="gen">{business.TITLE}</span></td> 
		<td class="{business.COLOR}" align="center"><span class="genmed">
			<script language="JavaScript" type="text/javascript"> 
			<!-- 
				onoff('business{business.ID}_switch',false); 
			//--> 
			</script>
		</td> 
		<td class="{business.COLOR}" align="center" ><span class="gensmall"><a href="{business.U_STATUS}">{business.STATUS}</a></span></td> 
		<td class="{business.COLOR}" align="center" ><span class="gensmall"><a href="{business.U_DELETE}">{business.DELETE}</a></span></td> 
	</tr> 
	<tr id="business{business.ID}_switch" style="display:none;"> 
		<td class="{business.COLOR}" border="1" colspan="4"> 
			<table cellpadding="5" cellspacing="1" border="0"> 
<!-- BEGIN detail --> 
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_NAME}</b></span></td>
					<td class="{business.COLOR}"><input name="title_" type="text" class="post" size="35" value="{business.TITLE}" /></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_ADDRESS}</b></span></td>
					<td class="{business.COLOR}"><textarea name='address_' cols='60' rows='5' wrap='soft' class='multitext'>{business.ADDRESS}</textarea></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_TELEPHONE_NO}</b></span></td>
					<td class="{business.COLOR}"><input name="telephone_" type="text" class="post" size="35" value="{business.TELEPHONE}" /></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_FAX_NO}</b></span></td>
					<td class="{business.COLOR}"><input name="fax_" type="text" class="post" size="35" value="{business.FAX}" /></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_WEBSITE}</b></span></td>
					<td class="{business.COLOR}"><input name="website_" type="text" class="post" size="35" value="{business.WEBSITE}" /></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_EMAIL}</b></span></td>
					<td class="{business.COLOR}"><input name="email_" type="text" class="post" size="35" value="{business.EMAIL}" /></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_OPENING_HOURS}</b></span></td>
					<td class="{business.COLOR}"><textarea name='opening_hours_' cols='60' rows='3' wrap='soft'   class='multitext'>{business.OPENING_HOURS}</textarea></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" width="20%"><span class="gen"><b>{L_BUSINESS_TYPE}</b></span></td>
					<td class="{business.COLOR}">{L_INSURANCE} : <input type='checkbox' name='insurance_' {business.INSURANCE_CHECKED} >&nbsp;{L_GARAGE} : <input type='checkbox' name='garage_' {business.GARAGE_CHECKED} >&nbsp;{L_RETAIL_SHOP} : <input type='checkbox' name='retail_shop_' {business.RETAIL_CHECKED} >&nbsp;{L_WEB_SHOP} : <input type='checkbox' name='web_shop_' {business.WEB_CHECKED} ></td>
				</tr>
				<tr>
					<td class="{business.COLOR}" align="center" height="28" colspan="2">{business.U_UPDATE}</td>
				</tr> 
<!-- END detail --> 
			</table> 
		</td> 
	</tr> 
<!-- END business --> 
	<tr>
		<td class="catBottom" height="18" align="center" valign="middle" colspan="4"><input type="hidden" value="" name="id" /></td>
	</tr>
</table>
</form>
<br/>

<form action="{S_GARAGE_MODE_NEW}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_NEW_BUSINESS}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_NAME}</b></span></td>
		<td class="row2"><input name="title" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="row1" width="35%"><span class="gen"><b>{L_BUSINESS_ADDRESS}</b></span></td>
		<td class="row2"><textarea name='address' cols='60' rows='5' wrap='soft' class='multitext'></textarea></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_TELEPHONE_NO}</b></span></td>
		<td class="row2"><input name="telephone" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_FAX_NO}</b></span></td>
		<td class="row2"><input name="fax" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_WEBSITE}</b></span></td>
		<td class="row2"><input name="website" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_EMAIL}</b></span></td>
		<td class="row2"><input name="email" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="row1" width="35%"><span class="gen"><b>{L_BUSINESS_OPENING_HOURS}</b></span></td>
		<td class="row2"><textarea name='opening_hours' cols='60' rows='3' wrap='soft'   class='multitext'></textarea></td>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen"><b>{L_BUSINESS_TYPE}</b></span></td>
		<td class="row2">{L_INSURANCE} : <input type='checkbox' name='insurance' {CHECKED} ><br />{L_GARAGE} : <input type='checkbox' name='garage' {CHECKED} ><br />{L_RETAIL_SHOP} : <input type='checkbox' name='retail_shop' {CHECKED} ><br />{L_WEB_SHOP} : <input type='checkbox' name='web_shop' {CHECKED} ></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="insert_business" name="mode" /><input name="submit" type="submit" value="{L_ADD_NEW_BUSINESS}" class="liteoption" /></td>
	</tr>
</table>
</form>
<br />

