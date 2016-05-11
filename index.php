<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "WGweixindate");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();注释掉
$wechatObj->responseMsg();//调用回复信息方法
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$dbname = 'gQFhtkjbieVqGqjgtvsf';
/*填入数据库连接信息*/
$host = 'sqld.duapp.com';
$port = 4050;
$user = '5b6035fd87d3490a9e9ae05537fbc907';//用户AK
$pwd = 'f90180e2df384fc3b6e059347e5d8139';//用户SK
 /*以上信息都可以在数据库详情页查找到*/

/*接着调用mysql_connect()连接服务器*/
/*为了避免因MySQL数据库连接失败而导致程序异常中断，此处通过在mysql_connect()函数前添加@，来抑制错误信息，确保程序继续运行*/
/*有关mysql_connect()函数的详细介绍，可参看http://php.net/manual/zh/function.mysql-connect.php*/
$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
if(!$link) {
    die("Connect Server Failed: " . mysql_error());
}
/*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
if(!mysql_select_db($dbname,$link)) {
    die("Select Database Failed: " . mysql_error($link));
}

/*至此连接已完全建立，就可对当前数据库进行相应的操作了*/

$sql = "SELECT * FROM `weixin` WHERE `tittle`  = '{$keyword}' LIMIT 0, 30 ";
$query=mysql_query($sql);//执行sql语句
$rs=mysql_fetch_array($query);//获取sql语句结果
if (!empty( $rs['content']))
{$contentStr=$rs['content'];}
else
					{$contentStr = "Hi,欢迎您关注郑州市维纲中学数字化中心!学潜报告结果查询请按照格式（姓名+测评日期，如：张三20160101）输入即可查询。";}
/*显式关闭连接，非必须*/
mysql_close($link);
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>