<?php
// +----------------------------------------------------------------------
// | BetaBuilder [ Beta Base CNF System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.betaec.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Johnny <johnnycaimail@yeah.net>
// +----------------------------------------------------------------------
// | Builder公共函数
// +----------------------------------------------------------------------

if(!function_exists('beta_time_format')){
    /**
     * 时间戳格式化
     * @param int $time
     * @return string 完整的时间显示
     * @author Johnny <johnnycaimail@yeah.net>
     */
    function beta_time_format($time = NULL, $format = 'Y-m-d H:i') {
        $time = $time === NULL ? time() : intval($time);
        return date($format, $time);
    }
}

if(!function_exists('beta_friendly_date')){
    /**
     * 友好的时间显示
     *
     * @param int    $sTime 待显示的时间
     * @param string $type  类型. normal | mohu | full | ymd | other
     * @param string $alt   已失效
     * @return string
     */
    function beta_friendly_date($sTime,$type = 'normal',$alt = 'false') {
        if (!$sTime || !is_timestamp($sTime))
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime      =   time();
        $dTime      =   $cTime-$sTime;
        $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
        $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if($type=='normal'){
            if( $dTime < 60 ){
                if($dTime < 10){
                    return '刚刚';    //by yangjs
                }else{
                    return intval(floor($dTime / 10) * 10).'秒前';
                }
            } elseif( $dTime < 3600 ){
                return intval($dTime/60).'分钟前';
                //今天的数据.年份相同.日期相同.
            } elseif( $dYear==0 && $dDay == 0  ){
                return '今天'.date('H:i',$sTime);
            } elseif( $dDay > 0 && $dDay<=3 ){
                return intval($dDay).'天前';
            } elseif($dYear==0){
                return date("m月d日 H:i",$sTime);
            } else{
                return date("Y-m-d H:i",$sTime);
            }
        } elseif($type=='mohu'){
            if( $dTime < 60 ){
                return $dTime.'秒前';
            } elseif( $dTime < 3600 ){
                return intval($dTime/60).'分钟前';
            } elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600).'小时前';
            } elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay).'天前';
            } elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . '周前';
            } elseif( $dDay > 30 ){
                return intval($dDay/30) .'个月前';
            } else{
                return date("Y-m-d H:i",$sTime);
            }
        } elseif($type=='full'){
            return date("Y-m-d , H:i:s",$sTime);
        } elseif($type=='ymd'){
            return date("Y-m-d",$sTime);
        } else{
            if( $dTime < 60 ){
                return $dTime.'秒前';
            } elseif( $dTime < 3600 ){
                return intval($dTime/60).'分钟前';
            } elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600).'小时前';
            } elseif($dYear==0){
                return date("Y-m-d H:i:s",$sTime);
            } else{
                return date("Y-m-d H:i:s",$sTime);
            }
        }
    }
}

if(!function_exists('beta_format_bytes')){
    /**
     * 格式化字节大小 把字节数格式为 B K M G T 描述的大小
     * @param  number $size      字节数
     * @param  string $delimiter 数字和单位分隔符
     * @return string            格式化后的带单位的大小
     * @author Johnny <johnnycaimail@yeah.net>
     */
    function beta_format_bytes($size, $delimiter = '') {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}
