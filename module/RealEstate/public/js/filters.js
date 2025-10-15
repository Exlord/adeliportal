/**
 * Created with JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/11/13
 * Time: 10:10 AM
 * To change this template use File | Settings | File Templates.
 */

function onFilterRegTypeChanged(el) {
    switch (parseInt(el)) {
        case 2 :
            $('#filter_mortgagePrice').parent().parent().show();
            $('#filter_rentalPrice').parent().parent().show();
            $('#filter_totalPrice_from').parent().parent().hide();
            $('#filter_totalPrice_to').parent().parent().hide();
            break;
        default :
            $('#filter_mortgagePrice').parent().parent().hide();
            $('#filter_rentalPrice').parent().parent().hide();
            $('#filter_totalPrice_from').parent().parent().show();
            $('#filter_totalPrice_to').parent().parent().show();
            break
    }
}