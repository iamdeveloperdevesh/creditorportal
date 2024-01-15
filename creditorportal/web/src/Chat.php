<?php

namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
error_reporting(E_ALL);
require_once('Dropoff.php');

class Chat implements MessageComponentInterface {
    protected $clients;
	public $users = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }
	
    public function onMessage(ConnectionInterface $from, $msg) {
		//
		echo 'resourceid----------'.$from->resourceId;
		send_dropoff_mail_sms($from->resourceId,true,$msg);

		//
		$this->users[$from->resourceId] = $msg;
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection2323 %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
		echo 'resourceidclosure----------'.$conn->resourceId;
		//print_r($this->users[$conn->resourceId]);
		send_dropoff_mail_sms($conn->resourceId,false,'');
		unset($this->users[$conn->resourceId]);
	  // $this->clients->detach($conn);
	 
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}