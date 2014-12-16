contact-form-7-signature-addon
==============================

This piece of code is an addon for the famous Contact Form 7 Wordpress plugin.
It brings a new field type that allow users to submit an handwritten signature.

The plugin uses a Javascript library (https://github.com/szimek/signature_pad).

# Installation

This plugin requires the Contact Form 7 plugin.

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin feature
2. Activate the plugin
3. Edit a form in Contact Form 7
4. Choose "Signature field" from the Generate Tag dropdown
5. Follow the instructions on the page

If you want the signature image to be included in the body of your mails, just put an image tag which src attribute is set to be the content of your field, just like this :
```
<img src="[your-signature-field]"/>
```
Your contact form needs to send HTML messages, of course.

And voilà!
