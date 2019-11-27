<?php
header("Content-type:text/html;charset=utf-8");
	include 'common/function.php';
	$isMobile = is_mobile_request();
	if( $_POST ){
		$isshow = true;
		$data = $_POST;
		// inputs
		$cloanterm = $data['cloanterm'];//贷款期限
		if($cloanterm > 30 || $cloanterm <1){
			echo '<script>alert("请检查贷款期限是否符合要求！");window.history.go(-1);</script>';
			exit;
		}
		if(!$data['cinterest'] || $data['cinterest'] == 0){
			echo '<script>alert("请检查利率是否大于0！");window.history.go(-1);</script>';
			exit;
		}
		if(!$data['cholding'] || $data['cholding'] == 0){
			echo '<script>alert("请检查持有年份是否至少1年！");window.history.go(-1);</script>';
			exit;
		}
		$cinterest = $data['cinterest']/100;//利率
		
		$crentincrease = $data['crentincrease']/100;// 每月租金年增加
		$cvacancy = $data['cvacancy']/100;// 空置率
		
		// Key Variables
		$loan_limit = $data['cprice']*(1-$data['cdownpayment']/100); //贷款额度 本金
		$monthly_repayment = $cloanterm * 12;//还贷月数
		$monthly_rate = $cinterest/12;//月利率
		
		$monthly_ctax = $data['ctax']/12;//每月地产税
		$monthly_cinsurance = $data['cinsurance']/12;//每月保险费
		$monthly_choa = $data['choa']/12;//每月物业费
		$monthly_cmaintenance = $data['cmaintenance']/12;//每月维修保养费
		$monthly_cother = $data['cother']/12;//每月其他费用
		
		
		//按揭付款=本金*月利率*(1+月利率)^n/[(1+月利率)^n-1] 
		$mortgage_payment = $loan_limit*$monthly_rate*pow((1+$monthly_rate),$monthly_repayment)/(pow((1+$monthly_rate),$monthly_repayment)-1) ;
		
		$list = array();
		for($i = 1; $i <= $cloanterm; $i++){
			$temp = array();
			for($y = 1; $y <= 12; $y++){
				$temp['year'] = $i;
				$temp['month'] = ($i-1)*12+$y;
				array_push($list,$temp);
			}
		}
		//第一年净收入Net income in the first year = SUM(Output!$P$3:$P$14)
		$nincome_first_year = 0;
		$all_total = 0;
		
		foreach($list as $k=>&$v){
			// 按揭付款
			$v['mortgage_payment'] =  $mortgage_payment;
			// 按揭余额
			if($k == 0){
				$v['mortgage_balance'] = $loan_limit;
			}else{
				$v['mortgage_balance'] = $list[$k-1]['loan_mortgage_balance'];
			}
			// 已付利息
			$v['interest_paid'] = $v['mortgage_balance'] * $cinterest / 12;
			// 已付本金
			$v['principal_paid'] = $v['mortgage_payment'] - $v['interest_paid'];
			// 抵押贷款余额
			$v['loan_mortgage_balance'] = $v['mortgage_balance']-$v['principal_paid'];
			/**/// 每月租金
			$v['crent'] = $data['crent'] * (1-$cvacancy) * pow((1+$crentincrease),($v['year']-1));
			// 其他月收入
			$v['cotherincome'] = $data['cotherincome']*(1-$cvacancy)*pow((1+$crentincrease),($v['year']-1));
			$v['cotherincome'] = $v['cotherincome']==0?'-':$v['cotherincome'];
			// 托管管理费 
			$v['cmanagement'] = $data['cmanagement']/100 * $data['crent']*pow((1+$crentincrease),($v['year']-1));
			// 地产税
			$v['ctax'] = $monthly_ctax*pow((1+$data['ctaxincrease']/100),($v['year']-1));
			// 保险 
			$v['cinsurance'] = $monthly_cinsurance*pow((1+$data['cinsuranceincrease']/100),($v['year']-1));
			// 物业费 
			$v['choa'] = $monthly_choa*pow((1+$data['choaincrease']/100),($v['year']-1));
			// 维修保养 
			$v['cmaintenance'] = $monthly_cmaintenance*pow((1+$data['cmaintenanceincrease']/100),($v['year']-1));
			// 其他费用='Key Variables'!$B$7*(1+Inputs!$C$24)^(Output!$A3-1)
			$v['cother']= $monthly_cother*pow((1+$data['cotherincrease']/100),($v['year']-1));
			// total=H3+I3-J3-K3-L3-M3-N3-O3
			$v['total'] = $v['crent']+$v['cotherincome']-$v['cmanagement']-$v['ctax']-$v['cinsurance']-$v['choa']-$v['cmaintenance']-$v['cother'];
			
			if($v['year'] == 1){
				$nincome_first_year += $v['total'];
			}
			if($data['cholding']  >=  $v['year'] ){
				$all_total += $v['total'];
			}
		}
		// 第一年租金回报率
		$rent_return_first_year = $nincome_first_year/$data['cprice']*100;
		// 持有年限平均租金回报率
		$avg_retrun_rate = (pow( (1+$all_total/$data['cprice']),(1/$data['cholding']))-1) * 100;
		
		// 持有年限总回报率（租金回报率+增值率
		$total_retrun_rate = $avg_retrun_rate + $data['cappreciation'];
		
	}else{
		$isshow = false;
	}
	include 'template/rent.html';