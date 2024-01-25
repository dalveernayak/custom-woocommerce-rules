<?php
/*
Plugin Name: Custom WooCommerce Dependency
Description: Custom plugin for WooCommerce to handle product purchase dependencies.
Version: 1.0
Author: Dalveer Nayak Wordpress Developer
*/

defined('ABSPATH') or die('No direct access allowed!');

/**
 * Custom function to check if the user has purchased a specific product
 *
 * @param string $product_sku Product SKU to check
 * @return bool Whether the user has purchased the specified product
 */
function has_purchased_product($product_sku) {
    $product_id = wc_get_product_id_by_sku($product_sku);

    if ($product_id) {
        $current_user = wp_get_current_user();
        return wc_customer_bought_product($current_user->user_email, $current_user->ID, $product_id);
    }

    return false;
}

/**
 * Custom function to restrict purchase based on previous purchases
 *
 * @param bool $purchasable Whether the product is purchasable
 * @param WC_Product $product WooCommerce product object
 * @return bool Whether the product is purchasable
 */
function restrict_purchase_based_on_dependencies($purchasable, $product) {
    $product_a_sku = 'product-a'; // Replace with the actual SKU of product A
    $product_b_sku = 'product-b'; // Replace with the actual SKU of product B
    $product_c_sku = 'productc'; // Replace with the actual SKU of product C

    // Check if the user has purchased Product A
    $has_purchased_product_a = has_purchased_product($product_a_sku);

    // Check if the user has purchased Product B
    $has_purchased_product_b = has_purchased_product($product_b_sku);

    // Apply restrictions based on previous purchases
    if ($product->get_sku() === $product_b_sku && !$has_purchased_product_a) {
        $purchasable = false;
        wc_add_notice(__('You must first purchase Product A before adding this product to your cart.'), 'error');
    } elseif ($product->get_sku() === $product_c_sku && (!$has_purchased_product_a || !$has_purchased_product_b)) {
        $purchasable = false;
        wc_add_notice(__('You must first purchase both Product A and Product B before adding this product to your cart.'), 'error');
    }

    return $purchasable;
}

add_filter('woocommerce_is_purchasable', 'restrict_purchase_based_on_dependencies', 10, 2);
