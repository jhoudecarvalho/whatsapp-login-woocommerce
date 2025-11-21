<?php
/**
 * Classe para gerenciar tokens de login
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Token_Manager
 */
class Token_Manager {

	/**
	 * Instância única (Singleton)
	 *
	 * @var Token_Manager
	 */
	private static $instance = null;

	/**
	 * Retorna instância única
	 *
	 * @return Token_Manager
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
		// Agenda limpeza de tokens expirados
		add_action( 'whatsapp_login_cleanup_tokens', array( $this, 'cleanup_expired_tokens' ) );
	}

	/**
	 * Gera token único
	 *
	 * @return string
	 */
	public function generate_token() {
		return bin2hex( random_bytes( 16 ) );
	}

	/**
	 * Cria token no banco de dados
	 *
	 * @param string $phone Número de telefone.
	 * @param int    $expiration_minutes Minutos até expirar.
	 * @return string|false Token criado ou false em caso de erro.
	 */
	public function create_token( $phone, $expiration_minutes = 15 ) {
		global $wpdb;

		$table_name = Database::get_tokens_table();
		$token = $this->generate_token();
		$now = current_time( 'mysql' );
		$expires_at = date( 'Y-m-d H:i:s', strtotime( $now . " +{$expiration_minutes} minutes" ) );
		$ip_address = $this->get_client_ip();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

		$result = $wpdb->insert(
			$table_name,
			array(
				'token'      => $token,
				'phone'      => sanitize_text_field( $phone ),
				'user_id'    => null,
				'created_at' => $now,
				'expires_at' => $expires_at,
				'used_at'    => null,
				'ip_address' => $ip_address,
				'user_agent' => $user_agent,
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( $result ) {
			return $token;
		}

		return false;
	}

	/**
	 * Valida token
	 *
	 * @param string $token Token a validar.
	 * @return object|false Objeto do token ou false se inválido.
	 */
	public function validate_token( $token ) {
		global $wpdb;

		$table_name = Database::get_tokens_table();
		$now = current_time( 'mysql' );

		$token_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table_name} 
				WHERE token = %s 
				AND expires_at > %s 
				AND used_at IS NULL 
				LIMIT 1",
				$token,
				$now
			)
		);

		return $token_data;
	}

	/**
	 * Marca token como usado
	 *
	 * @param string $token Token a marcar.
	 * @return bool
	 */
	public function mark_token_as_used( $token ) {
		global $wpdb;

		$table_name = Database::get_tokens_table();
		$now = current_time( 'mysql' );

		$result = $wpdb->update(
			$table_name,
			array( 'used_at' => $now ),
			array( 'token' => $token ),
			array( '%s' ),
			array( '%s' )
		);

		return false !== $result;
	}

	/**
	 * Verifica rate limiting
	 *
	 * @param string $phone Número de telefone.
	 * @param int    $max_attempts Máximo de tentativas.
	 * @param int    $time_window Janela de tempo em minutos.
	 * @return bool|WP_Error True se pode enviar, WP_Error se excedeu limite.
	 */
	public function check_rate_limit( $phone, $max_attempts = 3, $time_window = 60 ) {
		global $wpdb;

		$table_name = Database::get_tokens_table();
		$time_limit = date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) . " -{$time_window} minutes" ) );

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_name} 
				WHERE phone = %s 
				AND created_at > %s",
				$phone,
				$time_limit
			)
		);

		if ( $count >= $max_attempts ) {
			return new \WP_Error(
				'rate_limit_exceeded',
				sprintf(
					/* translators: %d: número máximo de tentativas, %d: janela de tempo em minutos */
					__( 'Limite de tentativas excedido. Máximo de %d tentativas por %d minutos. Tente novamente mais tarde.', 'whatsapp-login-woocommerce' ),
					$max_attempts,
					$time_window
				)
			);
		}

		return true;
	}

	/**
	 * Limpa tokens expirados
	 */
	public function cleanup_expired_tokens() {
		global $wpdb;

		$table_name = Database::get_tokens_table();
		$now = current_time( 'mysql' );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table_name} 
				WHERE expires_at < %s 
				OR (used_at IS NOT NULL AND used_at < DATE_SUB(%s, INTERVAL 7 DAY))",
				$now,
				$now
			)
		);
	}

	/**
	 * Obtém IP do cliente
	 *
	 * @return string
	 */
	private function get_client_ip() {
		$ip_keys = array(
			'HTTP_CF_CONNECTING_IP', // Cloudflare
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',
		);

		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( $_SERVER[ $key ] );
				// Se for lista de IPs (X-Forwarded-For), pega o primeiro
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				return $ip;
			}
		}

		return '0.0.0.0';
	}
}

