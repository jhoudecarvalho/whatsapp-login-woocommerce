<?php
/**
 * Plugin Name: WhatsApp Login for WooCommerce
 * Plugin URI: https://cdwtech.com.br
 * Description: Permite login de usuários via WhatsApp usando link mágico
 * Version: 1.1.1
 * Author: CDWTECH
 * Author URI: https://cdwtech.com.br
 * Text Domain: whatsapp-login-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 *
 * @package WhatsAppLogin
 */

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constantes do plugin
define( 'WHATSAPP_LOGIN_VERSION', '1.1.1' );
define( 'WHATSAPP_LOGIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WHATSAPP_LOGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WHATSAPP_LOGIN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Classe principal do plugin
 */
class WhatsApp_Login_WooCommerce {

	/**
	 * Instância única do plugin (Singleton)
	 *
	 * @var WhatsApp_Login_WooCommerce
	 */
	private static $instance = null;

	/**
	 * Retorna instância única do plugin
	 *
	 * @return WhatsApp_Login_WooCommerce
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construtor privado (Singleton)
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Inicializa o plugin
	 */
	private function init() {
		// Verifica se WooCommerce está ativo
		add_action( 'plugins_loaded', array( $this, 'check_woocommerce' ) );
		
		// Hook de ativação
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		
		// Hook de desativação
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Verifica se WooCommerce está ativo
	 */
	public function check_woocommerce() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		// Carrega classes do plugin
		$this->load_dependencies();

		// Inicializa componentes
		$this->init_components();
	}

	/**
	 * Carrega dependências do plugin
	 */
	private function load_dependencies() {
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-whatsapp-login.php';
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-login-handler.php';
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-token-manager.php';
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-whatsapp-api.php';
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-admin-settings.php';
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-database.php';
	}

	/**
	 * Inicializa componentes do plugin
	 */
	private function init_components() {
		// Inicializa handler de login
		WhatsAppLogin\Login_Handler::get_instance();

		// Inicializa admin se estiver no admin
		if ( is_admin() ) {
			WhatsAppLogin\Admin_Settings::get_instance();
		}
	}

	/**
	 * Ativação do plugin
	 */
	public function activate() {
		// Verifica se WooCommerce está ativo
		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Este plugin requer WooCommerce para funcionar.', 'whatsapp-login-woocommerce' ) );
		}

		// Carrega dependências antes de usar
		require_once WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-database.php';

		// Cria tabelas do banco de dados
		\WhatsAppLogin\Database::create_tables();
		
		// Agenda cron para limpeza de tokens
		if ( ! wp_next_scheduled( 'whatsapp_login_cleanup_tokens' ) ) {
			wp_schedule_event( time(), 'hourly', 'whatsapp_login_cleanup_tokens' );
		}
	}

	/**
	 * Desativação do plugin
	 */
	public function deactivate() {
		// Remove cron
		wp_clear_scheduled_hook( 'whatsapp_login_cleanup_tokens' );
	}

	/**
	 * Exibe aviso se WooCommerce não estiver ativo
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'WhatsApp Login for WooCommerce', 'whatsapp-login-woocommerce' ); ?></strong>
				<?php esc_html_e( 'requer que o WooCommerce esteja instalado e ativo.', 'whatsapp-login-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
}

/**
 * Inicializa o plugin
 */
function whatsapp_login_woocommerce_init() {
	return WhatsApp_Login_WooCommerce::get_instance();
}

// Inicia o plugin
whatsapp_login_woocommerce_init();

