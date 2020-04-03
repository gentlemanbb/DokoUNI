<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css?ver=20181201011" type="text/css">
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>

<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/jquery.jqplot.min.js"></script>
<script src="js/Functions.js?ver=201812010010"></script>
<script src="js/modal.js"></script>

<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded = false;
	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();

		setTimeout(function(){
			if(!LoadGroupData())
			{
				setTimeout(arguments.callee, 100);
			}
			else{
				setTimeout(function()
				{
					if(!LoadFriendData())
					{
						setTimeout(arguments.callee, 100);
					}
					else 
					{
						$('#functionButtons').load('functions.html');
						$('#menuScript').load('functionsEnabled.html');
						ResumeRayout();
						loaded = true;
					}
				}, 100);
			}			
		}, 100);

		return false;
	});

	var groupMembers = null;

	// -----------------------
	//  未登録フレンド情報の取得
	// -----------------------
	function LoadFriendData()
	{
		var userID = getCookie("USER_ID");

		var args = {
			userID : userID,
		};

		var friendData = GetAllFriendData(userID);
		var rowCnt = 0;

		if(friendData.FRIEND_DATA.length > 0)
		{
			$("#friend_info").append('<form id="joinGroupForm" name="joinGroupForm"></form>');
			$("#joinGroupForm").append('<table class="friendData" id="friend_data"></table>');
			$("#friend_data").append('<tr id="friendHeader"></tr>');
			$("#friendHeader").append('<th class="friendHeader1">ユーザー名</th>');
			$("#friendHeader").append('<th class="friendHeader3"><input type="checkbox" name="friends" value="" onChange="AllGroupCheck(); return false;"></th>');
			$("#friend_data").append('<tbody id="friendBody"></tbody>');

			friendData.FRIEND_DATA.forEach(function(value)
			{
				var hasFound = false;

				if(value.STATUS == 1)
				{
					groupMembers.forEach(function(value2)
					{
						if(value.USER_ID == value2.USER_ID && value2.STATUS == 1)
						{
							hasFound = true;
						}
					});

					if(!hasFound)
					{
						// 行を変更する
						$("#friendBody").append('<tr id="friendRow' + rowCnt + '">');
						$('#friendRow' + rowCnt).append('<td>' + value.USER_NAME + '</td>');
						$('#friendRow' + rowCnt).append('<td><input type="checkbox" name="friends" value="'+value.USER_ID+'"></td>');

						rowCnt = rowCnt + 1;
					}
				}
			});
		}
		else
		{
			$("#friend_info").append('<span class="attention">フレンドは未登録です。</span>');
		}
		return true;
	}

	/*-------------------------
	/  全体のチェック状態変更
	/ -------------------------*/
	function AllGroupCheck()
	{
		var checkValue;
		for (var i = 0; i < document.joinGroupForm.friends.length; i++)
		{
			if(document.joinGroupForm.friends[i].value == '')
			{
				checkValue = document.joinGroupForm.friends[i].checked;
			}
			else
			{
				// i番目のチェックボックスがチェックされているかを判定
				document.joinGroupForm.friends[i].checked = checkValue;
			}
		}

		return;
	}

	/*-------------------------
	/  グループへの招待送信
	/------------------------*/
	function InviteGroup()
	{
		var hasSelected = false;
		var friends = [];
		var userID = getCookie('USER_ID');
		var userName = getCookie('USER_NAME');
		var groupID = getCookie('GROUP_ID');
		
		for (var i = 0; i < document.joinGroupForm.friends.length; i++)
		{
			// i番目のチェックボックスがチェックされているかを判定
			if (document.joinGroupForm.friends[i].checked)
			{   
				hasSelected = true;
				if(document.joinGroupForm.friends[i].value != '')
				{
					friends.push(document.joinGroupForm.friends[i].value);
				}
			}
		}

		// 何も選択されていない場合の処理   
		if (!hasSelected) 
		{
			alert("項目が選択されていません。");
			return;
		}
		
		// モーダルを閉じる
		modalFadeOut('#modal-friends');

		// 送信する
		SendGroupInvite(userID, userName, groupID, friends);
	}

	// -----------------------
	//  グループ情報の取得
	// -----------------------
	function LoadGroupData()
	{
		var groupID = getCookie('GROUP_ID');
		var groupData = GetGroupDetailData(groupID);
		var rowCnt = 0;
		if(groupData != null && groupData.GROUP_MEMBER.length > 0)
		{
			groupMembers = groupData.GROUP_MEMBER;

			$("#output").append('<table class="groupData" id="group_data" style="text-align:left;"></table>');
			$("#group_data").append('<tr id="group_header"></tr>');
			$("#group_header").append('<th class="groupHeader1">グループメンバー</th>');
			$("#group_data").append('<tbody id="group_body"></tbody>');

			groupData.GROUP_MEMBER.forEach(function(value)
			{
				if(value.STATUS == 1)
				{
					// 行を変更する
					$("#group_body").append('<tr id="groupRow' + rowCnt + '">');
					$('#groupRow' + rowCnt).append('<td>' + value.USER_NAME + '</td>');

					rowCnt = rowCnt + 1;
				}
			});
		}
		else
		{
			$("#output").append('<span class="attention">グループメンバーは未登録です。</span>');
		}
		return true;
	}

	// -----------------------
	//  グループ削除
	// -----------------------
	function DeletingGroup()
	{
		var groupID = getCookie('GROUP_ID');
		var userID = getCookie('USER_ID');
		DeleteGroup(groupID, userID);
		window.location.href = "user_page.php";
		return true;
	}

	// ==========================
	//  簡易的に描画を停止します
	// ==========================
	function SuspendRayout()
	{
		$("#updatableContents").css({ visibility: "hidden" });
	}
	
	// ==========================
	//  描画を再開します
	// ==========================
	function ResumeRayout()
	{
		$("#updatableContents").css({ visibility: "visible" });
	}
	
	// ==========================
	//  前画面に戻る
	// ==========================
	function ReturnPrevPage()
	{
		window.location.href = "user_page.php";
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

<button class="noBorderButton" onClick="modalOpen('modal-friends'); return false;">
	<a class="btn" href="javascript:void(0)">Invite</a>
</button>

<button class="noBorderButton" onClick="DeletingGroup(); return false;">
	<a class="btn" href="javascript:void(0)">削除</a>
</button>

<div id="output"></div>
<div id="output2" style="font-size:0.7em;"></div>

<button class="noBorderButton" onClick="ReturnPrevPage(); return false;">
	<a class="btn" href="javascript:void(0)">戻る</a>
</button>

<div id="modal-friends" class="modal">
	ユーザー情報
	<div id="friend_info"></div>

	<button class="noBorderButton" id="InviteGroup" name="InviteGroup" onClick="InviteGroup(); return false;">
		<a class="btn" href="javascript:void(0)">グループに招待</a>
	</button>
</div>

<br/>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
</div>
<br/>
</body>