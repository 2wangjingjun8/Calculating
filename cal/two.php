<?php
header("Content-type:text/html;charset=utf-8");
if($_POST){
	$is_show = true;
	$data = $_POST;
	$cholding = $data['cholding'];//持有年限
	if($cholding > 50 || $cholding <1){
		echo '<script>alert("持有年限不符合要求！");window.history.go(-1);</script>';
	}
	$list = array();
	for($i=1;$i<=$cholding;$i++){
		$tmp = array();
		$tmp['year'] = $i; // 年
		array_push($list,$tmp);
	}
	$cprice = $data['cprice'];// 房价 
	$total_rent = 0;// 总租金收入
	$total_rent_v = 0;// 总租金收入现值

	foreach($list as $k=>&$v){
		// 年租金收入
		if($k == 0){
			$v['yrent_income'] = 12 * $data['mcrent'];
		}else{
			$v['yrent_income'] = $list[$k-1]['yrent_income'] * (1 + $data['crent']/100);
		}
		$total_rent += $v['yrent_income'];
		// 年租金收入现值
		$v['yrent_income_v'] = $v['yrent_income']/pow((1+$data['cinflation']/100),($v['year']-1));
		$total_rent_v += $v['yrent_income_v'];
	}
	$total_cost = $cprice - $total_rent_v;
	
}else{
	$is_show = false;
}
include "template/two.html";