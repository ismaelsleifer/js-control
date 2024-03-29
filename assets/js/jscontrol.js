/**
 * Função principal de retorno do ajax
 * @param object data
 * @returns null
 */

var xhr = $.ajax();
var control = false;

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
    let date = Date.now().toString();
    return parseInt(date.substring(4, date.length));
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
            case 'REMOVE_ATTR':
                $(act.selector).removeAttr(act.data);
                break;
            case 'CHECKBOX':
                $(act.selector).prop('checked', act.data);
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
                }

                $('#dialog').modal();

                if (typeof loadPlugins == 'function') {
                    loadPlugins();
                }
                break;
            case 'CREATE-MODAL':
                $('.container-modal').append(act.data);
                let sel = '#' + act.id +' .modal';
                $(sel).modal(act.options);
                $(sel).on('hidden.bs.modal', function(e) {
                    $('#' + act.id).remove();
                });
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
                $(act.selector).modal('hide');
                break;
            case 'ALERT':
                alert(act.msg);
                break;
            case 'GRITTER':
                if (act.options) {
                    $.extend($.gritter.options, act.options);
                }
                $.gritter.add(act.params);
                break;
            case 'REDIRECT':
                if (act.isAjax == true) {
                    if (act.pushstate == true) {
                        window.history.pushState(act.url, $(document).attr('title'), act.url);
                    }
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
            case 'CLEAR-ERROR':
                var element = $('.field-' + act.formName.toLowerCase() + '-' + act.attr);
                element.find('.help-block').html("");
                element.removeClass('has-error');
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
            case 'RELOAD':
                location.reload();
                break;
            case 'DOWNLOAD-PDF':
                blob = new Blob([act.file], { type: 'application/pdf' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "document.pdf";
                link.click();
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
            case 'SWEET-ALERT':
                if (act.urlButtonConfirm) {
                    act.params['preConfirm'] = () => {
                        execAjax(act.urlButtonConfirm, act.data)
                    }
                }
                swal.fire(act.params);
                $('.swal2-container').css('zIndex', getMaxZIndex());
                break;
            case 'EXEC-EVENT':
                if (act.type == 'click') {
                    $(act.selector).click();
                } else if (act.type == 'change') {
                    $(act.selector).change();
                } else if (act.type == 'blur') {
                    $(act.selector).blur();
                } else if (act.type == 'focus') {
                    $(act.selector).focus();
                }
                break;
            case 'APPEND':
                $(act.selector).append(act.data);
                break;
            case 'PREPEND':
                $(act.selector).prepend(act.data);
                break;
            case 'REPLACE-WITH':
                $(act.selector).replaceWith(act.data);
                break;
            case 'FADEIN':
                $(act.selector).fadeIn(act.duration);
                break;
            case 'FADEOUT':
                $(act.selector).fadeOut(act.duration);
                break;
            case 'HIDE':
                $(act.selector).hide();
                break;
            case 'SHOW':
                $(act.selector).show();
                break;
            case 'ANIMATE':
                $(act.selector).animate(act.params);
                break;
            case 'CONSOLE':
                switch (act.type){
                    case 1:
                        console.warn(act.text);
                        break;
                    case 2:
                        console.info(act.text);
                        break;
                    default:
                        console.log(act.text);
                        break;
                }
                break;
            case 'EXEC-AJAX':
                execAjax(act.url, act.params, act.loader, act.type, act.dataType, act.abort);
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

    abort ? xhr.abort() : null;

    xhr = $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: dataType,
        headers: { 'X-JSCONTROL': 'true' },
        beforeSend: function() {
            if (loader != 'false') {
                $(loader).css('zIndex', getMaxZIndex());
                $(loader).removeClass('d-none hide');
            }
        },
        success: function(data) {
            returnRequest(data);
        },
        complete: function(jqXHR, textStatus) {
            if (loader != 'false') {
                $(loader).addClass('d-none hide');
            }
            if (textStatus == 'error') {
                if (jqXHR.statusText == "Internal Server Error") {
                    console.log("Página não encontrada ou fora do ar");
                }
            }
            control = false;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'parsererror') {
                console.log('Retorno de dados invalido verifique se o retorno e do tipo json');
            }
            control = false;
        }
    });
}

var eventsList = ['click', 'blur', 'change', 'submit', 'focusout'];

$(eventsList).each(function(i, eventName) {
    $(document).on(eventName, '[data-jsc-event="' + eventName + '"]', function(e) {
        if($(this).attr('data-jsc-preventefault') != 'false'){
            e.preventDefault();
        }
        ajaxRequest($(this), eventName);
    });
});

function parseQueryString(url) {
    url = url.split('?');
    let data = url.length > 1 ? url[1].split('&') : url[0].split('&');;
    let params = [];
    $.each(data, function(i, val) {
        let param = val.split('=');
        params.push({ name: param[0], value: param[1] });
    });
    return params;
}

function ajaxRequest(obj, eventName) {
    let url = null;
    let params = [];
    let loader = '#page-loader';
    let method = 'post';

    //usado para evitar duplo clic no submit do forme
    let submit = obj.find("[type='submit']")
    if(submit.length > 0){
        if(control == true){
            return;
        }else{
            control = true;
        }
    }

    if (obj.attr('data-jsc-sendformfield')) {
        let form = obj.closest('form');
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

    if (eventName == 'blur' || eventName == 'focusout' && url == undefined) {
        console.log('Para este tipo de evento deve ser informado o data-jsc-url');
        return;
    }

    if (obj.attr('data-jsc-add')) {
        params = params.concat($('[data-jsc-param-add=' + obj.attr('data-jsc-add') + ']').serializeArray());
    }

    if (eventName == 'blur' || eventName == 'change' || eventName == 'focusout') {

        let type = obj.get(0).type;

        if (obj.val() == '' && obj.attr('data-jsc-empty') == undefined) {
            return;
        }

        if (obj.hasClass('ui-autocomplete-input')) {
            params = params.concat($('#' + obj.attr('id') + '-hidden').serializeArray());
        }

        if (obj.attr('data-jsc-complements')) {
            params = params.concat($('[data-jsc-complement=' + obj.attr('data-jsc-complements') + ']').serializeArray());
        }

        let val = obj.val();
        if (type == 'checkbox') {
            if (!obj.is(':checked')) {
                let obj2 = $('input[type="hidden"][name="' + obj.attr('name') + '"]');
                if (obj2 != undefined) {
                    val = obj2.val();
                }
            }
        }

        params.push({ name: 'value', value: val }, { name: 'name', value: obj.attr('name') });

        if (type == 'select-one') {
            let obj2 = $('#' + obj.attr('id') + ' option:selected')
            if (obj2.attr('data-jsc-params')) {
                $.each(parseQueryString(obj2.attr('data-jsc-params')), function(i, val) {
                    params.push(val);
                });
            }
        }
    }

    if (obj.attr('data-jsc-params')) {
        $.each(parseQueryString(obj.attr('data-jsc-params')), function(i, val) {
            params.push(val);
        });
    }

    if (obj.attr('data-jsc-checkbox-grid')) {
        keys = $('#' + obj.attr('data-jsc-checkbox-grid')).yiiGridView('getSelectedRows');
        $(keys).each(function(i, key) {
            params.push({ name: 'checkbox-grid[]', value: key });
        });
    }

    if (obj.attr('data-jsc-confirm')) {
        Swal.fire({
            text: obj.attr('data-jsc-confirm'),
            confirmButtonText: 'Sim',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            cancelButtonText: 'Não',
            focusCancel: true,
            icon: 'question',
            willOpen: function() {
                $('.swal2-container').css('zIndex', getMaxZIndex());
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (obj.attr('data-jsc-pushstate') != 'false') {
                    window.history.pushState(url, $(document).attr('title'), url);
                }

                if (obj.attr('data-jsc-loader')) {
                    loader = obj.attr('data-jsc-loader');
                }

                let abort = true;
                if (obj.attr('data-jsc-ajax-abort')) {
                    abort = obj.attr('data-jsc-ajax-abort');
                }
                execAjax(url, params, loader, method, 'json', abort);
            } else {
                return false;
            }
        });
        return;
    }

    if (obj.attr('data-jsc-pushstate') != 'false') {
        window.history.pushState(url, $(document).attr('title'), url);
    }

    if (obj.attr('data-jsc-loader')) {
        loader = obj.attr('data-jsc-loader');
    }

    let abort = true;
    if (obj.attr('data-jsc-ajax-abort')) {
        abort = obj.attr('data-jsc-ajax-abort');
    }

//	params.push({name: '_csrf', value: yii.getCsrfToken()});

    execAjax(url, params, loader, method, 'json', abort);
}
