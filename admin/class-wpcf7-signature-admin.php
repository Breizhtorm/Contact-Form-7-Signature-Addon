<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html
 * @since      4.0.0
 *
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/admin
 * @author     Breizhtorm <tameroski@gmail.com>
 */
class Wpcf7_Signature_Admin {

	const WPCF7_SIGNATURE_JS_CALLBACK = "$('div.wpcf7 > form').wpcf7ClearSignatures();";

	/**
	 * The ID of this plugin.
	 *
	 * @since    4.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    4.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    4.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Modify contact form properties for signatures
	 *
	 * @since    4.0.0
	 */
	public function contact_form_properties( $properties, $instance ) 
	{
	   	if (! is_array($properties)){
	   		return $properties;
	   	}

	   	// We need to know if the current form has a signature field
	   	$manager = WPCF7_FormTagsManager::get_instance();
	   	$scanned = $manager->scan( $properties['form'] );

	   	if ( empty( $scanned ) )
				return $properties;

		for ( $i = 0, $size = count( $scanned ); $i < $size; $i++ ) {
			if ( !empty( $scanned[$i]) && $scanned[$i]['basetype'] == "signature"){
				// We got one !
				//Let's add the callback if needed
			   	$WPCF7Callback = 'on_sent_ok: "'.$this::WPCF7_SIGNATURE_JS_CALLBACK.'"';
			   	$settings = $properties['additional_settings'];

			   	// No callback found, let's do this !
			    if(!strstr($settings, addslashes($WPCF7Callback)) && !strstr($settings, $WPCF7Callback)){

			    	if (strlen($settings) > 0)
			    		$settings .= "\n";

			    	$settings .= $WPCF7Callback."\n";
			    }

			   	$properties['additional_settings'] = $settings;
			}
		}

	    return $properties;
	}

	/**
	 * Add a tag generator for the signature field type
	 *
	 * @since    4.0.0
	 */
	public function add_tag_generator() {

		if (class_exists('WPCF7_TagGenerator')) {
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add( 'signature', __( 'signature', 'wpcf7-signature' ),array($this,'tag_generator_signature') );
		}
		
	}

	/**
	 * Tag generator form
	 *
	 * @since    4.0.0
	 */
	public function tag_generator_signature( $contact_form, $args = '' ) {

		$args = wp_parse_args( $args, array() );
		$type = 'signature';

		$description = __( "Generate a form-tag for a signature field.", 'contact-form-7' );
		?>
		<div class="control-box">
		<fieldset>
		<legend><?php echo sprintf( esc_html( $description ) ); ?></legend>
		<table class="form-table">
		<tbody>
			<tr>
			<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
			<td>
				<fieldset>
				<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
				<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
				</fieldset>
			</td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
			<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
			<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
			<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-width' ); ?>"><?php echo esc_html( __( 'Width attribute', 'contact-form-7' ) ); ?></label></th>
			<td><input type="number" name="cols" class="widthvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-width' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-height' ); ?>"><?php echo esc_html( __( 'Height attribute', 'contact-form-7' ) ); ?></label></th>
			<td><input type="number" name="rows" class="heightvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-height' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-color' ); ?>"><?php echo esc_html( __( 'Color attribute', 'wpcf7-signature' ) ); ?></label></th>
			<td><input type="text" name="color" class="heightvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-color' ); ?>" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-background' ); ?>"><?php echo esc_html( __( 'Background attribute', 'wpcf7-signature' ) ); ?></label></th>
			<td><input type="text" name="background" class="heightvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-background' ); ?>" /></td>
			</tr>

		</tbody>
		</table>
		</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>

			<br class="clear" />

			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag into an image tag (<img src=\"%s\"/>)in the field on the Mail tab.", 'wpcf7-signature' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
		</div>
		<?php
	}

	/**
	 * Remove old JS callback from properties when upgrading 
	 * to 4.0 for the first time
	 *
	 * @since    4.0.0
	 */
	public function remove_v3_js_callback($new_ver, $old_ver) {

		if ( version_compare( $old_ver, '4.0-dev', '>=' ) ) {
			return;
		}

		// Looping through all forms
		$posts = WPCF7_ContactForm::find( array(
			'post_status' => 'any',
			'posts_per_page' => -1,
		) );

		$oldJSCallback = "sigFieldsClear();";

		foreach ( $posts as $post ) {

			$props = $post->get_properties();
			$newProps = array();
			$needSave = false;

			foreach ( $props as $prop => $value ) {

				if ($prop == 'additional_settings'){
					if(strstr($value, $oldJSCallback)){
						$oldJSCallback = 'on_sent_ok: "'.$oldJSCallback.'"';
						$value = str_replace($oldJSCallback, "", $value);
						$needSave = true;
					}
				}

				$newProps[$prop] = $value;
			}

			if ($needSave){
				$post->set_properties($newProps);
				$post->save();
			}
		}
	}

}
