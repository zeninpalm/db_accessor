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
    public static $TYPE_GENDER = array(0 => '未知', 1 => '男', 2 => '女');


    public function defaultScope()
    {
        if (Yii::app()->user->getIsShopper()) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition('company_id', Yii::app()->user->getCompanyIds());
            return $criteria;
        } else if (Yii::app()->user->getIsAdmin()) {
            $criteria = new CDbCriteria;
            $criteria->join = 'left join company on company.id=company_id';
            $criteria->addInCondition('address_id', Yii::app()->user->getAddressIds());
            return $criteria;
        }
        return array();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'company_member';
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
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
            array('create_time, update_time, login_time, dob, gender, is_staff, star', 'numerical', 'integerOnly' => true),
            array('company_id, member_id', 'length', 'max' => 20),
            array('remark', 'safe'),
            array('name', 'length', 'max' => 40),
            array('email', 'length', 'max' => 100),
            array('email', 'email', 'message' => '错误的邮箱格式'),
            array('mobile', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, company_id, member_id, mobile, name, email, login_time, dob, star, remark, create_time, update_time', 'safe', 'on' => 'search'),
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
            'star' => '会员等级',
            'login_time' => '登陆次数',
            'create_time' => '注册时间',
            'update_time' => '最近登陆时间',
            'is_staff' => '本店员工'
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
        $criteria->compare('company_id', $this->company_id, true);
        $criteria->compare('member_id', $this->member_id, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('login_time', $this->login_time);
        $criteria->compare('dob', $this->dob);
        $criteria->compare('star', $this->star);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('update_time', $this->update_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CompanyMember the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function findMember($companyId, $memId)
    {
        return CompanyMember::model()->findByAttributes(array('company_id' => $companyId, 'member_id' => $memId));
    }

    public static function createMember($companyId, $memId)
    {
        $member = CompanyMember::model()->findByAttributes(array('company_id' => $companyId, 'member_id' => $memId));
        if ($member == null) {
            $member = new CompanyMember;
            $member->company_id = $companyId;
            $member->member_id = $memId;
            $member->save();
        }
        return $member;
    }
}
