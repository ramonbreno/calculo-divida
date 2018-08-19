<?php
namespace App\Action;

class HomeAction extends Action{
    private $valorJurosDesconto = 0;
    private $valorAtualizadoParcela = 0;

    public function index($request, $response){
        $vars ['page'] = "form";
        return $this->view->render($response, "template.phtml",$vars);
    }
    public function calcular($request, $response){
        $data = $request->getParsedBody();

        $dataVencimento = filter_var($data['data'],FILTER_SANITIZE_STRING);
        $valorParcela = filter_var($data['valor'],FILTER_SANITIZE_STRING);

        $vars['page'] = 'form';
        $vars['dataVencimento'] = $dataVencimento;
        $vars['valorParcela'] = $valorParcela;
        /*setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
        date_default_timezone_set('America/Manaus');*/

        $dataAtual = date('Y-m-d');

        if(!is_numeric($valorParcela)) {
            $vars['error'] = 'Insira um valor de parcela válido!';
        }else{
            $data1 = new \DateTime($dataVencimento);
            $data2 = new \DateTime($dataAtual);
            $dataDiferenca = array($data1->diff($data2)->y * 12 + $data1->diff($data2)->m,$data1->diff($data2)->d);

            if(strtotime($dataVencimento) < strtotime('1970-01-01')){
                $vars['alert'] =  'Digite uma data válida!';
            }else{
                $vars['dados'] =  true;
                if(strtotime($dataAtual) > strtotime($dataVencimento)){//caso for maior, entao, juros
                    $this->juros($dataDiferenca, $valorParcela);
                }
                else if(strtotime($dataAtual) < strtotime($dataVencimento))//caso for menor, entao, desconto
                    $this->desconto($dataDiferenca, $valorParcela);
                else
                    $vars['alert'] =  'Sua parcela vence hoje!';
            }

            $vars['quantidadeDias'] = $data1->diff($data2)->days;
            $vars['valorJurosDesconto'] = number_format($this->valorJurosDesconto, 2);
            $vars['valorParcelaAtualizado'] =  number_format($this->valorAtualizadoParcela, 2);
        }

        return $this->view->render($response,'/template.phtml',$vars);
    }
    public function juros($dataDiferenca, $valorParcela){
        $valorAnterior = $valorParcela;

        for($i = 1; $i <= $dataDiferenca[0]; $i++){
            $acrescimo = 0.11 * $valorParcela;
            $valorParcela += $acrescimo;
        }
        for($i = 1; $i <= $dataDiferenca[1]; $i++){
            $acrescimo = 0.00367;
            $valorParcela += $acrescimo;
        }
        $this->valorAtualizadoParcela = $valorParcela;
        $this->valorJurosDesconto = $this->valorAtualizadoParcela - $valorAnterior;
    }
    public function desconto($dataDiferenca, $valorParcela){
        $valorAnterior = $valorParcela;

        for($i = 1; $i <= $dataDiferenca[0]; $i++){
            $decrescimo = 0.015 * $valorParcela;
            $valorParcela -= $decrescimo;
        }
        for($i = 1; $i <= $dataDiferenca[1]; $i++){
            $decrescimo  = 0.0005;
            $valorParcela -= $decrescimo;
        }
        $this->valorAtualizadoParcela = $valorParcela;
        $this->valorJurosDesconto = -($valorAnterior - $this->valorAtualizadoParcela);
    }
}
?>