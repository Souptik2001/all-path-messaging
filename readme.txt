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

[Setup ⚙️](https://github.com/Souptik2001/wp-messaging/wiki/Setup-%E2%9A%99%EF%B8%8F) | [Issues](https://github.com/Souptik2001/wp-messaging/issues) | [Services and functions 🧩](https://github.com/Souptik2001/wp-messaging/wiki/Services-and-functions-%F0%9F%A7%A9) | [Create your own Adapter 🛠️](https://github.com/Souptik2001/wp-messaging/wiki/Create-your-own-Adapter-%F0%9F%9B%A0%EF%B8%8F)

### Coming soon ⏳

- Push notifications
- Email Testing page
- SMS Testing page
- Push notifications Testing page

### Examples

#### Email 📧📨

Send an email through a particular adapter (with headers 😉) -

`
\Souptik\WPMessaging\Email\send(
  [ 'dev2@souptik.dev' ],
  'Yay its working!',
  'This is some long mail body.',
  'Souptik',
  'dev1@souptik.dev',
  [
   'cc' => [
    [
     'name'  => 'CC Test',
     'email' => 'cc@souptik.dev',
    ],
   ],
   'attachments' => [
    trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-wp-messaging.php',
     'SameFileDifferentName.php' => trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-wp-messaging.php',
   ],
  ],
  'mailgun'
 );
`

Just remove the last parameter! And now it uses the default selected adapter -

`
\Souptik\WPMessaging\Email\send(
  [ 'dev2@souptik.dev' ],
  'Yay its working!',
  'This is some long mail body.',
  'Souptik',
  'dev1@souptik.dev',
  [
   'cc' => [
    [
     'name'  => 'CC Test',
     'email' => 'cc@souptik.dev',
    ],
   ],
   'attachments' => [
    trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-wp-messaging.php',
     'SameFileDifferentName.php' => trailingslashit( WP_CONTENT_DIR ) . '/mu-plugins/test-wp-messaging.php',
   ],
  ],
 );
`

Checked the override `wp_mail` checkbox? Try a simple `wp_mail`! -

`
wp_mail(
  [ 'dev2@souptik.dev' ],
  'Yay its working!',
  'This is some long mail body - from <strong>wp_mail</strong>.',
  [],
  []
 );
`

#### SMS 📲

Send a SMS through a particular adapter -

`
\Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!', 'twilio' );
`

Just remove the last parameter! And now it uses the default selected adapter -

`
\Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!' );
`

### Creating your own adapter 🛠️

Here comes the cool part fellow developers!

Docs coming soon! ⏳

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