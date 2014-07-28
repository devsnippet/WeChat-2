<?php
/*连接*/
		define("TOKEN","weixin");
		$wechatObj = new wechatCallbackapiTest();
		if(isset($_GET['echostr'])){
			$wechatObj->valid();
		}else{
			$wechatObj->responseMsg();
		}
		
		class wechatCallbackapiTest{
			public function valid(){
				$echoStr = $_GET['echostr'];
				if($this->checkSignature()){
					echo $echoStr;
					exit;
				}
			}			
			private function checkSignature(){
				$signature = $_GET["signature"];
				$timestamp = $_GET["timestamp"];
				$nonce = $_GET["nonce"];
				
				$token = TOKEN;
				$tmpArr = array($token,$timestamp,$nonce);
				sort($tmpArr);
				/*将数组转化为字符串数组*/
				$tmpStr = implode($tmpArr);
				/*加密*/
				$tmpStr = sha1($tmpStr);
				
				if($tmpStr = $signature){
					return true;
				}else{
					return false;
				}
			}
			
			public function responseMsg(){
				$postStr = $_GLOBALS['HTTP_RAW_POST_DATA'];
				
				if(!empty($postStr)){
					$postObj = simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
					$RX_TYPE = trim($postObj->MsgType);
					//用户发送的消息类型判断
				switch($RX_TYPE)
				{
					case "text":
						$result = $this->receiveText($postObj);
						break;
					case "image":
						$result = $this->receiveImage($postObj);
						break;
					case "voice":
						$result = $this->receiveVoice($postObj);
						break;
					case "video":
						$result = $this->receiveVideo($postObj);
						break;
					case "location":
						$result = $this->receiveLocation($postObj);
						break;
					case "link":
						$result = $this->receiveLink($postObj);
						break;
					default:
						$result = "unknow msg type".$RX_TYPE;
						break;
				}
				echo $result;
				}else{
					echo "";
					exit;
				}
				
		}
		/*接收消息*/
		private function receiveText($object){
			$content = "你发送的是文本，内容为：".$object->content;
			$result = $this->transmitText($object,$content);
			return $result;
		}
		private function receiveImage($object){
			$content = "你发送的是图片，内容为:".$object->PicUrl;
			$result = $this->transmitText($object.$content);
			return $result;
		}
		private function receiveVoice($object){
			$content = "你发送的是语音，媒体ID为：".$object->MediaId;
			$result = $this->transmitText($object.$content);
			return $result;
		}
		private function receiveVideo($object){
			$content = "你发送的是视频，媒体ID为：".$object->MediaId;
			$result = $this->transmitText($object.$content);
			return $result;
		}
		private function receiveLocation($object){
			$content = "你发送的是位置，纬度为：".$object->Location_X.";经度为:".$object->Location_Y.";缩放级别为：".$object->Scale.";位置为：".$object->Label;
			$result = $this->transmitText($object.$content);
			return $result;
		}
		private function receiveLink($object){
			$content = "你发送的是链接，标题为:".$object->Title.";内容为:".$object->Description.";链接地址为：".$object->Url;
			$result = $this->transmitText($object.$content);
			return $result;
		}
		private function transmitText($object,$content){
			$textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
			$result = sprintf($textTpl,,$object->FromUserName,$object->ToUserName,time(),$content);
			return $result;
		}
	}
?>