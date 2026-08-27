<?php

defined( 'ABSPATH' ) || exit;

class EWA_Woo_Notifications_Testable {

    public function parse_template( string $template, object $order ): string {
        $vars = [
            '{nome}'   => $order->get_billing_first_name(),
            '{pedido}' => $order->get_order_number(),
            '{total}'  => wp_strip_all_tags( $order->get_formatted_order_total() ),
            '{status}' => wc_get_order_status_name( $order->get_status() ),
            '{site}'   => get_bloginfo( 'name' ),
        ];

        return str_replace( array_keys( $vars ), array_values( $vars ), $template );
    }
}
