### TP2

## Etape 1
2 containers nommés comme suit :

- HTTP : 1 container avec un serveur HTTP qui écoute sur le port 8080
- SCRIPT : 1 container avec un interpréteur PHP (plus le protocole FPM pour NGINX)

Une page index.php qui lorsqu'elle est appelée exécute la fonction php_info() et qui sera situé dans les containers dans le répertoire /app.

Test de validité de l'exercice : avec un navigateur voir le résultat de l'exécution du php_info()
Décommenter et remplacer les lignes 30 à 36 par les suivantes :

location ~ \.php$ {
root /app;
fastcgi_pass script:9000;
fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME
$document_root$fastcgi_script_name;
include fastcgi_params;
}

Faire les commandes suivantes :

    ```docker network create tp2_network```

    ```docker run --name http -p 8080:80 --volume "$(pwd)":/app --network tp2_network -d nginx```

    ```docker run --name script --volume "$(pwd)":/app --network tp2_network -d bitnami/php-fpm```

    ```docker exec -it http bash```

    ``` apt-get update```

    ``` apt-get install nano```

    ```nano /etc/nginx/conf.d/default.conf```

    edit the line 30/36 and also the root path line 9
    location / {
        root   /app;
        index  index.php index.html index.htm;
    }

    ```exit```

    ```docker restart http```

## Etape 2
3 containers nommés comme suit :

- HTTP : 1 container avec un serveur HTTP qui écoute sur le port 8080
- SCRIPT : 1 container avec un interpréteur PHP (plus le protocole FPM pour NGINX)
- DATA : 1 container avec un serveur de base données SQL (MariaDB, MySQL, PostgreSQL,
...)
Une page test_bdd.php qui lorsqu'elle est appelée va executer 2 requêtes CRUD (Request :
lecture, Create Update Delete : écriture) au minimum sur le serveur SQL : 1 lecture et 1 écriture
Test de validité de l'exercice : avec un navigateur voir le résultat de l'exécution de la page en retournant un résultat différent et dépendant du contenu de la base de données à chaque refresh de la page



    ```docker pull mariadb```

    ```docker run --name data --network tp2_network -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=mydb -d mariadb```

    // mariadb --user root -proot

    ```docker exec -it data mariadb --user root -proot -e "CREATE TABLE mydb.test_table (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))"```

    create test_bdd.php in /app

    ```
        <?php
        $mysqli = new mysqli("data", "root", "root", "mydb");

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Perform a simple INSERT
        $mysqli->query("INSERT INTO test_table (name) VALUES ('test')");

        // Perform a simple SELECT
        $result = $mysqli->query("SELECT * FROM test_table");
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row["id"]. " - Name: " . $row["name"]. "<br>";
        }

        $mysqli->close();
        ?>
    ```

    Aller sur le : localhost:8080/test_bdd.php
    refresh la page autant que nécessaire


## Etape 3
3 containers nommés comme suit :
- HTTP : 1 container avec un serveur HTTP qui écoute sur le port 8080
- SCRIPT : 1 container avec un interpréteur PHP (plus le protocole FPM pour NGINX)
- DATA : 1 container avec un serveur de base données SQL (MariaDB, MySQL, PostgreSQL,
...)
Remplacer la/les pages PHP simples par un package Wordpress complet.
Test de validité de l'exercice : avec un navigateur voir l'interface d'admin/installation de Wordpress afin de finaliser l'installation de celui-ci

    Télécharger le script wordpress

    ``` curl -O https://wordpress.org/latest.tar.gz
        tar -xzvf latest.tar.gz
        mv wordpress/* /path/to/your/local/app
        rm latest.tar.gz
    ```

    Créer la base de données "wordpress_db"

    ``` docker exec -it data bash
        mariadb --user root -proot
        CREATE DATABASE wordpress_db;
        exit
        exit
    ```
    Non utilisé

    ```nano /etc/nginx/conf.d/default.conf```

        server {
            listen       80;
            listen  [::]:80;
            server_name  localhost;

            #access_log  /var/log/nginx/host.access.log  main;

            location / {
                root   /app/wordpress;
                index  index.php index.html index.htm;
            }

            #error_page  404              /404.html;

            # redirect server error pages to the static page /50x.html
            #
            error_page   500 502 503 504  /50x.html;
            location = /50x.html {
                root   /usr/share/nginx/html;
            }

            # proxy the PHP scripts to Apache listening on 127.0.0.1:80
            #
            #location ~ \.php$ {
            #    proxy_pass   http://127.0.0.1;
            #}

            # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
            #
            location ~ \.php$ {
                root           /app/wordpress;
                fastcgi_pass   script:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
                include        fastcgi_params;
            }

            # deny access to .htaccess files, if Apache's document root
            # concurs with nginx's one
            #
            #location ~ /\.ht {
            #    deny  all;
            #}
        }

## Etape 4

Convertir la configuration de l'étape 3 en Docker Compose
Test de validité de l'exercice : identique à l'étape 3

    créer un fichier docker-compose.yaml (voir fichier) mettre le default.conf dans un nginx/default.conf pour l'utiliser comme volume pour le conteneur http mettre wordpress dans le chemin actuel pour l'utiliser comme volume pour le conteneur script
    coller le tout dans tous les conteneurs.

    ```docker-compose up -d```

    https://localhost:8081