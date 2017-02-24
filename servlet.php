<?php
if (file_exists('./appconf.php')) {//check app configure file.
	include("./appconf.php");//include app configure file.
	if (constant('enabled')=='false') {
		header('Content-type: application/x-javascript; charset=utf-8');//javascript output standard configure
		die('var tests = [["系统维护中"]]');
	}
}
else {//if could not found app configure file,show json error output.
	header('Content-type: application/json; charset=utf-8');//json output standard configure
	die('{"status":"error"}');//json error ourput
}
?>
<?php
$act = _get('act');//import url action to variable
switch ($act) {//action judge
	case "addmsg":
		DoAddMsg();//add message action
		break;
	case "delmsg":
		DoDelMsg();//delete message action
		break;
	case "getmsg":
		DoGetMsg();//get message action
		break;
	case "authentication":
		DoAuth();//authentication action for delete message action
		break;
	case "error":
		MyErrorHandler();//error handler action
		break;
	default:
		header('Location: ./servlet.php?act=error');//default action
		break;
}
function DoAddMsg() {
	if (constant('enabled')=='false') {
		header('Content-type: application/json; charset=utf-8');//json output standard configure
		die('{"status":"error"}');
	}
	if (constant('AddEnabled')=='false') {
		header('Content-type: application/json; charset=utf-8');//json output standard configure
		die('{"status":"error"}');
	}
	header('Content-type: application/json; charset=utf-8');//json output standard configure
	@$res = $_POST['input'];//import post data to my resource container
	if ($res == '') {//if my resource is null,print error.
		die('{"status":"error"}');
	}
	else {//end operation finish to print pass
		$uip = $_SERVER["REMOTE_ADDR"];//get user ip address
		date_default_timezone_set("Asia/Shanghai");//set timezone to china
		$srvtime = date("ymdhis",time());//get server time
		$sqlconn = new MySQLi(constant('mysql_server_name'),constant('mysql_username'),constant('mysql_password'),constant('mysql_dbname'));
		if (!$sqlconn) {// if not connect mysql database to show error information
			die('{"status":"error","info":"'.$sqlconn->connect_error.'"}');
		}
		//$sqlconn->query("set names 'utf-8'");//set encode of MySQL query
		$result=$sqlconn->query("insert into ".constant('mysql_dbname').".spectaculars (info,uip,utime) values ('{$res}','{$uip}','{$srvtime}')");//insert new information
		
		if ($result === TRUE) {
			echo '{"status":"pass"}';
			$sqlconn->close();
		}
		else {
			echo '{"status":"error","info":"'.$sqlconn->error.'"}';
			$sqlconn->close();
		}
	}
}
function DoDelMsg() {
	header('Content-Type: text/html; charset=utf-8');
	if (!_get('code')=='') {
		session_start();
		if(isset($_SESSION['username'])) {//authentication pass action
			$sqlconn = new MySQLi(constant('mysql_server_name'),constant('mysql_username'),constant('mysql_password'),constant('mysql_dbname'));
			if (!$sqlconn) {// if not connect mysql database to show error information
				die('{"status":"error","info":"'.$sqlconn->connect_error.'"}');
			}
			//$sqlconn->query("set names 'utf-8'");//set encode of MySQL query
			$result=$sqlconn->query("truncate ".constant('mysql_dbname').".spectaculars");//delete all information
			if ($result === TRUE) {
				echo "操作成功结束！留言墙已被成功清空。";
				$sqlconn->close();
			}
			else {
				echo "操作失败，后端数据库反馈异常值！错误详情：".$sqlconn->error;
				$sqlconn->close();
			}
		}
		else {
			header("Location: LoginServlet.php?act=DelMsgAuth");//if not hava session permission,redirect to authentication action.
		}
	}
	else {
		header("Location: LoginServlet.php?act=DelMsgAuth");//if not hava action permission,redirect to authentication action.
	}
}
function DoGetMsg() {
	if (_get('format')=='json') {//if action set format to json output then do something
		header('Content-type: application/json; charset=utf-8');//json output standard configure
		$sqlconn = new MySQLi(constant('mysql_server_name'),constant('mysql_username'),constant('mysql_password'),constant('mysql_dbname'));
		if (!$sqlconn) {// if not connect mysql database to show error information
			die('{"status":"error","info":"'.$sqlconn->connect_error.'"}');
		}
		//$sqlconn->query("set names 'utf-8'");//set encode of MySQL query
		//$result=$sqlconn->query("select * from ".constant('mysql_dbname').".spectaculars");//get all information from database
		$result=$sqlconn->query("select distinct info from ".constant('mysql_dbname').".spectaculars");//get message information from database
		$css=$result->fetch_all();
		echo json_encode($css,JSON_UNESCAPED_UNICODE);
		$sqlconn->close();
	}
	else {
		header('Content-type: application/x-javascript; charset=utf-8');//javascript output standard configure
		$sqlconn = new MySQLi(constant('mysql_server_name'),constant('mysql_username'),constant('mysql_password'),constant('mysql_dbname'));
		if (!$sqlconn) {// if not connect mysql database to show error information
			header('Content-type: application/json; charset=utf-8');//json output standard configure
			die('{"status":"error","info":"'.$sqlconn->connect_error.'"}');
		}
		//$sqlconn->query("set names 'utf-8'");//set encode of MySQL query
		$result=$sqlconn->query("select distinct info from ".constant('mysql_dbname').".spectaculars");//get message information from database
		$css=$result->fetch_all();
		//echo $css;
		echo "var tests = ".$output=json_encode($css,JSON_UNESCAPED_UNICODE);
		$sqlconn->close();
	}
}
function DoAuth() {
	if (_get('status')=='pass' and !_get('tkey')=='') {
		session_start();
		$_SESSION['username'] = 'root';
		header("Location: servlet.php?act=delmsg&code=".date("ymdhis",time()));
	}
	else {
		header("Location: LoginServlet.php");
	}
}
function MyErrorHandler() {
	header('Content-Type: text/html; charset=utf-8');
	echo "<script>alert('非法调用，无法继续！后端版本：2.2_release，小丁工作室版权所有。');history.go(-1);</script>";
}
function _get($gstr){
    $val = !empty($_GET[$gstr]) ? $_GET[$gstr] : null;
    return $val;
}
?>