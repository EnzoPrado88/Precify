<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

class PrincipalController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('principal');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
    }

    public function index()
    {
        $this->set('title', 'Precify+');

        $resultado = null;
        $errors = [];

        if ($this->request->is('post')) {
            $dados = $this->request->getData();

            $validator = new Validator();
            $validator
                ->requirePresence(['peso_peca', 'espessura_banho', 'mao_de_obra_milesimos', 'cotacao_ouro', 'quantidade_pecas', 'margem_lucro'])
                ->notEmptyString('peso_peca', 'O peso da peça é obrigatório.')->greaterThan('peso_peca', 0, 'O peso da peça deve ser maior que zero.')
                ->notEmptyString('espessura_banho', 'A espessura do banho é obrigatória.')->greaterThanOrEqual('espessura_banho', 0, 'A espessura não pode ser negativa.')
                ->notEmptyString('mao_de_obra_milesimos', 'A mão de obra é obrigatória.')->greaterThanOrEqual('mao_de_obra_milesimos', 0, 'A mão de obra não pode ser negativa.')
                ->notEmptyString('cotacao_ouro', 'A cotação do ouro é obrigatória.')->greaterThan('cotacao_ouro', 0, 'A cotação do ouro deve ser maior que zero.')
                ->notEmptyString('quantidade_pecas', 'A quantidade de peças é obrigatória.')->integer('quantidade_pecas', 'A quantidade deve ser um número inteiro.')->greaterThan('quantidade_pecas', 0, 'A quantidade de peças deve ser maior que zero.')
                ->notEmptyString('margem_lucro', 'A margem de lucro é obrigatória.')->greaterThanOrEqual('margem_lucro', 0, 'A margem de lucro não pode ser negativa.');

            $errors = $validator->validate($dados);

            if (empty($errors)) {
                $pesoPeca = (float) $dados['peso_peca'];
                $espessuraMilesimos = (float) $dados['espessura_banho'];
                $maoDeObraMilesimos = (float) $dados['mao_de_obra_milesimos'];
                $cotacaoOuro = (float) $dados['cotacao_ouro'];
                $quantidadePecas = (int) $dados['quantidade_pecas'];
                $margemLucro = (float) $dados['margem_lucro'];

                $ouroParaBanhoPorPeca = ($espessuraMilesimos / 1000) * $pesoPeca;
                $ouroParaMaoDeObraPorPeca = ($maoDeObraMilesimos / 1000) * $pesoPeca;
                $totalOuroPorPeca = $ouroParaBanhoPorPeca + $ouroParaMaoDeObraPorPeca;

                //Calcula o custo da mão de obra em R$
                $custoMaoDeObraPorPeca = $ouroParaMaoDeObraPorPeca * $cotacaoOuro;

                $custoPorPeca = $totalOuroPorPeca * $cotacaoOuro;
                $precoVendaPorPeca = $custoPorPeca * (1 + $margemLucro / 100);
                $lucroPorPeca = $precoVendaPorPeca - $custoPorPeca;

                $custoTotalLote = $custoPorPeca * $quantidadePecas;
                $precoVendaTotalLote = $precoVendaPorPeca * $quantidadePecas;
                $lucroTotalLote = $lucroPorPeca * $quantidadePecas;
                //Calcula o custo total da mão de obra em R$ para o lote
                $custoMaoDeObraTotalLote = $custoMaoDeObraPorPeca * $quantidadePecas;

                $resultado = [
                    'precoVendaTotalLote' => $precoVendaTotalLote,
                    'precoVendaPorPeca' => $precoVendaPorPeca,
                    'custoTotalLote' => $custoTotalLote,
                    'custoPorPeca' => $custoPorPeca,
                    'lucroTotalLote' => $lucroTotalLote,
                    'lucroPorPeca' => $lucroPorPeca,
                    'inputs' => $dados,
                    'ouroParaBanhoPorPeca' => $ouroParaBanhoPorPeca,
                    'ouroParaMaoDeObraPorPeca' => $ouroParaMaoDeObraPorPeca,
                    'totalOuroPorPeca' => $totalOuroPorPeca,
                    'totalOuroLote' => $totalOuroPorPeca * $quantidadePecas,
                    //Adiciona os novos valores ao array de resultado ---
                    'custoMaoDeObraPorPeca' => $custoMaoDeObraPorPeca,
                    'custoMaoDeObraTotalLote' => $custoMaoDeObraTotalLote,
                ];
            } else {
                $this->Flash->error('Por favor, corrija os erros indicados no formulário.');
            }
        }
        $this->set(compact('resultado', 'errors'));
    }
}
