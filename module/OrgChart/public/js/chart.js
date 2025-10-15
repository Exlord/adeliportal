$(document).ready(function () {


    switch (parseInt(chartNode.viewTypeNode)) {
        case 1:
            //view at other page
            $('#orgChart').on('click', '.view-node-chart', function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                window.open(url);
            });
            //end
            break;
        case 2:
            //popup view
            $('#orgChart').on('click', '.view-node-chart', function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                $('.org-chart-view-popup').dialog({
                    autoOpen: true,
                    modal: true,
                    height: 500,
                    width: 500,
                    top: 100,
                    close: function (event, ui) {
                        $(this).dialog("destroy");
                        $(".org-chart-view-popup").hide();
                        $(".org-chart-view-popup").html('');
                    }
                });
                $(".org-chart-view-popup").html('<img class="view-popup-loading" src="' + chartNode.imgLoading + '" />').load(url);
            });
            //end
            break;
        case 3:
            //view down
            $('#orgChart').on('click', '.view-node-chart', function (e) {
                e.preventDefault();
                $(".org-chart-view-popup").slideUp();
                var tag = $(this);
                tag.addClass('ajax-loading-inline-select');
                var url = $(this).data('url');
                $(".org-chart-view-popup").load(url, function () {
                    $(this).slideDown();
                    tag.removeClass('ajax-loading-inline-select');
                });
            });
            //end
            break;
    }


});