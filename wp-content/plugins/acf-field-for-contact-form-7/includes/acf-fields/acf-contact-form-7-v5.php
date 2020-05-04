<?php
/**
 * ACF Field V5
 *
 * @package WordPress
 * @author KrishaWeb <support@krishaweb.com>
 */

// If check class exists or not.
if ( ! class_exists( 'ACF_Field_For_Contact_form_7_V5' ) ) {

	/**
	 * Declare class and extends to acf_field.
	 */
	class ACF_Field_For_Contact_form_7_V5 extends acf_field {

		/**
		 * Class construct.
		 *
		 * @param array $settings  The settings.
		 */
		function __construct( $settings ) {
			$this->name = 'acf_cf7';
			$this->label = __( 'Contact form 7', 'acf-field-for-contact-form-7' );
			$this->category = 'basic';
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Render acf fields.
		 *
		 * @param array $field  The field.
		 */
		function render_field( $field ) {
			$contact_forms = WPCF7_ContactForm::find(); ?>
			<select name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>">
				<option value="0"><?php echo __( 'Select form', 'acf-field-for-contact-form-7' ); ?></option>
				<?php
				foreach ( $contact_forms as $form ) {
					?>
					<option value='<?php echo $form->id(); ?>'<?php if ( $field['value'] == $form->id() ) {?> selected<?php } ?>><?php echo $form->title(); ?></option>
					<?php
				}
				?>
			</select>
			<?php
		}

		/**
		 * Loads a value.
		 *
		 * @param object $value    The value.
		 * @param int    $post_id  The post identifier.
		 * @param string $field    The field.
		 *
		 * @return object
		 */
		function load_value( $value, $post_id, $field ) {
			if ( ! is_admin() ) {
				$contact_forms = WPCF7_ContactForm::find();
				$form_id = ! empty( $value ) ? (int) $value : 0;
				foreach ( $contact_forms as $form ) {
					if ( $form->id() === $form_id ) {
						// apply filter.
						$contect_object = apply_filters( 'acf_cf7_object', false );
						// If check filter.
						if ( $contect_object ) {
							return $form;
						} else {
							return do_shortcode( '[contact-form-7 id="' . $form->id() . '" title="' . $form->title() . ']' );
						}
					}
				}
			} else {
				return $value;
			}
		}
	}
	new ACF_Field_For_Contact_form_7_V5( $this->settings );
}
