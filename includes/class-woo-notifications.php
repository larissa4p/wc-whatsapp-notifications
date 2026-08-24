<?php
defined( 'ABSPATH' ) || exit;

class EWA_Woo_Notifications {

    public function __construct() {
        add_action( 'woocommerce_order_status_changed', [ $this, 'on_status_change' ], 10, 4 );
    }

    public function on_status_change( int $order_id, string $from, string $to, \WC_Order $order ): void {
        if ( ! get_option( 'ewa_woo_enabled', 1 ) ) return;
        if ( ! get_option( "ewa_woo_{$to}_enabled", 1 ) ) return;

        $phone = $order->get_billing_phone();
        if ( empty( $phone ) ) return;

        $template = get_option( "ewa_woo_{$to}_template", '' );

        if ( empty( $template ) ) {
            $template = EWA_Settings::DEFAULT_TEMPLATES[ $to ] ?? '';
        }

        if ( empty( $template ) ) return;

        $message = $this->parse_template( $template, $order );

        $api    = new EWA_Evolution_API();
        $result = $api->send_text( $phone, $message );

        if ( is_wp_error( $result ) ) {
            $order->add_order_note(
                sprintf( '⚠️ Evolution WhatsApp: falha ao enviar mensagem — %s', $result->get_error_message() )
            );
            return;
        }

        $order->add_order_note(
            sprintf( '✅ Evolution WhatsApp: mensagem de status "%s" enviada para %s.', $to, $phone )
        );
    }

    private function parse_template( string $template, \WC_Order $order ): string {
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
