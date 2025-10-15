<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Localization\Controller;

use Application\API\App;
use Localization\Form\Translate;
use Localization\Form\Translation;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Theme\API\Table;
use Zend\Db\Sql\Sql;

class TranslationController extends BaseAbstractActionController
{
    private $miscellaneousTranslations = null;
    private $langs = null;

    //region public methods
    public function indexAction()
    {
        $languages = $this->getLanguageTable()->getArray();
        if ($languages && count($languages)) {
            $localizableContent = $this->getTranslationApi()->getConfig();
//            $api = $this->getTranslationApi();
//            foreach($localizableContent as $entityType=>$configs){
//                $table_name = $api->checkColumns($entityType, $configs);
//            }
        } else {
            $localizableContent = $this->vhm()->alert(t('The system has only 1 active language.'), 'alert-danger');
        }

        $this->viewModel->setVariables(array(
            'localizableContent' => $localizableContent
        ));
        $this->viewModel->setTemplate('localization/translation/index');
        return $this->viewModel;
    }

    public function selectEntityAction()
    {
        $entityType = $this->params()->fromRoute('entityType');
        $languages = $this->getLanguageTable()->getArray();
        if ($languages && count($languages)) {

            $localizableContent = $this->getTranslationApi()->getConfig($entityType);

            $data = $this->getContent($localizableContent);

            if ($data && $data->count()) {

                $headers = array();
                $headers[] = t('Id');
                foreach ($localizableContent['fields'] as $field => $details) {
                    $headers[] = t($details['label']);
                }
                $headers[] = array('data' => t('Translate Single Language'));
                $headers[] = array('data' => t('All Available Languages'));

                $rows = array();
                foreach ($data as $dataRow) {
                    $id = $dataRow[$localizableContent['pk']];
                    $row = array($id);

                    foreach ($localizableContent['fields'] as $field => $details) {
                        $text = strip_tags($dataRow[$field]);
                        $len = mb_strlen($text, 'UTF-8');
                        $text = $len > 50 ? mb_substr($text, 0, 50, 'UTF-8') . ' ...' : $text;
                        $row[] = $text;
                    }


                    //single lang trans
                    $transLinks = '';
                    foreach ($languages as $key => $value) {
                        $translateLink = url('admin/translation/select-entity/translate',
                            array('language' => $key, 'entityType' => $entityType, 'id' => $id));
                        $transLinks .= Common::Link($value, $translateLink,
                            array('class' => array('ajax_page_load', 'btn', 'btn-default')));
                    }
                    $transLinks = sprintf("<div class='translation-links'>%s</div>", $transLinks);
                    $row[] = array('data' => $transLinks, 'align' => 'center');

                    //multi lang trans
                    $translateLink = url('admin/translation/select-entity/translate',
                        array('language' => 'all', 'entityType' => $entityType, 'id' => $id));
                    $translateLink = Common::Link(t('Translate'), $translateLink,
                        array('class' => array('ajax_page_load', 'btn', 'btn-primary')));
                    $row[] = array('data' => $translateLink, 'align' => 'center', 'class' => 'translation-links-wrapper');

                    $rows[] = $row;
                }

                $localizableContent = Table::Table($headers, $rows, array('class' => 'grid', 'cellspacing' => 0));
            } else {
                $localizableContent = $this->vhm()->get('General')->ERROR(t('There is no content found for translation.'));
            }

        } else {
            $localizableContent = $this->vhm()->get('General')->ERROR(t('The system has only 1 active language.'));
        }

        $this->viewModel->setVariables(array(
            'localizableContent' => $localizableContent
        ));
        $this->viewModel->setTemplate('localization/translation/select-entity');
        return $this->viewModel;
    }

    public function translateAction()
    {
        $entityType = $this->params()->fromRoute('entityType', false);
        $entityId = $this->params()->fromRoute('id', false);
        //localization config defined in config file
        $api = $this->getTranslationApi();
        $localizableContent = $api->getConfig($entityType);
        $table_name = $api->checkColumns($entityType, $localizableContent);

        //selected language for translate exp: all , en , fa ...
        $language = $this->params()->fromRoute('language', 'all');

        if (!$entityType || !$entityId) {
            $this->flashMessenger()->addErrorMessage('EntityType and EntityId is required for content translations.');
            return $this->invalidRequest('admin/translation');
        }

        //all available languages in the system
        $all_languages = $this->getLanguageTable()->getArray(true);
        //the system's default language
        $defaultLang = $this->getLanguageTable()->getDefault();

        if ($language != 'all') {
            //the selected language is not a valid one
            if (!in_array($language, array_keys($all_languages))) {
                $this->flashMessenger()->addErrorMessage('The selected language is not available.');
                return $this->invalidRequest('admin/translation');
            }
            //only one language is selected + the default language
            $languages[$defaultLang] = $all_languages[$defaultLang];
            $languages[$language] = $all_languages[$language];
        } else {
            $languages = $all_languages;
        }

        //the languages available for translation except the default language
        $translate_langs = $languages;
        unset($translate_langs[$defaultLang]);

        //previously translated content if any
        $contents = $this->getTranslationApi()->getTable($table_name)->getAll($entityId, $language);
        //load the previously translated content into a array like $data['langs-content']['en']['fieldName'] = 'fieldValue'
        $lang_content = array('langs-content' => array());
        foreach ($translate_langs as $lang => $name) {
            $lang_content['langs-content'][$lang]['id'] = 'new';
        }
        if ($contents) {
            foreach ($contents as $row) {
                foreach ($localizableContent['fields'] as $key => $config) {
                    $lang_content['langs-content'][$row->lang][$key] = $row->{$key};
                }
                $lang_content['langs-content'][$row->lang]['id'] = 'edit';
            }
        }

        //the data for the default language. read only view
        $originalData = $this->getContent($localizableContent, $entityId);

        $form = new Translate($translate_langs, $localizableContent);
        $form->setAction(url('admin/translation/select-entity/translate',
            array('language' => $language, 'entityType' => $entityType, 'id' => $entityId)));
        $form = prepareConfigForm($form);
        $form->setData($lang_content);


        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $post = $form->getData();
                    $data = array();
                    foreach ($post['langs-content'] as $lSign => $fields) {
                        $trans = array();
                        $id = 'new';
                        foreach ($fields as $fieldName => $content) {
                            if ($fieldName == 'id')
                                $id = $content;
                            else
                                $trans[$fieldName] = $content;
                        }
                        $data[$id][$lSign] = $trans;
                    }
                    $this->getTranslationApi()->getTable($table_name)->multiSave($entityId, $data);
                    $this->flashMessenger()->addSuccessMessage('Content Translates successfully');
                } else
                    $this->formHasErrors();
            }
        }


        $this->viewModel->setVariables(array(
                'originalData' => $originalData,
                'localizableContent' => $localizableContent,
                'form' => $form,
                'languages' => $languages,
                'defaultLang' => $defaultLang
            )
        );
        $this->viewModel->setTemplate('localization/translation/translate');
        return $this->viewModel;
    }

    public function miscellaneousAction()
    {
        $langs = $this->getLangs();
        $content = $this->getMiscellaneousTranslations();

        $headers = array(array('data' => t('Edit'), 'width' => '50', 'align' => 'center'), t('Default'));
        foreach ($langs as $lSign => $lName) {
            $headers[] = $lName;
        }
        $rows = array();

        $colIndex = 1;
        foreach ($content as $lSign => $subContent) {
            $rowIndex = 0;
            foreach ($subContent as $default => $value) {
                $rows[$rowIndex][-1] = array('data' => Common::Link('Edit',
                        url('admin/translate-miscellaneous/edit', array('key' => base64_encode($default))),
                        array('class' => 'button grid_button edit_button ajax_page_load')), 'align' => 'center');
                $rows[$rowIndex][0] = $default;
                $rows[$rowIndex][$colIndex] = $value;
                $rowIndex++;
            }
            $colIndex++;
        }
        $localizableContent = Table::Table($headers, $rows, array('class' => 'grid', 'cellspacing' => 0));

        $this->viewModel->setVariables(array(
            'localizableContent' => $localizableContent
        ));
        $this->viewModel->setTemplate('localization/translation/miscellaneous');
        return $this->viewModel;
    }

    public function addAction($data = array())
    {
        $langs = $this->getLangs();
        $form = prepareForm(new Translation($langs), array('submit-new'));
        $form->setData($data);
        if (count($data))
            $form->setAction(url('admin/translate-miscellaneous/edit', array('key' => base64_encode($data['default']))));
        else
            $form->setAction(url('admin/translate-miscellaneous/add'));

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                unset($data['buttons']);
                $default = $data['default'];
                unset($data['default']);
                $content = $this->getMiscellaneousTranslations();
                foreach ($data as $lang => $value) {
                    if (has_value($value))
                        $content[$lang][$default] = $value;
                    elseif (isset($content[$lang][$default]))
                        unset($content[$lang][$default]);
                }
                $this->miscellaneousTranslations = $content;
                $this->saveMiscellaneousTranslations($content);
                return $this->miscellaneousAction();
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        $this->viewModel->setTemplate('localization/translation/add');
        return $this->viewModel;
    }

    public function editAction()
    {
        $key = $this->params()->fromRoute('key', false);
        if (!$key) {
            $this->flashMessenger()->addErrorMessage('Invalid request !');
            return $this->miscellaneousAction();
        }
        $key = base64_decode($key);
        $content = $this->getMiscellaneousTranslations();
        $data = array('default' => $key);
        foreach ($content as $lSign => $subContent) {
            if (isset($subContent[$key]))
                $data[$lSign] = $subContent[$key];
        }

        return $this->addAction($data);
    }
    //endregion

    //region private methods
    private function getContent($localizableContent, $id = 0)
    {
        //TODO permissions for user / self / others
        $adapter = App::getDbAdapter();
        $sql = new Sql($adapter, $localizableContent['table']);
        $select = $sql->select();

        $columns = array($localizableContent['pk']);
        foreach ($localizableContent['fields'] as $field => $details) {
            $columns[] = $field;
        }
        $select->columns($columns);

        if (isset($localizableContent['where']))
            $select->where($localizableContent['where']);

        if ($id)
            $select->where(array($localizableContent['pk'] => $id));

        $statement = $sql->prepareStatementForSqlObject($select);
        $data = $statement->execute();
        if ($id)
            return $data->current();
        return $data;
    }

    /**
     * @return \Localization\Model\LanguageTable
     */
    private function getLanguageTable()
    {
        return getSM('language_table');
    }

    /**
     * @return \Localization\API\Translation
     */
    private function getTranslationApi()
    {
        return getSM('translation_api');
    }

    private function getMiscellaneousTranslations()
    {
        if (is_null($this->miscellaneousTranslations)) {
            $langs = $this->getLangs();
            $content = array();
            foreach ($langs as $lSign => $lName) {
                $content[$lSign] = array();
                $lDir = ROOT . '/language/' . $lSign;
                if (is_dir($lDir)) {
                    $lFile = $lDir . '/miscellaneous.lang';
                    if (file_exists($lFile)) {
                        $items = include $lFile;
                        foreach ($items as $key => $value) {
                            if (has_value($value))
                                $content[$lSign][$key] = $value;
                        }
                    }
                }
            }
            $this->miscellaneousTranslations = $content;
        }
        return $this->miscellaneousTranslations;
    }

    private function saveMiscellaneousTranslations($content)
    {
        foreach ($content as $lSign => $values) {
            $lDir = ROOT . '/language/' . $lSign;
            if (!is_dir($lDir))
                mkdir($lDir, 0775, true);
            $data = '<? return ' . var_export($values, true) . ';';
            file_put_contents($lDir . '/miscellaneous.lang', $data);
        }
    }

    private function getLangs()
    {
        if (is_null($this->langs)) {
            $this->langs = getSM('language_table')->getArray(true);
        }
        return $this->langs;
    }
    //endregion
}
