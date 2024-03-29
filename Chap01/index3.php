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
					//关注/取消关注事件
					case "event":
						$result = $this->receiveEvent($postObj);
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
			$keyword = trim($object->Content);
			if($keyword=="文本"){
				//回复文本消息
				$content="这是个文本消息";
				$result=$this->transmitText($object,$content);
			}else if($keyword=="图文"||$keyword=="单图文"){
				//回复图文消息
				$content=array();
				$content[]=array("Title"=>"单图文标题","Description"=>"单图文内容","PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
				"Url"=>"http://m.cnblogs.com/?u=txw1958");
				$result=$this->transmitNews($object,$content);
			}else if($keyword=="多图文"){
				//回复多图文消息
				$content=array();
				$content[]=array("Title"=>"多图文标题1","Description"=>"","PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
				"Url"=>"http://m.cnblogs.com/?u=txw1958");
				$content[]=array("Title"=>"多图文标题2","Description"=>"","PicUrl"=>"http://d.hiphotos.dbimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg",
				"Url"=>"http://m.cnblogs.com/?u=txw1958");
				$content[]=array("Title"=>"多图文标题3","Description"=>"","PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg",
				"Url"=>"http://m.cnblogs.com/?u=txw1958");
				$result=$this->transmitNews($object,$content);
			}else if ($keyword=="音乐"){
				$content=array("Title"=>"最炫的民族风","Description"=>"歌手：凤凰传奇","MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3"
				,"HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
				$result=$this->transmitMusic($object,$content);
			}
			return $result;
		}
		private function receiveImage($object){
			$content=array("MediaId"=>$object->MediaId);
			$result=$this->transmitImage($object,$content);
			return $result;
		}
		private function receiveVoice($object){
			$content=array("MediaId"=>$object->MediaId);
			$result=$this->transmitVoice($object,$content);
			return $result;
		}
		private function receiveVideo($object){
			$content=array("MediaId"=>$object->MediaId,"ThumbMediaId"=>$object->ThumbMediaId,"Title"=>"","Description"=>"");
			$result=$this->transmitVideo($object,$content);
			return $result;
		}
		//关注/取消关注事件
		private function receiveEvent($object){
			$content= "";
			switch($object->Event){
			case "subscribe"://关注事件
				$content = "欢迎关注猩猩的小黑屋";
				break;
			case "unsubscribe":
				$content = "";
				break;
			$result = $this->transmitEvent($object,$content);
			return $result;
			}
		}
		
		private function transmitText($object,$content){
			$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time(),$content);
			return $result;
		}
		private function transmitImage($object,$content){
			$itemTpl="<Image><MediaId>[!CDATA[%s]]</MediaId></Image>";
			$item_str=sprintf($itemTpl,$imageArray['MediaId']);
			$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[image]]></MsgType>
			$item_str	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
			return $result;	
		}
		private function transmitVoice($object,$content){
			$itemTpl="<Voice><MediaId>[!CDATA[%s]]</MediaId></Voice>";
			$item_str=sprintf($itemTpl,$voiceArray['MediaId']);
			$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[voice]]></MsgType>
			$item_str	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
			return $result;
		}
		private function transmitVideo($object,$content){
			$itemTpl="<Video>
			<MediaId>[!CDATA[%s]]</MediaId>
			<ThumbMediaId>[!CDATA[%s]]</ThumbMediaId>
			<Title>[!CDATA[%s]]</Title>
			<Description>[!CDATA[%s]]</Description>
			</Video>";
			$item_str=sprintf($itemTpl,$videoArray['MediaId'],$videoArray['ThumbMediaId'],$videoArray['Title'],$videoArray['Description']);
			$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[video]]></MsgType>
			$item_str	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
			return $result;
		}
		private function transmitNews($object,$arr_item){
			$if(!is_array($arr_item))
				return;
			
			$itemTpl="<item>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>";
			
			$item_str="";
			foreach($arr_item as $item)
				$item_str .= sprintf($itemTpl,$item['Title'],$item['Description'],$item['PicUrl'],$item['Url']);
				
			$newsTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<Content><![CDATA[]]></Content>
			<ArticleCount>%s</ArticleCount>
			<Articles>
			$item_str</Articles>	
			</xml>";	
			
			$result=sprintf($newsTpl,,$object->FromUserName,$object->ToUserName,time(),count($arr_item));
			return $result;
				
		}
		private function transmitMusic($object,$musicArray){
			$itemTpl="<Music>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<MusicUrl><![CDATA[%s]]></MusicUrl>
			<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
			</Music>";
			
			$item_str=sprintf($itemTpl,$musicArray['Title'],$musicArray['Description'],$musicArray['MusicUrl'],$musicArray['HQMusicUrl']);
			
			$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[music]]></MsgType>
			$item_str	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
			return $result;
		}
		//关注/取消关注事件
		private function transmitEvent($object){
				$textTpl="<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>	
			</xml>";
			$result=sprintf($textTpl,$object->FromUserName,$object->ToUserName,time(),$content);
			return $result;
		}
	}
?>