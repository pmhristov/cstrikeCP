/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

var fmLib =  {

	fadeSpeed: 15,   // fade speed (0 - 30; 0 = no fading)
	mouseX: 0,
	mouseY: 0,
	fadeTimer: 0,
	fileIv: 0,
	mpIv: 0,
	opacity: 0,
	dragging: false,
	noMenu: false,
	useRightClickMenu: {},
	isResized: {},
	resizeTimeout: null,
	browserContextMenu: true,
	dialog: null,
	progBar: null,
	fdObj: {},
	refreshIv: {},
	useFlash: false,

	callOK: function(msg, url, frmName) {
		var ok = confirm(msg);
		if(ok) {
			if(url) this.call(url, frmName);
			else if(frmName) document.forms[frmName].submit();
		}
	},

	call: function(url, frmName) {
		url.match(/fmContainer=(\w+)/);
		var curCont = RegExp.$1;
		var ajaxObj = new ajax();

		if(this.dialog && this.opacity) this.fadeOut(this.opacity, this.dialog);

		if(url.match('fmMode=edit')) {
			ajaxObj.makeRequest(url, function() {
				var contObj = $$('fmEditorCont');

				if(contObj) {
					fmParser.parseEditor(ajaxObj.response, contObj);
					fmLib.initEditor();
				}
			});
		}
		else if(url.match('fmMode=readTextFile')) {
			ajaxObj.makeRequest(url, function() {
				var contObj = $$('fmTextViewerCont');

				if(contObj) {
					fmParser.parseTextViewer(ajaxObj.response, contObj);
					fmLib.initTextViewer();
				}
			});
		}
		else {
			var listCont = $$(curCont + 'List');
			if(listCont) {
				var listLoad;
				var fmCont = $$(curCont);

				if(fmCont) listLoad = this.viewLoader(fmCont, url);

				ajaxObj.makeRequest(url, function() {
					if(ajaxObj.query.match(/fmMode=toggleDeleted/)) {
						fmContSettings[curCont].viewDeleted = !fmContSettings[curCont].viewDeleted;
					}
					fmLib.viewResponse(ajaxObj.response, curCont, listLoad);
				}, frmName);
			}
		}
	},

	checkFile: function(listLoad, sid, curCont) {
		var iFrame = frames.fmFileAction;

		if(iFrame && iFrame.document) {
			var url = iFrame.document.location.href;
			var response = iFrame.document.body.innerHTML;

			if(response.match(/end:1\}$/)) {
				if(this.fileIv) clearInterval(this.fileIv);
				iFrame.document.body.innerHTML = '';
				this.viewResponse(response, curCont, listLoad);

				if(this.progBar) {
					this.fadeOut(this.opacity, this.progBar);
					this.progBar = null;
				}
			}
			else if(sid) {
				var ajaxObj = new ajax();
				ajaxObj.noWarning = true;
				url = fmWebPath + '/cgi/ping.pl?sid=' + sid + '&tmp=' + fmContSettings[curCont].tmp;

				ajaxObj.makeRequest(url, function() {
					if(ajaxObj.response) {
						var json = eval('(' + ajaxObj.response + ')');

						if(json.bytesTotal > 0) {
							var pbContent = fmLib.drawProgBar(json);

							if(!fmLib.progBar) {
								fmLib.viewProgress(pbContent);
							}
							else $$('fmProgressText').innerHTML = pbContent;
						}
					}
				});
			}
		}
	},

	getFile: function(curCont, id) {
		var iFrame = frames.fmFileAction;
		if(iFrame) {
			var url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getFile&fmObject=' + id;
			iFrame.document.location.href = url;
			this.fadeOut(this.opacity, this.dialog);
		}
	},

	drawProgBar: function(json) {
		if(typeof json != 'object') return false;
		var div, unit;

		if(json.bytesTotal > 1024 * 1024) {
			div = 1024 * 1024;
			unit = 'M';
		}
		else {
			div = 1024;
			unit = 'K';
		}
		var percent = (json.bytesTotal > 0) ? Math.round(json.bytesCurrent * 100 / json.bytesTotal) : 0;
		var current = fmTools.numberFormat(json.bytesCurrent / div, 2);
		var total = fmTools.numberFormat(json.bytesTotal / div, 2);
		var s = json.timeCurrent - json.timeStart;
		var bps = fmTools.numberFormat(json.bytesCurrent / 1024 / (s ? s : 1), 2);
		if(bps > 1024) bps = fmTools.numberFormat(bps / 1024, 2) + ' M/sec';
		else bps += ' K/sec';
		var h = Math.floor(s / 3600);
		s -= h * 3600;
		var m = Math.floor(s / 60);
		s -= m * 60;
		if(h < 10) h = '0' + h;
		if(m < 10) m = '0' + m;
		if(s < 10) s = '0' + s;

		html = '<table border="0" cellspacing="0" cellpadding="4" width="100%"><tr>';
		html += '<td colspan="2" align="center">';
		html += '<div class="fmProgressBarText" style="width:200px; margin-bottom:8px; overflow:hidden">' + json.filename + '</div>';
		html += '<div class="fmProgressBarBG" style="width:202px">';
		if(percent) html += '<div class="fmProgressBar" style="width:' + (percent * 2) + 'px">&nbsp;</div>';
		else html += '&nbsp;';
		html += '</div>';
		html += '</td>';
		html += '</tr><tr>';
		html += '<td class="fmProgressBarText" align="left">' + current + ' ' + unit + ' / ' + total + ' ' + unit + ' (' + percent + '%)</td>';
		html += '<td class="fmProgressBarText" align="right">' + bps + '</td>';
		html += '</tr><tr>';
		html += '<td class="fmProgressBarText" align="center" colspan="2">' + h + ':' + m + ':' + s + '</td>';
		html += '</tr></table>';
		return html;
	},

	viewLoader: function(fmCont, url) {
		var listLoad = document.createElement('div');
		this.setOpacity(20, listLoad);
		listLoad.style.position = 'absolute';
		listLoad.style.left = 0;
		listLoad.style.top = 0;
		listLoad.style.padding = 0;
		listLoad.style.backgroundColor = '#000000';
		listLoad.style.width = fmCont.offsetWidth + 'px';
		listLoad.style.height = fmCont.offsetHeight + 'px';
		listLoad.style.display = 'block';
		listLoad.style.zIndex = 68;
		fmCont.appendChild(listLoad);

		var img = document.createElement('img');
		var webPath = url.substring(0, url.lastIndexOf('/'));

		img.src = webPath + '/icn/ajax_loader.gif';
		img.width = 100;
		img.height = 100;
		img.style.position = 'absolute';
		img.style.left = '50%';
		img.style.top = '50%';
		img.style.marginLeft = '-50px';
		img.style.marginTop = '-50px';
		listLoad.appendChild(img);
		return listLoad;
	},

	viewResponse: function(json, curCont, listLoad, keepDialog) {
		var fmCont = $$(curCont);
		var titleCont = $$(curCont + 'Title');
		var listCont = $$(curCont + 'List');
		var logCont = $$(curCont + 'Log');
		var infoCont = $$(curCont + 'Info');

		if(typeof json != 'object') {
			if(json.match(/\{.+\}$/)) json = eval('(' + json + ')');
		}

		if(typeof json == 'object') {
			this.sortJson(json);
			fmContSettings[curCont].listJson = json;
			if(json.reloadExplorer != -1) fmContSettings[curCont].expJson = null;
			if(!json.login) this.getExplorer(curCont, '', '', json.reloadExplorer);
			if(titleCont) fmParser.parseTitle(json, titleCont);
			if(listCont) fmParser.parseMain(json, listCont);
			if(logCont) fmParser.parseLogMessages(json, logCont);
			if(infoCont) fmParser.parseDebugInfo(json, infoCont);
			if(json.error) this.viewError(json.error, curCont);
		}
		if(listLoad && fmCont) fmCont.removeChild(listLoad);
		
		if(!keepDialog && this.opacity && this.dialog) {
			this.fadeOut(this.opacity, this.dialog);
		}
	},

	initEditor: function() {
		var elems = document.getElementsByTagName('textarea');
		var options = ceos = [];

		for(var i = 0; i < elems.length; i++) {
			if(elems[i].className.match(/^codeedit(\s+(.+))?/i)) {
				options = RegExp.$2.split(/\s+/);
				ceos.push(new CodeEdit(elems[i], options, 'codeEdit_' + i));
			}
		}
		for(i in ceos) ceos[i].create();
	},

	initTextViewer: function() {
		var elems = document.getElementsByTagName('pre');
		var options = cvos = [];

		for(var i = 0; i < elems.length; i++) {
			if(elems[i].className.match(/^codeview(\s+(.+))?/i)) {
				options = RegExp.$2.split(/\s+/);
				cvos.push(new CodeView(elems[i], options, 'codeView_' + (i + 1)));
			}
		}
		for(i in cvos) cvos[i].create();
	},

	initFileManager: function(url, mode) {
		var ajaxObj = new ajax();
		url.match(/fmContainer=(\w+)/);
		var curCont = RegExp.$1;

		if(this.isResized[curCont]) return false;
		this.isResized[curCont] = true;

		if(!fmContSettings[curCont]) {
			ajaxObj.makeRequest(url + '&fmMode=getContSettings', function() {
				if(!ajaxObj.response) {
					alert('ERROR: Could not load settings!');
					return false;
				}
				fmContSettings[curCont] = eval('(' + ajaxObj.response + ')');
				fmLib.useRightClickMenu[curCont] = (fmContSettings[curCont].useRightClickMenu && !fmTools.isTouchDevice() && !OP);

				if(fmLib.useRightClickMenu[curCont]) {
					document.oncontextmenu = function(e) {
						if(!e) e = window.event;
						e.cancelBubble = true;
						if(e.stopPropagation) e.stopPropagation();
						return fmLib.browserContextMenu;
					}
				}

				if(fmContSettings[curCont].smartRefresh > 0) {
					if(fmLib.refreshIv[curCont]) clearInterval(fmLib.refreshIv[curCont]);
					fmLib.refreshIv[curCont] = setInterval(function() {
						var json = fmContSettings[curCont].listJson;
						if(!json || json.login) return false;

						ajaxObj.makeRequest(url + '&fmMode=checkUpdate', function() {
							if(ajaxObj.response != -1) {
								fmLib.call(url + '&fmMode=refresh&fmObject=' + ajaxObj.response);
							}
						});
						return true;
					}, fmContSettings[curCont].smartRefresh * 1000);
				}
				var doResize = fmLib.setFileManagerSize(curCont);

				if(!fmContSettings[curCont].userPerms) {
					ajaxObj.makeRequest(url + '&fmMode=getUserPerms', function() {
						if(!ajaxObj.response) {
							alert('ERROR: Could not load permissions!');
							return false;
						}
						fmContSettings[curCont].userPerms = eval('(' + ajaxObj.response + ')');

						if(!fmMsg[fmContSettings[curCont].language]) {
							fmMsg[fmContSettings[curCont].language] = {};

							ajaxObj.makeRequest(url + '&fmMode=getMessages', function() {
								if(!ajaxObj.response) {
									alert('ERROR: Could not load messages!');
									return false;
								}
								fmMsg[fmContSettings[curCont].language] = eval('(' + ajaxObj.response + ')');
								fmTools.touchScroll('fmExplorerText2');

								if(doResize) {
									fmTools.addListener(window, 'resize', function() {
										fmLib.isResized[curCont] = false;
										clearTimeout(fmLib.resizeTimeout);
										fmLib.resizeTimeout = setTimeout('fmLib.setFileManagerSize("' + curCont + '", true)', 250);
									});
								}
								if(mode) fmLib.call(url + '&fmMode=' + mode);
							});
						}
						else if(mode) fmLib.call(url + '&fmMode=' + mode);
					});
				}
				else if(mode) fmLib.call(url + '&fmMode=' + mode);
			});
		}
		else {
			this.setFileManagerSize(curCont);
			if(mode) this.call(url + '&fmMode=' + mode);
		}
		return true;
	},

	setFileManagerSize: function(curCont, doParse) {
		var fmCont = $$(curCont);
		var listCont = $$(curCont + 'List');
		var expCont = $$(curCont + 'Exp');
		var infoCont = $$(curCont + 'Info');
		var doResize = false;
		var width, height;

		if(typeof fmContSettings[curCont].fmWidth == 'string' && fmContSettings[curCont].fmWidth.indexOf('%') != -1) {
			width = Math.round(fmTools.getWindowWidth() * parseInt(fmContSettings[curCont].fmWidth) / 100) - 9;
			fmCont.style.width = width + 'px';
			doResize = true;
		}

		if(fmCont.style.height.indexOf('%') != -1) {
			height = Math.round(fmTools.getWindowHeight() * parseInt(fmCont.style.height) / 100) - 9;
			doResize = true;
		}
		else height = fmCont.offsetHeight - 2;

		if($$(curCont + 'Title')) height -= 25;

		if(fmContSettings[curCont].logHeight > 0) {
			height -= fmContSettings[curCont].logHeight + 1;
		}
		listCont.style.height = height + 'px';

		if(expCont) {
			expCont.style.height = height + 'px';

			if(typeof fmContSettings[curCont].explorerWidth == 'string' && fmContSettings[curCont].explorerWidth.indexOf('%') != -1) {
				expCont.style.width = Math.round(fmCont.offsetWidth * parseInt(fmContSettings[curCont].explorerWidth) / 100) + 'px';
			}
			else expCont.style.width = fmContSettings[curCont].explorerWidth + 'px';

			listCont.style.width = (fmCont.offsetWidth - expCont.offsetWidth - 2) + 'px';
		}
		else listCont.style.width = (fmCont.offsetWidth - 2) + 'px';

		if(infoCont) {
			infoCont.style.width = fmCont.offsetWidth + 'px';
		}

		if(doParse) {
			this.viewResponse(fmContSettings[curCont].listJson, curCont, null, true);
		}
		return doResize;
	},

	setContMenu: function(toggle) {
		setTimeout('fmLib.browserContextMenu=' + toggle, 100);
	},

	getExplorer: function(curCont, caption, link, folderId) {
		if(fmContSettings[curCont].expJson) {
			var html = fmParser.parseExplorer(fmContSettings[curCont].expJson, link);

			if(caption) {
				this.openDialog(null, 'fmExplorer', [caption, html]);
			}
			else {
				var obj = $$(curCont + 'Exp');
				if(obj) obj.innerHTML = html;
			}
		}
		else {
			var icon = $$(curCont + 'DirIcon' + folderId);
			if(icon) icon.src = fmWebPath + '/icn/mediaLoading.gif';

			var url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getExplorer';
			var ajaxObj = new ajax();

			ajaxObj.makeRequest(url, function() {
				if(!ajaxObj.response) {
					alert('ERROR: Could not load explorer!');
					return false;
				}
				var json = eval('(' + ajaxObj.response + ')');
				var html = fmParser.parseExplorer(json, link);

				if(caption) {
					fmLib.openDialog(null, 'fmExplorer', [caption, html]);
				}
				else {
					var obj = $$(curCont + 'Exp');
					if(obj) obj.innerHTML = html;
				}
			});
		}
	},

	toggleTreeItem: function(img) {
		var div, arr;

		if(img) {
			if(div = img.parentNode.nextSibling) {
				arr = div.id.split('|');

				if(div.style.display == 'none') {
					div.style.display = 'block';
					fmContSettings[arr[0]].expanded[arr[1]] = true;
					img.src = fmWebPath + '/icn/treeClose.gif';
					if(img.nextSibling) img.nextSibling.src = fmWebPath + '/icn/dir_open.gif';
				}
				else {
					div.style.display = 'none';
					fmContSettings[arr[0]].expanded[arr[1]] = false;
					img.src = fmWebPath + '/icn/treeOpen.gif';
					if(img.nextSibling) img.nextSibling.src = fmWebPath + '/icn/dir.gif';
				}
			}
		}
	},

	toggleListView: function(curCont) {
		var json = fmContSettings[curCont].listJson;
		if(!json) return false;

		var type = (fmContSettings[curCont].listType == 'details') ? 'icons' : 'details';
		fmContSettings[curCont].listType = type;

		fmParser.parseMain(json, $$(curCont + 'List'));
		fmParser.parseTitle(json, $$(curCont + 'Title'));
		if(this.dialog && this.opacity) this.fadeOut(this.opacity, this.dialog);
	},

	sortList: function(curCont, field, order) {
		var json = fmContSettings[curCont].listJson;
		if(!json) return false;

		this.sortJson(json, field, order);
		fmContSettings[curCont].sort.field = field;
		fmContSettings[curCont].sort.order = order;

		fmParser.parseMain(json, $$(curCont + 'List'));
	},

	sortJson: function(json, field, order) {
		if(!json.entries || !json.cont) return false;
		var items = json.entries.items;
		var i, j, cnt, swap, prefix, str1, str2, temp;

		if(!field) field = fmContSettings[json.cont].sort.field;
		if(!order) order = fmContSettings[json.cont].sort.order;

		cnt = items.length;
		swap = true;

		while(cnt && swap) {
			swap = false;

			for(i = 0; i < cnt; i++) {
				if(items[i].name != '..' && items[i].name != '') {
					for(j = i; j < cnt - 1; j++) {
						if(field == 'isDir') {
							prefix = items[j].isDir ? 'a' : 'z';
							str1 = prefix + items[j].name.toLowerCase();
							prefix = items[j + 1].isDir ? 'a' : 'z';
							str2 = prefix + items[j + 1].name.toLowerCase();
						}
						else if(field == 'size') {
							str1 = fmTools.toBytes(items[j].size);
							str2 = fmTools.toBytes(items[j + 1].size);
						}
						else {
							str1 = items[j][field].toLowerCase();
							str2 = items[j + 1][field].toLowerCase();
						}

						if((order == 'asc' && str1 > str2) || (order == 'desc' && str1 < str2)) {
							temp = items[j];
							items[j] = items[j + 1];
							items[j + 1] = temp;
							swap = true;
						}
					}
				}
			}
			cnt--;
		}
	},

	setOpacity: function(opacity, obj) {
		fmTools.setOpacity(opacity, obj);
		this.opacity = opacity;
	},

	fadeIn: function(opacity, obj) {
		if(obj) {
			if(this.fadeSpeed && opacity < 100) {
				opacity += this.fadeSpeed;
				if(opacity > 100) opacity = 100;
				this.setOpacity(opacity, obj);
				obj.style.visibility = 'visible';
				obj.style.display = 'block';
				setTimeout(this.fadeIn.bind(this, opacity, obj), 1);
			}
			else {
				this.setOpacity(100, obj);
				obj.style.visibility = 'visible';
				obj.style.display = 'block';
				this.dialog = obj;
			}
		}
	},

	fadeOut: function(opacity, obj) {
		if(obj) {
			if(this.fadeSpeed && opacity > 0) {
				opacity -= this.fadeSpeed;
				if(opacity < 0) opacity = 0;
				this.setOpacity(opacity, obj);
				setTimeout(this.fadeOut.bind(this, opacity, obj), 1);
			}
			else {
				this.setOpacity(0, obj);
				obj.style.visibility = 'hidden';
				obj.style.display = 'none';
				this.dialog = null;
				if(obj.id == 'fmMediaPlayer') this.stopPlayback();
			}
		}
	},

	setDialogLeft: function(x, obj) {
		if(!obj) obj = this.dialog;
		var width = obj.offsetWidth;
		var left = x ? x : this.mouseX - width + 13;

		if(left < 0) left = 0;
		if(x) left += fmTools.getScrollLeft();
		obj.style.left = left + 'px';
	},

	setDialogTop: function(y, obj) {
		if(!obj) obj = this.dialog;
		var hght = obj.offsetHeight;
		var top = y ? y : this.mouseY - 10;

		var winY = fmTools.getWindowHeight();
		var scrTop = fmTools.getScrollTop();

		if(y) top += scrTop;
		else if(top + hght - scrTop > winY) {
			if(hght > winY) top = 0;
			else top = winY + scrTop - hght;
		}
		obj.style.top = top + 'px';
	},

	openDialog: function(url, dialogId, text, fileId, x, y) {
		var f, e, i, obj, sid, tmp, curCont, iframeCont, dialog, perc, dir, width, height, name, perms, viewIcon;

		if(url) {
			url.match(/fmContainer=(\w+)/);
			curCont = RegExp.$1;
		}

		if(this.noMenu) {
			this.noMenu = false;
			return;
		}

		switch(dialogId) {

			case 'fmMediaPlayer':
				obj = $$('fmMediaCont');
				if(obj) {
					obj.style.width = fmContSettings[curCont].mediaPlayerWidth + 'px';
					obj.style.height = fmContSettings[curCont].mediaPlayerHeight + 'px';
				}
				viewIcon = this.setMediaPlayerIcon(url, curCont);
				obj = $$('fmMediaPlayerText');
				if(obj) obj.style.width = (fmContSettings[curCont].mediaPlayerWidth - (viewIcon ? 51 : 32)) + 'px';
				if(this.mpIv) clearInterval(this.mpIv);
				break;

			case 'fmDocViewer':
				if(typeof fileId != 'object') {
					obj = $$('fmDocViewerCont');
					if(obj) {
						if(fmEntries[curCont][fileId].dir) dir = fmEntries[curCont][fileId].dir;
						else dir = fmContSettings[curCont].listJson.path;
						url = fmContSettings[curCont].publicUrl + dir + '/' + fmEntries[curCont][fileId].name;
						var src = 'http://docs.google.com/viewer?embedded=true&url=' + escape(url);
						width = fmContSettings[curCont].docViewerWidth;
						height = fmContSettings[curCont].docViewerHeight;
						obj.innerHTML = '<iframe src="' + src + '" border="0" frameborder="0" style="width:' + width + 'px; height:' + height + 'px"></iframe>';
					}
					obj = $$('fmDocViewerText');
					if(obj) obj.style.width = (fmContSettings[curCont].docViewerWidth - 32) + 'px';
				}
				break;

			case 'fmTextViewer':
				if(typeof fileId != 'object') {
					obj = $$('fmDocViewerCont');
					if(obj) {
						width = fmContSettings[curCont].docViewerWidth;
						height = fmContSettings[curCont].docViewerHeight;
						obj.innerHTML = '<div id="fmTextViewerCont" style="width:' + width + 'px; height:' + height + 'px"></div>';
					}
					obj = $$('fmDocViewerText');
					if(obj) obj.style.width = (fmContSettings[curCont].docViewerWidth - 32) + 'px';
					dialogId = 'fmDocViewer';
					this.call(url + '&fmMode=readTextFile&fmObject=' + fileId, dialogId);
				}
				break;

			case 'fmEditor':
				if(typeof fileId != 'object') {
					obj = $$('fmEditorCont');
					if(obj) {
						obj.style.width = fmContSettings[curCont].docViewerWidth + 'px';
						obj.style.height = fmContSettings[curCont].docViewerHeight + 'px';
						obj.innerHTML = '';
					}
					obj = $$('fmEditorText');
					if(obj) obj.style.width = (fmContSettings[curCont].docViewerWidth - 54) + 'px';
					dialogId = 'fmEditor';
					this.call(url + '&fmMode=edit&fmObject=' + fileId, dialogId);
				}
				break;

			case 'fmImgViewer':
			case 'fmCoverViewer':
				if(typeof fileId != 'object') {
					obj = $$('fmDocViewerCont');
					if(obj) {
						width = parseInt(fmEntries[curCont][fileId].width);
						height = parseInt(fmEntries[curCont][fileId].height);

						if(width > fmContSettings[curCont].thumbMaxWidth) {
							perc = fmContSettings[curCont].thumbMaxWidth / width;
							width = fmContSettings[curCont].thumbMaxWidth;
							height = Math.round(height * perc);
						}

						if(height > fmContSettings[curCont].thumbMaxHeight) {
							perc = fmContSettings[curCont].thumbMaxHeight / height;
							height = fmContSettings[curCont].thumbMaxHeight;
							width = Math.round(width * perc);
						}

						if(dialogId == 'fmCoverViewer') {
							name = fmEntries[curCont][fileId].id3.Picture;
							url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getCachedImage&fmObject=' + name + '&width=' + width + '&height=' + height;
						}
						else url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getThumbnail&fmObject=' + fileId + '&hash=' + fmEntries[curCont][fileId].hash;

						obj.innerHTML = '<div class="fmThumbnail"' +
										' style="width:' + (width < 100 ? 100 : width) + 'px; height:' + (height < 50 ? 50 : height) + 'px; background-color:#FFFFFF; cursor:pointer"' +
										' onClick="fmLib.fadeOut(100, fmLib.dialog)">' +
										'<div style="height:' + (height < 50 ? 50 : height) + 'px; background:url(' + url + ') center no-repeat"></div></div>';

						obj = $$('fmDocViewerText');
						if(obj) obj.style.width = (width > 100 ? width - 20 : 80) + 'px';
					}
					dialogId = 'fmDocViewer';
				}
				break;

			default:
				if(f = document.forms[dialogId]) {
					f.reset();

					if(typeof fileId == 'number' || (typeof fileId == 'string' && fileId.indexOf(',') == -1)) {
						name = fmEntries[curCont][fileId].name;
						perms = fmEntries[curCont][fileId].permissions;
					}
					else name = perms = '';

					if(typeof fileId == 'object') fileId = fileId.join(',');
					if(fileId && f.fmObject) f.fmObject.value = fileId;
					if(name && f.fmName) f.fmName.value = name;

					switch(dialogId) {

						case 'fmNewFile':
							for(i = 1; i < 10; i++) {
								if(f['fmFile[' + i + ']']) {
									f['fmFile[' + i + ']'].style.display = 'none';
								}
							}

							if(fmContSettings[curCont] && fmContSettings[curCont].perlEnabled) {
								sid = fmContSettings[curCont].sid;
								tmp = fmContSettings[curCont].tmp;
								f.action = fmWebPath + '/cgi/upload.pl?cont=' + curCont + '&sid=' + sid + '&tmp=' + tmp;
							}
							else {
								f.action = url;
								sid = '';
							}

							f.target = 'fmFileAction';
							f.onsubmit = function() {
								if(fmLib.dialog && fmLib.opacity) fmLib.fadeOut(fmLib.opacity, fmLib.dialog);
								var fmCont = $$(curCont);
								var listLoad = fmLib.viewLoader(fmCont, url);
								fmLib.fileIv = setInterval(fmLib.checkFile.bind(fmLib, listLoad, sid, curCont), 250);
								fmContSettings[curCont].expJson = null;
							}
							break;

						case 'fmJavaUpload':
							if(frames.JUpload.document.location.href.match(/(fmCont\d+)/)) {
								iframeCont = RegExp.$1;
							}
							else iframeCont = '';

							if(iframeCont != curCont) {
								frames.JUpload.document.location.href = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=jupload';
							}
							f.action = "javascript:fmLib.call('" + url + "', '" + dialogId + "')";
							break;

						case 'fmFileDrop':
							if(!this.fdObj[curCont]) {
								fd.logging = false;
								this.fdObj[curCont] = {};
								this.fdObj[curCont].obj = new FileDrop('fdZone', {iframe: {url: fmWebPath + '/ext/filedrop/upload.php?fmContainer=' + curCont}});
								this.fdObj[curCont].obj.Multiple(true);
								this.fdObj[curCont].obj.on.iframeSetup.push(function() {
									var pbar = $$('fmFileDropProgress');
									pbar.innerHTML = '<img src="' + fmWebPath + '/icn/loading.gif" style="margin:10px"/>';
									pbar.style.display = 'block';
								});
								this.fdObj[curCont].obj.on.iframeDone.push(function(response) {
									fmLib.call(fmWebPath + '/action.php?fmContainer=' + curCont, 'fmFileDrop');
									fmLib.fdObj[curCont].uploading = false;
									var pbar = $$('fmFileDropProgress');
									pbar.innerHTML = '';
									pbar.style.display = 'none';
								});
								this.fdObj[curCont].obj.on.send.push(function(files) {
									if(!fmLib.fdObj[curCont].uploading) {
										fmLib.fdObj[curCont].uploading = true;
										var fdFileCnt = 0;
										var timeStart = Math.round(new Date().getTime() / 1000);
										var pbar = $$('fmFileDropProgress');
										var json;

										for(var i = 0; i < files.length; i++) {
											files[i].on.done.push(function() {fdFileCnt++;});
											files[i].on.progress.push(function(current, total, xhr, e, name) {
												json = {
													filename: name,
													bytesTotal: total,
													bytesCurrent: current,
													timeStart: timeStart,
													timeCurrent: Math.round(new Date().getTime() / 1000)
												};
												pbar.innerHTML = fmLib.drawProgBar(json);
												pbar.style.display = 'block';
											});
											files[i].SendTo(fmWebPath + '/ext/filedrop/upload.php?fmContainer=' + curCont);
										}

										function fdWait() {
											if(fdFileCnt >= files.length) {
												fmLib.call(fmWebPath + '/action.php?fmContainer=' + curCont, 'fmFileDrop');
												fmLib.fdObj[curCont].uploading = false;
												pbar.innerHTML = '';
												pbar.style.display = 'none';
											}
											else setTimeout(fdWait, 250);
										}
										fdWait();
									}
								});
								var lang = fmContSettings[curCont].language;
								$$('fmFileDropBoxTitle').innerHTML = fmMsg[lang].cmdDropFile;
								$$('fmFileDropBoxContent').innerHTML = fmMsg[lang].cmdBrowse;
							}
							break;

						case 'fmPerm':
							if(perms) {
								e = f.elements;
								for(i = 0; i < 9; i += 3) {
									e['fmPerms[' + i + ']'].checked = (perms[i + 1] == 'r');
									e['fmPerms[' + (i + 1) + ']'].checked = (perms[i + 2] == 'w');
									e['fmPerms[' + (i + 2) + ']'].checked = (perms[i + 3] == 'x');
								}
							}
							f.action = "javascript:fmLib.call('" + url + "', '" + dialogId + "')";
							break;

						default:
							f.action = "javascript:fmLib.call('" + url + "', '" + dialogId + "')";
					}
				}
		}

		if(text) {
			if(typeof(text) != 'object') text = [text];
			for(i = 0; i < text.length; i++) {
				obj = $$(dialogId + 'Text' + (i ? i + 1 : ''));
				if(obj) obj.innerHTML = text[i];
			}
		}
		dialog = $$(dialogId);

		if(this.dialog && this.opacity && this.dialog != dialog) {
			this.fadeOut(this.opacity, this.dialog);
		}
		this.fadeIn(0, dialog);
		this.setDialogLeft(x, dialog);
		this.setDialogTop(y, dialog);

		/* this must be executed when dialog is visible */
		if(dialogId == 'fmMediaPlayer') {
			fmParser.parseMediaPlayer({curCont:curCont,id:fileId,url:url});
		}
		else if(f && f.fmName) f.fmName.focus();
		return dialog;
	},

	viewError: function(msg, curCont) {
		var lang = fmContSettings[curCont].language;
		var caption = lang ? [fmMsg[lang].error, msg] : ['&nbsp;', msg];
		var x = Math.round((fmTools.getWindowWidth() - 400) / 2);
		var y = Math.round((fmTools.getWindowHeight() - 50) / 2);
		this.openDialog(null, 'fmError', caption, null, x, y);
	},

	viewProgress: function(msg) {
		var x = Math.round((fmTools.getWindowWidth() - 240) / 2);
		var y = Math.round((fmTools.getWindowHeight() - 200) / 2);
		this.progBar = this.openDialog(null, 'fmProgress', msg, null, x, y);
	},

	fileInfo: function(id, curCont) {
		var lang = fmContSettings[curCont].language;
		var html = fmParser.parseFileInfo(curCont, id, lang);
		this.openDialog(null, 'fmInfo', [fmMsg[lang].fileInfo, html]);
	},

	viewMenu: function(id, curCont) {
		if(this.noMenu) {
			this.noMenu = false;
			return;
		}
		var caption, html, confirm, cmd, icn, dialogId, ids;
		var lang = fmContSettings[curCont].language;
		var url = fmWebPath + '/action.php?fmContainer=' + curCont;
		var userPerms = fmContSettings[curCont].userPerms;

		var items = [];

		switch(id) {

			case 'bulkAction':
				ids = fmTools.getSelectedItems(curCont);

				if(userPerms.bulkDownload) {
					if(ids.length > 0) items.push({icon:'download.png',caption:fmMsg[lang].cmdDownload,exec:['fmLib.getCheckedFiles',curCont,ids]});
					else items.push({icon:'download_x.png',caption:fmMsg[lang].cmdDownload});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'download_x.png',caption:fmMsg[lang].cmdDownload});
				}

				if(userPerms.move) {
					if(ids.length > 0) items.push({icon:'move.png',caption:fmMsg[lang].cmdMove,exec:['fmLib.moveCheckedFiles',curCont,fmMsg[lang].cmdMove,ids]});
					else items.push({icon:'move_x.png',caption:fmMsg[lang].cmdMove});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'move_x.png',caption:fmMsg[lang].cmdMove});
				}

				if(userPerms.remove) {
					confirm = userPerms.restore ? '' : fmMsg[lang].msgDelItems;
					if(ids.length > 0) items.push({icon:'delete.png',caption:fmMsg[lang].cmdDelete,exec:['fmLib.deleteCheckedFiles',curCont, fmMsg[lang].cmdDelete,confirm,ids]});
					else items.push({icon:'delete_x.png',caption:fmMsg[lang].cmdDelete});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'delete_x.png',caption:fmMsg[lang].cmdDelete});
				}
				break;

			default:
				if(id >= 0) {
					items.push({caption:'separator',title:fmMsg[lang].fileActions + ': ' + fmEntries[curCont][id].name});

					if(fmEntries[curCont][id].deleted) {
						items.push({icon:'recycle.png',caption:fmMsg[lang].cmdRestore,call:'restore'});

						if(userPerms.remove) {
							items.push({icon:'delete.png',caption:fmMsg[lang].cmdDelete,dialog:'fmDelete',confirm:fmMsg[lang].msgDeleteFile});
						}
					}
					else {
						if(fmEntries[curCont][id].isDir) {
							items.push({icon:'folder_go.png',caption:fmMsg[lang].cmdChangeDir,call:'open'});
						}
						else {
							if(typeof fmContSettings[curCont].customAction == 'object' && fmContSettings[curCont].customAction.action) {
								items.push({icon:'cursor.png',caption:fmContSettings[curCont].customAction.caption,exec:[fmContSettings[curCont].customAction.action,curCont,id,fmEntries[curCont][id].fullName]});
							}

							if(fmEntries[curCont][id].name.match(fmParser.imageFiles)) {
								if(userPerms.imgViewer) {
									items.push({icon:'view.png',caption:fmMsg[lang].cmdView,dialog:'fmImgViewer'});
								}
								else if(!userPerms.hideDisabledIcons) {
									items.push({icon:'view_x.png',caption:fmMsg[lang].cmdView});
								}

								if(userPerms.rotate) {
									items.push({icon:'rotate_left.png',caption:fmMsg[lang].cmdRotateLeft,call:'rotateLeft'});
									items.push({icon:'rotate_right.png',caption:fmMsg[lang].cmdRotateRight,call:'rotateRight'});
								}
								else if(!userPerms.hideDisabledIcons) {
									items.push({icon:'rotate_left_x.png',caption:fmMsg[lang].cmdRotateLeft});
									items.push({icon:'rotate_right_x.png',caption:fmMsg[lang].cmdRotateRight});
								}
							}
							else if(fmEntries[curCont][id].name.match(fmParser.audioFiles) || fmEntries[curCont][id].name.match(fmParser.videoFiles)) {
								if(userPerms.mediaPlayer) {
									items.push({icon:'play.png',caption:fmMsg[lang].cmdPlay,dialog:'fmMediaPlayer'});
								}
								else if(!userPerms.hideDisabledIcons) {
									items.push({icon:'play_x.png',caption:fmMsg[lang].cmdPlay});
								}
							}
							else if(fmEntries[curCont][id].docType > 0) {
								switch(fmEntries[curCont][id].docType) {

									case 1:
										if(userPerms.docViewer) {
											items.push({icon:'view.png',caption:fmMsg[lang].cmdView,dialog:'fmTextViewer'});
										}
										else if(!userPerms.hideDisabledIcons) {
											items.push({icon:'view_x.png',caption:fmMsg[lang].cmdView});
										}

										if(userPerms.edit) {
											items.push({icon:'edit.png',caption:fmMsg[lang].cmdEdit,dialog:'fmEditor'});
										}
										else if(!userPerms.hideDisabledIcons) {
											items.push({icon:'edit_x.png',caption:fmMsg[lang].cmdEdit});
										}
										break;

									case 2:
										if(userPerms.docViewer && fmContSettings[curCont].publicUrl != '') {
											items.push({icon:'view.png',caption:fmMsg[lang].cmdView,dialog:'fmDocViewer'});
										}
										else if(!userPerms.hideDisabledIcons) {
											items.push({icon:'view_x.png',caption:fmMsg[lang].cmdView});
										}
										break;
								}
							}
						}

						if(fmEntries[curCont][id].isDir) {
							if(userPerms.bulkDownload) {
								items.push({icon:'download.png',caption:fmMsg[lang].cmdDownload,exec:['fmLib.getCheckedFiles',curCont,id]});
							}
							else if(!userPerms.hideDisabledIcons) {
								items.push({icon:'download_x.png',caption:fmMsg[lang].cmdDownload});
							}
						}
						else if(userPerms.download) {
							items.push({icon:'download.png',caption:fmMsg[lang].cmdDownload,exec:['fmLib.getFile',curCont,id]});
						}
						else if(!userPerms.hideDisabledIcons) {
							items.push({icon:'download_x.png',caption:fmMsg[lang].cmdDownload});
						}
						items.push({icon:'information.png',caption:fmMsg[lang].cmdFileInfo,exec:['fmLib.fileInfo',id,curCont]});
						items.push({caption:'separator'});

						if(userPerms.rename) {
							items.push({icon:'rename.png',caption:fmMsg[lang].cmdRename,dialog:'fmRename'});
						}
						else if(!userPerms.hideDisabledIcons) {
							items.push({icon:'rename_x.png',caption:fmMsg[lang].cmdRename});
						}

						if(userPerms.permissions) {
							items.push({icon:'permissions.png',caption:fmMsg[lang].cmdChangePerm,dialog:'fmPerm',text:[fmMsg[lang].owner,fmMsg[lang].group,fmMsg[lang].other,fmMsg[lang].read,fmMsg[lang].write,fmMsg[lang].execute]});
						}
						else if(!userPerms.hideDisabledIcons) {
							items.push({icon:'permissions_x.png',caption:fmMsg[lang].cmdChangePerm});
						}

						if(userPerms.move) {
							caption = fmMsg[lang].cmdMove + ': ' + fmEntries[curCont][id].name;
							caption = caption.replace(/\'/g, "\'");
							items.push({icon:'move.png',caption:fmMsg[lang].cmdMove,exec:['fmLib.getExplorer',curCont,caption,url + '&fmMode=move&fmObject=' + id]});
						}
						else if(!userPerms.hideDisabledIcons) {
							items.push({icon:'move_x.png',caption:fmMsg[lang].cmdMove});
						}

						if(!fmEntries[curCont][id].isDir) {
							if(userPerms.copy) {
								caption = fmMsg[lang].cmdCopy + ': ' + fmEntries[curCont][id].name;
								caption = caption.replace(/\'/g, "\'");
								items.push({icon:'copy.png',caption:fmMsg[lang].cmdCopy,exec:['fmLib.getExplorer',curCont,caption,url + '&fmMode=copy&fmObject=' + id]});
							}
							else if(!userPerms.hideDisabledIcons) {
								items.push({icon:'copy_x.png',caption:fmMsg[lang].cmdCopy});
							}
						}

						if(userPerms.remove) {
							if(userPerms.restore) {
								items.push({icon:'delete.png',caption:fmMsg[lang].cmdDelete,call:'delete'});
							}
							else {
								cmd = fmEntries[curCont][id].isDir ? fmMsg[lang].msgRemoveDir : fmMsg[lang].msgDeleteFile;
								items.push({icon:'delete.png',caption:fmMsg[lang].cmdDelete,dialog:'fmDelete',confirm:cmd});
							}
						}
						else if(!userPerms.hideDisabledIcons) {
							items.push({icon:'delete_x.png',caption:fmMsg[lang].cmdDelete});
						}
					}
					items.push({caption:'separator',title:fmMsg[lang].globalActions});
				}
				items.push({icon:'refresh.png',caption:fmMsg[lang].cmdRefresh,call:'refreshAll'});

				if(fmContSettings[curCont].listType == 'details') {
					cmd = fmMsg[lang].cmdIcons;
					icn = 'list_icons.png';
				}
				else {
					cmd = fmMsg[lang].cmdDetails;
					icn = 'list_details.png';
				}
				items.push({icon:icn,caption:cmd,exec:['fmLib.toggleListView',curCont]});

				if(userPerms.restore) {
					if(fmContSettings[curCont].viewDeleted) {
						icn = 'bin_closed.png';
						caption = fmMsg[lang].cmdHideDeleted;
					}
					else {
						icn = 'bin.png';
						caption = fmMsg[lang].cmdViewDeleted;
					}
					items.push({icon:icn,caption:caption,call:'toggleDeleted'});
				}

				if(userPerms['search']) {
					items.push({icon:'search.png',caption:fmMsg[lang].cmdSearch,dialog:'fmSearch'});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'search_x.png',caption:fmMsg[lang].cmdSearch});
				}

				if(userPerms.newDir) {
					items.push({icon:'folder_add.png',caption:fmMsg[lang].cmdNewDir,dialog:'fmNewDir'});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'folder_add_x.png',caption:fmMsg[lang].cmdNewDir});
				}

				if(userPerms.upload) {
					switch(fmContSettings[curCont].uploadEngine) {
						case 'js':dialogId = 'fmFileDrop';break;
						case 'java':dialogId = 'fmJavaUpload';break;
						default:dialogId = 'fmNewFile';
					}
					items.push({icon:'upload.png',caption:fmMsg[lang].cmdUploadFile,dialog:dialogId});
					items.push({icon:'download_url.png',caption:fmMsg[lang].cmdSaveFromUrl,dialog:'fmSaveFromUrl'});
				}
				else if(!userPerms.hideDisabledIcons) {
					items.push({icon:'upload_x.png',caption:fmMsg[lang].cmdUploadFile});
					items.push({icon:'download_url_x.png',caption:fmMsg[lang].cmdSaveFromUrl});
				}
		}
		html = fmParser.parseMenu(items, curCont, id);
		this.openDialog(null, 'fmMenu', [fmMsg[lang].cmdSelAction, html]);
	},

	newFileSelector: function(cnt) {
		var f = document.forms.fmNewFile;
		if(f && f['fmFile[' + cnt + ']']) f['fmFile[' + cnt + ']'].style.display = 'block';
	},

	deleteCheckedFiles: function(curCont, title, confirm, ids) {
		if(!ids) ids = fmTools.getSelectedItems(curCont);
		else if(typeof ids != 'object') ids = [ids];

		if(ids.length > 0) {
			var url = fmWebPath + '/action.php?fmContainer=' + curCont;
			if(confirm) this.openDialog(url, 'fmDelete', [title, confirm], ids);
			else this.call(url + '&fmMode=delete&fmObject=' + ids.join(','));
		}
	},

	getCheckedFiles: function(curCont, ids) {
		if(!ids) ids = fmTools.getSelectedItems(curCont);
		else if(typeof ids != 'object') ids = [ids];

		if(ids.length > 0) {
			var iFrame = frames.fmFileAction;
			if(iFrame) {
				var url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getFiles&fmObject=' + ids.join(',');
				iFrame.document.location.href = url;
				this.fadeOut(this.opacity, this.dialog);
			}
		}
	},

	moveCheckedFiles: function(curCont, title, ids) {
		if(!ids) ids = fmTools.getSelectedItems(curCont);
		else if(typeof ids != 'object') ids = [ids];

		if(ids.length > 0) {
			var url = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=move&fmObject=' + ids.join(',');
			this.getExplorer(curCont, title, url);
		}
	},

	playAudio: function(url, name, cover, curCont) {
		var width = fmContSettings[curCont].mediaPlayerWidth;
		var height = fmContSettings[curCont].mediaPlayerHeight;
		var forceFlash = (this.useFlash || fmContSettings[curCont].forceFlash);
		var params;

		this.stopPlayback();

		if(forceFlash || ((name.match(/\.mp3$/i) || name.match(/\.m4a$/i)) && !fmTools.supportsMP3())) {
			/* not supported MP3/M4A files via Flash player */
			if(typeof FlashReplace == 'object') {
				if(cover) $$('fmMediaCont').style.background = 'url(' + cover + ') black no-repeat center';
				else $$('fmMediaCont').style.background = 'black';
				params = {FlashVars: 'file=' + escape(url) + '&as=1', bgcolor: '#000000', wmode: 'transparent'};
				FlashReplace.replace('fmMediaCont', fmWebPath + '/ext/niftyplayer/niftyplayer.swf', 'fmFlashMovie', '165', '38', 9, params);

				if($$('fmFlashMovieCont')) {
					$$('fmFlashMovieCont').style.position = 'relative';
					$$('fmFlashMovieCont').style.top = (height - 40) + 'px';
					$$('fmFlashMovieCont').style.left = parseInt((width - 165) / 2) + 'px';
				}
			}
		}
		else {
			/* other audio files via HTML5 */
			fmAudioPlayer = new Uppod({m:'audio',uid:'fmMediaCont',lang:'en',poster:cover});
			fmAudioPlayer.Play(url);
		}
	},

	playVideo: function(url, name, curCont) {
		var forceFlash = (this.useFlash || fmContSettings[curCont].forceFlash);
		var params;

		this.stopPlayback();

		if(name.match(/\.swf$/i)) {
			/* SWF files via Flash */
			if(typeof FlashReplace == 'object') {
				params = {bgcolor: '#000000', allowFullScreen: 'true'};
				FlashReplace.replace('fmMediaCont', url, 'fmFlashMovie', '100%', '100%', 7, params);
			}
		}
		else if(forceFlash || (name.match(/\.flv$/i) || (name.match(/\.mp4$/i) && !fmTools.supportsMP4()))) {
			/* FLV files + not supported MP4 videos via Flash */
			if(typeof FlashReplace == 'object') {
				params = {FlashVars: 'fichier=' + escape(url) + '&auto_play=true', bgcolor: '#000000', allowFullScreen: 'true'};
				FlashReplace.replace('fmMediaCont', fmWebPath + '/ext/flvplayer/flvPlayer.swf', 'fmFlashMovie', '100%', '100%', 9, params);
			}
		}
		else {
			/* other video files via HTML5 */
			fmVideoPlayer = new Uppod({m:'video',uid:'fmMediaCont'});
			fmVideoPlayer.Play(url);
		}
	},

	stopPlayback: function() {
		if(fmAudioPlayer) fmAudioPlayer.Stop();
		if(fmVideoPlayer) fmVideoPlayer.Stop();

		if($$('fmMediaCont')) {
			$$('fmMediaCont').innerHTML = '';
			$$('fmMediaCont').style.background = 'black';
		}
	},

	switchMediaPlayer: function(useFlash, url) {
		this.useFlash = useFlash;
		this.stopPlayback();
		url.match(/fmObject=(\d+)/);
		this.openDialog(url, 'fmMediaPlayer', '', RegExp.$1);
	},

	setMediaPlayerIcon: function(url, curCont) {
		var icon, action, title;
		var lang = fmContSettings[curCont].language;
		var obj = $$('fmMediaPlayerSwitch');

		if(obj) {
			if(!fmContSettings[curCont].forceFlash) {
				if(this.useFlash) {
					icon = fmWebPath + '/icn/html5.png';
					action = "fmLib.switchMediaPlayer(false, '" + url + "')";
					title = fmMsg[lang]['cmdUseHtml5'];
				}
				else {
					icon = fmWebPath + '/icn/flash.png';
					action = "fmLib.switchMediaPlayer(true, '" + url + "')";
					title = fmMsg[lang]['cmdUseFlash'];
				}
				obj.innerHTML = '<img src="' + icon + '" border="0" style="cursor:pointer" title="' + title + '" onClick="' + action + '" />';
				return true;
			}
			else obj.innerHTML = '';
		}
		return false;
	}
}

fmTools.addListener(document, 'mousemove', function(e) {
	var mouseX = fmLib.mouseX;
	var mouseY = fmLib.mouseY;

	if(e && e.pageX != null) {
		fmLib.mouseX = e.pageX;
		fmLib.mouseY = e.pageY;
	}
	else if(event && event.clientX != null) {
		fmLib.mouseX = event.clientX + fmTools.getScrollLeft();
		fmLib.mouseY = event.clientY + fmTools.getScrollTop();
	}
	if(fmLib.mouseX < 0) fmLib.mouseX = 0;
	if(fmLib.mouseY < 0) fmLib.mouseY = 0;

	if(fmLib.dragging && fmLib.dialog) {
		var x = parseInt(fmLib.dialog.style.left + 0);
		var y = parseInt(fmLib.dialog.style.top + 0);
		fmLib.dialog.style.left = x + (fmLib.mouseX - mouseX) + 'px';
		fmLib.dialog.style.top = y + (fmLib.mouseY - mouseY) + 'px';
	}
});

fmTools.addListener(document, 'mousedown', function(e) {
	var firedobj = (e && e.target) ? e.target : event.srcElement;
	if(firedobj.nodeType == 3) firedobj = firedobj.parentNode;

	if(firedobj.className) {
		var isTitle = (firedobj.className.indexOf('fmDialogTitle') != -1);
		var isDialog = (firedobj.className.indexOf('fmDialog') != -1 && !isTitle);

		if(firedobj.className.indexOf('fmTH1') != -1 || isTitle) {
			fmTools.setUnselectable(firedobj);

			while(firedobj.tagName != 'HTML' && !isDialog) {
				firedobj = firedobj.parentNode;
				isTitle = (firedobj.className.indexOf('fmDialogTitle') != -1);
				isDialog = (firedobj.className.indexOf('fmDialog') != -1 && !isTitle);
			}

			if(firedobj.className.indexOf('fmDialog') != -1) {
				fmLib.dialog = firedobj;
				fmLib.dragging = true;
				fmLib.setOpacity(50, fmLib.dialog);
			}
		}
	}
});

fmTools.addListener(document, 'mouseup', function() {
	fmLib.dragging = false;
	fmLib.setOpacity(100, fmLib.dialog);
});
