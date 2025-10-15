<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Ads\API;

use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class Ads extends BaseAPI
{

    public function saveCache()
    {
        $config_newType = getConfig('ads_new_type_config')->varValue;
        $adsConfig = array();
        if ($config_newType) {

            $baseTypeAdsArray = array();
            $baseTypeMachineNameArray = array();
            $baseTypeGoogleMap = array();
            $baseTypeRate = array();
            $baseTypeAmountAllInfo = array();
            $baseTypePriceOneEmail = array();
            $baseTypePriceOneSms = array();

            if (isset($config_newType['base_type_ads']['baseType'])) {


                foreach ($config_newType['base_type_ads']['baseType'] as $row) {

                    $isRequest = $row['isRequest'];
                    $baseTypeId = $row['idBaseTypeAdsValue'];
                    $regTypeArray = array();
                    $regTypeArray[] = 0;
                    if ($isRequest)
                        $regTypeArray[] = 1;

                    foreach ($regTypeArray as $reg_t) {

                        $baseTypeAdsArray[$baseTypeId] = $row['baseTypeAdsValue'];
                        $baseTypeMachineNameArray[$baseTypeId] = $row['baseTypeAdsMachineName'];
                        $baseTypeGoogleMap[$baseTypeId] = $row['isGoogleMap'];
                        $baseTypeRate[$baseTypeId] = $row['rateStatus'];
                        $baseTypeAmountAllInfo[$baseTypeId] = $row['adViewPrice'];
                        $baseTypeRequest[$baseTypeId] = $isRequest;

                        $baseTypePriceOneEmail[$baseTypeId] = 0;
                        $baseTypePriceOneSms[$baseTypeId] = 0;
                        if (isset($row['isRequest']) && $row['isRequest']) {
                            if (isset($row['priceOneEmail']) && $row['priceOneEmail'])
                                $baseTypePriceOneEmail[$baseTypeId] = $row['priceOneEmail'];
                            if (isset($row['priceOneSms']) && $row['priceOneSms'])
                                $baseTypePriceOneSms[$baseTypeId] = $row['priceOneSms'];
                        }


                        if (isset($config_newType['starCount'])) {
                            $adsConfig[$baseTypeId][$reg_t]['starCount'] = $config_newType['starCount'];
                            for ($i = 1; $i <= $config_newType['starCount']; $i++)
                                $adsConfig[$baseTypeId][$reg_t]['starCountArray'][$i] = $i;
                        }
                        if (isset($config_newType['keywordCount'])) {
                            $adsConfig[$baseTypeId][$reg_t]['keywordCount'] = $config_newType['keywordCount'];
                        }
                        if (isset($config_newType['smallImgWidth']) && isset($config_newType['smallImgHeight'])) {
                            $adsConfig[$baseTypeId][$reg_t]['smallImg'] = array(
                                'width' => $config_newType['smallImgWidth'],
                                'height' => $config_newType['smallImgHeight'],
                            );
                        }

                        $adsConfig[$baseTypeId][$reg_t]['baseType'] = array(
                            $baseTypeId => $row['baseTypeAdsValue'],
                        );

                        $secondTypeAdsArray = array();
                        $config_first = getConfig('ads_first_config_' . $baseTypeId . '_' . $reg_t)->varValue;
                        if ($config_first) {
                            $secondTypeAdsArray = array();
                            if (isset($config_first['second_type_ads']['secondType'])) {
                                foreach ($config_first['second_type_ads']['secondType'] as $rowFirst) {
                                    $adsConfig[$baseTypeId][$reg_t]['secondType'][$rowFirst['idSecondTypeAdsValue']] = $rowFirst['secondTypeAdsValue'];
                                    $adsConfig[$baseTypeId][$reg_t]['secondTypeMachineName'][$rowFirst['idSecondTypeAdsValue']] = $rowFirst['secondTypeAdsMachineName'];
                                    $secondTypeAdsArray[$baseTypeId][$reg_t][$rowFirst['idSecondTypeAdsValue']] = $rowFirst['secondTypeAdsValue'];
                                }
                            }
                            if (isset($config_first['time_ads']['timeAds'])) {
                                foreach ($config_first['time_ads']['timeAds'] as $rowFirstTime) {
                                    $adsConfig[$baseTypeId][$reg_t]['timeAds'][$rowFirstTime['timeAdsValue']] = $rowFirstTime['timeAdsValue'];
                                }
                            }
                        }


                        $config_second = getConfig('ads_second_config_' . $baseTypeId . '_' . $reg_t)->varValue;
                        if ($config_second) {
                            if (isset($config_second['limited_ads']['limitedAds'])) {
                                foreach ($config_second['limited_ads']['limitedAds'] as $rowSecond) {
                                    $adsConfig[$baseTypeId][$reg_t]['ads'][] = array(
                                        'baseType_name' => $row['baseTypeAdsValue'],
                                        'secondType_name' => $secondTypeAdsArray[$baseTypeId][$reg_t][$rowSecond['secondType']],
                                        'baseType' => $baseTypeId,
                                        'secondType' => $rowSecond['secondType'],
                                        'timeAds' => $rowSecond['timeAds'],
                                        'countInDay' => $rowSecond['countInDay'],
                                        'countCatItem' => $rowSecond['countCatItem'],
                                        'haveLink' => $rowSecond['haveLink'],
                                        'countImage' => $rowSecond['countImage'],
                                        'starPrice_name' => currencyFormat($rowSecond['starPrice']),
                                        'starPrice' => $rowSecond['starPrice'],
                                        'regType' => $reg_t,
                                        'isRequest' => $isRequest,
                                        'image' => $rowSecond['image'],
                                        'showTitlePack' => $rowSecond['showTitlePack'],
                                    );
                                }
                            }
                        }


                        $config_third = getConfig('ads_third_config_' . $baseTypeId . '_' . $reg_t)->varValue;
                        if ($config_third) {
                            if (isset($config_third['sort_ads']['sortAds'])) {
                                foreach ($config_third['sort_ads']['sortAds'] as $rowThird) {
                                    if (isset($rowThird['isHomePage']) && (int)$rowThird['isHomePage']) {
                                        $adsConfig[$baseTypeId][$reg_t]['homePage'][] = array(
                                            'baseType' => $baseTypeId,
                                            'secondType' => $rowThird['secondType'],
                                        );
                                    }
                                    $adsConfig[$baseTypeId][$reg_t]['allPage'][] = array(
                                        'baseType' => $baseTypeId,
                                        'secondType' => $rowThird['secondType'],
                                    );
                                }
                            }
                        }


                        $adsConfig[$baseTypeId][$reg_t]['baseTypeMachineName'] = $baseTypeMachineNameArray[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypeGoogleMap'] = $baseTypeGoogleMap[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypeRate'] = $baseTypeRate[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypeAmountAllInfo'] = $baseTypeAmountAllInfo[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypePriceOneEmail'] = $baseTypePriceOneEmail[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypePriceOneSms'] = $baseTypePriceOneSms[$baseTypeId];

                        $adsConfig[$baseTypeId][$reg_t]['baseTypeRequest'] = $baseTypeRequest[$baseTypeId];

                    }

                    $cache_key = 'Ads_configs_' . $baseTypeId;
                    setCacheItem($cache_key, $adsConfig[$baseTypeId]);

                }

            }

        }
    }

    public function loadCache($baseType)
    {
        if ($baseType) {
            $cache_key = 'Ads_configs_' . $baseType;
            if ($adsConfig = getCache()->getItem($cache_key))
                return $adsConfig;
            else {
                $this->saveCache();
                if ($adsConfig = getCache()->getItem($cache_key))
                    return $adsConfig;
                else
                    return false;
            }
        } else {
            $baseTypeAdsArray = array();
            $config_newType = getConfig('ads_new_type_config')->varValue;
            if ($config_newType) {
                $baseTypeAdsArray = array();
                if (isset($config_newType['base_type_ads']['baseType'])) {
                    foreach ($config_newType['base_type_ads']['baseType'] as $row)
                        $baseTypeAdsArray[$row['idBaseTypeAdsValue']] = array(
                            'name' => $row['baseTypeAdsValue'],
                            'id' => $row['idBaseTypeAdsValue'],
                            'isRequest' => $row['isRequest'],
                            'baseTypeAdsMachineName' => $row['baseTypeAdsMachineName'],
                        );
                }
            }
            return $baseTypeAdsArray;
        }
    }

    public function createViewStarRate($AllStarCount, $starCount)
    {
        $html = '';
        if ($AllStarCount) {
            for ($i = 0; $i < $starCount; $i++)
                $html .= '<span class="star_enabled"></span>';
            for ($i = 0; $i < ($AllStarCount - $starCount); $i++)
                $html .= '<span class="star_disabled"></span>';
        }
        return $html;
    }

}