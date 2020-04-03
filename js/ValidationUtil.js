
function IsMailAddress(str){
  ml = /.+@.+\..+/; // チェック方式
  mf = str;
  if(!mf.match(ml)) {
    alert("メールアドレスが不正です");
    return false;
  }
  return true;
}

function InputCheck(str, argName) {
 if (str.match(/[^A-Za-z0-9]+/)) {
 //半角英数字以外の文字が存在する場合、エラー
    alert(argName + " に 半角英数字以外の文字は使えません");
    return false;
 }
 return true;
}


function IsNumber(str, argName) {
 if (str.match(/[^0-9]+/)) {
 //半角数字以外の文字が存在する場合、エラー
    alert(argName + " に 半角数字以外の文字は使えません");
    return false;
 }
 return true;
}