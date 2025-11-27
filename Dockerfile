# 1. On utilise une version officielle de PHP avec Apache (le serveur web)
FROM php:8.2-apache

# 2. IMPORTANT : On installe les extensions pour que PHP puisse parler à MySQL
# (Par défaut, le Docker PHP n'a pas mysqli, donc sans cette ligne, votre connexion échouera)
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

# 3. On active le module de réécriture d'URL (utile si vous avez un routeur ou .htaccess)
RUN a2enmod rewrite

# 4. On copie tous vos fichiers locaux vers le dossier web du serveur
COPY . /var/www/html/

# 5. On dit à Render que le serveur écoute sur le port 80
EXPOSE 80
