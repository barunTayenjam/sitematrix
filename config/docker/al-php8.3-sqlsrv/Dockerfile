FROM wordpress:php8.3-fpm

# Microsoft ODBC driver + unixODBC headers for the PHP sqlsrv extensions.
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      ca-certificates curl gnupg2 unixodbc-dev \
 && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
      | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
 && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
      > /etc/apt/sources.list.d/mssql-release.list \
 && apt-get update \
 && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
 && rm -rf /var/lib/apt/lists/*

# PHP extensions used by salesadmin/includes/dbconnect.php.
RUN pecl install sqlsrv pdo_sqlsrv \
 && docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN php -m | grep -qi '^sqlsrv$' && php -m | grep -qi '^pdo_sqlsrv$'
