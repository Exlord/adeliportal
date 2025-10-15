<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/25/13
 * Time: 10:29 AM
 */

namespace RealEstate\Controller;


use System\Controller\BaseAbstractActionController;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Http\Response;
use Zend\Soap\Server;
use Zend\View\Model\JsonModel;

class OfflineAppController extends BaseAbstractActionController
{
    private function authFailed($msg)
    {
        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_401);
        $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=UTF-8');
        $response->setReasonPhrase($msg);
        $response->setContent($msg);
        return $response;
    }

    public function updateDataAction()
    {
        if ($this->getRequest()->isPost()) {

            $trim = new StringTrim();
            $tags = new StripTags();
            $username = $this->params()->fromPost('username', '');
            $password = $this->params()->fromPost('password', '');

            $username = $tags->filter($trim->filter($username));
            $password = $tags->filter($trim->filter($password));

            if (empty($username) || empty($password))
                return $this->authFailed("Username and Password are required");

            $result = getSM()->get('user_table')->authenticate($username, $password);
            if (!is_object($result)) {
                if (is_array($result))
                    $result = implode(' , ', $result);
                return $this->authFailed($result);
            }

            if (!isAllowed(\RealEstate\Module::OFFLINE_APP_DATA, $result->roles))
                return $this->authFailed("Access Denied !");

            $cache_key = "data_update_xml";
            if (!$xml = getCacheItem($cache_key)) {
                $xml = new \XmlWriter();
                $xml->openMemory();
                $xml->startDocument("1.0", "UTF-8");
                $xml->startElement('data');

                $countries = getSM()->get('country_table')->getAllActive();
                $xml->startElement('countries');
                foreach ($countries as $country) {
                    $xml->startElement('i');
                    $xml->writeElement('id', $country->id);
                    $xml->writeElement('n', $country->countryTitle);
                    $xml->writeElement('o', $country->itemOrder);
                    $xml->endElement();
                }
                $xml->endElement();

                $states = getSM()->get('state_table')->getAllActive();
                $xml->startElement('states');
                foreach ($states as $state) {
                    $xml->startElement('i');
                    $xml->writeElement('id', $state->id);
                    $xml->writeElement('n', $state->stateTitle);
                    $xml->writeElement('cId', $state->countryId);
                    $xml->writeElement('o', $state->itemOrder);
                    $xml->endElement();
                }
                $xml->endElement();

                $cities = getSM()->get('city_table')->getAllActive();
                $xml->startElement('cities');
                foreach ($cities as $city) {
                    $xml->startElement('i');
                    $xml->writeElement('id', $city->id);
                    $xml->writeElement('n', $city->cityTitle);
                    $xml->writeElement('sId', $city->stateId);
                    $xml->writeElement('o', $city->itemOrder);
                    $xml->endElement();
                }
                $xml->endElement();

                $category_table = getSM()->get('category_item_table');
                $estateType = $category_table->getItemsByMachineName('estate_type');
                $xml->startElement('estateType');
                foreach ($estateType as $st) {
                    $xml->startElement('i');
                    $xml->writeElement('id', $st->id);
                    $xml->writeElement('n', $st->itemName);
                    $xml->writeElement('pId', $st->parentId);
                    $xml->writeElement('o', $st->itemOrder);
                    $xml->endElement();
                }
                $xml->endElement();

                $config_table = getSM()->get('config_table');
                $real_estate_config = $config_table->getByVarName('real_estate_config');
                $real_estate_config_advance = $config_table->getByVarName('real_estate_config_advance');
                /* @var $fields_api \Fields\API\Fields */
                $fields_api = getSM()->get('fields_api');

                $real_estate_config = $real_estate_config->varValue;
                $fields_id_list = $real_estate_config['transferFields'];

                $fields = $fields_api->getFields($fields_id_list);
                $this->dataElement($xml, $fields, 'fields');
                $fields_id_name = array();
                foreach ($fields as $f) {
                    $fields_id_name[$f['fieldMachineName']] = $f['id'];
                }

                $real_estate_config_advance = $real_estate_config_advance->varValue;
                $estateType_regType = $real_estate_config_advance['estateType_regType'];
                $estateType_fields = $real_estate_config_advance['estateType_fields'];
                $regType_fields = $real_estate_config_advance['regType_fields'];

                $xml->startElement('estateType_regType');
                foreach ($estateType_regType as $key => $sr) {
                    $xml->startElement('i');
                    $xml->writeElement('key', $key);
                    foreach ($sr as $id => $value) {
                        if ($value)
                            $xml->writeElement('d', $id);
                    }
                    $xml->endElement();
                }
                $xml->endElement();

                $xml->startElement('estateType_fields');
                foreach ($estateType_fields as $key => $sr) {
                    $xml->startElement('i');
                    $xml->writeElement('key', $key);
                    foreach ($sr as $name => $value) {
                        if ($value)
                            $xml->writeElement('d', $fields_id_name[$name]);
                    }
                    $xml->endElement();
                }
                $xml->endElement();


                $xml->startElement('regType_fields');
                foreach ($regType_fields as $key => $sr) {
                    $xml->startElement('i');
                    $xml->writeElement('key', $key);
                    foreach ($sr as $name => $value) {
                        if ($value)
                            $xml->writeElement('d', $fields_id_name[$name]);
                    }
                    $xml->endElement();
                }
                $xml->endElement();

                $xml->endElement();
                $xml = $xml->outputMemory(true);
            }


            $this->viewModel->setTerminal(true);
            $this->viewModel->setTemplate('real-estate/offline-app/update-data');
            $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'text/xml'));
            $this->viewModel->setVariables(array(
                'xml' => $xml,
            ));
        }

        return $this->viewModel;
    }

    private function dataElement(\XMLWriter $xml, $data, $element, $hasNumericKey = false)
    {
        $xml->startElement($element);
        $this->dataToXml($xml, $data, $hasNumericKey);
        $xml->endElement();
    }

    private function dataToXml(\XMLWriter $xml, $data, $hasNumericKey)
    {
        foreach ($data as $key => $value) {
            $itemKey = false;
            if (is_numeric($key)) {
                $itemKey = $key;
                $key = "item";
            }
            if (is_array($value)) {
                $xml->startElement($key);
                if ($itemKey && $hasNumericKey)
                    $xml->writeAttribute('key', $itemKey);
                $this->dataToXml($xml, $value, $hasNumericKey);
                $xml->endElement();
                continue;
            }
            $xml->writeElement($key, $value);
        }
    }

    public function uploadAppDataAction()
    {
        if ($this->getRequest()->isPost()) {

            $trim = new StringTrim();
            $tags = new StripTags();
            $username = $this->params()->fromPost('username', '');
            $password = $this->params()->fromPost('password', '');

            $username = $tags->filter($trim->filter($username));
            $password = $tags->filter($trim->filter($password));

            if (empty($username) || empty($password))
                return $this->authFailed("Username and Password are required");

            $result = getSM()->get('user_table')->authenticate($username, $password);
            if (!is_object($result)) {
                if (is_array($result))
                    $result = implode(' , ', $result);
                return $this->authFailed($result);
            }

            if (!isAllowed(\RealEstate\Module::APP_REAL_ESTATE_UPLOAD_APP_DATA, $result->roles))
                return $this->authFailed("Access Denied !");

            $data = $this->params()->fromPost('data', false);

            $arrayId = false;
            if ($data) {

                $data = trim($data);
                if ($data[0] != "{")
                    return new JsonModel(array('msg' => "Invalid data structure"));

                $data = json_decode($data);

                foreach ($data as $offId => $row) {

                    if (!(isset($row->data) && isset($row->fields)))
                        continue;

                    if ($row->data->regType == 78) {
                        $row->data->mortgagePrice = $row->data->totalPrice;
                        $row->data->totalPrice = 0;
                    }

                    foreach ((array)$row->fields as $key => $val) {
                        if ($key == 'service_behdashti' || $key == 't_noorgiri' || $key == 's_tasisat' || $key == 'refahi') {
                            $valArray = explode(',', $val);
                            $i = 1;
                            $checkBoxArray = array();
                            foreach ($valArray as $vals) {
                                if ((int)$vals)
                                    if ($i == 4 && $key == 'refahi')
                                        $checkBoxArray[] = 9;
                                    else
                                        $checkBoxArray[] = $i;
                                $i++;
                            }
                            $row->fields->$key = serialize($checkBoxArray);
                        }
                        if($key=='t_eskelet')
                            $row->fields->s_eskelet = $val;
                        if ($key == 'mashinro' || $key == 'mosharafbe' || $key == 'divarkeshi' || $key == 'hesar' || $key == 'system_abyari' || $key == 'emtiaz_ab' || $key == 'c_otagh_khab') {
                            $row->fields->$key = (int)$val - 1;
                        }
                        if ($key == 'zirbana' || $key == 'masahate_zamin') {
                            if ($val) {
                                if ($row->data->totalPrice) {
                                    $row->data->priceOneMeter = (int)$row->data->totalPrice / (int)$val;
                                }
                            }
                        }
                    }


                    if (!isset($row->data->mortgagePrice)) {
                        $row->data->mortgagePrice = 0;
                        $row->data->rentalPrice = 0;
                    }
                    if (!isset($row->data->totalPrice)) {
                        $row->data->totalPrice = 0;
                        $row->data->priceOneMeter = 0;
                    }


                    //$row->data => info for single real estate
                    //$row->fields => info for single real estate

                    // $id = 0;
                    /* if (isset($row->data->id) && $row->data->id) {
                         if ($row->data->GlobalID) {
                             $id = $row->data->GlobalID;
                             $row->fields->id = 1;
                         }
                         unset($row->data->GlobalID);
                     }*/

                    /*if ($id) {
                        getSM('real-estate-table')->remove($id);
                        $this->getFieldsApi()->init('real_estate');
                        $this->getFieldsApi()->remove($id);

                        $fileTable = getSM()->get('file_table');
                        $file = $fileTable->getByEntityType('real_estate', $id);
                        $fileArray = array();
                        foreach ($file as $value)
                            $fileArray[] = $value->fPath;
                        $fileTable->removeById($id);
                    }*/

                    $user = getSM('user_table')->getAll(array('username' => $username));
                    if ($user)
                        $user = $user->current();

                    if (!$user) {
                        return new JsonModel(array(
                            'id' => $arrayId,
                        ));
                    }

                    $row->data->userId = $user->id;

                    if (isset($row->data->ID))
                        unset($row->data->ID);


                    // if ($id)
                    $id = $row->data->id;
                    $row->data->created = time();
                    $row->data->published = time();
                    $row->data->modified = time();
                    $row->data->expire = strtotime('+1 month', time());
                    $row->data->status = 1;
                    $row->data->app = 1;

                    $result = getSM('real-estate-table')->save((array)$row->data);
                    $newData = (array)$row->fields;
                    $this->getFieldsApi()->init('real_estate');
                    if (!$id) {
                        $id = $result;
                    } else {
                        $oldData = getSM('fields_api')->getFieldData($id);
                        if ($oldData)
                            $newData['id'] = $oldData['id'];
                    }
                    if ($id) {
                        $arrayId[$offId] = $id;
                        $this->getFieldsApi()->save('real_estate', $id, $newData);
                    }
                }
                if (is_array($arrayId))
                    return new JsonModel(array(
                        'id' => $arrayId
                    ));
            }
            return new JsonModel(array(
                'id' => $arrayId
            ));

        }
        return new JsonModel(array(
            'msg' => 'Invalid request'
        ));
    }
}