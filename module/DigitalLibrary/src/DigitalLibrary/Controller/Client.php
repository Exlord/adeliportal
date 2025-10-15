<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/12/2014
 * Time: 10:35 AM
 */

namespace DigitalLibrary\Controller;


use Application\API\Breadcrumb;
use DigitalLibrary\Model\BookTable;
use File\API\PrivateFile;
use System\Controller\BaseAbstractActionController;
use Zend\Db\Sql\Where;

class Client extends BaseAbstractActionController
{
    public function indexAction()
    {
        //region Vars
        $where = new Where();
        $order = array('tbl_dl_book.id DESC');
        $validFilters = array(
            'table' => array(
                'title'
            )
        );
        //endregion

        //region Params,Query,Post
        $page = $this->params()->fromQuery('page', 1);
        $per_page = $this->params()->fromQuery('per_page', 20);
        $onlyItems = $this->params()->fromPost('only-items', false);
        $route = 'app/books';
        $filter = array_merge_recursive($this->params()->fromQuery(), $this->params()->fromPost());
        //endregion

        //region Fields
        $fields_api = $this->getFieldsApi();
        $fieldsTable = $this->getFieldsApi()->init('books');
        $fields = $this->getFieldsTable()->getByEntityType('books')->toArray();
        foreach ($fields as $f) {
            $validFilters['field'][] = $f['fieldMachineName'];
        }
        //endregion

        //region Filters
        foreach ($filter as $type => $params) {
            if (isset($validFilters[$type])) {
                foreach ($params as $name => $value) {
                    if (in_array($name, $validFilters[$type])) {
                        $tableName = ($type == 'table') ? 'tbl_dl_book' : 'f';

                        if (isset($value) && has_value($value)) {
                            $where->like($tableName . '.' . $name, "%" . $value . "%");
                        }
                    }
                }
            }
        }

        //endregion

        Breadcrumb::AddMvcPage('Books', 'app/books');

        $paginator = $this->getTable()->getItemsList($fieldsTable, $page, $per_page, $where);

        if ($onlyItems) {
            $this->viewModel->setTemplate('digital-library/client/list-item');
            $this->viewModel->setTerminal(true);
        } else {
            $this->viewModel->setTemplate('digital-library/client/index');
            $this->viewModel->setTerminal(false);
        }
        $this->viewModel->setVariables(array(
            'paginator' => $paginator,
            'route' => 'app/books',
            'fields' => $fields,
            'per_page' => $per_page,
        ));
        return $this->viewModel;
    }

    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('app/books');

        $fieldsTable = getSM()->get('fields_api')->init('books');

        $data = $this->getTable()->getData($id, $fieldsTable);

        if (!$data) {
            $this->flashMessenger()->addErrorMessage('Item with requested id not found');
            return $this->redirect()->toRoute('app/books');
        }

        $files = getSM('private_file_usage')->getFilesData('books', $id);

        $fields = $this->getFieldsTable()->getByEntityType('books')->toArray();
        $this->viewModel->setTemplate('digital-library/client/view');
        $this->viewModel->setVariables(array(
            'data' => $data,
            'fields' => $fields,
            'files' => $files
        ));
        return $this->viewModel;
    }

    public function viewerAction()
    {
        $this->viewModel->setTerminal(true);
        $file = $this->params()->fromRoute('fileId', false);
        if ($file) {
            if (PrivateFile::HasAccess($file)) {
                $this->viewModel->setTemplate('digital-library/client/viewer');
                $this->viewModel->setVariable('file', $file);
                return $this->viewModel;
            } else
                $this->viewModel->setTemplate('digital-library/client/unauthorized');
        } else
            $this->viewModel->setTemplate('digital-library/client/file-not-found');

        return $this->viewModel;
    }

    /**
     * @return BookTable
     */
    private function getTable()
    {
        return getSM('book_table');
    }
}