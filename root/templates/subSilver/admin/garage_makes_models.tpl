<h1>{L_GARAGE_MODELS_TITLE}</h1>

<p>{L_GARAGE_MODELS_EXPLAIN}</p>

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
			document.write( '<span '+sOnStyle+'onclick="__on('+sVar+');__off('+sOn+');__on('+sOff+');" id="'+sOn+'" title="Click here to show models"'+sSymStyle+'>+<\/span>' + 
					'<span '+sOffStyle+'onclick="__off('+sVar+');__off('+sOff+');__on('+sOn+');" id="'+sOff+'" title="Click here to hide models"'+sSymStyle+'>-<\/span>' ); 
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

<form action="{S_GARAGE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_MAKE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2"><input name="make" type="text" class="post" size="35" value="{S_MAKE}" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="add_make" name="mode" /><input name="submit" type="submit" value="{L_ADD_MAKE_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>


<form method="post" name="manage_makes_models" action="{S_MODE_ACTION}">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline"> 
	<tr> 
		<th class="thCornerL" width ="60%" height="20" valign="middle" nowrap="nowrap">{L_MAKE}</th> 
		<th class="thTop" height="20" valign="middle" nowrap="nowrap">{L_RENAME}</th> 
		<th class="thTop" height="20" valign="middle" nowrap="nowrap">{L_MODELS}</th> 
		<th class="thTop" height="20" valign="middle" nowrap="nowrap">{L_STATUS}</th> 
		<th class="thCornerR" height="20" valign="middle" nowrap="nowrap">{L_DELETE}</th> 
	</tr> 
<!-- BEGIN make --> 
	<tr>
		<td class="{make.COLOR}" align="left"><span class="gen">{make.MAKE}</span></td> 
		<td class="{make.COLOR}" align="center" nowrap=nowrap><input name="make_{make.ID}_title" type="text" class="post" size="25" value="{}" /><span class="genmed">&nbsp;&nbsp;<a href="{make.U_RENAME}">{make.RENAME}</a></span></td>
		<td class="{make.COLOR}" align="center"><span class="genmed">
			<script language="JavaScript" type="text/javascript"> 
			<!-- 
				onoff('make{make.ID}_switch',false); 
			//--> 
			</script>
		</td> 
		<td class="{make.COLOR}" align="center" ><span class="gensmall"><a href="{make.U_STATUS}">{make.STATUS}</a></span></td> 
		<td class="{make.COLOR}" align="center" ><span class="gensmall"><a href="{make.U_DELETE}">{make.DELETE}</a></span></td> 
	</tr> 
	<tr id="make{make.ID}_switch" style="display:none;"> 
		<td class="{make.COLOR}" width="100%" border="0" colspan="5"> 
			<table  width="100%" cellpadding="5" cellspacing="1" border="0"> 
				<tr>
					<td class="catBottom" align="center" valign="middle" >{L_MODEL}</td>
					<td class="catBottom" align="center" valign="middle" >{L_RENAME}</td>
					<td class="catBottom" align="center" valign="middle" >{L_STATUS}</td>
					<td class="catBottom" align="center" valign="middle" >{L_DELETE}</td>
				</tr>
<!-- BEGIN model --> 
				<tr>
					<td class="{make.COLOR}">{make.MAKE}</td>
					<td class="{make.COLOR}"><input name="name" type="text" class="post" size="35" value="{make.model.MODEL}" /></td>
					<td class="{make.COLOR}"><a href="{make.model.U_STATUS}">{make.model.STATUS}</a></td>
					<td class="{make.COLOR}"><a href="{make.model.U_DELETE}">{make.model.DELETE}</a></td>
				</tr> 
<!-- END model --> 
			</table> 
		</td> 
	</tr> 
<!-- END make --> 
	<tr>
		<td class="catBottom" height="18" align="center" valign="middle" colspan="4"><input type="hidden" value="" name="id" /></td>
	</tr>
</table>
