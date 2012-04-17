function dropdown(el, container)
{   
    if ($$('.' + container + ' .content')[0].empty() || $(el).hasClassName('active'))
	   return false;
	
    Effect.toggle(el, 'appear', {
	    duration: 0, 
	    afterFinish: function(){$(container).removeClassName('active');}
    });		
}