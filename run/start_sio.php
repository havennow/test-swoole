<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

try {

    $config = new SocketIO\Engine\Payload\ConfigPayload();
    $config
        // server worker_num
        ->setWorkerNum(2)
        // server daemonize
        ->setDaemonize(0);

    $redisServer = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
    ]);

    // quando inicia a carga do servidor você pode só rodar aqui...
    // carrega no redis tudo e depois vc tem acesso dentro do reactor



    $checkIsJson = static function (?string $value) : bool {
        if ($value === null) {
            return false;
        }

        return (bool) preg_match('/^{.*}$/i', $value);
    };


    // isso é o reactor
    $io = new SocketIO\Server(9500, $config, function(SocketIO\Server $io) use ($redisServer, $checkIsJson) {
        $io->on('connection', function (SocketIO\Server $socket) use ($redisServer, $checkIsJson) {
            $socket->on('new message', function (SocketIO\Server $socket) {
                $socket->broadcast('new message', $socket->getMessage());
            });

            $socket->on('new user', function (SocketIO\Server $socket) use ($redisServer, $checkIsJson) {
                $data = $socket->getMessage();

                if ($checkIsJson($data)) {
                    $user = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
                    $redisServer->lpush('users', [
                        json_encode([
                            'user' => $user['nickname'] ?? 'non',
                            'data' => (new DateTime())->getTimestamp()
                        ], JSON_THROW_ON_ERROR)
                    ]);
                }

                $socket->broadcast('login', $data);
            });

            $socket->on('disconnect', function (SocketIO\Server $socket) {
                $socket->broadcast('user left', $socket->getMessage());
            });
        });
    });

    $io->start();

} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}