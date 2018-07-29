<?php

namespace app\commands;

use app\components\Messenger;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Yii;
use yii\console\Controller;

/**
 * Class MessengerController
 * @package app\commands
 */
class MessengerController extends Controller {

	public function actionIndex() {
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Messenger()
				)
			),
			8888
		);

		$server->run();
	}
}
