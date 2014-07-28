<?php

/**
 * This is the model class for table "company".
 *
 * The followings are the available columns in table 'company':
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $node
 * @property string $stop_auth
 * @property string $stop_sms
 * @property string $commercial
 * @property string $logo
 * @property string $logo_id
 * @property integer $category_id
 * @property string $consumer_level
 * @property string $consumer_tag
 * @property string $contact
 * @property string $home_page
 * @property string $address_id
 * @property string $address_detail
 * @property double $latitude
 * @property double $longitude
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Address $address
 * @property Router[] $routers
 * @property Member[] #members
 * @property CompanyCategory $category
 * @property CompanyImg[] $imgs
 * @property CompanyImg[] $showimgs
 */
class Company extends CActiveRecord
{

    public static $BUSINESS_TYPE = array('中餐厅' => '中餐厅', '西餐厅' => '西餐厅', '酒店' => '酒店', '足浴按摩' => '足浴按摩', '桌游' => '桌游', '茶楼' => '茶楼', '咖啡厅' => '咖啡厅', '酒吧/俱乐部' => '酒吧/俱乐部', '酒店宾馆' => '酒店宾馆', '健身瑜伽' => '健身瑜伽', '美发美容' => '美发美容', '休闲娱乐' => '休闲娱乐');

    public static $CONSUMER_LEVELS = array('低端' => '低端', '工薪' => '工薪', '小资' => '小资', '中高档' => '中高档', '高档' => '高档', '奢华' => '奢华');

    public static $CONSUMER_TAGS = array('16—22岁' => '16—22岁', '23——30岁' => '23——30岁', '31——40岁' => '31——40岁', '40岁以上' => '40岁以上');

    public $category_parent;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'company';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, name, category_id, consumer_level, consumer_tag,node, contact, address_detail', 'required'),
            array('create_time, logo_id, category_id,stop_auth,stop_sms,commercial', 'numerical', 'integerOnly' => true),
            array('latitude, longitude', 'numerical'),
            array('stop_auth,stop_sms,commercial', 'length', 'max' => 11),
            array('user_id, address_id', 'length', 'max' => 20),
            array('name, consumer_level, consumer_tag', 'length', 'max' => 50),
            array('contact', 'length', 'max' => 200),
            array('logo', 'length', 'max' => 400),
            array('node', 'length', 'max' => 45,),
            array('home_page', 'length', 'max' => 400),
            array('address_detail', 'length', 'max' => 100),
            array('logo', 'file', 'types' => 'jpg,png', 'maxSize' => 1024 * 100, 'allowEmpty' => true, 'tooLarge' => '图片大小不能超过100KB'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, name, category_id, consumer_level, consumer_tag, contact, address_id, address_detail', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'address' => array(self::BELONGS_TO, 'Address', 'address_id'),
            'routers' => array(self::HAS_MANY, 'Router', 'company_id'),
            'imgs' => array(self::HAS_MANY, 'CompanyImg', 'company_id'),
            'showimgs' => array(self::HAS_MANY, 'CompanyImg', 'company_id', 'condition' => 'status=1'),
            'members' => array(self::MANY_MANY, 'Member', 'company_member(company_id, member_id)'),
            'category' => array(self::BELONGS_TO, 'CompanyCategory', 'category_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => '所属用户',
            'name' => '名称',
            'logo' => '品牌标志',
            'logo_id' => '标志资源ID',
            'category_id' => '商家类型',
            'commercial' => '启动商用',
            'node' => '网关id',
            'stop_sms' => '强制短信验证',
            'stop_auth' => '开启信息收集',
            'home_page' => '商家主页',
            'consumer_level' => '消费档次',
            'consumer_tag' => '消费人群特征',
            'contact' => '联系方式',
            'address_id' => '地址区域',
            'address_detail' => '详细地址',
            'latitude' => '纬度',
            'longitude' => '经度',
            'create_time' => '添加日期',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('stop_sms', $this->stop_sms);
        $criteria->compare('stop_auth', $this->stop_auth);
        $criteria->compare('node', $this->node, true);
        $criteria->compare('commercial', $this->commercial, true);
        $criteria->compare('category_id', $this->type, true);
        $criteria->compare('consumer_level', $this->consumer_level, true);
        $criteria->compare('consumer_tag', $this->consumer_tag, true);
        $criteria->compare('contact', $this->contact, true);
        $criteria->compare('address_id', $this->address_id, true);
        $criteria->compare('address_detail', $this->address_detail, true);
        $criteria->compare('latitude', $this->latitude);
        $criteria->compare('longitude', $this->longitude);
        $criteria->compare('create_time', $this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Company the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getAdImgUrls()
    {
        $imgs = $this->showimgs;
        $result = array();
        foreach ($imgs as $img) {
            $result[$img->sort] = $img->img;
        }
        if (count($result) == 0) {
            $result = array('http://assets.wifimore.cn/upload/assets/p1-ad-default.jpg');
        }
        return $result;
    }

    public function getAdImgHtml()
    {
        $imgs = $this->getAdImgUrls();
        $result = '';
         ksort($imgs);
         $index = 0;
        foreach ($imgs as $img) {
            if ($index == (count($imgs) - 1)) {
                $result = $result . '<img class="last" src="' . $img . '"/>';
            } else {
                $result = $result . '<img src="' . $img . '"/>';
            }
            $index++;
        }
        return $result;
    }

    public function getMemberCount()
    {
        return CompanyMember::model()->count('company_id = :company_id', array(':company_id' => $this->id));
    }
}
