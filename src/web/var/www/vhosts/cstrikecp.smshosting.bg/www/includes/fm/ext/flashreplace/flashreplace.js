/*
	FlashReplace is developed by Robert Nyman, http://www.robertnyman.com. License and downloads: http://code.google.com/p/flashreplace/
*/
// ---
var FlashReplace = {
	elmToReplace : null,
	flashIsInstalled : null,
	defaultFlashVersion : 7,
	replace : function (elmToReplace, src, id, width, height, version, params){
		this.elmToReplace = document.getElementById(elmToReplace);
		this.flashIsInstalled = this.checkForFlash(version || this.defaultFlashVersion);
		if(this.elmToReplace) {
			if(this.flashIsInstalled) {
				var w = (width.indexOf('%') != -1) ? width : width + 'px';
				var h = (height.indexOf('%') != -1) ? height : height + 'px';
				var obj = '<div id="' + id + 'Cont" style="width:' + w + '; height:' + h + '">';
				obj += '<object' + ((window.ActiveXObject)? ' id="' + id + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="' + src + '"' : '');
				obj += ' width="' + width + '"';
				obj += ' height="' + height + '"';
				obj += '>';
				var param = '<param';
				param += ' name="movie"';
				param += ' value="' + src + '"';
				param += '>';
				param += '';
				var extraParams = '';
				var extraAttributes = '';
				for(var i in params){
					extraParams += '<param name="' + i + '" swliveconnect="true" value="' + params[i] + '">';
					extraAttributes += ' ' + i + '="' + params[i] + '"';
				}
				var embed = '<embed id="' + id + '" src="' + src + '" type="application/x-shockwave-flash" width="' + width + '" height="' + height + '"';
				var embedEnd = extraAttributes + '></embed>';
				var objEnd = '</object></div>';
				this.elmToReplace.innerHTML = obj + param + extraParams + embed + embedEnd + objEnd;
			}
			else this.elmToReplace.innerHTML = '<div class="fmError" style="margin:5px">No Flash player available!</div>';
		}
	},

	checkForFlash : function (version){
		this.flashIsInstalled = false;
		var flash;
		if(window.ActiveXObject){
			try{
				flash = new ActiveXObject(("ShockwaveFlash.ShockwaveFlash." + version));
				this.flashIsInstalled = true;
			}
			catch(e){
				// Throws an error if the version isn't available
			}
		}
		else if(navigator.plugins && navigator.mimeTypes.length > 0){
			flash = navigator.plugins["Shockwave Flash"];
			if(flash){
				var flashVersion = navigator.plugins["Shockwave Flash"].description.replace(/.*\s(\d+\.\d+).*/, "$1");
				if(flashVersion >= version){
					this.flashIsInstalled = true;
				}
			}
		}
		return this.flashIsInstalled;
	}
};
// ---