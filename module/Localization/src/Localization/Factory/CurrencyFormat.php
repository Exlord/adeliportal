<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/30/14
 * Time: 11:19 AM
 */

namespace Localization\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CurrencyFormat implements FactoryInterface{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $currency = new \Localization\View\Helper\CurrencyFormat();
        /* @var $translate callable */
        $translate = $serviceLocator->get('translate');
        $currency->setCurrencyCode($translate('currency_code'));
        $currency->setShouldShowDecimals($translate('currency_show_decimals'));
        return $currency;
    }
}