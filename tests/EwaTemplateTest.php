<?php

use PHPUnit\Framework\TestCase;

class EwaTemplateTest extends TestCase {

    private EWA_Woo_Notifications_Testable $notif;
    private object $order;

    protected function setUp(): void {
        $this->notif = new EWA_Woo_Notifications_Testable();

        $this->order = new class {
            public function get_billing_first_name(): string    { return 'Maria'; }
            public function get_order_number(): string          { return '1042'; }
            public function get_formatted_order_total(): string { return 'R$&nbsp;350,00'; }
            public function get_status(): string                { return 'processing'; }
        };
    }

    public function test_replaces_nome_variable(): void {
        $result = $this->notif->parse_template( 'Olá, {nome}!', $this->order );
        $this->assertSame( 'Olá, Maria!', $result );
    }

    public function test_replaces_pedido_variable(): void {
        $result = $this->notif->parse_template( 'Pedido #{pedido}', $this->order );
        $this->assertSame( 'Pedido #1042', $result );
    }

    public function test_strips_html_from_total(): void {
        $result = $this->notif->parse_template( 'Total: {total}', $this->order );
        $this->assertStringNotContainsString( '&nbsp;', $result );
        $this->assertStringContainsString( '350,00', $result );
    }

    public function test_replaces_site_variable(): void {
        $result = $this->notif->parse_template( 'Loja: {site}', $this->order );
        $this->assertSame( 'Loja: Test Site', $result );
    }

    public function test_replaces_all_variables_in_full_template(): void {
        $template = "Olá, {nome}! ✅ Pedido *#{pedido}* no valor de *{total}* — {status}. {site}";
        $result   = $this->notif->parse_template( $template, $this->order );

        $this->assertStringContainsString( 'Maria', $result );
        $this->assertStringContainsString( '1042', $result );
        $this->assertStringContainsString( '350,00', $result );
        $this->assertStringNotContainsString( '{nome}', $result );
        $this->assertStringNotContainsString( '{pedido}', $result );
        $this->assertStringNotContainsString( '{total}', $result );
    }

    public function test_unknown_variables_are_left_intact(): void {
        $result = $this->notif->parse_template( 'Código: {codigo}', $this->order );
        $this->assertSame( 'Código: {codigo}', $result );
    }
}
