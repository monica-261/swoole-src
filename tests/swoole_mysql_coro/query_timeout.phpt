--TEST--
swoole_mysql_coro: mysql query timeout
--SKIPIF--
<?php require __DIR__ . '/../include/skipif.inc'; ?>
--FILE--
<?php
require __DIR__ . '/../include/bootstrap.php';

go(function (){
    $mysql = new Swoole\Coroutine\MySQL();
    $ret = $mysql->connect([
        'host' => MYSQL_SERVER_HOST,
        'user' => MYSQL_SERVER_USER,
        'password' => MYSQL_SERVER_PWD,
        'database' => MYSQL_SERVER_DB
    ]);
    if (Assert::true($ret)) {
        $s = microtime(true);
        $timeout = mt_rand(100, 500) / 1000;
        $ret = $mysql->query('select sleep(1)', $timeout);
        time_approximate($timeout, microtime(true) - $s);
        if (Assert::false($ret)) {
            Assert::eq($mysql->errno, SOCKET_ETIMEDOUT);
        }
    }
});
Swoole\Event::wait();
echo "DONE\n";
?>
--EXPECT--
DONE
