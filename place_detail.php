<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>どこUNI？</title>
<link rel="stylesheet" href="css/import.css" type="text/css">
<link rel="stylesheet" href="css/jquery.jqplot.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<meta charset="utf-8">

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>

<script src="js/table_tr_link.js"></script>
<script src="js/Cookie.js"></script>
<script src="js/jquery.jqplot.min.js"></script>
<script src="js/Functions.js?ver=2018122602"></script>
<script src="js/modal.js"></script>
<script src="https://maps.google.com/maps/api/js?key=AIzaSyCzzALgxyJwwdoI5VQWRUcpeqpEdHGfIdA"></script>
<script src="https://cdn.datatables.net/t/bs-3.3.6/jqc-1.12.0,dt-1.10.11/datatables.min.js"></script>

<script>
	var loaded = false;
	var isFavorite = false;

	// =========================
	//  ロード完了時イベント
	// =========================
	$(document).ready(function()
	{
		SuspendRayout();

		setTimeout(function()
		{
			if(!GetGroupData())
			{
				setTimeout(arguments.callee, 100);
			}
			else
			{
				setTimeout(function()
				{
					if(!LoadPlaceDetail())
					{
						setTimeout(arguments.callee, 100);
					}
					else
					{
						setTimeout(function()
						{
							if(!LoadPlacePlayers())
							{
								setTimeout(arguments.callee, 100);
							}
							else
							{
								setTimeout(function()
								{
									if(!LoadPlaceData())
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
					}
				}, 100);
			}
			
		}, 100);

		return false;
	});

	// =========================
	//  場所詳細をロード
	// =========================
	function LoadPlaceDetail()
	{
		var url = location.href;
		var placeID = GetParam('placeID', url);

		// var placeID = getCookie("PLACE_ID");
		var loginUserID = getCookie("USER_ID");

		if(loginUserID == "undefined" || loginUserID == "null")
		{
			document.getElementById("moveMypageButton").style.display="none";
		}

		if(placeID == "undefined")
		{
			alert("地域を選択してください");
			window.location.href = "where.php";
			return false;
		}

		var args = {
			placeID: placeID,
			userID: loginUserID
		}

		$.ajax({
			type: "POST",
			url: "php/GetPlaceDetailDataAPI.php",
			data: args,
			success: function(data)
			{
				var rowCnt = 0;
				var colorCode = '#999999';
				if(data.IS_FAVORITE)
				{
					isFavorite = true;

					// お気に入り済みの場合は黄色
					colorCode = 'FFFF00';
				}

				$("#placeHeader").append('<div id="placeName">' + data.PLACE_NAME + '</div>');
				$("#placeName").append('<span id="toggleButton" onClick="ChangeToggle(); return false;"><img src="img/buttons/minus.png" style="background-color:#AAAAAA; border-radius:2px;"></span>');
				$("#placeName").append('<img id="editButton" src="img/buttons/write32.png" onClick="MoveToPlaceEdit(' + placeID + '); return false;" style="background-color:#009944; margin-left:5px; border-radius:5px;">');
				$("#placeName").append('<img id="favoriteButton" src="img/buttons/star32.png?ver=1" onClick="AddFavoritePlace(' + placeID + '); return false;" style="background-color:' + colorCode + '; margin-left:5px; border-radius:15px;">');
				
				$("#output").append('<table id="placeDetail_data">');
				$("#placeDetail_data").append('<tr id="placeDetail_header">');
				$("#placeDetail_header").append('<th class="placeDetailHeader1">曜日</th>');
				$("#placeDetail_header").append('<th class="placeDetailHeader2">人気</th>');
				$("#placeDetail_data").append('</tr>');
				$("#placeDetail_data").append('<tbody id="placeDetail_body">');
				
				for(var week = 1; week < 8; week++)
				{
					var weekDay = "";
					
					if(week == 1){ weekDay = "日"; }
					else if(week == 2){ weekDay = "月"; }
					else if(week == 3){ weekDay = "火"; }
					else if(week == 4){ weekDay = "水"; }
					else if(week == 5){ weekDay = "木"; }
					else if(week == 6){ weekDay = "金"; }
					else if(week == 7){ weekDay = "土"; }
					
					// 行を変更する
					$("#placeDetail_body").append('<tr id="row' + rowCnt + '">');
					$("#row" + rowCnt).append('<td>' + weekDay + '</td>');
					
					var hasFoundData = false;
					data.DATA.forEach(function(value)
					{
						if(value.WEEKDAY == week){
							$("#row" + rowCnt).append('<td><div style="background-color:green; height: 20px; width:' + (value.PLAY_COUNT / data.MAX_COUNT) * 100 + '%;"></div></td>');
							hasFoundData = true;
						}
					});
					
					if(!hasFoundData)
					{
						$("#row" + rowCnt).append('<td><div style="background-color:green; height: 20px; width:0%;"></div></td>');
					}
					$("#placeDetail_body").append('</tr>');
					rowCnt += 1;
				}
				
				$("#placeDetail_body").append('</tbody>');
				$("#output").append("</table>");
				
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});
		
		return true;
	}


	/* ---------------------------
	//  場所データをロードします。
	// --------------------------- */
	function LoadPlaceData()
	{
		var url = location.href;
		var placeID = GetParam('placeID', url);
		var loginUserID = getCookie("USER_ID");
		
		if(placeID == "undefined")
		{
			alert("場所を選択してください");
			window.location.href = "where.php";
			return false;
		}

		if(loginUserID == "undefined" || loginUserID == null || loginUserID == "null")
		{
			// ログインしていない場合は
			// トグルON/OFF, 編集ボタン, お気に入りボタンを非表示にする。
			if(document.getElementById("toggleButton"))
			{
				document.getElementById("toggleButton").style.display="none";
			}

			if(document.getElementById("editButton"))
			{
				document.getElementById("editButton").style.display="none";
			}

			if(document.getElementById("favoriteButton"))
			{
				document.getElementById("favoriteButton").style.display="none";
			}
		}
		
		var placeData = GetCurrentPlaceData(placeID);

		if(placeData != null)
		{
			var dateTime= new Date();
			var hour = dateTime.getHours();
			var minute = dateTime.getMinutes();
			var second = dateTime.getSeconds();
			var timeStamp = hour + minute + second;

			var valueIsNothing = true;

			if(placeData.IMAGE_PATH != null && placeData.IMAGE_PATH != '')
			{
				valueIsNothing = false;
				$('#placeImage').append('<img src="' + placeData.IMAGE_PATH + '?' + timeStamp + '" style="width:90%">');
			}

			if(placeData.ADDRESS != null && placeData.ADDRESS != '')
			{
				valueIsNothing = false;
				$('#address').append('<p>住所</p><div style="padding:0 10px 0 10px;">' + placeData.ADDRESS + '</div>');

				setTimeout(
					function()
					{
						if(!DrawMap(placeData.ADDRESS, 'map'))
						{
							setTimeout(arguments.callee, 100);
						}
						else
						{
							console.log('map loaded.');
							document.getElementById("map").style.display="block";
						}
					}
				, 100);
			}
			else
			{
				document.getElementById("map").style.display="none";
			}

			if(placeData.COMMENT != null && placeData.COMMENT != '')
			{
				valueIsNothing = false;
				var comment = placeData.COMMENT.replace(/\r?\n/g, '<br>');
				$('#comment').append('<p>紹介文</p><div style="padding:0 10px 10px 10px;">' + comment + '</div>');
			}

			if(valueIsNothing)
			{
				if(document.getElementById("toggleButton"))
				{
					document.getElementById("toggleButton").style.display="none";
					document.getElementById("placeOutput").style.display="none";
				}
			}
		}
		else
		{
			document.getElementById("toggleButton").style.display="none";
		}

		return true;
	}

	// =========================
	//  グループデータをロード
	// =========================
	function GetGroupData()
	{
		var url = location.href;
		var placeID = GetParam('placeID', url);
		var loginUserID = getCookie("USER_ID");

		if(loginUserID == "undefined" || loginUserID == "null")
		{
			document.getElementById("moveMypageButton").style.display="none";
		}

		var groups = null;
		var groups = GetGroup(loginUserID);

		if(groups != null && groups.GROUP_DATA.length > 0)
		{
			var isFirst = true;
			var rowCnt = 0;
			
			$("#group_info").append('<form id="groupForm" name="groupForm"></form>');
			$("#groupForm").append('<table id="group_modal_data" style="color:black;"></table>');
			$("#group_modal_data").append('<thead id="group_header"></thead>');
			$("#group_header").append('<tr id="group_tr"></tr>');
			$("#group_tr").append('<th class="header1">グループ名</th>');
			$("#group_tr").append('<th class="header2"><input type="checkbox" name="groupID" value="" onChange="AllGroupCheck(); return false;"></th>');

			$("#group_modal_data").append('<tbody id="group_body"></tbody>');

			groups.GROUP_DATA.forEach(function(value)
			{
				if(value.STATUS == 1)
				{
					// 承認済みのグループのみ追加する
					$("#group_body").append('<tr id="groupRow' + rowCnt + '"></tr>');
					$('#groupRow' + rowCnt).append('<td>' + value.GROUP_NAME + '</td>');
					$('#groupRow' + rowCnt).append('<td><input type="checkbox" name="groupID" value="' +value.GROUP_ID+'"></td>');
					rowCnt = rowCnt + 1;
				}
			});
		}
		else
		{
			$("#group_info").css({ visibility: "hidden" });
		}

		return true;
	}

	function AllGroupCheck()
	{
		var checkValue;
		for (var i = 0; i < document.groupForm.groupID.length; i++)
		{
			if(document.groupForm.groupID[i].value == '')
			{
				checkValue = document.groupForm.groupID[i].checked;
			}
			else
			{
				// i番目のチェックボックスがチェックされているかを判定
				document.groupForm.groupID[i].checked = checkValue;
			}
		}

		return;
	}

	// ====================
	// 再描画
	// ====================
	function ReWrite(placeID)
	{
		document.getElementById("output2").innerHTML = "";
		LoadPlacePlayers();
	}

	// =========================
	//  地域プレーヤーをロード
	// =========================
	function LoadPlacePlayers()
	{
		var url = location.href;
		var placeID = GetParam('placeID', url);
		var loginUserID = getCookie("USER_ID");
		var twitterOnly = document.getElementById("twitterOnly").checked;

		if(loginUserID == "undefined" || loginUserID == "null")
		{
			document.getElementById("moveMypageButton").style.display="none";
		}

		if(placeID == "undefined")
		{
			alert("地域を選択してください");
			window.location.href = "where.php";
			return false;
		}

		var popUserData = GetPopularityDetail(placeID, 0);

		var args = {
			placeID: placeID
		}

		$.ajax({
			type: "POST",
			url: "php/GetPlacePlayersAPI.php",
			data: args,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					var rowCnt = 0;
					
					$("#output2").append('<div id="placeInfo">１カ月以内に見たプレーヤー</div>');
					$("#output2").append('<table id="placePlayers_data">');
					$("#placePlayers_data").append('<thead id="placePlayers_header">');
					$("#placePlayers_header").append('<tr id="placePlayers_tr">');
					$("#placePlayers_tr").append('<th class="placePlayersHeader1">ｶｳﾝﾄ</th>');
					$("#placePlayers_tr").append('<th class="placePlayersHeader2">ｱｲｺﾝ</th>');
					$("#placePlayers_tr").append('<th class="placePlayersHeader3">PN</th>');
					$("#placePlayers_tr").append('<th class="placePlayersHeader4">ﾌﾟﾛﾌｨｰﾙ</th>');
					$("#placePlayers_header").append('</tr>');
					$("#placePlayers_data").append('</thead>');
					$("#placePlayers_data").append('<tbody id="placePlayers_body">');

					if(data.DATA != null)
					{
						data.DATA.forEach(function(value)
						{
							var findData;
							if(twitterOnly && value.TWITTER == null)
							{
								return;
							}

							if(popUserData.length > 0)
							{
								findData = popUserData.find(function(element)
								{
									return "'" + element.USER_ID + "'" == value.USER_ID;
								});
							}

							var appendStr = '';
							if(findData != undefined)
							{
								appendStr = '<br/><br/>DEVIDE<br/>[' + findData.JOIN_TIME_FROM + '-' + findData.JOIN_TIME_TO + ']';
							}

							$("#placePlayers_body").append('<tr id="prow' + rowCnt + '">');
							$("#prow" + rowCnt).append('<td>' + value.PLAY_COUNT + '</td>');
							if(value.IMAGE_PATH != null)
							{
								$("#prow" + rowCnt).append('<td><img src="' + value.IMAGE_PATH + '" style="width:60px; height:60px;"></td>');
							}
							else
							{
								$("#prow" + rowCnt).append('<td><img src="img/users/icons/no_image.jpg" style="width:60px; height:60px;"></td>');
							}

							$("#prow" + rowCnt).append('<td onClick="MoveToIntroduce(' + value.USER_ID + ', ' + value.ARG_NAME +')" style="cursor:hand; text-decoration: underline;">' + value.NAME + '<br/>紹介：' + value.INTRODUCED_COUNT + '件</td>');
							$("#prow" + rowCnt).append('<td>' + value.COMMENT + appendStr + '</td>');
							$("#placePlayers_body").append('</tr>');
							
							rowCnt += 1;
						});
					}
					else
					{
						$("#placePlayers_body").append('<tr id="prow' + rowCnt + '">');
						$("#prow" + rowCnt).append('<td colspan=4>履歴が見つかりませんでした。</td>');

						return;

					}
					
					$("#placePlayers_body").append('</tbody>');
					$("#output2").append("</table>");

					// デフォルトの設定を変更
					$.extend( $.fn.dataTable.defaults, { 
						language: {
							url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json"
						} 
					}); 
					
					$("#placePlayers_data").dataTable({
						// 件数切替機能 無効
						lengthChange: true,
						
						// 検索機能 無効
						searching: false,
						
						// ソート機能 無効
						ordering: true,
						
						// 情報表示 無効
						info: true,
						
						// ページング機能 無効
						paging: true,
						
						order: [ [ 1, "desc"] ],
						
						// 件数切替の値を 5,10,15 刻みにする
						lengthMenu: [ 5, 10, 15 ],
						
						// 件数のデフォルトの値を 5 にする
						displayLength: 5
					});
				}
				else
				{
					alert('エラー:' + data.MESSAGE);
				}
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});
		
		return true;
	}

	/* ************************
	//  グループ宛に招待を送る
	// 
	// ************************ */
	function Invite()
	{
		var hasSelected = false;
		var groups = [];
		var userID = getCookie('USER_ID');
		var userName = getCookie('USER_NAME');
		var url = location.href;
		var placeID = GetParam('placeID', url);
		
		for (var i = 0; i < document.groupForm.groupID.length; i++)
		{
			// i番目のチェックボックスがチェックされているかを判定
			if (document.groupForm.groupID[i].checked)
			{   
				hasSelected = true;
				if(document.groupForm.groupID[i].value != '')
				{
					groups.push(document.groupForm.groupID[i].value);
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
		modalFadeOut('#modal-group');

		// 送信する
		SendInvite(userID, userName, placeID, groups);
	}


	/* ************************
	//  お気に入り登録する
	// ************************ */
	function AddFavoritePlace(_placeID)
	{
		if(!isFavorite)
		{
			// お気に入り登録する
			var userID = getCookie('USER_ID');

			// 送信する
			var result = AddFavorite(userID, 'PLACE', _placeID);

			if(result)
			{
				isFavorite = true;
				document.getElementById("favoriteButton").style.backgroundColor = "#FFFF00";				
				alert('お気に入り登録しました。');
			}
		}
		else
		{
			var result = RemoveFavoritePlace(_placeID);

			if(result)
			{
				isFavorite = false;
				document.getElementById("favoriteButton").style.backgroundColor = "#999999";
				alert('お気に入りを解除しました。');
			}
		}
	}

	/* ************************
	//  お気に入り登録を解除する
	// ************************ */
	function RemoveFavoritePlace(_placeID)
	{
		// お気に入り解除する
		var userID = getCookie('USER_ID');

		// 送信する
		var result = RemoveFavorite(userID, 'PLACE', _placeID);

		return result;
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

	// ====================
	// 詳細画面へ移動
	// ====================
	function MoveToPlaceEdit(_placeID)
	{
		setCookie('PLACE_ID', _placeID);
		window.location.href = "place_edit.php";
	}

	// ====================
	// 詳細画面へ移動
	// ====================
	function MoveToIntroduce(introUserID, introUserName)
	{
		if(introUserID == '' || introUserID == null || introUserID == 'undefined')
		{
			alert("どこUNI未登録ユーザーです。");
			return;
		}
		
		
		setCookie("INTRO_ID", introUserID, 1);
		setCookie("INTRO_USER_NAME", introUserName, 1);
		window.location.href = "introduce.php";
	}
	
	// ==========================
	//  前画面に戻る
	// ==========================
	function ReturnPrevPage()
	{
		window.location.href = "current_search.php";
	}
	
	// ==========================
	//  トグル
	// ==========================
	function ChangeToggle()
	{
		if(document.getElementById("placeOutput").style.display == "none")
		{
			document.getElementById("placeOutput").style.display="block";
			document.getElementById("toggleButton").innerHTML = '<img src="img/buttons/minus.png" style="background-color:#AAAAAA;">';
		}
		else
		{
			document.getElementById("placeOutput").style.display="none";
			document.getElementById("toggleButton").innerHTML = '<img src="img/buttons/plus.png" style="background-color:#AAAAAA;">';
		}
	}

</script>

<script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
</head>
<body>

<div id="functionButtons"></div>
<div id="menuScript"></div>

<!-- ここから下は書き換わる可能性がある -->
<div id="updatableContents">

<div id="main_header">
	<h1>どこＵＮＩ？</h1>
</div>

<button class="noBorderButton" onClick="modalOpen('modal-group'); return false;">
	<a class="btn" href="javascript:void(0)">Invite</a>
</button>

<div id="placeHeader" style="padding:3px 0 3px 0;"></div>
<div id="placeOutput" style="border:solid 1px #AAAAAA; border-radius:3px; padding:5px;">
	<div id="placeImage"></div>
	<div id="address" class="small-text"></div>
	<div id="comment" class="small-text"></div>
	<div id="map" style="width:95%; height:200px;"></div>
</div>

<div id="output"></div>
<div><input id="twitterOnly" type="checkbox" OnChange="ReWrite(); return false;">Twitter連携ユーザのみ表示</div>
<div id="output2" style="font-size:0.7em;"></div>

<button class="noBorderButton" onClick="ReturnPrevPage(); return false;">
	<a class="btn" href="javascript:void(0)">戻る</a>
</button>

<div id="modal-group" class="modal">
	グループ情報
	<div id="group_info"></div>

	<button class="noBorderButton" id="SendInvite" name="SendInvite" onClick="Invite(); return false;">
		<a class="btn" href="javascript:void(0)">招待</a>
	</button>
</div>

<br/>

<!-- admax -->
<script src="//adm.shinobi.jp/s/8c8b2e52b1faa0be62ef85056906ca82"></script>
<!-- admax -->
</div>
<br/>
</body>