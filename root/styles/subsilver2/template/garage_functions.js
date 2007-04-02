/**
* Taken from phpBB.com permissions.js
* Set display of page element
* s[-1,0,1] = hide,toggle display,show
*/
function dE(n, s, type)
{
	if (!type)
	{
		type = 'block';
	}

	var e = document.getElementById(n);
	if (!s)
	{
		s = (e.style.display == '') ? -1 : 1;
	}
	e.style.display = (s == 1) ? type : 'none';
}

/**
* Taken from phpBB.com permissions.js
* Show/hide option panels
* value = suffix for ID to show
* adv = we are opening advanced permissions
* view = called from view permissions
*/
function swap_options(pmask, fmask, cat, adv, view)
{
	id = pmask + fmask + cat;
	active_option = active_pmask + active_fmask + active_cat;

	var old_tab = document.getElementById('tab' + active_option);	
	var new_tab = document.getElementById('tab' + id);
	var adv_block = document.getElementById('advanced' + pmask + fmask);

	// no need to set anything if we are clicking on the same tab again
	if (new_tab == old_tab && !adv)
	{
		return;
	}

	// set active tab
	old_tab.className = old_tab.className.replace(/\ activetab/g, '');
	new_tab.className = new_tab.className + ' activetab';

	if (id == active_option && adv != true)
	{
		return;
	}

	dE('options' + active_option, -1);
	
	if (!view)
	{
		dE('advanced' + pmask + fmask, 1);
	}
	dE('options' + id, 1);

	active_pmask = pmask;
	active_fmask = fmask;
	active_cat = cat;
}




