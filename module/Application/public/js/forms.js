/**
 * Created with PhpStorm.
 * User: Exlord
 * Date: 11/2/13
 * Time: 10:27 AM
 * To change this template use File | Settings | File Templates.
 */
function NumOnly(el) {
    $(el).each(function () {
        $(this).keydown(function (event) {
            // Allow only backspace and delete and colon
            if (
                event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 190 || event.keyCode == 110 || event.keyCode == 9 ||
                    // Ensure that it is a number and stop the keypress
                    (event.keyCode >= 96 && event.keyCode <= 105) ||
                    (event.keyCode >= 48 && event.keyCode <= 57)
                ) {
                // let it happen, don't do anything
            }
            else {
                event.preventDefault();
            }
        });
    });
}


//-------------------- for num only and price -----------------------

function NotEmpty(el) {
    $(el).change(function () {
        if ($(this).val() == '' || $(this).val().length == 0) {
            $(this).val(0);
        }
    });
}

function intFormat(n, obj) {
    var regex = /(\d)((\d{3},?)+)$/;
    n = n.split(',').join('');

    while (regex.test(n)) {
        n = n.replace(regex, '$1,$2');
    }
    obj.value = n;
}


function numFormat(obj) {
    var n = obj.value;
    var result = true;
    if (result == true) {
        var pointReg = /([\d,\.]*)\.(\d*)$/, f;
        if (pointReg.test(n)) {
            f = RegExp.$2;
            return intFormat(RegExp.$1, obj) + '.' + f;
        }
        return intFormat(n, obj);
    }
}


function removecomma(vals) {
    return vals.split(',').join('');
}

function insertcomma(vals) {
   vals = vals.toString();
    var regex = /(\d)((\d{3},?)+)$/;
    while (regex.test(vals)) {
        vals = vals.replace(regex, '$1,$2');
    }
    return vals;
}
