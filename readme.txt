=== Raidify Contact Form ===

Contributors: olaleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=EUQS4R94GUKRG&lc=GB&item_name=Donation%20towards%20Raidify%20Plugin%20Development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Author URL: http://raidify.com/
Plugin URL: http://raidify.com/raidify-contact-form/
Tags: raidify, contact,contact form, contact form plugin, contact me, contact us, contacts, contacts form plugin, raidify contact form, feedback, feedback form, form, request, request form, customizable contact form, contact form with smtp
Requires at least: 3.9
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Raidify contact form is a free customizable contact form with SMTP (Simple Mail Transfer Protocol) support

== Description ==

Raidify contact form is a WordPress contact form plugin that gives you the freedom of defining how your audience reaches out to you, Yippee!!. It gives you the flexibility to customize what fields are very important to your data collection; define what kind of placeholders your audience sees when they need to connect with you while also giving you the chance to use either phpmail or SMTP - isn't that great?

First you activate the plugin and put the shortcode [rcf_contact_form] in the required page or post (whew! that was quite short, yeah?). This gives you the default Raidify contact form functionality with phpmail. At this stage, your wordpress admin email account will also be the default mail receiving address. From that point on, you are just a few clicks away from customizing your very own contact form

Bonus: Unlike most Contact form plugins for WordPress, this one comes with all of it's features completely free.

Requires at least WordPress 3.9

**Current add-ons**

* Use your WordPress's site admin email to receive mails or use a customized email account
* Set the required fields easily from the plugin settings page
* Set the fields placeholders easily
* Set the required label to give your client hints about which fields they must fill
* Choose to send mail by PHP Mail or SMTP (defaults to PHP mail)
* Easily set your SMTP settings
* Specify the from name (Optional) and email address for outgoing email
* Specify a SMTP host
* Specify an SMTP port number.
* You can use either SSL / TLS encryption (not the same as STARTTLS)
* Choose either SMTP authentication or no authentication
* Specify a SMTP Username
* Specify a SMTP Password


**Coming soon**

* CAPTCHA support
* Got more ideas? Tell me!)

If you have suggestions for a new add-on, feel free to email me at o.osunsanya@raidify.com.

Or follow my sites on Twitter!

https://twitter.com/raidify_dev

= Translators =

* French (fr_FR) - [Olaleye](http://raidify.com/)
* Spanish (es_ES) - [Jorge Azambuya](http://jorgeazambuya.com.ar/)

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to [me](http://raidify.com/about/) so that I can bundle it into Raidify Contact Form.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload plugin to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the Raidify Contact Form link on the Dashboard, and customize the settings
4. On the page wherein you want to see the contact form, paste the value that's in the "Shortcode Name". The default is "[rcf_contact_form]".
5. Done

== Frequently Asked Questions ==

= How can I change the default email address used to receive mails? =

You have to modify the 'Use this email address to receive mails' section on the settings page of the plugin

In Raidify Contact Form settings page: 

1. On the "Use this email address to receive mails", you will see "	( Custom Email account )", select the radio button on that line

2. Type the email address you wish to use to receive emails inside the field

3. Save your settings

= What exactly are placeholders? =

Placehoders are hints that are displayed in the input elements of forms to give the user an idea of the content that should be provided

The short hint is displayed in the input field before the user enters a value

= How can I make a field required? =

All the fields are set to required by default, but to make an element required

1. Go to Raidify Contact Form settings page

2. Under the Required column, make sure that the checkbox for the field is checked

3. Save your settings

= How can I change the text or label in front of the required field? =

The default text in front of the required field is "(required)"

To change the text you have to :

1. Go to Raidify Contact Form settings page

2. From the required label settings directly under the field table, change the text to any text you want

3. Save your settings

= What does Raidify Contact Form use to send email by default? =

By default Raidify contact form makes use of PHPMail by default to send email

= What is SMTP and how can I use it with Raidify Contact form? =

Simple Mail Transfer Protocol (SMTP) is an Internet standard for electronic mail (e-mail) transmission

To use SMTP with Raidify Contact Form

1. Go to Raidify Contact Form settings page

2. Select the SMTP radio button

3. Fill the necessary details from your SMTP settings

4. Save your settings

= Where can I get support and report bugs? =

Send me a message please http://raidify.com/contact-me/

== Screenshots ==

1. Raidify contact form
2. Raidify contact form admin page

== Changelog ==

= 1.0.0 =

* Initial release.

= 1.0.3 =

* Fixed bugs that prevents the contact form from loading on a page.

= 1.1.3 =

* Code restructure.
* Tested with Wordpress 4.2.2

= 1.1.4 =
* Fixed translation problem of the message field.
* Spanish translation was added. Thanks to [Jorge Azambuya](http://jorgeazambuya.com.ar/).