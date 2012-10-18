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

require_once(CLASS_REALDIR."helper/SC_Helper_Purchase.php");

/**
 * ポイントシステム連携プラグインの購入処理上書き用クラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint_Helper_Purchase extends SC_Helper_Purchase {
    function sfUpdateOrderStatus($orderId, $newStatus = null, $newAddPoint = null, $newUsePoint = null, &$sqlval) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));
      	SC_Helper_Purchase::sfUpdateOrderStatus($orderId, $newStatus, $newAddPoint, $newUsePoint, $sqlval);
      	
        if (USE_POINT !== false) {
			$customer = $objQuery->getRow("*", "dtb_customer", "customer_id = ?", array($arrOrderOld['customer_id']));
		
	    	$server = new CLCustomerPoint_CustomerPoint();
	        $site = SC_Helper_DB_Ex::sfGetBasisData();
	        $shop_name = $site["shop_name"];

            // ▼使用ポイント
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($arrOrderOld['status']) && !$this->isUsePoint($newStatus)) {
				$customer = $server->changeRealPoint($customer, $arrOrderOld['use_point'], "EC【".$shop_name."】受注（ID: ".$orderId."）利用ポイント戻し");
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if (!$this->isUsePoint($arrOrderOld['status']) && $this->isUsePoint($newStatus)) {
				$customer = $server->changeRealPoint($customer, - $arrOrderOld['use_point'], "EC【".$shop_name."】受注（ID: ".$orderId."）利用ポイント");
            }

            // ▲使用ポイント

            // ▼加算ポイント
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($arrOrderOld['status']) && !$this->isAddPoint($newStatus)) {
				$customer = $server->changeRealPoint($customer, - $arrOrderOld['add_point'], "EC【".$shop_name."】受注（ID: ".$orderId."）加算ポイント戻し");
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if (!$this->isAddPoint($arrOrderOld['status']) && $this->isAddPoint($newStatus)) {
				$customer = $server->changeRealPoint($customer, $arrOrderOld['add_point'], "EC【".$shop_name."】受注（ID: ".$orderId."）加算ポイント");
            }
            // ▲加算ポイント
        }
    }
}
?>
