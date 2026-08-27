<?php

define( 'ABSPATH', __DIR__ . '/' );

// WordPress stubs
function get_option( string $key, mixed $default = '' ): mixed { return $default; }
function sanitize_text_field( string $v ): string              { return trim( strip_tags( $v ) ); }
function wp_strip_all_tags( string $v ): string                { return strip_tags( html_entity_decode( $v, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ); }
function wp_json_encode( mixed $v ): string|false              { return json_encode( $v ); }
function is_wp_error( mixed $v ): bool                         { return $v instanceof WP_Error; }
function wp_remote_retrieve_response_code( array $r ): int     { return $r['status'] ?? 0; }
function wp_remote_retrieve_body( array $r ): string           { return $r['body'] ?? ''; }
function get_bloginfo( string $key ): string                   { return 'Test Site'; }
function wc_get_order_status_name( string $s ): string         { return ucfirst( $s ); }

class WP_Error {
    public function __construct(
        private string $code,
        private string $message,
        private mixed $data = null
    ) {}
    public function get_error_message(): string { return $this->message; }
    public function get_error_code(): string    { return $this->code; }
}

require_once __DIR__ . '/../includes/class-evolution-api.php';
require_once __DIR__ . '/../includes/class-woo-notifications-testable.php';
