<?php
/*
Plugin Name: Gravity Forms - List Field Date Picker
Description: Gives the option of adding a date picker to a list field column
Version: 1.1
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
			add_filter('gform_column_input_content', array(&$this,'itsg_gp_list_field_datepicker_change_column_content'), 10, 6);
			add_action('gform_enqueue_scripts', array(&$this,'itsg_gp_list_field_datepicker_js'), 90, 3);
			add_action('gform_editor_js', array(&$this,'itsg_gp_list_field_datepicker_editor_js'));
			}
		}

		/*
         * Changes column field if 'date field' option is ticked. Adds 'datepicker' class.
         */
		public static function itsg_gp_list_field_datepicker_change_column_content($input, $input_info, $field, $text, $value, $form_id) {
			if (is_admin()) {
				return $input;
			} else {
				foreach($field["choices"] as $choice){
					if ($text == $choice["text"]  &&  $choice["isDatePicker"] == true) {
					$new_input = str_replace("<input ","<input class='datepicker ".$choice["isDatePickerFormat"]." ' ",$input);
					return $new_input;
					} else if ($text == $choice["text"]) {
					return $input;
					}
				}
			}
		} // itsg_gp_list_field_datepicker_change_column_content

		/*
         * Enqueue JavaScript to footer
         */
		public function itsg_gp_list_field_datepicker_js() {
			wp_enqueue_script('gform_datepicker_init');
			add_action('wp_footer', array(&$this,'itsg_gp_list_field_datepicker_js_script'));
		} // END itsg_gp_list_field_datepicker_js
		
		/*
         * JavaScript used by front end - assigns datepicker to fields, unbinds/destorys then re-assigns when field row is repeated
         */
		public static function itsg_gp_list_field_datepicker_js_script() {
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
		} // END itsg_gp_list_field_datepicker_js_script
		
		/*
         * JavaScript used by form editor - Functions taken from Gravity Forms source and extended to handle the 'Date field' option
         */
		public static function itsg_gp_list_field_datepicker_editor_js() {
		?>
		<script type='text/javascript'>
		function GetFieldChoices(field){
			if(field.choices == undefined)
				return "";

			var currency = GetCurrentCurrency();
			var str = "";
			for(var i=0; i<field.choices.length; i++){

				var checked = field.choices[i].isSelected ? "checked" : "";
				var inputType = GetInputType(field);
				var type = inputType == 'checkbox' ? 'checkbox' : 'radio';

				var value = field.enableChoiceValue ? String(field.choices[i].value) : field.choices[i].text;
				var price = field.choices[i].price ? currency.toMoney(field.choices[i].price) : "";
				if(!price)
					price = "";
				var isDatePicker = field.choices[i].isDatePicker ? "checked" : "";
				var isDatePickerFormat = field.choices[i].isDatePickerFormat;
				
				str += "<li data-index='" + i + "'>";
				str += "<i class='fa fa-sort field-choice-handle'></i> ";
				str += "<input type='" + type + "' class='gfield_choice_" + type + "' name='choice_selected' id='" + inputType + "_choice_selected_" + i + "' " + checked + " onclick=\"SetFieldChoice('" + inputType + "', " + i + ");\" /> ";
				str += "<input type='text' id='" + inputType + "_choice_text_" + i + "' value=\"" + field.choices[i].text.replace(/"/g, "&quot;") + "\" onkeyup=\"SetFieldChoice('" + inputType + "', " + i + ");\" onchange='CheckChoiceConditionalLogicDependency(this);' class='field-choice-input field-choice-text' />";
				str += "<input type='text' id='"+ inputType + "_choice_value_" + i + "' value=\"" + value.replace(/"/g, "&quot;") + "\" onkeyup=\"SetFieldChoice('" + inputType + "', " + i + ");\" onchange='CheckChoiceConditionalLogicDependency(this);' class='field-choice-input field-choice-value' />";
				str += "<input type='text' id='"+ inputType + "_choice_price_" + i + "' value=\"" + price.replace(/"/g, "&quot;") + "\" onchange=\"SetFieldChoice('" + inputType + "', " + i + ");\" class='field-choice-input field-choice-price' />";

				if(window["gform_append_field_choice_option_" + field.type])
					str += window["gform_append_field_choice_option_" + field.type](field, i);

				str += gform.applyFilters('gform_append_field_choice_option', '', field, i);

				str += "<a class='gf_insert_field_choice' onclick=\"InsertFieldChoice(" + (i+1) + ");\"><i class='fa fa-plus-square'></i></a>";


				if(field.choices.length > 1 )
					str += "<a class='gf_delete_field_choice' onclick=\"DeleteFieldChoice(" + i + ");\"><i class='fa fa-minus-square'></i></a>";
				
				if (inputType == 'list' ){
				str += "<div style='white-space:nowrap'>";
				 str += "<input type='checkbox' name='choice_datepicker' id='" + inputType + "_choice_datepicker_" + i + "' " + isDatePicker + " onclick=\"SetFieldChoice('" + inputType + "', " + i + ");\" /> ";
				 str += "	<label class='inline' for='"+ inputType + "_choice_datepicker_" + i + "'>Date field</label>";
				 
				str += "<div style='display:none' class='itsg_date_format'>";
				 str += "<label for='" + inputType + "_choice_datepickerformat_" + i + "'>";
				 str += "Date Format</label>";
				 str += "<select class='choice_datepickerformat' id='" + inputType + "_choice_datepickerformat_" + i + "' onclick=\"SetFieldChoice('" + inputType + "', " + i + ");\">";
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
				 
				str += "</li>";

			}
			return str;
		}

		function SetFieldChoice(inputType, index){

			text = jQuery("#" + inputType + "_choice_text_" + index).val();
			value = jQuery("#" + inputType + "_choice_value_" + index).val();
			price = jQuery("#" + inputType + "_choice_price_" + index).val();
			
			var element = jQuery("#" + inputType + "_choice_selected_" + index);
			isSelected = element.is(":checked");
			
			if ('list' == inputType) {
			var element = jQuery("#" + inputType + "_choice_datepicker_" + index);
			isDatePicker = element.is(":checked");
			isDatePickerFormat = jQuery("#" + inputType + "_choice_datepickerformat_" + index).val();
			}
			field = GetSelectedField();

			field.choices[index].text = text;
			field.choices[index].value = field.enableChoiceValue ? value : text;
			if ('list' == inputType) {
			field.choices[index].isDatePickerFormat = isDatePickerFormat;
			}

			if(field.enablePrice){
				var currency = GetCurrentCurrency();
				var price = currency.toMoney(price);
				if(!price)
					price = "";

				field.choices[index]["price"] = price;
				jQuery("#" + inputType + "_choice_price_" + index).val(price);
			}

			//set field selections
			jQuery("#field_choices :radio, #field_choices :checkbox").each(function(index){
				field.choices[index].isSelected = this.checked;
			});
			jQuery("#field_columns :checkbox").each(function(index){
				field.choices[index].isDatePicker = this.checked;
			});

			LoadBulkChoices(field);

			UpdateFieldChoices(GetInputType(field));
		}

		</script>
		
		<script type="text/javascript">
		function itsp_gf_ajax_upload_function(){
		jQuery('#field_columns input[name=choice_datepicker]').each(function() {
			if (jQuery(this).is(":checked")) {
					jQuery(this).parent("div").find(".itsg_date_format").show();
				}
				else {
					jQuery(this).parent("div").find(".itsg_date_format").hide();
				}
		});
		jQuery("#field_columns select.choice_datepickerformat").each(function(index){
				jQuery(this).val(field.choices[index].isDatePickerFormat);
			});

		}
	
		jQuery('ul.gform_fields').on("click",  function(){
			itsp_gf_ajax_upload_function();  
		
		jQuery('#field_columns').on("click", "input[name=choice_datepicker]", function(){
			itsp_gf_ajax_upload_function();  
		});
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