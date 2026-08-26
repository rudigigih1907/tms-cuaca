<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cuaca_gambar".
 *
 * @property int $id
 * @property int $cuaca_id
 * @property string $file_name
 * @property string|null $created_at
 *
 * @property Cuaca $cuaca
 */
class CuacaGambar extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cuaca_gambar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cuaca_id', 'file_name'], 'required'],
            [['cuaca_id'], 'integer'],
            [['created_at'], 'safe'],
            [['file_name'], 'string', 'max' => 255],
            [['cuaca_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cuaca::class, 'targetAttribute' => ['cuaca_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cuaca_id' => 'Cuaca ID',
            'file_name' => 'File Name',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Cuaca]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCuaca()
    {
        return $this->hasOne(Cuaca::class, ['id' => 'cuaca_id']);
    }

    public function getImageUrl(): string
    {
        return Yii::getAlias('@web/uploads/cuaca/') . $this->file_name;
    }

}
