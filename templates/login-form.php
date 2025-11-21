<?php
/**
 * Template do formulário de login WhatsApp
 *
 * @package WhatsAppLogin
 */

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button_text = get_option( 'whatsapp_login_button_text', __( 'Entrar com WhatsApp', 'whatsapp-login-woocommerce' ) );
$position = get_option( 'whatsapp_login_position', 'after' );
?>

<div class="whatsapp-login-form" data-position="<?php echo esc_attr( $position ); ?>">
	<div class="whatsapp-login-header">
		<h3 class="whatsapp-login-title">
			<span class="whatsapp-icon">📱</span>
			<?php esc_html_e( 'Login Rápido via WhatsApp', 'whatsapp-login-woocommerce' ); ?>
		</h3>
		<p class="whatsapp-login-subtitle">
			<?php esc_html_e( 'Para usuários já cadastrados na plataforma', 'whatsapp-login-woocommerce' ); ?>
		</p>
	</div>
	
	<div class="whatsapp-login-wrapper">
		<div class="whatsapp-login-field">
			<label for="whatsapp-phone">
				<?php esc_html_e( 'Telefone', 'whatsapp-login-woocommerce' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="tel"
				id="whatsapp-phone"
				name="whatsapp_phone"
				class="input-text"
				placeholder="<?php esc_attr_e( '(44) 99999-9999', 'whatsapp-login-woocommerce' ); ?>"
				autocomplete="tel"
				required
			/>
			<p class="description">
				<?php esc_html_e( 'Digite seu número com DDD. Você receberá um link no WhatsApp para acessar sua conta.', 'whatsapp-login-woocommerce' ); ?>
			</p>
		</div>
		
		<button
			type="button"
			id="whatsapp-login-btn"
			class="button button-primary whatsapp-login-button"
		>
			<span class="whatsapp-icon">📱</span>
			<span class="button-text"><?php echo esc_html( $button_text ); ?></span>
			<span class="button-loader" style="display: none;">
				<span class="spinner"></span>
			</span>
		</button>
		
		<div id="whatsapp-login-message" class="whatsapp-login-message" role="alert" aria-live="polite"></div>
		
		<div id="whatsapp-login-alternative" class="whatsapp-login-alternative" style="display: none;">
			<p class="alternative-text">
				<strong><?php esc_html_e( 'Problemas?', 'whatsapp-login-woocommerce' ); ?></strong>
				<?php esc_html_e( 'Tente fazer login com e-mail e senha acima.', 'whatsapp-login-woocommerce' ); ?>
			</p>
		</div>
	</div>
</div>

