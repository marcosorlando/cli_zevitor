$(function () {
    /* --------------------------------------------
     FORMULÁRIO DA LANDING PAGE
     Mesmos campos do contato; grava o lead via lp/ajax/Lp.ajax.php.
    -------------------------------------------- */
    $('.j_landing_page').on('submit', function (event) {
        event.preventDefault();

        var form = $(this);
        var button = form.find('.call_button');
        if (button.prop('disabled')) {
            return;
        }

        button.prop('disabled', true);
        form.find('.form_load').fadeIn(300);

        var send = function () {
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'POST',
                dataType: 'json'
            }).done(function (data) {
                form.find('.callback_return').html(data.trigger || '');
                form.find('.trigger_ajax').fadeIn('slow');
                form.find('.error').removeClass('error');

                // A mensagem fica no topo do formulário, fora da tela de quem clicou no botão.
                form.find('.callback_return')[0].scrollIntoView({behavior: 'smooth', block: 'center'});

                if (data.field) {
                    form.find("[name='" + data.field + "']").addClass('error').trigger('focus');
                } else if (form.find('.trigger_success').length) {
                    form[0].reset();
                }

                if (data.redirect) {
                    window.setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            }).fail(function () {
                form.find('.callback_return').html("<div class='trigger trigger_alert'><span>Não foi possível enviar agora. Tente novamente.</span></div>");
            }).always(function () {
                button.prop('disabled', false);
                form.find('.form_load').fadeOut(300);
            });
        };

        // Definido em assets/js/script.js, carregado no rodapé do index.php.
        if (typeof window.withRecaptchaToken === 'function') {
            window.withRecaptchaToken(this, 'landing_page', send);
        } else {
            send();
        }
    });
});
