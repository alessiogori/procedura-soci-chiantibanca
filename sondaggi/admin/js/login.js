$(window, document, undefined).ready(function() {

  $('input').blur(function() {
    var $this = $(this);
    if($this.val())
      $this.addClass('used');
    else
      $this.removeClass('used');
  });

  var $nmcontainer = $('.nmcontainer');

  $nmcontainer.on('click.nmcontainer', function(e) {

    var $this = $(this);
    var $offset = $this.parent().offset();
    var $circle = $this.find('.nmcontainerCircle');

    var x = e.pageX - $offset.left;
    var y = e.pageY - $offset.top;

    $circle.css({
      top: y + 'px',
      left: x + 'px'
    });

    $this.addClass('is-active');

  });

  $nmcontainer.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function(e) {
  	$(this).removeClass('is-active');
  });

});