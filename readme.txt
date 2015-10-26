=== Contact Form 7 Signature Addon ===
Plugin URI: http://www.breizhtorm.fr/blog/
Contributors: tameroski
Tags: Contact Form 7, form, forms, contactform7, contact form, signature pad, sig, signature field, cf7, handwriting, write
Requires at least: 3.9
Tested up to: 4.3
Stable tag: 2.6.5

Easily add an handwritten signature field to Contact Form 7

== Description ==

### Add a signature field to Contact Form 7

This plugin adds a new field type to the Contact Form 7 plugin and allows users to add an handwritten signature to the message via a signature pad. The plugin uses Szymon Nowak's great Javascript library (https://github.com/szimek/signature_pad).

= News =
* Please upgrade to 2.6.2 and save your forms again, as it fixes a bug in previous versions where additional settings were wrongly added to your forms settings.
* Version 2.6 is out, finally adding support for HDPi and Retina screens. Be careful though, as i had to refactor things a bit, specially field layout and classes attributes. You might have to update your field's custom CSS!

= Compatibility =
This plugin requires version 3.5 or higher of the "Contact Form 7" plugin.
It should work on almost every modern web and mobile browser (IE9+, ...).

= Installation / Support =
Please read the [installation notes](http://wordpress.org/plugins/contact-form-7-signature-addon/installation/) and [FAQ](http://wordpress.org/plugins/contact-form-7-signature-addon/faq/) for details.

You can ask for support [here](http://wordpress.org/support/plugin/contact-form-7-signature-addon), and if you're new to web development and Wordpress things, i think you should have a look at [this article](http://www.wpbeginner.com/beginners-guide/how-to-properly-ask-for-wordpress-support-and-get-it/) first.

= Be kind =
This plugin is just sharing the result of something i needed once for a project, there's nothing commercial in there. But i'll listen to your requests and do my best to keep the plugin up to date anyway.

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

**Important note** : Plugin scripts are loaded in the footer, so your theme **MUST** be using the [wp_footer()](http://codex.wordpress.org/Plugin_API/Action_Reference/wp_footer) template tag for the signature field to work properly.

== Screenshots ==

1. Signature field configuration
2. Signature field rendered in contact form

== Frequently Asked Questions ==

= I don't see the signature image in the mail. What can i do ? =

First things first : verify that your email is sent in HTML format. In CF7, theres's a checkbox at the bottom of each mail configuration to do that.

Then verify that the image is wrapped in an HTML image tag in your mail configuration, like this :
`<img src="[your-signature-field]"/>`

= How can i customize my signature field =

When building your signature field with CF7's field generator, you have several options you can add to the field shortcode.

Width and height :
`[signature signature-666 333x222]`
Will tell the plugin to draw the field 333px wide and 222px tall.

ID and class :
`[signature signature-666 id:foo class:bar]`

= How do i add styles to my signature field ? =

Use CSS like you would do for any other field in your form. 
For example, using the field wrapper, you can add a border like this : 
`
.wpcf7-form-control-signature-body canvas{
	border: 1px dotted #BADA55;
}
`

= How do i make my signature field responsive ? =

It depends on your form layout but once again, you can do this by using basic CSS instructions. The plugin will deal with window size and device orientation automatically updating itself to match the right size.

The only thing you have to do is apply width and/or height styles *to the field wrapper, not the canvas*, like this :
`
@media screen and (max-width: 768px) {
    .wpcf7-form-control-signature-wrap {
        width:100%;
    }
}
...
`

= The field is not working well after my desktop browser window was resized or after orientation change on my mobile. How can i fix that ? =

The signature field needs to be "reloaded" too when its container's size changes, but you should be aware that it will also clear its content. I assume this is your responsability to do so. The plugin provides a javascript function that you can call from your theme's Javascript in order to do that :
`
window.onresize = sigFieldsResize;
`

== Changelog ==

= 2.6.5 =
* Changed the way plugin assets are loaded
* Added a note about the plugin to require wp_footer() in the theme.
* Removed unused action call

= 2.6.4 =
* Plugin scripts are now loaded in the footer in order to prevent DOM loading issues.

= 2.6.3 =
* Bugfix : Shortcode options "id:" and "class:" were not taken into account

= 2.6.2 =
* Automatically get rid of rubbish JS added through 2.5 & 2.6 versions, so plugin users won't have to do it

= 2.6.1 =
* Bugfix : additional settings JS callback was called once more each time the form settings were submitted
* Bugfix : fixed an issue with submit buttons not working when there's more than one form in a page
* Bugfix : fixed an issue with single forms without a signature field

= 2.6 =
* REALLY fixed the device ratio bug on HDPi devices like iPads
* Fixed a major issue with 2.5 where the field was "growing" on window resize (facepalm)
* Refactored field layout

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
