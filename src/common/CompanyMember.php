<?php

/**
 * This is the model class for table "company_member".
 *
 * The followings are the available columns in table 'company_member':
 * @property string $id
 * @property string $company_id
 * @property string $member_id
 * @property string $remark
 * @property string $name
 * @property string $email
 * @property integer $dob
 * @property double $star
 * @property string $mobile
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $login_time
 * @property integer $gender
 * @property integer $is_staff
 *
 * The followings are the available model relations:
 * @property Member $member
 * @property Company $company
 */
class CompanyMember extends CActiveRecord
{
        /**
         * for stat
         */
        public $statCount;
        public $statTime;
        public static $TYPE_GENDER = array(0=>'未知',1=>'男',2=>'女');

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'company_member';
    }

        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->create_time = time();
                    $this->update_time = time();
                }else{
                    $this->update_time = time();
                }
                return true;                
            }
            return false;
        }
        
        
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_time, update_time, login_time, dob, gender', 'numerical', 'integerOnly'=>true),
                        array('star', 'numerical'),
            array('company_id, member_id', 'length', 'max'=>20),
            array('remark', 'safe'),
                        array('name', 'length', 'max'=>40),
            array('email', 'length', 'max'=>100),
                        array('email', 'email','message'=>'错误的邮箱格式'),
            array('mobile', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, company_id, member_id, mobile, name, email, login_time, dob, star, remark, create_time, update_time', 'safe', 'on'=>'search'),
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
            'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
            'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'company_id' => '所属公司',
            'member_id' => 'WIFI+ ID',
                        'name' => '姓名',
            'email' => '邮箱地址',
            'mobile' => '手机号',
            'remark' => '商家备注',
                        'dob' => '出生年月',
                        'gender' => '性别',
                        'star' => '会员星级',
                        'login_time' => '登陆次数',
            'create_time' => '注册时间',
                        'update_time' => '最近登陆',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CompanyMember the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
        
    public static function findMember($companyId,$memId){
        return CompanyMember::model()->findByAttributes(array('company_id'=>$companyId,'member_id'=>$memId));
    }
}
