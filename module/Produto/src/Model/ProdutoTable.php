<?php

namespace Produto\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use RuntimeException;

class ProdutoTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function lista() {
        return $this->tableGateway->select();
    }    

    public function info($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        if(!$row) {
            throw new RuntimeException('Produto não encontrado.');
        }

        return $row;
    }        

    public function salva(Produto $produto) {

        $data = [
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'quantidade' => $produto->getQuantidade(),
            'preco' => $produto->getPreco()
        ];

        $id = (int) $produto->getId();

        if($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            $this->tableGateway->update($data, ['id' => $id]);
        }

        return;
    }

    public function deleta($id) {
        return $this->tableGateway->delete(['id' => $id]);
    }

}