/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

function CodeEdit(node, options, id) {
//---------------------------------------------------------------------------------------------------------
// Initialization
//---------------------------------------------------------------------------------------------------------
	this.node = node;
	this.language = options[0] ? options[0].toLowerCase() : '';
	this.viewLineNumbers = fmTools.inArray('lineNumbers', options, true);
	this.setFocus = fmTools.inArray('focus', options, true);
	this.id = id;
	this.textWidth = node.offsetWidth;
	this.textHeight = node.offsetHeight;
	this.fieldName = (node.name != '') ? node.name : node.id;
	this.bgColor = this.node.style.backgroundColor ? this.node.style.backgroundColor : '#FFFFFF';
	this.content = this.node.value.replace(/\s+$/, '');
	this.editor = null;
	this.canvas = null;
	this.numbers = null;
	this.input = null;
	this.timer = null;
	this.lines = [];
	this.cntLines = Math.round(this.textHeight / 16);
	this.maxLines = 0;
	this.paste = false;

//---------------------------------------------------------------------------------------------------------
// Class methods
//---------------------------------------------------------------------------------------------------------
	this.create = function() {
		if(DM) {
			var cont = document.createElement('div');
			cont.style.width = this.textWidth + 'px';
			cont.style.height = this.textHeight + 'px';
			this.node.parentNode.replaceChild(cont, this.node);

			if(this.viewLineNumbers) {
				this.numbers = document.createElement('div');
				this.numbers.style.display = 'none';
				this.numbers.style.styleFloat = 'left';
				this.numbers.style.cssFloat = 'left';
				this.numbers.style.overflow = 'hidden';
				this.numbers.style.textAlign = 'right';
				this.numbers.style.padding = '4px';
				this.numbers.style.borderRight = '1px solid #808080';
				this.numbers.style.color = '#808080';
				this.numbers.style.backgroundColor = '#F0F0F0';
				this.numbers.style.height = this.textHeight + 'px';
				this.numbers.style.width = '40px';
				this.numbers.style.fontFamily = 'Monospace';
				this.numbers.style.fontSize = '13px';
				cont.appendChild(this.numbers);
				fmTools.setUnselectable(this.numbers);
				this.setNumbers();
			}

			this.node = document.createElement('iframe');
			this.node.id = this.id;
			this.node.frameBorder = 0;
			this.node.style.width = cont.offsetWidth + 'px';
			this.node.style.height = cont.offsetHeight + 'px';
			cont.appendChild(this.node);

			this.input = document.createElement('input');
			this.input.type = 'hidden';
			this.input.name = this.input.id = this.fieldName;
			cont.appendChild(this.input);

			if(!this.initEditor()) alert("Could not create code editor");
		}
		else {
			this.node.style.whiteSpace = 'pre';
			this.node.style.padding = '2px';
			this.editor = this.node;
			fmTools.addListener(this.node, 'keydown', this.keyDownHandler.bindAsEventListener(this));
		}
	}

	this.getEditor = function() {
		if(this.node.contentWindow) return this.node.contentWindow;
		if(document.frames) return document.frames[this.id];
		return false;
	}

	this.initEditor = function() {
		if(this.editor = this.getEditor()) {
			var html =	'<html><head><style type="text/css"> ' +
						'BODY { ' +
						'margin: 4px; ' +
						'background-color: ' + this.bgColor + '; ' +
						'white-space: nowrap; ' +
						'color: #000000; ' +
						'font-family: Monospace; ' +
						'font-size: 13px; ' +
						'} ' +
						'P { margin: 0px; } ' +
						'IMG { width: 1px; height: 1px; } ' +
						this.setLanguageStyle() +
						'</style></head>' +
						'<body></body></html>';

			this.editor.document.designMode = 'on';
			if(GK) {
				this.editor.document.execCommand('useCSS', false, true); /* for older browsers */
				this.editor.document.execCommand('styleWithCSS', false, false);
			}
			this.editor.document.open();
			this.editor.document.write(html);
			this.editor.document.close();
			this.canvas = this.editor.document.body;

			if(this.viewLineNumbers) {
				this.numbers.style.display = 'block';
				this.node.style.width = (this.node.offsetWidth - 40) + 'px';
			}

			if(this.content != '') {
				this.content = this.content.replace(/</g, '&lt;');
				this.content = this.content.replace(/>/g, '&gt;');
				this.setCode(0, 0, this.content);
				this.syntaxHilight(true);
			}
			else if(FF) {
				/* workaround for Firefox: place caret correctly into canvas */
				this.canvas.innerHTML = '<br>';
			}
			fmTools.addListener(this.editor.document, 'keydown', this.keyDownHandler.bindAsEventListener(this));
			fmTools.addListener(this.editor.document, 'keyup', this.keyUpHandler.bindAsEventListener(this));
			fmTools.addListener(this.editor, 'scroll', this.scrollHandler.bindAsEventListener(this));

			if(FF) {
				/* for some reason, this only works with Firefox :-( */
				fmTools.addListener(this.editor, 'load', this.loadHandler.bindAsEventListener(this));
			}
			else {
				/* ugly workaround for other browsers */
				setTimeout(this.loadHandler.bindAsEventListener(this), 1000);
			}
			return true;
		}
		return false;
	}

	this.setLanguageStyle = function() {
		var map = fmSyntax[this.language];
		var style = 'u, tt, b, s, i, em, ins { text-decoration: none; font-style: normal; font-weight: normal; } ';
		for(var key in map) if(map[key].style) style += map[key].style + ' ';
		return style;
	}

	this.setNumbers = function() {
		if(this.lines) {
			var cnt = this.lines.length + 1;
			if(cnt < this.cntLines) cnt = this.cntLines;
		}
		else var cnt = this.cntLines;

		var numbers = [];
		cnt += 10;
		for(var i = 1; i <= cnt; i++) numbers.push(i);
		this.numbers = fmTools.replaceHtml(this.numbers, numbers.join('<br>'));
		this.maxLines = cnt;
	}

	this.getCode = function(lineFrom, lineTo, convSpecialChars) {
		var code = this.canvas.innerHTML.replace(/[\r\n]/g, '');
		if(code) {
			code = code.replace(/<(p|div)>(.*?)<\/(p|div)>/gi, '$2\n');
			code = code.replace(/<br>/gi, '\n');
			if(!IE) code = code.replace(/<img>/i, '\u0001');

			this.lines = code.split('\n');

			if(!lineFrom) lineFrom = 0;
			if(!lineTo || lineTo > this.lines.length) lineTo = this.lines.length;

			code = this.lines.slice(lineFrom, lineTo).join('\n');
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

	this.setCode = function(lineFrom, lineTo, code) {
		code = code.replace(/\r?\n/g, '<br>');
		code = code.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
		code = code.replace(/\s/g, '&nbsp;');

		if(this.lines && lineTo > 0) {
			var c = [];
			c = this.lines.slice(0, lineFrom);
			c.push(code);
			c = c.concat(this.lines.slice(lineTo));
			code = c.join('<br>');
		}
		if(!IE) code = code.replace(/\u0001/, '<img>');
		this.canvas = fmTools.replaceHtml(this.canvas, code);

		if(this.numbers) {
			if(this.lines.length > this.maxLines) this.setNumbers();
			setTimeout(this.scrollHandler.bind(this), 50);
		}
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

	this.insertMarker = function() {
		if(IE) {
			this.insertText('\u0001');
		}
		else if(GK) {
			var range = this.editor.getSelection().getRangeAt(0);
			range.insertNode(this.editor.document.createElement('img'));
		}
	}

	this.removeMarker = function() {
		if(IE) {
			var range = this.canvas.createTextRange();
			if(range.findText('\u0001')) {
				range.text = '';
				range.select();
			}
		}
		else if(GK) {
			var sel = this.editor.getSelection();
			var range = this.editor.document.createRange();
			var node = this.canvas.getElementsByTagName('img')[0];
			range.selectNode(node);
			if(OP) range.collapse(true);
			sel.removeAllRanges();
			sel.addRange(range);
			node.parentNode.removeChild(node);
		}
	}

	this.insertText = function(str) {
		if(IE) {
			var range = this.editor.document.selection.createRange();
			range.text = str;
		}
		else if(GK) {
			if(DM) {
				this.insertMarker();
				var range = this.editor.getSelection().getRangeAt(0);
				range.insertNode(this.editor.document.createTextNode(str));
				this.removeMarker();
			}
			else {
				/* special treatment for textarea */
				var start = this.editor.selectionStart;
				var end = this.editor.selectionEnd;
				var top = this.editor.scrollTop;
				var content = this.editor.value;
				this.editor.value = content.substring(0, start) + str + content.substring(end, content.length);
				this.editor.selectionStart = start + str.length;
				this.editor.selectionEnd = start + str.length;
				if(top) this.editor.scrollTop = top;
			}
		}
	}

	this.syntaxHilight = function(init) {
		if(init) {
			var lineFrom = lineTo = 0;
		}
		else {
			var lineFrom = this.getStartLine();
			var lineTo = lineFrom + this.cntLines;
		}
		this.insertMarker();
		var code = this.parseCode(this.getCode(lineFrom, lineTo));
		this.setCode(lineFrom, lineTo, code);
		this.removeMarker();
		this.timer = null;
	}

	this.getStartLine = function() {
		var perc = this.canvas.scrollTop / this.canvas.scrollHeight;
		return Math.round(this.lines.length * perc) + 1;
	}

//---------------------------------------------------------------------------------------------------------
// Event handlers
//---------------------------------------------------------------------------------------------------------
	this.loadHandler = function(e) {
		if(this.setFocus) this.editor.focus();
		fmTools.addListener(this.input.form, 'submit', this.submitHandler.bindAsEventListener(this));

		/* workaround for FileManager - the above submit event handler does not work there */
		fmTools.addListener(this.editor, 'blur', this.submitHandler.bindAsEventListener(this));
	}

	this.keyDownHandler = function(e) {
		var evt = e ? e : this.editor.event;
		var keyCode = (evt.which || evt.keyCode || evt.charCode);
		this.paste = (keyCode == 86 && (evt.ctrlKey || evt.metaKey));

		if(!evt.shiftKey && !evt.ctrlKey && !evt.altKey && !evt.metaKey) {
			switch(keyCode) {
				case 9:
					this.insertText('\u00A0\u00A0\u00A0\u00A0');
					if(evt.preventDefault) evt.preventDefault();
					return false;
				case 13:
					if(IE) {
						this.insertText('\n');
						if(evt.preventDefault) evt.preventDefault();
						return false;
					}
					break;
			}
		}
	}

	this.keyUpHandler = function(e) {
		if(typeof fmTools == 'undefined') return;
		var evt = e ? e : this.editor.event;
		var keyCode = (evt.which || evt.keyCode || evt.charCode);
		var ctrlA = (keyCode == 65 && (evt.ctrlKey || evt.metaKey));
		var ctrlC = (keyCode == 67 && (evt.ctrlKey || evt.metaKey));
		var ignoreKey = (fmTools.inArray(keyCode, [16, 17]) || ctrlA || ctrlC);
		var moveKey = fmTools.inArray(keyCode, [33, 34, 37, 38, 39, 40]);

		if(!ignoreKey && !moveKey) {
			if(this.timer) clearTimeout(this.timer);
			this.timer = setTimeout(this.syntaxHilight.bind(this), 500);
		}
	}

	this.scrollHandler = function(e) {
		if(this.numbers) this.numbers.scrollTop = this.canvas.scrollTop;
	}

	this.submitHandler = function(e) {
		this.input.value = this.getCode(0, 0, true);
	}
}
