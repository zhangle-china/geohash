class Near{
	protected $nearRange;
	function __construct(){
		$this->nearRange[1] = 5000 * 1000; //5000公里
		$this->nearRange[2] = 624 * 1000;   //624公里
		$this->nearRange[3] = 156 *1000;   //156公里
		$this->nearRange[4] = 19.5 *1000;   //19.5公里
		$this->nearRange[5] = 4.9 *1000;   //4.9公里
		$this->nearRange[6] = 0.6*1000;     //0.6公里
	}
	
	
	/**
	 * 指定坐标和范围，返回当前坐标所在块及周边8块的geohash串，块的大小由range来确定
	 * @param Geohash $geohash; //
	 * @param Array $position 坐标 lon 键存放经度，lat键存放纬度
	 * @param int $range  范围 0-78000米
	 * @throws Exception
	 * @return Array
	 */
	function rangeNeighbors(Geohash $geohash,$position,$range){
		if(empty($geohash)) throw new \Exception("缺少Geohash 对象！");
		$lon = $position["lon"]; //经度
		$lat = $position["lat"]; //纬度
		$lon || $lon = $position[0]; //经度
		$lat || $lat = $position[1]; //纬度
		Message::ensure(preg_match("/[\d]{1,2}\.[\d]+/",$lat), Message::MSG_ERROR_LATITUDE);
		Message::ensure(preg_match("/[\d]{1,2}\.[\d]+/",$lon), Message::MSG_ERROR_LONGITUDE);
		$prelen = 6;
		foreach($this->nearRange as $key=>$value){
			if($range < $value && $range > $this->nearRange[$key+1] ){
				$prelen = $key;
				break;
			}
		}
// 		$geohashCode = $geohash>encode($lat, $lon);
		$geohashCode = $geohash->encode($lat, $lon);
		$geohashKey = substr($geohashCode,0,$prelen);
		$geohashs = $geohash->neighbors($geohashKey);
		array_push($geohashs, $geohashKey);
		return $geohashs;
	}	
	

	/**
	 * 取得两坐标点之间的距离
	 * @param array $position 当前位置坐标  0键放经度  1键放纬度
	 * @param array $tarPosition 目标位置坐标  0键放经度  1键放纬度
	 * @return  float 返回 两坐标点的距离
	 */
	function getDistance(Array $position,Array $tarPosition){
		//地球半径,单位为米
		$R = 6378137;
		//将角度转为狐度
		$radLat1 = deg2rad($position[1]);
		$radLat1 ||  $radLat1 = deg2rad($position["lat"]);
		$radLat2 = deg2rad($tarPosition[1]);
		$radLat2 ||  $radLat2 = deg2rad($tarPosition["lat"]);
			
		$radLng1 = deg2rad($position[0]);
		$radLng1 ||  $radLng1 = deg2rad($position["lon"]);
		$radLng2 = deg2rad($tarPosition[0]);
		$radLng2 ||  $radLng2 = deg2rad($tarPosition["lon"]);
		//结果
		$s = acos(cos($radLat1)*cos($radLat2)*cos($radLng1-$radLng2)+sin($radLat1)*sin($radLat2))*$R;
		//精度
		$s = round($s* 10000)/10000;
			
		return  round($s);
	}
}
