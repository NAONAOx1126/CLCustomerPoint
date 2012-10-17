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

/**
 * ポイントシステム連携用JSONインターフェイスのクラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint_JsonObject{
	protected function call($package, $class, $params){
        // マスタデータ
        $masterData = new SC_DB_MasterData_Ex();
        // 職業マスタ
        $constants = $masterData->getMasterData("mtb_constants");
        
		$protocol = "http";
		$host = $constants["CL_CUSTOMER_POINT_HOST"];
		$port = "80";
		echo $host."<br>";
		if(($fp = fsockopen($host, $port)) !== FALSE){
			fputs($fp, "POST ".$constants["CL_CUSTOMER_POINT_BASEDIR"]."jsonp.php HTTP/1.0\r\n");
			fputs($fp, "Host: ".$host."\r\n");
			fputs($fp, "User-Agent: CLAY-JSON-CALLER\r\n");
			$data = "";
			$data .= "c=".urlencode($package);
			$data .= "&p=".urlencode($class);
			foreach($params as $key => $value){
				$data .= "&".urlencode($key)."=".urlencode($value);
			}
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: ".strlen($data)."\r\n");
			fputs($fp, "\r\n");
			fputs($fp, $data);
			echo $data;
			$response = "";
			while(!feof($fp)){
				$response .= fgets($fp, 4096);
			}
			echo $response;
			fclose($fp);
			$result = split("\r\n\r\n", $response, 2);
			return json_decode($result[1]);
		}
		return null;
	}
}
?>
