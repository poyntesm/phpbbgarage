<!-- BEGIN dropdown -->
	<select name="{dropdown.FIELD_IDENT}">
		<!-- BEGIN options --><option value="{dropdown.options.OPTION_ID}"{dropdown.options.SELECTED}>{dropdown.options.VALUE}</option><!-- END options -->
	</select>
<!-- END dropdown -->

<!-- BEGIN text -->
	<textarea name="{text.FIELD_IDENT}" rows="{text.FIELD_ROWS}" cols="{text.FIELD_COLS}">{text.FIELD_VALUE}</textarea>
<!-- END text -->

<!-- BEGIN string -->
	<input type="text" class="post" name="{string.FIELD_IDENT}" size="{string.FIELD_LENGTH}" maxlength="{string.FIELD_MAXLEN}" value="{string.FIELD_VALUE}" />
<!-- END string -->

<!-- BEGIN bool -->
	<!-- IF bool.FIELD_LENGTH eq 1 -->
		<!-- BEGIN options --><input type="radio" name="{bool.FIELD_IDENT}" value="{bool.options.OPTION_ID}"{bool.options.CHECKED} /><span class="genmed">{bool.options.VALUE}</span>&nbsp; &nbsp;<!-- END options -->
	<!-- ELSE -->
		<input type="checkbox" name="{bool.FIELD_IDENT}"<!-- IF bool.FIELD_VALUE --> checked="checked"<!-- ENDIF --> />
	<!-- ENDIF -->
<!-- END bool -->

<!-- BEGIN int -->
	<input type="text" class="post" name="{int.FIELD_IDENT}" size="{int.FIELD_LENGTH}" value="{int.FIELD_VALUE}" />
<!-- END int -->

<!-- BEGIN date -->
	<span class="genmed">{L_DAY}:</span> <select name="{date.FIELD_IDENT}_day">{date.S_DAY_OPTIONS}</select>
	<span class="genmed">{L_MONTH}:</span> <select name="{date.FIELD_IDENT}_month">{date.S_MONTH_OPTIONS}</select>
	<span class="genmed">{L_YEAR}:</span> <select name="{date.FIELD_IDENT}_year">{date.S_YEAR_OPTIONS}</select>
<!-- END date -->
