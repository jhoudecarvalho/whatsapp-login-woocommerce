<?php
/**
 * Classe para configurações administrativas
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Admin_Settings
 */
class Admin_Settings {

	/**
	 * Instância única (Singleton)
	 *
	 * @var Admin_Settings
	 */
	private static $instance = null;

	/**
	 * WhatsApp API
	 *
	 * @var WhatsApp_API
	 */
	private $whatsapp_api;

	/**
	 * Retorna instância única
	 *
	 * @return Admin_Settings
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construtor
	 */
	private function __construct() {
		$this->whatsapp_api = WhatsApp_API::get_instance();

		// Adiciona página de configurações (prioridade alta para garantir que seja executado)
		// Usa 'init' para garantir que WooCommerce está carregado
		add_action( 'init', array( $this, 'register_settings_page' ), 20 );
		
		// Enqueue scripts e styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Registra página de configurações
	 */
	public function register_settings_page() {
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ), 20 );
		}
	}

	/**
	 * Adiciona página de configurações
	 *
	 * @param array $settings Páginas de configurações existentes.
	 * @return array
	 */
	public function add_settings_page( $settings ) {
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
		
		// Verifica se WooCommerce está ativo
		if ( ! class_exists( 'WC_Settings_Page' ) ) {
			return $settings;
		}
		
		// Carrega a classe de settings page
		$settings_page_file = WHATSAPP_LOGIN_PLUGIN_DIR . 'includes/class-settings-page.php';
		if ( file_exists( $settings_page_file ) ) {
			$settings_page = include $settings_page_file;
			if ( $settings_page && is_object( $settings_page ) ) {
				$settings[] = $settings_page;
			}
		}
		
		return $settings;
	}

	/**
	 * Enqueue scripts e styles do admin
	 *
	 * @param string $hook Hook da página atual.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}

		if ( ! isset( $_GET['tab'] ) || 'whatsapp_login' !== $_GET['tab'] ) {
			return;
		}

		wp_enqueue_style(
			'whatsapp-login-admin',
			WHATSAPP_LOGIN_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			WHATSAPP_LOGIN_VERSION
		);
	}

	/**
	 * Sanitiza URL
	 *
	 * @param string $url URL a sanitizar.
	 * @return string
	 */
	public function sanitize_url( $url ) {
		return esc_url_raw( trim( $url ) );
	}

	/**
	 * Sanitiza token
	 *
	 * @param string $token Token a sanitizar.
	 * @return string
	 */
	public function sanitize_token( $token ) {
		return sanitize_text_field( trim( $token ) );
	}
}

