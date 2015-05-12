=== Contact Form 7 Signature field ===
Plugin URI: http://www.breizhtorm.fr/blog/
Contributors: tameroski
Tags: Contact Form 7, form, forms, contactform7, contact form, signature pad, sig, signature field, cf7, handwriting, write
Requires at least: 3.9
Tested up to: 4.2
Stable tag: 2.0

Adds a new field type to Contact Form 7 that allow users to submit an handwritten signature.

== Description ==

### Add a signature field to Contact Form 7

This plugin adds a new field type to the Contact Form 7 plugin and allows users to add an handwritten signature to the message via a signature pad. The plugin uses a Javascript library (https://github.com/szimek/signature_pad).

This plugin requires version 3.9 or higher of the "Contact Form 7" plugin.

Please read the installation notes for more details.

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

Important note : since version 2.0, the plugin no longer uses inline base64 encoded images, and signature images are saved to a "signatures/" subdir of your wordpress upload directory. So every email clients compatibility issues should be gone !

== Screenshots ==

1. Signature field configuration
2. Signature field rendered in contact form

== Frequently Asked Questions ==

== Changelog ==

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
