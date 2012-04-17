var Slider = Class.create();
Slider.prototype = {
    options: {
        shift: 329
    },
    
    initialize: function(container, controlLeft, controlRight){
        this.animating = false;
        this.containerSize = {
            width: $(container).offsetWidth,
            height: $(container).offsetHeight
        },
        this.content = $(container).down();
        this.controlLeft = $(controlLeft);
        this.controlRight = $(controlRight);
        
        this.initControls();
    },
    
    initControls: function(){
        this.controlLeft.href = this.controlRight.href = 'javascript:void(0)';
        Event.observe(this.controlLeft,  'click', this.shiftLeft.bind(this));
        Event.observe(this.controlRight, 'click', this.shiftRight.bind(this));
        this.updateControls(1, 0);
    },
    
    shiftRight: function(){
        if (this.animating)
            return;
        
        var left = isNaN(parseInt(this.content.style.left)) ? 0 : parseInt(this.content.style.left);
        
        if ((left + this.options.shift) < 0) {
            var shift = this.options.shift;
            this.updateControls(1, 1);
        } else {
            var shift = Math.abs(left);
            this.updateControls(1, 0);
        }
        this.moveTo(shift);
    },
    
    shiftLeft: function(){
        if (this.animating)
            return;
        
        var left = isNaN(parseInt(this.content.style.left)) ? 0 : parseInt(this.content.style.left);
        
        var lastItemLeft = this.content.childElements().last().positionedOffset()[0];
        var lastItemWidth = this.content.childElements().last().offsetWidth;
        var contentWidth = lastItemLeft + lastItemWidth;
        
        if ((contentWidth + left - this.options.shift) > this.containerSize.width) {
            var shift = this.options.shift;
            this.updateControls(1, 1);
        } else {
            var shift = contentWidth + left - this.containerSize.width;
            this.updateControls(0, 1);
        } 
        this.moveTo(-shift);
    },
    
    moveTo: function(shift){
        var scope = this;
                     
        this.animating = true;
        
        new Effect.Move(this.content, {
            x: shift,
            duration: 0.4,
            delay: 0,
            afterFinish: function(){
                scope.animating = false;
            }
        });
    },
    
    updateControls: function(left, right){
        if (!left)
            this.controlLeft.addClassName('disabled');
        else
            this.controlLeft.removeClassName('disabled');
        
        if (!right)
            this.controlRight.addClassName('disabled');
        else
            this.controlRight.removeClassName('disabled');
    }
}
