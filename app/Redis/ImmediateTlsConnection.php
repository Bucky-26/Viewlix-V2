<?php

namespace App\Redis;

use Predis\Connection\Resource\Stream;
use Predis\Connection\Resource\Exception\StreamInitException;
use Predis\Connection\ParametersInterface;
use Predis\Connection\StreamConnection;
use Psr\Http\Message\StreamInterface;

class ImmediateTlsConnection extends StreamConnection
{
    protected function createResource(): StreamInterface
    {
        $host = $this->parameters->host;
        $port = $this->parameters->port;
        $timeout = isset($this->parameters->timeout) ? max(30.0, (float) $this->parameters->timeout) : 30.0;
        $context = stream_context_create([
            'ssl' => ['peer_name' => $host],
        ]);
        $address = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
            ? "tls://[$host]:$port"
            : "tls://$host:$port";
        $resource = @stream_socket_client($address, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

        if (!$resource) {
            throw new StreamInitException(trim($errstr) ?: "Unable to connect to $address (error $errno)", $errno);
        }

        if (isset($this->parameters->read_write_timeout)) {
            $readWriteTimeout = (float) $this->parameters->read_write_timeout;
            $seconds = floor($readWriteTimeout);
            stream_set_timeout($resource, $seconds, (int) (($readWriteTimeout - $seconds) * 1000000));
        }

        return new Stream($resource);
    }
}