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
* Classe de URLs
*/

class CO_URL{

	private $requisicao;
	private $separador;
	public $parametros;
	public $string_parametros;

	/*
	* Construtor, recupera e configura os parametros da url
	*/

	public function __construct(){

		//CASO NÃO EXISTA UMA REQUISICAO DEFINIMOS O PADRAO 'index/index'
		$this->requisicao= (isset($_GET['requisicao'])) ? $_GET['requisicao'] : 'index/index' ;
		$this->separador= explode('/',$this->requisicao);//SEPARA AS AÇÕES DA REQUISIÇÃO POR '/'

		//RECUPERA OS PARAMETROS
		unset($this->separador[0],$this->separador[1]);

		if( end($this->separador)==null ){  array_pop($this->separador); }//Retiramos do array parametros vazios no final

		$this->separador= array_values($this->separador);//Refaz o array

		if( !empty($this->separador) ){

			$i=0;
			foreach($this->separador as $valor ){

				( $i %2 ==0 )? $p_indices[]= $valor : $p_valores[]= $valor; ;//Se for par define como indice...

				$i++;

			}

		}else{

			$p_indices= array(); $p_valores= array();

		}

		if( count($p_indices) == count($p_valores) && !empty($p_indices) && !empty($p_valores) ){
			$this->parametros= array_combine($p_indices,$p_valores);
		}else{
			$this->parametros= array();
		}

	}

	//--------------------------------------------------------------------------------------

	/*
	* Retorna o valor do parametro requisitado
	* Retorno string
	*/

	public function obterParametro( $parametro=null ){

		return ( !is_null($parametro) && !empty($parametro) )? $this->parametros[$parametro] : false ;

	}



	//--------------------------------------------------------------------------------------


	/*
	* Retorna um array com os parametros
	* Retorno array
	*/

	public function obterParametros(  ){

		return $this->parametros;

	}

	//--------------------------------------------------------------------------------------

	/*
	* Retorna string completa dos parametros
	* Retorno string
	*/

	public function obterstringParametros( $parametros=null ){

		$stringparametros= '' ;//STRING LIMITE INICIA VAZIA

		$parametros= ( is_null( $parametros ) )? $this->parametros : $parametros ;

		if( count( $parametros ) ){

			foreach( $parametros as $chavepar => $valorpar ){

				$stringparametros[]= $chavepar.'/'.$valorpar;

			}

			$stringparametros= implode('/',$stringparametros);

		}

		return $stringparametros;//RETORNA A STRING DE PARAMETROS

	}

	//--------------------------------------------------------------------------------------

	/*
	* Inclui um parametro na matriz
	*/

	public function incluiParametro( $parametro, $valor ){

		$this->parametros[$parametro]=$valor;

		return $this;

	}

	//--------------------------------------------------------------------------------------

	/*
	* Remove um parametro da matriz
	*/

	public function removeParametro($parametro){

		unset($this->parametros[$parametro]);

		array_values($this->parametros);

		return $this;

	}


}

?>