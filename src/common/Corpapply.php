<?php

/**
 * This is the model class for table "corp_apply".
 *
 * The followings are the available columns in table 'corp_apply':
 * @property string $id
 * @property string $industry
 * @property string $industry_sub
 * @property string $area
 * @property string $province
 * @property string $city
 * @property string $address
 * @property string $contact_name
 * @property string $contact_tel
 * @property string $ip
 * @property string $source
 * @property integer $status
 * @property integer $update_time
 * @property integer $create_time
 * @property string $notes
 */
class Corpapply extends CActiveRecord
{    
        public static $INDUSTRY = array(
            '餐饮业'=>array(
                '咖啡厅'=>'咖啡厅',
                '茶楼'=>'茶楼',
                '美食店'=>'美食店',
                '酒吧'=>'酒吧',
            ),
            '服务业'=>array(
                'KTV'=>'KTV',
                '美容'=>'美容',
                '美发'=>'美发',
                '健身'=>'健身',
                '美体'=>'美体',
                '电影院'=>'电影院',
                '酒店'=>'酒店',
            ),
            '零售业'=>array(
                '购物广场'=>'购物广场',
                '百货商场'=>'百货商场',
                '汽车4S店'=>'汽车4S店',
                '咖啡厅'=>'咖啡厅',
            ),
            '社会机构'=>array(
                '银行门店'=>'银行门店',
                '医院'=>'医院',
                '教育机构'=>'教育机构',
                '办公室'=>'办公室',
            ),
        );
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'corp_apply';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('industry, industry_sub, source', 'length', 'max'=>100),
                        array('notes', 'length', 'max'=>1000),
			array('area, province, city, contact_name, contact_tel, ip', 'length', 'max'=>50),
                        array('status, update_time, create_time', 'numerical', 'integerOnly'=>true),
			array('address', 'length', 'max'=>400),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, industry, industry_sub, area, province, city, address, contact_name, contact_tel, ip, source, status, update_time,notes, create_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'industry' => '行业',
			'industry_sub' => '子行业',
			'area' => '面积',
			'province' => '省份',
			'city' => '城市',
			'address' => '详细地址',
			'contact_name' => '联系人',
			'contact_tel' => '联系电话',
			'ip' => 'IP地址',
			'source' => '来源',
                        'status' => '状态',
			'update_time' => '更新日期',
			'create_time' => '申请日期',
			'notes' => '备注',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('industry',$this->industry,true);
		$criteria->compare('industry_sub',$this->industry_sub,true);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('contact_tel',$this->contact_tel,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('source',$this->source,true);
                $criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('notes',$this->notes);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Corpapply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getStatus(){
            switch ($this->status){
                case 0:return '申请成功';
                case 1:return '同意安装';
                case 2:return '拒绝安装';
        }
        }
        public static function getStatusTypes(){
            return array(array('id'=>  0,'name'=>'申请成功'),
                array('id'=>  1,'name'=>'同意安装'),
                array('id'=>  2,'name'=>'拒绝安装'));
        }
}
