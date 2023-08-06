<?php

/**
* Colibri
*
* Framework de desenvolvimento PHP 5 ou superior
* @author Time de desenvolvimento Organy
* VERSÃO 1.0
*
*/



/*
* Classe Roteador
* Define o roteamento das requisicoes
*/

class CO_Roteador{


	private $requisicao;
	private $separador;
	public $controlador;
	public $acao;


	/*
	* Construtor, configura o controlador e acao
	*/

	public function __construct(){


		//CASO NÃO EXISTA UMA REQUISICAO DEFINIMOS O PADRAO 'index/index'
		$this->requisicao= (isset($_GET['requisicao'])) ? $_GET['requisicao'] : 'index/index' ;

		$this->separador= explode('/',$this->requisicao);//SEPARA AS AÇÕES DA REQUISIÇÃO POR '/'

		$this->controlador= $this->separador[0];//DEFINIMOS O CONTROLADOR

		//DEFINIMOS A ACAO

		$this->acao= ( !isset($this->separador[1]) || $this->separador[1]==NULL ) ?  'index' :  $this->separador[1] ;

	}





}





?>