=== Date Picker in List Fields for Gravity Forms ===
Contributors: ovann86
Donate link: http://www.itsupportguides.com/
Tags: Gravity Forms
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.2.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to turn a list field column into a 'date' field

== Description ==

Adds the ability to make the [Gravity Forms](http://www.gravityforms.com/ "Gravity Forms website") list field include a jQuery date picker.

The plugin allows you to choose if a list field or any list field columns use the jQuery date pick as seen in the Gravity Forms 'Date' field.

For multi-column lists, the date picker options are under the 'General' tab, below the list of columns. For single-column lists the date picker options are under the 'Appearance' tab.

You can choose the date format for each datepicker. Options include:

* mm/dd/yyyy
* dd/mm/yyyy
* dd-mm-yyyy
* dd.mm.yyyy
* yyyy/mm/dd
* yyyy-mm-dd
* yyyy.mm.dd

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

== Screenshots ==

1. Shows the 'Date field' option in the forms editor
2. Shows a list field using the jQuery datepicker

== Changelog ==

= 1.2.4 =
* Improvement: Added the ability to apply date picker to a single column list field (found under the appearance tab options).
* Improvement: Included Gravity Forms date picker CSS. The same styles will be applied as seen in the 'date' field. This will be disabled if you have configured the Gravity Forms settings to not use their CSS styles.
* Improvement: Added check so that JavaScript does not load on front-end form page if there are no pick picker enabled lists in the form.

= 1.2.3 =
* Fix: Resolve issue with date picker not loading beyond the first row in ajax enabled multi-page forms.

= 1.2.2 =
* Fix: Resolve issue with plugin attempting to load before Gravity Forms has loaded, making this plugin not function.

= 1.2 =
* Improvement: Updated how field 'Date picker' option appears when editing a list field in the back-end form editor.
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