<?php

/**
 * 用于数据统计
 *
 * @author Renmian
 */
class StatModel {
        
    const MYSQL_STAT_DAY = 'd';
    const MYSQL_STAT_HOUR = 'H';
    const MYSQL_STAT_WEEK = 'u';
    const MYSQL_STAT_MONTH = 'm';
    /**
     * 清除该时段内一天的数据
     * @param type $begintime
     */
    public function clearData($begintime) {
        Yii::app()->db->createCommand()->delete('connection_data', 'stat_time between :begintime and :endtime', 
                                                array(':begintime'=>$begintime, ':endtime'=>($begintime + 86400)));
    }
    /**
     * 按小时分段查询一天的数据
     * @param int $begintime
     * @param int $comId
     * @return array
     */
    public function queryConnectionData($begintime, $comId='', $statType = self::MYSQL_STAT_HOUR) {
        $condition = 'add_time>=:begintime AND add_time<:endtime';
        $params = array(':begintime'=>$begintime, ':endtime'=>($begintime + 86400));
        if($comId) {
            $condition .= ' and company_id=:comId';
            $params[':comId'] = $comId;
        } else {
            if(Yii::app()->user->getIsShopper()) {
                $condition .= ' and company_id in (:companyIds)';
                $params[':companyIds'] = implode(',', Yii::app()->user->getCompanyIds());
            }
        }
        return Yii::app()->db->createCommand()->select('count(c.id) as view_count, count(DISTINCT(mobile)) as view_mobile,
                                sum(IF(`status`=1,1,0)) as login_count,  COUNT(DISTINCT CASE WHEN `status` = 1 THEN mobile END) login_mobile,
                                DATE_FORMAT(FROM_UNIXTIME(add_time), \'%' . $statType .'\') as stat_time,company_id')
                            ->from('connection_user c')
                            ->where($condition, $params)
                            ->group('company_id, stat_time')
                            ->order('company_id, stat_time')
                            ->query();
    }
    
    /**
     * 查询一小时内的新增会员数
     * @param type $comId
     * @param type $stat_time
     * @return type
     */
    public function queryMemberData($comId, $stat_time, $endtime=0) {
        $endtime = ($endtime == 0) ? ($stat_time + 3600) : $endtime;
         $company_member = Yii::app()->db->createCommand()->select('count(member_id)')->from('company_member')->where('company_id=:companyId and create_time>=:begintime AND create_time<:endtime', 
                                                    array(':companyId'=>$comId, ':begintime'=>$stat_time, ':endtime'=> $endtime))
                                       ->queryScalar();
        return $company_member ? $company_member : 0;
    }
    
    /**
     * 保存数据
     * @param type $comId
     * @param type $stat_time
     * @param type $view_count
     * @param type $view_mobile
     * @param type $login_count
     * @param type $login_mobile
     * @param type $new_member
     * @return type
     */
    public function addData($comId, $stat_time, $view_count, $view_mobile, $login_count, $login_mobile, $new_member) {
        return Yii::app()->db->createCommand()->insert('connection_data', array('company_id' => $comId, 'stat_time' => $stat_time,
                                    'view_count' => $view_count, 'view_mobile' => $view_mobile, 
                                    'login_count' => $login_count, 'login_mobile' => $login_mobile,
                                    'new_member' => $new_member));
    }
    
    /**
     * 按天进行计算
     * @param string $starttime
     * @param string $endtime
     * @param string $interval
     * @param string $timeformat
     * @param int $comId
     * @return array    统计数据+曲线图横坐标+画图的点
     */
    public function statByDaily1($starttime, $endtime, $interval, $timeformat, $comId='') {
        $result = array();
        $chartData = $chart_x = $chart_y = array();
        while($starttime < $endtime) {
            $chart_x[] = date($timeformat, $starttime);
            $stat_endtime = strtotime($interval, $starttime);
            $data = ConnectionData::sumDataBy1($starttime, $stat_endtime, $comId);
            if(empty($data) || empty($data->login_mobile)) {
                 $chart_y['login_mobile'][] = 0;
                 $chart_y['new_member'][] = 0;
            } else {
                 $data->stat_time = $interval == '+1 week' ? date($timeformat, $starttime) . '--' . date($timeformat, $stat_endtime-1) 
                                        : date($timeformat, $starttime);
                 $chart_y['login_mobile'][] = intval($data->login_mobile);
                 $chart_y['new_member'][] = intval($data->new_member);
                 $result[] = $data;
            }
            $starttime = $stat_endtime;
        }
        $data = new ConnectionData();
        $chartData[] = new ChartData($chart_y['login_mobile'], $data->getAttributeLabel('login_mobile'), ChartData::GRAY);
        $chartData[] = new ChartData($chart_y['new_member'], $data->getAttributeLabel('new_member'),  ChartData::BLUE);   
        return array('statData' => $result, 'labels' => $chart_x, 'chartData' => $chartData);
    }
    public function statByDaily($starttime, $endtime, $interval, $timeformat, $addressId,$companyCategoryId){
        
        $result = array();
        $firstTime = $starttime;
        $chartData = $chart_x = $chart_y = array();
        $mark = 0;
        while($starttime < $endtime) {
            $chart_x[] = $this->getSuitChartX($starttime, $firstTime, $endtime, 12, $mark,$timeformat);
            $stat_endtime = strtotime($interval, $starttime);
            $data = ConnectionData::sumDataBy($starttime, $stat_endtime, $addressId,$companyCategoryId); 
            if(empty($data) || empty($data->login_mobile)) {
                 $chart_y['login_mobile'][] = 0;
                 $chart_y['new_member'][] = 0;
            } else {
                 $data->stat_time = $interval == '+1 week' ? date($timeformat, $starttime) . '--' . date($timeformat, $stat_endtime-1) 
                                        : date($timeformat, $starttime);
                 $chart_y['login_mobile'][] = intval($data->login_mobile);
                 $chart_y['new_member'][] = intval($data->new_member);
                 $result[] = $data;
            }
            $starttime = $stat_endtime;
            $mark++;
        }
        $data = new ConnectionData();
         
        $chartData[] = new ChartData($chart_y['login_mobile'], $data->getAttributeLabel('login_mobile'), ChartData::GRAY);
        $chartData[] = new ChartData($chart_y['new_member'], $data->getAttributeLabel('new_member'),  ChartData::BLUE);   
        return array('statData' => $result, 'labels' => $chart_x, 'chartData' => $chartData);
    }
    private function getSuitChartX($starttime,$firstTime,$endTime,$num,$mark,$timeformat='m-d'){
        $charx = '';
        $days = ($endTime-$firstTime)/86400+(($endTime-$firstTime)%86400>0?1:0);
        //echo $days;die;
        $multiple = $days/$num;//倍数
        $remainder = $days%$num;//余数
        $realMultiple = $multiple+($remainder>0?1:0);
        if($multiple==0||$days==$mark+1){
            $charx = date($timeformat, $starttime);
        }else if($multiple==1){
           if($remainder!=0){
               $charx = $mark%2==0?date($timeformat, $starttime):'';
               
           }else{
               $charx = date($timeformat, $starttime);
           }
        }else{
            $charx = $mark%$realMultiple>0?'':date($timeformat, $starttime);
        }
        return $charx;
    }
    /**
     * 获取历史统计数据
     * @param type $comId
     * @return type
     */
    public function getTotalConnection($comId) {
        $condition = 'c.status=1 and company_id=:comId';
        $params[':comId'] = $comId;
        //'count(c.id) as view_count, count(DISTINCT(mobile)) as view_mobile,
        //sum(IF(`status`=1,1,0)) as login_count,  COUNT(DISTINCT CASE WHEN `status` = 1 THEN mobile END) login_mobile'
        $totalConnection = Yii::app()->db->createCommand()->select('count(DISTINCT(mobile)) as login_mobile')
                            ->from('connection_user c')
                            ->where($condition, $params)
                            ->queryScalar();
        return $totalConnection ? $totalConnection : 0;
    } 
    
    /**
     * 从ConnectionUser表中获取当天的数据
     * @param type $comId
     * @return \ConnectionData
     */
    public function queryConnectionDataForToday($comId) {
        $data = array();
        $starttime = strtotime(date('Y-m-d'));  //当天内的数据
        $result = $this->queryConnectionData($starttime, $comId);
        if($result->rowCount > 0) {
            foreach ($result as $key => $vo) {
                $stat_time = $starttime + $vo['stat_time'] * 3600;
                $new_member = $this->queryMemberData($vo['company_id'], $stat_time);
                $attrs = array('company_id'=>$vo['company_id'], 'stat_time'=>$stat_time,  
                                        'view_count'=>$vo['view_count'], 'view_mobile'=>$vo['view_mobile'],
                                        'login_count'=>$vo['login_count'], 'login_mobile'=>$vo['login_mobile'], 
                                        'new_member'=>$new_member);
                $connectionData = new ConnectionData;
                $connectionData->setAttributes($attrs);
                $data[] = $connectionData;
            }
        }
        return $data;
    }
}
