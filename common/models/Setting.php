<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $title_en
 * @property string $addres
 * @property string $addres_ru
 * @property string $addres_en
 * @property string $copyright
 * @property string $copyright_ru
 * @property string $mail
 * @property string $tell
 * @property string $facebook
 * @property string $instagram
 * @property string $telegram
 * @property string $youtube
 * @property string $description
 * @property string $description_ru
 * @property string $description_en
 * @property string $logo
 * @property string $logo_bottom
 * @property string $favicon
 * @property string $open_graph_photo
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public $logo1;
    public $logo_bottom1;
    public $favicon1;
    public $open_graph_photo1;
    public function rules()
    {
        return [
            [['title', 'title_ru', 'title_en', 'addres', 'addres_ru', 'addres_en', 'copyright', 'copyright_ru', 'copyright_en', 'mail', 'facebook', 'instagram', 'telegram', 'youtube', 'description', 'description_ru', 'description_en', 'tell'], 'required'],
            [['description', 'description_ru', 'description_en'], 'string'],
            [['title', 'title_ru', 'title_en', 'addres', 'addres_ru', 'addres_en', 'copyright', 'copyright_ru', 'copyright_en', 'mail', 'facebook', 'instagram', 'telegram', 'youtube', 'logo', 'logo_bottom', 'favicon', 'open_graph_photo', 'tell'], 'string', 'max' => 255],
            ['logo1', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg'], 'skipOnEmpty' => true],
            ['logo_bottom1', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg'], 'skipOnEmpty' => true],
            ['favicon1', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'ico'], 'skipOnEmpty' => true],
            ['open_graph_photo1', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg'], 'skipOnEmpty' => true],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function getDescriptionTranslate()
    {
        if(\Yii::$app->language == 'ru'){
            return $this->description_ru;
        }
        if(\Yii::$app->language == 'uz'){
            return $this->description;
        }
        if(\Yii::$app->language == 'en'){
            return $this->description_en;
        }

    }

    public function getCopyrightTranslate()
    {
        if(\Yii::$app->language == 'ru'){
            return $this->copyright_ru;
        }
        if(\Yii::$app->language == 'uz'){
            return $this->copyright;
        }
        if(\Yii::$app->language == 'en'){
            return $this->copyright_en;
        }

    }

    public function getAddressTranslate()
    {
        if(\Yii::$app->language == 'ru'){
            return $this->addres_ru;
        }
        if(\Yii::$app->language == 'uz'){
            return $this->addres;
        }
        if(\Yii::$app->language == 'en'){
            return $this->addres_en;
        }

    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'title_ru' => Yii::t('app', 'Title Ru'),
            'title_en' => Yii::t('app', 'Title En'),
            'addres' => Yii::t('app', 'Addres'),
            'addres_ru' => Yii::t('app', 'Addres Ru'),
            'addres_en' => Yii::t('app', 'Addres En'),
            'copyright' => Yii::t('app', 'Copyright'),
            'copyright_ru' => Yii::t('app', 'Copyright Ru'),
            'copyright_en' => Yii::t('app', 'Copyright En'),
            'mail' => Yii::t('app', 'Mail'),
            'facebook' => Yii::t('app', 'Facebook'),
            'instagram' => Yii::t('app', 'Instagram'),
            'telegram' => Yii::t('app', 'Telegram'),
            'youtube' => Yii::t('app', 'Youtube'),
            'description' => Yii::t('app', 'Description'),
            'description_ru' => Yii::t('app', 'Description Ru'),
            'description_en' => Yii::t('app', 'Description En'),
            'logo' => Yii::t('app', 'Logo'),
            'logo_bottom' => Yii::t('app', 'Logo Bottom'),
            'favicon' => Yii::t('app', 'Favicon'),
            'open_graph_photo' => Yii::t('app', 'Open Graph Photo'),
            'logo1' => Yii::t('app', 'Logo'),
            'logo_bottom1' => Yii::t('app', 'Logo Large'),
            'favicon1' => Yii::t('app', 'Favicon'),
            'open_graph_photo1' => Yii::t('app', 'Open Graph Photo'),
        ];
    }
}