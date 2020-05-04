<?php
// If check class exists or not.
if ( ! class_exists( 'ACF_Field_For_Contact_form_7_V4' ) ) {
	// Declare class and extends to acf_field.
	class ACF_Field_For_Contact_form_7_V4 extends acf_field {
		/**
		 * __construct
		 */
		function __construct() {
			// vars
	  	$this->name = 'acf_cf7';
	  	$this->label = __( 'Contact Form 7', 'acf-field-for-contact-form-7' );
	    // do not delete!
	  	parent::__construct();
	  }

	  /**
	   * Loads a value.
	   *
	   * @param      string  $value    The value
	   * @param      int  $post_id  The post identifier
	   * @param      string  $field    The field
	   *
	   * @return     string  ( return field value )
	   */
	  function load_value( $value, $post_id, $field ) {
	  	return $value;
	  }

	  /**
	   * Update value.
	   *
	   * @param      string  $value    The value
	   * @param      string  $field    The field
	   * @param      int  $post_id  The post identifier
	   *
	   * @return     string  ( return field value )
	   */
	  function update_value( $value, $field, $post_id ) {
	  	return $value;
	  }

	  /**
	   * Format value.
	   *
	   * @param      string  $value  The value
	   * @param      string  $field  The field
	   *
	   * @return     string  ( return field value )
	   */
	  function format_value( $value, $field ) {
	  	return $value;
	  }

	  /**
	   * Format value for  API.
	   *
	   * @param      string          $value  The value
	   * @param      string          $field  The field
	   *
	   * @return     boolean|string
	   */
	  function format_value_for_api( $value, $field ) {
	  	
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

	  /**
	   * Loads a field.
	   *
	   * @param      string  $field  The field
	   *
	   * @return     string  ( return field )
	   */
	  function load_field( $field ) {
	  	return $field;
	  }

	  /**
	   * Update field.
	   *
	   * @param      string  $field    The field
	   * @param      int  $post_id  The post identifier
	   *
	   * @return     string  ( return field )
	   */
	  function update_field( $field, $post_id ) {
	  	return $field;
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
	   * Creates options.
	   *
	   * @param      string  $field  The field
	   */
	  function create_options( $field ) {
	    // vars
	  	$defaults = array(
	  		'multiple'    =>  0,
	  		'allow_null'  =>  0,
	  		'default_value' => '',
	  		'choices'   =>  '',
	  		'disable'   => '',
	  		'hide_disabled' => 0,
	  	);

	  	$field = array_merge( $defaults, $field );
	  	$key = $field['name'];
	  	?>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	  		<td class="label">
	  			<label><?php _e( 'Disabled Forms:' ,'acf-field-for-contact-form-7'); ?></label>
	  			<p class="description"><?php _e( 'You will not be able to select these forms','acf-field-for-contact-form-7' ); ?></p>
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
	  			do_action( 'acf/create_field', array(
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
	  			do_action( 'acf/create_field', array(
	  				'type'  =>  'radio',
	  				'name'  =>  'fields['.$key.'][allow_null]',
	  				'value' =>  $field['allow_null'],
	  				'choices' =>  array(
	  					1 =>  __("Yes",'acf-field-for-contact-form-7'),
	  					0 =>  __("No",'acf-field-for-contact-form-7'),
	  				),
	  				'layout'  => 'horizontal',
	  			) );
	  			?>
	  		</td>
	  	</tr>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	  		<td class="label">
	  			<label><?php _e( 'Select Multiple?' ,'acf-field-for-contact-form-7' ); ?></label>
	  		</td>
	  		<td>
	  			<?php 
	  			do_action( 'acf/create_field', array(
	  				'type'  =>  'radio',
	  				'name'  =>  'fields['.$key.'][multiple]',
	  				'value' =>  $field['multiple'],
	  				'choices' =>  array(
	  					1 =>  __( 'Yes', 'acf-field-for-contact-form-7' ),
	  					0 =>  __( 'No', 'acf-field-for-contact-form-7' ),
	  				),
	  				'layout'  => 'horizontal',
	  			));
	  			?>
	  		</td>
	  	</tr>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	  		<td class="label">
	  			<label><?php _e( 'Hide disabled forms?','acf-field-for-contact-form-7' ); ?></label>
	  		</td>
	  		<td>
	  			<?php 
	  			do_action( 'acf/create_field', array(
	  				'type'  =>  'radio',
	  				'name'  =>  'fields['.$key.'][hide_disabled]',
	  				'value' =>  $field['hide_disabled'],
	  				'choices' =>  array(
	  					1 =>  __( 'Yes','acf-field-for-contact-form-7'),
	  					0 =>  __( 'No','acf-field-for-contact-form-7'),
	  				),
	  				'layout'  =>  'horizontal',
	  			));
	  			?>
	  		</td>
	  	</tr>
	  	<?php
	  }
	  /**
	   * Enqueue script.
	   */
	  function input_admin_enqueue_scripts() {

	  }
	  /**
	   * Admin head.
	   */
	  function input_admin_head() {

	  }

	  /**
	   * Admin enqueue scripts.
	   */
	  function field_group_admin_enqueue_scripts() {

	  }

	  /**
	   * Admin head.
	   */
	  function field_group_admin_head() {

	  }
	}
	new ACF_Field_For_Contact_form_7_V4();
}
