/**
 * Função principal de retorno do ajax
 * @param object data
 * @returns null
 */

var xhr = $.ajax();

/**
 * Convert uma string "false" ou "true" para boolean
 */

function toBoolean(bool) {
    if (bool == 'true' || bool == true) {
        return true
    }
    return false;
}

function getMaxZIndex() {
    let date = Date.now();
    return parseInt(date.toString().substr(4, 10));
}

/**
 * Controla os eventos dos botões de voltar do navegador
 */
window.onpopstate = function(event) {
    execAjax(document.location);
}

function returnRequest(data) {
    $.each(data.actions, function(i, act) {
        switch (act.action) {
            case 'HTML':
                $(act.selector).html(act.data);
                if (typeof loadPlugins == 'function') {
                    loadPlugins();
                }
                break;
            case 'OPTION':
                $(act.selector).html(act.data);
                break;
            case 'VAL':
                $(act.selector).val(act.data);
                break;
            case 'ATTR':
                $(act.selector).attr(act.data);
                break;
            case 'UPDATEGRID':
                updateGrid(act.id, act.options);
                break;
            case 'OPEN-DIALOG':

                $('#dialog').css('zIndex', getMaxZIndex());
                $('#dialog .modal-title').html(act.title);
                $('#dialog .modal-body').html(act.data);
                $('#dialog .modal-dialog').removeClass('modal-lg');
                $('#dialog .modal-dialog').removeClass('modal-sm');
                if (act.type != '') {
                    $('#dialog .modal-dialog').addClass(act.type);
                } else if (act.size != 0) {
                    $('#dialog .modal-dialog').width(act.size);
                }

                $('#dialog').modal();

                if (typeof loadPlugins == 'function') {
                    loadPlugins();
                }
                break;
            case 'OPEN-MODAL':

                $('#modal').css('zIndex', getMaxZIndex());
                $('#modal .modal-title').html(act.title);
                $('#modal .modal-body').html(act.data);
                $('#modal .modal-dialog').removeClass('modal-lg');
                $('#modal .modal-dialog').removeClass('modal-sm');
                if (act.type != '') {
                    $('#modal .modal-dialog').addClass(act.type);
                } else if (act.size != 0) {
                    $('#modal .modal-dialog').width(act.size);
                }

                $('#modal').modal({
                    'backdrop': false,
                    'keyboard': false,
                    'show': true,
                });
                if (typeof loadPlugins == 'function') {
                    loadPlugins();
                }
                break;
            case 'CLOSE-MODAL':
                $('.modal').modal('hide');
                break;
            case 'ALERT':
                alert(act.msg);
                break;
            case 'GRITTER':
                $.gritter.add(act.params);
                break;
            case 'REDIRECT':
                if (data.isAjax == true) {
                    window.history.pushState(act.url, $(document).attr('title'), act.url);
                    execAjax(act.url, {}, '#page-loader');
                } else {
                    window.location.href = act.url;
                }

                break;
            case 'REMOVE':
                $(act.selector).remove();
                break;
            case 'REMOVEDATAGRID':
                $('#' + act.grid + ' [data-key="' + act.id + '"]').remove();
                break;
            case 'EXEC-FUNCTION':
                if (eval('typeof ' + act.name + ' == "function"')) {
                    eval(act.name + "();");
                } else {
                    console.log(act.name + ' function not found');
                }
                break;
            case 'ADD-ERRORS':
                $.each(act.errors, function(key, value) {
                    var element = $('.field-' + act.formName.toLowerCase() + '-' + key);
                    var text = '';
                    var sep = '';
                    $.each(value, function(i, t) {
                        text += sep + t;
                        sep = '<br>';
                    });
                    element.find('.help-block').html(text);
                    element.addClass('has-error');
                });
                break;
            case 'REMOVE-CLASS':
                $(act.selector).removeClass(act.className);
                break;
            case 'ADD-CLASS':
                $(act.selector).addClass(act.className);
                break;
            case 'NEW-TAB':
                window.open(act.link, '_blank');
                break;
            case 'DOWNLOAD-PDF':
                blob = new Blob([act.file], { type: 'application/pdf' });

                var link = document.createElement('a');

                link.href = window.URL.createObjectURL(blob);
                link.download = "document.pdf";

                link.click();

                //alert("Nice!");
                break;
            case 'MINI-MENU':
                if (act.mini == true) {
                    if (!$('#page-container').hasClass('page-sidebar-minified')) {
                        $('#page-container').addClass('page-sidebar-minified');
                    }
                } else {
                    if ($('#page-container').hasClass('page-sidebar-minified')) {
                        $('#page-container').removeClass('page-sidebar-minified');
                    }
                }
                break;
            default:
                break;
        }
    });
}

function execAjax(url, data, loader, type, dataType, abort) {

    type = type !== undefined ? type : 'POST';
    dataType = dataType !== undefined ? dataType : 'json';
    abort = toBoolean(abort);

    if (abort) {
        xhr.abort();
    }

    xhr = $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: dataType,
        headers: { 'X-JSCONTROL': 'true' },
        beforeSend: function() {
            if (loader != 0) {
                $(loader).css('zIndex', getMaxZIndex());
                $(loader).removeClass('d-none hide');
            }
        },
        success: function(data) {
            returnRequest(data)
        },
        complete: function(jqXHR, textStatus) {
            if (loader != 0) {
                $(loader).addClass('d-none hide');
            }

            if (textStatus == 'error') {
                if (jqXHR.statusText == "Internal Server Error") {
                    console.log("Página não encontrada ou fora do ar");
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'parsererror') {
                console.log('Retorno de dados invalido verifique se o retorno e do tipo json');
            }
        }
    });
}

var eventsList = [
    'click',
    'blur',
    'change',
    'submit'
];

$(eventsList).each(function(i, eventName) {
    $(document).on(eventName, '[data-jsc-event="' + eventName + '"]', function(e) {
        e.preventDefault();
        ajaxRequest($(this), eventName);
    });
});


function ajaxRequest(obj, eventName) {
    var url = null;
    var params = [];
    var loader = '#page-loader';
    var method = 'post';

    if (obj.attr('data-jsc-confirm')) {
        if (!confirm(obj.attr('data-jsc-confirm'))) {
            return;
        }
    }

    if (obj.attr('data-jsc-sendformfield')) {
        var form = obj.closest('form');
        params = form.serializeArray();
    }

    if (obj.attr('data-jsc-url')) {
        url = obj.attr('data-jsc-url');
    } else if (obj.attr('href')) {
        url = obj.attr('href');
    } else {
        url = obj.attr('action');
        method = obj.attr('method');
        params = obj.serializeArray();
    }

    if (eventName == 'blur' && url == undefined) {
        console.log('Para este tipo de evento deve ser informado o data-jsc-url');
        return;
    }

    if (obj.attr('data-jsc-add')) {
        params = params.concat($('[data-jsc-param-add=' + obj.attr('data-jsc-add') + ']').serializeArray());
    }

    if (obj.attr('data-jsc-params')) {
        var data = obj.attr('data-jsc-params').split('&');
        $.each(data, function(i, val) {
            var param = val.split('=');
            eval('t ={name:"' + param[0] + '", value:"' + param[1] + '"}')
            params.push(t);
        });
    }

    if (eventName == 'blur' || eventName == 'change') {
        if (obj.val() == '' && obj.attr('data-jsc-empty') == undefined) {
            return;
        }
        if (obj.hasClass('ui-autocomplete-input')) {
            params = params.concat($('#' + obj.attr('id') + '-hidden').serializeArray());
        }
        if (obj.attr('data-jsc-complements')) {
            params = params.concat($('[data-jsc-complement=' + obj.attr('data-jsc-complements') + ']').serializeArray());
        }
        params.push({ name: 'value', value: obj.val() }, { name: 'name', value: obj.attr('name') });
    }

    if (obj.attr('data-jsc-checkbox-grid')) {
        keys = $('#' + obj.attr('data-jsc-checkbox-grid')).yiiGridView('getSelectedRows');
        $(keys).each(function(i, key) {
            params.push({ name: 'checkbox-grid[]', value: key });
        });
    }

    if (obj.attr('data-jsc-pushState') != 0) {
        window.history.pushState(url, $(document).attr('title'), url);
    }

    if (obj.attr('data-jsc-loader')) {
        loader = obj.attr('data-jsc-loader');
    }

    var abort = true;
    if (obj.attr('data-jsc-ajax-abort')) {
        abort = obj.attr('data-jsc-ajax-abort');
    }

    //	params.push({name: '_csrf', value: yii.getCsrfToken()});

    execAjax(url, params, loader, method, 'json', abort);
}