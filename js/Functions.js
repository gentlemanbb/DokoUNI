
	function GetTodayAddDays(addDays){
    	var now = new Date();
		now.setDate(now.getDate() + addDays);
		var year = now.getFullYear();
		var month = now.getMonth() + 1;
		var day = now.getDate();
		
		return String(year) + "/" + String(month) + "/" + String(day);
	}

	// ====================
	//  リンク先へ移動
	// ====================
	function MoveToLink(url)
	{
		window.location.href = url;
	}

	/* ------------------------------
	/ ＵＲＬからパラメータを取得します。
	/  引数１：引数名
	/  引数２：ＵＲＬ
	/ ------------------------------- */
	function GetParam(name, url)
	{
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

	//
	// 日付の差分日数を返却します。
	//
	function getDiff(date1Str, date2Str) {
		var date1 = new Date(date1Str);
		var date2 = new Date(date2Str);
	
		// getTimeメソッドで経過ミリ秒を取得し、２つの日付の差を求める
		var msDiff = date2.getTime() - date1.getTime();
	
		// 求めた差分（ミリ秒）を日付へ変換します（経過ミリ秒÷(1000ミリ秒×60秒×60分×24時間)。端数切り捨て）
		var daysDiff = Math.floor(msDiff / (1000 * 60 * 60 *24));
	
		// 差分へ1日分加算して返却します
		return daysDiff;
	}


	// ========================
	//  ユーザに紐づくグループのロード
	// ========================
	function GetGroup(_userID)
	{
		var args = {
			userID : _userID
		};

		var groups = null;
		$.ajax({
			type: "POST",
			url: "php/GetGroupAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				groups = data;
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return null;
			}
		});

		return groups;
	}

	// ========================
	//  グループ詳細のロード
	// ========================
	function GetGroupDetailData(_groupID)
	{
		var args = {
			groupID : _groupID
		};

		var group = null;
		$.ajax({
			type: "POST",
			url: "php/GetGroupDetailDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					group = data.GROUP_DATA;
				}
				else
				{
					return null;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return null;
			}
		});

		return group;
	}

	// ========================
	//  グループユーザデータを取得します。
	// ========================
	function GetGroupUserDetailData(_groupID, _userID)
	{
		var args = {
			groupID : _groupID,
			userID : _userID
		};

		var returnData = null;
		$.ajax({
			type: "POST",
			url: "php/GetGroupUserDetailDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				returnData = data.GROUP_USER_DATA;
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return null;
			}
		});

		return returnData;
	}
	// ========================
	//  ユーザーデータを取得します。
	// ========================
	function GetUserDataFunc(_userID)
	{
		var args = {
			userID : _userID,
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetUserDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}
	
	// ========================
	//  どこUNI宣言します。
	// ========================
	function SendPoplarity(_placeID, _placeName, _userID, _playerName
		, _joinType, _joinText, _purposeType, _purposeText
		, _RIP, _characterID, _from, _to
		, _comment, _addDays, _withTweet)
	{
		var args = {
			placeID : _placeID,
			placeName : _placeName,
			userID : _userID,
			playerName : _playerName,
			joinType : _joinType,
			joinText : _joinText,
			purposeType : _purposeType,
			purposeText : _purposeText,
			RIP : _RIP,
			characterID : _characterID,
			from : _from,
			to : _to,
			comment : _comment,
			addDays : _addDays,
			withTweet : _withTweet
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/SendPopularity.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = false;
			}
		});

		return returnData;
	}

	// ========================
	//  招待を送ります。
	// ========================
	function SendInvite(_sendUserID, _sendUserName, _placeID ,_groupArray)
	{
		var args = {
			sendUserID : _sendUserID,
			sendUserName : _sendUserName,
			placeID : _placeID,
			groups : _groupArray
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/SendInvite.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  招待を送ります。
	// ========================
	function ReceiveInvite(_sendUserName, _placeID, _receiveType, _comment, _groupArray)
	{
		var args = {
			sendUserName : _sendUserName,
			placeID : _placeID,
			receiveType: _receiveType,
			comment : _comment,
			groups : _groupArray
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/ReceiveInvite.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  グループ招待を送ります。
	// ========================
	function SendGroupInvite(_sendUserID, _sendUserName, _groupID, _friends)
	{
		var args = {
			sendUserID : _sendUserID,
			sendUserName : _sendUserName,
			groupID : _groupID,
			friends : _friends
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/SendGroupInvite.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  グループ招待に返信します。
	// ========================
	function ReceiveGroupInvite(_receiveUserID, _inviteUserID, _receiveType, _groupID)
	{
		var args = {
			receiveUserID : _receiveUserID,
			inviteUserID : _inviteUserID,
			receiveType: _receiveType,
			groupID : _groupID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/ReceiveGroupInvite.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  フレンド招待に返信します。
	// ========================
	function ReceiveFriendInvite(_receiveUserID, _inviteUserID, _receiveType)
	{
		var args = {
			receiveUserID : _receiveUserID,
			inviteUserID : _inviteUserID,
			receiveType: _receiveType
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/ReceiveFriendInvite.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  場所の取得
	// ========================
	function GetPlace(_placeID){

		var args = {
			 placeID : _placeID
		};
		
		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetPlaceDetailAPI.php",
			data: args,
			async: false,
			timeout: 1000,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					if(data.PLACE_DATA != null)
					{
						returnData = data.PLACE_DATA;
					}
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return null;
			}
		});
		
		return returnData;
	}

	// ========================
	//  区分を取得します。
	// ========================
	function GetType(_type)
	{
		var data = {
			 key : _type
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetType.php",
			data: data,
			async: false,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data.TYPE_DATA;
				}
				else
				{
					return null;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return null;
			}
		});

		return returnData;
	}

	// ========================
	//  ユーザ詳細データを取得します。
	// ========================
	function GetUserDetailData(_userID)
	{
		var args = {
			userID : _userID,
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetUserDataAPI.php",
			data: args,
			async: false,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  フレンドを取得します。
	// ========================
	function GetAllFriendData(_userID)
	{
		var args = {
			userID : _userID,
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetAllFriendDataAPI.php",
			data: args,
			async: false,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  フレンドを取得します。
	// ========================
	function GetFriendDetailData(_inviteUserID, _receiveUserID)
	{
		var args = {
			inviteUserID : _inviteUserID,
			receiveUserID : _receiveUserID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetFriendDetailDataAPI.php",
			data: args,
			async: false,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  紹介を取得
	// ========================
	function GetIntroduce(_introUserID)
	{
		var args = {
			introUserID: _introUserID
		}

		$.ajax({
			type: "POST",
			url: "php/GetIntroduceAPI.php",
			data: args,
			async: false,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data.DATA;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  フレンドを申請します。
	// ========================
	function SendAddFriend(_sendUserID, _receiveUserID)
	{
		var args = {
			sendUserID : _sendUserID,
			receiveUserID : _receiveUserID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/SendAddFriendAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  グループを新規作成します。
	// ========================
	function RegistGroup(_groupName, _userID)
	{
		var args = {
			groupName : _groupName,
			userID : _userID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/RegistGroupAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  グループを新規作成します。
	// ========================
	function DeleteGroup(_groupID, _userID)
	{
		var args = {
			groupID : _groupID,
			userID : _userID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/DeleteGroupAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}


	// ========================
	//  イベントを取得します。
	// ========================
	function GetEventData(addDaysFrom_, addDaysTo_, areaID_, userID_)
	{
		var args = {
			areaID : areaID_,
			addDaysFrom : addDaysFrom_,
			addDaysTo : addDaysTo_,
			userID : userID_
		};

		var returnData = null;

		$.ajax({
			type: "GET",
			dataType: "json",
			url: "php/GetEventAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data.DATA;
				}
				else
				{
					alert('イベントの取得に失敗しました。');
					return null;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("XMLHttpRequest : " + XMLHttpRequest.status);
				alert("textStatus     : " + textStatus);
				alert("errorThrown    : " + errorThrown.message);	
				return null;
  			}
		});

		return returnData;
	}

	// ========================
	//  当月のイベントを取得します。
	// ========================
	function GetMonthlyEvent(_areaID, _userID)
	{
		var date = new Date();
		var day = date.getDate();
		var maxDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
		var maxDay = maxDate.getDate();


		var args = {
			areaID : _areaID,
			addDaysTo : (maxDay - day),
			addDaysFrom : -(day - 1),
			userID : _userID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetEventAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				returnData = data;
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// ========================
	//  イベントテンプレートを取得します。
	// ========================
	function GetEventTemplateData(userID_)
	{
		var args = {
			userID : userID_
		};
		
		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetEventTemplate.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data.DATA;
				}
				else
				{
					alert('Fail: ' + data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}
	
	// ========================
	//  グループを新規作成します。
	// ========================
	function TweetCalendar(_areaID, _userID)
	{
		var date = new Date();
		var day = date.getDate();
		var maxDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
		var maxDay = maxDate.getDate();


		var args = {
			areaID : _areaID,
			addDaysTo : (maxDay - day),
			addDaysFrom : -(day - 1),
			userID : _userID
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetEventAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				returnData = data;
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = null;
			}
		});

		return returnData;
	}

	// =======================
	//  自分の投票データを取得
	// =======================
	function GetMyPopularity(_userID, _addDays)
	{
		var args = {
			userID: _userID,
			addDays: _addDays
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetMyPopularity.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT)
				{
					if(data.POP_DATA != null)
					{
						returnData = data.POP_DATA;
					}
				}
				else
				{
					console.log(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});
		
		return returnData;
	}


	// =======================
	//  投票データを取得
	// =======================
	function GetPopularityDetail(_placeID, _addDays)
	{
		var args = {
			placeID: _placeID,
			addDays: _addDays
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetPopularityDetail.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				returnData = data;
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});
		
		return returnData;
	}

	// =======================
	//  イベント画像の更新
	// =======================
	function UploadEventImage(_formData, _eventID)
	{
		//フォームのデータを変数formに格納
		var form = $('#sendData').get()[0];
		
		//FormData オブジェクトを作成
		var formData = new FormData(form);
		formData.append("eventID", _eventID);
		
		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/UploadFileAPI.php",
			data		: formData,
			cache	   	: false,
			processData : false,
			contentType : false,
			async		: false,
			timeout		: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.IMAGE_PATH;
				}
				else
				{
					// 何もしない
					alert("画像のアップロードに失敗しました。");
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// =======================
	//  イベント画像の更新
	// =======================
	function UploadPlaceImage(_formData, _placeID, _fileType)
	{
		_formData.append("placeID", _placeID);
		_formData.append("fileType", _fileType);
		
		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/UploadFileAPI2.php",
			data		: _formData,
			cache	   	: false,
			processData : false,
			contentType : false,
			async		: false,
			timeout		: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.IMAGE_PATH;
				}
				else
				{
					// 何もしない
					alert("画像のアップロードに失敗しました。");
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// =======================
	//  TwitterIDが他人に使用されているかチェックします。
	// =======================
	function CheckTwitterAccount(_userID, _twitterID)
	{
		var args = {
			userID : _userID,
			twitterID : _twitterID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/CheckTwitterUserAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}


	// =======================
	//  TwitterIDを更新します。
	// =======================
	function UpdateTwitterAccountFunc(_userID, _twitterID)
	{
		var args = {
			userID : _userID,
			twitterID : _twitterID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/UpdateTwitterAccountAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}


	// ==============================
	//  キャラクター詳細データを取得します。
	// ==============================
	function GetCharacterDetailData(_characterID)
	{
		var args = {
			characterID : _characterID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetCharacterDetailDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.CHARACTER_DATA;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ==============================
	//  コンボデータを取得します。
	// ==============================
	function GetComboDetailData(_comboID)
	{
		var args = {
			comboID : _comboID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetComboDetailDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ==============================
	//  コンボデータリストを取得します。
	// ==============================
	function GetComboDataList(_characterID)
	{
		var args = {
			characterID : _characterID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetCharacterComboDataListAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.COMBO_DATA;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ==============================
	//  タグデータを取得します。
	// ==============================
	function GetTagsData(_tagType)
	{
		var args = {
			tagType : _tagType
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetTagsDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.TAG_DATA;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// =======================
	//  場所詳細データを取得します。
	// =======================
	function GetCurrentPlaceData(_placeID)
	{
		var args = {
			placeID : _placeID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetPlaceAPI2.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.PLACE_DATA;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}
	
	/* *********************************
	//  地図を描画します。
	// --------------------------------
	//  
	// ********************************* */
	function DrawMap(address, drawID)
	{
		var result = false;
        var geocoder = new google.maps.Geocoder();
		
		//住所から座標を取得する
		geocoder.geocode
		(
			{
            	'address': address,//検索する住所　〒◯◯◯-◯◯◯◯ 住所　みたいな形式でも検索できる
            	'region': 'jp'
			},
		
			function (results, status)
			{
				if (status == google.maps.GeocoderStatus.OK)
				{
					// google.maps.event.addDomListener(window, 'load', function ()
					// {
						var map_tag = document.getElementById(drawID);
						// 取得した座標をセット緯度経度をセット
						var map_location = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
						//マップ表示のオプション
						var map_options =
							{
								zoom: 16,//縮尺
								center: map_location,//地図の中心座標
								//ここをfalseにすると地図上に人みたいなアイコンとか表示される
								disableDefaultUI: true,
								mapTypeId: google.maps.MapTypeId.ROADMAP//地図の種類を指定
							};

							//マップを表示する
						var map = new google.maps.Map(map_tag, map_options);

						//地図上にマーカーを表示させる
						var marker = new google.maps.Marker({
							position: map_location,//マーカーを表示させる座標
							map: map//マーカーを表示させる地図
						});

						document.getElementById(drawID).classList.add("loaded")
					// });
				}
			}
		);

		var target = document.getElementById(drawID);
		var classNames = target.className;

		if(classNames == 'loaded')
		{
			result = true;
		}

		return result;
	}

	// =======================
	//  TwitterIDを更新します。
	// =======================
	function UpdateCurrentPlaceData(_placeID, _placeName, _address, _imagePath, _comment, _userID)
	{
		var args = {
			placeID 	: _placeID,
			placeName 	: _placeName,
			address 	: _address,
			imagePath 	: _imagePath,
			comment		: _comment,
			userID		: _userID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/UpdatePlaceAPI2.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// =======================
	//  お気に入り登録します。
	// =======================
	function AddFavorite(_userID, _favoriteType, _keyID)
	{
		var args = {
			userID		 : _userID,
			keyID 	 	 : _keyID,
			favoriteType : _favoriteType
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/RegistFavoriteAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}


	// =======================
	//  お気に入り登録を解除します。
	// =======================
	function RemoveFavorite(_userID, _favoriteType, _keyID)
	{
		var args = {
			userID		 : _userID,
			keyID 	 	 : _keyID,
			favoriteType : _favoriteType
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/RemoveFavoriteAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = true;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  イベントを新規作成します。
	// ========================
	function RegistEvent(
		_userID, _userName, _eventName
		, _placeID, _placeName, _comment
		, _eventDate, _eventTimeFrom, _eventTimeTo)
	{
		var argData = {
			userID		: _userID,
			userName	  : _userName,
			eventName	 : _eventName,
			placeID	   : _placeID,
			placeName	 : _placeName,
			comment	   : _comment,
			eventDate	 : _eventDate,
			eventTimeFrom : _eventTimeFrom,
			eventTimeTo   : _eventTimeTo
		};

		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 : "php/RegistEventAPI.php",
			data		: argData,
			async: false,
			timeout: 100,
			
			success: function(data, dataType)
			{
				// 呼び出し結果
				if(data.RESULT == false)
				{
					alert(data.MESSAGE);
				}
				else
				{
					returnData = data.INSERT_ID;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  コンボを新規作成します。
	// ========================
	function RegistCombo(
		_characterID, _userID, _comboName, _comboRecipe
		, _comboDamage, _comment, _useGauge, _gainGauge, _tags, _movieTweetID) 
	{
		var argData = {
			characterID	: _characterID,
			userID		: _userID,
			comboName	: _comboName,
			comboRecipe	: _comboRecipe,
			comboDamage	: _comboDamage,
			comment	 	: _comment,
			useGauge	: _useGauge,
			gainGauge	: _gainGauge,
			tags		: _tags,
			movieTweetID: _movieTweetID
		};

		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 : "php/RegistComboAPI.php",
			data		: argData,
			async: false,
			timeout: 100,
			
			success: function(data, dataType)
			{
				// 呼び出し結果
				if(data.RESULT == false)
				{
					alert(data.MESSAGE);
				}
				else
				{
					returnData = data.INSERT_ID;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  コンボを更新します。
	// ========================
	function UpdateCombo(_comboID, _userID, _comboName, _comboRecipe
		, _comboDamage, _comment, _useGauge, _gainGauge, _tags, _movieTweetID) 
	{
		var argData = {
			comboID		: _comboID,
			userID		: _userID,
			comboName	: _comboName,
			comboRecipe	: _comboRecipe,
			comboDamage	: _comboDamage,
			comment	 	: _comment,
			useGauge	: _useGauge,
			gainGauge	: _gainGauge,
			tags		: _tags,
			movieTweetID: _movieTweetID
		};

		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 : "php/UpdateComboAPI.php",
			data		: argData,
			async: false,
			timeout: 100,
			
			success: function(data, dataType)
			{
				// 呼び出し結果
				if(data.RESULT == false)
				{
					alert(data.MESSAGE);
				}
				else
				{
					returnData = true;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  コンボを更新します。
	// ========================
	function DeleteCombo(_comboID) 
	{
		var argData = {
			comboID		: _comboID
		};

		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/DeleteComboAPI.php",
			data		: argData,
			async: false,
			timeout: 100,
			
			success: function(data, dataType)
			{
				// 呼び出し結果
				if(data.RESULT == false)
				{
					alert(data.MESSAGE);
				}
				else
				{
					returnData = true;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  週間イベントを新規作成します。
	// ========================
	function RegistWeeklyEvent(
		_userID,  _eventName
		, _eventDate, _year, _month
		, _placeID, _placeName, _comment
		, _eventTimeFrom, _eventTimeTo)
	{
		var argData = {
			userID		: _userID,
			eventName	 : _eventName,
			eventDate	: _eventDate,
			year : _year,
			month : _month,
			placeID	   : _placeID,
			placeName	 : _placeName,
			comment	   : _comment,
			eventTimeFrom : _eventTimeFrom,
			eventTimeTo   : _eventTimeTo
		};

		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 : "php/RegistWeeklyEventAPI.php",
			data		: argData,
			async: false,
			timeout: 100,
			
			success: function(data, dataType)
			{
				// 呼び出し結果
				if(data.RESULT == false)
				{
					alert(data.MESSAGE);
				}
				else
				{
					returnData = data.INSERT_ID;
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ========================
	//  イベントに画像を付与します
	// ========================
	function UploadEventImage(_formData, _eventID)
	{
		_formData.append("eventID", _eventID);

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 : "php/UploadFileAPI.php",
			data		: _formData,
			cache	   : false,
			processData : false,
			contentType : false,
			async: false,
			timeout: 100,
					
			success: function(data, dataType)
			{
				if(data == false)
				{
					// 何もしない
					alert("ファイルのアップロードに失敗しました。");
				}
				else
				{
					returnData = true;
				}
			},
					
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
				returnData = false;
			}
		});

		return returnData;
	}

	// =======================
	//  コンボ動画の更新
	// =======================
	function UploadComboMovie(_formData, _comboID)
	{
		_formData.append("placeID", _comboID);
		_formData.append("fileType", 'COMBO_MOVIE');
		
		var returnData = null;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/UploadFileAPI2.php",
			data		: _formData,
			cache	   	: false,
			processData : false,
			contentType : false,
			async		: false,
			timeout		: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.MOVIE_PATH;
				}
				else
				{
					// 何もしない
					alert("動画のアップロードに失敗しました。");
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}

	// ==============================
	//  評価を送信します。
	// ------------------------------
	//  EVALUTION_TYPE: [0] コンボ
	//                  [1] ユーザー
	// ==============================
	function SendEvalution(_targetID, _evalutionType, _value, _userID, _comment)
	{
		var args = {
			targetID : _targetID,
			evalutionType : _evalutionType,
			value : _value,
			userID : _userID,
			comment : _comment
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/SendEvalutionAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = true;
					alert(data.MESSAGE);
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				returnData = false;
			}
		});

		return returnData;
	}

	// ==============================
	//  評価を取得します。
	// ------------------------------
	//  EVALUTION_TYPE: [0] コンボ
	//                  [1] ユーザー
	// ==============================
	function GetEvalutionData(_targetID, _evalutionType)
	{
		var args = {
			targetID : _targetID,
			evalutionType : _evalutionType,
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "php/GetEvalutionDataAPI.php",
			data: args,
			async: false,
			timeout: 100,
			success: function(data)
			{
				if(data.RESULT == true)
				{
					returnData = data.EVALUTION_DATA;
				}
				else
				{
					alert(data.MESSAGE);
				}
			},

			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
			}
		});

		return returnData;
	}


	// ==============================
	//  ツイートIDからツイート者のIDを取得
	// ==============================
	function GetTwitterID(_tweetID)
	{
		var args = {
			tweetID : _tweetID
		}

		var returnData = false;

		$.ajax({
			type		: "POST",
			dataType	: 'json',
			url		 	: "php/GetTwitterIDAPI.php",
			data: args,
			async: false,
			timeout: 100,
							
			success: function(data, dataType)
			{
				if(data.RESULT == true)
				{
					returnData = data.TWITTER_ID;
				}
				else
				{
					// 何もしない
					alert(data.MESSAGE);
				}
			},
							
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert('error : ' + errorThrown);
			}
		});

		return returnData;
	}
	
	// ==============================
	//  メインヘッダーを読み込みます。
	// ==============================
	function LoadMainHeader(targetID_)
	{
		var control = document.getElementById(targetID_);
		control.innerHTML = "<h1>どこＵＮＩ？</h1>";
	}
	
	// ========================
	//  場所のロード
	// ========================
	function GetPlaceData(areaID_)
	{
		var args = {
			areaID : areaID_,
		};

		var returnData = null;

		$.ajax({
			type: "POST",
			url: "php/GetPlaceAPI.php",
			data: args,
			success: function(data)
			{
				returnData = data;
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				console.log('error : ' + errorThrown);
				return false;
			}
		});
				
		return true;
	}
			
			// ========================
			//  参加区分のロード
			// ========================
			function GetJoinTypeNew()
			{
				var data = {
					key : "JOIN_TYPE",
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetType.php",
					data: data,
					success: function(data)
					{
						if(data.RESULT == true)
						{
							data.TYPE_DATA.forEach(function(value)
							{
								// 行を変更する
								$("#joinType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
							});
						}
						else
						{
							alert(data.MESSAGE);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// ========================
			//  目的のロード
			// ========================
			function GetPurposeTypeNew()
			{
				var args = {
					key : "PURPOSE",
				};
				
				$.ajax({
					type: "POST",
					url: "php/GetType.php",
					data: args,
					success: function(data)
					{
						if(data.RESULT == true)
						{
							data.TYPE_DATA.forEach(function(value)
							{
								// 行を変更する
								$("#purposeType").append('<option value=' + value.VALUE + '>' + value.CAPTION + '</option>');
							});
						}
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}
			
			// ========================
			//  ユーザデータのロード
			// ========================
			function GetUserDataNew()
			{
				var userID = getCookie("USER_ID");
				var args = {
					userID : userID,
				};
				
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "php/GetUserData.php",
					data: args,
					success: function(data)
					{
						data.forEach(function(value)
						{
							// クッキー設定
							setCookie("USER_NAME", value.USER_NAME, 1);
							setCookie("MAIN_CHARACTER_ID", value.MAIN_CHARACTER_ID, 1);
							setCookie("RIP", value.RIP, 1);
							setCookie("AREA_ID", value.AREA_ID, 1);
						});
					},
					
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						console.log('error : ' + errorThrown);
						return false;
					}
				});
				
				return true;
			}



