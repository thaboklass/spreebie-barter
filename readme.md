# Spreebie Barter - Ethereum Payments and Donations WordPress Plugin

## Description

Spreebie Barter is a widget that enables easy and fast Ethereum payments on any WordPress website via Metamask. It allows website owners to receive payments and donations in Ethereum cryptocurrency seamlessly.

Online payments have evolved significantly since the early days of the internet, with services like PayPal and Stripe offering solutions for facilitating payments. The emergence of cryptocurrencies, especially Ethereum, with its advanced features like smart contracts, has further revolutionized online transactions. With the maturity of JavaScript and the dominance of WordPress as a web platform, Spreebie Barter represents a new paradigm in online payments.

This plugin requires Metamask to work.

### Third Party Services

- **Firebase**: Used to store donator names and emails for donations. Donators can choose to donate anonymously to avoid storing their personal information.
- **CoinMarketCap API**: Used to fetch Ethereum price information for different currencies.

## Installation

### Minimum Requirements:

- PHP version 5.2.4 or greater (PHP 5.6 or greater is recommended)
- MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
- WordPress 4.1+

1. Install the plugin via FTP or through the WordPress "Add Plugin" function.
2. Activate the plugin in the WordPress plugin administration page.
3. Go to "Spreebie Barter" in your WordPress dashboard and click the "Settings" tab.
4. Enter your personal Ethereum address and click "Save Changes".
5. Configure payments and donations in the "Payments" or "Donations" tabs.
6. Place the "Spreebie Barter" widget in your desired location on the sidebar or footer through "Appearance -> Widgets".

## Usage

1. Go to the "Spreebie Barter" menu and click the "Settings" tab. Enter your personal Ethereum address.
2. For support and access to the manual, click the "BUY NOW" button.
3. Create payments by populating the entries in the "Payments" tab and clicking "Create Payment".
4. Copy the "payment_token" value from the payment details, which can be shared with customers for payment.
5. Customers paste the payment token value into the widget, confirm details, and make the payment via Metamask.
6. Keep the payment page open until the payment is processed.

## Frequently Asked Questions

### What is Metamask and why is it required?

Metamask is a browser plugin facilitating Ethereum transactions by providing injected Web3, a method for JavaScript to interact with Ethereum. It is necessary for conducting Ethereum transactions.

### Where can I find Metamask?

You can find Metamask at [metamask.io](https://metamask.io/).

### Does Spreebie Barter require Javascript to function?

Yes, it does.

### How do I distribute payment and donation tokens to users?

Tokens can be distributed via text, email, or publicly on your website.

### Can tokens generated on one site work on another?

No, payment and donation data are stored only on the site that created them.

### Is Spreebie Barter safe?

The plugin follows standard WordPress security measures to ensure safety.

### Does the Firebase third party service comply with the current EU data protection rules?

Yes, it does.

## Screenshots

1. Spreebie Barter widget on a sidebar.
2. Spreebie Barter admin backend page for creating payments and donations.
3. Settings page to enter the Ethereum address.
4. List of all payments and donations created.
5. Payment or donation details page showing the token for customers to use.
6. Categories page.

## Changelog

### 1.0
Initial release

## License

- License: GPLv2 or later
- License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

## Contributors

- Thabo David Klass
