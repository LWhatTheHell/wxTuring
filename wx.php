<?php
define("TOKEN","wxxx");
define("TURINGKEY","d6cbd5f8ab498744d73764cbfff43729");
/**
* 
*/
$wxObj = new wx();
$wxObj->responseMsg();

class wx{
	public function responseMsg(){
		$postO = $GLOBALS["HTTP_RAW_POST_DATA"];
		if(!empty($postO)){
			$postX = simplexml_load_string($postO);
			$toUserName = $postX->ToUserName;
			$fromUserName = $postX->FromUserName;
			$createTime = time();
			$msgType = $postX->MsgType;
			//!!!!!!!!!!!!!Text start!!!!!!!!!!!!!!!!!
			if($msgType=="text"){
				$template = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";
				$content = $postX->Content;
				//!!!!!!!!!!!turing start!!!!!!!!!!!!
				$turingApi = "http://www.tuling123.com/openapi/api";
				$url = $turingApi."?key=".TURINGKEY."&info=".$content;
				$reply = file_get_contents($url);
				$replyj = json_decode($reply,ture);
				//preg_match('/(?<="text":")[^"]+(?=")/i', $reply,$m);
				//!!!!!!!!turing end!!!!!!!!!!!!!!!
				$text = sprintf($template,$fromUserName,$toUserName,$createTime,$replyj["text"].$replyj["url"]);
				echo $text;
			}
			//!!!!!!!!!!!!!!!Text end!!!!!!!!!!!!!!!!
			elseif($msgType=="event"){
				$event=$postX->Event;
				if($event=="subscribe"){
				$template = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";	
				$content = "欢迎关注WhatWHATwhat,发送文字消息或语音与机器人聊天,将会推出更多功能-。-";
				$text = sprintf($template,$fromUserName,$toUserName,$createTime,$content);
				echo $text;
				}
			elseif($event=="CLICK"){
				$template = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";	
				$eventKey = $postX->EventKey;
				if($eventKey=="1"){
					$content = "你点了消息推送";
					$text = sprintf($template,$fromUserName,$toUserName,$createTime,$content);
					echo $text;
				}
				elseif($eventKey=="2"){
					$content = "你点他干啥";
					$text = sprintf($template,$fromUserName,$toUserName,$createTime,$content);
				}
			}

				else{
					echo success;
				}
			}
			//!!!!!!!!!!!!!!!!!!!!!!!Voice start!!!!!!!!!!!!!!
			elseif($msgType=="voice"){
				$template = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";	
				$recognition = $postX->Recognition;				
				//!!!!!!!!!!!turing start!!!!!!!!!!!!
				$turingApi = "http://www.tuling123.com/openapi/api";
				$url = $turingApi."?key=".TURINGKEY."&info=".$recognition;
				$reply = file_get_contents($url);
				$replyj = json_decode($reply,ture);
				//preg_match('/(?<="text":")[^"]+(?=")/i', $reply,$m);
				//!!!!!!!!turing end!!!!!!!!!!!!!!!
				$text = sprintf($template,$fromUserName,$toUserName,$createTime,$replyj["text"]);
				echo $text;
			}
			//!!!!!!!!!!!!!!!!!!!!!!!Voice end!!!!!!!!!!!!!!!!!!!!!
			}
			else{
				echo success;
			}
		}
		private function getToken(){
			$memc = memcache_init();
			$token = memcache_get($memc,"token");
			if (empty($token)){
				$appid = "wxf8522449819aafc8";
				$appsecret = "e40c98e8dab9791efbd6db3fd97f89be";
				$urlO = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
				$url = sprintf($urlO,$appid,$appsecret);
				$access_token = file_get_contents($url);
				$access_tokenJ = json_decode($access_token,ture);
				memcache_set($memc,"token",$access_tokenJ["access_token"],0,7000);
				$token = memcache_get($memc,"token");
				return $token;
			}
			else{
				return $token;
			}
		}
	}
?>