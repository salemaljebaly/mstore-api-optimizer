# MStore API Optimizer

A WordPress plugin that dramatically improves the performance of MStore API endpoints for large shopping carts, reducing response times from 30+ seconds to under 1 second.

## 🚀 Performance Improvement

- **Before**: 30+ seconds for 50+ items (often timeout)
- **After**: <1 second for 50+ items  
- **Improvement**: 95%+ faster response times

## 🔧 Problem Solved

The MStore API plugin's `shipping_methods` and `payment_methods` endpoints suffer from severe performance issues when processing carts with many products. Each cart item triggers expensive WooCommerce callbacks, causing:

- ⏱️ 30+ second response times
- ⚠️ Frequent timeouts  
- 📱 Poor mobile app user experience
- 💔 Cart abandonment

## ✅ Solution

This plugin intercepts the problematic endpoints **before** MStore processes them and applies optimized batch processing:

1. **Batch Operations**: Process all cart items at once instead of individually
2. **Hook Management**: Temporarily disable expensive WooCommerce hooks during processing
3. **Single Calculation**: Calculate cart totals once instead of per-item
4. **Update-Safe**: Survives MStore API plugin updates

## 📋 Features

- ✅ **Zero Configuration**: Works immediately after activation
- ✅ **Update-Safe**: Doesn't modify original MStore files
- ✅ **Backward Compatible**: Maintains all original functionality
- ✅ **Debug Logging**: Built-in performance monitoring
- ✅ **Lightweight**: Minimal resource usage

## 🔧 Requirements

- **WordPress**: 5.0 or higher
- **WooCommerce**: 3.0 or higher  
- **MStore API**: 4.0 or higher (by FluxStore team)
- **PHP**: 7.4 or higher

## 📦 Installation

### From WordPress Admin

1. Download the plugin zip file
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the zip file and activate
4. Done! Performance improvements are immediate

### Manual Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through **Plugins** menu in WordPress
3. No configuration needed

## 🔍 How It Works

The plugin uses WordPress's `rest_pre_dispatch` filter to intercept these endpoints:

- `/wp-json/api/flutter_woo/shipping_methods`
- `/wp-json/api/flutter_woo/payment_methods`

When these endpoints are called, our optimized handlers process the request using efficient batch operations instead of the original item-by-item approach.

## 📊 Performance Monitoring

The plugin includes debug logging to monitor performance:

```bash
# Check WordPress debug log
grep "MStore API Optimizer DEBUG" wp-content/debug.log
```

Example output:
```
MStore API Optimizer DEBUG: Starting shipping_methods with 42 items
MStore API Optimizer DEBUG: Cart processing took 0.597 seconds
MStore API Optimizer DEBUG: Total shipping_methods execution time 0.6 seconds
```

## ⚙️ Technical Details

### Optimization Techniques

1. **Direct Cart Operations**: Uses `WC()->cart->add_to_cart()` directly
2. **Hook Suspension**: Temporarily disables these hooks during processing:
   - `woocommerce_add_to_cart`
   - `woocommerce_cart_loaded_from_session`
   - `woocommerce_cart_updated`
3. **Batch Processing**: All items processed in single operation
4. **Session Management**: Proper WooCommerce session initialization

### Compatibility

- ✅ **WooCommerce Subscriptions**: Full support
- ✅ **WCFM Marketplace**: Location-based shipping support
- ✅ **WooCommerce Multilingual**: Multi-currency support
- ✅ **All Payment Gateways**: Including COD, Stripe, PayPal, etc.

## 🐛 Troubleshooting

### Enable Debug Logging

Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Common Issues

**Q: Plugin not working after MStore update?**
A: This plugin is update-safe and shouldn't be affected. Deactivate and reactivate if needed.

**Q: Still seeing slow responses?**
A: Check if MStore API is active and verify the endpoints are being intercepted in debug logs.

**Q: Compatibility issues?**
A: This plugin maintains full MStore API compatibility. If issues occur, temporarily deactivate to isolate the problem.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 Changelog

### 1.0.0
- Initial release
- Optimized shipping_methods endpoint
- Optimized payment_methods endpoint
- Added debug logging
- Update-safe architecture

## 👨‍💻 Author

**Salem Aljebaly**
- GitHub: [@salemaljebaly](https://github.com/salemaljebaly)
- Website: [lamah.co](https://lamah.co)

## 📄 License

This plugin is licensed under the GPL v2 or later.

## ⭐ Support

If this plugin helped improve your store's performance, please:

- ⭐ Rate it on WordPress.org
- 🐛 Report issues on GitHub
- 💡 Suggest features
- 📢 Share with other developers

---

**Made with ❤️ for the WooCommerce & Flutter community**