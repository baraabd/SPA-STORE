=== ACF Field For Contact Form 7 ===
Contributors: dilipbheda, krishaweb
Tags: acf, contact form 7, advanced custom fields, contact form, forms
Requires at least: 4.4
Tested up to: 5.3
Stable tag: 1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a 'Contact Form 7' field type for the Advanced Custom Fields WordPress plugin.

== Description ==

Adds a 'Contact Form 7' field type for the Advanced Custom Fields WordPress plugin.

Store one or multiple contact forms in an advanced custom field.

Mark one or more forms as disabled to prevent them from being selected.

Field is returned as Contact Form 7 markup.

= Compatibility =

This ACF field type is compatible with :
* ACF 3
* ACF 4
* ACF 5

== Installation ==

1. Copy the `acf-field-for-contact-form-7` folder into your `wp-content/plugins` folder.
2. Activate the Advanced Custom Fields: Contact Form 7 Field plugin via the plugins admin page.
3. Create a new field via ACF and select the Contact Form 7 type.

== Frequently Asked Questions ==

= How to use =

This example shows how to get the value of field `field_name` from the current post.

`echo get_field( 'field_name' );`


This example shows how to get form (contact form 7) object.

`function get_acf_cf7_object() {
	return true;
}
add_filter( 'acf_cf7_object', 'get_acf_cf7_object' );`

= I have an idea for a great way to improve this plugin =

Great! I’d love to hear from you at <a href="mailto:support@krishaweb.com">support@krishaweb.com</a>

== Changelog ==

= 1.4 =
* Added: ACF pro option page support.

= 1.3 =
* Fixed: ACF Group field issue.

= 1.2 =
* Tested upto 5.0

= 1.1 =
* Filter added to get form object.

= 1.0 =
* Initial Release.
