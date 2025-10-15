/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/26/14
 * Time: 10:46 AM
 */
$(document).ready(function () {
    var ie8_warning = "<div class='bg-danger' style='padding:5px;z-index:9999;'>" +
        "اینترنت اکسپلورر ناامنترین، کندترین و ضعیفترین مرورگر اینترنی میباشد. برای حفظ امنیت خود و دیگران و مرور صفحات با سرعت بیشتر و استفاده از جدیدترین امکانات وب 2، از مرورگر های به روز و قدرتمندی مثل " +
        "<a href='http://www.mozilla.org/en-US/firefox/features/'> فایرفاکس </a>" +
        ", <a href='http://www.google.com/intl/en/chrome/education/browser/user/'> گوگل کروم </a>" +
        " و یا <a href='http://www.opera.com/computer/windows'> اپرا </a>" +
        " استفاده بکنید." +
        "</div>";
    $('body').prepend(ie8_warning);
});