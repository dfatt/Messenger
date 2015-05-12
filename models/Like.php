<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Like
 * @package app\models
 */
class Like extends ActiveRecord {

	/**
	 * @return string
	 */
	public static function tableName() {
		return 'likes';
	}
}