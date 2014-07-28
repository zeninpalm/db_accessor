<?php
/**
 * Created by PhpStorm.
 * User: wei
 * Date: 14-7-28
 * Time: 下午3:04
 */

require_once __DIR__ . '/../common/User.php';


/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $role
 * @property integer $update_time
 *
 * The followings are the available model relations:
 * @property UserApp[] $userApps
 */
class ProxyUser extends User{

    public static $PHONE_MATCH = "/^13[0-9]{9}$|14[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$/";

    public static $ROLE_ADMIN = 0;
    public static $ROLE_DEV = 1;

    public static function getUserRole(){
        return array(ProxyUser::$ROLE_ADMIN=>'管理员',User::$ROLE_DEV=>'开发者');
    }

    public function getRoleName(){
        $userRole = ProxyUser::getUserRole();
        return $userRole[$this->role];
    }

    public function beforeSave(){
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->create_time = time();
                $this->update_time = time();
            }
            return true;
        }
        return false;
    }

    public function updateLoginTime(){
        $this->update_time = time();
        $this->save();
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,password,email,mobile', 'required'),
            array('role, update_time, create_time', 'numerical', 'integerOnly'=>true),
            array('mobile', 'length', 'max'=>20),
            array('name', 'length', 'max'=>50),
            array('password', 'length', 'max'=>64,'min'=>6),
            array('email', 'length', 'max'=>100),
            array('header', 'length', 'max'=>500),
            array('email', 'email','message'=>"请输入正确的邮箱."),
            array('email', 'unique','message'=>'邮箱已经存在 不可重复注册.'),
            array('name', 'unique','message'=>'用户名存在 不可重复注册.'),
            array('mobile', 'unique', 'message'=>'手机号存在 不可重复注册.'),
            array('mobile', 'match', 'pattern' => User::$PHONE_MATCH,),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, role, mobile, name, password, email, header, update_time, create_time', 'safe', 'on'=>'search'),
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
            'userApps' => array(self::HAS_MANY, 'UserApp', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'role' => '角色',
            'mobile' => '手机号',
            'name' => '用户名',
            'password' => '密码',
            'email' => '邮箱',
            'header' => '头像',
            'update_time' => '上次登录',
            'create_time' => '注册日期',
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

        $criteria= $this->createDbCriteria();

        $criteria->compare('update_time',$this->update_time);
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
} 