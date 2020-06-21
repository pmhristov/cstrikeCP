/*********************************************************************************************************
 This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
*********************************************************************************************************/

//---------------------------------------------------------------------------------------------------------
// Syntax higlighting: supported languages
//---------------------------------------------------------------------------------------------------------
var fmSyntax = {

	javascript: {
		operators: {
			match: [ /\/\*/g, /\*\//g, /\/\//g, /((&amp;)+|(&lt;)+|(&gt;)+|[\|!=%\*\/\+\-]+)/g, /\u0002/g, /\u0003/g, /\u0004/g ],
			replace: [ '\u0002', '\u0003', '\u0004', '<tt>$1</tt>', '/*', '*/', '//' ],
			style: 'tt { color: #C00000; }'
		},
		brackets: {
			match: [ /([\(\)\{\}\[\]])/g ],
			replace: [ '<b>$1</b>' ],
			style: 'b { color: #A000A0; font-weight: bold; }'
		},
		numbers: {
			match: [ /\b(-?\d+)\b/g ],
			replace: [ '<u>$1</u>' ],
			style: 'u { color: #C00000; }'
		},
		keywords: {
			match: [ /\b(break|case|catch|const|continue|default|delete|do|else|export|false|finally|for|function|if|in|instanceof|new|null|return|switch|this|throw|true|try|typeof|undefined|var|void|while|with)\b/g ],
			replace: [ '<em>$1</em>' ],
			style: 'em { color: #0000C0; }'
		},
		strings: {
			match: [ /(".*?")/g, /('.*?')/g ],
			replace: [ '<s>$1</s>', '<s>$1</s>' ],
			style: 's, s u, s tt, s b, s em, s i { color: #008000; font-weight: normal; }'
		},
		comments: {
			match: [ /(\/\/[^\n]*)(\n|$)/g, /(\/\*)/g, /(\*\/)/g ],
			replace: [ '<i>$1</i>$2', '<i>$1', '$1</i>' ],
			style: 'i, i u, i tt, i b, i s, i em { color: #808080; font-weight: normal; }'
		}
	},

	php: {
		tags: {
			match: [ /&lt;(\/?(a|abbr|acronym|address|applet|area|b|base|basefont|bdo|big|blockquote|body|br|button|caption|center|cite|code|col|colgroup|dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frame|frameset|h[1-6]|head|hr|html|i|iframe|img|input|ins|isindex|kbd|label|legend|li|link|map|menu|meta|noframes|noscript|object|ol|optgroup|option|p|param|pre|q|s|samp|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var)(\s+.*?)?)&gt;/gi ],
			replace: [ '��$1���' ]
		},
		operators: {
			match: [ /\/\*/g, /\*\//g, /\/\//g, /((&amp;)+|(&lt;)+|(&gt;)+|[\|!=%\*\/\+\-]+)/g, /\u0002/g, /\u0003/g, /\u0004/g, /��(.+?)���/g ],
			replace: [ '\u0002', '\u0003', '\u0004', '<tt>$1</tt>', '/*', '*/', '//', '<em>&lt;$1&gt;</em>' ],
			style: 'tt { color: #C00000; }'
		},
		brackets: {
			match: [ /([\(\)\{\}\[\]])/g, /(<tt>)?&lt;(<\/tt>)?\?(php)?/gi, /\?(<tt>)?&gt;(<\/tt>)?/gi ],
			replace: [ '<b>$1</b>', '<b>&lt;?$3</b>', '<b>?&gt;</b>' ],
			style: 'b { color: #A000A0; font-weight: bold; }'
		},
		numbers: {
			match: [ /\b(-?\d+)\b/g ],
			replace: [ '<u>$1</u>' ],
			style: 'u { color: #C00000; }'
		},
		keywords: {
			match: [ /\b(__CLASS__|__FILE__|__FUNCTION__|__LINE__|__METHOD__|abstract|and|array|as|break|case|catch|class|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|eval|exception|exit|extends|final|false|for|foreach|function|global|if|implements|include|include_once|interface|isset|list|new|or|print|private|protected|public|require|require_once|return|static|switch|this|throw|true|try|unset|use|var|while|xor)\b/g ],
			replace: [ '<em>$1</em>' ],
			style: 'em, em tt { color: #0000C0; font-weight: normal; }'
		},
		variables: {
			match: [ /(\$)(<[^>]+>)?(\w+)(<\/[^>]+>)?\b/gi ],
			replace: [ '<ins>$1$3</ins>' ],
			style: 'ins { color: #909000; }'
		},
		strings: {
			match: [ /(".*?")/g, /('.*?')/g ],
			replace: [ '<s>$1</s>', '<s>$1</s>' ],
			style: 's, s u, s tt, s b, s em, s ins, s i { color: #008000; font-weight: normal; }'
		},
		comments: {
			match: [ /(\/\/[^\n]*)(\n|$)/g, /(#[^\n]*)(\n|$)/g, /(\/\*)/g, /(\*\/)/g, /(<tt>)?&lt;(<\/tt><tt>)?!--(<\/tt>)/gi, /(<tt>)?--(<\/tt><tt>)?&gt;(<\/tt>)?/gi ],
			replace: [ '<i>$1</i>$2', '<i>$1</i>$2', '<i>$1', '$1</i>', '<i>&lt;!--', '--&gt;</i>' ],
			style: 'i, i u, i tt, i b, i s, i em, i ins { color: #808080; font-weight: normal; }'
		}
	},

	html: {
		scriptAreas: {
			match: [ /(&lt;script(.*?)&gt;)/gi, /(&lt;\/script&gt;)/gi ],
			replace: [ '$1<tt>', '</tt>$1' ],
			style: 'tt { color: #909000; }'
		},
		styleAreas: {
			match: [ /(&lt;style(.*?)&gt;)/gi, /(&lt;\/style&gt;)/gi ],
			replace: [ '$1<b>', '</b>$1' ],
			style: 'b { color: #A000A0; }'
		},
		tags: {
			match: [ /(&lt;\/?(a|abbr|acronym|address|applet|area|b|base|basefont|bdo|big|blockquote|body|br|button|caption|center|cite|code|col|colgroup|dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frame|frameset|h[1-6]|head|hr|html|i|iframe|img|input|ins|isindex|kbd|label|legend|li|link|map|menu|meta|noframes|noscript|object|ol|optgroup|option|p|param|pre|q|s|samp|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var)(\s+.*?)?&gt;)/gi ],
			replace: [ '<em>$1</em>' ],
			style: 'em { color: #0000C0; }'
		},
		strings: {
			match: [ /=(".*?")/g, /=('.*?')/g ],
			replace: [ '=<s>$1</s>', '=<s>$1</s>' ],
			style: 's, s tt, s b, s em, s i { color: #008000; }'
		},
		comments: {
			match: [ /(&lt;!--)/g, /(--&gt;)/g ],
			replace: [ '<i>$1', '$1</i>' ],
			style: 'i, i tt, i b, i s, i em { color: #808080; }'
		}
	},

	css: {
		classes: {
			match: [ /(.+?)\{/g ],
			replace: [ '<tt>$1</tt>{' ],
			style: 'tt { color: #0000C0; }'
		},
		keys: {
			match: [ /([\{\n]\s*)([\w\-]*?:)([^\/])/g ],
			replace: [ '$1<u>$2</u>$3', ':' ],
			style: 'u { color: #C00000; }'
		},
		brackets: {
			match: [ /([\{\}])/g ],
			replace: [ '<b>$1</b>' ],
			style: 'b { color: #A000A0; font-weight: bold; }'
		},
		comments: {
			match: [ /(\/\*)/g, /(\*\/)/g ],
			replace: [ '<i>$1', '$1</i>' ],
			style: 'i, i tt, i u, i b { color: #808080; font-weight: normal; }'
		}
	},

	perl: {
		tags: {
			match: [ /&lt;(\/?(a|abbr|acronym|address|applet|area|b|base|basefont|bdo|big|blockquote|body|br|button|caption|center|cite|code|col|colgroup|dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frame|frameset|h[1-6]|head|hr|html|i|iframe|img|input|ins|isindex|kbd|label|legend|li|link|map|menu|meta|noframes|noscript|object|ol|optgroup|option|p|param|pre|q|s|samp|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var)(\s+.*?)?)&gt;/gi ],
			replace: [ '��$1���' ]
		},
		operators: {
			match: [ /((&amp;)+|(&lt;)+|(&gt;)+|[\|=\+\-]+|[!%\*\/~])/g, /��(.+?)���/g ],
			replace: [ '<tt>$1</tt>', '<em>&lt;$1&gt;</em>' ],
			style: 'tt { color: #C00000; }'
		},
		brackets: {
			match: [ /([\(\)\{\}\[\]])/g ],
			replace: [ '<b>$1</b>' ],
			style: 'b { color: #A000A0; font-weight: bold; }'
		},
		numbers: {
			match: [ /\b(-?\d+)\b/g ],
			replace: [ '<u>$1</u>' ],
			style: 'u { color: #C00000; }'
		},
		keywords: {
			match: [ /\b(abs|accept|alarm|atan2|bind|binmode|bless|caller|chdir|chmod|chomp|chop|chown|chr|chroot|close|closedir|connect|continue|cos|crypt|dbmclose|dbmopen|defined|delete|die|do|dump|each|else|elsif|endgrent|endhostent|endnetent|endprotoent|endpwent|eof|eval|exec|exists|exit|fcntl|fileno|find|flock|for|foreach|fork|format|formlinegetc|getgrent|getgrgid|getgrnam|gethostbyaddr|gethostbyname|gethostent|getlogin|getnetbyaddr|getnetbyname|getnetent|getpeername|getpgrp|getppid|getpriority|getprotobyname|getprotobynumber|getprotoent|getpwent|getpwnam|getpwuid|getservbyaddr|getservbyname|getservbyport|getservent|getsockname|getsockopt|glob|gmtime|goto|grep|hex|hostname|if|import|index|int|ioctl|join|keys|kill|last|lc|lcfirst|length|link|listen|LoadExternals|local|localtime|log|lstat|map|mkdir|msgctl|msgget|msgrcv|msgsnd|my|next|no|oct|open|opendir|ordpack|package|pipe|pop|pos|print|printf|push|pwd|qq|quotemeta|qw|rand|read|readdir|readlink|recv|redo|ref|rename|require|reset|return|reverse|rewinddir|rindex|rmdir|scalar|seek|seekdir|select|semctl|semget|semop|send|setgrent|sethostent|setnetent|setpgrp|setpriority|setprotoent|setpwent|setservent|setsockopt|shift|shmctl|shmget|shmread|shmwrite|shutdown|sin|sleep|socket|socketpair|sort|splice|split|sprintf|sqrt|srand|stat|stty|study|sub|substr|symlink|syscall|sysopen|sysread|system|syswritetell|telldir|tie|tied|time|times|tr|truncate|uc|ucfirst|umask|undef|unless|unlink|until|unpack|unshift|untie|use|utime|values|vec|waitpid|wantarray|warn|while|write)\b/g ],
			replace: [ '<em>$1</em>' ],
			style: 'em, em tt { color: #0000C0; font-weight: normal; }'
		},
		variables: {
			match: [ /(<tt>)?([\$@%])(<\/tt>)?(<[^>]+>)?(\w+)(<\/[^>]+>)?\b/gi ],
			replace: [ '<ins>$2$5</ins>' ],
			style: 'ins { color: #909000; }'
		},
		strings: {
			match: [ /(".*?")/g, /('.*?')/g ],
			replace: [ '<s>$1</s>', '<s>$1</s>' ],
			style: 's, s u, s tt, s b, s em, s ins, s i { color: #008000; font-weight: normal; }'
		},
		comments: {
			match: [ /(#[^\n]*)(\n|$)/g, /(<tt>)?&lt;(<\/tt><tt>)?!--(<\/tt>)/gi, /(<tt>)?--(<\/tt><tt>)?&gt;(<\/tt>)?/gi ],
			replace: [ '<i>$1</i>$2', '<i>&lt;!--', '--&gt;</i>' ],
			style: 'i, i u, i tt, i b, i s, i em, i ins { color: #808080; font-weight: normal; }'
		}
	},

	xml: {
		tags: {
			match: [ /(&lt;\/?([\w\-:]+)(\s+.*?)?\/?&gt;)/gi ],
			replace: [ '<em>$1</em>' ],
			style: 'em { color: #0000C0; }'
		},
		attributes: {
			match: [ /([\w\-:]+)=(".*?")/g, /([\w\-]+)=('.*?')/g ],
			replace: [ '<u>$1</u>=<s>$2</s>', '<u>$1</u>=<s>$2</s>' ],
			style: 'u { color: #C00000; } s, s u, s em, s i { color: #008000; }'
		},
		comments: {
			match: [ /(&lt;!--)/g, /(--&gt;)/g ],
			replace: [ '<i>$1', '$1</i>' ],
			style: 'i, i u, i em { color: #808080; }'
		}
	},

	sql: {
		operators: {
			match: [ /((&amp;)+|(&lt;)+|(&gt;)+|[\|=]+|[!%\*\/\+\-])/gi ],
			replace: [ '<tt>$1</tt>' ],
			style: 'tt { color: #C00000; }'
		},
		numbers: {
			match: [ /\b(-?\d+)\b/g ],
			replace: [ '<u>$1</u>' ],
			style: 'u { color: #C00000; }'
		},
		commands: {
			match: [ /\b(abort|alter|analyze|begin|call|checkpoint|close|cluster|comment|commit|copy|create|deallocate|declare|delete|drop|end|execute|explain|fetch|grant|insert|listen|load|lock|move|notify|optimize|prepare|reindex|replace|reset|restart|revoke|rollback|select|set|show|start|truncate|unlisten|update)\b/gi ],
			replace: [ '<em>$1</em>' ],
			style: 'em { color: #0000C0; font-weight: bold; }'
		},
		keywords: {
			match: [ /\b(accessible|add|after|aggregate|alias|all|and|as|asc|authorization|auto_increment|between|both|by|cascade|cache|cache|called|cascade|case|character\s+set|charset|check|collate|column|comment|constraint|createdb|createuser|cycle|databases?|default|deferrable|deferred|delayed|desc|diagnostics|distinct(row)?|domain|duplicate|each|else|else?if|encrypted|except|exception|exists|false|fixed|for|force|foreign|from|full|function|get|group|having|high_priority|if|immediate|immutable|in|increment|index|inherits|initially|inner|input|intersect|into|invoker|is|join|key|language|left|like|limit|local|loop|low_priority|match|maxvalue|minvalue|natural|nextval|no|nocreatedb|nocreateuser|not|null|of|offset|oids|on|only|operator|or|order|outer|owner|partial|password|perform|plpgsql|primary|record|references|require|restrict|returns?|right|row|rule|schemas?|security|sensitive|separator|sequence|session|spatial|sql|stable|statistics|table|temp|temporary|terminated|then|to|trailing|transaction|trigger|true|type|unencrypted|union|unique|unsigned|user|using|valid|values?|view|volatile|when|where|while|with(out)?|xor|zerofill|zone)\b/gi ],
			replace: [ '<b>$1</b>' ],
			style: 'b { color: #0000E0; }'
		},
		types: {
			match: [ /\b(bigint|bigserial|binary|bit|blob|bool(ean)?|box|bytea|char(acter)?|cidr|circle|date(time)?|dec(imal)?|double|enum|float[48]?|inet|int[248]?|integer|interval|line|longblob|longtext|lseg|macaddr|mediumblob|mediumint|money|numeric|oid|path|point|polygon|precision|real|refcursor|serial[48]?|smallint|text|time(stamp)?|tinyblob|tinyint|varbinary|varbit|varchar(acter)?|year)\b/gi ],
			replace: [ '<ins>$1</ins>' ],
			style: 'ins { color: #909000; }'
		},
		strings: {
			match: [ /(".*?")/g, /('.*?')/g ],
			replace: [ '<s>$1</s>', '<s>$1</s>' ],
			style: 's, s b, s u, s tt, s em, s ins, s i { color: #008000; font-weight: normal; }'
		},
		comments: {
			match: [ /(#[^\n]*)(\n|$)/g ],
			replace: [ '<i>$1</i>$2' ],
			style: 'i, i b, i tt, i u, i s, i em, i ins { color: #808080; font-weight: normal; }'
		}
	}
}
