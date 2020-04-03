// =========================
//  ドラッグを開始
// =========================
function f_dragstart(event)
{
  //ドラッグするデータのid名をDataTransferオブジェクトにセット
  event.dataTransfer.setData("text", event.target.id);
}

// =========================
//  ドラッグオーバー
// =========================
function f_dragover(event)
{
  //dragoverイベントをキャンセルして、ドロップ先の要素がドロップを受け付けるようにする
  event.preventDefault();
}

// =========================
//  ドロップ
// =========================
function f_drop(event)
{
  //ドラッグされたデータのid名をDataTransferオブジェクトから取得
  var id_name = event.dataTransfer.getData("text");

  //id名からドラッグされた要素を取得
  var drag_elm =document.getElementById(id_name);

  //ドロップ先にドラッグされた要素を追加
  event.currentTarget.appendChild(drag_elm);
  
  //エラー回避のため、ドロップ処理の最後にdropイベントをキャンセルしておく
  event.preventDefault();
}