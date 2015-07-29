<?php
/*
Plugin Name: Date Picker in List Fields for Gravity Forms
Description: Gives the option of adding a date picker to a list field column
Version: 1.3
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com 
License: GPL2
Text Domain: itsg_list_field_datepicker
*/

load_plugin_textdomain( 'itsg_list_field_datepicker', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

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
			// register plugin functions through 'plugins_loaded' - 
			// this delays the registration until all plugins have been loaded, ensuring it does not run before Gravity Forms is available.
            add_action( 'plugins_loaded', array(&$this,'register_actions') );
		}
		
		/*
         * Register plugin functions
         */
		function register_actions() {
            if ((self::is_gravityforms_installed())) {
				// start the plugin
				add_filter('gform_column_input_content', array(&$this,'change_column_content'), 10, 6);
				add_action('gform_enqueue_scripts', array(&$this,'datepicker_js'), 90, 2);
				add_action('gform_editor_js', array(&$this,'editor_js'));
				
				add_action('gform_field_appearance_settings', array(&$this,'field_datepicker_settings') , 10, 2 );
				add_filter('gform_tooltips', array(&$this,'field_datepicker_tooltip'));
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
					if (isset( $field["itsg_list_field_datepicker"])  && "on" == $field["itsg_list_field_datepicker"] && isset( $field["itsg_list_field_datepicker_format"])) {
						$new_input = str_replace("<input ","<input class='datepicker ".$field["itsg_list_field_datepicker_format"]." ' ",$input);
						return $new_input;
					}
					return $input;
				}
			}
		} // itsg_gp_list_field_datepicker_change_column_content

		/*
         * Enqueue JavaScript to footer
         */
		public function datepicker_js($form, $is_ajax) {
			if ( self::list_has_datepicker_field( $form ) ) {
				wp_enqueue_script('gform_datepicker_init');
				add_action('wp_footer', array(&$this,'datepicker_js_script'));
				
				// load Gravity Forms datepicker CSS styles 
				if ( ! get_option( 'rg_gforms_disable_css' ) ) {
					if ( ! wp_style_is( 'gforms_css' ) ) {
						$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
							
						wp_enqueue_style( 'gforms_datepicker_css', GFCommon::get_base_url() . "/css/datepicker{$min}.css", null, GFCommon::$version );
						wp_print_styles( array( 'gforms_datepicker_css' ) );
						
						// patch to make datepicker column use full width - overrides Gravity Forms formsmain.min.css style
						add_action('wp_footer', array(&$this,'list_field_datepicker_css_override'));
					}
				}
			}
		} // END itsg_gp_list_field_datepicker_js
		
		/*
         * JavaScript used by front end - assigns datepicker to fields, unbinds/destorys then re-assigns when field row is repeated
         */
		public static function datepicker_js_script() {
		?>
		<script>
		function itsg_gf_ajax_datepicker_function(self){
			// run for each existing list row
			jQuery('.gfield_list').each(function() {
				jQuery(this).find('.datepicker').removeClass('hasDatepicker').removeAttr('id');
				jQuery(this).find('.datepicker').unbind('.datepicker').datepicker();
				jQuery(this).find('.datepicker').datepicker('destroy');
				<?php
				if(has_action('itsg_default_datepicker_date')) {
					?> jQuery(".datepicker").datepicker("setDate","<?php do_action('itsg_default_datepicker_date'); ?> "); 
				<?php
				}
				?>
			});						
			
			gformInitDatepicker();
			
		}		
		jQuery(function(){
		
		// bind the datepicker function to the 'add list item' button click event
		jQuery('.gfield_list').on("click", ".add_list_item", function(){
			itsg_gf_ajax_datepicker_function(jQuery(this));  			
		});
		
		// runs the main function when the page loads
		jQuery(document).bind('gform_post_render', function($) {itsg_gf_ajax_datepicker_function(jQuery(this));  });
		// runs the main function when the page loads (for ajax enabled forms)
		jQuery(document).ready(function($) {itsg_gf_ajax_datepicker_function(jQuery(this));  });
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
			// handles displaying the date format option for multi column lists
			jQuery('#field_columns input[name=choice_datepicker]').each(function() {
				if (jQuery(this).is(":checked")) {
						jQuery(this).parent("div").find(".itsg_datepicker").show();
					}
					else {
						jQuery(this).parent("div").find(".itsg_datepicker").hide();
					}
			});
			
			// handles displaying the date format option for single column lists
			jQuery('.ui-tabs-panel input#itsg_list_field_datepicker').each(function() {
				if (jQuery(this).is(":checked")) {
						jQuery(this).parent("li").find("#itsg_list_field_datepicker_format_div").show();
					}
					else {
						jQuery(this).parent("li").find("#itsg_list_field_datepicker_format_div").hide();
					}
			});

			// only display this option if a single column list field
			jQuery('#field_settings input[id=field_columns_enabled]').each(function() {
				if (jQuery(this).is(":checked")) {
						jQuery(this).closest("#field_settings").find(".itsg_list_field_datepicker").hide();
						jQuery("#field_columns:visible select.choice_datepickerformat").each(function(index){
							jQuery(this).val(field.choices[index].isDatePickerFormat);
						});
					}
					else {
						jQuery(this).closest("#field_settings").find(".itsg_list_field_datepicker").show();
					}
			});
		}
		
		// trigger for when field is opened
		jQuery(document).on('click', 'ul.gform_fields', function(){
			itsg_gf_list_datepicker_function();  
		});
		
		// trigger when 'Enable multiple columns' is ticked
		jQuery(document).on('change', '#field_settings input[id=field_columns_enabled], .ui-tabs-panel input#itsg_list_field_datepicker', function(){
			itsg_gf_list_datepicker_function();  
		});
		
		// trigger for when column titles are updated
		jQuery(document).on('change','#gfield_settings_columns_container #field_columns li',function() {
			InsertFieldChoice(0);
			DeleteFieldChoice(0);
			itsg_gf_list_datepicker_function();
		});
		
		// handle 'Enable datepicker' option in the Gravity forms editor
		jQuery(document).ready(function($) {
				//adding setting to fields of type "list"
				fieldSettings["list"] += ", .itsg_list_field_datepicker";
				//set field values when field loads		
				jQuery(document).bind("gform_load_field_settings", function(event, field, form){
					jQuery("#itsg_list_field_datepicker").prop('checked', field["itsg_list_field_datepicker"] );
				});
			});
			
		// handle 'Enable datepicker format' option in the Gravity forms editor
		jQuery(document).ready(function($) {
				//adding setting to fields of type "list"
				fieldSettings["list"] += ", .itsg_list_field_datepicker_format";
				//set field values when field loads		
				jQuery(document).bind("gform_load_field_settings", function(event, field, form){
					jQuery("#itsg_list_field_datepicker_format").val(field["itsg_list_field_datepicker_format"]);
				});
			});
		</script>	
		<?php
		} // END itsg_gp_list_field_datepicker_editor_js
		
		/*
          * Adds custom sortable setting for field
          */
        public static function field_datepicker_settings($position, $form_id)
        {      
            // Create settings on position 50 (top position)
            if ($position == 50) {
				?>
				<li class="itsg_list_field_datepicker field_setting">
					<input type="checkbox" id="itsg_list_field_datepicker" onclick="SetFieldProperty('itsg_list_field_datepicker', this.checked);">
					<label class="inline" for="itsg_list_field_datepicker">
					<?php _e("Enable datepicker", "itsg_list_field_datepicker"); ?>
					<?php gform_tooltip("itsg_list_field_datepicker");?>
					</label>
					<div id="itsg_list_field_datepicker_format_div">
						<label for="itsg_list_field_datepicker_format" style="display: inline; margin-right: 10px; font-weight: 800;">Date Format:</label>
						<select onchange="SetFieldProperty('itsg_list_field_datepicker_format', this.value);" id="itsg_list_field_datepicker_format" class="itsg_list_field_datepicker_format">
							<option value="mdy">mm/dd/yyyy</option>
							<option value="dmy">dd/mm/yyyy</option>
							<option value="dmy_dash">dd-mm-yyyy</option>
							<option value="dmy_dot">dd.mm.yyyy</option>
							<option value="ymd_slash">yyyy/mm/dd</option>
							<option value="ymd_dash">yyyy-mm-dd</option>
							<option value="ymd_dot">yyyy.mm.dd</option>
						</select>
					</div>
				</li>
			<?php
            }
        } // END field_datepicker_settings
		
		/*
         * Tooltip for for datepicker option
         */
		public static function field_datepicker_tooltip($tooltips){
			$tooltips["itsg_list_field_datepicker"] = "<h6>Datepicker</h6>Makes list field column a datepicker. Only applies to single column list fields.";
			return $tooltips;
		} // END field_datepicker_tooltip
		
		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed()
        {
            return class_exists('GFAPI');
        } // END is_gravityforms_installed
		
		/*
         * Check if list field has a date picker in the current form
         */
		private static function list_has_datepicker_field($form ) {
			if (is_array($form['fields'])) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field['type']  ) {
						$has_columns = is_array( $field->choices );
						if ($has_columns) {
							foreach($field['choices'] as $choice){
								if (isset( $choice["isDatePicker"] ) && true  == $choice["isDatePicker"])  {
									return true;
								}
							}
						} else if ( isset( $field["itsg_list_field_datepicker"] ) && true == $field['itsg_list_field_datepicker'] ) {
							return true;
						}
					}
				}
			}
		return false;
		} // END list_has_datepicker_field
		
		/*
         * CSS styles places in footer to override Gravity Forms CSS. Allows date picker fields to use full column width.
         */
        public function list_field_datepicker_css_override() {
			?>
			<style>
			.gform_wrapper .gfield_list td.gfield_list_cell input.datepicker {
				width: 97.5% !important;
			}
			</style>
			<?php
		} // END list_field_datepicker_css_override
		
	}
    $ITSG_GF_List_Field_Date_Picker = new ITSG_GF_List_Field_Date_Picker();
}