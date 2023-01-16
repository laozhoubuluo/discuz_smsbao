<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class smsgw_smsbao {

	var $version = '1.1';
	var $name = '短信宝短信网关接口';
	var $description = '本插件方便用户对接短信宝短信网关接口，以发送短信验证码或者站点通知。';
	var $copyright = '<a href="https://laozhou.org/" target="_blank">老周部落</a>';
	var $customname = '';
	var $sendrule = '86';
	var $type = '0';

	function __construct() {
		$currentlang = currentlang();
		switch($currentlang) {
			case 'SC_UTF8':
				$this->name = '短信宝短信网关接口';
				$this->description = '本插件方便用户对接短信宝短信网关接口，以发送短信验证码或者站点通知。';
				break;
			case 'TC_UTF8':
				$this->name = '短信寶簡訊閘道介面';
				$this->description = '本外掛程式方便用戶對接簡訊寶簡訊閘道介面，以發送簡訊驗證碼或者網站通知。';
				break;
			default:
				$this->name = 'SMSBao SMS Gateway Interface';
				$this->description = 'This plugin is convenient for users connect to the SMSBao SMS gateway interface to send SMS verification codes or site notifications.';
				break;
		}
	}

	function send($uid, $smstype, $svctype, $secmobicc, $secmobile, $content) {
		global $_G;

		if(!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}

		if($secmobicc == '86') {
			$operation = 'sms';
			$mobile = $secmobile;
		} else {
			$operation = 'wsms';
			$mobile = '+' . $secmobicc . $secmobile;
		}

		if($smstype) {
			$content = '【'.$_G['cache']['plugin']['laozhoubuluo_smsbao']['smsheader'].'】' . $content['content'];
		} else {
			$content = str_replace(array('{code}'), array($content['content']), $_G['cache']['plugin']['laozhoubuluo_smsbao']['codetemplate']);
			$content = '【'.$_G['cache']['plugin']['laozhoubuluo_smsbao']['smsheader'].'】' . $content;
		}

		$endpoint = 'https://api.smsbao.com/';
		
		$smsinfo = array(
			'username' => $_G['cache']['plugin']['laozhoubuluo_smsbao']['username'],
			'apikey' => $_G['cache']['plugin']['laozhoubuluo_smsbao']['apikey'],
			'mobile' => urlencode($mobile),
			'content' => urlencode($content),
		);

		$url = $endpoint . $operation . '?u=' . $smsinfo['username'] . '&p=' . $smsinfo['apikey'] . '&m=' . $smsinfo['mobile'] . '&c=' . $smsinfo['content'];

		$params = array(
			'url' => $url,
			'method' => 'GET'
		);

		$client = filesock::open($params);
		$data = $client->request();
		if(!$data) {
			$data = $client->filesockbody;
		}

		$result = json_decode($data);
		return $result == 0 ? true : -9;
	}

	function getsetting() {
		$settings = array();
		return $settings;
	}

	function setsetting() {
		return true;
	}

}