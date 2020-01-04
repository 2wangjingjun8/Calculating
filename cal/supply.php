<?php
header("Content-type:text/html;charset=utf-8");
$list=array();
if( $_POST ){
	$isshow = true;
	$data = $_POST;
	$cholding = $data['cholding'];
	if($cholding > 50 || $cholding <1){
		echo '<script>alert("持有年限不符合要求！");window.history.go(-1);</script>';
		exit;
	}
	if(!$data['cinterest'] || $data['cinterest'] == 0){
		echo '<script>alert("请检查利率是否大于0！");window.history.go(-1);</script>';
		exit;
	}
	$cinterest = $data['cinterest']/100;//利率
	
	$list = array();
	for($i = 1; $i <= $cholding; $i++){
		$temp = array();
		$temp['year'] = $i;
		array_push($list,$temp);
	}
		
	$loan_limit = $data['cprice'] - $data['cdownpayment']; //本金
	$monthly_rate = $cinterest/12;//月利率
	$monthly_repayment = $cholding * 12;//还贷月数
	//月供本息支出=本金*月利率*(1+月利率)^n/[(1+月利率)^n-1] 
	$mortgage_payment = $loan_limit*$monthly_rate*pow((1+$monthly_rate),$monthly_repayment)/(pow((1+$monthly_rate),$monthly_repayment)-1) ;
	//月供本息总支出
	$mortgage_payment_total = $mortgage_payment * $monthly_repayment + $data['cothercost'] + $data['cdownpayment'];
	//第一个月包括地税和保险的月供金额=B17+B9/12+B11/12
	$first_m_amount = $mortgage_payment + $data['ctax']/12 + $data['cinsurance']/12;
	// 总地产税
	$total_ctax = 0;
	// 总保险支出
	$total_cinsurance = 0;
	// 年本息支出NPV
	$principal_interest_exp_NPV = 0;
	$ctax_exp_NPV = 0;
	$cinsurance_exp_NPV = 0;
	
	foreach($list as $k=>&$v){
		$v['principal_interest_exp'] = $mortgage_payment * 12;
		if($k == 0){
			$v['ctax'] = $data['ctax'];
			$v['cinsurance'] = $data['cinsurance'];
		}else{
			$v['ctax'] = $list[$k-1]['ctax'] * (1 + $data['ctaxincrease']/100);
			$v['cinsurance'] = $list[$k-1]['cinsurance'] * (1 + $data['cinsuranceincrease']/100);
		}
		$total_ctax += $v['ctax'];
		$total_cinsurance += $v['cinsurance'];
		$principal_interest_exp_NPV += $v['principal_interest_exp']/pow((1+$data['cinflation']/100),($v['year']-1));
		$ctax_exp_NPV += $v['ctax']/pow((1+$data['cinflation']/100),($v['year']-1));
		$cinsurance_exp_NPV += $v['cinsurance']/pow((1+$data['cinflation']/100),($v['year']-1));
	}
	// 地产税和保险总支出
	$total_ctax_cinsurance = $total_ctax + $total_cinsurance;
	// 包括地税和保险的月供总额 = $mortgage_payment_total + $total_ctax_cinsurance
	$ctax_cinsurance_m_amount = $mortgage_payment_total + $total_ctax_cinsurance;
	
	// 月供本息的现值（计入通货膨胀）
	$principal_interest_total_NPV = $principal_interest_exp_NPV + $data['cothercost'] + $data['cdownpayment'];
	// 地产税和保险总支出的现值（计入通货膨胀）
	$ctax_cinsurance_total_NPV = $ctax_exp_NPV + $cinsurance_exp_NPV;
	// 月供总额的现值（计入通货膨胀）
	$mortgage_payment_total_NPV = $principal_interest_total_NPV + $ctax_cinsurance_total_NPV;

}else{
	$isshow = false;
}
include 'template/supply.html';