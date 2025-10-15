<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace GeographicalAreas;

use GeographicalAreas\Model\CityTable;
use GeographicalAreas\Model\CountryTable;
use GeographicalAreas\Model\StateTable;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    const GEOGRAPHICAL_AREAS = 'route:admin/geographical-areas';

    const GEOGRAPHICAL_AREAS_COUNTRY = 'route:admin/geographical-areas/country';
    const GEOGRAPHICAL_AREAS_COUNTRY_NEW = 'route:admin/geographical-areas/country/new';
    const GEOGRAPHICAL_AREAS_COUNTRY_UPDATE = 'route:admin/geographical-areas/country/update';
    const GEOGRAPHICAL_AREAS_COUNTRY_EDIT = 'route:admin/geographical-areas/country/edit';
    const GEOGRAPHICAL_AREAS_COUNTRY_DELETE = 'route:admin/geographical-areas/country/delete';

    const GEOGRAPHICAL_AREAS_STATE = 'route:admin/geographical-areas/state';
    const GEOGRAPHICAL_AREAS_STATE_NEW = 'route:admin/geographical-areas/state/new';
    const GEOGRAPHICAL_AREAS_STATE_UPDATE = 'route:admin/geographical-areas/state/update';
    const GEOGRAPHICAL_AREAS_STATE_EDIT = 'route:admin/geographical-areas/state/edit';
    const GEOGRAPHICAL_AREAS_STATE_DELETE = 'route:admin/geographical-areas/state/delete';

    const GEOGRAPHICAL_AREAS_CITY = 'route:admin/geographical-areas/city';
    const GEOGRAPHICAL_AREAS_CITY_NEW = 'route:admin/geographical-areas/city/new';
    const GEOGRAPHICAL_AREAS_CITY_UPDATE = 'route:admin/geographical-areas/city/update';
    const GEOGRAPHICAL_AREAS_CITY_EDIT = 'route:admin/geographical-areas/city/edit';
    const GEOGRAPHICAL_AREAS_CITY_DELETE = 'route:admin/geographical-areas/city/delete';

    const GEOGRAPHICAL_AREAS_CITY_AREA = 'route:admin/geographical-areas/area';
    const GEOGRAPHICAL_AREAS_CITY_AREA_NEW = 'route:admin/geographical-areas/area/new';
    const GEOGRAPHICAL_AREAS_CITY_AREA_UPDATE = 'route:admin/geographical-areas/area/update';
    const GEOGRAPHICAL_AREAS_CITY_AREA_EDIT = 'route:admin/geographical-areas/area/edit';
    const GEOGRAPHICAL_AREAS_CITY_AREA_DELETE = 'route:admin/geographical-areas/area/delete';
}