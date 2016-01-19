<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blog".
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $author
 * @property integer $view
 * @property integer $updater_id
 */
class Blog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'image', 'content', 'created_at', 'updated_at'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at', 'author', 'view', 'updater_id'], 'integer'],
            [['title', 'image'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'image' => 'Image',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'author' => 'Author',
            'view' => 'View',
            'updater_id' => 'Updater ID',
        ];
    }

    /**
     * @inheritdoc
     * @return BlogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlogQuery(get_called_class());
    }

    public static function getDublicateByTitle($title){
        $model = self::find()->where(['title' => $title])->one();
        if($model){
            return true;
        }else{
            return false;
        }
    }
}
