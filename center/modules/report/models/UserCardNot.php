<?php

namespace center\modules\report\models;

use Yii;

/**
 * This is the model class for table "user_card_not".
 *
 * @property string $id
 * @property string $card_number
 */
class UserCardNot extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_card_not';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_number'], 'string', 'max' => 18],
            [['card_number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_number' => 'Card Number',
        ];
    }
}
