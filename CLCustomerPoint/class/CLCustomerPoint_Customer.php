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

require_once(CLASS_REALDIR."SC_Customer.php");

/**
 * ポイントシステム連携プラグインの顧客上書きクラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint_Customer extends SC_Customer{
    function getCustomerDataFromEmailPass( $pass, $email, $mobile = false ) {
    	$server = new CLCustomerPoint_CustomerPoint();
    	// 親クラスの呼び出し。
    	$result = parent::getCustomerDataFromEmailPass( $pass, $email, $mobile );
    	if($result){
			$this->customer_data = $server->changeRealPoint($this->customer_data, 0, "");
    	}
    	return $result;
    }

    function getCustomerDataFromMobilePhoneIdPass($pass) {
    	$server = new CLCustomerPoint_CustomerPoint();
    	// 親クラスの呼び出し。
    	$result = parent::getCustomerDataFromEmailPass( $pass );
    	if($result){
			$this->customer_data = $server->changeRealPoint($this->customer_data, 0, "");
    	}
    	return $result;
    }
}
?>
