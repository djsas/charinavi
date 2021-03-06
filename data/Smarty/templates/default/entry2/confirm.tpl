<!--{*
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
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_entry">
        <h2 class="title"><!--{$tpl_title|h}--></h2>
        <p>下記の内容で送信してもよろしいでしょうか？<br />
            よろしければ、一番下の「会員登録をする」ボタンをクリックしてください。</p>
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/foreach}-->

        <table summary="入力内容確認">
            <colgroup width="30%"></colgroup>
            <colgroup width="70%"></colgroup>
            <tr>
                <th>ボランティア団体名<span class="attention">※</span></th>
                <td>
                    <!--{$arrForm.name|h}-->
                </td>
            </tr>
            <tr>
                <th>ボランティア団体名(フリガナ)<span class="attention">※</span></th>
                <td>
                    <!--{$arrForm.kana|h}-->
                </td>
            </tr>
            <tr>
                <th>郵便番号<span class="attention">※</span></th>
                <td>
                    〒<!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}-->
                </td>
            </tr>
            <tr>
                <th>住所<span class="attention">※</span></th>
                <td>
                    <!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
                </td>
            </tr>
            <tr>
                <th>電話番号<span class="attention">※</span></th>
                <td>
                    <!--{$arrForm.tel01|h}--> - <!--{$arrForm.tel02|h}--> - <!--{$arrForm.tel03|h}-->
                </td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>
                    <!--{if strlen($arrForm.fax01) > 0 && strlen($arrForm.fax02) > 0 && strlen($arrForm.fax03) > 0}-->
                        <!--{$arrForm.fax01|h}--> - <!--{$arrForm.fax02|h}--> - <!--{$arrForm.fax03|h}-->
                    <!--{else}-->
                        未登録
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>メールアドレス<span class="attention">※</span></th>
                <td>
                    <a href="mailto:<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a>
                </td>
            </tr>
            <tr>
                <th>活動内容<span class="attention">※</span></th>
                <td>
                    <!--{$arrForm.description|h}-->
                </td>
            </tr>
            <tr>
                <th>ログインID<span class="attention">※</span></th>
                <td>
                    <!--{$arrForm.login_id|h}-->
                </td>
            </tr>
            <tr>
                <th>希望するパスワード<span class="attention">※</span><br />
                </th>
                <td><!--{$passlen}--></td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="?" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg','back')" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg','back')"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" border="0" name="back" id="back" /></a>
                </li>
                <li>
                    <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_entry_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_entry.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_entry.jpg" alt="会員登録をする" border="0" name="send" id="send" />
                </li>
            </ul>
        </div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
