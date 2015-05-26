<?php
/*
Plugin Name: Contact Form 7 Signature Addon
Plugin URI: 
Description: Add signature field type to the popular Contact Form 7 plugin.
Author: Breizhtorm
Author URI: http://www.breizhtorm.fr
Version: 2.2
*/

// this plugin needs to be initialized AFTER the Contact Form 7 plugin.
add_action('plugins_loaded', 'contact_form_7_signature_fields', 10); 
function contact_form_7_signature_fields() {
	global $pagenow;
	if(!function_exists('wpcf7_add_shortcode')) {
		if($pagenow != 'plugins.php') { return; }
		add_action('admin_notices', 'cfsignaturefieldserror');
		add_action('admin_enqueue_scripts', 'contact_form_7_signature_fields_scripts');

		function cfsignaturefieldserror() {
			$out = '<div class="error" id="messages"><p>';
			if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
				$out .= 'The Contact Form 7 plugin is installed, but <strong>you must activate Contact Form 7</strong> below for the Signature Field plugin to work.';
			} else {
				$out .= 'The Contact Form 7 plugin must be installed for the Tag-it Field plugin to work. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">Install Now.</a>';
			}
			$out .= '</p></div>';
			echo $out;
		}
	}
}

load_plugin_textdomain('wpcf7-signature', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'signature_add_stylesheets' );
function signature_add_stylesheets() {
	wp_register_style( 'signature', plugins_url( 'signature.css' , __FILE__ ) );
	wp_enqueue_style( 'signature' );
}

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_signature' );
function wpcf7_add_shortcode_signature() {
	wpcf7_add_shortcode(
		array( 'signature', 'signature*' ),
		'wpcf7_signature_shortcode_handler', true );
}

function wpcf7_signature_shortcode_handler( $tag ) {

	// loading signature javascript
	wp_enqueue_script('signature-pad',plugins_url( 'signature_pad.min.js' , __FILE__ ),array(),'1.0',false);

	$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-signature' );

	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$atts = array();

	$width = $tag->get_cols_option( '300' );
	$height = $tag->get_rows_option( '200' );

	$atts['class'] = $tag->get_class_option( $class );
	//$atts['id'] = $tag->get_id_option();

	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->has_option( 'readonly' ) )
		$atts['readonly'] = 'readonly';

	if ( $tag->is_required() )
		$atts['aria-required'] = 'true';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	} elseif ( '' === $value ) {
		$value = $tag->get_default_option();
	}

	if ( wpcf7_is_posted() && isset( $_POST[$tag->name] ) )
		$value = wp_unslash( $_POST[$tag->name] );

	$atts['value'] = $value;

	$atts['type'] = 'hidden';

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$sigid = str_replace("-","_",sanitize_html_class( $tag->name ));

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s id="wpcf7_%4$s_input"/>%3$s
		<canvas id="wpcf7_%4$s_signature" class="%4$s" width="%5$s" height="%6$s"></canvas><input id="#wpcf7_%4$s_clear" type="button" value="%7$s"/></span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error, $tag->name, $width, $height, __( 'Clear', 'wpcf7-signature' ) );

	// script needs to be added for each signature field
	$html .= '<script type="text/javascript">';
	$html .= 'document.addEventListener("DOMContentLoaded", function(){';
	$html .= 'var canvas_'.$sigid.' = document.querySelector("#wpcf7_'.$tag->name.'_signature");';
	$html .= 'var signaturePad_'.$sigid.' = new SignaturePad(canvas_'.$sigid.');';
	$html .= 'document.getElementById("#wpcf7_'.$tag->name.'_clear").addEventListener("click", function(){signaturePad_'.$sigid.'.clear();input_'.$sigid.'.value = "";});';
	$html .= 'var input_'.$sigid.' = document.querySelector("#wpcf7_'.$tag->name.'_input");';
	$html .= 'var submit = document.querySelector("input.wpcf7-submit");';
	$html .= 'submit.addEventListener("click", function(){if (!signaturePad_'.$sigid.'.isEmpty()){input_'.$sigid.'.value = signaturePad_'.$sigid.'.toDataURL();}else{input_'.$sigid.'.value = "";}}, false)';
	$html .= '});';
	$html .= '</script>';

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_signature', 'wpcf7_signature_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_signature*', 'wpcf7_signature_validation_filter', 10, 2 );

function wpcf7_signature_validation_filter( $result, $tag ) {

	$tag = new WPCF7_Shortcode( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] )
		? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
		: '';

	if ( 'signature*' == $tag->type ) {
		if ( '' == $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
	}

	return $result;
}

/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_signature', 15 );

function wpcf7_add_tag_generator_signature() {
	if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
		return;

	wpcf7_add_tag_generator( 'signature', __( 'Signature', 'wpcf7-signature' ),
		'wpcf7-tg-pane-signature', 'wpcf7_tg_pane_signature' );
}

function wpcf7_tg_pane_signature( ) {

?>
<div id="wpcf7-tg-pane-signature" class="hidden">
<form action="">
<table>
<tr><td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'contact-form-7' ) ); ?></td></tr>
<tr><td><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
</table>

<table>
<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>

<td><code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="class" class="classvalue oneline option" /></td>
</tr>

<tr>
<td><code>width</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="cols" class="numeric oneline option" min="1" /></td>

<td><code>height</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="rows" class="numeric oneline option" min="1" /></td>
</tr>

</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="signature" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>

<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'contact-form-7' ) ); ?><br /><input type="text" class="mail-tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>
</form>
</div>
<?php
}

/**
* When form data is posted, we save the image somewhere in WP public directory
* and change the posted value to the image URL
*/
function wpcf7_manage_signature ($posted_data) {

	$dir = "/signatures";

	foreach ($posted_data as $key => $data) {
		if (is_string($data) && strrpos($data, "data:image/png;base64", -strlen($data)) !== FALSE){
	        $data_pieces = explode(",", $data);
	        $encoded_image = $data_pieces[1];
	        $decoded_image = base64_decode($encoded_image);

	        $upload_dir = wp_upload_dir();
	        $signature_dir = $upload_dir['basedir'].$dir;
	        $signature_dir_url = $upload_dir['baseurl'].$dir;

	        if( ! file_exists( $signature_dir ) ){
	    		wp_mkdir_p( $signature_dir );
	        }

	        $filename = $key."-".time().".png";
	        $filepath = $signature_dir."/".$filename;

	        file_put_contents( $filepath,$decoded_image);

	        if (file_exists($filepath)){
	        	// File created : changing posted data to the URL instead of base64 encoded image data
	        	$fileurl = $signature_dir_url."/".$filename;
	        	
        		$posted_data[$key] = $fileurl;
	        }else{
	        	error_log("Cannot create signature file in directory ".$filepath);
	        }
		}
	}

	return $posted_data;
}
add_filter( 'wpcf7_posted_data', 'wpcf7_manage_signature' );

?>