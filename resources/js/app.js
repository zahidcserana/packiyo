
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./imageuploadify.min')

import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
//
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Font from '@ckeditor/ckeditor5-font/src/font';
import * as ace from 'ace-builds'
import 'ace-builds/src-noconflict/mode-css'
import 'ace-builds/src-noconflict/theme-dracula'

export default class ClassicEditor extends ClassicEditorBase {}

ClassicEditor.builtinPlugins = [
    Essentials,
    Bold,
    Italic,
    Heading,
    Link,
    List,
    Font,
    Alignment
];

ClassicEditor.defaultConfig = {
    fontSize: {
        options: [
            9,
            10,
            11,
            12,
            13,
            14,
            'default',
        ]
    },
    toolbar: {
        items: [
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'alignment',
            'heading',
            'undo',
            'fontSize',
            'redo'
        ]
    }
    ,
    language: 'en'
};


window.GridStack = require('gridstack/dist/gridstack-h5');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.escapeQuotes = function(text) {
    return text.replace(/"/g, '&quot;');
};

window.app = {
    alert: function (title, message, discardAction = null, iconClass = null) {
        const modal = $('#alert-modal');

        if (title == null) {
            title = modal.find('[data-default-title]').data('default-title');
        }

        if (iconClass == null) {
            iconClass = modal.find('[data-default-icon]').data('default-icon');
        }

        modal.find('.modal-title').text(title);
        modal.find('.modal-icon').addClass(iconClass);
        modal.find('.modal-message').html(message);

        if (typeof discardAction == 'function') {
            modal.on('hide.bs.modal', discardAction);
        }

        modal.modal();
    },

    confirm: function(title, message, confirmAction, confirmButtonText = null, discardAction = null, iconClass = null) {
        const modal = $('#confirm-modal');
        const confirmButton = modal.find('.modal-button-confirm');

        if (title == null) {
            title = modal.find('[data-default-title]').data('default-title');
        }

        if (confirmButtonText == null) {
            confirmButtonText = modal.find('[data-default-confirm-button-text]').data('default-confirm-button-text');
        }

        if (iconClass == null) {
            iconClass = modal.find('[data-default-icon]').data('default-icon');
        }

        modal.find('.modal-title').text(title);
        modal.find('.modal-icon').addClass(iconClass);
        modal.find('.modal-message').html(message);
        modal.find('.modal-button-confirm').text(confirmButtonText);

        confirmButton.off('click');
        modal.off('hide.bs.modal');

        confirmButton.on('click', confirmAction);

        if (typeof discardAction == 'function') {
            modal.on('hide.bs.modal', discardAction);
        }

        modal.modal();
    },

    /**
     * Helper to generate table delete button easily.
     *
     * @param {string} message Message to display the user for confirmation.
     * @param {object} linkDelete Object with delete URL and CSRF token.
     * @returns {string}
     */
    tableDeleteButton: function (message, linkDelete, ajax = false) {
        return `<form action="${linkDelete.url}" method="post" class="d-inline-block ${ajax ? 'ajax-form shows-toasts' : ''}">
                    <input type="hidden" name="_method" value="delete">
                    <input type="hidden" name="_token" value="${linkDelete.token}">
                    <button type="button" class="table-icon-button" data-confirm-message="${escapeQuotes(message)}" data-confirm-button-text="Delete">
                        <i class="picon-trash-filled del_icon icon-lg" title="Delete"></i>
                    </button>
                </form>`
    },

    tablePostButton: function (message, textLabel = 'Save', linkPost, btnClass = 'bg-logoOrange', ajax = false) {
        return `<form action="${linkPost.url}" method="post" class="d-inline-block ${ajax ? 'ajax-form shows-toasts' : ''}">
                    <input type="hidden" name="_token" value="${linkPost.token}">
                    <button type="button" class="btn ${btnClass} text-white px-5 font-weight-700" data-confirm-message="${escapeQuotes(message)}" data-confirm-button-text="Yes">
                        ${escapeQuotes(textLabel)}
                    </button>
                </form>`
    },
    /**
     * Helper to generate attributes string from object. Class attribute is ignored.
     *
     * @param {object} attributes Object with html attributes and values.
     * @returns {string}
     */
    prepareAttributes: function (attributes) {
        let preparedAttributes = ''

        for (const [attribute, value] of Object.entries(attributes)) {
            if (attribute === 'class') {
                continue;
            }

            preparedAttributes += `${attribute}="${escapeQuotes(value)}"`
        }

        return preparedAttributes
    },

    initTags: function () {
        $('.select-ajax-tags').not('.select2-hidden-accessible').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            ajax: {
                processResults: function (data) {
                    return  {
                        results: data.results,
                    }
                },
                data: function (params) {
                    var term = (/\s/).test(params.term) ? null : params.term;

                    return  {
                        term: term
                    }
                },
            },
            createTag: function (params) {
                var term = $.trim(params.term).replace(/\s/g,'');

                if (term === '' && term.length < 3) {
                    return null;
                }

                return {
                    id: term,
                    text: term
                }
            }
        });
    }
}

window.datatableGlobalLanguage = {
    paginate: {
        next:
            `<div class="d-flex"><i class="picon-arrow-forward-filled icon-lg"></i></div>`,
        previous:
            `<div class="d-flex"><i class="picon-arrow-backward-filled icon-lg"></i></div>`,
    }
}

$(function() {
    app.initTags();

    $(document).on('submit', 'form.ajax-form', function(event) {
        const form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function(data) {
                if (form.hasClass('shows-toasts')) {
                    toastr.success(data.message);
                }

                form.trigger('packiyo:ajax-success', data);
            },
            error: function(error) {
                if (form.hasClass('shows-toasts')) {
                    toastr.error(error.responseJSON.errors.join('<br />'));
                }

                form.trigger('packiyo:ajax-error', error);
            }
        });

        event.preventDefault();
    });

    $(document).on('click', '[data-alert-message]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let clickedElement = $(this);
        let title = clickedElement.data('alert-title');
        let message = decodeURI(clickedElement.data('alert-message'));
        let iconClass = clickedElement.data('alert-icon-class');

        app.alert(title, message, null, null, iconClass);
    });

    $(document).on('click', '[data-confirm-message]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let clickedElement = $(this);
        let title = clickedElement.data('confirm-title');
        let message = clickedElement.data('confirm-message');
        let confirmButtonText = clickedElement.data('confirm-button-text');
        let iconClass = clickedElement.data('confirm-icon-class');
        let href = clickedElement.attr('href');

        let confirmAction = function() {
            location.replace(href);
        };

        if (!href) {
            confirmAction = function() {
                clickedElement.closest('form').submit();
            }
        }

        app.confirm(title, message, confirmAction, confirmButtonText, null, iconClass);
    });

    $(document).on('click', '#filter-icon', function () {
        $('#toggleFilterForm').toggleClass('collapse');
    })

    const anchor = window.location.hash;
    $(`a[href="${anchor}"]`).tab('show');

});

function routeFix(element) {
    let ajaxUrl = $(element).data('ajax--url');
    let selectedValue = element.value;

    let url = ajaxUrl + '?term=' + selectedValue;
    let optionText = element.options[0] !== undefined ? element.options[0].text.length : 0;

    if (ajaxUrl && optionText === 0 && element.value.length !== 0) {

        let select = element
        $.get(url, function(data, status){
            let results = data.results;

            select.append(new Option(results[0].text, results[0].id, 'selected', 'selected'));
        });
    }
}

$("[data-toggle='select']").each(function(i, item) {
    routeFix(item);
   const fixRouteAfter = $(item).data('fixRouteAfter')
   if (fixRouteAfter) {

       $(fixRouteAfter).on('ajaxSelectOldValueUrl:toggle', function() {
           routeFix(item)
       });
   }
});

$(document).on('click', '.logoutBtn', function(event) {
    event.preventDefault();
    event.stopPropagation();

    $('#logout-form').submit();
});

window.editors = [];

window.ckeditor = function () {
    $('#editor, .editor').each(function() {
        ClassicEditor
            .create(this)
            .then(editor => {
                window.editors.push(editor);
            })
            .catch(error => {
                console.error(error);
            })
    });
}

window.updateCkEditorElements = () => {
    window.editors.forEach(editor => {
        editor.updateSourceElement();
    })
}

$(document).ready(function () {
    calculateDropdownTopPosition()
    $('#uploadify').imageuploadify();

    $('.change-tab').click(function () {
        $($(this).data('id')).trigger('click')
    })
})

$('#sidenav-main div').scroll(function () {
    calculateDropdownTopPosition()
})

function calculateDropdownTopPosition() {
    $('[data-dropdown=true]').each(function () {
        $(this).css({
            'top': $(this).parent().position().top,
            'left': ($('#sidenav-main').width() + 10),
        })
    })
}

$('[data-dropdown=true]').parent().click(function (event) {
    if($(window).width() >= 1199) {
        if (!$(event.target).closest('[data-dropdown=true]').length) {
            event.preventDefault()
            calculateDropdownTopPosition();
            $(this).find('[data-dropdown=true]').toggleClass('d-none')
        }
    }
})

$(document).click(function (event) {
    if (!$(event.target).closest('[data-dropdown-parent]').length) {
        $(this).find('[data-dropdown=true]').addClass('d-none')
    }
})

$('[data-positioned=true]').on('show.bs.modal', function (e) {
        let button = $(e.relatedTarget);
        if($(window).width() > 575) {
            let align = 0;
            if (button.hasClass('modal-align-right')) {
                align = 340
            }

            $(button.data('target')).find('.modal-dialog')
                .css({
                'top' : (button.offset().top - $(window).scrollTop() + 20) + "px",
                'left' : 'calc( 100% - (' + ($(document).width() - (button.offset().left + button.width() + align) - 20) + 'px))',
                'transform': 'translateX(-100%)',
            }).removeClass('d-none');
        } else {
            $(button.data('target')).find('.modal-dialog')
                .css({
                'top' : (button.offset().top - $(window).scrollTop() + 20) + "px",
                'left' : 'calc( 100% - (' + 0 + 'px))',
                'transform': 'translateX(-100%)',
            }).removeClass('d-none');
        }
})

$(document).ready(function (event) {
    if($(window).width() <= 1200) {
        $('.sub-menu').removeAttr('data-dropdown-parent');
        $('.third_menu').removeAttr('data-dropdown');
    } else {
        $('.sub-menu .nav-link').removeAttr('data-toggle');
    }

    $(document).on('select2:open', () => {
        document.querySelector('.select2-container--open .select2-search__field').focus()
    })

    var timezone = moment.tz.guess();
    // Add default timezone
    let form = $('.loginForm');
    if(form.find('input[name=timezone]').val() == '') {
        form.find('input[name=timezone]').val(timezone);
    }
})

$(document).on('click', '.editFormContent', function() {
    $('.formsContainer').find('form.editable').removeClass('editable')

    let form = $(this).closest('form')

    form.addClass('editable')
    form.find('.saveSuccess').addClass('d-none').css('display', 'none')
    form.find('.saveError').addClass('d-none').css('display', 'none')
    form.find('.editSelect').removeClass('d-none').addClass('d-flex')
    form.find('.editSelect').closest('.productForm').find('.empty').css('display', 'none')
})

$.fn.focusWithoutScrolling = function() {
    let x = window.scrollX
    let y = window.scrollY

    this.focus()

    window.scrollTo(x, y)

    return this
}

$.fn.debounce = function(callback, timeout = 500) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { callback.apply(this, args); }, timeout);
    };
}

$(document).ready(function () {
    // Create a sibling element that will catch focus when tab is clicked
    $('.focus-thief').after(
        // $('<input />').addClass('focus-thief-sidekick')
    ).focusWithoutScrolling();

    $(document).on('focus', '.focus-thief-sidekick', () => {
        $('.focus-thief').focusWithoutScrolling();
    });

    $(document).on('blur', ':input', function (e) {
        let nodeTypes = ['INPUT', 'BUTTON', 'A']

        if (e.relatedTarget == null || ! nodeTypes.includes(e.relatedTarget.nodeName)) {
            if (! $(e.relatedTarget).hasClass('select2-selection')) {
                setTimeout(function () {
                    // $('.focus-thief').focusWithoutScrolling()
                }, 200)
            }
        }
    });

    $('.dropup').on('show.bs.dropdown', function (){
        $('.navbar-collapse').find('.nav-item').each(function (key, e) {
            let item = $(e)
            if (item.find('.collapse.show').length) {
                if (! (item.find('.active_item').length)){
                    item.find('.nav-link').attr('aria-expanded', false)
                }
                item.find('.collapse.show').removeClass('show')
            }
        })
        if (! ($(this).find('.collapse .active_item').length)) {
            $(this).find('.nav-link').attr('aria-expanded', false)
        }
    })

    if($(window).width() < 1199) {
        $('body').removeClass('g-sidenav-show').removeClass('g-sidenav-pinned');
    }

    $("body").tooltip({ selector: '[data-toggle=tooltip]' });

    $(document).ajaxError(function(event, jqXhr, settings, exception) {
        if (exception == 'Unauthorized') {
            // TODO: replace with a nice alert
            window.app.alert('Session expired, please log in again.');
            window.location.href = '/';
        }
    });

    // On first hover event make popover and then AJAX content into it.
    $('[data-load-popover]').hover(
        function (event) {
            var el = $(this);
            var loadingImg = `<div class="spinner small">
            <img src="../../img/loading.gif">
            </div>`;

            // disable this event after first binding
            el.off(event);

            // add initial popovers with LOADING text
            el.popover({
                content: loadingImg,
                html: true,
                container: 'body',
                trigger: 'hover',
                sanitize: false,
                placement: function() { return $(window).width() < 992 ? 'auto' : 'left'; }
            });

            // show this LOADING popover
            el.popover('show');

            // Find selected element add in popover content body
            el.data('bs.popover').config.content = $(el.data('load-popover')).html()
            el.popover('show');
        },
        // Without this handler popover flashes on first mouseout
        function() { }
    );
})