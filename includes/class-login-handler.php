<?php
/**
 * Classe para gerenciar login via WhatsApp
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Login_Handler
 */
class Login_Handler {

	/**
	 * Instância única (Singleton)
	 *
	 * @var Login_Handler
	 */
	private static $instance = null;

	/**
	 * Token Manager
	 *
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * WhatsApp API
	 *
	 * @var WhatsApp_API
	 */
	private $whatsapp_api;

	/**
	 * Retorna instância única
	 *
	 * @return Login_Handler
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
		$this->token_manager = Token_Manager::get_instance();
		$this->whatsapp_api = WhatsApp_API::get_instance();

		// Hooks AJAX
		add_action( 'wp_ajax_whatsapp_send_login', array( $this, 'ajax_send_login' ) );
		add_action( 'wp_ajax_nopriv_whatsapp_send_login', array( $this, 'ajax_send_login' ) );

		// Hook para processar login via token
		add_action( 'init', array( $this, 'process_login_token' ) );

		// Adiciona formulário de login DEPOIS do formulário WooCommerce (fora do form)
		add_action( 'woocommerce_after_customer_login_form', array( $this, 'render_login_form' ), 10 );
		// Para formulário global (checkout, etc)
		add_action( 'woocommerce_login_form_end', array( $this, 'render_login_form_after_form' ), 20 );
		// Para login padrão do WordPress
		add_action( 'login_form', array( $this, 'render_login_form' ), 20 );
		
		// Enqueue scripts e styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Handler AJAX para enviar link de login
	 */
	public function ajax_send_login() {
		check_ajax_referer( 'whatsapp_login_nonce', 'nonce' );

		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

		if ( empty( $phone ) ) {
			wp_send_json_error( array( 'message' => __( 'Número de telefone é obrigatório.', 'whatsapp-login-woocommerce' ) ) );
		}

		// Valida formato do telefone
		$formatted_phone = $this->format_phone( $phone );
		if ( ! $formatted_phone ) {
			wp_send_json_error( array( 'message' => __( 'Formato de telefone inválido. Use o formato internacional.', 'whatsapp-login-woocommerce' ) ) );
		}

		// Verifica se usuário existe antes de enviar link
		$user_exists = $this->user_exists_by_phone( $formatted_phone );
		if ( ! $user_exists ) {
			wp_send_json_error( array( 
				'message' => __( 'Usuário não encontrado. É necessário fazer o cadastro na plataforma antes de usar o login via WhatsApp.', 'whatsapp-login-woocommerce' ) 
			) );
		}

		// Verifica rate limiting
		$max_attempts = get_option( 'whatsapp_login_max_attempts', 3 );
		$time_window = get_option( 'whatsapp_login_time_window', 60 );
		$rate_check = $this->token_manager->check_rate_limit( $formatted_phone, $max_attempts, $time_window );

		if ( is_wp_error( $rate_check ) ) {
			wp_send_json_error( array( 'message' => $rate_check->get_error_message() ) );
		}

		// Cria token
		$expiration_minutes = get_option( 'whatsapp_login_token_expiration', 15 );
		$token = $this->token_manager->create_token( $formatted_phone, $expiration_minutes );

		if ( ! $token ) {
			wp_send_json_error( array( 'message' => __( 'Erro ao gerar token. Tente novamente.', 'whatsapp-login-woocommerce' ) ) );
		}

		// Gera link de login
		$login_url = add_query_arg(
			array(
				'whatsapp_login' => $token,
			),
			home_url( '/wp-login.php' )
		);

		// Gera mensagem
		$message = $this->generate_message( $login_url, $expiration_minutes );

		// Envia mensagem via WhatsApp
		$result = $this->whatsapp_api->send_message( $formatted_phone, $message );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Log de auditoria
		do_action( 'whatsapp_login_sent', $formatted_phone, $token );

		wp_send_json_success( array(
			'message' => __( '✅ Link enviado para seu WhatsApp! Clique no link para acessar sua conta.', 'whatsapp-login-woocommerce' ),
			'expiration' => $expiration_minutes,
		) );
	}

	/**
	 * Processa login via token
	 */
	public function process_login_token() {
		if ( ! isset( $_GET['whatsapp_login'] ) ) {
			return;
		}

		$token = sanitize_text_field( $_GET['whatsapp_login'] );

		// Valida token
		$token_data = $this->token_manager->validate_token( $token );

		if ( ! $token_data ) {
			wp_die(
				__( 'Link de login inválido ou expirado.', 'whatsapp-login-woocommerce' ),
				__( 'Erro de Login', 'whatsapp-login-woocommerce' ),
				array( 'response' => 403 )
			);
		}

		// Busca usuário (apenas usuários já cadastrados)
		$user = $this->get_or_create_user( $token_data->phone );

		if ( ! $user ) {
			// Marca token como usado mesmo em caso de erro para evitar reutilização
			$this->token_manager->mark_token_as_used( $token );
			
			wp_die(
				__( 'Usuário não encontrado. É necessário fazer o cadastro na plataforma antes de usar o login via WhatsApp.', 'whatsapp-login-woocommerce' ),
				__( 'Usuário Não Cadastrado', 'whatsapp-login-woocommerce' ),
				array( 'response' => 403 )
			);
		}

		// Marca token como usado após confirmar que usuário existe
		$this->token_manager->mark_token_as_used( $token );

		// Faz login
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );

		// Atualiza token com user_id
		global $wpdb;
		$table_name = Database::get_tokens_table();
		$wpdb->update(
			$table_name,
			array( 'user_id' => $user->ID ),
			array( 'token' => $token ),
			array( '%d' ),
			array( '%s' )
		);

		// Log de auditoria
		do_action( 'whatsapp_login_success', $user->ID, $token_data->phone );

		// Redireciona
		$redirect_url = apply_filters( 'whatsapp_login_redirect', wc_get_page_permalink( 'myaccount' ), $user );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Verifica se usuário existe pelo telefone
	 *
	 * @param string $phone Número de telefone.
	 * @return bool
	 */
	private function user_exists_by_phone( $phone ) {
		// Busca usuário existente pelo telefone
		$users = get_users( array(
			'meta_key'   => 'whatsapp_phone',
			'meta_value' => $phone,
			'number'     => 1,
		) );

		if ( ! empty( $users ) ) {
			return true;
		}

		// Se não encontrou pelo meta whatsapp_phone, tenta buscar pelo telefone de billing do WooCommerce
		$users = get_users( array(
			'number' => -1, // Busca todos para verificar billing_phone
		) );

		foreach ( $users as $user ) {
			// Verifica telefone de billing do WooCommerce
			$billing_phone = get_user_meta( $user->ID, 'billing_phone', true );
			if ( ! empty( $billing_phone ) ) {
				$formatted_billing = $this->format_phone( $billing_phone );
				if ( $formatted_billing === $phone ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Busca usuário pelo telefone (apenas usuários já cadastrados)
	 *
	 * @param string $phone Número de telefone.
	 * @return WP_User|false
	 */
	private function get_or_create_user( $phone ) {
		// Busca usuário existente pelo telefone
		$users = get_users( array(
			'meta_key'   => 'whatsapp_phone',
			'meta_value' => $phone,
			'number'     => 1,
		) );

		if ( ! empty( $users ) ) {
			$user = $users[0];
			// Marca telefone como verificado
			update_user_meta( $user->ID, 'whatsapp_phone_verified', true );
			return $user;
		}

		// Se não encontrou pelo meta whatsapp_phone, tenta buscar pelo telefone de billing do WooCommerce
		$users = get_users( array(
			'number' => -1, // Busca todos para verificar billing_phone
		) );

		foreach ( $users as $user ) {
			// Verifica telefone de billing do WooCommerce
			$billing_phone = get_user_meta( $user->ID, 'billing_phone', true );
			if ( ! empty( $billing_phone ) ) {
				$formatted_billing = $this->format_phone( $billing_phone );
				if ( $formatted_billing === $phone ) {
					// Encontrou usuário pelo telefone de billing
					// Salva também no meta whatsapp_phone para futuras buscas
					update_user_meta( $user->ID, 'whatsapp_phone', $phone );
					update_user_meta( $user->ID, 'whatsapp_phone_verified', true );
					return $user;
				}
			}
		}

		// Usuário não encontrado - não cria automaticamente
		return false;
	}

	/**
	 * Gera mensagem WhatsApp
	 *
	 * @param string $login_url URL de login.
	 * @param int    $expiration_minutes Minutos até expirar.
	 * @return string
	 */
	private function generate_message( $login_url, $expiration_minutes ) {
		$template = get_option( 'whatsapp_login_message_template', '' );

		if ( empty( $template ) ) {
			$template = __( "Olá! 👋\n\nAlguém solicitou login em {nome_loja}.\n\nClique no link abaixo para entrar:\n{link}\n\nEste link expira em {expiracao} minutos.\n\nNão solicitou? Ignore esta mensagem.", 'whatsapp-login-woocommerce' );
		}

		$store_name = get_bloginfo( 'name' );
		$expiration = $expiration_minutes;

		$message = str_replace(
			array( '{nome_loja}', '{link}', '{expiracao}' ),
			array( $store_name, $login_url, $expiration ),
			$template
		);

		return apply_filters( 'whatsapp_login_message', $message, $login_url, $expiration_minutes );
	}

	/**
	 * Formata telefone para padrão internacional
	 *
	 * @param string $phone Telefone a formatar.
	 * @return string|false Telefone formatado ou false se inválido.
	 */
	private function format_phone( $phone ) {
		// Remove caracteres não numéricos
		$phone = preg_replace( '/[^0-9]/', '', $phone );

		// Se começa com 0, remove
		if ( substr( $phone, 0, 1 ) === '0' ) {
			$phone = substr( $phone, 1 );
		}

		// Se não começa com código do país, adiciona 55 (Brasil)
		if ( strlen( $phone ) <= 10 ) {
			$phone = '55' . $phone;
		}

		// Valida formato (mínimo 10 dígitos, máximo 15)
		if ( strlen( $phone ) < 10 || strlen( $phone ) > 15 ) {
			return false;
		}

		return $phone;
	}

	/**
	 * Enqueue scripts e styles
	 */
	public function enqueue_scripts() {
		$enabled = get_option( 'whatsapp_login_enabled', 'yes' );
		if ( $enabled !== 'yes' ) {
			return;
		}

		wp_enqueue_style(
			'whatsapp-login-frontend',
			WHATSAPP_LOGIN_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			WHATSAPP_LOGIN_VERSION
		);

		wp_enqueue_script(
			'whatsapp-login-frontend',
			WHATSAPP_LOGIN_PLUGIN_URL . 'assets/js/frontend.js',
			array( 'jquery' ),
			WHATSAPP_LOGIN_VERSION,
			true
		);

		wp_localize_script(
			'whatsapp-login-frontend',
			'whatsappLogin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'whatsapp_login_nonce' ),
				'strings'  => array(
					'invalid_phone' => __( 'Telefone inválido. Use o formato: (44) 99999-9999', 'whatsapp-login-woocommerce' ),
					'sending'        => __( 'Enviando link para seu WhatsApp...', 'whatsapp-login-woocommerce' ),
					'error'          => __( 'Erro ao enviar link. Tente novamente.', 'whatsapp-login-woocommerce' ),
					'success'        => __( '✅ Link enviado para seu WhatsApp! Clique no link para acessar sua conta.', 'whatsapp-login-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Renderiza formulário de login (para hooks após o formulário)
	 */
	public function render_login_form() {
		$enabled = get_option( 'whatsapp_login_enabled', 'yes' );
		if ( $enabled !== 'yes' ) {
			return;
		}

		$position = get_option( 'whatsapp_login_position', 'after' );
		$button_text = get_option( 'whatsapp_login_button_text', __( 'Entrar com WhatsApp', 'whatsapp-login-woocommerce' ) );

		// Carrega template
		$template_path = WHATSAPP_LOGIN_PLUGIN_DIR . 'templates/login-form.php';
		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}

	/**
	 * Renderiza formulário de login após o fechamento do form (para woocommerce_login_form_end)
	 * Usa JavaScript para mover o elemento para fora do formulário
	 */
	public function render_login_form_after_form() {
		$enabled = get_option( 'whatsapp_login_enabled', 'yes' );
		if ( $enabled !== 'yes' ) {
			return;
		}

		// Renderiza o formulário com um ID especial para ser movido via JS
		echo '<div id="whatsapp-login-form-temp" style="display:none;">';
		$this->render_login_form();
		echo '</div>';
		
		// Script para mover o formulário para fora do form após o fechamento
		?>
		<script type="text/javascript">
		(function() {
			var tempDiv = document.getElementById('whatsapp-login-form-temp');
			if (tempDiv) {
				var form = tempDiv.closest('form');
				if (form) {
					// Move o conteúdo para depois do formulário
					var content = tempDiv.innerHTML;
					tempDiv.remove();
					
					// Cria novo elemento após o form
					var newDiv = document.createElement('div');
					newDiv.innerHTML = content;
					form.parentNode.insertBefore(newDiv, form.nextSibling);
				} else {
					// Se não encontrou form, apenas mostra
					tempDiv.style.display = 'block';
					tempDiv.removeAttribute('id');
				}
			}
		})();
		</script>
		<?php
	}
}

