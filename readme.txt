=== WP Messaging ===
Contributors: souptik
Tags: messaging, email, sms, push-notification
Requires at least: 4.4
Tested up to: 6.6.2
Requires PHP: 5.6
Stable tag: 1.0.0

Limitless Communication: All-in-one, super scalable, messaging Solution for WordPress.

== Description ==

[Check out the Github Repository ♥](https://github.com/Souptik2001/wp-messaging)

**Limitless Communication:** All-in-one, super scalable, messaging Solution for WordPress.

Ok hold on! ✋. So, many words in one line.
Let's understand each one-by-one.

- **All-in-one:** What do you want? - Email, SMS, push-notification? Get all-in-one.
  - But can I don't want to use `xyz` provider for SMS, I want to use `pqr`, can I have that? Yes it provides you with lot of pre implemented providers for all email, sms and push-notification.
- **Super Scalable:** But I want to use an email provider named `yxr` you haven't heard the name of. Now what? 🧐
  - No worries! Are you a developer? If yes, just write your own plugin and implement your own adapter and see it nicely hooked-up with "WordPress messaging". Please refer to this section for implementing adapters.

And that's how it provides **Limitless communication**! 🚀

### Quick Links

[Setup](https://github.com/Souptik2001/wp-messaging?tab=readme-ov-file#setup) | [Issues](https://github.com/Souptik2001/wp-messaging/issues)

### Coming soon ⏳

- Push notifications
- Email Testing page
- SMS Testing page
- Push notifications Testing page

### Examples

`
<?php
/**
 * SMS: Twilio Messaging.
 *
 * Remove the last parameter to use the default selected adapter.
 */
$response = \Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!', 'twilio' );

/**
 * Email: Mailgun Messaging - with headers.
 *
 * Remove the last parameter to use the default selected adapter.
 */
$response = \Souptik\WPMessaging\Email\send(
	[ 'to@test.com' ],
	'Yay its working!',
	'This is some long mail body, with <strong>HTML</strong>!',
	'From Name',
	'from@test.com',
	[
		'cc' => [
			[
				'name'  => 'CC Name',
				'email' => 'cc@test.com',
			],
		],
		'attachments' => [
			'/path/to/attachment.pdf',
			'SameFileDifferentName.pdf' => '/path/to/attachment.pdf',
		],
	],
	'mailgun'
);
`

== Installation ==

Upload 'wp-messaging' to the '/wp-content/plugins/' directory.

Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. WordPress Options (Email)
2. WordPress Options (SMS)
3. WordPress Options (Push notifications)

== Changelog ==

= 1.0.0 =
* First stable release.