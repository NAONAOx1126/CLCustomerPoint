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

require_once(PLUGIN_UPLOAD_REALDIR."CLCustomerPoint/class/CLCustomerPoint_JsonObject.php");

/**
 * ポイントシステム連携プラグイン連携用クラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint_CustomerPoint extends CLCustomerPoint_JsonObject{
	/**
	 * 顧客データをポイントシステム側に送信
	 */
	function registerRealCustomer($customer){
		$data = array();
        // 店舗基本情報取得
        $info = SC_Helper_DB_Ex::sfGetBasisData();
        // マスタデータ
        $masterData = new SC_DB_MasterData_Ex();
        // 職業マスタ
        $jobs = $masterData->getMasterData("mtb_job");
        
		// EC-CUBE側の顧客IDは顧客コードとして扱う
		$data["customer_id"] = $customer["point_customer_id"];
		$data["customer_code"] = $customer["customer_id"];
		$data["sei"] = $customer["name01"];
		$data["mei"] = $customer["name02"];
		$data["sei_kana"] = $customer["kana01"];
		$data["mei_kana"] = $customer["kana02"];
		$data["zip1"] = $customer["zip01"];
		$data["zip2"] = $customer["zip02"];
		$data["pref"] = $customer["pref"];
		$data["address1"] = $customer["addr01"];
		$data["address2"] = $customer["addr02"];
		$data["email"] = $customer["email"];
		$data["email_mobile"] = $customer["email_mobile"];
		$data["tel1"] = $customer["tel01"];
		$data["tel2"] = $customer["tel02"];
		$data["tel3"] = $customer["tel03"];
		$data["sex"] = $customer["sex"];
		$data["job"] = $jobs[$customer["job"]];
		$data["birthday"] = $customer["birth"];
		$data["customer_type"] = $info["shop_name"];
		$data["active_flg"] = "1";
		$result = $this->call("member", "RegisterCustomer", $data);
		// 登録後にポイントを更新
        $objQuery =& SC_Query_Ex::getSingletonInstance();
		$objQuery->update("dtb_customer", array("point" => $result->point, "point_customer_id" => $result->customer_id), "customer_id = ?", array($customer["customer_id"]));
		$customer["point_customer_id"] = $result->customer_id;
		$customer["point"] = $result->point;
		return $customer;
	}

	/**
	 * ポイント履歴をポイントシステム側に送信する
	 * ポイントを0ポイントに設定した場合は現在のポイントを取得するのみとなる。
	 */
	function changeRealPoint($customer, $point, $comment){
        // 店舗基本情報取得
        $info = SC_Helper_DB_Ex::sfGetBasisData();
		$result = $this->call("member", "AddPoint", array("customer_id" => $customer["point_customer_id"], "point" => $point, "comment" => $comment));
        $objQuery =& SC_Query_Ex::getSingletonInstance();
		$objQuery->update("dtb_customer", array("point" => $result->point), "customer_id = ?", array($customer["customer_id"]));
		$customer["point"] = $result->point;
		return $customer;
	}
}
?>
