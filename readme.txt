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

Here comes the cool part fellow developers! 💻

**Tip:** I have provided a dummy adapter for each service at `inc/<service>/adapters/dummy/`.

Consider that as the starting point and let's understand what each file does.

- Let's start with `namespace.php`. It is the entry point of your adapter.
  - In that you will see a simple `bootstrap` function.
  - In that function we are hooking into `EMAIL_SLUG . '_adapters'` and registering our adapter.
  - We pass the following data -
    - `slug`
    - `name`
    - `adapter` class object.
    - `options` - An array defining the settings required for this adapter, which will be used to automatically display the options on the settings page.
- Next is `class-adapter.php`, which is the Adapter class, which we initialized in the above file and passed it to `adapter`. It contains three simple functions -
  - `get_settings_fields` - This is the function which returns the array of options, which we used in the above file for `options`. Each option, will have -
    - The key as the name of the option.
    - And three values -
      - `label` - Label to display in the settings page beside the input.
      - `type` - Type of the field.
      - `sanitize_callback`
  - `get_settings` - This function returns an associative array, whose keys are the name of the options and the value as the value of the options.
  - `get_adapter` - This function will just return the core provider class, which is responsible for processing the message.
    - First check if `Utopia Messaging` already provides the provider or not [here](https://github.com/utopia-php/messaging?tab=readme-ov-file#adapters), for example `Utopia\Messaging\Adapter\Email\Mailgun`.
    - If it is present just use it. Easy peasy! ✨
    - But if not, let's code it ourself, because `Utopia Messaging` makes it so easy to create a new adapter!
- `class-dummy.php` is for that purpose, assuming you don't get a provider already present in `Utopia Messaging`.
  - It's basically a child class of `EmailAdapter` or `SMSAdapter`, which abstract a lot of stuff for us!
  - Let me explain two main functions, `_construct` and `process`. *Rest of the functions and properties are self-explanatory!* 😉
    - In the `_construct` function just put the arguments which you want to accept. That's it! And now they will be available everywhere else as `$this->param_name`!
    - The `process` function is the place where you have to write the main logic of calling your providers API to send the message.
      - As said above all the credentials/data you accepted through constructor are available as `$this->param_name`.
      - Build the `body` and the `headers`.
      - And then you can use the `$this->request` function as demonstrated in the dummy!
      - Create a response using Utopia's `Response` class.
      - Handle the errors, populate the response, return! Done! 🚀

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