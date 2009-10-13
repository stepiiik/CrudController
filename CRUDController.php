<?php
 
/**
 * CRUDControllerAbstract
 *
 * Abstraktní magický nástoj, který pomůže snadno vytvořit webovou aplikaci umožňující CRUD operace s databázovou tabulkou
 * Magie je provedena ve třech krocích
 * 1. Implementace metody getForm(), která vrátí formulář (Zend_Form) a metody getModel(), která vrátí Data Table Gateway třídu pro tabulku (Zend_Db_Table)
 * 2. Implementace metody indexAction() podle požadavků konkrétní aplikace
 * 3. Vytvoření příslušných šablon (index.phtml, create.phtml, edit.phtml)
 * 
 * @author Štěpán Zikmund (stepan.zikmund@gmail.com)
 * @version 0.1
 */
abstract class Stepiiik_CRUDController extends Zend_Controller_Action
{
    /**
     * FlashMessenger
     *
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
	protected $_flashMessenger = null;
	 
    public function init()
    {
		$this->_flashMessenger =
            $this->_helper->getHelper('FlashMessenger');
	        
        $this->initView();
    }
 
    /**
     * Metoda se pro použitíí implementuje tak, že vrátí pole zpráv s indexy
     * create - Pro zprávu po vytvoření řádku
     * update - Pro zprávu po úpravě řádku
     * delete - Pro zpárvu po smazání řádku
     *
     * @return array
     */
    protected function _flashMessages()
    {
        return array();
    }
    
    protected function _getFlashMessage($title)
    {
        if (array_key_exists($title, $this->_flashMessages)) {
            $arr = $this->_flashMessages;
            return $arr[$title];
        }
        
        return null;
    }
 
    public function postDispatch()
    {
        //$this->view->messages = $this->_flashMessenger->getMessages();
    }
 
    protected function _preEdit($args = null)
    {
    
    }
 
    protected function _postEdit($args = null)
    {
    
    }
    
    protected function _preDelete($args = null)
    {
 
    }
 
    protected function _postDelete($args = null)
    {
    
    }
    
    protected function _preInsert($args = null)
    {
    	
    }
    
    protected function _postInsert($args = null)
    {
    
    }
    
 
    /**
     * @return Zend_Db_Table
     */
    abstract protected function getModel();
 
    /**
     * @retun Zend_Form
     */
    abstract protected function getForm();
 
    /**
     * Controller ocekava vzdy akci index
     */
    abstract function indexAction();
 
    public function createAction()
    {
        $this->_preInsert();
 
        $form = $this->getForm();
        
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if (! $form->isValid($data)) {
                $this->view->form = $form;
            }
            else {
                $model = $this->getModel();
                
                $args = array('id' => $model->insert($form->getValues()));
 
                if ($this->_getFlashMessage('create')) {
                    $this->_flashMessenger->addMessage($this->_getFlashMessage('create'));
                }
                
                $this->_postInsert($args);
                
                $this->_redirect($this->_helper->url('index'));
            }
        }
        
        $this->view->form = $form;
    }
 
    public function editAction()
    {
        $this->_preEdit();
        
        $id = $this->_getParam('id');
        
        $form = $this->getForm();
        $model = $this->getModel();
        $result = $model->find($id);
        
        if ($result->count() !== 1) {
            $this->_redirect($this->_helper->url('index'));
        }
        $row = $result->current();
        
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if (! $form->isValid($data)) {
                $this->view->form = $form;
            }
            else {
	            $model->update($form->getValues(), 'id=' . $model->getAdapter()->quote($id,  'INTEGER'));
 
                if ($this->_getFlashMessage('edit')) {
                    $this->_flashMessenger->addMessage($this->_getFlashMessage('edit'));
                }
                
                $this->_postEdit();
                
                $this->_redirect($this->_helper->url('index'));
            }
        }

        $form->populate($row->toArray());
        $this->view->data = $row;
        $this->view->form = $form;
    }
 
    public function deleteAction()
    {
        $model = $this->getModel();
        $id = $this->_getParam('id');
 
        $this->_preDelete(array('id' => $id));
 
        $result = $model->find($id);
        if ($result->count() !== 1) {
            return $this->_redirect($this->_helper->url('index'));
        }
        $result->current()->delete();
 
        if ($this->_getFlashMessage('delete')) {
            $this->_flashMessenger->addMessage($this->_getFlashMessage('delete'));
        }
        
        $this->_postDelete();
        
        return $this->_redirect($this->_helper->url('index'));
    }
 
    public function detailAction()
    {
        $model = $this->getModel();
        $id = $this->_getParam('id');
        $result = $model->find($id);
        
        if ($result->count() !== 1) {
            return $this->_redirect($this->_helper->url('index'));
        }
        $this->view->data = $result->current();
    }
}