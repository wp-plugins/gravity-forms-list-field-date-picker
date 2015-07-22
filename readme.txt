=== Date Picker in List Fields for Gravity Forms ===
Contributors: ovann86
Donate link: http://www.itsupportguides.com/
Tags: Gravity Forms
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.2.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A date picker for the list field

== Description ==

Adds the ability to make the [Gravity Forms](http://www.gravityforms.com/ "Gravity Forms website") list field include a jQuery datepicker.

The plugin adds a 'Date field' option to each column (when a multiple columns enabled). When the 'Date field' option is ticked the field will use the jQuery datepicker on the frontend.

You can also choose the date format for each datepicker. Options include:

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