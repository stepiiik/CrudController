<?php

/**
 * CRUDControllerAbstract
 * 
 * @author
 * @version 
 */
abstract class Stepiiik_CRUDController extends Zend_Controller_Action
{

    protected function _preUpdate($args = null)
    {
    
    }

	protected function _postUpdate($args = null)
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
     * Zatim ocekava vzdy akci index
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
                $model->update($form->getValues(), 'id=' . $id);
                
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
?>
