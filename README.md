#Simple Poll

Edit Medoo.php for mysql, Install.sql will help with getting that going.


Config for nginx for short urls


        location /p/ {
        try_files $uri $uri/ /p/getpoll.php?q=$uri;
        }

        location /p/r/ {
        try_files $uri $uri/ /p/getresults.php?q=$uri;
        }


current version is uses a sqlite DB, you can find a copy of it at example.sqlite
