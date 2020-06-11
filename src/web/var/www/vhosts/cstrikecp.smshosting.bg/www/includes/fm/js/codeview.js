/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

function CodeView(node, options, id) {
//---------------------------------------------------------------------------------------------------------
// Initialization
//---------------------------------------------------------------------------------------------------------
	this.node = node;
	this.id = id;
	this.language = options[0] ? options[0].toLowerCase() : '';
	this.viewLineNumbers = fmTools.inArray('lineNumbers', options, true);
	this.textWidth = node.offsetWidth;
	this.textHeight = node.offsetHeight;
	this.bgColor = this.node.style.backgroundColor ? this.node.style.backgroundColor : '#FFFFFF';
	this.content = fmTools.trim(this.node.innerHTML);
	this.iframe = null;
	this.canvas = null;
	this.numbers = null;
	this.numbersCont = null;

//---------------------------------------------------------------------------------------------------------
// Class methods
//---------------------------------------------------------------------------------------------------------
	this.create = function() {
		this.node.style.overflow = 'hidden';
		if(!this.node.style.borderWidth) this.node.style.border = '1px solid #808080';
		this.setSize();
		this.setLanguageStyle();

		if(this.viewLineNumbers) {
			this.numbersCont = document.createElement('div');
			this.numbersCont.style.width = '40px';
			this.numbersCont.style.height = '100%';
			this.numbersCont.style.styleFloat = 'left';
			this.numbersCont.style.cssFloat = 'left';
			this.numbersCont.style.overflow = 'hidden';
			this.numbersCont.style.borderRight = '1px solid #808080';
			this.numbersCont.style.backgroundColor = '#F0F0F0';
			this.node.appendChild(this.numbersCont);

			this.numbers = document.createElement('div');
			this.numbers.style.textAlign = 'right';
			this.numbers.style.padding = '4px';
			this.numbers.style.color = '#808080';
			this.numbers.style.fontFamily = 'Monospace';
			this.numbers.style.fontSize = '13px';
			this.numbersCont.appendChild(this.numbers);

			fmTools.setUnselectable(this.numbers);
			this.setNumbers();
			this.textWidth -= 41;
		}

		if(this.createIFrame()) {
			if(this.content != '') {
				this.content = this.content.replace(/</g, '&lt;');
				this.content = this.content.replace(/>/g, '&gt;');
				this.setCode(this.content);
				this.syntaxHilight();
			}
		}
		else alert("Could not create code viewer");

		this.node.style.visibility = 'visible';
	}

	this.createIFrame = function() {
		var iframe, html;

		iframe = document.createElement('iframe');
		iframe.frameBorder = 0;
		iframe.style.width = this.textWidth + 'px';
		iframe.style.height = '100%';
		iframe.style.backgroundColor = this.bgColor;
		this.node.appendChild(iframe);

		html =	'<!doctype html>' +
				'<html><head><style type="text/css"> ' +
				'* { box-sizing:content-box; -moz-box-sizing:content-box; -ms-box-sizing:content-box; } ' +
				'body { ' +
				'margin: 4px; ' +
				'background-color: ' + this.bgColor + '; ' +
				'white-space: nowrap; ' +
				'color: #000000; ' +
				'font-family: Monospace; ' +
				'font-size: 13px; ' +
				'} ' +
				'p { margin: 0px; } ' +
				this.setLanguageStyle() +
				'</style></head>' +
				'<body></body></html>';

		if(iframe.contentWindow) this.iframe = iframe.contentWindow;
		else if(document.frames) this.iframe = document.frames[this.id];

		if(this.iframe) {
			this.iframe.document.open();
			this.iframe.document.write(html);
			this.iframe.document.close();
			this.canvas = this.iframe.document.body;
			fmTools.addListener(this.iframe, 'scroll', this.scrollHandler.bindAsEventListener(this));
			return true;
		}
		return false;
	}

	this.setSize = function() {
		var i, val;
		var offWidth = offHeight = 0;
		var offWidths = ['borderLeftWidth', 'borderRightWidth', 'paddingLeft', 'paddingRight'];
		var offHeights = ['borderTopWidth', 'borderBottomWidth', 'paddingTop', 'paddingBottom'];

		for(i in offWidths) {
			val = parseInt(this.node.style[offWidths[i]]);
			if(!isNaN(val)) offWidth += val;
		}

		for(i in offHeights) {
			val = parseInt(this.node.style[offHeights[i]]);
			if(!isNaN(val)) offHeight += val;
		}
		if(this.node.style.height == '') this.node.style.height = this.node.offsetHeight + 'px';
		if(this.node.style.width == '') this.node.style.width = this.node.offsetWidth + 'px';
		this.textWidth = this.node.offsetWidth - offWidth;
		this.textHeight = this.node.offsetHeight - offHeight;
		this.node.innerHTML = '';
	}

	this.setLanguageStyle = function() {
		var map = fmSyntax[this.language];
		var style = 'u, tt, b, s, i, em, ins { text-decoration: none; font-style: normal; font-weight: normal; } ';
		for(var key in map) if(map[key].style) style += map[key].style + ' ';
		return style;
	}

	this.setNumbers = function() {
		var lines = this.content.split(/\n/);
		var cnt = lines.length;
		var numbers = [];
		cnt += 10;
		for(var i = 1; i <= cnt; i++) numbers.push(i);
		this.numbers = fmTools.replaceHtml(this.numbers, numbers.join('<br>'));
	}

	this.getCode = function(convSpecialChars) {
		var code = this.canvas.innerHTML.replace(/[\r\n]/g, '');
		if(code) {
			if(OP) {
				/* ugly workaround for Opera */
				code = code.replace(/<p>(.*?)<br><\/p>/gi, '$1\n');
			}
			else if(WK) {
				/* ugly workaround for Chrome */
				code = code.replace(/<div>(.*?)<br><\/div>/gi, '$1\n');
			}
			code = code.replace(/<(p|div)>(.*?)<\/(p|div)>/gi, '$2\n');
			code = code.replace(/<br>/gi, '\n');
			code = code.replace(/(&nbsp;){4}/g, '\t');
			code = code.replace(/&nbsp;/g, ' ');
			code = code.replace(/<[^>]+>/g, '');

			if(convSpecialChars) {
				code = code.replace(/&amp;/g, '&');
				code = code.replace(/&lt;/g, '<');
				code = code.replace(/&gt;/g, '>');
			}
		}
		return code;
	}

	this.setCode = function(code) {
		code = code.replace(/\r?\n/g, '<br>');
		code = code.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
		code = code.replace(/\s/g, '&nbsp;');
		this.canvas = fmTools.replaceHtml(this.canvas, code);
		if(this.numbers) setTimeout(this.scrollHandler.bind(this), 50);
	}

	this.parseCode = function(code) {
		var map, key, i;
		if(map = fmSyntax[this.language]) for(key in map) {
			if(map[key].match) for(i = 0; i < map[key].match.length; i++) {
				code = code.replace(map[key].match[i], map[key].replace[i]);
			}
		}
		return code;
	}

	this.syntaxHilight = function() {
		var code = this.parseCode(this.getCode());
		this.setCode(code);
	}

//---------------------------------------------------------------------------------------------------------
// Event handlers
//---------------------------------------------------------------------------------------------------------
	this.scrollHandler = function(e) {
		var scrTop = this.canvas.scrollTop ? this.canvas.scrollTop : this.canvas.parentNode.scrollTop;
		if(this.numbersCont) this.numbersCont.scrollTop = scrTop;
	}
}
