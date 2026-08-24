<?php
defined( 'ABSPATH' ) || exit;

class EWA_Chat_Widget {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
        add_action( 'wp_footer',          [ $this, 'render' ] );
    }

    public function enqueue(): void {
        if ( ! get_option( 'ewa_widget_enabled', 1 ) ) return;
        if ( empty( get_option( 'ewa_widget_phone' ) ) ) return;

        wp_enqueue_style(
            'ewa-widget',
            EWA_URL . 'assets/css/widget.css',
            [],
            EWA_VERSION
        );

        wp_enqueue_script(
            'ewa-widget',
            EWA_URL . 'assets/js/widget.js',
            [],
            EWA_VERSION,
            true
        );

        wp_localize_script( 'ewa-widget', 'ewaConfig', [
            'phone'    => get_option( 'ewa_widget_phone', '' ),
            'message'  => get_option( 'ewa_widget_message', 'Olá! Vim pelo site e gostaria de mais informações.' ),
            'color'    => get_option( 'ewa_widget_color', '#25d366' ),
            'position' => get_option( 'ewa_widget_position', 'bottom-right' ),
        ] );
    }

    public function render(): void {
        if ( ! get_option( 'ewa_widget_enabled', 1 ) ) return;

        $phone    = get_option( 'ewa_widget_phone', '' );
        $label    = get_option( 'ewa_widget_label', 'Fale conosco' );
        $color    = get_option( 'ewa_widget_color', '#25d366' );
        $position = get_option( 'ewa_widget_position', 'bottom-right' );

        if ( empty( $phone ) ) return;

        $side = $position === 'bottom-left' ? 'left: 24px;' : 'right: 24px;';
        ?>
        <div id="ewa-widget"
             class="ewa-widget ewa-<?php echo esc_attr( $position ); ?>"
             style="<?php echo esc_attr( $side ); ?>">

            <div class="ewa-tooltip"><?php echo esc_html( $label ); ?></div>

            <button class="ewa-btn"
                    style="background-color: <?php echo esc_attr( $color ); ?>;"
                    aria-label="<?php echo esc_attr( $label ); ?>"
                    data-phone="<?php echo esc_attr( $phone ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="28" height="28" aria-hidden="true">
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.168 1.618 5.955L0 24l6.234-1.594A11.942 11.942 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm6.18 16.783c-.261.733-1.524 1.4-2.1 1.448-.575.05-1.123.258-3.783-.788-3.195-1.264-5.227-4.521-5.386-4.732-.158-.211-1.29-1.714-1.29-3.269s.816-2.317 1.106-2.633c.29-.317.632-.396.843-.396l.606.011c.194.009.454-.074.711.543.263.635.893 2.19.972 2.349.078.158.13.342.026.55-.103.208-.155.338-.307.52-.152.183-.32.409-.457.549-.153.154-.312.322-.134.631.178.309.791 1.305 1.699 2.113 1.167 1.04 2.15 1.362 2.459 1.516.309.153.49.128.671-.077.181-.205.774-.903 .98-1.213.206-.31.411-.258.693-.155.282.103 1.793.846 2.101.999.308.153.514.23.589.358.075.128.075.741-.186 1.456z"/>
                </svg>
            </button>
        </div>
        <?php
    }
}
