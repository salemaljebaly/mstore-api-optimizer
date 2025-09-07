<?php
/*
Plugin Name: MStore API Optimizer
Plugin URI: https://github.com/salemaljebaly/mstore-api-optimizer
Description: Dramatically improves MStore API performance for large shopping carts, reducing response times from 30+ seconds to under 1 second.
Version: 1.0.2
Author: Salem Aljebaly
Author URI: https://github.com/salemaljebaly
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mstore-api-optimizer
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
WC requires at least: 3.0
WC tested up to: 8.0
Network: false

MStore API Optimizer is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

MStore Performance Fix is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with MStore API Optimizer. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class MStorePerformanceFix {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Only activate if MStore API plugin is active
        if (!is_plugin_active('mstore-api/mstore-api.php')) {
            return;
        }
        
        // Override the problematic endpoints
        add_filter('rest_pre_dispatch', array($this, 'override_endpoints'), 10, 3);
    }
    
    public function override_endpoints($result, $server, $request) {
        $route = $request->get_route();
        
        // Override shipping_methods endpoint
        if ($route === '/api/flutter_woo/shipping_methods') {
            return $this->handle_optimized_shipping_methods($request);
        }
        
        // Override payment_methods endpoint
        if ($route === '/api/flutter_woo/payment_methods') {
            return $this->handle_optimized_payment_methods($request);
        }
        
        // Let MStore handle all other endpoints
        return $result;
    }
    
    public function handle_optimized_shipping_methods($request) {
        // DEBUG: Start timing
        $debug_start_time = microtime(true);
        
        // Set execution time limit to prevent timeout
        set_time_limit(60);
        
        $json = file_get_contents('php://input');
        $body = json_decode($json, TRUE);
        
        // DEBUG: Log item count
        $item_count = isset($body['line_items']) ? count($body['line_items']) : 0;
        error_log("MStore API Optimizer DEBUG: Starting shipping_methods with {$item_count} items");

        // Basic permission check (simplified)
        if (!isPurchaseCodeVerified()) {
            return new WP_Error('forbidden', 'Access denied', array('status' => 403));
        }

        // Initialize WooCommerce if needed
        if (!WC()->customer) {
            wc_load_cart();
        }

        $shipping = $body["shipping"];
        WC()->customer->set_shipping_first_name($shipping["first_name"]);
        WC()->customer->set_shipping_last_name($shipping["last_name"]);
        WC()->customer->set_shipping_company($shipping["company"]);
        WC()->customer->set_shipping_address_1($shipping["address_1"]);
        WC()->customer->set_shipping_address_2($shipping["address_2"]);
        WC()->customer->set_shipping_city($shipping["city"]);
        WC()->customer->set_shipping_state($shipping["state"]);
        WC()->customer->set_shipping_postcode($shipping["postcode"]);
        WC()->customer->set_shipping_country($shipping["country"]);

        // DEBUG: Time cart processing
        $cart_start_time = microtime(true);
        
        // OPTIMIZATION: Batch add items directly to avoid expensive callbacks
        WC()->cart->empty_cart();
        
        // Disable WooCommerce hooks during batch processing
        $this->disable_wc_hooks();
        
        // Batch process items
        $failed_items = $this->batch_add_items_to_cart($body['line_items']);
        
        // Restore WooCommerce hooks
        $this->restore_wc_hooks();
        
        // Calculate totals once at the end
        WC()->cart->calculate_totals();
        
        $cart_time = microtime(true) - $cart_start_time;
        error_log("MStore API Optimizer DEBUG: Cart processing took " . round($cart_time, 3) . " seconds");
        
        // Get stock adjustments count for logging
        $stock_adjustments = WC()->session->get('mstore_stock_adjustments', array());
        $stock_count = count($stock_adjustments);
        
        if ($failed_items > 0) {
            error_log("MStore API Optimizer DEBUG: {$failed_items} items failed to add, {$stock_count} stock issues handled");
        }
        
        // Continue with shipping calculation even if some items failed
        // This allows users to get shipping for available items

        if(isset($body['coupon_lines']) && is_array($body['coupon_lines']) && count($body['coupon_lines']) > 0){
            WC()->cart->apply_coupon($body['coupon_lines'][0]['code']);
        }
        
        /* set calculation type if product is subscription to get shipping methods for subscription product have trial days */
        if (is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php')) {
            foreach ($body['line_items'] as $product) {
                $productId = absint($product['product_id']);
                $variationId = isset($product['variation_id']) ? absint($product['variation_id']) : 0;
                $product_data = wc_get_product($variationId != 0 ? $variationId : $productId);
                if (class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription($product_data)) {
                    WC_Subscriptions_Cart::set_calculation_type('recurring_total');
                    break;
                }
            }
        }
        
        if( apply_filters( 'wcfmmp_is_allow_checkout_user_location', true ) ) {
			if ( !empty($shipping["wcfmmp_user_location"]) ) {
				WC()->customer->set_props( array( 'wcfmmp_user_location' => sanitize_text_field( $shipping["wcfmmp_user_location"] ) ) );
				WC()->session->set( '_wcfmmp_user_location', sanitize_text_field( $shipping["wcfmmp_user_location"] ) );
			}
			if ( !empty($shipping["wcfmmp_user_location_lat"]) ) {
				WC()->session->set( '_wcfmmp_user_location_lat', sanitize_text_field( $shipping['wcfmmp_user_location_lat'] ) );
			}
			if ( !empty( $shipping['wcfmmp_user_location_lng'] ) ) {
				WC()->session->set( '_wcfmmp_user_location_lng', sanitize_text_field( $shipping['wcfmmp_user_location_lng'] ) );
			}
		}

        // DEBUG: Time shipping calculation
        $shipping_calc_start = microtime(true);
        $shipping_methods = WC()->shipping->calculate_shipping(WC()->cart->get_shipping_packages());
        $shipping_calc_time = microtime(true) - $shipping_calc_start;
        error_log("MStore API Optimizer DEBUG: Shipping calculation took " . round($shipping_calc_time, 3) . " seconds");
        
        $required_shipping = WC()->cart->needs_shipping() && WC()->cart->show_shipping();
        
        // NO cart clearing here - this was the original problem!

        if(count( $shipping_methods) == 0){
            return new WP_Error('no_shipping', 'No Shipping', array('required_shipping' => $required_shipping));
        }

        $results = [];
        foreach ($shipping_methods as $shipping_method) {
            $rates = $shipping_method['rates'];
            foreach ($rates as $rate) {
                $results[] = [
                    "id" => $rate->get_id(),
                    "method_id" => $rate->get_method_id(),
                    "instance_id" => $rate->get_instance_id(),
                    "label" => $rate->get_label(),
                    "cost" => $rate->get_cost(),
                    "taxes" => $rate->get_taxes(),
                    "shipping_tax" => $rate->get_shipping_tax()
                ];
            }
        }
        
        if(count( $results) == 0){
            return new WP_Error('no_shipping', 'No Shipping', array('required_shipping' => $required_shipping));
        }
        
        // Check for stock adjustments
        $stock_adjustments = WC()->session->get('mstore_stock_adjustments', array());
        WC()->session->set('mstore_stock_adjustments', null); // Clear after reading
        
        // DEBUG: Total execution time
        $total_time = microtime(true) - $debug_start_time;
        error_log("MStore API Optimizer DEBUG: Total shipping_methods execution time " . round($total_time, 3) . " seconds for {$item_count} items");
        error_log("MStore API Optimizer DEBUG: Found " . count($results) . " shipping methods");
        if (!empty($stock_adjustments)) {
            error_log("MStore API Optimizer DEBUG: " . count($stock_adjustments) . " stock adjustments made");
        }
        
        // Return results with stock adjustment info if needed
        if (!empty($stock_adjustments)) {
            // Generate user-friendly message based on stock issue types
            $out_of_stock_count = 0;
            $limited_stock_count = 0;
            $not_found_count = 0;
            
            foreach ($stock_adjustments as $adjustment) {
                $reason = $adjustment['reason'] ?? 'unknown';
                if ($reason === 'out_of_stock') {
                    $out_of_stock_count++;
                } elseif ($reason === 'limited_stock') {
                    $limited_stock_count++;
                } elseif ($reason === 'not_found') {
                    $not_found_count++;
                }
            }
            
            $message_parts = [];
            if ($out_of_stock_count > 0) {
                $message_parts[] = $out_of_stock_count . "_items_out_of_stock";
            }
            if ($limited_stock_count > 0) {
                $message_parts[] = $limited_stock_count . "_items_limited_stock";
            }
            if ($not_found_count > 0) {
                $message_parts[] = $not_found_count . "_items_not_found";
            }
            
            $message = implode(', ', $message_parts);
            
            return array(
                'shipping_methods' => $results,
                'stock_adjustments' => $stock_adjustments,
                'message' => $message
            );
        }
        
        return $results;
    }
    
    public function handle_optimized_payment_methods($request) {
        // DEBUG: Start timing
        $debug_start_time = microtime(true);
        
        // Set execution time limit to prevent timeout
        set_time_limit(60);
        
        $json = file_get_contents('php://input');
        $body = json_decode($json, TRUE);
        
        // DEBUG: Log item count
        $item_count = isset($body['line_items']) ? count($body['line_items']) : 0;
        error_log("MStore API Optimizer DEBUG: Starting payment_methods with {$item_count} items");

        // Basic permission check (simplified)
        if (!isPurchaseCodeVerified()) {
            return new WP_Error('forbidden', 'Access denied', array('status' => 403));
        }

        // Initialize WooCommerce if needed
        if (!WC()->customer) {
            wc_load_cart();
        }

        $shipping = $body["shipping"];
        if (isset($shipping)) {
            WC()->customer->set_shipping_first_name($shipping["first_name"]);
            WC()->customer->set_shipping_last_name($shipping["last_name"]);
            WC()->customer->set_shipping_company($shipping["company"]);
            WC()->customer->set_shipping_address_1($shipping["address_1"]);
            WC()->customer->set_shipping_address_2($shipping["address_2"]);
            WC()->customer->set_shipping_city($shipping["city"]);
            WC()->customer->set_shipping_state($shipping["state"]);
            WC()->customer->set_shipping_postcode($shipping["postcode"]);
            WC()->customer->set_shipping_country($shipping["country"]);
        }
        
        //Fix to show COD based on the country for WooCommerce Multilingual & Multicurrency
        if(is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php') && !is_plugin_active('elementor-pro/elementor-pro.php')){
			$_GET['wc-ajax'] = 'update_order_review';
            $_POST['country'] = $shipping["country"];
		}
        
        // DEBUG: Time cart processing
        $cart_start_time = microtime(true);
        
        // OPTIMIZATION: Batch add items directly to avoid expensive callbacks
        WC()->cart->empty_cart();
        
        // Disable WooCommerce hooks during batch processing
        $this->disable_wc_hooks();
        
        // Batch process items
        $failed_items = $this->batch_add_items_to_cart($body['line_items']);
        
        // Restore WooCommerce hooks
        $this->restore_wc_hooks();
        
        // Calculate totals once at the end
        WC()->cart->calculate_totals();
        
        $cart_time = microtime(true) - $cart_start_time;
        error_log("MStore API Optimizer DEBUG: Cart processing took " . round($cart_time, 3) . " seconds");
        
        if ($failed_items > 0) {
            return new WP_Error('invalid_item', "Failed to add {$failed_items} items", array('status' => 400));
        }
        
        if(isset($body['coupon_lines']) && is_array($body['coupon_lines']) && count($body['coupon_lines']) > 0){
            WC()->cart->apply_coupon($body['coupon_lines'][0]['code']);
        }
        if (isset($body["shipping_lines"]) && !empty($body["shipping_lines"])) {
            $shippings = [];
            foreach ($body["shipping_lines"] as $shipping_line) {
                $shippings[] = $shipping_line["method_id"];
            }
            WC()->session->set('chosen_shipping_methods', $shippings);
        }
        $payment_methods = WC()->payment_gateways->get_available_payment_gateways();
        
        // NO cart clearing here - this was the original problem!
        
        $results = [];
        foreach ($payment_methods as $key => $value) {
            $results[] = ["id" => $value->id, "title" => $value->title, "method_title" => $value->method_title, "description" => $value->description];
        }
        
        // DEBUG: Total execution time
        $total_time = microtime(true) - $debug_start_time;
        error_log("MStore API Optimizer DEBUG: Total payment_methods execution time " . round($total_time, 3) . " seconds for {$item_count} items");
        error_log("MStore API Optimizer DEBUG: Found " . count($results) . " payment methods");
        
        return $results;
    }
    
    private function batch_add_items_to_cart($line_items) {
        $failed_items = 0;
        $stock_issues = array();
        
        foreach ($line_items as $item) {
            $productId = absint($item['product_id']);
            $requested_qty = intval($item['quantity']);
            $variationId = isset($item['variation_id']) ? absint($item['variation_id']) : 0;
            
            $attributes = array();
            if (isset($item["meta_data"])) {
                foreach ($item["meta_data"] as $meta) {
                    if($meta["value"] != null){
                        $attributes[strtolower($meta["key"])] = $meta["value"];
                    }
                }
            }
            
            // Check product availability first to avoid unnecessary add_to_cart attempts
            $product = wc_get_product($variationId ?: $productId);
            
            if (!$product) {
                // Product doesn't exist - skip it entirely
                $stock_issues[] = array(
                    'product_id' => $productId,
                    'requested' => $requested_qty,
                    'available' => 0,
                    'product_name' => 'Product not found',
                    'reason' => 'not_found'
                );
                error_log("MStore API Optimizer: Skipped non-existent product {$productId}");
                continue;
            }
            
            if (!$product->is_in_stock()) {
                // Product is out of stock - skip it entirely and notify user
                $stock_issues[] = array(
                    'product_id' => $productId,
                    'requested' => $requested_qty,
                    'available' => 0,
                    'product_name' => $product->get_name(),
                    'reason' => 'out_of_stock'
                );
                error_log("MStore API Optimizer: Skipped out-of-stock product {$productId}");
                continue;
            }
            
            $available = $product->get_stock_quantity();
            if ($available !== null && $available < $requested_qty) {
                // Limited stock - try with available quantity
                if ($available > 0) {
                    $result = WC()->cart->add_to_cart($productId, $available, $variationId, $attributes);
                    if ($result) {
                        $stock_issues[] = array(
                            'product_id' => $productId,
                            'requested' => $requested_qty,
                            'available' => $available,
                            'product_name' => $product->get_name(),
                            'reason' => 'limited_stock'
                        );
                        error_log("MStore API Optimizer: Adjusted quantity for product {$productId} from {$requested_qty} to {$available}");
                    } else {
                        $failed_items++;
                        error_log("MStore API Optimizer: Failed to add product {$productId} even with available quantity {$available}");
                    }
                } else {
                    // Available is 0, treat as out of stock
                    $stock_issues[] = array(
                        'product_id' => $productId,
                        'requested' => $requested_qty,
                        'available' => 0,
                        'product_name' => $product->get_name(),
                        'reason' => 'out_of_stock'
                    );
                    error_log("MStore API Optimizer: Skipped zero-stock product {$productId}");
                }
            } else {
                // Try original quantity (unlimited stock or sufficient stock)
                $result = WC()->cart->add_to_cart($productId, $requested_qty, $variationId, $attributes);
                if (!$result) {
                    $failed_items++;
                    error_log("MStore API Optimizer: Failed to add product {$productId} with sufficient stock - other error");
                }
            }
        }
        
        // Store stock adjustments for the response
        if (!empty($stock_issues)) {
            WC()->session->set('mstore_stock_adjustments', $stock_issues);
        }
        
        return $failed_items;
    }
    
    private $removed_actions = array();
    
    private function disable_wc_hooks() {
        $hooks_to_remove = array(
            'woocommerce_add_to_cart',
            'woocommerce_cart_loaded_from_session',
            'woocommerce_cart_updated'
        );
        
        foreach ($hooks_to_remove as $hook) {
            $this->removed_actions[$hook] = $GLOBALS['wp_filter'][$hook] ?? null;
            if ($this->removed_actions[$hook]) {
                $GLOBALS['wp_filter'][$hook] = new WP_Hook();
            }
        }
    }
    
    private function restore_wc_hooks() {
        foreach ($this->removed_actions as $hook => $actions) {
            if ($actions) {
                $GLOBALS['wp_filter'][$hook] = $actions;
            }
        }
        $this->removed_actions = array(); // Clear for next use
    }
}

// Initialize the plugin
new MStorePerformanceFix();