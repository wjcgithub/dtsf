<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 19-1-29
 * Time: 下午2:27
 */

namespace PhpAmqpLib\Connection;


use PhpAmqpLib\Wire\IO\SwooleIO;

class AMQPSwooleConnection extends AbstractConnection
{
    /**
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     * @param bool $insist
     * @param string $login_method
     * @param null $login_response
     * @param string $locale
     * @param float $read_timeout
     * @param bool $keepalive
     * @param int $write_timeout
     * @param int $heartbeat
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vhost = '/',
        $insist = false,
        $login_method = 'AMQPLAIN',
        $login_response = null,
        $locale = 'en_US',
        $read_timeout = 3,
        $keepalive = false,
        $write_timeout = 3,
        $heartbeat = 0
    ) {
        $io = new SwooleIO($host, $port, $read_timeout, $read_timeout, $context = null,
            $keepalive = false,
            $heartbeat = 0);

        parent::__construct(
            $user,
            $password,
            $vhost,
            $insist,
            $login_method,
            $login_response,
            $locale,
            $io,
            $heartbeat
        );
    }

    /**
     * Attempts to close the connection safely
     */
    public function safeClose()
    {
        try {
            if (isset($this->input) && $this->input) {
                $this->close();
            }
        } catch (\Exception $e) {
            // Nothing here
        }
    }
}