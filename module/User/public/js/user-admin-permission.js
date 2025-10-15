var Permissions = {
    initialized: false,
    activeSections: {},
    init: function () {
        if (!this.initialized) {
            var permission_table_active = $.cookie("permission_table_active");
            if (permission_table_active && permission_table_active.length) {
                //the value is a serialized json
                if (permission_table_active[0] == '{')
                    Permissions.activeSections = JSON.parse(permission_table_active);
                //the value is not json, its from the old system just on id
                else
                    Permissions.activeSections[permission_table_active] = permission_table_active;
            }
            Permissions.toggleSections();

            $('body')
                .on('click', '.permissions-table table thead', function () {
                    var id = $(this).parent().prop('id');
                    Permissions.activeSections[id] = id;
                    Permissions.onPermTableClick(id);
                })
                .on('click', '.perm-select', function () {
                    if (!$(this).hasClass('active')) {
                        var parent = $(this).closest('td');
                        var name = $(this).prop('name');
                        var value = parseInt($(this).data('value'));
                        var button = $(this);
                        parent.addClass('ajax-disabled');
                        var data = {};
                        data[name] = value;

                        $.ajax({
                            url: permission_change_url,
                            type: 'POST',
                            data: data,
                            success: function (data) {
                                parent.removeClass('ajax-disabled');
                                $('button', parent).each(function () {
                                    $(this).removeClass($(this).data('class'));
                                });
                                $(button).addClass($(button).data('class'));

                                perms = $.parseJSON(data.perms);
                                Permissions.reCalcPerms(perms);
                                if (data.status == 0) {
                                    System.AjaxMessage(data.msg);
                                }
                                /*if (data.status != 'invalid') {
                                 $(deny_el).removeClass('hidden');
                                 $(allow_el).removeClass('hidden');
                                 }
                                 if (data.status == true)
                                 deny_el.addClass('hidden');
                                 else if (data.status == false)
                                 allow_el.addClass('hidden')*/
                            },
                            error: System.AjaxError
                        });
                    }
                });

            this.initialized = true;
        }
    },
    toggleSections: function () {
        $.each(Permissions.activeSections, function (id, index) {
            $('#' + id + ' tbody').toggle();
        });
    },
    onPermTableClick: function (id) {
        var tbody = $('#' + id + ' tbody');
        tbody.toggle();
        var status = tbody.css('display');
        if (status != 'none')
            Permissions.activeSections[id] = id;
        else
            delete Permissions.activeSections[id];

        $.cookie("permission_table_active", JSON.stringify(Permissions.activeSections));
    },
    reCalcPerms: function (perms) {
        //console.log(perms);
        $.each(perms, function (id, status) {
            var pd = $('#' + id + ' .permission-denied');
            var pa = $('#' + id + ' .permission-allowed');
            $(pd).removeClass('hidden');
            $(pa).removeClass('hidden');
            if (status == true)
                $(pd).addClass('hidden');
            else if (status == false)
                $(pa).addClass('hidden');
        });
    }
};
