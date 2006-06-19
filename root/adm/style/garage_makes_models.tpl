<h1>{L_GARAGE_MODELS_TITLE}</h1>

<p>{L_GARAGE_MODELS_EXPLAIN}</p>

<script language="JavaScript"> 
<!-- 

	//Function To Rename Make Or Model
	function add_model ( selected )
	{
		formErrors = false;    
		
		//Check To Make Sure A Value Has Been Entered
		if (document.manage_makes_models.elements['add_model_'+selected+'_title'].value.length < 2)
		{
			formErrors = "{L_EMPTY_TITLE}";
		}

		//Display If Needed Error Message Else Perform Work
		if (formErrors) 
		{
			alert(formErrors);
		} 
		else
		{
			document.manage_makes_models.model.value = document.manage_makes_models.elements['add_model_'+selected+'_title'].value;
			document.manage_makes_models.make_id.value = selected;
			document.manage_makes_models.mode.value = "insert_model" ;
	
			//Submit The Form
			document.manage_makes_models.submit() ;
		}
	}


	//Function To Rename Make Or Model
	function rename ( selected, obj )
	{
		formErrors = false;    
		
		//Check To Make Sure A Value Has Been Entered
		if (obj == '1')
		{
			if (document.manage_makes_models.elements['make_'+selected+'_title'].value.length < 2)
			{
				formErrors = "{L_EMPTY_TITLE}";
			}
		}
		else if (obj == '2')
		{
			if (document.manage_makes_models.elements['model_'+selected+'_title'].value.length < 2)
			{
				formErrors = "{L_EMPTY_TITLE}";
			}
		}

		//Display If Needed Error Message Else Perform Work
		if (formErrors) 
		{
			alert(formErrors);
		} 
		else
		{
			//Set The Action To Correct Type
			if (obj == '1')
			{
				document.manage_makes_models.make.value = document.manage_makes_models.elements['make_'+selected+'_title'].value;
				document.manage_makes_models.id.value = selected;
				document.manage_makes_models.mode.value = "update_make" ;
			}
			else if (obj == '2' )
			{
				document.manage_makes_models.model.value = document.manage_makes_models.elements['model_'+selected+'_title'].value;
				document.manage_makes_models.id.value = selected;
				document.manage_makes_models.mode.value = "update_model" ;
			}
	
			//Submit The Form
			document.manage_makes_models.submit() ;
		}
	}

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

<form name="insert_make" action="{S_MODE_ACTION}" method="post">
<table width="100%" cellpadding="3" cellspacing="1" border="0" class="forumline">
	<tr>
		<th class="thHead" height="25" nowrap="nowrap" colspan="2">{L_ADD_MAKE}</th>
	</tr>
	<tr>
		<td class="row1" width="20%"><span class="gen">{L_VEHICLE_MAKE}</span></td>
		<td class="row2"><input name="make" type="text" class="post" size="35" value="" /></td>
	</tr>
	<tr>
		<td class="catBottom" align="center" height="28" colspan="2"><input type="hidden" value="insert_make" name="mode" /><input name="submit" type="submit" value="{L_ADD_MAKE_BUTTON}" class="liteoption" /></td>
	</tr>
</table>
</form>

<form name="manage_makes_models" action="{S_MODE_ACTION}" method="post">
<table width="100%" cellpadding="1" cellspacing="1" border="0" class="forumline"> 
	<tr> 
		<th class="thCornerL" width ="20%" valign="middle" nowrap="nowrap">{L_MAKE}</th> 
		<th class="thTop" valign="middle" nowrap="nowrap">{L_RENAME}</th> 
		<th class="thTop" valign="middle" nowrap="nowrap">{L_MODELS}</th> 
		<th class="thTop" valign="middle" nowrap="nowrap">{L_STATUS}</th> 
		<th class="thCornerR" valign="middle" nowrap="nowrap">{L_DELETE}</th> 
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
			<table class="forumline" width="100%" cellpadding="1" cellspacing="1" border="0"> 
				<tr>
					<th class="thTop" align="center" valign="middle" colspan="4">{L_ADD_MODEL}</td>
				</tr>
				<tr>
					<td class="{make.COLOR}" align="center" colspan="4"><input name="add_model_{make.ID}_title" type="text" class="post" size="35" value="{}" /><span class="genmed">&nbsp;&nbsp;<a href="{make.U_ADD_MODEL}">{make.ADD_MODEL}</a></span></td>
				</tr>
				<tr>
					<th class="thCornerL" width="20%" align="center" valign="middle" >{L_MODEL}</td>
					<th class="thTop" align="center" valign="middle" >{L_RENAME}</td>
					<th class="thTop" align="center" valign="middle" >{L_STATUS}</td>
					<th class="thCornerR" align="center" valign="middle" >{L_DELETE}</td>
				</tr>
<!-- BEGIN model --> 
				<tr>
					<td class="{make.COLOR}" align="left">{make.model.MODEL}</td>
					<td class="{make.COLOR}" align="center"><input name="model_{make.model.ID}_title" type="text" class="post" size="35" value="{}" /><span class="genmed">&nbsp;&nbsp;<a href="{make.model.U_RENAME}">{make.model.RENAME}</a></span></td>
					<td class="{make.COLOR}" align="center"><a href="{make.model.U_STATUS}">{make.model.STATUS}</a></td>
					<td class="{make.COLOR}" align="center"><a href="{make.model.U_DELETE}">{make.model.DELETE}</a></td>
				</tr> 
<!-- END model --> 
			</table> 
		</td> 
	</tr> 
<!-- END make --> 
	<tr>
		<td class="catBottom" height="18" align="center" valign="middle" colspan="5"><input type="hidden" value="" name="id" /><input type="hidden" value="" name="make_id" /><input type="hidden" value="" name="make" /><input type="hidden" value="" name="mode" /><input type="hidden" value="" name="model" /></td>
	</tr>
</table>
