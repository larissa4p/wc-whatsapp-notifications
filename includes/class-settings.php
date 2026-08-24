<?php
defined( 'ABSPATH' ) || exit;

class EWA_Settings {

    const WOO_STATUSES = [
        'pending'    => 'Pendente',
        'processing' => 'Processando',
        'on-hold'    => 'Aguardando',
        'completed'  => 'Concluído',
        'cancelled'  => 'Cancelado',
        'refunded'   => 'Reembolsado',
        'failed'     => 'Falhou',
    ];

    const DEFAULT_TEMPLATES = [
        'pending'    => "Olá, {nome}! 👋 Recebemos seu pedido *#{pedido}* no valor de *{total}*.\nAssim que o pagamento for confirmado, avisamos por aqui!",
        'processing' => "Olá, {nome}! ✅ Pagamento confirmado. Seu pedido *#{pedido}* já está sendo preparado.",
        'on-hold'    => "Olá, {nome}! ⏳ Seu pedido *#{pedido}* está aguardando confirmação de pagamento.",
        'completed'  => "Olá, {nome}! 🎉 Seu pedido *#{pedido}* foi concluído. Obrigada pela compra!",
        'cancelled'  => "Olá, {nome}! ❌ Seu pedido *#{pedido}* foi cancelado. Qualquer dúvida, fale conosco.",
        'refunded'   => "Olá, {nome}! 💰 O reembolso do pedido *#{pedido}* foi processado.",
        'failed'     => "Olá, {nome}! ⚠️ Houve um problema com seu pedido *#{pedido}*. Entre em contato conosco.",
    ];

    public function __construct() {
        add_action( 'admin_menu',    [ $this, 'add_menu' ] );
        add_action( 'admin_init',    [ $this, 'register_settings' ] );
        add_action( 'admin_notices', [ $this, 'connection_notice' ] );
    }

    public function add_menu(): void {
        add_options_page(
            'Evolution WhatsApp',
            'Evolution WhatsApp',
            'manage_options',
            EWA_SLUG,
            [ $this, 'render_page' ]
        );
    }

    public function register_settings(): void {
        register_setting( 'ewa_settings', 'ewa_api_url',  [ 'sanitize_callback' => 'esc_url_raw' ] );
        register_setting( 'ewa_settings', 'ewa_api_key',  [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'ewa_settings', 'ewa_instance', [ 'sanitize_callback' => 'sanitize_text_field' ] );

        register_setting( 'ewa_settings', 'ewa_widget_enabled',  [ 'sanitize_callback' => 'absint' ] );
        register_setting( 'ewa_settings', 'ewa_widget_phone',    [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'ewa_settings', 'ewa_widget_message',  [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'ewa_settings', 'ewa_widget_label',    [ 'sanitize_callback' => 'sanitize_text_field' ] );
        register_setting( 'ewa_settings', 'ewa_widget_color',    [ 'sanitize_callback' => 'sanitize_hex_color' ] );
        register_setting( 'ewa_settings', 'ewa_widget_position', [ 'sanitize_callback' => 'sanitize_text_field' ] );

        register_setting( 'ewa_settings', 'ewa_woo_enabled', [ 'sanitize_callback' => 'absint' ] );

        foreach ( array_keys( self::WOO_STATUSES ) as $status ) {
            register_setting( 'ewa_settings', "ewa_woo_{$status}_enabled",  [ 'sanitize_callback' => 'absint' ] );
            register_setting( 'ewa_settings', "ewa_woo_{$status}_template", [ 'sanitize_callback' => 'sanitize_textarea_field' ] );
        }
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) return;
        ?>
        <div class="wrap">
            <h1>⚡ Evolution WhatsApp</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'ewa_settings' ); ?>

                <?php $this->render_section_api(); ?>
                <?php $this->render_section_widget(); ?>
                <?php $this->render_section_woo(); ?>

                <?php submit_button( 'Salvar configurações' ); ?>
            </form>
        </div>
        <?php
    }

    private function render_section_api(): void {
        $api = new EWA_Evolution_API();
        ?>
        <h2>🔌 Configuração da API</h2>
        <table class="form-table">
            <tr>
                <th>URL da Evolution API</th>
                <td>
                    <input type="url" name="ewa_api_url" class="regular-text"
                        value="<?php echo esc_attr( get_option( 'ewa_api_url' ) ); ?>"
                        placeholder="https://api.seuservidor.com" />
                </td>
            </tr>
            <tr>
                <th>API Key</th>
                <td>
                    <input type="password" name="ewa_api_key" class="regular-text"
                        value="<?php echo esc_attr( get_option( 'ewa_api_key' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th>Nome da Instância</th>
                <td>
                    <input type="text" name="ewa_instance" class="regular-text"
                        value="<?php echo esc_attr( get_option( 'ewa_instance' ) ); ?>"
                        placeholder="minha-instancia" />
                    <?php if ( $api->is_configured() ) : ?>
                        <p class="description">
                            <?php
                            $connected = $api->check_connection();
                            if ( true === $connected ) {
                                echo '<span style="color:#00a32a">✅ Instância conectada</span>';
                            } else {
                                echo '<span style="color:#d63638">❌ Instância desconectada ou erro</span>';
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <hr>
        <?php
    }

    private function render_section_widget(): void {
        ?>
        <h2>💬 Widget de Chat</h2>
        <table class="form-table">
            <tr>
                <th>Ativar widget</th>
                <td>
                    <label>
                        <input type="checkbox" name="ewa_widget_enabled" value="1"
                            <?php checked( 1, get_option( 'ewa_widget_enabled', 1 ) ); ?> />
                        Exibir botão flutuante de WhatsApp no site
                    </label>
                </td>
            </tr>
            <tr>
                <th>Número do WhatsApp</th>
                <td>
                    <input type="text" name="ewa_widget_phone" class="regular-text"
                        value="<?php echo esc_attr( get_option( 'ewa_widget_phone' ) ); ?>"
                        placeholder="5561999999999" />
                    <p class="description">DDI + DDD + número, sem espaços ou símbolos.</p>
                </td>
            </tr>
            <tr>
                <th>Mensagem pré-preenchida</th>
                <td>
                    <input type="text" name="ewa_widget_message" class="large-text"
                        value="<?php echo esc_attr( get_option( 'ewa_widget_message', 'Olá! Vim pelo site e gostaria de mais informações.' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th>Label do botão</th>
                <td>
                    <input type="text" name="ewa_widget_label"
                        value="<?php echo esc_attr( get_option( 'ewa_widget_label', 'Fale conosco' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th>Cor do botão</th>
                <td>
                    <input type="color" name="ewa_widget_color"
                        value="<?php echo esc_attr( get_option( 'ewa_widget_color', '#25d366' ) ); ?>" />
                </td>
            </tr>
            <tr>
                <th>Posição</th>
                <td>
                    <select name="ewa_widget_position">
                        <?php foreach ( [ 'bottom-right' => 'Inferior direito', 'bottom-left' => 'Inferior esquerdo' ] as $val => $label ) : ?>
                            <option value="<?php echo esc_attr( $val ); ?>"
                                <?php selected( get_option( 'ewa_widget_position', 'bottom-right' ), $val ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <hr>
        <?php
    }

    private function render_section_woo(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            echo '<p>⚠️ <strong>WooCommerce não está ativo.</strong> Ative o WooCommerce para usar as notificações.</p>';
            return;
        }
        ?>
        <h2>🛒 Notificações WooCommerce</h2>
        <p class="description">
            Variáveis disponíveis nos templates:
            <code>{nome}</code> <code>{pedido}</code> <code>{total}</code> <code>{status}</code> <code>{site}</code>
        </p>
        <table class="form-table">
            <tr>
                <th>Ativar notificações</th>
                <td>
                    <label>
                        <input type="checkbox" name="ewa_woo_enabled" value="1"
                            <?php checked( 1, get_option( 'ewa_woo_enabled', 1 ) ); ?> />
                        Enviar WhatsApp ao cliente quando o status do pedido mudar
                    </label>
                </td>
            </tr>
        </table>

        <h3>Templates por status</h3>
        <?php foreach ( self::WOO_STATUSES as $status => $label ) : ?>
            <h4 style="margin: 16px 0 8px;"><?php echo esc_html( $label ); ?></h4>
            <table class="form-table" style="margin-top:0">
                <tr>
                    <th style="width:180px">Ativar</th>
                    <td>
                        <input type="checkbox" name="ewa_woo_<?php echo esc_attr( $status ); ?>_enabled" value="1"
                            <?php checked( 1, get_option( "ewa_woo_{$status}_enabled", 1 ) ); ?> />
                    </td>
                </tr>
                <tr>
                    <th>Mensagem</th>
                    <td>
                        <textarea name="ewa_woo_<?php echo esc_attr( $status ); ?>_template"
                            rows="3" class="large-text"><?php
                            echo esc_textarea( get_option(
                                "ewa_woo_{$status}_template",
                                self::DEFAULT_TEMPLATES[ $status ] ?? ''
                            ) );
                        ?></textarea>
                    </td>
                </tr>
            </table>
        <?php endforeach; ?>
        <?php
    }

    public function connection_notice(): void {
        $screen = get_current_screen();
        if ( ! $screen || $screen->id !== 'settings_page_' . EWA_SLUG ) return;

        $api = new EWA_Evolution_API();
        if ( ! $api->is_configured() ) {
            echo '<div class="notice notice-warning"><p>⚡ <strong>Evolution WhatsApp:</strong> Configure a URL da API, a API Key e o nome da instância para começar.</p></div>';
        }
    }
}
