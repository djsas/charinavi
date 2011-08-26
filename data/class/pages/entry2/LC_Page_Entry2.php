<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Entry.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Entry2 extends LC_Page_Ex {

    // {{{ properties

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ボランティア団体新規登録";
        $this->tpl_mainpage = 'entry2/index.tpl';
        $this->tpl_page_category = 'entry2';


        // マスタ-データから権限配列を取得
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();

        SC_Helper_Volunteer_Ex::sfCustomerEntryParam($objFormParam);
        $objFormParam->setParam($_POST);
        $this->arrForm  = $objFormParam->getHashArray();

        // PC時は規約ページからの遷移でなければエラー画面へ遷移する
        if ($this->lfCheckReferer($this->arrForm, $_SERVER['HTTP_REFERER']) === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
        }

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($this->arrForm['return'])) {
            $_POST['mode'] = 'return';
        }

        switch ($this->getMode()) {
        case 'confirm':
            //-- 確認
            $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);
            // 入力エラーなし
            if(empty($this->arrErr)) {
                //パスワード表示
                $this->passlen      = SC_Utils_Ex::sfPassLen(strlen($this->arrForm['password']));

                $this->tpl_mainpage = 'entry2/confirm.tpl';
                $this->tpl_title    = '会員登録(確認ページ)';
            }
            break;
        case 'complete':
            //-- 会員登録と完了画面
            $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);
            if(empty($this->arrErr)) {

                $uniqid             = $this->lfRegistCustomerData($this->lfMakeSqlVal($objFormParam));

                $this->tpl_mainpage = 'entry2/complete.tpl';
                $this->tpl_title    = '会員登録(完了ページ)';
                $this->lfSendMail($uniqid, $this->arrForm);

                // 仮会員が無効の場合
                if(CUSTOMER_CONFIRM_MAIL == false) {
                    // ログイン状態にする
                    $objCustomer = new SC_Customer_Ex();
                    $objCustomer->setLogin($this->arrForm['email']);
                }

                // 完了ページに移動させる。
                SC_Response_Ex::sendRedirect('complete.php', array('ci' => SC_Helper_Customer_Ex::sfGetCustomerId($uniqid)));
            }
            break;
        default:
            break;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // }}}
    // {{{ protected functions
    /**
     * 会員情報の登録
     *
     * @access private
     * @return uniqid
     */
    function lfRegistCustomerData($sqlval) {
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval);
        return $sqlval["secret_key"];
    }

    /**
     * 会員登録に必要なSQLパラメータの配列を生成する.
     *
     * フォームに入力された情報を元に, SQLパラメータの配列を生成する.
     * モバイル端末の場合は, email を email_mobile にコピーし,
     * mobile_phone_id に携帯端末IDを格納する.
     *
     * @param mixed $objFormParam
     * @access private
     * @return $arrResults
     */
    function lfMakeSqlVal(&$objFormParam) {
        $arrForm                = $objFormParam->getHashArray();
        $arrResults             = $objFormParam->getDbArray();

        // 生年月日の作成
        $arrResults['birth']    = SC_Utils_Ex::sfGetTimestamp($arrForm['year'], $arrForm['month'], $arrForm['day']);

        // 仮会員 1 本会員 2
        $arrResults['status']   = (CUSTOMER_CONFIRM_MAIL == true) ? "1" : "2";

        /*
         * secret_keyは、テーブルで重複許可されていない場合があるので、
         * 本会員登録では利用されないがセットしておく。
         */
        $arrResults["secret_key"] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();

        // 入会時ポイント
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $arrResults['point'] = $CONF["welcome_point"];

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 携帯メールアドレス
            $arrResults['email_mobile']     = $arrResults['email'];
            // PHONE_IDを取り出す
            $arrResults['mobile_phone_id']  =  SC_MobileUserAgent_Ex::getId();
        }
        return $arrResults;
    }

    /**
     * 会員登録完了メール送信する
     *
     * @access private
     * @return void
     */
    function lfSendMail($uniqid, $arrForm){
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        $objMailText    = new SC_SiteView_Ex();
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign("name01", $arrForm['name01']);
        $objMailText->assign("name02", $arrForm['name02']);
        $objMailText->assign('uniqid', $uniqid);
        $objMailText->assignobj($this);

        $objHelperMail  = new SC_Helper_Mail_Ex();

        // 仮会員が有効の場合
        if(CUSTOMER_CONFIRM_MAIL == true) {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
        } else {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
        }

        $objMail = new SC_SendMail();
        $objMail->setItem(
            ''                    // 宛先
            , $subject              // サブジェクト
            , $toCustomerMail       // 本文
            , $CONF["email03"]      // 配送元アドレス
            , $CONF["shop_name"]    // 配送元 名前
            , $CONF["email03"]      // reply_to
            , $CONF["email04"]      // return_path
            , $CONF["email04"]      // Errors_to
            , $CONF["email01"]      // Bcc
        );
        // 宛先の設定
        $objMail->setTo($arrForm['email'],
                        $arrForm["name01"] . $arrForm["name02"] ." 様");

        $objMail->sendMail();
    }

    /**
     * kiyaku.php からの遷移の妥当性をチェックする
     *
     * 以下の内容をチェックし, 妥当であれば true を返す.
     * 1. 規約ページからの遷移かどうか
     * 2. PC及びスマートフォンかどうか
     * 3. $post に何も含まれていないかどうか
     *
     * @access protected
     * @param array $post $_POST のデータ
     * @param string $referer $_SERVER['HTTP_REFERER'] のデータ
     * @return boolean kiyaku.php からの妥当な遷移であれば true
     */
    function lfCheckReferer(&$post, $referer){

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
            && empty($post)
            && (preg_match('/kiyaku.php/', basename($referer)) === 0)) {
            return false;
            }
        return true;
    }
}

// D.S.G.
