<?php
defined( 'ABSPATH' ) || exit;

class EWA_Evolution_API {

    private string $base_url;
    private string $api_key;
    private string $instance;

    public function __construct() {
        $this->base_url = rtrim( get_option( 'ewa_api_url', '' ), '/' );
        $this->api_key  = get_option( 'ewa_api_key', '' );
        $this->instance = get_option( 'ewa_instance', '' );
    }

    public function send_text( string $phone, string $text ): bool|\WP_Error {
        if ( ! $this->is_configured() ) {
            return new \WP_Error( 'ewa_not_configured', 'Evolution API não configurada.' );
        }

        $url  = "{$this->base_url}/message/sendText/{$this->instance}";
        $body = wp_json_encode( [
            'number' => $this->format_phone( $phone ),
            'text'   => $text,
        ] );

        $response = wp_remote_post( $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'apikey'       => $this->api_key,
            ],
            'body'    => $body,
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );

        if ( $code < 200 || $code >= 300 ) {
            return new \WP_Error(
                'ewa_api_error',
                sprintf( 'Erro na Evolution API: HTTP %d', $code ),
                wp_remote_retrieve_body( $response )
            );
        }

        return true;
    }

    public function check_connection(): bool|\WP_Error {
        if ( ! $this->is_configured() ) {
            return new \WP_Error( 'ewa_not_configured', 'Evolution API não configurada.' );
        }

        $url      = "{$this->base_url}/instance/connectionState/{$this->instance}";
        $response = wp_remote_get( $url, [
            'headers' => [ 'apikey' => $this->api_key ],
            'timeout' => 10,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        return isset( $body['instance']['state'] ) && $body['instance']['state'] === 'open';
    }

    public function format_phone( string $phone ): string {
        $phone = preg_replace( '/\D/', '', $phone );

        if ( strlen( $phone ) === 11 || strlen( $phone ) === 10 ) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    public function is_configured(): bool {
        return ! empty( $this->base_url )
            && ! empty( $this->api_key )
            && ! empty( $this->instance );
    }
}
