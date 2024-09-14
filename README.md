# WordPress messaging

![GitHub Actions](https://github.com/Souptik2001/wp-messaging/workflows/Coding%20Standards%20and%20Static%20Analysis/badge.svg)

Limitless Communication: All-in-one, super scalable, messaging Solution for WordPress.

Ok hold on! ✋. So, many words in one line.
Let's understand each one-by-one.

- **All-in-one:** What do you want? - Email, SMS, push-notification? Get all-in-one.
  - But can I don't want to use `xyz` provider for SMS, I want to use `pqr`, can I have that? Yes it provides you with lot of pre implemented providers for all email, sms and push-notification.
- **Super Scalable:** But I want to use an email provider named `yxr` you haven't heard the name of. Now what? 🧐
  - No worries! Are you a developer? If yes, just write your own plugin and implement your own adapter and see it nicely hooked-up with "WordPress messaging". Please refer to this section for implementing adapters.

And that's how it provides **Limitless communication**! 🚀

## Open courtesy

A big thank you to these open source projects, which play a crucial role in this project!

- [Utopia Messaging](https://github.com/utopia-php/messaging) - This is literally the backbone of the project. More about it [here](https://github.com/Souptik2001/wp-messaging?tab=readme-ov-file#special-mention-about-utopia-messaging-package-). 🔥
- [Travelopia WordPress PHPCS Coding Standards](https://github.com/Travelopia/wordpress-coding-standards-phpcs) - Super cool PHPCS coding standard rules. ✨

## WIKI

The main two ideas of the the plugin are -

#### For Developers -

- To give a simple easy to use  function to send **Email**, **SMS** or **Push Notifications** through the selected adapter.
- Don't have your desired adapter? No need to wait for me! Go ahead and add the adapter yourself, by just a simple boilerplate code! [See how easy it is to add your own adapter](https://github.com/Souptik2001/wp-messaging?tab=readme-ov-file#creating-your-own-adapter-%EF%B8%8F)!

#### For users -

- WordPress' default `wp_mail` doesn't deliver your mail reliably? Select any of the available adapters to override `wp_mail` to deliver mails reliably.
- Already using some email marketing plugin? But not finding your desired provider? And tired of requesting the author to introduce the provider? 😣 - No worries! 🎉 - Have some developer friend, or some coding knowledge? [Learn how easy it is to add your own provider with a simple boilerplate code](https://github.com/Souptik2001/wp-messaging?tab=readme-ov-file#creating-your-own-adapter-%EF%B8%8F)!
  - TLDR; Keep using your favorite email marketing plugin, while routing the emails through **WP Messaging** plugin! 🚀

### Settings page ⚙️

WP-Messaging provides different settings page for each service, under the "tools" menu -

![settings-location](./assets/images/settings-location.png)

And each settings just contains list of adapters, with their required settings and a radio button to make it the default one -

![settings-sms](./assets/images/settings-sms.png)

The Email settings just have one extra settings, to choose whether to override the `wp_mail` function or not, using a simple checkbox! -

![settings-email](./assets/images/settings-email.png)

You are now all set to start using **WP Messaging**! 🎉

### Services -

#### Email 📧📨

Send an email through a particular adapter (with headers 😉) -

```php
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
```

Just remove the last parameter! And now it uses the default selected adapter -

```php
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
```

Checked the override `wp_mail` checkbox? Try a simple `wp_mail`! -

```php
wp_mail(
  [ 'dev2@souptik.dev' ],
  'Yay its working!',
  'This is some long mail body - from <strong>wp_mail</strong>.',
  [],
  []
 );
```

##### Keep using your favorite email marketing/managing tool ❤️

The interesting part of this plugin is that it only focuses on solving the smallest purpose it is created for in the most efficient way possible.

For almost all the email marketing/managing tool there is an option to select how you want to send the email (if there is none, then it by default uses `wp_mail`), like these -

![default-mail-method-example-1](./assets/images/default-mail-method-example-1.png)
![default-mail-method-example-2](./assets/images/default-mail-method-example-2.png)

If you are using any other mailer/method in these plugins, just change to this default option and see how seamlessly `wp-messaging` hooks in and sends emails reliably through your selected adapter! 😎

#### SMS 📲

Send a SMS through a particular adapter -

```php
\Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!', 'twilio' );
```

Just remove the last parameter! And now it uses the default selected adapter -

```php
\Souptik\WPMessaging\SMS\send( [ '+xxxxxxxxxxxx' ], 'Yay its working!' );
```

#### Push notification 💬

Coming soon! ⏳

### Creating your own adapter 🛠️

Here comes the cool part fellow developers!

Docs coming soon! ⏳

### Special mention about "Utopia Messaging" package 🙏

Thank you message coming soon!

## Contribute

Feel free to open a issue or pull request if you want to contribute anything to this plugin!

Here is how you can easily setup the project locally! -

- Run `nvm use`.
- Run `npm run start`.
- Run `npm run start-env`.
- That's it. You are ready with your local development environment.
- To stop your local development setup run `npm run stop-env`.
- And to completely remove all data related to your local development environment setup run `npm run destroy-env` (Disclaimer: You will loose all your data you created in the local development site).
