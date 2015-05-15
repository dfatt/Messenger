<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {

	public $authKey;
	public $accessToken;

	public static function tableName() {
		return 'users';
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id) {
		$user = User::find()->where(['id' => $id])->asArray()->one();
		return isset($user) ? new static($user) : null;
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		return null;
	}

	/**
	 * Finds user by username
	 * @param  string $username
	 * @return static|null
	 */
	public static function findByUsername($username) {
		$user = User::find()->where(['username' => $username])->asArray()->one();

		if (isset($user)) {
			return new static($user);
		} else {
			$user = new User;
			$user->username = $username;

			$user->save();

			return new static($user);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->authKey;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey) {
		return $this->authKey === $authKey;
	}

	/**
	 * Validates password
	 * @param  string $password password to validate
	 * @return boolean if password provided is valid for current user
	 */
	public function validatePassword($password) {
		return $this->password === $password;
	}
}
