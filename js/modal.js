//modal
function modalOpen(modalID){
	modalID = "#" + modalID;
	//body内の最後に<div id="modal-bg"></div>を挿入
	$("body").append('<div id="modal-background" onClick="modalFadeOut(\'' + modalID + '\'); return false;"></div>');
	//画面中央を計算する関数を実行
	
	modalResize(modalID);
	
	//モーダルウィンドウを表示
	$("#modal-background,"+ modalID).fadeIn("slow");
	
	//画面の左上からmodal-mainの横幅・高さを引き、その値を2で割ると画面中央の位置が計算できます
	$(window).resize(modalResize);

}
		
function modalResize(modalID)
{
}

function modalFadeOut(modalID)
{
	$("#modal-background,"+ modalID).fadeOut("slow",function()
	{
		//挿入した<div id="modal-bg"></div>を削除
		$('#modal-background').remove() ;
	});
}