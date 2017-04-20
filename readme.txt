=== Contact Form 7 Signature Addon ===
Plugin URI: http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html
Donate link: http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html
Contributors: tameroski
Tags: Contact Form 7, form, forms, contactform7, contact form, signature pad, sig, signature field, cf7, handwriting, write
Requires at least: 3.9
Tested up to: 4.7
Stable tag: 4.0

Easily add an handwritten signature field to Contact Form 7

== Description ==

### Add a signature field to Contact Form 7

This plugin adds a new field type to the Contact Form 7 plugin and allows users to add an handwritten signature to the message via a signature pad. The plugin uses Szymon Nowak's great Javascript library (https://github.com/szimek/signature_pad).

= News =
* Version 4 is out, with a major technical refactoring of the plugin. If you're a theme developer, please review the FAQ to see what changed (specially if you were using the provided javascript functions).
* It is also now possible to use Base64 encoded Inline images (at your own risk because of mail clients compatibility issues).

= Compatibility =
This plugin requires version 4.6 or higher of the "Contact Form 7" plugin.
The signature pad should work on almost every modern web and mobile browser (IE9+, ...).

= Installation / Support =
Please read the [FAQ](http://wordpress.org/plugins/contact-form-7-signature-addon/#faq) for details on how to setup your signature fields.

You can ask for support [here](http://wordpress.org/support/plugin/contact-form-7-signature-addon).

= Be kind =
This plugin is just sharing the result of something i needed once for a project, there's nothing commercial in there. But i'll listen to your requests and do my best to keep the plugin up to date anyway.

Don't forget to rate the plugin if you like it (or not). You can even make a small donation [here](http://www.keybored.fr/2016/08/14/WP-Contact-Form-Signature-Field.html).

== Installation ==

This plugin requires the Contact Form 7 plugin.

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin feature
2. Activate the plugin
3. Edit a form in Contact Form 7
4. Choose "Signature field" from the Generate Tag dropdown
5. Follow the instructions on the page

If you want the signature image to be included in the body of your emails, just put an image tag which src attribute is set to be the content of your field, just like this :
`<img src="[your-signature-field]"/>`

If you want the signature image to be sent as an attachment to the email, just follow these steps : 

1. add a "attachment" parameter to your field like this : `[signature your-signature-field attachment]`
2. add the signature tag to the mail attachment section, like you would do for a file (see [this tutorial](http://contactform7.com/file-uploading-and-attachment/)) : `[your-file][your-signature-field]`

== Screenshots ==

1. Signature field configuration
2. Signature field rendered in contact form

== Frequently Asked Questions ==

= I don't see the signature image in the mail. What can i do ? =

First things first : verify that your email is sent in HTML format. In CF7, theres's a checkbox at the bottom of each mail configuration to do that.

Then verify that the image is wrapped in an HTML image tag in your mail configuration, like this :
`<img src="[your-signature-field]"/>`

= How do i include the signature image as an attachment ?  =

If you want the signature image to be sent as an attachment to the email, just follow these steps : 

1. add a "attachment" parameter to your field like this : `[signature your-signature-field attachment]`
2. add the signature tag to the mail attachment section, like you would do for a file (see [this tutorial](http://contactform7.com/file-uploading-and-attachment/)) : `[your-file][your-signature-field]`

= And what if i want to use a Base64 encoded inline image in my email instead ?  =

If you want the signature image to be sent as an attachment to the email, just follow these steps : 

1. add a "inline" parameter to your field like this : `[signature your-signature-field inline]`
2. include the image in the body of your email, like you would normally do : `<img src="[your-signature-field]"/>`

= How can i customize my signature field ? =

When building your signature field with CF7's field generator, you have several options you can add to the field shortcode :

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

The only thing you have to do is override width and/or height styles *to the field wrapper, not the canvas*, like this :
`
@media screen and (max-width: 768px) {
    .wpcf7-form-control-signature-wrap {
        width:100% !important;
    }
}
...
`

= How do i change my field's colors ? =

There are options for that in the field settings (only hex color supported for the moment) :

`[signature signature-666 background:#333333 color:#FFFFFF]`

= The field is not working well after my desktop browser window was resized or after orientation change on my mobile. How can i fix that ? =

The signature field needs to be "reloaded" too when its container's size changes, but you should be aware that it will also clear its content. I assume this is your responsability to do so. The plugin provides a jQuery function that you can call from your theme's script file :
`
window.onresize = function(){
    $('div.wpcf7 > form').wpcf7ResizeSignatures();
};
`

= Since version 4.0 i got Javascript errors. What changed and what can i do ? =

The main thing that changed is the way you can interact with the plugin. There's now a jQuery plugin for that, and i got rid of all the old JS functions like sigFieldsResize and sigFieldsClear.

Available methods are now : 

`$('div.wpcf7 > form').wpcf7ResizeSignatures();` to resize fields
`$('div.wpcf7 > form').wpcf7ClearSignatures();` to clear fields
`$('div.wpcf7 > form').wpcf7InitSignatures();` to reload fields

So you only have to replace the former functions by this new ones.

== Changelog ==

= 4.0 =
* Technical refactoring
    - now using jQuery : if you were using the old sigFieldsResize function for example, please update to its jQuery counterpart, see [FAQ](http://wordpress.org/plugins/contact-form-7-signature-addon/faq/).
    - new plugin architecture using [Wordpress Plugin Boilerplate](https://wppb.me/)
* Dumped support for old CF7 versions
* CF7 4.6+ is now made mandatory on plugin activation
* It is possible to use base 64 encoded inline image again
* Storing plugin version in WP options for upgrade purpose

= 3.2.1 =
* Fixed a bug when there's no submit button in form

= 3.2 =
* Removed use of CF7 deprecated classes & methods, thanks to [leac](https://github.com/leac)

= 3.1 =
* Fixed a bug where mandatory signatures cannot be sent as attachments

= 3.0 =
* Added support for signatures as attachments

= 2.8.1 =
* Fixed a bug where CF7 additionnal settings were erased sometimes
* Fixed : signature field is cleared after contact form is successfully sent

= 2.8 =
* Improvements on signature storage & security

= 2.7.1 =
* Now posssible to change background color and pen color

= 2.7 =
* Updated signature pad library to v1.5.3 (fixing a few mobile issues)

= 2.6.8 =
* Full i18n support

= 2.6.7 =
* Fixed text domain loading issue

= 2.6.6 =
* CF7 Autosaver compatibility

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

