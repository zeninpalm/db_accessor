<?php

require_once __DIR__ . '/../common/MemberDevice.php';

class SjMemberDevice extends MemberDevice {
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('mac',$this->mac,true);
		$criteria->compare('brand',$this->brand,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function saveDevice($member, $connectionUser) {
        $find = MemberDevice::model()->findByAttributes(array('member_id'=>$member->id,'mac'=>$connectionUser->mac));
        if($find==null){
            $find = new MemberDevice;
            $find->member_id = $member->id;
            $find->mobile = $connectionUser->mobile;
            $find->mac = $connectionUser->mac;
            $find->save();
        }
        if($connectionUser->router!=null && $connectionUser->router->company_id>0){
            $cmember = CompanyMember::findMember($connectionUser->router->company_id,$member->id);
            if($cmember==null){
                $cmember = new CompanyMember;
                $cmember->company_id = $connectionUser->router->company_id;
                $cmember->member_id = $member->id;
                $cmember->mobile = $connectionUser->mobile;
            }else{
                $cmember->login_time = $cmember->login_time + 1;
            }
            $cmember->save();
        }
    }
}