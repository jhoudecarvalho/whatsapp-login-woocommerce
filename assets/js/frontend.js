/**
 * Scripts do frontend
 */
(function($) {
	'use strict';

	$(document).ready(function() {
		var messageTimeouts = {};

		// Máscara de telefone com validação em tempo real (para todos os inputs)
		$(document).on('input', '.whatsapp-phone-input', function() {
			var $input = $(this);
			var context = $input.data('context') || 'default';
			var value = $input.val().replace(/\D/g, '');
			if (value.length <= 11) {
				value = value.replace(/^(\d{2})(\d{4,5})(\d{4})$/, '($1) $2-$3');
			}
			$input.val(value);
			
			// Remove mensagens de erro ao digitar
			var $message = $('#whatsapp-login-message-' + context);
			if ($message.length && $message.hasClass('error')) {
				$message.removeClass('show error');
			}
		});

		// Função para mostrar mensagem por tempo determinado
		function showMessage($message, type, text, duration, context) {
			context = context || 'default';
			
			// Limpa timeout anterior para este contexto
			if (messageTimeouts[context]) {
				clearTimeout(messageTimeouts[context]);
			}

			$message
				.removeClass('success error loading')
				.addClass(type + ' show')
				.html(text);

			// Se for sucesso, mantém visível por mais tempo (15 segundos)
			if (type === 'success') {
				duration = duration || 15000;
			}

			// Se for erro, mantém visível até o usuário interagir
			if (type === 'error') {
				return; // Não esconde automaticamente
			}

			// Esconde mensagem após duração especificada
			messageTimeouts[context] = setTimeout(function() {
				$message.removeClass('show');
			}, duration);
		}

		// Enviar login (usando delegação de eventos para múltiplos formulários)
		$(document).on('click', '.whatsapp-login-button', function() {
			var $btn = $(this);
			var context = $btn.data('context') || 'default';
			var $form = $btn.closest('.whatsapp-login-form');
			var $message = $('#whatsapp-login-message-' + context);
			var $phone = $form.find('.whatsapp-phone-input');
			var $alternative = $('#whatsapp-login-alternative-' + context);
			var phone = $phone.val().replace(/\D/g, '');

			// Validação no cliente antes de enviar
			if (!phone || phone.length < 10) {
				showMessage(
					$message,
					'error',
					'<strong>Telefone inválido</strong><br>Digite um número válido com DDD (ex: 44 99999-9999)',
					0,
					context
				);
				$phone.focus();
				return;
			}

			// Validação de formato internacional básico
			if (phone.length < 10 || phone.length > 15) {
				showMessage(
					$message,
					'error',
					'<strong>Formato inválido</strong><br>O número deve ter entre 10 e 15 dígitos',
					0,
					context
				);
				$phone.focus();
				return;
			}

			// Desabilita botão e mostra loading
			$btn.prop('disabled', true).addClass('loading');
			showMessage(
				$message,
				'loading',
				'<span class="spinner"></span> Enviando link para seu WhatsApp...',
				0,
				context
			);

			// Esconde alternativa em caso de erro anterior
			if ($alternative.length) {
				$alternative.slideUp();
			}

			// Envia requisição
			$.ajax({
				url: whatsappLogin.ajax_url,
				type: 'POST',
				data: {
					action: 'whatsapp_send_login',
					phone: phone,
					nonce: whatsappLogin.nonce
				},
				success: function(response) {
					$btn.prop('disabled', false).removeClass('loading');

					if (response.success) {
						// Sucesso: mostra mensagem por 15 segundos
						showMessage(
							$message,
							'success',
							response.data.message || '✅ Link enviado para seu WhatsApp! Clique no link para acessar sua conta.',
							15000,
							context
						);
						$phone.val('').blur();
					} else {
						// Erro: mostra mensagem clara e reabilita botão
						var errorMsg = response.data && response.data.message 
							? response.data.message 
							: 'Erro ao enviar link. Tente novamente.';
						
						showMessage(
							$message,
							'error',
							'<strong>Erro</strong><br>' + errorMsg,
							0,
							context
						);
						
						// Mostra alternativa de login tradicional
						if ($alternative.length) {
							$alternative.slideDown();
						}
						
						$phone.focus();
					}
				},
				error: function(xhr, status, error) {
					$btn.prop('disabled', false).removeClass('loading');
					
					var errorMsg = 'Erro de conexão. Verifique sua internet e tente novamente.';
					if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
						errorMsg = xhr.responseJSON.data.message;
					}
					
					showMessage(
						$message,
						'error',
						'<strong>Erro de Conexão</strong><br>' + errorMsg,
						0,
						context
					);
					
					// Mostra alternativa de login tradicional
					if ($alternative.length) {
						$alternative.slideDown();
					}
					
					$phone.focus();
				}
			});
		});

		// Fecha mensagem de erro ao clicar nela
		$(document).on('click', '.whatsapp-login-message.error', function() {
			$(this).removeClass('show');
		});
	});
})(jQuery);

