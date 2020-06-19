=== Spreebie Barter - Ethereum Payments and Donations ===
Contributors: Thabo David Klass
Donate link: http://openbeacon.biz/?p=712
Tags: payments, donations, ethereum, crypto, metamask
Requires at least: 4.1
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The SPREEBIE BARTER plugin is a widget that enables easy and fast Ethereum payments on any WordPress website via Metamask.

== Description ==

Online payments have been one of the major applications of the web since the rise of usage of the internet by the general public in the early 1990s – earlier implementations were naturally a lot cruder. Later on, superior payment systems like PayPal and Stripe came along and offered developers ways to facilitate payments.

Another huge wave followed after that – cryptocurrency. Many people had been trying to create popular digital currencies for years – most notably was Nick Szabo with his early efforts in the 1990s. In 2009, blockchain-based currencies were create and later Ethereum, which comes out of the box with smart contracts, was created. Not only is the Ethereum platform advanced, it is also versatile in that it comes with Web3 that can be injected into a web application’s instance to facilitate Ethereum transactions.

Javascript has become a mature language that is capable of doing things that had long been ascribed to compiled programming languages – this maturity has given leeway to create better products.

WordPress is now running on 30% of all websites – in a way, it has become a kind of web operating system. This means that it has become unavoidable and thinking about creating new products and dedicated implementations of existing products for WordPress is imperative.

The combination of better payments technology, cryptocurrencies, mature Javascript and a web dominated by a very powerful WordPress was fertile soil to create Spreebie Bartner – a completely new paradigm in online payment.

This plugin requires Metamask to work.

THIRD PARTY SERVICE NOTE: When making donations to the author of this plugin, a third party service called Firebase (https://firebase.google.com/) is used to store donator names and emails. A donator can choose to donate anonymously so that their name and email are not stored. This third party service on this plugin ONLY works with donations to the author - the rest of the plugin functions without it.

Another third party service used is CoinMarketCap (https://api.coinmarketcap.com) - this is used to get Ethereum price information for different currencies. There is no storage that happens with this service.

== Installation ==

= Minimum Requirements =

* PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* Spreebie Barter 1.0.1 requires WordPress 4.1+

1. Install the plugin like it is commonly done, either by uploading it via FTP or by using the
"Add Plugin" function of WordPress.

2. Activate the plugin at the plugin administration page.

3. Go to the "Spreebie Barter" and click the "Settings" tab.

4. Put in your personal Ethereum address and click "Save Changes".

5. You can then go to the "Payments" or "Donations" tabs to create payments.

6. Go to the "Appearance -> Widgets".

7. Drag the "Spreebie Barter" widget to your desired location on the sidebar or footer.

8. Give it a pertinent title like "Pay with Ether" or "Donate".

= Usage =

1. Go to the "Spreebie Barter" menu and click it. Click on the "Settings" tab.  Enter you personal Ethereum address - DO NOT put the address in quotes.

2. Should you need support, you can buy 6 months support by clicking the "BUY NOW" button. This will also provide you with a comprehensive manual.

3. To create a payment, go to the "Payments" tab and populate all the entries - then click "Create Payment". PLEASE LABEL your titles in a way that will help you to remember what they are about.

4. After the payment has been created, go to "SB Payments and Donations -> View SB Payments and Donations". Click on the payment that you just created. Under "Custom Fields", you will see "payment_token" towards the bottom of that table. Copy it's value on the right. This value can then be sent to you customers in order to pay your on the widget.

5. To pay on the widget, paste the payment token value into the input field and click "GET". This will get all the payment information including the amount you have to pay. If you agree with all the details, click pay and Metamask will open. Click the "Submit" button on Metamask to make the payment.

6. PLEASE keep the page open until the payment has been processed. It usually takes a few minutes. The widget will indicate when the payment has been processed.

== Frequently Asked Questions ==

Q: What is Metamask and why is it required?
A: Metamask is a Google Chrome and Firefox plugin that facilitates Ethereum transactions on your browser. It is necessary because it provides something called "injected Web3", which a way in which Javascript performs Ethereum transactions. It is the easiest and most popular Web3 implementation.

Q: Where can I find Metamask?
A: You can find Metask at https://metamask.io/.

Q: Does Spreebie Barter require Javascript to function?
A: Yes.

Q: How do it get users to get payment and donation tokens?
A: You can text them, email them or place them publicly on your site. 

Q: Can tokens generated on one site work on another?
No. The payment and donation data is store only on the the site that create it.

Q: Is Spreebie Barter safe?
A: As with all WordPress plugins, the security of your site is what keeps the site secure.  The plugin uses ALL the standard security measures.

Q: Does the Firebase third party service comply with the current EU data protection rules?
A: Yes, it does.


== Screenshots ==

1. The Spreebie Barter widget on a sidebar ready to be used.

2. The Spreebie Barter page on the admin backend - this is where the payments and donations are created.

3.This is the settings page where a user enters the Ethereum address that will receive funds.

4. The list page showing all the payments and donations that have been created.

5. Clicking on one of the items on the "View SB Payments and Donations" page will open one of the payments or donations a user has created. THE MOST IMPORTANT thing here is the "payment_token" or the "donation_token". This what the user gives to there subscribers in order to use on the widget.

5. The categories page.

== Changelog ==

= 1.0 =
Initial release

== Upgrade Notice ==

= 1.0 =
Initial release. No upgrade yet.
