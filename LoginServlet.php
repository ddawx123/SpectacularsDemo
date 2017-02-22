<?php
if (file_exists('./appconf.php')) {//check app configure file.
	include("./appconf.php");//include app configure file.
}
else {//if could not found app configure file,show json error output.
	header('Content-type: application/json; charset=utf-8');//json output standard configure
	die('{"status":"error"}');//json error ourput
}
if (_get('act')=='verify'){
	if ($_POST['username']==constant('adminer') and $_POST['password']==constant('adminpasswd')){
		header("Location: servlet.php?act=authentication&status=pass&tkey=".date("ymdhis",time()));
	}
	else {
		header('Content-type: text/html; charset=utf-8');//json output standard configure
		$errinfo = "抱歉，用户名或密码无效。无法通过系统身份验证！";
	}
}
else {
	$errinfo = "您需要通过身份认证才可以继续之前的操作。。。";
}
function _get($gstr){
    $val = !empty($_GET[$gstr]) ? $_GET[$gstr] : null;
    return $val;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>身份认证系统</title>
</head>

<body>
<div id="loginbox" align="center">
	<p align="center"><strong>用户身份认证系统-测试版</strong></p>
	<?php echo $errinfo; ?><hr />
	<form method="post" action="LoginServlet.php?act=verify">
		<label for="username">账号：</label>
		<input id="username" name="username" type="text" placeholder="键入您的账号"><br />
		<label for="password">口令：</label>
		<input id="password" name="password" type="password" placeholder="键入您的口令"><br />
		<input id="btnLogin" name="btnLogin" type="submit" value="登录">
		<input id="btnReset" name="btnReset" type="reset" value="重置">
	</form>
</div>
</body>
</html>