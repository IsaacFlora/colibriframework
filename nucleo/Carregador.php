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
* Classe Carregador
* Carrega funções, bibliotecas, visoes
*/

class CO_Carregador{


	/*
	* Lista de caminhos para carregar ajudantes
	*/

	protected $caminhos_ajudantes= array();


	/*
	* Lista de caminhos para carregar bibliotecas
	*/

	protected $caminhos_bibliotecas= array();


	/*
	* Armazena as conexões realizadas
	*/

	protected $conexoes= array();



	/*
	* Metodo construtor
	* Define os caminhos das bibliotecas e ajudantes entre outros
	*/

	public function __construct(){

		$this->caminhos_ajudantes= array( CAMINHOAPP,CAMINHOSIS );

		$this->caminhos_bibliotecas= array( CAMINHOAPP.'bibliotecas/',CAMINHOSIS.'bibliotecas/' );

	}




	//--------------------------------------------------------------------------------------




	/*
	* Metodo autocarregamento
	* Invoca o metodo para carregamento automatico de ajudantes
	*/

	public function inicializar(){

		$this->autocarregar();

		return $this;

	}



	//--------------------------------------------------------------------------------------



	/*
	* Metodo de Visoes
	* Carrega o arquivo de visao especificado
	*/
	public function visao( $nome_arquivo, $parametros=null, $caminho=null  ){


		$CO =& obter_instancia();


		//Transforma os parametros em variaveis com prefixo

		if( is_array($parametros) && count($parametros) >0 ){

			extract($parametros, EXTR_PREFIX_ALL, 'visao');

			//Cria automaticamente as variaveis para visao
			if( defined('AUTOURLPAR_VISAO') && AUTOURLPAR_VISAO ){
				extract($CO->url->obterParametros(), EXTR_PREFIX_ALL, 'visaoauto'); 
			}

		}




		$_co_CO =& obter_instancia();

		foreach ( get_object_vars( $_co_CO ) as $_co_key => $_co_var){

			if ( ! isset( $this->$_co_key ) )

			{

				$this->$_co_key =& $_co_CO->$_co_key;

			}

		}



		//Define o caminho a ser usado para a visao
		$caminhovisao= ( !is_null( $caminho ) && !empty( $caminho ) )? $caminho : CAMINHOAPP ;

		//Define o arquivo
		$arquivo_visao= $caminhovisao.VISAO.$nome_arquivo;

		//Insere o arquivo
		try{

			if(file_exists($arquivo_visao)){
				

				return require_once($arquivo_visao);

				exit;

			}else{

				throw new Exception('Arquivo '.$arquivo_visao.' não encontrado<br>');

			}

		

		}catch (Exception $e) {

			echo $e->getMessage();

		}


	}


	//--------------------------------------------------------------------------------------



	/*
	* Metodo de ajudantes
	* Carrega o ajudante especificado pelo usuario
	*/

	public function ajudante( array $ajudantes ){


		foreach ( $ajudantes as $ajudante) {

			foreach ( $this->caminhos_ajudantes as $caminho ) {


				if( file_exists( $caminho.'ajudantes/'.$ajudante.'.php' ) ){

					include_once( $caminho.'ajudantes/'.$ajudante.'.php' );

					break;

				}

				

			}

			



		}

		

	}





	//--------------------------------------------------------------------------------------





	/*

	* Metodo que permite ao usuario carregar e instanciar classes de bibliotecas

	*/

	public function biblioteca( $biblioteca, $prefix=null, $caminho=null, array $parametros= array() ){



		$biblioteca = strtolower( $biblioteca );



		//Adiciona um novo caminho caso exista

		if( !is_null( $caminho ) ){ $this->caminhos_bibliotecas[]= trim( $caminho );  }


		$CO =& obter_instancia();



		if( is_null( $prefix ) ){



			$classe= 'CO_'.$biblioteca;



		}else if( $prefix=='' ){



			$classe= $biblioteca;



		}else{



			$classe= $prefix.$biblioteca;



		}





		if ( class_exists( $classe ) ) {

		    return;

		}


		foreach ( $this->caminhos_bibliotecas as $caminho ) {

			if( file_exists( $caminho.$biblioteca.'.php' ) ){

				

				include( $caminho.$biblioteca.'.php' );

				$CO->$biblioteca = new $classe( $parametros );
					

				break;

			}

		}



	}





	//--------------------------------------------------------------------------------------





	/*
	* Metodo de modelos
	* Permita ao usuario carregar e instanciar modelos
	*/

	public function modelo( $modelo, $condb=FALSE, $caminho=null ){

		$modelo = strtolower( $modelo );

		//Adiciona um novo caminho caso exista

		if( !is_null( $caminho ) ){ $this->caminhos_modelos[]= trim( $caminho );  }



		$CO =& obter_instancia();

		$caminhomodelo=( is_null( $caminho ) )? CAMINHOAPP.'modelos/'.$CO->roteador->controlador.'/'.$modelo.'Modelo.php' : $caminho.'/'.$modelo.'Modelo.php' ;

		$classemodelo= $modelo.'Modelo';

		if ( class_exists( $classemodelo ) ) {

		    return;

		}


		if( file_exists( $caminhomodelo ) ){


			if ( ! class_exists( 'CO_Modelo' ) ){

				$classe= carrega_classe( 'Modelo', 'nucleo' );

			}


			if( $condb !== FALSE ){

				$CO->carregar->bancodedados();

			}


			require_once( $caminhomodelo );

			$CO->$modelo = new $classemodelo();

			return;

		}

		// Modelo nao encontrado

		exit('Nao foi possivel localizar o modelo: '.$modelo);


	}




	//--------------------------------------------------------------------------------------




	/*
	* Metodo banco de dados
	* Carrega o gerenciador de banco de dados
	*/

	public function bancodedados(){

		//Se ja existir uma conexao com o banco ignora
		if ( in_array( $bd['banco'], $this->conexoes ) ) {

		    return;

		}

		$CO =& obter_instancia();

		require_once( CAMINHOSIS.'bancodedados/BD.php');

		$CO->bd='';

		$CO->bd =& BD();

		$this->conexoes[]= $bd['banco'];

	}




	//--------------------------------------------------------------------------------------



	/*
	* Metodo de autocarregamento
	* Carrega automaticamente os ajudantes configurados no config/autocarregamento.php
	*/

	private function autocarregar(){

		if( CAMINHOAPP.'config/autocarregamento.php'  ){

			include(CAMINHOAPP.'config/autocarregamento.php');

		}


		if ( ! isset( $autocarregar ) ){

			return FALSE;

		}


		// Autoload helpers and languages
		foreach ( array('ajudante') as $tipo ){

			if ( isset( $autocarregar[ $tipo  ] ) AND count( $autocarregar[ $tipo  ] ) > 0 ){

				$this->$tipo( $autocarregar[ $tipo  ] );

			}

		}

		// Autoload helpers and languages
		foreach ( array('biblioteca') as $tipo ){

			if ( isset( $autocarregar[ $tipo  ] ) AND count( $autocarregar[ $tipo  ] ) > 0 ){

				foreach ( $autocarregar[ $tipo  ] as $biblioteca ) {

					$this->$tipo( $biblioteca );

				}

			}

		}

	}

}

?>