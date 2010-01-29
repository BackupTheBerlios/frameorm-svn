
function SpawFlash(oFlash, oImage)
{
	oFlash.setAttribute('src', '/flvplayer.swf');
	var vars = "file="+oImage.src.substring(oImage.src.indexOf("src=")+4)
	oFlash.setAttribute('flashvars', vars);
	oFlash.setAttribute('wmode','transparent');
	oFlash.setAttribute('style','float:left');
	return oFlash;
}


function Player(params) {
	params = params || {};

	params.width = params.width || '100%';
	params.height = params.height || '100%';
	params.overflow = 'hidden';
	this.playlist = (params.playlist==true ||
					  params.playlist=='true')?true:false;
					  
	this.showIcons = (params.showicons==true ||
					  params.showicons=='true')?true:false;
	this.autoStart = (params.autostart==true ||
					  params.autostart=='true')?true:false;
	this.showControls = (params.showcontrols==true ||
						 params.showcontrols=='true')?true:false;
	this.file = params.file;
    this.wmode = params.wmode || 'transparent';
	this.image = params.image
	this.volume = (typeof params.volume == 'undefined')?80:params.volume;
	//this._addSWFObject();
}

Player.prototype.render = function() {
	var iOffset = (this.showControls)?20:0;
	var oDiv = document.createElement('DIV');
	var swf = '<object type="application/x-shockwave-flash" ' +
			'data="/flvplayer.swf" ' +
			'width="100%" height="100%">' +
		'<param name="allowScriptAccess" value="always" />' +
		'<param name="movie" value="/flvplayer.swf" />' +
		'<param name="quality" value="high" />' +
		'<param name="scale" value="noScale" />' +
		'<param name="wmode" value="' + this.wmode + '" />' +
		'<param name="allowfullscreen" value="false" />' +
		'<param name="flashvars" value="overstretch=true' +
			'&displayheight=200' +
			'&autostart=' +	this.autoStart.toString() +
            '&showicons=' + this.showIcons.toString() +
			'&wmode=' +	this.wmode +
			'&volume=' + this.volume.toString();
	if (this.file)
		swf += '&file=' + this.file;
	if (this.playlist)
		swf += '&playlist=bottom';
	if (this.image)
		swf += '&image=' + this.image;
	swf += '&allowfullscreen=false&enablejs=true&rotatetime=20000&bufferlength=2"/></object>';
	oDiv.innerHTML = swf;
	return oDiv;
}





