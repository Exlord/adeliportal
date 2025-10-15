/**
 * Created with JetBrains PhpStorm.
 * User: Ali
 * Date: 10/17/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */


$(document).ready(function () {

    var commentText = '';
    var iconLoader = $('<span class="icon-ajax-loader"></span>');
    var tagForAppendNew = '';
    var loadMoreComments = $('.load-more-comments');
    var commentsWrapper = $('body');
    var comments = $('#comments');
    var commentForm = $('#comment-form');
    var allowForNew = true;
    var classCount = 2;
    if (parseInt(Comment.count) > parseInt(Comment.countShowComment))
        loadMoreComments.show();
    else
        loadMoreComments.hide();


    function getClassName(id) {
        if ($('#comment_' + id).attr('class').search('back-comment-1') > -1)
            classCount = 2;
        else
            classCount = 1;
    }

    function hideNegative() {
        $('.rate-sum').each(function () {
            if (parseInt($(this).html()) <= parseInt(Comment.closedShow))
            {
                $(this).parent().parent().siblings('.main-content-comments').hide();
                $(this).parent().parent().siblings('.slide-toggle-icon').removeClass('attach-open-icon').addClass('attach-close-icon');
            }
        });
    }

    function loadComment(data, url, loadingTag, thisTag, type, typeForm) {

        loadingTag.append(iconLoader);
        data['typeComment'] = Comment.typeComment;
        var id = data['id'];
        $.ajax({
            url: url,
            type: typeForm,
            data: data,
            complete: function () {
                iconLoader.remove();
                loadingTag.removeClass('ajax-loading');
            },
            success: function (data) {
                if (type == 'firstLoad') {
                    if (data.html) {
                        thisTag.html(data.html);
                        eval(data.script);
                        hideNegative();
                        thisTag.slideDown(500);
                    } else
                    {
                        $('.footer-wrapper').hide();
                        thisTag.html(Comment.noComment);
                        thisTag.slideDown(500);
                    }
                }
                if (type == 'showMore') {
                    if (parseInt(Comment.startOffset) + (parseInt(Comment.countShowComment) * 2) >= parseInt(Comment.count))
                        $('.load-more-comments').hide();
                    comments.append(data.html);
                    eval(data.script);
                    hideNegative();
                    thisTag.slideDown(500);
                }
                if (type == 'new-reply') {
                    if (data.status) {
                        if (data.commentStatus) {
                            loadingTag.dialog('close');
                            tagForAppendNew.append(data.html);
                            eval(data.script);
                            $('#comment_' + data.id).addClass('back-comment-' + classCount);
                            tagForAppendNew.slideDown(500, function () {
                                $("html,body").animate({scrollTop: $('#comment_' + data.id).offset().top + 20 }, 1000);
                            });
                            allowForNew = false;
                            setTimeout(function () {
                                allowForNew = true;
                            }, 60 * 1000);
                        }
                        else {
                            System.AjaxMessage(Comment.confirmMessage);
                            loadingTag.dialog('close');
                        }
                    }
                    else
                        commentForm.html(data);
                }
                if (type == 'open-new-reply') {
                    thisTag.html(data);
                }
                if (type == 'replies') {
                    thisTag.html(data.html);
                    eval(data.script);


                    thisTag.slideToggle(500);
                }
                if (type == 'delete') {
                    if (data.status == 0)
                        alert("error");
                    else {
                        thisTag.dialog("close");
                        loadingTag.slideUp(500);
                    }
                }
                if (type == 'editComment') {
                    if (data.status == 0) {
                        $('#comment_content_' + id).html(commentText);
                        System.AjaxMessage(Comment.error);
                    }
                    else {
                        $('#comment_content_' + id).html(data.text);
                        $('#comment_' + id + ' .comment-buttons').show();
                    }
                }
            },
            error: System.AjaxError
        });
    }


    var data = {};
    data['entityId'] = Comment.entityId;
    data['entityType'] = Comment.entityType;
    data['startOffset'] = 0;
    data['level'] = 1;
    loadComment(data, Comment.urlShowComment, commentsWrapper, comments, 'firstLoad', 'post');


    commentsWrapper
        .on('click', '.load-more-comments', function () {
            var data = {};
            data['entityId'] = Comment.entityId;
            data['entityType'] = Comment.entityType;
            data['level'] = 1;
            data['startOffset'] = parseInt(Comment.startOffset) + parseInt(Comment.countShowComment);
            loadComment(data, Comment.urlShowComment, $(this), comments, 'showMore', 'post');
        })
        .on('click', '.new-reply', function () {
            if (parseInt(Comment.current_user_id) != 0) {
                if (allowForNew) {
                    var Div = $(this).parent();//TODO ba un yeki yeki shavad yeki
                    commentForm.addClass('ajax-loading');
                    commentForm.html('');
                    var parentId = parseInt($(this).data('id'));
                    $("#comment_dialog").dialog({
                        resizable: false,
                        height: 400,
                        width: 400,
                        modal: true,
                        title: Comment.title,
                        buttons: [
                            {
                                text: Comment.saveBTN,
                                icons: {},
                                click: function () {
                                    $('#entityId').val(Comment.entityId);
                                    $('#entityType').val(Comment.entityType);
                                    $('#status').val(Comment.commentStatus);

                                    if (parentId) {
                                        getClassName(parentId);
                                        tagForAppendNew = $('#comment_' + parentId);
                                        $('#parentId').val(parentId);
                                    }
                                    else {
                                        classCount = 1;
                                        tagForAppendNew = comments;
                                        $('#parentId').val(0);
                                    }

                                    var data = $('#comment_form').serialize();
                                    loadComment(data, Comment.url, $(this), '', 'new-reply', 'post');
                                }
                            }
                        ]
                    });
                    loadComment('', Comment.url, commentForm, commentForm, 'open-new-reply', 'get');
                } else
                    System.AjaxMessage(Comment.timeOut);
            } else
                System.AjaxMessage(Comment.notLogin);
        })
        .on('click', '.replies', function () {
            var parentId = parseInt($(this).data('id'));
            if ($('#showReply' + parentId).html())
                $('#showReply' + parentId).slideToggle(500);
            else {
                getClassName(parentId);
                var data = {};
                data['parentId'] = parentId;
                data['level'] = 0;
                data['entityType'] = Comment.entityType;
                data['entityId'] = Comment.entityId;
                data['className'] = 'back-comment-' + classCount;
                loadComment(data, Comment.urlShowComment, $(this), $('#showReply' + parentId), 'replies', 'post');
            }
        })
        .on('click', 'a.delete-comment', function () {
            var id = $(this).data('id');
            $("#comment_dialog_delete").dialog({
                resizable: false,
                height: 200,
                width: 400,
                modal: true,
                title: Comment.titleDelete,
                buttons: [
                    {
                        text: Comment.yes,
                        icons: {},
                        click: function () {
                            var data = {};
                            data['id'] = id;
                            loadComment(data, Comment.urlDeleteComment, $('#comment_' + id), $(this), 'delete', 'post');
                        }
                    },
                    {
                        text: Comment.no,
                        icons: {},
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                ]
            });

        })
        .on('click', '#open_edit_comment_text', function () {
            $('.comment-div').each(function () {
                var element = $(this).find('.comment_text_cancel');
                if (element)
                    element.click();
            });
            $(this).parent().hide();
            var id = $(this).data('id');
            var tagP = $('#comment_content_' + id);
            var text = tagP.text().trim();
            commentText = text;
            tagP.html('<textarea id="comment_text_' + id + '">' + text + '</textarea><br/><a href="#cancel" class="comment_text_cancel" id="comment_text_cancel" data-id="' + id + '">' + Comment.cancel + '</a><a href="#edit" id="comment_text_edit" class="comment_text_edit" data-id="' + id + '">' + Comment.edit + '</a>')
        })
        .on('click', '#comment_text_cancel', function () {
            var id = $(this).data('id');
            $(this).parent().html(commentText);
            $('#comment_' + id + ' .comment-buttons').show();
        })
        .on('click', '#comment_text_edit', function () {
            var id = $(this).data('id');
            var html = $('#comment_text_' + id).val();
            var data = {};
            data['id'] = id;
            data['comment'] = html;
            var url = Comment.urlEditComment;
            loadComment(data, url, $(this), $(this), 'editComment', 'POST');
        })
        .on('click', '.slide-toggle-icon', function () {
            if ($(this).attr('class').search('attach-open-icon') > -1)
                $(this).removeClass('attach-open-icon').addClass('attach-close-icon');
            else
                $(this).removeClass('attach-close-icon').addClass('attach-open-icon');
            $(this).next().slideToggle(500);
        })


});


//$(document).ready(function () {
//
//    function loadComment(data, url, loadingTag, thisTag, type, typeForm) {
//        console.log(thisTag);
//        loadingTag.addClass('ajax-loading-inline');
//        data['typeComment']=Comment.typeComment;
//        $.ajax({
//            url: url,
//            type: typeForm,
//            data: data,
//            complete: function () {
//                loadingTag.removeClass('ajax-loading-inline');
//            },
//            success: function (data) {
//                if (type == 'firstLoad') {
//                    if(data.html)
//                    {
//                        thisTag.html(data.html);
//                        eval(data.script);
//                        thisTag.slideDown(500);
//                    }else
//                        $('.footer-wrapper').hide();
//
//                }
//                if (type == 'showMore') {
//                    if (parseInt(Comment.startOffset) + (10 * 2) >= parseInt(Comment.count))
//                        $('.load-more-comments').hide();
//                    comments.append(data.html);
//                    eval(data.script);
//                    thisTag.slideDown(500);
//                }
//                if (type == 'newComment' || type == 'new-reply') {
//                    if (data.status)
//                    {
//                        if(data.commentStatus)
//                        {
//                            loadingTag.dialog('close');
//                            $('#new-comments').append(data.html);
//                            eval(data.script);
//                            $('#new-comments').slideDown(500, function () {
//                               // $("html,body").animate({scrollTop: $('#comment_' + data.id).offset().top }, 1000);
//                            });
//                        }
//                        else
//                            System.AjaxMessage('please wait ta modir taiid konad');
//                    }
//                    else
//                    comments.html(data);
//                }
//                if (type == 'openComment' || type == 'open-new-reply') {
//                    thisTag.html(data);
//                }
//                if (type == 'replies') {
//                    thisTag.html(data.html);
//                    eval(data.script);
//                    thisTag.slideToggle(500);
//                }
//                if (type == 'replied-to') {
//                    if (data.status == 0) {
//                        thisTag.html(Comment.errorDelete);
//                        thisTag.slideToggle(500);
//                    }
//                    else {
//                        thisTag.html(data.html);
//                        eval(data.script);
//                        thisTag.slideToggle(500);
//                    }
//                }
//                if (type == 'delete') {
//                    if (data.status == 0)
//                        alert("error");
//                    else {
//                        thisTag.dialog("close");
//                        loadingTag.slideUp(500);
//                    }
//                }
//            },
//            error: System.AjaxError
//        });
//    }
//
//    var loadMoreComments = $('.load-more-comments');
//    var commentsWrapper = $('#comments-wrapper');
//    var comments = $('#comments');
//    var commentForm = $('#comment-form');
//
//    if (parseInt(Comment.count) > 10)
//        loadMoreComments.show();
//    else
//        loadMoreComments.hide();
//
//
//    var data = {};
//    data['id'] = Comment.entityId;
//    data['entityType'] = Comment.entityType;
//    data['startOffset'] = 0;
//    loadComment(data, Comment.urlShowComment, commentsWrapper, comments, 'firstLoad', 'post');
//
//    commentsWrapper
//        .on('click', '.openComment', function () {
//            commentForm.addClass('ajax-loading');
//            commentForm.html('');
//            $("#comment_dialog").dialog({
//                resizable: false,
//                height: 450,
//                width: 400,
//                modal: true,
//                title: Comment.title,
//                buttons: [
//                    {
//                        text: Comment.saveBTN,
//                        icons: {},
//                        click: function () {//TODO disable button after click
//                            $('#entityId').val(Comment.entityId);
//                            $('#entityType').val(Comment.entityType);
//                            var data = $('#comment_form').serialize();
//                            loadComment(data, Comment.url, $(this), $('#comment_dialog'), 'newComment', 'post');
//                        }
//                    }
//                ]
//            });
//            loadComment('', Comment.url, commentForm, commentForm, 'openComment', 'get');
//
//        })
//        .on('click', '.load-more-comments', function () {
//            var data = {};
//            data['id'] = Comment.entityId;
//            data['entityType'] = Comment.entityType;
//            data['startOffset'] = parseInt(Comment.startOffset) + 10;
//            loadComment(data, Comment.urlShowComment, comments, comments, 'showMore', 'post');
//        })
//        .on('click', '.new-reply', function () {
//            var Div = $(this).parent().parent();//TODO ba un yeki yeki shavad yeki
//            commentForm.addClass('ajax-loading');
//            commentForm.html('');
//            var parentId = parseInt($(this).data('id'));
//            $("#comment_dialog").dialog({
//                resizable: false,
//                height: 450,
//                width: 400,
//                modal: true,
//                title: Comment.title,
//                buttons: [
//                    {
//                        text:Comment.saveBTN,
//                        icons: {},
//                        click: function () {
//                            $('#entityId').val(Reply[parentId].entityId);
//                            $('#entityType').val(Reply[parentId].entityType);
//                            $('#parentId').val(parentId);
//                            var data = $('#comment_form').serialize();
//                            loadComment(data, Comment.url, $(this), '', 'new-reply', 'post');
//                        }
//                    }
//                ]
//            });
//            loadComment('', Comment.url, commentForm, commentForm, 'open-new-reply', 'get');
//        //    loadComment('', Reply[parentId].url, commentForm, commentForm, 'open-new-reply', 'get');
//        })
//        .on('click', '.replies', function () {
//            var parentId = parseInt($(this).data('id'));
//            if ($('#showReply' + parentId).html())
//                $('#showReply' + parentId).slideToggle(500);
//            else {
//                var data = {};
//                data['parentId'] = parentId;
//                data['entityType'] = Reply[parentId].entityType;
//                loadComment(data, Comment.urlShowComment, $(this), $('#showReply' + parentId), 'replies', 'post');
//            }
//        })
//        .on('click', '.replied-to', function () {
//            var parentId = parseInt($(this).data('id'));
//            if ($('#showReplyTo' + parentId).html()){
//                $('#showReplyTo' + parentId).slideToggle(500);
//            }else {
//                //  $('#showReplyTo' + parentId).addClass('ajax-loading');
//                var data = {};
//                data['idParent'] = Reply[parentId].parentId;
//                data['entityType'] = Reply[parentId].entityType;
//                loadComment(data, Comment.urlShowComment, $('#showReplyTo' + parentId), $('#showReplyTo' + parentId), 'replied-to', 'post');
//            }
//        })
//        .on('click', 'a.delete-comment', function () {
//            var id = $(this).data('id');
//            $("#comment_dialog_delete").dialog({
//
//                resizable: false,
//                height: 200,
//                width: 400,
//                modal: true,
//                title: Comment.titleDelete,
//                buttons: [
//                    {
//                        text: Comment.yes,
//                        icons: {},
//                        click: function () {
//                            var data = {};
//                            data['id'] = id;
//                            loadComment(data, Comment.urlDeleteComment, $('#comment_' + id), $(this), 'delete', 'post');
//                        }
//                    },
//                    {
//                        text: Comment.no,
//                        icons: {},
//                        click: function () {
//                            $(this).dialog("close");
//                        }
//                    }
//                ]
//            });
//
//        });
//});