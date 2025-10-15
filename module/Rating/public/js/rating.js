/**
 * Created with JetBrains PhpStorm.
 * User: Ali
 * Date: 10/17/13
 * Time: 2:47 PM
 * To change this template use File | Settings | File Templates.
 */
var StarRating = {
    iconLoader: $('<span class="icon-ajax-loader"></span>'),
    readOnly: false,
    run: function (id) {
        var userId = parseInt(Rating.userId);
        if (!userId)
            this.readOnly = true;

        $('#star-rater-' + id).raty({
            cancel: false,
            readOnly: StarRating.readOnly,
            score: Rating.instances[id].average,
            number: 10,
            path: Rating.imagesPath,
            half: true,
            hints: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            width: 230,
            target: '#star-rater-' + id + ' .rating-hint',
            targetType: 'number',
            targetKeep: true,
            click: function (score, evt) {
                if (userId) {
                    $('#star-rater-' + id + ' .star-rating').append(StarRating.iconLoader);
                    var data = {};
                    var dataScore = score;
                    data['rateScore'] = score;
                    data['entityType'] = Rating.instances[id].entityType;
                    data['entityId'] = Rating.instances[id].entityId;
                    data['entityId2'] = Rating.instances[id].entityId2;
                    data['userId'] = userId;
//                data['date'] = Rating.time;

                    $.ajax({
                        url: Rating.url,
                        type: 'POST',
                        data: data,
                        complete: function () {
                            StarRating.iconLoader.remove();
                        },
                        success: function (data) {
                            if (data.status) {
                                $('#star-rater-' + id + ' .star-rating').attr('data-score', dataScore);
                                if (Rating.showMyVote)
                                    $('#star-rater-' + id + ' .user-vote-span').html(dataScore);
                            }
                            else {
                                $('#star-rater-' + id + ' .star-rating').raty('reload');
                                if (Rating.showMyVote)
                                    $('#star-rater-' + id + ' .user-vote-span').html(Rating.notSaved);
                            }
                        },
                        error: System.AjaxError
                    });
                }
                else {
                    System.AjaxMessage(Rating.error1);
                }
            }
        });
    }
};
$(document).ready(function () {
    $(document).trigger('star-rating-loaded');
});