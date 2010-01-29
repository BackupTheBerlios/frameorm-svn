
function Tab(container)
{
	var container = $(container);
	this.tabs = container.getElementsByTagName('UL')[0].getElementsByTagName('LI');
	
	var self = this;
	for(var i=0; i<this.tabs.length; i++){
		var tab = this.tabs[i];
		tab.tabContent = $(tab.getAttribute('target'));
		tab.onclick = function(){
			self.activate(this);
		}
	}
}

Tab.prototype.activate = function(tab)
{
	for(var i=0; i<this.tabs.length; i++){
		var _tab = this.tabs[i];
		_tab.className = '';
		_tab.tabContent.className = 'content';
	}
	tab.className = 'active';
	tab.tabContent.className = 'show';
	var f = tab.getAttribute('onactivate');
	if(f){
		f = eval(f);
		f(tab);
	}
}