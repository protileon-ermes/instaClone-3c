#!/bin/bash
set -e

echo "⏳ Aguardando MySQL em ${DB_HOST}:${DB_PORT}..."

# Teste de conexão via TCP (ignora SSL, senhas e usuários)
while ! timeout 1s bash -c "echo > /dev/tcp/${DB_HOST}/${DB_PORT}" 2>/dev/null; do
  echo "... porta ${DB_PORT} ainda fechada. Dormindo 2s"
  sleep 2
done

echo "✅ MySQL aceitou a conexão TCP!"

if [ ! -f .env ]; then
    echo "📝 Criando .env a partir do .env.example..."
    cp .env.example .env
fi

echo "🔑 Gerando Application Key..."
php artisan key:generate --force

echo "📦 Executando migrations..."
php artisan migrate --force

echo "🧹 Otimizando caches do Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔗 Criando link simbólico do storage..."
php artisan storage:link || true

echo "🚀 Iniciando FrankenPHP..."
# O "$@" executa o comando padrão (CMD) passado pelo Dockerfile
exec "$@"