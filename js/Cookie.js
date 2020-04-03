// クッキー保存　setCookie(クッキー名, クッキーの値, クッキーの有効日数); //
function setCookie(c_name, value, expiredays){

	// pathの指定
	var path = location.pathname;
	
	// pathをフォルダ毎に指定する場合のIE対策
	var paths = new Array();
	
	// Cookieのパス
	paths = path.split("/");
	
	if(paths[paths.length-1] != "")
	{
		paths[paths.length-1] = "";
		path = paths.join("/");
	}
	
	// 有効期限の日付
	var extime = new Date().getTime();
	var cltime = new Date(extime + (60*60*24*1000*expiredays));
	var exdate = cltime.toUTCString();
	
	// クッキーに保存する文字列を生成
	var s="";
	s += c_name +"="+ escape(value);// 値はエンコードしておく
	s += "; path="+ path;
	
	if(expiredays)
	{
		s += "; expires=" +exdate+"; ";
	}
	else
	{
		s += "; ";
	}
	
	// クッキーに保存
	document.cookie=s;
}

// クッキーの値を取得 getCookie(クッキー名); //
function getCookie(c_name){
	var st="";
	var ed="";
	
	if(document.cookie.length>0){
		// クッキーの値を取り出す
		st=document.cookie.indexOf(c_name + "=");
		if(st!=-1)
		{
			st=st+c_name.length+1;
			ed=document.cookie.indexOf(";",st);
			
			if(ed==-1)
			{
				ed=document.cookie.length;
			}
			// 値をデコードして返す
			return unescape(document.cookie.substring(st,ed));
		}
	}
	
	return null;
}

// クッキーの値をアラートで表示
function checkCookie(){
	if(getCookie('testName')){
		var setName = getCookie('testName');
		alert(setName);
	}
}