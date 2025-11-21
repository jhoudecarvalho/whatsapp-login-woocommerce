#!/bin/bash

# Script para fazer push do plugin para GitHub
# Uso: ./push-to-github.sh SEU_USUARIO_GITHUB

set -e

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== WhatsApp Login for WooCommerce - Push para GitHub ===${NC}\n"

# Verifica se o usuário foi fornecido
if [ -z "$1" ]; then
    echo -e "${RED}❌ Erro: Usuário do GitHub não fornecido${NC}"
    echo -e "${YELLOW}Uso: ./push-to-github.sh SEU_USUARIO_GITHUB${NC}"
    echo -e "${YELLOW}Exemplo: ./push-to-github.sh cdwtech${NC}\n"
    exit 1
fi

GITHUB_USER="$1"
REPO_NAME="whatsapp-login-woocommerce"
REPO_URL="https://github.com/${GITHUB_USER}/${REPO_NAME}.git"

echo -e "${YELLOW}⚠️  IMPORTANTE: Certifique-se de que o repositório já foi criado no GitHub!${NC}"
echo -e "${YELLOW}   Acesse: https://github.com/new e crie o repositório '${REPO_NAME}'${NC}\n"
read -p "O repositório já foi criado no GitHub? (s/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${RED}❌ Crie o repositório primeiro e execute o script novamente${NC}"
    exit 1
fi

echo -e "${BLUE}📦 Configurando repositório remoto...${NC}"

# Verifica se já existe remote
if git remote get-url origin &>/dev/null; then
    echo -e "${YELLOW}⚠️  Remote 'origin' já existe${NC}"
    read -p "Deseja atualizar para ${REPO_URL}? (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        git remote set-url origin "$REPO_URL"
        echo -e "${GREEN}✓ Remote atualizado${NC}"
    else
        echo -e "${YELLOW}Mantendo remote existente${NC}"
    fi
else
    git remote add origin "$REPO_URL"
    echo -e "${GREEN}✓ Remote adicionado${NC}"
fi

echo -e "\n${BLUE}📤 Fazendo push para GitHub...${NC}"

# Verifica se há mudanças não commitadas
if ! git diff-index --quiet HEAD --; then
    echo -e "${YELLOW}⚠️  Há mudanças não commitadas${NC}"
    read -p "Deseja fazer commit antes do push? (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        git add .
        read -p "Mensagem do commit: " COMMIT_MSG
        git commit -m "$COMMIT_MSG"
    fi
fi

# Faz push
if git push -u origin main; then
    echo -e "\n${GREEN}✅ Push realizado com sucesso!${NC}"
    echo -e "${GREEN}🔗 Repositório: https://github.com/${GITHUB_USER}/${REPO_NAME}${NC}\n"
else
    echo -e "\n${RED}❌ Erro ao fazer push${NC}"
    echo -e "${YELLOW}Verifique:${NC}"
    echo -e "  - Se o repositório existe no GitHub"
    echo -e "  - Se você tem permissão para fazer push"
    echo -e "  - Se suas credenciais estão configuradas\n"
    exit 1
fi

echo -e "${BLUE}📋 Próximos passos sugeridos:${NC}"
echo -e "  1. Acesse o repositório no GitHub"
echo -e "  2. Adicione uma descrição e tags"
echo -e "  3. Crie uma release (v1.0.0) se desejar"
echo -e "  4. Configure GitHub Pages se necessário\n"

