<?php
/*
 * $Id$
 * @author
 */
class Afx_RandomUtil {
    /**
     * 传入概率值，算出此次概率是否成功
     *
     * @param $value
     * @return boolean
     * @example if (probability(10)) echo '我有10%的概率说话';
     */
	public function probability($value, $valueMax = 100) {
		return (mt_rand(1, $valueMax) <= $value);
	}


    public function arrayRandom($arr) {
        return $arr[array_rand($arr)];
    }


    /**
     * 用圆桌方法计算概率
     *
     * eg.
     * 1. $ar = array(1=>40, 2=>40, 3=>70) runRate($array) 可得到1,2,3中的一个值
     * 2. $ar = array(1=> array('id'=> 4, 'rate'=> 40), 2=> array('id'=>5, 'rate'=> 70), 3=> array('id=>6, 'rate'=> 70))
     *    执行 runRate($ar, 'rate') 后可得到1,2,3中的一个值
     * 3. $ar = array(1=> array('id'=> 4, 'rate'=> 40), 2=> array('id'=>5, 'rate'=> 70), 3=> array('id=>6, 'rate'=> 70))
     *    执行 runRate($ar, 'rate', 'id') 后，可得到4,5,6中的一个值
     *
     * @access  public
     * @param   array   $array  概率数组
     * @param   string  $index  键
     * @param   string  $value  概率值的键
     * @return  mixed
     */
    public function runRate($array, $value = null, $index = null) {
        $cnt = 0;
        //while ( list($key, $val) = @each($array) ) {
		foreach( $array as $key => $val ) {
            if ( ! $val ) continue;
            $cnt += $value !== null && isset($val[$value]) ? $val[$value] : $val;
            $k = $index !== null && isset($val[$index]) ? $val[$index] : $key;
            $rate[$cnt] = $k;
        }

        // 摇色子
        $iRate = mt_rand(1,$cnt);

        // 获得对应的key
        //while ( list( $cnt, $key) = @each($rate) ) {
		foreach( $rate as $cnt => $key ) {
            if($cnt >= $iRate) return $key;
        }
    }
    
    /*
     * 根据概率进行区间命中
     * 
     * Monster 2012.5.17
     * @access  public
     * @param   array 	$array进行命中的数组  array('a' => 50, 'b' => 30, 'c' => 20)
     * @param   $valueMax  命中的最大值
     * @return  string or int    返回命中的值;
     */
	public function runHit($array ,$valueMax = 100){
		$arrHit = array();
		$arrItems = array();
		foreach ($array as $key => $value){
			$arrHit[] = $value;
			$arrItems[] = $key;
		}
		$rand = rand(1,$valueMax);
		$k = -1; 										//无视第一次计算，所以从-1开始。
		foreach ($arrHit as $key => $itemHit){
			$k++;
			if($key == 0){
				$hit = $this->hitRegion(1, $itemHit, $rand);
			} elseif($key == (count($arrHit)-1)){
				$hit = $this->hitRegion((($valueMax - $itemHit) + 1), $valueMax, $rand);
			} else{
				$start = 0;
				for($i=0;$i<$k;$i++){
					$start += $arrHit[$i];
				}
				$start = $start + 1;
				$end = ($start + $itemHit) - 1;
				$hit = $this->hitRegion($start, $end, $rand);
			}
			if($hit){
				$strHit = $key;
			}
		}
		return $arrItems[$strHit];
	}

	/*
	 * 是否命中区间
	 * Monster 2012.5.17
	 * @param int $start 区间开始值
	 * @param int $end	 区间结束值
	 * @param int $rand  要命中的值
	 * @return bool 
	 */
	public function hitRegion($start,$end,$rand){
		if($rand >= $start && $rand <= $end){
			return true;
		} else{
			return false;
		}
	}
}