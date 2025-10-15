/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/3/14
 * Time: 3:08 PM
 */
$(document).ready(function () {
    var Note = {
        forms: {},
        entityId: 0,
        entityType: null,
        newUrl: null,
        template: null,
        dialog: null,
        deleteDialog: null,
        loading: " <span class='glyphicon ajax-loading-inline' style='width:16px;height:16px;'></span>",
        init: function () {

            Note.newUrl = NoteDefaults.newUrl;
            Note.entityId = NoteDefaults.entityId;
            Note.entityType = NoteDefaults.entityType;
            Note.template = NoteDefaults.template;

            //show add note form
            $('body')
                .on('click', '#add-note', function (e) {
                    e.preventDefault();
                    var entityType = $(this).data('entitytype');
                    if (typeof Note.forms[entityType] == 'undefined')
                        Note.__loadForm.call(this);
                    else
                        Note.__showForm.call(this);
                })
                .on('submit', '#note_form', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var data = $(this).serializeObject();
                    data.entityId = Note.entityId;
                    data.entityType = Note.entityType;
                    data['buttons[submit]'] = 1;

                    var btn = $(this).find('button[type=submit]')
                        .append(Note.loading)
                        .addClass('disabled');

                    var form = $(this);

                    var note = $('<div></div>').html(data.note).text();

                    var id = data.id;

                    $.ajax({
                        type: 'POST',
                        url: form.attr('action'),
                        data: data,
                        complete: function () {
                            $(btn).removeClass('disabled').find('.ajax-loading-inline').remove();
                        },
                        error: System.AjaxError,
                        success: function (data) {
                            if (typeof data == 'object' && data.result == 'success') {
                                $(Note.dialog).dialog('close');
                                if (id == 0) {
                                    note = Note.template.replace('__NOTE__', note);
                                    //js only replaces the first find
                                    note = note.replace('__ID__', data.id);
                                    note = note.replace('__ID__', data.id);
                                    note = note.replace('__ID__', data.id);
                                    $('#notes-list').prepend(note);
                                }
                                else
                                    $('#note-' + id + ' .note-text').text(note);
                            }
                            else
                                form.closest('#note-form-container').replaceWith($(data));
                        }
                    });
                })
                .on({
                    mouseenter: function () {
                        $(this).find('.note-commands').show();
                    },
                    mouseleave: function () {
                        $(this).find('.note-commands').hide();
                    }
                }, '#notes-list .media')
                .on('click', '#notes-list .edit-note', function (e) {
                    e.preventDefault();
                    Note.__edit.call(this);
                })
                .on('click', '#notes-list .delete-note', function (e) {
                    e.preventDefault();
                    Note.__confirmDelete.call(this);
                })
            ;
        },
        __loadForm: function () {
            $(this)
                .append(Note.loading)
                .addClass('disabled');
            var btn = $(this);

            $.ajax({
                type: 'POST',
                url: Note.newUrl,
                data: {'entityId': Note.entityId, 'entityType': Note.entityType},
                complete: function () {
                    $(btn).removeClass('disabled').find('.ajax-loading-inline').remove();
                },
                error: System.AjaxError,
                success: function (data) {
                    Note.forms[Note.entityType] = $(data);
                    Note.__showForm();
                }
            });
        },
        __showForm: function () {
            Note.dialog = $('<div></div>').html(Note.forms[Note.entityType]).dialog({
                modal: true,
                width: '400px'
            });
        },
        __confirmDelete: function () {
            var id = $(this).data('id');
            var url = $(this).data('url');
            var btn = $(this);

//            if (Note.deleteDialog == null) {
            Note.deleteDialog = $("<div></div>").html(NoteDefaults.deleteMessage).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                closeText: NoteDefaults.closeText,
                close: function (event, ui) {
                    $(this).dialog("close");
                },
                buttons: [
                    {
                        text: NoteDefaults.yes,
                        icons: { primary: "ui-icon-check"},
                        click: function () {
                            var grid_dialog_object = $(this);
                            $('.ui-dialog-content').append(Note.loading);
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {id: id},
                                complete: function () {
                                    $('.ui-dialog-content').find('span').remove();
                                    grid_dialog_object.dialog("close");
                                },
                                success: function (data) {
                                    if (typeof data == 'object' && data.status == 1) {
                                        $(btn).closest('.media').fadeOut(function () {
                                            $(this).remove();
                                        });
                                    } else
                                        System.AjaxMessage(data);
                                },
                                error: System.AjaxError
                            });
                        }
                    },
                    {
                        text: NoteDefaults.no,
                        icons: { primary: "ui-icon-closethick"},
                        click: function () {
                            $(this).dialog("close");
                        }
                    },
                ]
            });
//            }
            $(Note.deleteDialog).dialog('open');
        },
        __edit: function () {
            var btn = $(this).addClass('disabled').prepend(Note.loading);
            $.ajax({
                type: 'POST',
                url: btn.data('url'),
                data: {'entityId': Note.entityId, 'entityType': Note.entityType},
                complete: function () {
                    $(btn).removeClass('disabled').find('.ajax-loading-inline').remove();
                },
                error: System.AjaxError,
                success: function (data) {
                    Note.dialog = $('<div></div>').html(data).dialog({
                        modal: true,
                        width: '400px'
                    });
                }
            });
        }
    };
    Note.init();
});