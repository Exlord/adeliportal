<?php
namespace OnlineOrder\API;

class OnlineOrder
{
    public static $onlineOrderGroups = array(
        '1' => 'عضویت رایگان',
        '2' => 'عضویت برنزی',
        '3' => 'عضویت نقره ای',
        '4' => 'عضویت طلایی',
    );
    public static $onlineOrderCountDomain = array(
        '1' => 0,
        '2' => 2,
        '3' => 3,
        '4' => 4,
    );

    public static $onlineOrderAmount = array(
        '1' => 0,
        '2' => 2000000,
        '3' => 7000000,
        '4' => 10000000,
    );

    public static $onlineOrderItems = array(
        '1' => array(
            'اضافه کردن 10 مورد ملک برای هر کاربر',
            'فقط یک تصویر برای هر مورد',
            'عدم نمایش اطلاعات تماس کاربر برای عموم و نمایش اطلاعات ملک یاب بجای آن',
            'نمایش مورد فقط در جستجوها و بصورت مرتب شده بر حسب تاریخ افزودن مورد',
            'عدم امکان تمدید رایگان مدت آگهی',
            'بدون انتخاب دامنه شخصی',
        ),
        '2' => array(
            'اضافه کردن 99 مورد ملک برای هر کاربر',
            'قرار گیری 3 تصویر برای هر مورد',
            'عدم نمایش اطلاعات تماس کاربر برای عموم و نمایش اطلاعات ملک یاب بجای آن',
            'نمایش در صفحه ی اول جستجو در صورت تطبیق جستجو با مورد',
            'امکان تمدید رایگان مدت آگهی تا 3 دوره ی زمانی',
            'واگذاری برای 2 دامنه شخصی',
        ),
        '3' => array(
            'اضافه کردن ملک بصورت نامحدود',
            'قرارگیری 7 تصویر برای هر مورد',
            'نمایش اطلاعات تماس کاربر در صورت کلیک بر روی مورد ( در صفحه دوم )',
            'نمایش در صفحه ی اول جستجو در صورت تطبیق جستجو با مورد',
            'امکان تمدید رایگان مدت آگهی بصورت نامحدود',
            'واگذاری برای 3 دامنه شخصی',
        ),
        '4' => array(
            'اضافه کردن ملک بصورت نامحدود',
            'قرارگیری 25 تصویر برای هر مورد و نمایش تصاویر بصورت گالری ',
            'نمایش اطلاعات تماس کاربر در همان صفحه نخست جستجوی ملک',
            'نمایش موارد در صفحه ی اول وب سایت بصورت تصادفی',
            'نمایش در صفحات جستجو در صورت تطبیق جستجو با مورد',
            'نمایش در بخش highlight شده ی صفحات جستجو بدون وابستگی به جستجو',
            'عدم نیاز به تمدید آگهی',
            'واگذاری برای 4  دامنه شخصی',
        ),
    );

    //info : true =>show info user false=>dont show info user
    //new : tedade melki ke mitavanad sabt konad.
    //image : tedade image ke mitavanad baraye har melk upload konad.
    public static $globalConfig = array(
        '1' => array('new' => 10, 'image' => 1, 'info' => 0, 'search' => 0),
        '2' => array('new' => 99, 'image' => 3, 'info' => 0, 'search' => 1),
        '3' => array('new' => 0, 'image' => 7, 'info' => 1, 'search' => 1),
        '4' => array('new' => 0, 'image' => 25, 'info' => 2, 'search' => 2),
    );

    //changeBanner : 0=>ejaze taghiire banner nadarad va 1=>ejaze darad
    public static $changeBanner=array(
        '1'=>0,
        '2'=>0,
        '3'=>1,
        '4'=>1,
    );


    public static $moduleList = array(
        'Application',
    );

    public function getOnlineOrderGroups()
    {
        return self::$onlineOrderGroups;
    }

    public function getOnlineOrderItems()
    {
        return self::$onlineOrderItems;
    }

    public function getOnlineOrderModules()
    {
        return self::$moduleList;
    }

    public function getOnlineOrderCountDomain()
    {
        return self::$onlineOrderCountDomain;
    }

    public function getRefCode()
    {
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $refCode = "";
        $length = 5;

        for ($p = 0; $p < $length; $p++) {
            $refCode .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        if (getSM()->get('customer_table')->getAll(array('refCode' => $refCode))->count())
            $this->getRefCode();
        return $refCode;
    }

    public function getGlobalRealEstateConfig()
    {
        return self::$globalConfig;
    }

    public function getOnlineOrderAmount()
    {
        return self::$onlineOrderAmount;
    }

    public function getCahngeBanner()
    {
        return self::$changeBanner;
    }
}
