(function(){
	var t=document.getElementById('jinbound_tracks');
	if(!t)return;
	var n=t.getAttribute('jib');
	if(n)document.cookie='__jib__='+n;
})();