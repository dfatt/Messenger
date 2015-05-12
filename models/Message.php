<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Message extends ActiveRecord {

	/**
	 * @return string
	 */
	public static function tableName() {
		return 'messages';
	}

	/**
	 * @return array
	 */
	public function behaviors() {
		return [
			[
				'class'              => TimestampBehavior::className(),
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => 'updated_at',
				'value'              => function() {
					return date('Y-m-d H:i:s');
				},
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLikes() {
		return $this->hasMany(Like::className(), ['message_id' => 'id']);
	}
}