<?php
/**
 * Classe para comunicação com API WhatsApp
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe WhatsApp_API
 */
class WhatsApp_API {

	/**
	 * Instância única (Singleton)
	 *
	 * @var WhatsApp_API
	 */
	private static $instance = null;

	/**
	 * URL base da API
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * Token de autenticação
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Retorna instância única
	 *
	 * @return WhatsApp_API
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
		$this->load_settings();
	}

	/**
	 * Carrega configurações da API
	 */
	private function load_settings() {
		$this->api_url = get_option( 'whatsapp_login_api_url', '' );
		$this->token   = get_option( 'whatsapp_login_api_token', '' );
	}

	/**
	 * Atualiza configurações
	 */
	public function reload_settings() {
		$this->load_settings();
	}

	/**
	 * Envia mensagem via WhatsApp API
	 *
	 * @param string $number Número do telefone (formato: 5544999999999).
	 * @param string $message Mensagem a ser enviada.
	 * @return bool|WP_Error True se enviado com sucesso, WP_Error em caso de erro.
	 */
	public function send_message( $number, $message ) {
		// Validações
		if ( empty( $this->api_url ) || empty( $this->token ) ) {
			return new \WP_Error(
				'api_not_configured',
				__( 'API não configurada. Configure a URL e o Token nas configurações.', 'whatsapp-login-woocommerce' )
			);
		}

		if ( empty( $number ) || empty( $message ) ) {
			return new \WP_Error(
				'invalid_params',
				__( 'Número ou mensagem vazios.', 'whatsapp-login-woocommerce' )
			);
		}

		// Determina endpoint
		$saved_endpoint = get_option( 'whatsapp_login_api_endpoint', '' );
		
		if ( empty( $saved_endpoint ) ) {
			$endpoint = $this->api_url;
		} else {
			if ( strpos( $saved_endpoint, 'http' ) === 0 ) {
				$endpoint = $saved_endpoint;
			} else {
				$base_url = rtrim( $this->api_url, '/' );
				$endpoint_path = '/' . ltrim( $saved_endpoint, '/' );
				$endpoint = $base_url . $endpoint_path;
			}
		}

		// Determina formato do body
		$saved_format = get_option( 'whatsapp_login_api_body_format', array( 'number' => 'number', 'message' => 'body' ) );
		$number_key   = isset( $saved_format['number'] ) ? $saved_format['number'] : 'number';
		$message_key  = isset( $saved_format['message'] ) ? $saved_format['message'] : 'body';

		// Prepara dados
		$data = array(
			$number_key  => sanitize_text_field( $number ),
			$message_key => sanitize_textarea_field( $message ),
		);

		// Prepara headers
		$headers = array(
			'Content-Type' => 'application/json',
		);

		// Determina formato de autenticação
		$auth_type = get_option( 'whatsapp_login_api_auth_type', 'bearer' );
		switch ( $auth_type ) {
			case 'token':
				$headers['Authorization'] = 'Token ' . sanitize_text_field( $this->token );
				break;
			case 'apikey':
				$headers['X-API-Key'] = sanitize_text_field( $this->token );
				break;
			case 'bearer':
			default:
				$headers['Authorization'] = 'Bearer ' . sanitize_text_field( $this->token );
				break;
		}

		// Prepara requisição
		$args = array(
			'method'  => 'POST',
			'headers' => $headers,
			'body'    => wp_json_encode( $data ),
			'timeout' => 30,
		);

		// Faz requisição
		$response = wp_remote_post( $endpoint, $args );

		// Verifica erros HTTP
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Verifica código de resposta
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Se sucesso (200-299)
		if ( $response_code >= 200 && $response_code < 300 ) {
			return true;
		}

		// Tenta extrair mensagem de erro da resposta
		$error_message = sprintf( __( 'Erro na API: %d', 'whatsapp-login-woocommerce' ), $response_code );
		$decoded_body = json_decode( $response_body, true );
		if ( is_array( $decoded_body ) ) {
			if ( isset( $decoded_body['message'] ) ) {
				$error_message .= ' - ' . $decoded_body['message'];
			} elseif ( isset( $decoded_body['error'] ) ) {
				$error_message .= ' - ' . ( is_string( $decoded_body['error'] ) ? $decoded_body['error'] : wp_json_encode( $decoded_body['error'] ) );
			}
		}

		// Erro na API
		return new \WP_Error(
			'api_error',
			$error_message
		);
	}

	/**
	 * Verifica se a API está configurada
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->api_url ) && ! empty( $this->token );
	}
}

