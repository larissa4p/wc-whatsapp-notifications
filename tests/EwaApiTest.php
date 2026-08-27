<?php

use PHPUnit\Framework\TestCase;

class EwaApiTest extends TestCase {

    public function test_format_phone_adds_country_code_to_11_digits(): void {
        $api = new EWA_Evolution_API();
        $this->assertSame( '5561999998888', $api->format_phone( '61999998888' ) );
    }

    public function test_format_phone_adds_country_code_to_10_digits(): void {
        $api = new EWA_Evolution_API();
        $this->assertSame( '5561999998888', $api->format_phone( '61999998888' ) );
    }

    public function test_format_phone_strips_non_digits(): void {
        $api = new EWA_Evolution_API();
        $this->assertSame( '5561999998888', $api->format_phone( '(61) 99999-8888' ) );
    }

    public function test_format_phone_keeps_full_international_number(): void {
        $api = new EWA_Evolution_API();
        $this->assertSame( '5561999998888', $api->format_phone( '5561999998888' ) );
    }

    public function test_is_configured_returns_false_when_empty(): void {
        $api = new EWA_Evolution_API();
        $this->assertFalse( $api->is_configured() );
    }

    public function test_send_text_returns_wp_error_when_not_configured(): void {
        $api    = new EWA_Evolution_API();
        $result = $api->send_text( '5561999998888', 'Olá!' );

        $this->assertTrue( is_wp_error( $result ) );
        $this->assertSame( 'ewa_not_configured', $result->get_error_code() );
    }

    public function test_check_connection_returns_wp_error_when_not_configured(): void {
        $api    = new EWA_Evolution_API();
        $result = $api->check_connection();

        $this->assertTrue( is_wp_error( $result ) );
    }
}
