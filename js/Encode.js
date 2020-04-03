
// 「byte配列」暗号化
function encrypt(password, hash) {
   let value = hash.digest('hex')
   return value;
}


	function GetTodayDate(){
    	var now = new Date();

		var year = now.getFullYear();
		var month = now.getMonth() + 1;
		var day = now.getDate();

		return year.toString() + "-" + month.toString() + "-" + day.toString();
	}