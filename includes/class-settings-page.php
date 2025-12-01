<?php
/**
 * Página de configurações do WooCommerce
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Verifica se WC_Settings_Page existe (WooCommerce está ativo)
if ( ! class_exists( 'WC_Settings_Page' ) ) {
	return null;
}

/**
 * Classe Settings_Page
 */
class Settings_Page extends \WC_Settings_Page {

	/**
	 * Construtor
	 */
	public function __construct() {
		$this->id    = 'whatsapp_login';
		$this->label = __( 'WhatsApp Login', 'whatsapp-login-woocommerce' );

		parent::__construct();
	}

	/**
	 * Retorna configurações
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'woocommerce_whatsapp_login_settings', array(

			array(
				'title' => __( 'Configurações da API WhatsApp', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure a integração com a API WhatsApp.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_api_settings',
			),

			array(
				'title'    => __( 'URL da API', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'URL base da API WhatsApp', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_api_url',
				'type'     => 'text',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 600px;',
			),

			array(
				'title'    => __( 'Token/API Key', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Token de autenticação da API', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_api_token',
				'type'     => 'password',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 600px;',
			),

			array(
				'title'    => __( 'Tipo de Autenticação', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Formato de autenticação da API', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_api_auth_type',
				'type'     => 'select',
				'default'  => 'bearer',
				'options'  => array(
					'bearer' => 'Bearer Token',
					'token'  => 'Token',
					'apikey' => 'API Key',
				),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_api_settings',
			),

			array(
				'title' => __( 'Configurações de Segurança', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure as opções de segurança do login.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_security_settings',
			),

			array(
				'title'    => __( 'Tempo de Expiração do Token', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Minutos até o link de login expirar (padrão: 15)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_token_expiration',
				'type'     => 'number',
				'default'  => 15,
				'custom_attributes' => array(
					'min'  => 5,
					'max'  => 60,
					'step' => 1,
				),
			),

			array(
				'title'    => __( 'Máximo de Tentativas', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Número máximo de tentativas por telefone a cada hora (padrão: 3)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_max_attempts',
				'type'     => 'number',
				'default'  => 3,
				'custom_attributes' => array(
					'min'  => 1,
					'max'  => 10,
					'step' => 1,
				),
			),

			array(
				'title'    => __( 'Janela de Tempo', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Janela de tempo para rate limiting em minutos (padrão: 60)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_time_window',
				'type'     => 'number',
				'default'  => 60,
				'custom_attributes' => array(
					'min'  => 15,
					'max'  => 1440,
					'step' => 15,
				),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_security_settings',
			),

			array(
				'title' => __( 'Personalização de Mensagem', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Personalize a mensagem enviada via WhatsApp. Use: {nome_loja}, {link}, {expiracao}', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_message_settings',
			),

			array(
				'title'    => __( 'Template da Mensagem', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Template da mensagem WhatsApp', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_message_template',
				'type'     => 'textarea',
				'default'  => __( "Olá! 👋\n\nAlguém solicitou login em {nome_loja}.\n\nClique no link abaixo para entrar:\n{link}\n\nEste link expira em {expiracao} minutos.\n\nNão solicitou? Ignore esta mensagem.", 'whatsapp-login-woocommerce' ),
				'css'      => 'width: 100%; max-width: 600px; height: 200px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_message_settings',
			),

			array(
				'title' => __( 'Configurações de Exibição', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure onde e como o formulário de login será exibido em cada área do site.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_display_settings',
			),

			array(
				'title'    => __( 'Ativar Login WhatsApp', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Ativa o login via WhatsApp globalmente', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_enabled',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_display_settings',
			),

			// ============================================
			// PÁGINA MINHA CONTA (MY ACCOUNT)
			// ============================================
			array(
				'title' => __( 'Página Minha Conta (My Account)', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure a exibição do login via WhatsApp na página de login/registro do WooCommerce.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_myaccount_settings',
			),

			array(
				'title'    => __( 'Exibir na Página Minha Conta', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Ativa o botão de login via WhatsApp na página Minha Conta', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_show_myaccount',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),

			array(
				'title'    => __( 'Posição na Página Minha Conta', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Onde exibir o formulário na página Minha Conta', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_myaccount_position',
				'type'     => 'select',
				'default'  => 'after',
				'options'  => array(
					'before' => __( 'Antes do formulário padrão', 'whatsapp-login-woocommerce' ),
					'after'  => __( 'Depois do formulário padrão', 'whatsapp-login-woocommerce' ),
					'replace' => __( 'Substituir o formulário padrão', 'whatsapp-login-woocommerce' ),
				),
			),

			array(
				'title'    => __( 'Texto do Botão (My Account)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Texto exibido no botão de login na página Minha Conta', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_myaccount_button_text',
				'type'     => 'text',
				'default'  => __( 'Entrar com WhatsApp', 'whatsapp-login-woocommerce' ),
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Título Personalizado (My Account)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Título exibido acima do formulário (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_myaccount_title',
				'type'     => 'text',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Descrição Personalizada (My Account)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Descrição exibida abaixo do título (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_myaccount_description',
				'type'     => 'textarea',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 600px; height: 80px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_myaccount_settings',
			),

			// ============================================
			// PAINEL ADMINISTRATIVO (WP-ADMIN)
			// ============================================
			array(
				'title' => __( 'Painel Administrativo (wp-admin)', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure a exibição do login via WhatsApp na tela de login do WordPress admin.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_wpadmin_settings',
			),

			array(
				'title'    => __( 'Exibir no Login do Admin', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Ativa o botão de login via WhatsApp na tela de login do WordPress (wp-login.php)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_show_wpadmin',
				'type'     => 'checkbox',
				'default'  => 'no',
			),

			array(
				'title'    => __( 'Posição no Login do Admin', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Onde exibir o formulário na tela de login do admin', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_wpadmin_position',
				'type'     => 'select',
				'default'  => 'after',
				'options'  => array(
					'before' => __( 'Antes do formulário padrão', 'whatsapp-login-woocommerce' ),
					'after'  => __( 'Depois do formulário padrão', 'whatsapp-login-woocommerce' ),
				),
			),

			array(
				'title'    => __( 'Texto do Botão (wp-admin)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Texto exibido no botão de login na tela de login do admin', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_wpadmin_button_text',
				'type'     => 'text',
				'default'  => __( 'Entrar com WhatsApp', 'whatsapp-login-woocommerce' ),
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Título Personalizado (wp-admin)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Título exibido acima do formulário (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_wpadmin_title',
				'type'     => 'text',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Descrição Personalizada (wp-admin)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Descrição exibida abaixo do título (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_wpadmin_description',
				'type'     => 'textarea',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 600px; height: 80px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_wpadmin_settings',
			),

			// ============================================
			// PÁGINA DE CHECKOUT
			// ============================================
			array(
				'title' => __( 'Página de Checkout', 'whatsapp-login-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Configure a exibição do login via WhatsApp durante o processo de finalização de compra.', 'whatsapp-login-woocommerce' ),
				'id'    => 'whatsapp_login_checkout_settings',
			),

			array(
				'title'    => __( 'Exibir no Checkout', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Ativa o botão de login via WhatsApp na página de checkout', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_show_checkout',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),

			array(
				'title'    => __( 'Posição no Checkout', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Onde exibir o formulário na página de checkout', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_checkout_position',
				'type'     => 'select',
				'default'  => 'before',
				'options'  => array(
					'before' => __( 'Antes das opções de checkout', 'whatsapp-login-woocommerce' ),
					'after'  => __( 'Depois das opções de checkout', 'whatsapp-login-woocommerce' ),
					'inline' => __( 'Junto com as opções de checkout', 'whatsapp-login-woocommerce' ),
				),
			),

			array(
				'title'    => __( 'Texto do Botão (Checkout)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Texto exibido no botão de login na página de checkout', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_checkout_button_text',
				'type'     => 'text',
				'default'  => __( 'Continuar com WhatsApp', 'whatsapp-login-woocommerce' ),
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Título Personalizado (Checkout)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Título exibido acima do formulário (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_checkout_title',
				'type'     => 'text',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 400px;',
			),

			array(
				'title'    => __( 'Descrição Personalizada (Checkout)', 'whatsapp-login-woocommerce' ),
				'desc'     => __( 'Descrição exibida abaixo do título (deixe vazio para usar o padrão)', 'whatsapp-login-woocommerce' ),
				'id'       => 'whatsapp_login_checkout_description',
				'type'     => 'textarea',
				'default'  => '',
				'css'      => 'width: 100%; max-width: 600px; height: 80px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'whatsapp_login_checkout_settings',
			),

		) );
	}
}

return new Settings_Page();

