# ==========================================
# STAGE 1: VENDOR (Build das dependências)
# ==========================================
FROM composer:lts AS vendor
WORKDIR /app

COPY composer.json composer.lock ./

# Instala dependências usando cache do Docker para o Composer
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-scripts --no-interaction --prefer-dist --ignore-platform-reqs

COPY . .
# Gera o autoload otimizado após copiar os arquivos do projeto
RUN composer dump-autoload --optimize

# ==========================================
# STAGE 2: RUNTIME (Aplicação Final)
# ==========================================
FROM dunglas/frankenphp:1-php8.4-alpine AS runtime

# Instala ferramentas essenciais do sistema
RUN apk add --no-cache mysql-client bash tini curl

# Instala extensões do PHP solicitadas
RUN install-php-extensions pdo_mysql intl zip bcmath opcache pcntl gd redis

WORKDIR /app

# Copia tudo do stage de vendor
COPY --from=vendor /app /app

# Aplica as configurações do Docker
COPY docker/php.ini $PHP_INI_DIR/conf.d/custom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Ajusta permissões pro usuário www-data (padrão do server)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Configurações de porta do FrankenPHP
ENV SERVER_NAME=":8000"
EXPOSE 8000

# Healthcheck contra a rota nativa /up do Laravel 11
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:8000/up || exit 1

# Tini previne processos zumbis
ENTRYPOINT ["tini", "--", "/usr/local/bin/entrypoint.sh"]

# Inicia o servidor do FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]