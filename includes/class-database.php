<?php
/**
 * Classe para gerenciar banco de dados
 *
 * @package WhatsAppLogin
 */

namespace WhatsAppLogin;

// Se este arquivo for chamado diretamente, aborta.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe Database
 */
class Database {

	/**
	 * Cria tabelas do banco de dados
	 */
	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'whatsapp_login_tokens';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			token VARCHAR(32) UNIQUE NOT NULL,
			phone VARCHAR(20) NOT NULL,
			user_id BIGINT UNSIGNED NULL,
			created_at DATETIME NOT NULL,
			expires_at DATETIME NOT NULL,
			used_at DATETIME NULL,
			ip_address VARCHAR(45) NOT NULL,
			user_agent TEXT,
			INDEX idx_token (token),
			INDEX idx_phone (phone),
			INDEX idx_expires (expires_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Retorna nome da tabela de tokens
	 *
	 * @return string
	 */
	public static function get_tokens_table() {
		global $wpdb;
		return $wpdb->prefix . 'whatsapp_login_tokens';
	}
}

