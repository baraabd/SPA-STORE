<?php
// If check class exists or not.
if ( ! class_exists( 'ACF_Field_For_Contact_form_7_V3' ) ) {
	// Declare class and extends to acf_field.
	class ACF_Field_For_Contact_form_7_V3 extends acf_Field {
		/**
		 * Construct
		 *
		 * @param      object  $parent  The parent
		 */
		function __construct( $parent ) {
			// do not delete!
			parent::__construct( $parent );
			// set name / title
			$this->name = 'acf_cf7';
			$this->title = __( 'Contact Form 7','acf-field-for-contact-form-7' );
		}

		/**
		 * Creates options.
		 *
		 * @param      string  $key    The key
		 * @param      string  $field  The field
		 */
		function create_options( $key, $field ) {
			// defaults
			$field['multiple'] = isset( $field['multiple'] ) ? $field['multiple'] : 0;
			$field['allow_null'] = isset( $field['allow_null'] ) ? $field['allow_null'] : 0;
			$field['disable'] = isset( $field['disable'] ) ? $field['disable'] : '0';
			$field['hide_disabled'] = isset( $field['hide_disabled'] ) ? $field['hide_disabled'] : 0; ?>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Disabled forms:', 'acf-field-for-contact-form-7' ); ?></label>
					<p class="description"><?php _e( 'You can not select these forms', 'acf-field-for-contact-form-7' ); ?></p>
				</td>
				<td>
					<?php 
					//Get form names
					$forms = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'orderby' => 'id', 'order' => 'ASC', 'posts_per_page' => 999, 'numberposts' => 999 ) );  
					$choices = array();
					$choices[0] = '---';
					$k = 1;
					foreach ( $forms as $f ) {
						$choices[$k] = $f->post_title;
						$k++;
					} 
					$this->parent->create_field( array(
						'type'  =>  'select',
						'name'  =>  'fields['.$key.'][disable]',
						'value' =>  $field['disable'],
						'multiple'    =>  '1',
						'allow_null'  =>  '0',
						'choices' =>  $choices,
						'layout'  =>  'horizontal',
						) );
						?>
					</td>
				</tr>
				<tr class="field_option field_option_<?php echo $this->name; ?>">
					<td class="label">
						<label><?php _e( 'Allow Null?', 'acf-field-for-contact-form-7' ); ?></label>
					</td>
					<td>
						<?php 
						$this->parent->create_field( array(
							'type'  =>  'radio',
							'name'  =>  'fields['.$key.'][allow_null]',
							'value' =>  $field['allow_null'],
							'choices' =>  array(
								'1' =>  'Yes',
								'0' =>  'No',
							),
							'layout'  =>  'horizontal',
							) );
							?>
						</td>
					</tr>
					<tr class="field_option field_option_<?php echo $this->name; ?>">
						<td class="label">
							<label><?php _e("Select multiple forms?",'acf-field-for-contact-form-7'); ?></label>
						</td>
						<td>
							<?php 
							$this->parent->create_field( array(
								'type'  =>  'radio',
								'name'  =>  'fields['.$key.'][multiple]',
								'value' =>  $field['multiple'],
								'choices' =>  array(
									'1' =>  'Yes',
									'0' =>  'No',
								),
								'layout'  =>  'horizontal',
								) );
								?>
							</td>
						</tr>
						<tr class="field_option field_option_<?php echo $this->name; ?>">
							<td class="label">
								<label><?php _e( 'Hide disabled forms?', 'acf-field-for-contact-form-7' ); ?></label>
							</td>
							<td>
								<?php 
								$this->parent->create_field( array(
									'type'  =>  'radio',
									'name'  =>  'fields['.$key.'][hide_disabled]',
									'value' =>  $field['hide_disabled'],
									'choices' =>  array(
										'1' =>  'Yes',
										'0' =>  'No',
									),
									'layout'  =>  'horizontal',
									) );
									?>
								</td>
							</tr>
							<?php
						}

		/**
		 * Save field.
		 *
		 * @param      <type>  $field  The field
		 *
		 * @return     <type>  ( description_of_the_return_value )
		 */
		function pre_save_field( $field ) {
			// do stuff with field (mostly format options data)
			return parent::pre_save_field( $field );
		}

		/**
		 * Creates a field.
		 *
		 * @param      string  $field  The field
		 */
		function create_field( $field ) {

			$field['multiple'] = isset( $field['multiple'] ) ? $field['multiple'] : false;
			$field['disable'] = isset( $field['disable'] ) ? $field['disable'] : false;
			$field['hide_disabled'] = isset( $field['hide_disabled'] ) ? $field['hide_disabled'] : false;

			// Add multiple select functionality as required
			$multiple = '';
			if ( $field['multiple'] == '1' ) {
				$multiple = ' multiple="multiple" size="5" ';
				$field['name'] .= '[]';
			}
			// Begin HTML select field
			echo '<select id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';

			// Add null value as required
			if ( $field['allow_null'] == '1' ) {
				echo '<option value="null"> - Select - </option>';
			}

			// Display all contact form 7 forms
			$forms = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'orderby' => 'id', 'order' => 'ASC', 'posts_per_page' => 999, 'numberposts' => 999 ) );       
			if ( $forms ) {  
				foreach ( $forms as $k => $form ) {
					$key = $form->ID;
					$value = $form->post_title; 
					$selected = '';
					// Mark form as selected as required
					if ( is_array( $field['value'] ) ) {
						// If the value is an array (multiple select), loop through values and check if it is selected
						if ( in_array( $key, $field['value'] ) ) {
							$selected = 'selected="selected"';
						}
					} else {
						// If not a multiple select, just check normaly
						if ( $key == $field['value'] ) {
							$selected = 'selected="selected"';
						}
					}
					//Check if field is disabled
					if ( in_array( ( $k+1 ), $field['disable'] ) ) {
						//Show disabled forms?
						if ( $field['hide_disabled'] == 0 ) {
							echo '<option value="' . $key . '" ' . $selected . ' disabled="disabled">' . $value . '</option>';
						}
					} else {
						echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
					}
				} 
			}
			echo '</select>';
		}

		/**
		 * Admin head.
		 */
		function admin_head() {

		}

		/**
		 * Admin script.
		 */
		function admin_print_scripts() {

		}

		/**
		 * Admin styles.
		 */
		function admin_print_styles() {

		}

		/**
		 * Update value
		 *
		 * @param      int  $post_id  The post identifier
		 * @param      string  $field    The field
		 * @param      string  $value    The value
		 */
		function update_value( $post_id, $field, $value ) {
			// do stuff with value
			// save value
			parent::update_value( $post_id, $field, $value );
		}

		/**
		 * Gets the value.
		 *
		 * @param      int  $post_id  The post identifier
		 * @param      string  $field    The field
		 *
		 * @return     string  The value.
		 */
		function get_value( $post_id, $field ) {
			// get value
			$value = parent::get_value( $post_id, $field );

			// return value
			return $value;    
		}

		/**
		 * Gets the value for api.
		 *
		 * @param      int          $post_id  The post identifier
		 * @param      string       $field    The field
		 *
		 * @return     boolean|string  The value for api.
		 */
		function get_value_for_api( $post_id, $field ) {
			// get value
			$value = $this->get_value( $post_id, $field );

			if ( ! $value || $value == 'null' ) {
				return false;
			}
			// apply filter
			$contact_obj = apply_filters( 'acf_cf7_object', false );
			//If there are multiple forms, construct and return an array of form markup
			if ( is_array( $value ) ) {
				foreach ( $value as $k => $v ) {
					$form = get_post($v);
					if ( $contact_obj ) {
						$value = $form;
					} else {
						$f = do_shortcode( '[contact-form-7 id="'.$form->ID.'" title="'.$form->post_title.'"]' );
						$value[$k] = array();
						$value[$k] = $f;
					}
				}
			} else {
				$form = get_post( $value );
				if ( $contact_obj ) {
					$value = $form;
				} else {
					$value = do_shortcode( '[contact-form-7 id="'.$form->ID.'" title="'.$form->post_title.'"]' );
				}
			}
			return $value;
		}
	}
	new ACF_Field_For_Contact_form_7_V3;
}