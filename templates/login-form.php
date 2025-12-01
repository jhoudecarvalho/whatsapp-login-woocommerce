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

// Variáveis padrão (caso não sejam passadas)
if ( ! isset( $template_button_text ) ) {
	$template_button_text = get_option( 'whatsapp_login_button_text', __( 'Entrar com WhatsApp', 'whatsapp-login-woocommerce' ) );
}
if ( ! isset( $template_position ) ) {
	$template_position = get_option( 'whatsapp_login_position', 'after' );
}
if ( ! isset( $context ) ) {
	$context = 'default';
}
if ( ! isset( $template_title ) ) {
	$template_title = __( 'Login Rápido via WhatsApp', 'whatsapp-login-woocommerce' );
}
if ( ! isset( $template_description ) ) {
	$template_description = __( 'Para usuários já cadastrados na plataforma', 'whatsapp-login-woocommerce' );
}
?>

<div class="whatsapp-login-form whatsapp-login-context-<?php echo esc_attr( $context ); ?>" data-position="<?php echo esc_attr( $template_position ); ?>" data-context="<?php echo esc_attr( $context ); ?>">
	<div class="whatsapp-login-header">
		<h3 class="whatsapp-login-title">
			<span class="whatsapp-icon">📱</span>
			<?php echo esc_html( $template_title ); ?>
		</h3>
		<?php if ( ! empty( $template_description ) ) : ?>
		<p class="whatsapp-login-subtitle">
			<?php echo esc_html( $template_description ); ?>
		</p>
		<?php endif; ?>
	</div>
	
	<div class="whatsapp-login-wrapper">
		<div class="whatsapp-login-field">
			<label for="whatsapp-phone-<?php echo esc_attr( $context ); ?>">
				<?php esc_html_e( 'Telefone', 'whatsapp-login-woocommerce' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="tel"
				id="whatsapp-phone-<?php echo esc_attr( $context ); ?>"
				name="whatsapp_phone"
				class="input-text whatsapp-phone-input"
				placeholder="<?php esc_attr_e( '(44) 99999-9999', 'whatsapp-login-woocommerce' ); ?>"
				autocomplete="tel"
				required
				data-context="<?php echo esc_attr( $context ); ?>"
			/>
			<p class="description">
				<?php esc_html_e( 'Digite seu número com DDD. Você receberá um link no WhatsApp para acessar sua conta.', 'whatsapp-login-woocommerce' ); ?>
			</p>
		</div>
		
		<button
			type="button"
			id="whatsapp-login-btn-<?php echo esc_attr( $context ); ?>"
			class="button button-primary whatsapp-login-button"
			data-context="<?php echo esc_attr( $context ); ?>"
		>
			<span class="whatsapp-icon">📱</span>
			<span class="button-text"><?php echo esc_html( $template_button_text ); ?></span>
			<span class="button-loader" style="display: none;">
				<span class="spinner"></span>
			</span>
		</button>
		
		<div id="whatsapp-login-message-<?php echo esc_attr( $context ); ?>" class="whatsapp-login-message" role="alert" aria-live="polite"></div>
		
		<div id="whatsapp-login-alternative-<?php echo esc_attr( $context ); ?>" class="whatsapp-login-alternative" style="display: none;">
			<p class="alternative-text">
				<strong><?php esc_html_e( 'Problemas?', 'whatsapp-login-woocommerce' ); ?></strong>
				<?php esc_html_e( 'Tente fazer login com e-mail e senha acima.', 'whatsapp-login-woocommerce' ); ?>
			</p>
		</div>
	</div>
</div>

