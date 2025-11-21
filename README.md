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
- API WhatsApp configurada ([CDWCHAT](https://cdwtech.com.br/sistema-de-chat/) ou compatível)

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

### Passo a Passo Completo

Após ativar o plugin, siga estes passos para configurar:

#### 1. Acessar as Configurações

1. No painel administrativo do WordPress, vá em **WooCommerce**
2. Clique em **Configurações**
3. Na barra de abas superior, localize e clique em **WhatsApp Login**

#### 2. Configurar API WhatsApp

Configure a integração com a API WhatsApp. Recomendamos o uso do [CDWCHAT](https://cdwtech.com.br/sistema-de-chat/) - Sistema de Chat profissional da CDW Tech.

**Campos a preencher:**

- **URL da API**: 
  - Cole a URL base da sua API WhatsApp
  - Exemplo para CDWCHAT: `https://apiwhatsapp.cdwchat.com.br/v1/api/external/SEU_ID_AQUI`
  - ⚠️ **Importante**: Substitua `SEU_ID_AQUI` pelo ID único fornecido pela CDWCHAT

- **Token/API Key**: 
  - Cole o token de autenticação fornecido pela CDWCHAT
  - Este token é gerado no painel do CDWCHAT
  - ⚠️ **Importante**: Mantenha este token seguro e não compartilhe

- **Tipo de Autenticação**: 
  - Selecione **Bearer Token** (padrão para CDWCHAT)
  - Esta é a opção recomendada para integração com CDWCHAT

**Sobre o CDWCHAT:**
O CDWCHAT é um sistema completo de atendimento via WhatsApp que oferece multiatendimento, histórico completo, CRM integrado e muito mais. [Saiba mais sobre o CDWCHAT](https://cdwtech.com.br/sistema-de-chat/).

#### 3. Configurar Segurança

Ajuste as configurações de segurança conforme sua necessidade:

- **Tempo de Expiração do Token**: 
  - Padrão: 15 minutos
  - Define quanto tempo o link de login permanece válido
  - Recomendado: entre 10 e 30 minutos

- **Máximo de Tentativas**: 
  - Padrão: 3 tentativas
  - Limite de tentativas de login por telefone a cada hora
  - Ajuda a prevenir abuso e ataques

- **Janela de Tempo**: 
  - Padrão: 60 minutos
  - Período em que o limite de tentativas é contabilizado
  - Recomendado: manter em 60 minutos

#### 4. Personalizar Mensagem WhatsApp

Personalize o template da mensagem que será enviada aos usuários:

**Variáveis disponíveis:**
- `{nome_loja}` - Nome da loja (obtido automaticamente do WordPress)
- `{link}` - Link mágico de login (gerado automaticamente)
- `{expiracao}` - Tempo de expiração em minutos

**Template padrão:**
```
Olá! 👋

Alguém solicitou login em {nome_loja}.

Clique no link abaixo para entrar:
{link}

Este link expira em {expiracao} minutos.

Não solicitou? Ignore esta mensagem.
```

**Dicas:**
- Você pode personalizar completamente a mensagem
- Mantenha o `{link}` na mensagem (obrigatório para funcionar)
- Use emojis para tornar a mensagem mais amigável
- Seja claro sobre a expiração do link

#### 5. Configurar Exibição

Configure onde e como o formulário será exibido:

- **Ativar Login WhatsApp**: 
  - Marque esta opção para ativar o formulário
  - Se desmarcado, o formulário não aparecerá

- **Texto do Botão**: 
  - Padrão: "Entrar com WhatsApp"
  - Personalize o texto do botão conforme sua preferência

- **Posição do Botão**: 
  - **Depois do formulário padrão** (recomendado): O formulário WhatsApp aparece após o link "Perdeu sua senha?"
  - **Antes do formulário padrão**: O formulário WhatsApp aparece antes do formulário tradicional

#### 6. Salvar Configurações

Após preencher todas as configurações:

1. Role a página até o final
2. Clique no botão **Salvar alterações**
3. Aguarde a mensagem de confirmação
4. Teste o login via WhatsApp para verificar se está funcionando

#### 7. Testar a Configuração

Para testar se tudo está funcionando:

1. Acesse a página de login do WooCommerce (Minha Conta)
2. Role até o formulário "Login Rápido via WhatsApp"
3. Digite um número de telefone de um usuário cadastrado
4. Clique em "Entrar com WhatsApp"
5. Verifique se a mensagem foi recebida no WhatsApp
6. Clique no link recebido
7. Verifique se o login foi realizado com sucesso

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

**Desenvolvido por:** Jhou de Carvalho  
**Empresa:** [CDW TECH](https://cdwtech.com.br)

### Sobre o Desenvolvedor

Jhou de Carvalho é desenvolvedor especializado em soluções WordPress/WooCommerce e integrações com WhatsApp. Este plugin foi desenvolvido para facilitar o login de usuários através do WhatsApp, melhorando a experiência do cliente e reduzindo a fricção no processo de autenticação.

### Sobre a CDW TECH

A [CDW TECH](https://cdwtech.com.br) é uma empresa especializada em desenvolvimento web, sistemas de chat, hospedagem e soluções tecnológicas. Oferecemos serviços como:

- **Sistema de Chat (CDWCHAT)** - Atendimento profissional via WhatsApp
- **Desenvolvimento de Lojas Virtuais**
- **Hospedagem de Sites e E-mails**
- **Desenvolvimento Web e Sistemas**
- **Infraestrutura de Servidor**

**Site:** https://cdwtech.com.br  
**CDWCHAT:** https://cdwtech.com.br/sistema-de-chat/

## 📞 Suporte

Para suporte, abra uma issue no GitHub ou entre em contato através do site da [CDW TECH](https://cdwtech.com.br).

---

**Desenvolvido com ❤️ Por Jhou de Carvalho - CDW TECH**

