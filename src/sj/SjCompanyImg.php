<?php

class SjCompanyImg extends CompanyImg {
    public function defaultScope(){
        if(Yii::app()->user->getIsShopper()) {
            $criteria = new CDbCriteria;
            $criteria->with = 'company';
            $criteria->addInCondition('company_id',Yii::app()->user->getCompanyIds());
            return $criteria;
        }else if (Yii::app()->user->getIsAdmin()) {
            $criteria = new CDbCriteria;
            $criteria->with = 'company';
            $criteria->addInCondition('address_id',Yii::app()->user->getAddressIds());
            return $criteria;
        }
        return array();
    }

    public function rules() {
    	$rules = parent::rules();
    	$addtionals = array(
    		array('img_id', 'numerical', 'integerOnly'=>true)
    	);
    	return array_merge($rules, $addtionals);
    }

    public function attributeLabels() {
    	$labels = parent::attributeLabels();
    	$addtionals = array(
    		'img_id', 'numerical', 'integerOnly'=>true
    	);
    	return array_merge($labels, $addtionals);
    }

    public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('company_id',$this->company_id,true);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('type',$this->type,true);
                $criteria->compare('status',$this->status,true);
                $criteria->compare('sort',$this->sort,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}