jQuery(document).ready(function ($) {

    $('.ota-favorites-link a').click(function (e) {
        var action = $(this).data('action');

        $.ajax({
            type: 'POST',
            url: otaFavorites.url,
            data: {
                security: otaFavorites.nonce,
                action: 'ota_' + action,
                postId: otaFavorites.postId
            },
            beforeSend: function () {
                $('.ota-favorites-link a').fadeOut(300, function () {
                    $('.ota-favorites-link .ota-favorites-hidden').fadeIn();
                })
            },
            success: function (res) {
                $('.ota-favorites-link .ota-favorites-hidden').fadeOut(300, function () {
                    $('.ota-favorites-link').html(res);
                    if (action == 'del') {
                        $('.widget_ota-favorites-widget').find('li.cat-item-' + otaFavorites.postId).remove();
                    }
                })
            },
            error: function () {
                alert('Ошибка!');
            }
        });
        e.preventDefault();
    });

});
