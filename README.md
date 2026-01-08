# MStore API Optimizer

A WordPress plugin that dramatically improves the performance of MStore API endpoints for large shopping carts and product search, reducing response times from 30+ seconds to under 1 second.

## 🚀 Performance Improvements

### Cart Processing (Shipping & Payment Methods)
- **Before**: 30+ seconds for 50+ items (often timeout)
- **After**: <1 second for 50+ items
- **Improvement**: 95%+ faster response times

### Product Search (New in v1.1.0)
- **Before**: 2-5 seconds per search query
- **After**: <100ms for cached searches
- **Improvement**: 95-98% faster for popular searches

## 🔧 Problems Solved

### 1. Slow Cart Processing
The MStore API plugin's `shipping_methods` and `payment_methods` endpoints suffer from severe performance issues when processing carts with many products. Each cart item triggers expensive WooCommerce callbacks, causing:

- ⏱️ 30+ second response times
- ⚠️ Frequent timeouts
- 📱 Poor mobile app user experience
- 💔 Cart abandonment

### 2. Slow Product Search
Product search queries hit the database every time, causing:

- ⏱️ 2-5 second search response times
- 🐌 Slow user experience for repeat searches
- 💾 Unnecessary database load for popular searches
- 📱 Users abandoning search due to slowness

## ✅ Solutions

### Cart Optimization
This plugin intercepts the problematic endpoints **before** MStore processes them and applies optimized batch processing:

1. **Batch Operations**: Process all cart items at once instead of individually
2. **Hook Management**: Temporarily disable expensive WooCommerce hooks during processing
3. **Single Calculation**: Calculate cart totals once instead of per-item
4. **Update-Safe**: Survives MStore API plugin updates

### Search Caching (New in v1.1.0)
Intelligent caching system for product search queries:

1. **Smart Caching**: Cache search results using WordPress Transients API
2. **Automatic Expiration**: 5-30 minute cache based on search term length
3. **Auto-Invalidation**: Cache cleared when products are updated
4. **Zero Config**: Works immediately, upgrades to Redis if available

## 📋 Features

### Core Features
- ✅ **Zero Configuration**: Works immediately after activation
- ✅ **Update-Safe**: Doesn't modify original MStore files
- ✅ **Backward Compatible**: Maintains all original functionality
- ✅ **Debug Logging**: Built-in performance monitoring
- ✅ **Lightweight**: Minimal resource usage

### Search Caching Features (v1.1.0)
- ✅ **Intelligent Caching**: Automatic result caching for search queries
- ✅ **Smart Expiration**: 5-30 min cache based on search term length
- ✅ **Auto-Invalidation**: Cache cleared on product updates
- ✅ **Native WordPress**: Uses Transients API (no plugins needed)
- ✅ **Redis Ready**: Automatically upgrades to Redis if available

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

The plugin uses WordPress REST API filters to intercept and optimize MStore endpoints:

### Cart Processing
Uses `rest_pre_dispatch` filter to intercept:
- `/wp-json/api/flutter_woo/shipping_methods`
- `/wp-json/api/flutter_woo/payment_methods`

When called, optimized handlers process the request using efficient batch operations instead of the original item-by-item approach.

### Search Caching (v1.1.0)
Uses dual-filter approach for intelligent caching:

1. **`rest_pre_dispatch`**: Checks cache before processing
   - Intercepts: `/wp-json/api/flutter_woo/products?search=...`
   - Cache hit → Return cached results (<100ms)
   - Cache miss → Continue to normal processing

2. **`rest_post_dispatch`**: Caches results after processing
   - Stores search results in WordPress Transients
   - Smart expiration: 5-30 min based on search length
   - Includes pagination, filters, and language in cache key

3. **Auto-invalidation**: Hooks into product updates
   - `woocommerce_update_product` → Clear cache
   - `woocommerce_new_product` → Clear cache
   - `woocommerce_delete_product` → Clear cache

## 📊 Performance Monitoring

The plugin includes debug logging to monitor performance:

```bash
# Check WordPress debug log
grep "MStore API Optimizer" wp-content/debug.log
```

### Cart Processing Logs
```
MStore API Optimizer DEBUG: Starting shipping_methods with 42 items
MStore API Optimizer DEBUG: Cart processing took 0.597 seconds
MStore API Optimizer DEBUG: Total shipping_methods execution time 0.6 seconds
```

### Search Caching Logs (v1.1.0)
```
# Cache miss (first search)
MStore API Optimizer: Cache miss for search 'أيفونة الدجاج' (cat: 0, page: 1) - processing query
MStore API Optimizer: Cached search results for 'أيفونة الدجاج' (length: 13, TTL: 1800s, cat: 0, page: 1)

# Cache hit (subsequent search)
MStore API Optimizer: Returned cached search results in 45.23ms for 'أيفونة الدجاج' (cat: 0, page: 1)

# Cache cleared
MStore API Optimizer: Cleared all search cache due to product update
```

### Analyzing Cache Performance
```bash
# Count cache hits vs misses
grep "Cache miss" wp-content/debug.log | wc -l
grep "Returned cached" wp-content/debug.log | wc -l

# Check cache response times
grep "Returned cached.*ms" wp-content/debug.log
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

## 📚 Documentation

- **[Search Caching Guide](docs/search-caching-optimization.md)**: Complete guide to product search caching
- **[CHANGELOG.md](CHANGELOG.md)**: Detailed version history

## 📝 Changelog

### 1.1.0 (2025-01-08)
- **NEW**: Product search caching with intelligent expiration
- **NEW**: Automatic cache invalidation on product updates
- **NEW**: Smart cache TTL based on search term length
- 95-98% faster search for cached queries
- Native WordPress Transients API (Redis-ready)

### 1.0.0
- Initial release
- Optimized shipping_methods endpoint
- Optimized payment_methods endpoint
- Added debug logging
- Update-safe architecture

## 👨‍💻 Author

**Salem Aljebaly**
- GitHub: [@salemaljebaly](https://github.com/salemaljebaly)

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