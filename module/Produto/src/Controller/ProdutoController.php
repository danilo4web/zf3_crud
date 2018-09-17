<?php

namespace Produto\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProdutoController extends AbstractActionController {
    private $table;

    public function __construct() {
        $this->table = new ProdutoTable();
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function listarAction() {
        return new ViewModel( ['produtos' => $this->table->lista()] );
    }

    public function cadastrarAction() {
        $form = new \Produto\Form\ProdutoForm();

        $form->get('submit')->setValue('Adicionar');
        $request = $this->getRequest();

        if(! $request->isPost()) {
            return new ViewModel( ['form' => $form] );
        } else {

            $produto = new \Produto\Model\Produto();
            $form->setData($request->getPost());

            if(!$form->isValid()) {
                return new ViewModel( ['form' => $form] );
            } else {
                $produto->exchangeArray( $form->getData() );
                $this->table->salva($produto);
    
                return $this->redirect()->toRoute('produto');
            }
        }
    }

    public function editarAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if($id === 0) {
            return $this->redirect()->toRoute('produto', ['action' => 'cadastrar']);
        } else {

            try {
                $produto = new $this->table->info($id);
            } catch(Exception $e) {
                print_r($e); 
                exit;
            }

            $form = new \Produto\Form\ProdutoForm();
            $form->bind($produto);

            $form->get('submit')->setAttribute('value', 'Salvar');
            $request = $this->getRequest();

            if(! $request->isPost()) {
                return new ViewModel( ['form' => $form, 'id' => $id] );
            } else {

                $produto = new \Produto\Model\Produto();
                $form->setData($request->getPost());
    
                if(!$form->isValid()) {
                    return new ViewModel( ['form' => $form, 'id' => $id] );
                } else {
                    // $produto->exchangeArray( $form->getData() );
                    $this->table->salva( $form->getData() );
        
                    return $this->redirect()->toRoute('produto');
                }
            }
        }
    }

    public function deletarAction() {
        $id = (int) $this->params()->getRoute('id', 0);

        if(0 === $id) {
            return $this->redirect()->toRoute('produto');
        } else {
            $request = $this->getRequest();

            if($request->isPost()) {
                $this->table->deleta($id);
            }
            
            return $this->redirect()->toRoute('produto');
        }
    }    

    public function incrementarAction() {
        $id = (int) $this->params()->getRoute('id', 0);

        if(0 === $id) {
            return $this->redirect()->toRoute('produto');
        }

        $produto = new $this->table->info($id);
        $produto->setQuantidade($produto->getQuantidade() + 1);
        $this->table->salva( $produto );
        return $this->redirect()->toRoute('produto');
    }

    public function decrementarAction($id) {
        $id = (int) $this->params()->getRoute('id', 0);

        if(0 === $id) {
            return $this->redirect()->toRoute('produto');
        }

        $produto = new $this->table->info($id);
        $produto->setQuantidade($produto->getQuantidade() - 1);
        $this->table->salva( $produto );
        return $this->redirect()->toRoute('produto');        
    } 
}
