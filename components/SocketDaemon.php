<?php

namespace app\components;

class SocketDaemon
{
  private $magicString = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
  private $null = NULL;
  private $socket;
  private static $host;
  private static $port;
  private $connectedClients = [];

  public function __construct($host, $port)
  {
    self::setConfig($host, $port);
    $this->createSocket();
  }
  
  public static function setConfig($host, $port)
  {
    self::$host = $host;
    self::$port = $port;
  }

  public function start()
  {
    while (true) {
      $changed = $this->connectedClients;
      socket_select($changed, $this->null, $this->null, 0, 10);
      if (in_array($this->socket, $changed)) {
        $socket_new = socket_accept($this->socket);


        $header = socket_read($socket_new, 1024);
        $headers = $this->parseHeaders($header);
        if (is_array($headers)) {
          $this->performHandshaking($header, $socket_new);
          socket_getpeername($socket_new, $ip);
          $identity = $headers['Sec-WebSocket-Protocol'];
        } else {
          $data = json_decode($headers);
          $this->sendMessage(json_encode($data->message), $data->to);
          continue;
        }
        $this->connectedClients[$identity] = $socket_new;
        $found_socket = array_search($this->socket, $changed);
        unset($changed[$found_socket]);
      }
      foreach ($changed as $socket) {
        $buf = @socket_read($socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) {
          $found_socket = array_search($socket, $this->connectedClients);
          unset($this->connectedClients[$found_socket]);
        }
      }

    }
  }

  /**
   * @param $data
   * @param mixed $to
   */
  public static function send($data, $to = null)
  {
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    $result = socket_connect($socket, self::$host, self::$port) or die("Could not connect toserver\n");
    $message = json_encode([
      'message' => $data,
      'to' => $to
    ]);
    socket_write($socket, $message, strlen($message));
  }


  /**
   * @param $message
   * @param mixed $to
   */
  private function sendMessage($message, $to = null)
  {
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($message);
    if($length <= 125) {
      $header = pack('CC', $b1, $length);
    } elseif($length > 125 && $length < 65536) {
      $header = pack('CCn', $b1, 126, $length);
    } elseif($length >= 65536) {
      $header = pack('CCNN', $b1, 127, $length);
    } else {
      $header = '';
    }
    $message = $header.$message;
    $connections = [];
    if (!$to) {
      $connections = $this->connectedClients;
    } else {
      if (!is_array($to)) {
        $to = [$to];
      }
      foreach ($to as $identity) {
        if (isset($this->connectedClients[$identity])) {
          $connections[] = $this->connectedClients[$identity];
        }
      }
    }
    foreach ($connections as $connection) {
      @socket_write($connection,$message,strlen($message));
    }

  }

  private function createSocket()
  {
    $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($this->socket, 0, self::$port);
    socket_listen($this->socket);
    $this->connectedClients[] = $this->socket;
  }

  private function parseHeaders($header)
  {
    $headers = [];
    foreach(preg_split("/\r\n/", $header) as $line) {
      if(preg_match('/\A(\S+): (.*)\z/', chop($line), $matches)) {
        $headers[$matches[1]] = $matches[2];
      }
    }
    return $headers ? $headers : $header;
  }

  private function performHandshaking($header,$client_conn)
  {
    $headers = $this->parseHeaders($header);
    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . $this->magicString)));
    $upgrade = [];
    $upgrade[] = 'HTTP/1.1 101 Web Socket Protocol Handshake';
    $upgrade[] = 'Upgrade: websocket';
    $upgrade[] = 'Connection: Upgrade';
    $upgrade[] = sprintf('WebSocket-Origin: %s', self::$host);
    $upgrade[] = sprintf('WebSocket-Location: ws://%s:%s', self::$host, self::$port);
    $upgrade[] = sprintf('Sec-WebSocket-Accept:%s', $secAccept);
    $upgrade = implode("\r\n", $upgrade)."\r\n\r\n";
    socket_write($client_conn,$upgrade,strlen($upgrade));
  }

  public function __destruct()
  {
    @socket_close($this->socket);
  }
}