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

- [Utopia Messaging](https://github.com/utopia-php/messaging) - This is literally the backbone of the project. More about it in the Wiki pages. 🔥
- [Travelopia WordPress PHPCS Coding Standards](https://github.com/Travelopia/wordpress-coding-standards-phpcs) - Super cool PHPCS coding standard rules. ✨

## Contribute

Feel free to open a issue or pull request if you want to contribute anything to this plugin!

Here is how you can easily setup the project locally! -

- Run `nvm use`.
- Run `npm run start`.
- Run `npm run start-env`.
- That's it. You are ready with your local development environment.
- To stop your local development setup run `npm run stop-env`.
- And to completely remove all data related to your local development environment setup run `npm run destroy-env` (Disclaimer: You will loose all your data you created in the local development site).
