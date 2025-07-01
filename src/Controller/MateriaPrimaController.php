<?php
    namespace App\Controller;
    use App\Controller\AppController;

    class MateriaPrimaController extends AppController{

        public function initialize(): void
        {
            parent::initialize();
            $this->viewBuilder()->setLayout('principal'); // Define o layout padrão para as views deste controller
            $this->viewBuilder()->setLayout('materia');
        }

        public function ouro(){
            $this->set('materia', 'Ouro');
        }
    }
?>
