<?php

namespace app\controllers;

use app\models\Like;
use app\models\Message;
use app\models\UploadForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class MessengerController
 * @package app\controllers
 */
class MessengerController extends Controller {

	private $current_user = null;

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only'  => ['index', 'upload', 'newmessage', 'deletemessage', 'newlike'],
				'rules' => [
					[
						'actions' => ['index', 'upload', 'newmessage', 'deletemessage', 'newlike'],
						'allow'   => true,
						'roles'   => ['@'],
					],
				],
			],
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 *
	 */
	public function init() {
		$this->current_user = isset(Yii::$app->user->identity->username) ? Yii::$app->user->identity->username : null;
	}

	/**
	 * Страница чата
	 * @return string
	 */
	public function actionIndex() {
		return $this->render('index');
	}

	/**
	 * Загрузка изображений к сообщению
	 */
	public function actionUpload() {
		Yii::$app->response->format = Response::FORMAT_JSON;

		$model = new UploadForm();

		if (Yii::$app->request->isPost) {
			$model->file = UploadedFile::getInstance($model, 'file');

			if ($model->file && $model->validate()) {
				$model->file->saveAs(
					Yii::getAlias('@storage') . '/' . md5($model->file->baseName) . '.' . $model->file->extension
				);

				return md5($model->file->baseName) . '.' . $model->file->extension;
			}
		} else {
			return false;
		}

	}

	/**
	 * Добавить лайк к сообщению
	 * @return mixed
	 */
	public function actionNewLike() {
		$msg = Yii::$app->request->post();

		$like             = new Like();
		$like->message_id = $msg['message_id'];
		$like->user_name  = $msg['user_name'];

		$like->save();

		return Like::find()->where(['message_id' => $msg['message_id']])->count();
	}

	/**
	 * @return mixed
	 */
	public function actionNewMessage() {
		Yii::$app->response->format = Response::FORMAT_JSON;

		$msg = Yii::$app->request->post();

		$message            = new Message();
		$message->content   = $msg['content'];
		$message->user_name = $msg['user_name'];
		$message->save();

		return [
			'id'         => $message->id,
			'created_at' => date("d M H:i", strtotime($message->created_at)),
			'owner'      => true
		];
	}

	/**
	 * @return bool
	 */
	public function actionDeleteMessage() {
		$msg  = Yii::$app->request->post();
		$user = Message::findOne($msg['message_id']);

		if ($user->user_name === $this->current_user) {
			$user->delete();

			return true;
		} else {
			return false;
		}
	}
}


