<?php
/*
Plugin Name: Contact Form 7 Signature Addon
Plugin URI: 
Description: Add signature field type to the popular Contact Form 7 plugin.
Author: Breizhtorm
Author URI: http://www.breizhtorm.fr
Version: 3.1
Text Domain: wpcf7-signature
Domain Path: /languages
*/

define('WPCF7SIG_VERSION',"3.1");

// this plugin needs to be initialized AFTER the Contact Form 7 plugin.
add_action('plugins_loaded', 'contact_form_7_signature_fields', 10); 
function contact_form_7_signature_fields() {
	global $pagenow;
	if(!function_exists('wpcf7_add_shortcode')) {
		if($pagenow != 'plugins.php') { return; }

		add_action('admin_notices', 'cfsignaturefieldserror');

		function cfsignaturefieldserror() {
			$out = '<div class="error" id="messages"><p>';
			if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
				$out .= 'The Contact Form 7 plugin is installed, but <strong>you must activate Contact Form 7</strong> below for the Signature Field plugin to work.';
			} else {
				$out .= 'The Contact Form 7 plugin must be installed for the Signature Field plugin to work. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">Install Now.</a>';
			}
			$out .= '</p></div>';
			echo $out;
		}
	}else{
		add_action('wp_enqueue_scripts', 'wpcf7_signature_assets_handler' );
	}
}

add_action( 'plugins_loaded', 'wpcf7_signature_load_plugin_textdomain' );
function wpcf7_signature_load_plugin_textdomain() {
	load_plugin_textdomain('wpcf7-signature', false, basename( dirname( __FILE__ ) ). '/languages' );
}

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_signature' );
function wpcf7_add_shortcode_signature() {
	wpcf7_add_shortcode(
		array( 'signature', 'signature*' ),
		'wpcf7_signature_shortcode_handler', true );
}

function wpcf7_signature_assets_handler() {
	// loading signature stylesheets, if required
	wp_enqueue_style( 'wpcf7-signature-styles', plugins_url( 'signature.css' , __FILE__ ), array(), WPCF7SIG_VERSION, 'all' );

	// loading signature javascript, if required
	wp_enqueue_script('wpcf7-signature-pad',plugins_url( 'signature_pad.min.js' , __FILE__ ),array(),WPCF7SIG_VERSION,true);
	wp_enqueue_script('wpcf7-signature-scripts',plugins_url( 'scripts.js' , __FILE__ ),array(),WPCF7SIG_VERSION,true);
}

/* TODO
add_action( 'admin_enqueue_scripts', 'wpcf7_signature_admin_enqueue_scripts' );
function wpcf7_signature_admin_enqueue_scripts( ) {

	// Loading admin js
	wp_enqueue_script( 'wpcf7-signature-admin-taggenerator',
		plugins_url( 'tag-generator.js' , __FILE__ ),
		array( 'jquery', 'wpcf7-admin', 'wpcf7-admin-taggenerator' ), WPCF7SIG_VERSION, true );
}
*/

function wpcf7_signature_shortcode_handler( $tag ) {

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

	/* Input attributes */

	$atts['value'] = $value;
	$atts['type'] = 'hidden';
	$atts['name'] = $tag->name;
	$atts = wpcf7_format_atts( $atts );

	/* Canvas attributes */

	// Pen color
	$atts_canvas['data-color'] = $tag->get_option( 'color', '#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})', true );
	
	// Background color
	$atts_canvas['data-background'] = $tag->get_option( 'background', '#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})', true );

	$atts_canvas['id'] = ($tag->get_id_option() != '' ? $tag->get_id_option() : "wpcf7_".$tag->name."_signature");

	$canvas_class = $tag->name;
	$atts_canvas['class'] = $tag->get_class_option( $canvas_class );

	$atts_canvas = wpcf7_format_atts( $atts_canvas );

	/* Attachment attributes */

	$atts_attach['value'] = $tag->has_option( 'attachment' );
	$atts_attach['type'] = 'hidden';
	$atts_attach['name'] = $tag->name . "-attachment";
	$atts_attach = wpcf7_format_atts( $atts_attach );

	$html = sprintf(
		'<div class="wpcf7-form-control-signature-global-wrap" data-field-id="%1$s">
			<div class="wpcf7-form-control-signature-wrap" style="width:%5$spx;height:%6$spx;">
				<div class="wpcf7-form-control-signature-body">
					<canvas %8$s></canvas>
				</div>
			</div>
			<div class="wpcf7-form-control-clear-wrap">
				<input id="wpcf7_%4$s_clear" type="button" value="%7$s"/>
			</div>
		</div>
		<span class="wpcf7-form-control-wrap wpcf7-form-control-signature-input-wrap %1$s">
			<input %2$s id="wpcf7_input_%1$s"/><input %9$s id="wpcf7_input_%1$s_attachment"/>%3$s
		</span>
		',
		sanitize_html_class( $tag->name ), $atts, $validation_error, $tag->name, $width, $height, __( 'Clear', 'wpcf7-signature' ), $atts_canvas, $atts_attach );

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
			if (method_exists($result,"invalidate")){
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				return $result;
			}else{
				$result['valid'] = false;
				$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
			}
		}
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	return $result;
}

/*
* Modifying form properties 
* Adding a Javascript callback to form validation, so we can clear the signature fields 
*/
function filter_wpcf7_contact_form_properties( $properties, $instance ) 
{
   	if (! is_array($properties)){
   		return $properties;
   	}

   	// We need to know if the current form has a signature field
   	$manager = WPCF7_ShortcodeManager::get_instance();
   	$scanned = $manager->scan_shortcode( $properties['form'] );

   	if ( empty( $scanned ) )
			return $properties;

	for ( $i = 0, $size = count( $scanned ); $i < $size; $i++ ) {
		if ( !empty( $scanned[$i]) && $scanned[$i]['basetype'] == "signature"){
			// We got one !
			//Let's add the callback if needed
		   	$JSCallback = 'sigFieldsClear();';
		   	$WPCF7Callback = 'on_sent_ok: "'.$JSCallback.'"';
		   	$settings = $properties['additional_settings'];

		   	// first we need to get rid of the old callback if present
		   	if (strpos($settings, $JSCallback) === 0){
		   		$settings = substr($settings, strlen($JSCallback));
		   	}

		   	// and add the new one
		    if(!strstr($settings, addslashes($WPCF7Callback)) && !strstr($settings, $WPCF7Callback)){

		    	if (strlen($settings) > 0)
		    		$settings .= "\n";

		    	$settings .= $WPCF7Callback."\n";
		    }

		   	$properties['additional_settings'] = $settings;
		}
	}

    return $properties;
};
add_filter( 'wpcf7_contact_form_properties', 'filter_wpcf7_contact_form_properties', 10, 2 );

/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_signature', 60 );

function wpcf7_add_tag_generator_signature() {

	if (class_exists('WPCF7_TagGenerator')) {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'signature', __( 'signature', 'wpcf7-signature' ),'wpcf7_tag_generator_signature' );
	} else if (function_exists('wpcf7_add_tag_generator')) {
		wpcf7_add_tag_generator( 'signature', __( 'Signature', 'wpcf7-signature' ), 'wpcf7-tg-pane-signature', 'wpcf7_tag_generator_signature' );
	}
	
}

function wpcf7_tag_generator_signature( $contact_form, $args = '' ) {

	if (class_exists('WPCF7_TagGenerator')) {
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

			<?php /* TODO
			<tr>
			<th scope="row"><?php echo esc_html( __( 'Send as attachment ?', 'wpcf7-signature' ) ); ?></th>
			<td>
				<fieldset>
				<legend class="screen-reader-text"><?php echo esc_html( __( 'Send as attachment ?', 'wpcf7-signature' ) ); ?></legend>
				<label><input type="checkbox" name="attachment" /></label>
				</fieldset>
			</td>
			</tr>
			*/ ?>

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

			<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
		</div>
		<?php
	}else{

		// For older CF7 versions
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
}

/**
* Modifying mail components
* Adding signatures as attachments if needed
*/
function wpcf7_signature_mail_components($components){

	// Which email template is it ?
	if ( $mail = WPCF7_Mail::get_current() ) {

		if ( $submission = WPCF7_Submission::get_instance() ) {

			if ( $contact_form = WPCF7_ContactForm::get_current() ) {

				// Dealing with main Email
				$mail = $contact_form->prop($mail->name());
				$new_attachments = $mail['attachments'];

				// Getting attachments one by one in mail configuration
				$attachments = preg_split("/\\r\\n|\\r|\\n/", $mail['attachments']);

				foreach ($attachments as $attachment) {

					preg_match_all("/\[(.*?)\]/", $attachment, $attachment_names);

					foreach ($attachment_names[1] as $attachment_name) {
						$data = $submission->get_posted_data($attachment_name);

						// Is is matching a signature tag ?
						$tags = $contact_form->form_scan_shortcode();
						foreach ($tags as $tag) {
							if (("signature" == $tag['type'] || "signature*" == $tag['type'])  && $tag['name'] == $attachment_name){
								
								// File exists ?
								if (@file_exists($data)){

									// Adding file as attachment
									$components['attachments'][] = $data;
								}
							}
						}
					}
					
				}
			}
		}
	}

	return $components;
}
add_filter( 'wpcf7_mail_components', 'wpcf7_signature_mail_components' );

/**
* When form data is posted, we save the image somewhere in WP public directory
* and change the posted value to the image URL
*/
function wpcf7_manage_signature ($posted_data) {

	foreach ($posted_data as $key => $data) {
		if (is_string($data) && strrpos($data, "data:image/png;base64", -strlen($data)) !== FALSE){
	        $data_pieces = explode(",", $data);
	        $encoded_image = $data_pieces[1];
	        $decoded_image = base64_decode($encoded_image);
	        $filename = sanitize_file_name(wpcf7_canonicalize($key."-".time().".png"));

	        // Do we need to treat it as attachement ?
	        $is_attachment = $posted_data[$key."-attachment"] == 1;

	        $signature_dir = trailingslashit(wpcf7_signature_dir());

	        if (!$is_attachment){

	        	// Sending signature image inline (default)

		        if( !file_exists( $signature_dir ) ){ // Creating directory and htaccess file
		    		if (wp_mkdir_p( $signature_dir )){
		    			$htaccess_file = $signature_dir . '.htaccess';

						if ( !file_exists( $htaccess_file ) && $handle = @fopen( $htaccess_file, 'w' ) ) {
							fwrite( $handle, 'Order deny,allow' . "\n" );
							fwrite( $handle, 'Deny from all' . "\n" );
							fwrite( $handle, '<Files ~ "^[0-9A-Za-z_-]+\\.(png)$">' . "\n" );
							fwrite( $handle, '    Allow from all' . "\n" );
							fwrite( $handle, '</Files>' . "\n" );
							fclose( $handle );
						}
		    		}
		        }

		        $filepath = wp_normalize_path( $signature_dir . $filename );

		       	// Writing signature
		        if ( $handle = @fopen( $filepath, 'w' ) ) {
					fwrite( $handle, $decoded_image );
					fclose( $handle );
		        	@chmod( $filepath, 0644 );
				}

		        if (file_exists($filepath)){

		        	$fileurl = wpcf7_signature_url($filename);
	        		$posted_data[$key] = $fileurl;

		        }else{
		        	error_log("Cannot create signature file : ".$filepath);
		        }

	        }else{

	        	// Preparing to send signature as attachement

	        	wpcf7_init_uploads(); // Confirm upload dir
				$uploads_dir = wpcf7_upload_tmp_dir();
				$uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );
				$filename = wp_unique_filename( $uploads_dir, $filename );

				$filepath = trailingslashit( $uploads_dir ) . $filename;

		       	// Writing signature
		        if ( $handle = @fopen( $filepath, 'w' ) ) {
					fwrite( $handle, $decoded_image );
					fclose( $handle );
		        	@chmod( $filepath, 0400 ); // Make sure the uploaded file is only readable for the owner process
				}

				if (file_exists($filepath)){

	        		$posted_data[$key] = $filepath;

		        }else{
		        	error_log("Cannot create signature file as attachment : ".$filepath);
		        }
	        }
	        
		}
	}

	//error_log(print_r($posted_data, true));

	return $posted_data;
}
add_filter( 'wpcf7_posted_data', 'wpcf7_manage_signature' );

function wpcf7_signature_dir() {
	if ( defined( 'WPCF7_SIGNATURE_DIR' ) )
		return WPCF7_SIGNATURE_DIR;
	else
		return wpcf7_upload_dir( 'dir' ) . '/wpcf7_signatures';
}
function wpcf7_signature_dir_url() {
	if ( defined( 'WPCF7_SIGNATURE_URL' ) )
		return WPCF7_SIGNATURE_URL;
	else
		return wpcf7_upload_dir( 'url' ) . '/wpcf7_signatures';
}
function wpcf7_signature_url( $filename ) {
	$url = trailingslashit( wpcf7_signature_dir_url() ) . $filename;

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return apply_filters( 'wpcf7_signature_url', esc_url_raw( $url ) );
}

?>