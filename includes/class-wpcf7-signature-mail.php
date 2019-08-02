<?php

/**
 * The mail-specific functionality of the plugin.
 *
 * @link       http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html
 * @since      4.0.0
 *
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/includes
 */

/**
 * The mail-specific functionality of the plugin.
 *
 * @package    Wpcf7_Signature
 * @subpackage Wpcf7_Signature/includes
 * @author     Breizhtorm <tameroski@gmail.com>
 */
class Wpcf7_Signature_Mail {

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
	 * Modifying mail components in order to add signatures as attachments if needed
	 *
	 * @since    4.0.0
	 */
	public function modify_components($components){

		// Which email template is it ?
		if ( $mail = WPCF7_Mail::get_current() ) {

			if ( $submission = WPCF7_Submission::get_instance() ) {

				if ( $contact_form = WPCF7_ContactForm::get_current() ) {

					// Dealing with main Email
					$mail = $contact_form->prop($mail->name());
					$new_attachments = $mail['attachments'];

					$signature_dir = trailingslashit($this->signature_dir());

					// Getting attachments one by one in mail configuration
					$attachments = preg_split('/[\s,\]]+/', $mail['attachments']);

					foreach ($attachments as $attachment) {

						if ($attachment != '') {

							$attachment .= ']';

							preg_match_all("/\[(.*?)\]/", $attachment, $attachment_names);

							foreach ($attachment_names[1] as $attachment_name) {

								$data = $submission->get_posted_data($attachment_name);

								// Is is matching a signature tag ?
								$tags = $contact_form->scan_form_tags();

								foreach ($tags as $tag) {

									if ($tag->name == $attachment_name) {

										if (strpos($tag->type, 'signature') !== false) {

											$filename = explode('wpcf7_signatures/',$data);

											// File exists ?
											if (file_exists($signature_dir.$filename[1])) {

												// Adding file as attachment
												$components['attachments'][] = $signature_dir.$filename[1];
											}
										}
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

	/**
	 * When form data is posted, we save the image somewhere in WP public directory 
	 * and change the posted value to the image URL
	 *
	 * @since    4.0.0
	 */
	public function manage_signature_data ($posted_data) {

		foreach ($posted_data as $key => $data) {
			if (is_string($data) && strrpos($data, "data:image/png;base64", -strlen($data)) !== FALSE){

		        // Do we need to treat it as inline data ?
		        if ($posted_data[$key."-inline"] == 1){

		        	// Sending a base64 encoded inline image
		        	$posted_data[$key] = $data;
		        	return $posted_data;

		        }

		        $data_pieces = explode(",", $data);
		        $encoded_image = $data_pieces[1];
		        $decoded_image = base64_decode($encoded_image);
		        $filename = sanitize_file_name(wpcf7_canonicalize($key."-".time().".png"));

		        $signature_dir = trailingslashit($this->signature_dir());

		        // Do we need to treat it as attachement ?
		        $is_attachment = $posted_data[$key."-attachment"] == 1;

		        if ($is_attachment){

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
		        }else{

		        	// Sending signature asa server image

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

			        	$fileurl = $this->signature_url($filename);
		        		$posted_data[$key] = $fileurl;

			        }else{
			        	error_log("Cannot create signature file : ".$filepath);
			        }

		        }
		        
			}
		}

		return $posted_data;
	}

	private function signature_dir() {
		if ( defined( 'WPCF7_SIGNATURE_DIR' ) )
			return WPCF7_SIGNATURE_DIR;
		else
			return wpcf7_upload_dir( 'dir' ) . '/wpcf7_signatures';
	}

	private function signature_dir_url() {
		if ( defined( 'WPCF7_SIGNATURE_URL' ) )
			return WPCF7_SIGNATURE_URL;
		else
			return wpcf7_upload_dir( 'url' ) . '/wpcf7_signatures';
	}

	private function signature_url( $filename ) {
		$url = trailingslashit( $this->signature_dir_url() ) . $filename;

		if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
			$url = 'https:' . substr( $url, 5 );
		}

		return apply_filters( 'wpcf7_signature_url', esc_url_raw( $url ) );
	}

}
