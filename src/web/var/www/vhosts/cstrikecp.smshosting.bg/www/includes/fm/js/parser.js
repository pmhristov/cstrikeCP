/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

var fmParser = {

	imageFiles: /\.(jpe?g|png|gif)$/i,
	audioFiles: /\.(mp3|m4a|aac|wav|oga)$/i,
	videoFiles: /\.(swf|flv|mp4|webm|ogv)$/i,
	mediaCnt: 0,

	parseTitle: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');

		var html, title, icon, dialogId;
		var lang = fmContSettings[json.cont].language;
		var userPerms = fmContSettings[json.cont].userPerms;
		var url = fmWebPath + '/action.php?fmContainer=' + json.cont;
		var width = $$(json.cont).offsetWidth - 162;
		var cntFiles = this._getFileCount(json.cont);

		if(typeof json.title != 'undefined' && json.title != '') {
			title = json.title;
			
			if(typeof json.path != 'undefined') {
				title += ' ' + json.path + ' (' + cntFiles + ')';
			}
		}
		else if(typeof json.search != 'undefined' && json.search != '') {
			title = fmMsg[lang].searchResult + ': ' + json.search;
		}
		else if(typeof json.sysType != 'undefined' && json.sysType != '') {
			title = '[' + json.sysType + '] ' + json.path + ' (' + cntFiles + ')';
		}
		else title = json.path + ' (' + cntFiles + ')';

		var style = 'padding:4px; text-align:left; white-space:nowrap';
		html = '<div class="fmTH1" style="float:left; width:' + width + 'px; overflow:hidden; ' + style + '" title="' + title + '">' + title + '</div>\n';
		
		if(!json.login) {
			html += '<div class="fmTH1" style="float:right; ' + style + '">\n';
			html += '<img src="' + fmWebPath + '/icn/refresh.png" title="' + fmMsg[lang].cmdRefresh + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.call(\'' + url + '&fmMode=refreshAll\')" />\n';

			if(fmContSettings[json.cont].listType == 'details') {
				title = fmMsg[lang].cmdIcons;
				icon = 'list_icons.png';
			}
			else {
				title = fmMsg[lang].cmdDetails;
				icon = 'list_details.png';
			}
			html += '<img src="' + fmWebPath + '/icn/' + icon + '" title="' + title + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.toggleListView(\'' + json.cont + '\')" />\n';

			if(userPerms.restore) {
				if(fmContSettings[json.cont].viewDeleted) {
					title = fmMsg[lang].cmdHideDeleted;
					icon = 'bin_closed.png';
				}
				else {
					title = fmMsg[lang].cmdViewDeleted;
					icon = 'bin.png';
				}
				html += '<img src="' + fmWebPath + '/icn/' + icon + '" title="' + title + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.call(\'' + url + '&fmMode=toggleDeleted\')" />\n';
			}

			if(userPerms['search']) {
				html += '<img src="' + fmWebPath + '/icn/search.png" title="' + fmMsg[lang].cmdSearch + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.openDialog(\'' + url + '\', \'fmSearch\', \'' + fmMsg[lang].cmdSearch + '\')" />\n';
			}

			if(userPerms.newDir) {
				html += '<img src="' + fmWebPath + '/icn/folder_add.png" title="' + fmMsg[lang].cmdNewDir + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.openDialog(\'' + url + '\', \'fmNewDir\', \'' + fmMsg[lang].cmdNewDir + '\')" />\n';
			}

			if(userPerms.upload) {
				switch(fmContSettings[json.cont].uploadEngine) {
					case 'js':dialogId = 'fmFileDrop';break;
					case 'java':dialogId = 'fmJavaUpload';break;
					default:dialogId = 'fmNewFile';
				}
				html += '<img src="' + fmWebPath + '/icn/upload.png" title="' + fmMsg[lang].cmdUploadFile + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.openDialog(\'' + url + '\', \'' + dialogId + '\', \'' + fmMsg[lang].cmdUploadFile + '\')" />\n';
				html += '<img src="' + fmWebPath + '/icn/download_url.png" title="' + fmMsg[lang].cmdSaveFromUrl + '" style="margin-left:2px; cursor:pointer" onClick="fmLib.openDialog(\'' + url + '\', \'fmSaveFromUrl\', \'' + fmMsg[lang].cmdSaveFromUrl + '\')" />\n';
			}
			html += '</div>\n';
		}
		contObj.innerHTML = html;
		return true;
	},

	parseMain: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');

		var clickArea = contObj;
		var html, id;

		if(json.login) {
			html = this.parseLogin(json);
		}
		else if(json.entries) {
			var now = new Date();
			var d = [];
			d[0] = now.getFullYear();
			d[1] = now.getMonth() + 1;
			if(d[1] < 10) d[1] = '0' + d[1];
			d[2] = now.getDate();
			if(d[2] < 10) d[2] = '0' + d[2];

			switch(fmContSettings[json.cont].listType) {
				case 'details': html = this.parseEntriesDetailView(json, d.join('-')); break;
				case 'icons': html = this.parseEntriesIconView(json, d.join('-')); break;
				default: html = 'Unknown list type';
			}
		}
		if(html) contObj.innerHTML = html;

		if(fmContSettings[json.cont].listType == 'details') {
			id = json.cont + '_detBody';

			try {
				var cHead = $$(json.cont + '_detHead');
				var cBody = $$(json.cont + '_detBodyTable');
				var hDivs = cHead.getElementsByTagName('div');
				var bTr = cBody.getElementsByTagName('tr')[0];
				var bTds = bTr.getElementsByTagName('td');

				for(var i = 0; i < hDivs.length; i++) {
					hDivs[i].style.width = bTds[i].offsetWidth + 'px';
				}
				clickArea = cBody;
			}
			catch(e) {}
		}
		else {
			id = json.cont + 'Entries';
			clickArea = contObj.childNodes[0];
		}

		if(!json.login) {
			fmTools.touchScroll(id);

			fmTools.addListener(clickArea, 'mouseup', function(e) {
				if($$('fmMenu').style.visibility != 'visible') {
					var target;
					if(!e) e = event;
					if(e.target) target = e.target;
					else target = e.srcElement;

					if(target.type != 'checkbox') {
						if(fmLib.useRightClickMenu[json.cont] && e.button == 2) {
							fmLib.viewMenu(-1, json.cont);
						}
						else if(!fmLib.useRightClickMenu[json.cont] && e.button != 2) {
							fmLib.viewMenu(-1, json.cont);
						}
					}
				}
				return true;
			});
		}
		return true;
	},

	parseEntriesDetailView: function(json, date) {
		if(typeof json != 'object') return '';
		if(!json.entries) return '';

		var html, i, j, action, cssRow, cssData, style, content, tooltip, img, order, caption;
		var entries = json.entries;
		var userPerms = fmContSettings[json.cont].userPerms;
		var lang = fmContSettings[json.cont].language;
		var cw = [];
		var cwLastCol = (userPerms.remove || userPerms.bulkDownload || !userPerms.hideDisabledIcons) ? 20 : 0;
		var listCont = $$(json.cont + 'List');
		var twidth = listCont.offsetWidth - 16;
		var theight = listCont.offsetHeight;

		for(i in entries.captions) {
			switch(entries.captions[i]) {
				case 'isDir':
					cw[i] = 30;
					break;
				case 'name':
					if(entries.captions.length > 2) {
						cw[i] = Math.floor((twidth - cwLastCol) / entries.captions.length * 1.8);
					}
					else cw[i] = twidth - cwLastCol - 35;
					break;
				case 'size':
					if(entries.captions.length > 3) {
						cw[i] = Math.floor((twidth - cwLastCol) / entries.captions.length * 0.6);
					}
					break;
				case 'changed':
					if(entries.captions.length > 3) {
						cw[i] = Math.floor((twidth - cwLastCol) / entries.captions.length * 1.3);
					}
					break;
			}
		}
		var cwOther = Math.floor((twidth - cwLastCol - 30 - fmTools.arraySum(cw)) / (entries.captions.length - cw.length));

		cssRow = (json.search != '') ? 'fmSearchResult' : 'fmTD1';
		html = '<div id="' + json.cont + '_detHead" class="fmTH2" style="height:20px">\n';

		for(i in entries.captions) {
			if(!cw[i]) cw[i] = cwOther;
			if(entries.captions[i] == 'isDir') tooltip = caption = '';
			else tooltip = caption = fmMsg[lang][entries.captions[i]];
			if(tooltip != '') tooltip += ': ';

			if(entries.captions[i] == fmContSettings[json.cont].sort.field) {
				if(fmContSettings[json.cont].sort.order == 'asc') {
					img = 'sort_asc.gif';
					order = 'desc';
					tooltip += fmMsg[lang].cmdSortDesc;
				}
				else {
					img = 'sort_desc.gif';
					order = 'asc';
					tooltip += fmMsg[lang].cmdSortAsc;
				}
			}
			else {
				img = '';
				order = 'asc';
				tooltip += fmMsg[lang].cmdSortAsc;
			}
			action = "fmLib.sortList('" + json.cont + "', '" + entries.captions[i] + "', '" + order + "')";
			style = 'float:left; overflow:hidden; width:' + cw[i] + 'px; height:20px';
			html += '<div class="fmTH3" style="' + style + '" title="' + tooltip + '" onMouseOver="this.className=\'fmTH4\'" onMouseOut="this.className=\'fmTH3\'" onMouseDown="this.className=\'fmTH5\'" onMouseUp="this.className=\'fmTH4\'" onClick="' + action + '">\n';
			if(img != '') html += '<img src="' + fmWebPath + '/icn/' + img + '" />';
			if(caption != '') html += ((img != '') ? '&nbsp;' : '') + caption;
			html += '</div>';
		}

		if(cwLastCol > 0) {
			action = this.parseAction(entries.lastCol, json.cont);
			html += '<div class="fmTH2" style="float:left; width:' + cwLastCol + 'px; height:20px; padding:2px; text-align:center">';
			html += '<img src="' + fmWebPath + '/icn/' + entries.lastCol.icon + '" style="' + entries.lastCol.style + '" title="' + entries.lastCol.tooltip + '" onClick="' + action + '" />';
			html += '</div>\n';
		}
		html += '</div>\n';
		html += '<div id="' + json.cont + '_detBody" style="height:' + (theight - 20) + 'px; overflow:auto">\n';
		html += '<table id="' + json.cont + '_detBodyTable" border="0" cellspacing="1" cellpadding="0" width="100%" class="fmTH2">\n';

		for(i in entries.items) {
			fmEntries[json.cont][entries.items[i].id] = entries.items[i];
			action = this._checkAction(entries.items[i], json);

			if(entries.items[i].deleted) {
				cssData = 'fmContentDisabled';
			}
			else if(fmContSettings[json.cont].markNew && entries.items[i].changed.substr(0, 10) == date) {
				cssData = 'fmContentNew';
			}
			else cssData = 'fmContent';

			html += '<tr class="' + cssRow + '" style="height:24px" onMouseOver="this.className=\'fmTD2\'" onMouseOut="this.className=\'' + cssRow + '\'">\n';

			for(j in entries.captions) {
				switch(entries.captions[j]) {
					case 'isDir':
						content = '<img src="' + fmWebPath + '/icn/small/' + entries.items[i].icon + '" />';
						style = 'text-align:center';
						tooltip = '';
						break;
					case 'name':
						content = tooltip = entries.items[i].name;
						style = 'text-align:left';
						break;
					case 'size':
						content = tooltip = entries.items[i].size;
						style = 'text-align:right';
						break;
					default:
						content = tooltip = entries.items[i][entries.captions[j]];
						style = 'text-align:center';
				}
				html += '<td class="' + cssData + '" style="cursor:pointer" title="' + tooltip + '" " onMouseDown="' + action + '" align="left">';
				html += '<div class="' + cssData + '" style="width:' + cw[j] + 'px; overflow:hidden; padding:2px; white-space:nowrap; ' + style + '">' + content + '</div>';
				html += '</td>\n';
			}

			if(cwLastCol > 0) {
				html += '<td width="' + cwLastCol + '" class="' + cssData + '" align="center">';
				html += '<input type="checkbox" style="margin:0" value="' + entries.items[i].id + '"';
				if(entries.items[i].id == '') html += ' onClick="fmTools.selectAll(this, \'' + json.cont + '\')" />';
				else if(entries.items[i].deleted) html += ' disabled="disabled" />';
				else html += ' />';
				html += '</td>\n';
			}
			html += '</tr>\n';
		}
		html += '</table>\n';
		html += '</div>\n';
		return html;
	},

	parseEntriesIconView: function(json, date) {
		if(typeof json != 'object') return '';
		if(!json.entries) return '';

		var html, i, action, cssRow, cssData, cssIcon, thumbWidth, thumbHeight, perc, name, img, cellsPerRow, cellWidth;
		var url = fmWebPath + '/action.php?fmContainer=' + json.cont;
		var entries = json.entries;
		var cellCnt = 0;
		var listWidth = $$(json.cont + 'List').offsetWidth;

		cellsPerRow = listWidth / 130;
		cellWidth = Math.ceil(listWidth / cellsPerRow) - 14;
		cellsPerRow = Math.floor(cellsPerRow);
		cssRow = (json.search != '') ? 'fmSearchResult' : 'fmTD1';

		html = '<table border="0" cellspacing="2" cellpadding="5" width="100%" class="fmTH2">\n';
		html += '<colgroup>\n';

		for(i = 0; i < cellsPerRow; i++) {
			html += '<col width="' + cellWidth + 'px"/>\n';
		}
		html += '</colgroup>\n';
		html += '<tr align="center" valign="top">\n';

		for(i in entries.items) {
			fmEntries[json.cont][entries.items[i].id] = entries.items[i];
			action = this._checkAction(entries.items[i], json);

			if(entries.items[i].deleted) {
				cssData = 'fmContentDisabled';
			}
			else if(fmContSettings[json.cont].markNew && entries.items[i].changed.substr(0, 10) == date) {
				cssData = 'fmContentNew';
			}
			else cssData = 'fmContent';

			if(cellCnt >= cellsPerRow) {
				html += '</tr>\n<tr align="center" valign="top">\n';
				cellCnt = 0;
			}
			cssIcon = (entries.items[i].type == 'image' || entries.items[i].id3.Picture) ? 'fmThumbnail' : '';

			html += '<td class="' + cssRow + '" style="cursor:pointer" onMouseDown="' + action + '" onMouseOver="this.className=\'fmTD2\'" onMouseOut="this.className=\'' + cssRow + '\'">\n';
    		html += '<table border="0" cellspacing="0" cellpadding="0" width="100%">\n';
			html += '<tr>\n';
 			html += '<td align="center" class="' + cssIcon + '" style="height:54px">';

			if(entries.items[i].id3.Picture) {
				thumbWidth = cellWidth;
				thumbHeight = 50;
				name = entries.items[i].id3.Picture.split(':')[0];
				img = url + '&fmMode=getCachedImage&fmObject=' + name + '&width=' + thumbWidth + '&height=' + thumbHeight;
			}
 			else if(entries.items[i].type == 'image') {
 				thumbWidth = entries.items[i].width;
				thumbHeight = entries.items[i].height;

 				if(thumbWidth > cellWidth) {
					perc = cellWidth / thumbWidth;
					thumbWidth = cellWidth;
					thumbHeight = Math.round(thumbHeight * perc);
				}

				if(thumbHeight > 50) {
					perc = 50 / thumbHeight;
					thumbWidth = Math.round(thumbWidth * perc);
					thumbHeight = Math.round(thumbHeight * perc);
				}
				img = url + '&fmMode=getThumbnail&fmObject=' + entries.items[i].id + '&width=' + thumbWidth + '&height=' + thumbHeight + '&hash=' + entries.items[i].hash;
			}
 			else {
				img = fmWebPath + '/icn/big/' + entries.items[i].icon;
				thumbHeight = 50;
			}
			html += '<div style="height:' + thumbHeight + 'px; background:url(' + img + ') center no-repeat"></div>';
 			html += '</td>\n';
 			html += '</tr><tr>\n';
 			html += '<td align="center" style="height:20px">\n';
 			html += '<div class="' + cssData + '" style="overflow:hidden; white-space:nowrap; vertical-align:middle; width:' + (cellWidth - 2) + 'px" title="' + entries.items[i].name + '">' + entries.items[i].name + '</div>\n';
 			html += '</td>\n';
			html += '</tr></table>\n';
 			html += '</td>\n';
			cellCnt++;
		}

		while(cellCnt < cellsPerRow) {
			html += '<td class="' + cssRow + '">&nbsp;</td>\n';
			cellCnt++;
		}
		html += '</tr></table>\n';
		return html;
	},

	parseExplorer: function(json, link) {
		if(typeof json != 'object') return '';
		if(!json.explorer) return '';

		var url, action, icon, icon2, style, hash, caption, i, j, cls, isCurDir, cntFiles;
		var html = '';
		var explorer = json.explorer;
		fmContSettings[json.cont].expJson = json;

		if(typeof fmContSettings[json.cont].expanded != 'object') {
			fmContSettings[json.cont].expanded = {};

			for(i = 0; i < explorer.items.length; i++) {
				hash = explorer.items[i].hash;
				if(explorer.expandAll || explorer.items[i].level == 1) {
					fmContSettings[json.cont].expanded[hash] = true;
				}
			}
		}

		for(i = 0; i < explorer.items.length; i++) {
			if(explorer.items[i].deleted) continue;
			if(link) url = link + '&fmName=' + explorer.items[i].id;
			else url = fmWebPath + '/action.php?fmContainer=' + json.cont + '&fmMode=expOpen&fmObject=' + explorer.items[i].id;

			if(explorer.items[i-1] && explorer.items[i-1].level < explorer.items[i].level) {
				hash = explorer.items[i-1].hash;

				if(!fmContSettings[json.cont].expanded[hash]) {
					html += '<div id="' + json.cont + '|' + hash + '" style="display:none">';
				}
				else html += '<div id="' + json.cont + '|' + hash + '">';
			}
			isCurDir = (explorer.items[i].path == fmContSettings[json.cont].listJson.path);
			cls = isCurDir ? 'fmExplorerActive' : 'fmExplorer';
			html += '<div class="' + cls + '" onMouseOver="this.className=\'fmExplorerHilight\'" onMouseOut="this.className=\'' + cls + '\'">';

			if(explorer.items[i].level > 1) for(j = 1; j < explorer.items[i].level; j++) {
				html += '<img src="' + fmWebPath + '/icn/blank.gif" width="8" height="1" />';
			}

			if(explorer.items[i+1] && explorer.items[i+1].level > explorer.items[i].level) {
				hash = explorer.items[i].hash;
				if(fmContSettings[json.cont].expanded[hash]) {
					icon = 'treeClose.gif';
					icon2 = 'dir_open.gif';
				}
				else {
					icon = 'treeOpen.gif';
					icon2 = 'dir.gif';
				}
				action = 'fmLib.toggleTreeItem(this)';
				style = 'cursor:pointer';
			}
			else {
				icon = 'blank.gif';
				icon2 = 'dir.gif';
				action = style = '';
			}

			if(typeof fmContSettings[json.cont].listJson.search != 'undefined') {
				if(isCurDir && fmContSettings[json.cont].listJson.search == '') {
					cntFiles = this._getFileCount(json.cont);

					if(cntFiles != explorer.items[i].files) {
						explorer.items[i].files = cntFiles;
						/* ugly workaround: if number of files doesn't match, reload */
						fmLib.call(fmWebPath + '/action.php?fmContainer=' + json.cont + '&fmMode=refreshAll');
					}
				}
			}
			caption = explorer.items[i].name + ' (' + explorer.items[i].files + ')';
			html += '<img src="' + fmWebPath + '/icn/' + icon + '" onClick="' + action + '" style="' + style + '" />';
			html += '<img src="' + fmWebPath + '/icn/' + icon2 + '" id="' + (!link ? json.cont + 'DirIcon' + i : '') + '" hspace="4" onClick="fmLib.call(\'' + url + '\')" style="cursor:pointer" />';
			html += '<span class="fmExplorerContent" onClick="fmLib.call(\'' + url + '\')" style="cursor:pointer" title="' + caption + '">' + explorer.items[i].name;
			html += (fmContSettings[json.cont].hideFileCnt ? '</span>' : ' <span class="fmExplorerFileCnt">(' + explorer.items[i].files + ')</span></span>');
			html += '</div>';

			if(explorer.items[i+1] && explorer.items[i+1].level < explorer.items[i].level) {
				for(j = 0; j < explorer.items[i].level - explorer.items[i+1].level; j++) {
					html += '</div>';
				}
			}
		}
		fmTools.touchScroll(json.cont + 'Exp');
		return html;
	},

	parseEditor: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');

		var btn = $$('fmEditorButton');
		var lang = fmContSettings[json.cont].language;

		if(btn && btn.innerHTML == '') {
			btn.innerHTML = '<img src="' + fmWebPath + '/icn/save.png" style="cursor:pointer" title="' + fmMsg[lang].cmdSave + '" onClick="fmLib.callOK(\'' + fmMsg[lang].msgSaveFile + '\', \'\', \'frmEdit\')" />';
		}

		var html;
		var url = fmWebPath + '/action.php?fmContainer=' + json.cont;
		var width = fmContSettings[json.cont].docViewerWidth;
		var height = fmContSettings[json.cont].docViewerHeight;

		html = '<form name="frmEdit" class="fmForm" action="javascript:fmLib.call(\'' + url + '\', \'frmEdit\')" method="post">\n';
    	html += '<input type="hidden" name="fmMode" value="edit" />\n';
    	html += '<input type="hidden" name="fmObject" value="' + json.id + '" />\n';
		html += '<textarea name="fmText" style="width:' + width + 'px; height:' + height + 'px; margin:0px" ';
		html += 'class="codeedit ' + json.text.lang + ' lineNumbers focus" wrap="off">' + json.text.content + '</textarea>\n';
		html += '</form>\n';

		contObj.innerHTML = html;
		return true;
	},

	parseTextViewer: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');

		var html;
		var width = fmContSettings[json.cont].docViewerWidth;
		var height = fmContSettings[json.cont].docViewerHeight;

		html = '<pre style="width:' + width + 'px; height:' + height + 'px; margin:0px; visibility:hidden" ';
		html += 'class="codeview ' + json.text.lang + ' lineNumbers">' + json.text.content + '</pre>\n';

		contObj.innerHTML = html;
		return true;
	},

	parseLogin: function(json) {
		if(typeof json != 'object') return '';
		if(!json.login) return '';

		var html;
		var action = this.parseAction(json.login, json.cont);
		var lang = fmContSettings[json.cont].language;

		html =  '<div class="fmTH3" style="height:100%; padding:4px">\n';
		html += '<div style="position:relative; width:180px; top:50%; left:50%; margin-top:-30px; margin-left:-90px; text-align:center">\n';
		html += '<form name="' + json.cont + 'Login" action="javascript:' + action + '" class="fmForm" method="post">\n';
		html += '<input type="hidden" name="fmMode" value="login" />\n';
		html += '<input type="password" name="fmName" size="20" maxlength="60" class="fmField" /><br />\n';
		html += '<input type="checkbox" name="fmRememberPwd" value="1" />' + fmMsg[lang].rememberPwd + '<br />\n';
		html += '<input type="submit" class="fmButton" value="' + fmMsg[lang].cmdLogin + '" />\n';
		html += '</form>\n';
		html += '</div>\n';
		html += '</div>\n';
		return html;
	},

	parseDebugInfo: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');
		if(!json.debug) return false;

		var html;

		html =  '<table border="0" cellspacing="0" cellpadding="2">\n';
		html += '<tr valign="top">\n';
		html += '<td>Session cookie:</td><td>' + json.debug.cookie + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>PHP version:</td><td>' + json.debug.phpVersion + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>Perl version:</td><td>' + json.debug.perlVersion + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>Memory limit:</td><td>' + json.debug.memoryLimit + '</td>\n';
		html += '</tr><tr valign="top">\n';

		if(json.debug.memoryUsage) {
			html += '<td>Memory usage:</td><td>' + json.debug.memoryUsage + '</td>\n';
			html += '</tr><tr valign="top">\n';
		}
		html += '<td>FileManager::$language:</td><td>' + json.debug.lang + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$locale:</td><td>' + json.debug.locale + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$encoding:</td><td>' + json.debug.encoding + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$uploadEngine:</td><td>' + json.debug.uploadEngine + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$perlEnabled:</td><td>' + json.debug.perlEnabled + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$fmWebPath:</td><td>' + json.debug.webPath + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$rootDir:</td><td>' + json.debug.rootDir + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$maxImageWidth:</td><td>' + json.debug.maxImageWidth + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$maxImageHeight:</td><td>' + json.debug.maxImageHeight + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$forceFlash:</td><td>' + json.debug.forceFlash + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>FileManager::$smartRefresh:</td><td>' + json.debug.refresh + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>Listing::$curDir:</td><td>' + json.debug.curDir + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>Listing::$searchString:</td><td>' + json.debug.search + '</td>\n';
		html += '</tr><tr valign="top">\n';
		html += '<td>Cache directory:</td><td>' + json.debug.cache + ' files</td>\n';
		html += '</tr></table>\n';

		contObj.innerHTML = html;
		return true;
	},

	parseLogMessages: function(json, contObj) {
		if(typeof contObj != 'object') return false;
		if(typeof json != 'object') json = eval('(' + json + ')');
		if(!json.messages) return false;

		var cssLog, i, msg;
		var html = '';

		for(i = 0; i < json.messages.length; i++) {
			msg = json.messages[i];
			cssLog = 'fmLog' + msg.type.substr(0, 1).toUpperCase() + msg.type.substr(1, msg.type.length);
			html += '<table border="0" cellspacing="0" cellpadding="0"><tr valign="top">';
			html += '<td class="' + cssLog + '" style="padding-right:10px; white-space:nowrap">' + msg.time + '</td>';
			html += '<td class="' + cssLog + '">' + msg.text + '</td>';
			html += '</tr></table>\n';
		}
		contObj.innerHTML += html;
		contObj.scrollTop = contObj.scrollHeight;
		fmTools.touchScroll(contObj);
		return true;
	},

	parseMediaPlayer: function(json) {
		if(typeof json != 'object') json = eval('(' + json + ')');

		var name = fmEntries[json.curCont][json.id].name;
		var width, height, maxWidth, img, cover;

		if(name.match(this.audioFiles)) {
			img = fmEntries[json.curCont][json.id].id3.Picture;

			if(img && fmContSettings[json.curCont].mediaPlayerHeight >= 100) {
				width = parseInt(fmEntries[json.curCont][json.id].width);
				height = parseInt(fmEntries[json.curCont][json.id].height);
				maxWidth = fmContSettings[json.curCont].mediaPlayerWidth;

				if(width > maxWidth) {
					width = maxWidth;
					height = Math.round(height * (maxWidth / width));
				}
				cover = fmWebPath + '/action.php?fmContainer=' + json.curCont + '&fmMode=getCachedImage&fmObject=' + img + '&width=' + width + '&height=' + height;
			}
			else cover = '';

			fmLib.playAudio(json.url, name, cover, json.curCont);

			fmLib.mpIv = setInterval(function() {
				if($$('fmMediaPlayerText')) {
					var mId3 = fmEntries[json.curCont][json.id].id3;
					var content = $$('fmMediaPlayerText').innerHTML;
					var txt = 'n/a';

					switch(content.substring(0, content.indexOf(':'))) {
						case 'File':
							if(mId3.Title) txt = mId3.Title;
							$$('fmMediaPlayerText').innerHTML = 'Title: ' + txt;
							break;

						case 'Title':
							if(mId3.Artist) txt = mId3.Artist;
							$$('fmMediaPlayerText').innerHTML = 'Artist: ' + txt;
							break;

						case 'Artist':
							if(mId3.Album) txt = mId3.Album;
							$$('fmMediaPlayerText').innerHTML = 'Album: ' + txt;
							break;

						case 'Album':
							if(mId3.Year) txt = mId3.Year;
							$$('fmMediaPlayerText').innerHTML = 'Year: ' + txt;
							break;

						default:
							$$('fmMediaPlayerText').innerHTML = 'File: ' + name;
					}
				}
				else clearInterval(fmLib.mpIv);
			}, 3000);
		}
		else if(name.match(this.videoFiles)) {
			fmLib.playVideo(json.url, name, json.curCont);
		}
		return true;
	},

	parseFileInfo: function(curCont, id, lang) {
		var icon = css = style = name = tags = id3Picture = action = '';
		var thumbWidth = thumbHeight = 0;
		var id3, key;
		var size = fmEntries[curCont][id].size;

		if(fmEntries[curCont][id].type == 'image' && fmEntries[curCont][id].width) {
			size += ' (' + fmEntries[curCont][id].width + ' &times; ' + fmEntries[curCont][id].height + ')';
		}

		var html = '<table border="0" cellspacing="0" cellpadding="1" width="100%"><tr align="left" valign="top">' +
			'<td class="fmContent"><b>' + fmMsg[lang].name + ':</b></td><td class="fmContent">' + fmEntries[curCont][id].fullName + '</td>' +
			'</tr><tr align="left">' +
			'<td class="fmContent"><b>' + fmMsg[lang].permissions + ':</b></td><td class="fmContent">' + fmEntries[curCont][id].permissions + '</td>' +
			'</tr><tr align="left">' +
			'<td class="fmContent"><b>' + fmMsg[lang].owner + ':</b></td><td class="fmContent">' + fmEntries[curCont][id].owner + '</td>' +
			'</tr><tr align="left">' +
			'<td class="fmContent"><b>' + fmMsg[lang].group + ':</b></td><td class="fmContent">' + fmEntries[curCont][id].group + '</td>' +
			'</tr><tr align="left">' +
			'<td class="fmContent"><b>' + fmMsg[lang].changed + ':</b></td><td class="fmContent" nowrap="nowrap">' + fmEntries[curCont][id].changed + '</td>' +
			'</tr><tr align="left">' +
			'<td class="fmContent"><b>' + fmMsg[lang].size + ':</b></td><td class="fmContent" nowrap="nowrap">' + size + '</td>';

		if(typeof fmEntries[curCont][id].id3 == 'object') {
			id3 = fmEntries[curCont][id].id3;

			for(key in id3) {
				if(key == 'Picture') {
					id3Picture = id3[key];
				}
				else if(id3[key] != '') {
					tags += '</tr><tr align="left">';
					tags +=	'<td class="fmContent"><b>' + key + ':</b></td><td class="fmContent" nowrap="nowrap">' + id3[key] + '</td>';
				}
			}

			if(tags) {
				html += '</tr><tr align="left">';
				html += '<td class="fmContent" colspan="2"><div style="height:0px; margin:4px 0px; border:1px inset #FFFFFF"></div></td>';
				html += tags;
			}
		}

		if(fmEntries[curCont][id].type == 'image' || id3Picture) {
			if(id3Picture) {
				icon = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getCachedImage&fmObject=' + id3Picture + '&width=100&height=100';
				css = 'fmThumbnail';
				style = 'width:102px; height:102px; background-color:#FFFFFF; cursor:pointer';
				action = this.parseAction({caption:fmMsg[lang].cmdView,dialog:'fmCoverViewer',content:id3Picture}, curCont, id);
			}
			else {
				thumbWidth = fmEntries[curCont][id].width;
				thumbHeight = fmEntries[curCont][id].height;

				if(thumbWidth > 100) {
					thumbHeight = Math.round(thumbHeight * 100 / thumbWidth);
					thumbWidth = 100;
				}

				if(thumbHeight > 100) {
					thumbWidth = Math.round(thumbWidth * 100 / thumbHeight);
					thumbHeight = 100;
				}
				icon = fmWebPath + '/action.php?fmContainer=' + curCont + '&fmMode=getThumbnail&fmObject=' + id + '&width=' + thumbWidth + '&height=' + thumbHeight + '&hash=' + fmEntries[curCont][id].hash;
				css = 'fmThumbnail';
				style = 'width:' + thumbWidth + 'px; height:' + thumbHeight + 'px; background-color:#FFFFFF; cursor:pointer';
				action = this.parseAction({caption:fmMsg[lang].cmdView,dialog:'fmImgViewer'}, curCont, id);
			}
		}
		else {
			icon = fmWebPath + '/icn/big/' + fmEntries[curCont][id].icon;
			width = height = 32;
		}
		html += '</tr><tr align="left"><td colspan="2" height="8"></td></tr><tr>' +
				'<td colspan="2" align="center">' +
				'<div class="' + css + '" style="' + style + '" onClick="' + action + '">' +
				'<img src="' + icon + '"/></div></td>';
		html += '</tr></table>';
		return html;
	},

	parseAction: function(json, curCont, id) {
		if(typeof json != 'object') return '';

		var url = fmWebPath + '/action.php?fmContainer=' + curCont;
		var i, params, caption;
		var action = name = '';

		if(json.call) {
			action = "fmLib.call('" + url + '&fmMode=' + json.call;
			if(id >= 0) action += '&fmObject=' + id;
			action += "')";
		}
		else if(json.submit) {
			action = "fmLib.call('" + url + "', '" + json.submit + "')";
		}
		else if(json.exec) {
			for(i = 1, params = []; i < json.exec.length; i++) {
				if(typeof json.exec[i] == 'object') {
					params.push("['" + json.exec[i].join("','") + "']");
				}
				else if(typeof json.exec[i] == 'string') {
					params.push("'" + json.exec[i].replace(/\'/g, "\\'") + "'");
				}
			}
			action = json.exec[0] + '(' + params.join(',') + ')';
		}
		else if(json.dialog) {
			if(json.dialog == 'fmError') {
				for(i in json.caption) json.caption[i] = json.caption[i].replace(/\'/g, "\\'");
				action = "fmLib.openDialog('', 'fmError', ['" + json.caption[0] + "', '" + json.caption[1] + "'])";
			}
			else if(fmTools.inArray(json.dialog, ['fmSearch', 'fmNewDir', 'fmSaveFromUrl', 'fmFileDrop', 'fmJavaUpload', 'fmNewFile'])) {
				caption = json.caption.replace(/\'/g, "\\'");
				action = "fmLib.openDialog('" + url + "', '" + json.dialog + "', '" + caption + "')";
			}
			else {
				if(json.dialog == 'fmMediaPlayer') {
					url += '&fmMode=loadFile&fmObject=' + id + '&hash=' + fmEntries[curCont][id].hash;
				}
				caption = [json.caption + (id ? ': ' + fmEntries[curCont][id].name : '')];
				if(json.confirm) caption.push(json.confirm);
				if(json.text) for(i = 0; i < json.text.length; i++) {
					caption.push(json.text[i]);
				}
				if(typeof json.content != 'undefined') name = json.content;
				else if(id) name = fmEntries[curCont][id].name.replace(/\'/g, "\\'");
				for(i in caption) caption[i] = caption[i].replace(/\'/g, "\\'");
				action = "fmLib.openDialog('" + url + "', '" + json.dialog + "', ['" + caption.join("','") + "'], '" + id + "')";
			}
		}
		return action;
	},

	parseMenu: function(items, curCont, id) {
		var i, css, icon, action;
		var html = '<table border="0" cellspacing="0" cellpadding="0" width="100%">';

		for(i = 0; i < items.length; i++) {
			if(items[i].caption == 'separator') {
				if(items[i+1] && items[i+1].caption != 'separator') {
					if(items[i].title) {
						css = 'width:190px; text-align:left; white-space:nowrap; padding-left:5px; overflow:hidden';
						html += '<tr class="fmMenuTitle">' +
								'<td class="fmMenuBorder" height="22" colspan="2"><div class="fmMenuTitle" style="' + css + '">' + items[i].title + '</div></td>' +
								'</tr>';
					}
					else {
						css = 'height:0; border-bottom:1px solid #FFFFFF';
						html += '<tr class="fmTD2">' +
								'<td class="fmMenuBorder" style="' + css + '"></td>' +
								'<td class="fmMenuBorder" style="' + css + '"></td>' +
								'</tr>';
					}
				}
			}
			else {
				css = (items[i].call || items[i].exec || items[i].dialog) ? 'fmMenuItem' : 'fmContentDisabled';
				icon = fmWebPath + '/icn/' + items[i].icon;
				action = this.parseAction(items[i], curCont, id);

				html += '<tr class="fmTD2" style="cursor:pointer; height:22px"' +
						' onMouseOver="this.className=\'fmTH2\'"' +
						' onMouseOut="this.className=\'fmTD2\'"' +
						' onClick="' + action + '">' +
						'<td class="fmTH2" width="24" align="center">' +
						'<img src="' + icon + '" border="0" /></td>' +
						'<td class="' + css + '" align="left" style="padding-left:4px">' + items[i].caption + '</td>' +
						'</tr>';
			}
		}
		html += '</table>';
		return html;
	},

	_checkAction: function(item, json) {
		var url = fmWebPath + '/action.php?fmContainer=' + json.cont;
		var menu = "fmLib.viewMenu('" + item.id + "', '" + json.cont + "')";
		if(item.deleted) return menu;

		var action = menu;

		if(item.type == 'cdup') {
			var mode = (json.search != '') ? 'search' : 'parent';
			action = "fmLib.call('" + url + '&fmMode=' + mode + "')";
			menu = "fmLib.viewMenu(-1, '" + json.cont + "')";

			if(fmLib.useRightClickMenu[json.cont]) {
				return 'if(event.button == 2) { ' + menu + '; } else { ' + action + '; }';
			}
			return "(event.button == 2) ? '' : fmLib.noMenu = true; " + action + ';';
		}

		if(fmLib.useRightClickMenu[json.cont]) {
			var lang = fmContSettings[json.cont].language;
			var userPerms = fmContSettings[json.cont].userPerms;

			if(item.type == 'dir') {
				action = this.parseAction({call:'open'}, json.cont, item.id);
			}
			else if(fmContSettings[json.cont].customAction && fmContSettings[json.cont].customAction.action) {
				action = this.parseAction({exec:[fmContSettings[json.cont].customAction.action,json.cont,item.id,fmEntries[json.cont][item.id].fullName]});
			}
			else if(userPerms.docViewer && item.docType > 0) {
				switch(item.docType) {

					case 1:
						action = this.parseAction({caption:fmMsg[lang].cmdView,dialog:'fmTextViewer'}, json.cont, item.id);
						break;

					case 2:
						if(fmContSettings[json.cont].publicUrl != '') {
							action = this.parseAction({caption:fmMsg[lang].cmdView,dialog:'fmDocViewer'}, json.cont, item.id);
						}
						break;
				}
			}
			else if(userPerms.imgViewer && item.name.match(this.imageFiles)) {
				action = this.parseAction({caption:fmMsg[lang].cmdView,dialog:'fmImgViewer'}, json.cont, item.id);
			}
			else if(userPerms.mediaPlayer && (item.name.match(this.audioFiles) || item.name.match(this.videoFiles))) {
				action = this.parseAction({caption:fmMsg[lang].cmdPlay,dialog:'fmMediaPlayer'}, json.cont, item.id);
			}
			return 'if(event.button == 2) { ' + menu + '; } else { ' + action + '; }';
		}
		return "(event.button == 2) ? '' : " + menu;
	},

	_getFileCount: function(cont) {
		if(!fmContSettings[cont].listJson.entries) return 0;
		var items = fmContSettings[cont].listJson.entries.items;
		var cnt = 0;

		if(items) for(var i in items) {
			if(!items[i].isDir && items[i].type != 'cdup' && !items[i].deleted) {
				cnt++;
			}
		}
		return cnt;
	}
}
