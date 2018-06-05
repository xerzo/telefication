=== Telefication ===
Contributors: arshen
Tags: telegram, wordpress, notification, woocommerce, email, order
Requires at least: 3.1.0
Tested up to: 4.9.4
Requires PHP: 5.6
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get a notification on Telegram by your own bot or Telefication bot. Notification for emails, new Woocommerce orders, new comments, new posts and new users.

== Description ==

## Telegram plugin for Wordpress!

Do you want receive notification from your wordpress in your telegram? or get a notification for new orders from Woocommerce? This plugin is for you.

Telefication send Wordpress emails and events as a notification to your Telegram through your own bot.

This plugin use [Telefication](https://telefication.ir) service to send notifications to Telegram. Since version 1.3.0 you can use your own Telegram bot to get notifications directly.

Feature List:

*   Use can use your own Telegram bot to get notifications directly.
*   Send notification to Telegram user or group.
*   Send email subject as a Telegram notification.
*   Send email body as a Telegram notification.
*   Display recipient email address in notifications.
*   Send Woocommerce detailed new order notification to Telegram.
*   Notify for new comments
*   Notify for new Posts
*   Notify for new users

== Installation ==

1. Upload the entire `Telefication` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Join [@teleficationbot](https://t.me/teleficationbot) and get id. (If you want more than one user get notified, you can add @teleficationbot to groups.)
4. Go to Telefication setting under Settings menu
5. Insert your id in Telefication ID field and save settings.

== Frequently Asked Questions ==

= Installation Instructions =

1. Upload the entire `Telefication` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

###If you want use your own bot:
3. Go to Telefication setting under Settings menu and go to "My Own Bot" tab.
4. Follow instructions there to create your own bot and insert your bot token then save changes .
5. Go to "General Setting" tab and press "Get your ID" button then save changes.

###If you want use Telefication service:
3. Join [@teleficationbot](https://t.me/teleficationbot) and get id. (If you want more than one user get notified, you can add @teleficationbot to groups.)
4. Go to Telefication setting under Settings menu
5. Insert your id in Telefication ID field and save changes.

= How do I get notifications? =

You get notifications through @teleficationbot which is a Telegram bot.
Since version 1.3.0 you can use your own bot to get notifications.

= Is there a limit to the number of notifications? =

Unlimited, If you use your own bot and, 50 Notifications per 24 hours, If you use Telefication service.

== Screenshots ==

1. Telefication general setting page.
2. Telefication Custom bot setting page
3. Telegram messages.

== Changelog ==
= 1.4.0 =
* [Add] Get your chat ID from Telefication setting page when you use your own bot.
* [Add] Notify for new comments
* [Add] Notify for new posts
* [Add] Notify for new users
* [Add] Now, you have an option to cancel notifications for emails.

= 1.3.0 =
* [Add] A new feature to use your own Telegram bot

= 1.2.1 =
* [Fix] sending not allowed html tag problem.

= 1.2.0 =
* [Add] option to send email body.
* [Add] option to display recipient email.

= 1.1.0 =
* [Add] Send test message button.
* [Add] Emails field to filter notifications by recipients email.

= 1.0.0 =
* The first release.

