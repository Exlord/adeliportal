<?php
namespace RealEstate\View\Helper;


use System\View\Helper\BaseHelper;

class StatisticsRealEstateBlock extends BaseHelper
{
    private $cssJsLoaded = true;

    public function __invoke($block)
    {
        $estate_reg_type = array();
        $estate_type = array();
        $statistic_type = 1;

        if (isset($block->data[$block->type]['estate_type']) && $block->data[$block->type]['estate_type'])
            $estate_type = $block->data[$block->type]['estate_type'];
        if (isset($block->data[$block->type]['estate_reg_type']) && $block->data[$block->type]['estate_reg_type'])
            $estate_reg_type = $block->data[$block->type]['estate_reg_type'];
        if (isset($block->data[$block->type]['statistic_type']) && $block->data[$block->type]['statistic_type'])
            $statistic_type = (int)$block->data[$block->type]['statistic_type'];

        if ($statistic_type == 1) {
            $realEstateName = 'statistics-real-estate-block-' . $block->id;
            $block->data['class'] .= ' statistics-real-estate-block';
            $block->blockId = $realEstateName;

            $cache_key = 'statistics_real_estate_' . $block->id;
            if (cacheExist($cache_key))
                $statisticsEstate = getCacheItem($cache_key);
            else {
                $statisticsEstate = getSM('real_estate_table')->realEstateStatistics($estate_type, $estate_reg_type);
                setCacheItem($cache_key, $statisticsEstate);
            }

            $estate_type = getSM('category_item_table')->getItemsTreeByMachineName('estate_type');
            $estate_reg_type = array();
            $regTypeArray = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
            foreach ($regTypeArray as $key => $val)
                $estate_reg_type[$key] = t($val);
            return $this->view->render('real-estate/helper/statistics-real-estate', array(
                'statisticsEstate' => $statisticsEstate,
                'estateRegType' => $estate_reg_type,
                'estateType' => $estate_type
            ));
        } elseif ($statistic_type == 2) {
            $areaId = null;
            $routeParams = $this->params()->fromQuery();
            if (isset($routeParams['table']['parentAreaId']))
                $areaId = $routeParams['table']['parentAreaId'];

            $realEstateName = 'statistics-real-estate-agent-block-' . $block->id;
            $block->data['class'] .= ' statistics-real-estate-agent-block';
            $block->blockId = $realEstateName;

            $cache_key = 'statistics_real_estate_agent_' . $block->id . $areaId;
            if (cacheExist($cache_key))
                $statisticsEstate = getCacheItem($cache_key);
            else {
               // $userIdArray = getSM('agent_area_table')->getAgentArray($areaId);
                $userIdArray = array();
                $agentUserRole = getConfig('real_estate_config')->varValue;
                if (isset($agentUserRole['agentUserRole']) && $agentUserRole['agentUserRole'])
                    $agentUserRole = $agentUserRole['agentUserRole'];
                $select = getSM('user_table')->getByRoleId($agentUserRole, false, 'full');
                if($select)
                    foreach($select as $row)
                        $userIdArray[$row['id']]= $row['displayName'];
                $statisticsEstate = getSM('real_estate_table')->realEstateAgentStatistics($estate_type, $estate_reg_type, $userIdArray);
                //  setCacheItem($cache_key, $statisticsEstate);
            }

            $estate_type2 = getSM('category_item_table')->getItemsTreeByMachineName('estate_type');
            $estate_reg_type2 = array();


            $regTypeArray2 =  getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
            foreach ($regTypeArray2 as $key => $val)
                $estate_reg_type2[$key] = t($val);
            return $this->view->render('real-estate/helper/statistics-real-estate-agent', array(
                'statisticsEstate' => $statisticsEstate,
                'estateRegType' => $estate_reg_type,
                'estateType' => $estate_type,
                'estateRegType2' => $estate_reg_type2,
                'estateType2' => $estate_type2
            ));
        } elseif ($statistic_type == 3) {
            $realEstateName = 'statistics-real-estate-region-block-' . $block->id;
            $block->data['class'] .= ' statistics-real-estate-region-block';
            $block->blockId = $realEstateName;
            $allData = array();
            $cache_key = 'statistics_real_estate_region_' . $block->id;
            if (cacheExist($cache_key))
                $allData = getCacheItem($cache_key);
            else {

                $allArea = getSM('city_area_table')->getALl(array('itemStatus' => 1, 'parentId' => 0));

                if ($allArea)
                    foreach ($allArea as $row) {


                        $childArea = getSM('city_area_table')->getSubArray($row->id);
                        if ($childArea) {
                            $statisticsEstate = getSM('real_estate_table')->getPriceRange(array_keys($childArea),$row->id);
                            if ($statisticsEstate)
                                $allData[] = array(
                                    'id'=>$row->id,
                                    'title'=>$row->areaTitle,
                                    'data'=>$statisticsEstate,
                                );
                        }

                    }
              //  setCacheItem($cache_key, $allData);
            }
            return $this->view->render('real-estate/helper/all-region-statistic', array(
                'data' => $allData
            ));
        }
    }

}
