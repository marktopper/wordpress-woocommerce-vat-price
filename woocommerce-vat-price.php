<?php
/**
 * WooCommerce VAT Price
 *
 * @package     WooCommerceVatPrice
 * @author      Mark Topper
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce VAT Price Plugin
 * Version: 1.0.0
 * Description: This plugins replaces the WooCommerce's price div HTML, with two HTML div boxes, one containing the price without VAT, and the other containing the price including VAT.
 * Author: Mark Topper
 * Author URI: https://marktopper.com
 * Plugin URI: https://github.com/ulties/wordpress-woocommerce-vat-price
 * Text Domain: woocommerce-vat-price
 * License: MIT
 * License URI: https://github.com/ulties/wordpress-woocommerce-vat-price/blob/master/LICENSE
 */

/**
 * Replace WooCommerce's price element
 */
function woocommerce_vat_price() {
    global $product;

	$decimals = 0;
	$vatFactor = 1.25;

	$price = $product->price;
	$priceLabelWithVat = number_format($price * $vatFactor, $decimals, ",", ".");
	$priceLabelWithoutVat = number_format($price, $decimals, ",", ".");

	$variations = $product->get_children();
	if ($variations && $variations >= 2) {
		$minPrice = null;
		$maxPrice = null;

		foreach ($variations as $variation) {
			$productVariation = new WC_Product_Variation($variation);

			if ($minPrice == null || $minPrice > $productVariation->price) {
				$minPrice = $productVariation->price;
			}

			if ($maxPrice == null || $maxPrice < $productVariation->price) {
				$maxPrice = $productVariation->price;
			}
		}
		
		$priceLabelWithVat = number_format($minPrice * $vatFactor, $decimals, ",", ".") . ' - ' . number_format($maxPrice * $vatFactor, $decimals, ",", ".");
		$priceLabelWithoutVat = number_format($minPrice, $decimals, ",", ".") . ' - ' . number_format($maxPrice, $decimals, ",", ".");
	}

	// Price without VAT
	echo '<span class="price-without-vat"><span class="woocommerce-Price-amount amount">' . $priceLabelWithoutVat . '&nbsp;<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency() . '</span></span></span>';

	// Price including VAT
	echo '<span class="price-with-vat"><span class="woocommerce-Price-amount amount">' . $priceLabelWithVat . '&nbsp;<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency() . '</span></span></span>';
}
add_filter('woocommerce_get_price_html', 'woocommerce_vat_price', 10, 2);
