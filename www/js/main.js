$(function() {

    // styling
    $('#bg').css({
        height: $(window).height(),
        width: $(window).width()
    });
    
    $(window).change(function() {
        $(".forward span").css("marginTop", Math.round(window.innerHeight/2)-48);
    }).trigger('change');

    $(".right")
        .on('mouseenter', function() {
            $(this).find('.forward').fadeIn();
        })
        .on('mouseleave', function() {
            $(this).find('.forward').fadeOut();
        })
        .on('click', playParable);

    // parable playback

    var currentParable = {
        parable : {
            id : ''
        }
    };

    var howler = null;

    function playParable() {
        if (howler) howler.unload();

        $.getJSON('api.php?last=' + currentParable.parable.id, function(response) {
            if (response.status == 'success') {

                currentParable = response;

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
                }, 500);

                // start the next audio clip
                setTimeout(function() {
                    howler = new Howl({
                        src:      ['/mp3/' + response.parable.mp3_file],
                        autoplay: true,
                        loop:     false,
                        volume:   0.5,
                        onend: function() {
                            playParable();
                        }
                    });
                }, 3000);
            }
            else {
                $('body').append('Something went wrong.  Refresh and try again.');
            }
        });
    
    }

    playParable();

    // function play_parable(parable)
    // {
    //     var sound = new Howl({
    //         src:      ['/mp3/' + parable.mp3_file],
    //         autoplay: true,
    //         loop:     false,
    //         volume:   0.5,
    //         onend: function() {
    //             sound.unload();
    //             $.getJSON('/api.php?last=' + parable.id, function(response) {
                    
    //                 // preload the next image
    //                 $('<img/>')[0].src = '/img/' + response.background.image;

    //                 // after a few seconds, replace the background
    //                 setTimeout(function(){
    //                     // replace the background
    //                     $('#mask').css({height: $(window).height(), width: $(window).width()})
    //                     .fadeIn('slow', function(){
    //                         $('#bg').css('background-image', 'url(/img/' + response.background.image + ')');
    //                         $('#mask').fadeOut('slow');
    //                     });
    //                 }, 2000);
                    
    //                 // start the next audio clip
    //                 setTimeout(function() {
    //                     play_parable(response.parable);
    //                 }, 3000);
                    
    //             });
    //         }
    //     });
    // }
    
    // $.getJSON('api.php', function(response) {
    //     if (response.status == 'success') {
    //         play_parable(response.parable);
    //     }
    //     else {
    //         $('body').append('Something went wrong.  Refresh and try again.');
    //     }
    // });



});