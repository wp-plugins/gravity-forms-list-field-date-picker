<?php
/*
Plugin Name: Gravity Forms - List Field Date Picker
Description: Gives the option of adding a date picker to a list field column
Version: 1.2
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com 
License: GPL2
*/


if (!class_exists('ITSG_GF_List_Field_Date_Picker')) {
    class ITSG_GF_List_Field_Date_Picker
    {
		private static $name = 'Gravity Forms - List Field Date Picker';
		private static $slug = 'itsg_gp_list_field_date_picker';
		
		/**
         * Construct the plugin object
         */
		 public function __construct()
        {
            // register actions
            if ((self::is_gravityforms_installed())) {
			// start the plugin
			add_filter('gform_column_input_content', array(&$this,'change_column_content'), 10, 6);
			add_action('gform_enqueue_scripts', array(&$this,'datepicker_js'), 90, 3);
			add_action('gform_editor_js', array(&$this,'editor_js'));
			}
		}

		/*
         * Changes column field if 'date field' option is ticked. Adds 'datepicker' class.
         */
		public static function change_column_content($input, $input_info, $field, $text, $value, $form_id) {
			if (is_admin()) {
				return $input;
			} else {
				$has_columns = is_array($field["choices"]);
				if ($has_columns) {
					foreach($field["choices"] as $choice){
						if ($text == $choice["text"]  && (isset( $choice["isDatePicker"] ) && $choice["isDatePicker"] == true) && isset( $choice["isDatePickerFormat"] )) {
							$new_input = str_replace("<input ","<input class='datepicker ".$choice["isDatePickerFormat"]." ' ",$input);
							return $new_input;
						} else if ($text == $choice["text"]) {
							return $input;
						}
					}
				} else {
					return $input;
				}
			}
		} // itsg_gp_list_field_datepicker_change_column_content

		/*
         * Enqueue JavaScript to footer
         */
		public function datepicker_js() {
			wp_enqueue_script('gform_datepicker_init');
			add_action('wp_footer', array(&$this,'datepicker_js_script'));
		} // END itsg_gp_list_field_datepicker_js
		
		/*
         * JavaScript used by front end - assigns datepicker to fields, unbinds/destorys then re-assigns when field row is repeated
         */
		public static function datepicker_js_script() {
		?>
		<script>
		function itsg_gf_ajax_datepicker_function(self){
			jQuery('.gfield_list').each(function() {
				jQuery(this).find('.datepicker').removeClass('hasDatepicker').removeAttr('id');
				jQuery(this).find('.datepicker').unbind('.datepicker').datepicker();
				jQuery(this).find('.datepicker').datepicker('destroy');
				gformInitDatepicker();
			});
		}

		jQuery(function(){
		// run when page is loaded
		itsg_gf_ajax_datepicker_function();  
			// run when new row is added
			jQuery('.gfield_list').on("click", ".add_list_item", function(){
				itsg_gf_ajax_datepicker_function(jQuery(this));  
			});
		});
		</script> <?php
		} // END datepicker_js_script
		
		/*
         * JavaScript used by form editor - Functions taken from Gravity Forms source and extended to handle the 'Date field' option
         */
		public static function editor_js() {
		?>
		<script type='text/javascript'>
		// ADD drop down options to list field in form editor - hooks into existing GetFieldChoices function.
		(function (w){
			var GetFieldChoicesOld = w.GetFieldChoices;
			
			w.GetFieldChoices = function (){

				str = GetFieldChoicesOld.apply(this, [field]);
				
				if(field.choices == undefined)
				return "";
				
				for(var i=0; i<field.choices.length; i++){
				var inputType = GetInputType(field);
				var isDatePicker = field.choices[i].isDatePicker ? "checked" : "";
				var value = field.enableChoiceValue ? String(field.choices[i].value) : field.choices[i].text;
				var isDatePickerFormat = isDatePicker ? field.choices[i].isDatePickerFormat : "";
				if (inputType == 'list' ){
				if (i == 0 ){
				str += "<p><strong>Date Picker fields</strong><br>Place a tick next to the column name to make it a date picker field. Select the date format from the 'Date Format' options.</p>";
				}
				str += "<div>";
				 str += "<input type='checkbox' name='choice_datepicker' id='" + inputType + "_choice_datepicker_" + i + "' " + isDatePicker + " onclick=\"SetFieldChoiceDP('" + inputType + "', " + i + ");itsg_gf_list_datepicker_function();\" /> ";
				 str += "	<label class='inline' for='"+ inputType + "_choice_datepicker_" + i + "'>"+value+" - Make Date Picker</label>";
				 str += "<div style='display:none' class='itsg_datepicker'>";
				 str += "<label style='display: inline; margin-right: 10px; font-weight: 800;' for='" + inputType + "_choice_datepickerformat_" + i + "'>";
				 str += "Date Format:</label>";
				 str += "<select class='choice_datepickerformat' id='" + inputType + "_choice_datepickerformat_" + i + "' onchange=\"SetFieldChoiceDP('" + inputType + "', " + i + ");\">";
				 str += "<option value='mdy'>mm/dd/yyyy</option>";
				 str += "<option value='dmy'>dd/mm/yyyy</option>";
				 str += "<option value='dmy_dash'>dd-mm-yyyy</option>";
				 str += "<option value='dmy_dot'>dd.mm.yyyy</option>";
				 str += "<option value='ymd_slash'>yyyy/mm/dd</option>";
				 str += "<option value='ymd_dash'>yyyy-mm-dd</option>";
				 str += "<option value='ymd_dot'>yyyy.mm.dd</option>";
				 str += "</select>";		 
				 
				 str += "</div>";
				 str += "</div>";
				 }
				 }
				return str;
			}
		})(window || {});
		function SetFieldChoiceDP(inputType, index){
			
			var element = jQuery("#" + inputType + "_choice_selected_" + index);
			
			if ('list' == inputType) {
			var element = jQuery("#" + inputType + "_choice_datepicker__" + index);
			isDatePicker = element.is(":checked");
			isDatePickerFormat = jQuery("#" + inputType + "_choice_datepickerformat_" + index).val();
			}
			field = GetSelectedField();

			if ('list' == inputType) {
			field.choices[index].isDatePickerFormat = isDatePickerFormat;
			}

			//set field selections
			jQuery("#field_columns input[name='choice_datepicker']").each(function(index){
				field.choices[index].isDatePicker = this.checked;
			});

			LoadBulkChoices(field);

			UpdateFieldChoices(GetInputType(field));
		}

		</script>
		
		<script type="text/javascript">
		function itsg_gf_list_datepicker_function(){
			jQuery('#field_columns input[name=choice_datepicker]').each(function() {
				if (jQuery(this).is(":checked")) {
						jQuery(this).parent("div").find(".itsg_datepicker").show();
					}
					else {
						jQuery(this).parent("div").find(".itsg_datepicker").hide();
					}
			});
		jQuery("#field_columns select.choice_datepickerformat").each(function(index){
				jQuery(this).val(field.choices[index].isDatePickerFormat);
			});
		}
		
		// trigger for when field is opened
		jQuery(document).on('click', 'ul.gform_fields', function(){
			itsg_gf_list_datepicker_function();  
		});
		
		// trigger for when column titles are updated
		jQuery(document).on('change','#gfield_settings_columns_container #field_columns li',function() {
			InsertFieldChoice(0);
			DeleteFieldChoice(0);
			itsg_gf_list_datepicker_function();
		});		
		</script>
		
		
		<?php
		} // END itsg_gp_list_field_datepicker_editor_js
		
		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed()
        {
            return class_exists('GFAPI');
        } // END is_gravityforms_installed
	}
    $ITSG_GF_List_Field_Date_Picker = new ITSG_GF_List_Field_Date_Picker();
}