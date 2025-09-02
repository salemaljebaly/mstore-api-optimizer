=== MStore API Optimizer ===
Contributors: salemaljebaly
Tags: mstore, performance, woocommerce, flutter, api, optimization, cart, shipping
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dramatically improves MStore API performance for large shopping carts, reducing response times from 30+ seconds to under 1 second.

== Description ==

**MStore API Optimizer** solves critical performance issues in the MStore API plugin when processing large shopping carts. If your Flutter/React Native app experiences timeouts or slow responses with the MStore API, this plugin is the solution.

= The Problem =

The MStore API's `shipping_methods` and `payment_methods` endpoints suffer from severe performance bottlenecks:

* 30+ second response times for carts with 50+ items
* Frequent timeouts causing poor user experience
* Cart abandonment due to slow checkout process
* Each cart item processed individually with expensive callbacks

= The Solution =

This plugin intercepts the problematic endpoints and applies optimized batch processing:

* **95%+ Performance Improvement**: From 30+ seconds to <1 second
* **Batch Operations**: Process all items at once instead of individually
* **Smart Hook Management**: Temporarily disable expensive WooCommerce hooks
* **Update-Safe**: Survives MStore API plugin updates without losing fixes

= Key Features =

* ✅ **Zero Configuration** - Works immediately after activation
* ✅ **Update-Safe** - Doesn't modify original MStore files
* ✅ **Full Compatibility** - Maintains all original MStore functionality
* ✅ **Debug Logging** - Built-in performance monitoring
* ✅ **Lightweight** - Minimal resource usage

= Technical Highlights =

* Uses `rest_pre_dispatch` filter for clean endpoint interception
* Batch processing with suspended WooCommerce hooks
* Proper session management and cart initialization
* Compatible with WooCommerce Subscriptions, WCFM, and other extensions

= Perfect For =

* E-commerce stores using MStore API (FluxStore)
* Flutter/React Native mobile apps with WooCommerce
* Stores with large product catalogs
* High-volume checkout processes

== Installation ==

= Automatic Installation =

1. Go to Plugins > Add New in your WordPress admin
2. Search for "MStore API Optimizer"
3. Click Install Now and then Activate
4. Done! Performance improvements are immediate

= Manual Installation =

1. Upload the plugin files to `/wp-content/plugins/mstore-api-optimizer/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. No configuration needed - it works automatically

== Frequently Asked Questions ==

= Does this plugin modify the original MStore API files? =

No! This plugin uses WordPress hooks to intercept endpoints before MStore processes them. Your original MStore files remain untouched, so updates won't break the fix.

= Will this work with my existing MStore setup? =

Yes, it's fully compatible with all MStore API versions 4.0+. It maintains 100% backward compatibility with all features.

= How much performance improvement can I expect? =

Typically 95%+ improvement. For example:
* Before: 30 seconds for 50 items
* After: 0.6 seconds for 50 items

= Does it work with WooCommerce extensions? =

Yes! It's compatible with:
* WooCommerce Subscriptions
* WCFM Marketplace
* WooCommerce Multilingual
* All payment gateways (Stripe, PayPal, COD, etc.)

= How can I verify it's working? =

Enable WordPress debug logging and check for "MStore API Optimizer DEBUG" entries in your debug log.

= What if I have issues? =

1. Ensure MStore API plugin is active
2. Check WordPress and PHP versions meet requirements
3. Enable debug logging to monitor performance
4. Contact support with specific error details

== Screenshots ==

1. Performance comparison: Before vs After optimization
2. Debug log showing improved response times
3. Plugin activation - zero configuration needed

== Changelog ==

= 1.0.0 =
* Initial release
* Optimized shipping_methods endpoint processing
* Optimized payment_methods endpoint processing
* Added comprehensive debug logging
* Update-safe architecture implementation
* Full MStore API compatibility maintained

== Upgrade Notice ==

= 1.0.0 =
Initial release. Install to immediately optimize MStore API performance by 95%+.

== Support ==

For support and feature requests:

* GitHub: https://github.com/salemaljebaly/mstore-api-optimizer
* Website: https://lamah.co

== Technical Details ==

This plugin optimizes these specific MStore API endpoints:
* `/wp-json/api/flutter_woo/shipping_methods`
* `/wp-json/api/flutter_woo/payment_methods`

The optimization works by:
1. Intercepting requests using `rest_pre_dispatch` filter
2. Applying batch cart operations instead of item-by-item processing
3. Temporarily suspending expensive WooCommerce hooks during processing
4. Calculating totals once instead of per-item calculations
5. Maintaining full compatibility with all MStore features