function loadScript(src, callback)
{
	var done = false;
	var head = document.getElementsByTagName('head')[0];
	var script = document.createElement('script');
	
	script.src = src;
	head.appendChild(script);
	
	script.onload = script.onreadystatechange = function() {
		if(!done && (!this.readyState ||
			this.readyState === "loaded" ||
			this.readyState === "complete)) {
			done = true;
			callback();
			
			script.onload = script.onreadystatechange = null;
			
			if(head && script.parentNode) {
				head.removeChild(script);
			}
		}
	
	};
}
