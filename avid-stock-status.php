<?php
/*
Plugin Name: 07 AVID Stock Status Indicator
Description: Displays stock availability and the ID of products on the archive page (Only for Admins and Store Managers)
Version: 1.0
Author: AVID-MIS
Author URI: www.avid.com.ph
*/

function is_shop_manager() {
    global $current_user;
    $user = wp_get_current_user();
    if ( isset( $user->roles[0] ) && $user->roles[0] == 'shop_manager' ) {
        return true;
    } else {
        return false;
    }
}
function is_admin_not_dashboard() {
    global $current_user;
    $user = wp_get_current_user();
    if ( isset( $user->roles[0] ) && $user->roles[0] == 'administrator' ) {
        return true;
    } else {
        return false;
    }
}

function stock_status_indicator_styles() {

    wp_enqueue_style('plugin_styling', get_template_directory_uri() . '/woocommerce.css');

    $logo_css = "
    .stock-status-indicator {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding-left: 4px;
        padding-right: 4px;
        top: 0;
        left: 0;
    }
    
    .instock-true {
        background: green;
    }
    
    .instock-false {
        background: red;
    }
    
    .product-id-copy {
        color: white;
        font-size: 13px;
        margin: 0;
    }
    
    .label-prevent-select {
        user-select: none;
    }
    ";
    wp_add_inline_style('plugin_styling', $logo_css);
}
add_action('wp_enqueue_scripts', 'stock_status_indicator_styles');

function get_stock_availability(){
if(is_shop_manager() || is_admin_not_dashboard()){
    global $product;
    $prod_id = $product->get_id();
    $green_stock= '<div class="instock-true stock-status-indicator"><p class="product-id-copy label-prevent-select">ID:</p><p class="product-id-copy">' . $prod_id . '</p></div>';
    $red_stock= '<div class="instock-false stock-status-indicator"><p class="product-id-copy label-prevent-select">ID:</p><p class="product-id-copy">' . $prod_id . '</p></div>';

    if ($product->is_type('simple')){
        if ($product->is_in_stock()) {
        echo $green_stock;
        }
        else {
        echo $red_stock;
        }
    }
//for variable
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        $var_has_stock = false;

        foreach ($variations as $variation) {
            $variation_obj = wc_get_product($variation['variation_id' ]);
            if ($variation_obj->is_in_stock()) {
                $var_has_stock = true;
                break;
            }
        }

        if ($var_has_stock) {
            echo $green_stock;
        } else {
            echo $red_stock;
        }
    }
    }
}

add_action('woocommerce_before_shop_loop_item', 'get_stock_availability');