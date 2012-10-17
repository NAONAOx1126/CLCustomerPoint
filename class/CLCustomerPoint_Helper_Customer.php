<?php
/*
 * CLAY Customer Point Plugin
 * Copyright (C) 2012 Naohisa Minagawa All Rights Reserved.
 * http://www.clay-system.jp/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once(CLASS_REALDIR."helper/SC_Helper_Customer.php");

/**
 * ポイントシステム連携プラグインの顧客ヘルパー上書きクラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint_Helper_Customer extends SC_Helper_Customer{
    function sfEditCustomerData($array, $customer_id = null) {
    	echo "call plugin";
    	// 親クラスの呼び出し。
    	$customer_id = SC_Helper_Customer::sfEditCustomerData($array, $customer_id);
    	echo $customer_id."<br>";
    	
    	// 登録した顧客データの取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $customer = $objQuery->getRow("*", "dtb_customer", "customer_id = ?", array($customer_id));
    	
    	// APIでポイントシステムと連携
    	$server = new CLCustomerPoint_CustomerPoint();
    	$customer = $server->registerRealCustomer($customer);
    	
    	// 結果を返却
    	return $customer_id;
    }	
}
?>
