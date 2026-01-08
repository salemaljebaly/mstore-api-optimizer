# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-01-08

### Added
- **Product Search Caching**: Intelligent caching for product search queries
- Automatic cache invalidation when products are created, updated, or deleted
- Smart cache expiration based on search term length:
  - Short searches (1-3 characters): 5-minute cache
  - Longer searches (4+ characters): 30-minute cache
- Comprehensive cache key generation including:
  - Search terms
  - Category filters
  - Pagination
  - Sorting parameters
  - Price ranges
  - Language settings
- REST API endpoint interception for `/api/flutter_woo/products`
- Detailed debug logging for cache hits and misses

### Performance Improvements
- Search response time reduced from 2-5 seconds to <100ms for cached queries
- 95-98% performance improvement for popular/repeated searches
- Sub-second response times for frequently searched terms
- Zero impact on uncached searches or catalog browsing

### Technical Features
- Uses native WordPress Transients API for caching
- Database-backed caching (works on any WordPress installation)
- Automatic upgrade path to Redis/Memcached if available
- Clean implementation without modifying MStore API plugin files
- Cache automatically cleared on product updates to ensure data freshness
- Support for multi-language searches

### Documentation
- Added search caching implementation guide
- Performance benchmarking documentation
- Cache management and troubleshooting guide

## [1.0.0] - 2025-09-02

### Added
- Initial release of MStore API Optimizer plugin
- Optimized `shipping_methods` endpoint processing
- Optimized `payment_methods` endpoint processing
- Batch cart item processing to replace individual item operations
- Smart WooCommerce hook management during processing
- Comprehensive debug logging for performance monitoring
- Update-safe architecture using `rest_pre_dispatch` filter
- Full compatibility with WooCommerce Subscriptions
- Support for WCFM Marketplace location-based shipping
- WooCommerce Multilingual & Multicurrency support
- Automatic WooCommerce session initialization

### Performance Improvements
- Reduced response time from 30+ seconds to <1 second for large carts
- 95%+ performance improvement for carts with 50+ items
- Per-item processing time reduced from ~0.5s to ~0.016s
- Single cart total calculation instead of per-item calculations

### Technical Features
- Clean endpoint interception without modifying original MStore files
- Temporary suspension of expensive WooCommerce hooks:
  - `woocommerce_add_to_cart`
  - `woocommerce_cart_loaded_from_session`
  - `woocommerce_cart_updated`
- Proper hook restoration after processing
- Execution time limit protection (60 seconds)
- Comprehensive error handling and validation

### Compatibility
- WordPress 5.0+
- WooCommerce 3.0+
- MStore API 4.0+
- PHP 7.4+
- All major payment gateways
- WooCommerce extensions and third-party plugins

### Documentation
- Comprehensive README.md with installation and usage instructions
- WordPress.org compatible readme.txt
- Performance monitoring guide
- Troubleshooting documentation
- Technical implementation details

## [Unreleased]

### Planned Features
- Multi-language support
- Advanced caching mechanisms
- Performance analytics dashboard
- Compatibility with additional WooCommerce extensions
- Unit tests and automated testing
- Performance profiling tools

---

## Support

For support, feature requests, or bug reports:
- **GitHub Issues**: https://github.com/salemaljebaly/mstore-api-optimizer/issues
- **Author Website**: https://github.com/salemaljebaly
- **Email**: salemaljebaly@gmail.com