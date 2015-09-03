=== Date Picker in List Fields for Gravity Forms ===
Contributors: ovann86
Donate link: http://www.itsupportguides.com/
Tags: Gravity Forms
Requires at least: 4.2
Tested up to: 4.3
Stable tag: 1.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to turn a list field column into a 'date' field

== Description ==

> This plugin is an add-on for the <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">Gravity Forms</a>. If you don't yet own a license of the best forms plugin for WordPress, go and <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">buy one now</a>!

**What does this plugin do?**

* make any list field column a date picker field
* works with single and multi-column list fields

You can choose the date format for each datepicker. Options include:

* mm/dd/yyyy
* dd/mm/yyyy
* dd-mm-yyyy
* dd.mm.yyyy
* yyyy/mm/dd
* yyyy-mm-dd
* yyyy.mm.dd

For multi-column lists, the date picker options are under the 'General' tab, below the list of columns. For single-column lists the date picker options are under the 'Appearance' tab.

> See a demo of this plugin at [demo.itsupportguides.com/gravity-forms-list-field-date-picker/](http://demo.itsupportguides.com/gravity-forms-list-field-date-picker/ "demo website")

**Disclaimer**

*Gravity Forms is a trademark of Rocketgenius, Inc.*

*This plugins is provided “as is” without warranty of any kind, expressed or implied. The author shall not be liable for any damages, including but not limited to, direct, indirect, special, incidental or consequential damages or losses that occur out of the use or inability to use the plugin.*

== Installation ==

1. Install plugin from WordPress administration or upload folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress administration
1. Open the Gravity Forms 'Forms' menu
1. Open the forms editor for the form you want to change
1. Add or open an existing list field
1. With multiple columns enabled you will see a 'date field' option - when ticked the front end will use the jQuery datapicker

== Frequently Asked Questions ==

**How do I set a default date?**

The 'itsg_default_datepicker_date' action is available to set a default date.

To use this action you will need to add some code to your themes functions.php file, below the starting <?php line.

The action takes a date formatted as a string, for example 01/01/2015. Your code could return a simple date, e.g. 01/06/2015 or using the PHP date function you could create a dynamic date, such as 'Monday of this week'.

It is important that the format of the string matches the formatting of the date picker field.

The example below will set the default date to the Monday of the current week - note the date format is d/m/Y

`add_action('itsg_default_datepicker_date', function () {
	echo date("d/m/Y",strtotime('monday this week'));
});`

The example below will set the default date to 20 days ahead of the current date - note the date format is yyyy.mm.dd

`add_action('itsg_default_datepicker_date', function () {
	echo date("Y.m.d",strtotime('+20 days'));
});`

The example below will set the default date to 15-01-2015 (15 January 2015) - note the date format is d-m-Y

`add_action('itsg_default_datepicker_date', function () {
	echo '15-01-2015';
});`

The example below will set the default date to the first day of the following year - note the date format is in m/d/Y

`add_action('itsg_default_datepicker_date', function () {
    echo date("m/d/Y",strtotime('first day of January next year'));
});`

For more information on the strtotime function, see [strtotime](http://php.net/manual/en/function.strtotime.php)

For more information on the date function, see [date](http://php.net/manual/en/function.date.php)

**How do I configure the datepicker**

Rocketgenius have documentation on how to configure the datepicker in Gravity Forms using the [gform_datepicker_options_pre_init hook](https://www.gravityhelp.com/documentation/article/gform_datepicker_options_pre_init/#2-no-weekends).

As an example of how to implement this, the following code will disable weekends from the datepicker for all forms and all datepicker fields.

`	add_action('wp_footer', function () {
	?>
	<script>
	gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {
		optionsObj.firstDay = 1;
		optionsObj.beforeShowDay = jQuery.datepicker.noWeekends;
		return optionsObj;
	});
	</script>
	<?php
	},10);`

== Screenshots ==

1. Shows the 'Date field' option in the forms editor
2. Shows a list field using the jQuery datepicker
3. Shows a list field using the jQuery datepicker, with the default Gravity Forms styles

== Changelog ==

= 1.3 =
* Fix: Update datepicker jQuery to improve performance.
* Fix: Add CSS override to allow the list field datepicker to use the full column width. Override only applies if Gravity Forms styles are enabled.

= 1.2.5 =
* Feature: Added the ability to set a default date by calling the 'itsg_default_datepicker_date' action. See frequently asked questions for how to use this.

= 1.2.4 =
* Feature: Added the ability to apply date picker to a single column list field (found under the appearance tab options).
* Feature: Included Gravity Forms date picker CSS. The same styles will be applied as seen in the 'date' field. This will be disabled if you have configured the Gravity Forms settings to not use their CSS styles.
* Feature: Added check so that JavaScript does not load on front-end form page if there are no pick picker enabled lists in the form.

= 1.2.3 =
* Fix: Resolve issue with date picker not loading beyond the first row in ajax enabled multi-page forms.

= 1.2.2 =
* Fix: Resolve issue with plugin attempting to load before Gravity Forms has loaded, making this plugin not function.

= 1.2 =
* Feature: Updated how field 'Date picker' option appears when editing a list field in the back-end form editor.
* Maintenance: Updated back-end form editor JavaScript so it wont conflict with other plugins and is more adaptable to changes in the Gravity Forms JavaScript.
* Fix: Resolve issue with plugin breaking single column list fields, but checking that field has columns before attempting to load and modify column contents.
* Fix: Resolve PHP error messages - added isset( $choice["isDatePicker"] ) before calling array item, and check that list field has columns before calling column data.
* Maintenance: Changed name from 'Gravity Forms - List Field Date Picker' to 'Date Picker in List Fields for Gravity Forms'.

= 1.1 =
* Feature: Added ability to select the date format for each datepicker field. Formats available are mm/dd/yyyy, dd/mm/yyyy, dd-mm-yyyy, dd.mm.yyyy, yyyy/mm/dd, yyyy-mm-dd, yyyy.mm.dd.

= 1.0 =
* First public release.

== Upgrade Notice ==

= 1.0 =
First public release.