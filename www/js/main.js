$(function() {

    $('#bg').css({
        height: $(window).height(),
        width: $(window).width()
        // backgroundSize: 'cover'
    });
    
    function play_parable(parable)
    {
        var sound = new Howl({
            src: ['/mp3/' + parable.mp3_file],
            autoplay: true,
            loop: false,
            volume: 0.5,
            onend: function() {
                sound.unload();
                $.getJSON('/api.php?last=' + parable.id, function(response) {
                    
                    // preload the next image
                    $('<img/>')[0].src = '/img/' + response.background.image;

                    // after a few seconds, replace the background
                    setTimeout(function(){
                        // replace the background
                        $('#mask').css({height: $(window).height(), width: $(window).width()})
                        .fadeIn('slow', function(){
                            $('#bg').css('background-image', 'url(/img/' + response.background.image + ')');
                            $('#mask').fadeOut('slow');
                        });
                    }, 2000);
                    
                    // start the next audio clip
                    setTimeout(function() {
                        play_parable(response.parable);
                    }, 3000);
                    
                });
            }
        });
    }
    
    $.getJSON('api.php', function(response) {
        if (response.status == 'success') {
            play_parable(response.parable);
        }
        else {
            $('body').append('Something went wrong.  Refresh and try again.');
        }
    });

});