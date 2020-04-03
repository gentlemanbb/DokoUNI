
<head>
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/rayout.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>

<script src="js/Cookie.js"></script>
<script src="js/Functions.js"></script>

<script>
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		var url = location.href;
		var userID = GetParam('receiveUserID', url);
		var loginUserID = getCookie('USER_ID');

		if(userID != loginUserID)
		{
			alert('受信ユーザー以外は承認できません。');
			window.location.href = "where.php";
		}

		LoadUserData(userID);

		var groupID = GetParam('groupID', url);
		LoadGroupData(groupID);
		LoadGroupUserData(groupID, userID);
		LoadReceiveType();
		
		return false;
	});

	function LoadPlaceData(_placeID)
	{
		var place = null;
		var place = GetPlace(_placeID);

		if(place != null)
		{
			$("#placeName").append(place.PLACE_NAME);
		}
		return true;
	}

	function LoadGroupData(_groupID)
	{
		var group = null;
		var group = GetGroupDetailData(_groupID);

		if(group != null)
		{
			$("#groupName").append(group.GROUP_NAME);
		}
		return true;
	}

	function LoadGroupUserData(_groupID, _userID)
	{
		var groupUser = null;
		var groupUser = GetGroupUserDetailData(_groupID, _userID);

		if(groupUser != null)
		{
			if(groupUser.STATUS != 0)
			{
				var message = '';
				if(groupUser.STATUS == 1)
				{
					message = '承認済み';
				}
				else if(groupUser.STATUS == 2)
				{
					message = '拒否済み';
				}

				alert('このリクエストはすでに' + message + 'です。');
				window.open('about:blank','_self').close();
			}
		}
		return true;
	}
	
	function LoadUserData(_userID)
	{
		var userData = null;
		var userData = GetUserDataFunc(_userID);

		if(userData != null)
		{
			$("#userName").append(userData.USER_DATA.USER_NAME);
		}
		return true;
	}

	// コンボボックスの値を取得
	function LoadReceiveType()
	{
		var receiveData = null;
		var receiveData = GetType("AGREE_TYPE");

		if(receiveData != null)
		{
			receiveData.forEach(function(value)
			{
				// 行を変更する
				$("#receiveType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
			});
		}

		return true;
	}

	function Receive()
	{
		var url = location.href;
		var receiveUserID  = GetParam('receiveUserID', url);
		var sendUserID  = GetParam('sendUserID', url);
		var receiveType = $('#receiveType').val();
		var groupID = GetParam('groupID', url);
		
		ReceiveGroupInvite(receiveUserID, sendUserID, receiveType, groupID);	
		window.open('about:blank','_self').close();
	}

</script>
</head>
<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

	<div id="main_header">
		<h1>どこＵＮＩ？</h1>
	</div>

	<div class="sendWrapper" id="sendDataForm">
		<span class="box-title">どこかに行く</span>
		<form id="sendData" name="sendData">
			<p>
			<label for="label_place" accesskey="n">場所：</label><span id="placeName"></span>
			<label for="label_group" accesskey="n">グループ：</label><span id="groupName"></span>
				<label for="label_user" accesskey="n">ユーザー：</label><span id="userName"></span>
			</p>
			<p>
				<label for="label_receive" accesskey="n">返事：</label><br/>
				<select class="formComboBox" name="receiveType" id="receiveType">
				</select>
			</p>
		</form>
	</div>
	<button class="noBorderButton" id="ReceiveInvite" name="ReceiveInvite" onClick="Receive()">
		<span class="btn" href="javascript:void(0)">返信</span>
	</button>

</div>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
<br/>
</body>


