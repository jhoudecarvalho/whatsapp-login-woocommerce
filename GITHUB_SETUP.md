# Instruções para Push no GitHub

## 1. Criar Repositório no GitHub

1. Acesse https://github.com
2. Clique em **New repository** (ou **+** > **New repository**)
3. Preencha:
   - **Repository name**: `whatsapp-login-woocommerce`
   - **Description**: `Plugin WordPress para login via WhatsApp usando link mágico no WooCommerce`
   - **Visibility**: Escolha **Public** ou **Private**
   - **NÃO marque** "Add a README file" (já temos um)
   - **NÃO marque** "Add .gitignore" (já temos um)
   - **NÃO marque** "Choose a license" (pode adicionar depois se quiser)
4. Clique em **Create repository**

## 2. Conectar e Fazer Push

### Opção A: Usando o Script Helper (Recomendado)

Após criar o repositório no GitHub, execute:

```bash
cd /home/cloudcaos-sinergia/htdocs/sinergia.cloudcaos.com.br/wp-content/plugins/whatsapp-login-woocommerce

# Execute o script (substitua SEU_USUARIO pelo seu usuário do GitHub)
./push-to-github.sh SEU_USUARIO
```

O script irá:
- Verificar se o repositório existe
- Configurar o remote
- Fazer o push automaticamente

### Opção B: Manual

Após criar o repositório, o GitHub mostrará instruções. Execute os seguintes comandos:

```bash
cd /home/cloudcaos-sinergia/htdocs/sinergia.cloudcaos.com.br/wp-content/plugins/whatsapp-login-woocommerce

# Adicionar remote (substitua SEU_USUARIO pelo seu usuário do GitHub)
git remote add origin https://github.com/SEU_USUARIO/whatsapp-login-woocommerce.git

# Ou se preferir SSH:
# git remote add origin git@github.com:SEU_USUARIO/whatsapp-login-woocommerce.git

# Verificar remote
git remote -v

# Fazer push
git push -u origin main
```

## 3. Verificar

Acesse o repositório no GitHub e verifique se todos os arquivos foram enviados corretamente.

## 4. Próximos Passos (Opcional)

- Adicionar tags de versão:
  ```bash
  git tag -a v1.0.0 -m "Versão inicial 1.0.0"
  git push origin v1.0.0
  ```

- Criar releases no GitHub:
  1. Vá em **Releases** > **Create a new release**
  2. Escolha a tag `v1.0.0`
  3. Adicione título e descrição
  4. Publique

## Notas

- Se você já tem um repositório existente e quer substituir:
  ```bash
  git remote set-url origin https://github.com/SEU_USUARIO/whatsapp-login-woocommerce.git
  git push -u origin main --force
  ```

- Para atualizar o repositório no futuro:
  ```bash
  git add .
  git commit -m "Descrição das mudanças"
  git push origin main
  ```

