<?php
namespace NewsLetter\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Mail\API\Mail;
use NewsLetter\Form\ConfigMore;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class ClientController extends BaseAbstractActionController
{
    public function signUpAction()
    {
        $flag = 0;
        $categories = array();
        /* @var $config ConfigMore */
        $config = getConfig('newsLetter_config_more')->varValue;
        if (is_array($config)) {
            foreach ($config as $row)
                if (isset($row['select']))
                    foreach ($row['select'] as $cat)
                        $categories[$cat['catId']] = $cat['count'];
        }

        $selectCat = getSM('category_item_table')->getAll(array('id' => array_keys($categories)));
        $categoryArray = array();
        if ($selectCat->count())
            foreach ($selectCat as $row)
                $categoryArray[$row->id] = $row->itemName;

        $model = new \NewsLetter\Model\NewsletterSignUp();
        $form = new \NewsLetter\Form\NewsletterSignUp($categoryArray);

        $form->bind($model);

        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $count = getSM('news_letter_sign_up_table')->getAll(array('email' => $data->email))->count();
                if ($count < 1) {
                    $model->config = serialize($model->config);
                    $id = getSM('news_letter_sign_up_table')->save($model);
                    $flag = 1;
                } else
                    $flag = 2;
            } else
                $flag = 3;
        }
        $this->viewModel->setTemplate('news-letter/client/sign-up');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'flag' => $flag
        ));
        return $this->viewModel;
    }
}
