<?php
class Kaushik_Outofstock_IndexController extends Mage_Core_Controller_Front_Action
{
	// public function indexAction()
 //    {
 //    	$this->loadLayout();     
	// 	$this->renderLayout();
 //    }
    
    public function subscribeAction()
    {
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$response = array();
			$model = Mage::getModel('outofstock/outofstock')->getCollection();
			$model->addFieldToFilter('product_id', array('eq' => $data['product_id']));
			$model->addFieldToFilter('email', array('eq' => $data['email']));
			 
			foreach($model as $data){
				$outofstockId = $data->getOutofstockId();
				$createdTime = $data->getCreatedTime();
			}
			if($outofstockId) {
				$response['msg'] = 'This email is already subscribed on '.$createdTime;
				$response['status'] = 'warning';
			} else {
				$newmodel = Mage::getModel('outofstock/outofstock');
				$data['created_time'] = now();
				$newmodel->setData($data);
				try {
					$newmodel->save();
					$response['msg'] = 'You will be notified when this item is available for purchase.';
					$response['status'] = 'success';
				} catch(Exception $e) {
					$response['msg'] = $e->getMessage();
					$response['status'] = 'failure';
				}
			}
			echo json_encode($response);
		}
    }

    public function unsubscribeAction() {
    	if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$response = array();
			$model = Mage::getModel('outofstock/outofstock')->getCollection();
			$model->addFieldToFilter('email', $data['email']);
			$model->addFieldToFilter('product_id', $data['product_id']);
			//print_r($model->getData());
			try {
				$model->walk('delete');
				$response['msg'] = 'Unsubscribed successfully.';
				$response['status'] = 'success';
			} catch(Exception $e) {
				$response['msg'] = $e->getMessage();
				$response['status'] = 'failure';
			}
			echo json_encode($response);
		}
    }

    public function subscribedlistAction() {

    	if(Mage::getModel('customer/session')->isLoggedin()) {
	    	$this->loadLayout();
	        $this->_initLayoutMessages('customer/session');
	        $this->_initLayoutMessages('catalog/session');
	        $this->getLayout()->getBlock('head')->setTitle($this->__('My Product Stock Subscriptions'));
	        $this->renderLayout();
    	} else {
    		Mage::app()->getResponse()->setRedirect(Mage::getUrl("customer/account/login/"));
    	}
    }
}