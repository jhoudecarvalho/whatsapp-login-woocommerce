# WhatsApp Login for WooCommerce

Plugin WordPress profissional que permite login de usuários via WhatsApp usando link mágico (magic link). Desenvolvido para WooCommerce com foco em segurança, usabilidade e experiência do usuário.

## 🚀 Características

- ✅ **Login via WhatsApp com link mágico** - Autenticação sem senha usando WhatsApp
- ✅ **Integração completa com WooCommerce** - Funciona perfeitamente com a página "Minha Conta"
- ✅ **Segurança robusta** - Rate limiting, tokens de uso único, expiração temporal
- ✅ **Validação de cadastro** - Apenas usuários já cadastrados podem fazer login
- ✅ **Interface intuitiva** - Formulário moderno com feedback visual em tempo real
- ✅ **Configurações completas** - Painel administrativo completo no WooCommerce
- ✅ **Personalização de mensagens** - Template customizável para mensagens WhatsApp
- ✅ **Logs de auditoria** - Registro de todas as tentativas de login
- ✅ **Responsivo** - Funciona perfeitamente em todos os dispositivos
- ✅ **Acessibilidade** - Suporte a leitores de tela e navegação por teclado

## 📋 Requisitos

- WordPress 5.8 ou superior
- WooCommerce 6.0 ou superior
- PHP 7.4 ou superior
- API WhatsApp configurada (Twilio, 360Dialog, Meta Cloud API ou compatível)

## 📦 Instalação

### Via WordPress Admin

1. Faça o download do plugin
2. Acesse **Plugins > Adicionar Novo > Enviar Plugin**
3. Selecione o arquivo ZIP e clique em **Instalar Agora**
4. Ative o plugin através do menu **Plugins**

### Via FTP

1. Faça upload da pasta `whatsapp-login-woocommerce` para `/wp-content/plugins/`
2. Ative o plugin através do menu **Plugins** no WordPress

## ⚙️ Configuração

Após ativar o plugin, acesse **WooCommerce > Configurações > WhatsApp Login** para configurar:

### API WhatsApp

- **URL da API**: URL base da sua API WhatsApp
- **Token/API Key**: Token de autenticação
- **Tipo de Autenticação**: Bearer Token, Token ou API Key

### Segurança

- **Tempo de Expiração do Token**: Minutos até o link expirar (padrão: 15)
- **Máximo de Tentativas**: Número máximo de tentativas por telefone a cada hora (padrão: 3)
- **Janela de Tempo**: Janela de tempo para rate limiting em minutos (padrão: 60)

### Personalização de Mensagem

Personalize o template da mensagem WhatsApp usando as variáveis:
- `{nome_loja}` - Nome da loja
- `{link}` - Link mágico de login
- `{expiracao}` - Tempo de expiração em minutos

### Exibição

- **Ativar Login WhatsApp**: Ativa/desativa o formulário
- **Texto do Botão**: Texto exibido no botão (padrão: "Entrar com WhatsApp")
- **Posição do Botão**: Onde exibir o formulário (após o formulário padrão)

## 🎯 Como Funciona

1. **Usuário acessa a página de login** (wp-login.php ou Minha Conta do WooCommerce)
2. **Digita o número de telefone** no formulário WhatsApp
3. **Clica em "Entrar com WhatsApp"**
4. **Sistema valida** se o usuário está cadastrado na plataforma
5. **Gera token único** e envia link mágico via WhatsApp
6. **Usuário recebe mensagem** no WhatsApp com o link
7. **Clica no link** e faz login automaticamente
8. **Redireciona** para a página "Minha Conta" ou página configurada

## 🔒 Segurança

- ✅ **Validação de cadastro**: Apenas usuários já cadastrados podem fazer login
- ✅ **Rate limiting**: Limite de tentativas por telefone/hora
- ✅ **Tokens de uso único**: Cada token só pode ser usado uma vez
- ✅ **Expiração temporal**: Tokens expiram automaticamente (padrão: 15 minutos)
- ✅ **Validação de formato**: Telefone deve estar em formato internacional
- ✅ **Sanitização**: Todos os inputs são sanitizados e validados
- ✅ **Nonces WordPress**: Proteção CSRF em formulários
- ✅ **Logs de auditoria**: Registro de todas as tentativas (sucesso/falha)
- ✅ **IP Tracking**: Registro do IP de cada tentativa
- ✅ **Limpeza automática**: Cron job para deletar tokens expirados

## 🎨 Interface do Usuário

- **Formulário moderno** com design limpo e intuitivo
- **Feedback visual** em tempo real (sucesso, erro, carregamento)
- **Mensagens claras** em português brasileiro
- **Validação no cliente** antes de enviar ao servidor
- **Separação visual** do formulário tradicional
- **Responsivo** para todos os dispositivos

## 📁 Estrutura do Plugin

```
whatsapp-login-woocommerce/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       └── frontend.js
├── includes/
│   ├── class-admin-settings.php
│   ├── class-database.php
│   ├── class-login-handler.php
│   ├── class-settings-page.php
│   ├── class-token-manager.php
│   ├── class-whatsapp-api.php
│   └── class-whatsapp-login.php
├── templates/
│   └── login-form.php
├── languages/
├── whatsapp-login-woocommerce.php
└── README.md
```

## 🛠️ Desenvolvimento

### Hooks e Filtros

#### Actions

- `whatsapp_login_before_send` - Antes de enviar link
- `whatsapp_login_sent` - Após enviar link
- `whatsapp_login_success` - Após login bem-sucedido
- `whatsapp_login_user_created` - Quando usuário é criado (não usado mais)

#### Filters

- `whatsapp_login_message` - Filtra mensagem WhatsApp
- `whatsapp_login_redirect` - Filtra URL de redirecionamento após login

## 📝 Changelog

### 1.0.0
- Versão inicial
- Login via WhatsApp com link mágico
- Integração com WooCommerce
- Validação de cadastro obrigatória
- Interface moderna e responsiva
- Configurações completas no admin

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este plugin é licenciado sob a GPL v2 ou posterior.

## 👨‍💻 Autor

Desenvolvido por CDWTECH

## 📞 Suporte

Para suporte, abra uma issue no GitHub ou entre em contato através do site.

---

**Desenvolvido com ❤️ para a comunidade WordPress/WooCommerce**

