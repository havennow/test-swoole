##

Exemplo de swoole com Redis

bem simples

de um docker-compose build
e depois docker-compose up -d

e depois docker-compose exec php-fpm bash

vai entrar no console do docker

rodar um composer install
agora lá dentro voce executa o servidor web do swoole, só para render o index ali e depois o do socket

php run/start_http.php && php run/start_sio.php

depois da para matar o processo só executar o run/start_sio.php

por que o serviço o php web ja está on