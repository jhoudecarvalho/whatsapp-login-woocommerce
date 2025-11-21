<?php
/**
 * Classe principal do plugin
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WhatsApp_Login
 */
class WhatsApp_Login {

	/**
	 * Instância única (Singleton)
	 *
	 * @var WhatsApp_Login
	 */
	private static $instance = null;

	/**
	 * Retorna instância única
	 *
	 * @return WhatsApp_Login
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
		// Esta classe pode ser usada para funcionalidades futuras
	}
}

