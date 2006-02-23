<script type="text/javascript" language="javascript">
<!--
// Auto-generated make/model combinations
var cars = new Array();

{VEHICLE_ARRAY}

	function sortFuncAsc(record1, record2) {
	    var value1 = record1.optText.toLowerCase();
	    var value2 = record2.optText.toLowerCase();
	    if (value1 > value2) return(1);
	    if (value1 < value2) return(-1);
	    return(0);
	}
	
	function sortSelect(selectToSort,curItem) {
	
	    // copy options into an array
	    var myOptions = [];
	    for (var loop=0; loop<selectToSort.options.length; loop++) {
	        myOptions[loop] = { optText:selectToSort.options[loop].text, optValue:selectToSort.options[loop].value };
	    }

	    // sort array
	    myOptions.sort(sortFuncAsc);

	    // copy sorted options from array back to select box
	    selectToSort.options.length = 0;
	    for (var loop=0; loop<myOptions.length; loop++) {
	        var optObj = document.createElement('option');
	        optObj.text = myOptions[loop].optText;
	        optObj.value = myOptions[loop].optValue;
	        selectToSort.options.add(optObj);
	        if (optObj.text == curItem) {
	            optObj.selected = true;
	        }
	    }
	}

	// Generate the Make Dropdown option list.
	function updateMakeSelect(makeSel, curMake) {
		with (makeSel) {
			options.length = 0;	// Clear the option list.

			// Add a header row.
			options[0] = new Option("-- Select Make --", "");

			if (! curMake) {
				options[0].selected = true;
			}

			// Add options for all makes.
			var make;
			for (make in cars) {
	            var make_id = cars[make][0];
				options[options.length] = new Option(make, make_id);
				if (curMake == make) {
					options[options.length-1].selected = true;
				}
			}

	        sortSelect(makeSel,curMake);
		}

		// Netscape does not refresh the dropdown lists automatically.
		if (!curMake) {
			return;
		}
	}

	// Update the Model Dropdown to reflect the selected make. 
	function updateModelSelect(modelSel, curMake, curModel) {
		// which model field should we update?
		with (modelSel) {
			options.length = 0;	// Clear the option list.

			// Add a header row.
			options[0] = new Option("-- Select Model --", "");

			if (! curModel) {
				options[0].selected = true;
			}

			// No models w/o a make.
			if (! curMake) {
				return;
			}

			// Add options for all this makes models.
			var num;
			for (num in cars[curMake][1]) {
				var model = cars[curMake][1][num];
	            var model_id = cars[curMake][2][num];
				options[options.length] = new Option(model, model_id);
				if (curModel == model) {
					options[options.length-1].selected = true;
				}
			}

	        sortSelect(modelSel,curModel);
		}
	}

-->
</script>
