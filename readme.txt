=== Contact Form 7 Signature Addon ===
Plugin URI: http://www.breizhtorm.fr/blog/
Contributors: tameroski
Tags: Contact Form 7, form, forms, contactform7, contact form, signature pad, sig, signature field, cf7, handwriting, write
Requires at least: 3.9
Tested up to: 4.3
Stable tag: 2.5

Easily add an handwritten signature field to Contact Form 7

== Description ==

### Add a signature field to Contact Form 7

This plugin adds a new field type to the Contact Form 7 plugin and allows users to add an handwritten signature to the message via a signature pad. The plugin uses Szymon Nowak's great Javascript library (https://github.com/szimek/signature_pad).

= Important Note =
This plugin requires version 3.5 or higher of the "Contact Form 7" plugin.
It works on almost every modern web browser (IE9+, ...)

= Installation / Support =
Please read the [installation notes](http://wordpress.org/plugins/contact-form-7-signature-addon/installation/) for details.

You can ask for support [here](http://wordpress.org/support/plugin/contact-form-7-signature-addon), and if you're new to web development and Wordpress things, i think you should have a look at [this article](http://www.wpbeginner.com/beginners-guide/how-to-properly-ask-for-wordpress-support-and-get-it/) first.

= Be kind =
This plugin is just sharing something i needed for a project, there's nothing commercial in there. But i'll listen to your requests and do my best to keep the plugin up to date anyway.

Don't forget to rate the plugin if you like it (or not).

== Installation ==

This plugin requires the Contact Form 7 plugin.

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin feature
2. Activate the plugin
3. Edit a form in Contact Form 7
4. Choose "Signature field" from the Generate Tag dropdown
5. Follow the instructions on the page

If you want the signature image to be included in the body of your emails, just put an image tag which src attribute is set to be the content of your field, just like this :
`<img src="[your-signature-field]"/>`

Your contact form needs to send **HTML formatted** messages for it to work properly, so don't forget to check the corresponding box at the bottom of your email settings.

**Important note** : since version 2.0, the plugin no longer uses inline base64 encoded images, and signature images are saved to your wordpress upload directory. So every email clients compatibility issues should be gone !

== Screenshots ==

1. Signature field configuration
2. Signature field rendered in contact form

== Frequently Asked Questions ==

= I don't see the signature image in the mail. What can i do ? =

First things first : verify that your email is sent in HTML format. In CF7, theres's a checkbox at the bottom of each mail configuration to do that.

Then verify that the image is wrapped in an HTML image tag in your mail configuration, like this :
`<img src="[your-signature-field]"/>`

= How do i add styles to my signature field ? =

Use CSS like you would do for any other field in your form. 
For example, using the field wrapper, you can add a border like this : 
`
.wpcf7-form-control-signature-wrap canvas{
	border: 1px dotted #BADA55;
}
`

= How do i make my signature field responsive ? =

It depends on your form layout but once again, you can do this by using basic CSS instructions. The plugin will deal with window size and device orientation changes by automatically updating itself to match the right size (and clearing the field content). 
`
@media screen and (max-width: 768px) {
    .wpcf7-form-control-signature-wrap canvas{
        width:100%;
        height:100%;
    }
}
...
`

== Changelog ==

= 2.5 =
* Updated signature pad library to v1.4.0
* Fixed a bug with device ratio not being properly taken into account sometimes
* Fixed a bug where signature fields were not cleared after successful submit
* Fixed a bug where cleared signature fields were not correctly validating
* Refactored JavaScript for easier plugin maintenance

= 2.4.1 =
* Fixed a major issue where mandatory signature fields were note validating correctly (since CF7 4.1)

= 2.4 =
* Fixed CF7 older versions compatibility issues (down to at least CF7 v3.5)
* Removed useless plugin own css
* New CSS class added around the signature field for easier styling
* Updated screenshots, FAQ and Readme

= 2.3 =
* Fixed the plugin to match the new CF7 4.2 code and UI
* Fixed a bug with signature clearing and form validation

= 2.2 =
* Fixed a bug where CF7 form submission was blocked sometimes

= 2.1 =
* Fixed a bug where only the last signature of a form was sent

= 2.0 =
* Signature are now stored as image files for a better compatibility with email clients

= 1.1 =
* Bug fix : field configuration form not displaying in admin
* Bug fix : Clear button not working
* More than one signature field available in forms now

= 1.0 =
* Initial plugin release.

== Upgrade Notice ==

= 1.1 =

= 1.0 =
* Initial plugin release.
