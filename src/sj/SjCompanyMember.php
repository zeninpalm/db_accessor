<?php

require_once __DIR__ . "/../common/CompanyMember.php";

class SjCompanyMember extends CompanyMember {
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

    public function rules() {
    	$rules = parent::rules();
    	$rules[0] = array(
    		'create_time, 
    		update_time, 
    		login_time, 
    		dob, gender, 
    		is_staff, 
    		star', 
    		'numerical', 
    		'integerOnly' => true
    	);
    }

    public function attributeLabels() {
    	$labels = parent::attributeLabels();
    	$additionals = array('is_staff' => '本店员工');
    	return array_merge($labels, $additionals);
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