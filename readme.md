# AFTC PHP MVC Framework v6.0.0
## PHP Min Version Required 8.3.6

### WARNING
```
The docker files here are for development environments not production environments. DO NOT USE IN PRODUCTION AND MAKE SURE YOU DONT UPLOAD THEM!
```

### 3rd Party Open Source Libraries Used
```
1. FastRoute
packagist: https://packagist.org/packages/nikic/fast-route
git: https://github.com/nikic/FastRoute

2. PhpMailer
packagist: https://packagist.org/packages/phpmailer/phpmailer
git: https://github.com/PHPMailer/PHPMailer

3. firebase/php-jwt
packagist: https://packagist.org/packages/firebase/php-jwt
git: https://github.com/firebase/php-jwt

3. Twig
packagist: https://packagist.org/packages/twig/twig
git: https://github.com/twigphp/Twig
```

### Installation
```
1. Copy aftc-framework to the folder below your publich html folder, for many this will be named httpdocs or public_html, this is dependant on your host/server setup.

2. Copy the contents of the httpdocs folder to your public html folder, for many this will be named httpdocs or public_html depending on the host.

3. Open terminal/cmd/powershell and CD into aftc-framework directory and enter "composer install"

4. Test API response via localhost/api/status

5. Test PHP View via localhost/php

6. Test Twig View via localhost
```

### Local Development Setup
```
1. Install docker (docker desktop for windows).

2. Download repo and place in a folder somewhere.

3. Open CMD or Terminal and CD into that folder.

4. Windows users can use the bat files to build, remove, up, down, enter and list the containers (no need to add .bat to run them). Linux users use docker command or create .sh versions of the .bat files.

5. Visit 127.0.0.1 or localhost and start your work (if you have not modified the ports and docker files).
```