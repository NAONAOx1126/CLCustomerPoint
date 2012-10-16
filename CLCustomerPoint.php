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

require_once(PLUGIN_UPLOAD_REALDIR."CLCustomerPoint/class/CLCustomerPoint_CustomerPoint.php");

/**
 * ポイントシステム連携プラグインのメインクラス.
 *
 * @package CLCustomerPoint
 * @author Naohisa Minagawa
 * @version 1.0
 */
class CLCustomerPoint extends SC_Plugin_Base {

    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }
    
    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        
        // 顧客テーブルにカラム名を追加
        $objQuery->query("ALTER TABLE  `dtb_customer` ADD  `point_customer_id` INT NULL AFTER  `customer_id` , ADD UNIQUE (`point_customer_id`)");
        
        // 定数マスタデータにデータを追加
        $masterData = new SC_DB_MasterData_Ex();
        $constants = $masterData->getMasterData("mtb_constants");
        if(!isset($constants["CL_CUSTOMER_POINT_HOST"])){
	        $masterData->insertMasterData('mtb_constants', "CL_CUSTOMER_POINT_HOST", "\"member.dev.clay-system.jp\"", "連携するポイントシステムのホスト名");
        }
        if(!isset($constants["CL_CUSTOMER_POINT_BASEDIR"])){
	        $masterData->insertMasterData('mtb_constants', "CL_CUSTOMER_POINT_BASEDIR", "\"/\"", "連携するポイントシステムのベースディレクトリ");
        }

        // キャッシュを再生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
        
        CLCustomerPoint::updateContents($arrPlugin);
    }
    
    function updateContents($arrPlugin){
        // ロゴ画像のコピー
        if(copy(PLUGIN_UPLOAD_REALDIR . "CLCustomerPoint/logo.png", PLUGIN_HTML_REALDIR . "CLCustomerPoint/logo.png") === false);
    }
    
    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        // 顧客テーブルからカラム名を削除
        $objQuery->query("ALTER TABLE  `dtb_customer` DROP  `point_customer_id`");
    }
    
    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }
    
    function loadClassFile(&$plugin_class, &$plugin_classpath) {
    	// クラス処理の差し替え
    	print_r($plugin_class);
    	print_r($plugin_classpath);
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $debug = "";
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_PC:
                break;
            default:
            	// 管理画面
                // 商品一覧画面
                if (strpos($filename, 'customer/edit.tpl') !== false) {
                	// 管理画面からのポイント編集を無効化する予定
                }
            	break;
        }
        $source = $debug.$objTransform->getHTML();
    }
}
?>
