<?php

namespace app\components;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Yii;

/**
 * Class Messenger
 * @package app\components
 */
class Messenger implements MessageComponentInterface {

	const NEW_LIKE = 'new_like';
	const NEW_MESSAGE = 'new_message';
	const DELETE_MESSAGE = 'delete_message';

	protected $clients;

	public function __construct() {
		$this->clients = new \SplObjectStorage;

	}

	/**
	 * @param ConnectionInterface $conn
	 */
	public function onOpen(ConnectionInterface $conn) {
		$this->clients->attach($conn);
		echo "Новое соединение ({$conn->resourceId})\n";
	}

	/**
	 * @param ConnectionInterface $from
	 * @param string              $msg
	 */
	public function onMessage(ConnectionInterface $from, $msg) {
		foreach ($this->clients as $client) {
			if ($from !== $client) {

				if (isset($msg)) {

					$msg = json_decode($msg);
					switch ($msg->type) {
						case self::NEW_LIKE:
							$client->send(json_encode([
								'type'       => self::NEW_LIKE,
								'message_id' => $msg->message_id,
								'user_name'  => $msg->user_name,
								'count'      => $msg->count,
							]));
							break;

						case self::NEW_MESSAGE:
							$client->send(json_encode([
								'type'       => self::NEW_MESSAGE,
								'message_id' => $msg->message_id,
								'content'    => $msg->content,
								'user_name'  => $msg->user_name,
								'created_at' => $msg->created_at
							]));
							break;

						case self::DELETE_MESSAGE:
							$client->send(json_encode([
								'type'       => self::DELETE_MESSAGE,
								'message_id' => $msg->message_id,
							]));
							break;
					}
				}
			}
		}
	}

	/**
	 * @param ConnectionInterface $conn
	 */
	public function onClose(ConnectionInterface $conn) {
		$this->clients->detach($conn);
		echo "Пользователь {$conn->resourceId} отключился\n";
	}

	/**
	 * @param ConnectionInterface $conn
	 * @param \Exception          $e
	 */
	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "Произошла ошибка: {$e->getMessage()}\n";
		$conn->close();
	}
}