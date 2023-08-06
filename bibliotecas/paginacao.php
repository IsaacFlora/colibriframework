<?php

/*
* Colibri
*
* Framework de desenvolvimento PHP 5 ou superior
* Autores Time de desenvolvimento Organy
* VERSÃO 1.0
*
*/

/*
* Colibri Classe Paginacao
* Esta classe permite a geração de chaves numericas e alfanumericas aleatorias 
*
*/


class CO_paginacao{


	//PROPRIEDADES PUBLICAS
	public $pagina_atual;//Pagina atual
	public $resultadospor_pagina= 10;//Define a quantidade de resultados a serem exibidos por página
	public $limit;//Define o inico da paginacao para a consulta
	public $maximo_links=8;//Define quantos links serão exibidos na paginacao

	//PROPRIEDADES PRIVADAS
	protected $total_registros;//Total de registros a serem paginados
	protected $total_paginas;//Total de paginas
	protected $paginacao_pronta;//Links da paginacao
	protected $resumo_pronto;//Html resumo da paginacao
	protected $caminho;
	protected $botoes_navegacao=true;
	protected $CO;//Super objeto

	public function __construct(){

		$this->CO =& obter_instancia();

		return $this;

	}




	/*
	* Metodo que defina o limit para consultas
	*/

	public function definelimit( $paginaatual, $registrosporpagina, $limitepadrao=10 ){

		$paginaatual= ( is_numeric( $paginaatual ) )?  $paginaatual : 1 ;//Define a pagina atual

		$limite= ( is_numeric( $registrosporpagina ) )? $registrosporpagina : $limitepadrao ;//Define o limite

		$this->limit= (( $paginaatual-1 ) * $limite).','.$limite;

		return $this;

	}



	/*
	* Cria a paginacao
	* Retorno string ( html )
	*/

	public function paginar( $links=false ){

		$pagina_atual= $this->CO->url->obterParametro( 'paginacao' );
		$this->pagina_atual= ( !empty( $pagina_atual ) )?  $pagina_atual :  1 ;

		//Define o valor inicial
		$valorinicial=1;

		if( $this->total_paginas > $this->maximo_links ){

			$quantidade_resultados= $this->maximo_links;

			if( $this->pagina_atual > floor( ( $this->maximo_links/2 ) ) && ( $this->pagina_atual+floor( $this->maximo_links/2 ) ) <= $this->total_paginas ){

				$valorinicial = $this->pagina_atual - floor( ( $this->maximo_links/2 ) );

				$quantidade_resultados= $this->pagina_atual + floor( ( $this->maximo_links/2 ) );

			}


			if( $this->pagina_atual > floor( ( $this->maximo_links/2 ) ) && ( $this->pagina_atual+floor( $this->maximo_links/2 ) ) > $this->total_paginas ){

				$valorinicial = $this->total_paginas- ( $this->maximo_links-1 );

				$quantidade_resultados= $this->total_paginas;

			}


		}else{

			$quantidade_resultados= $this->total_paginas;

		}


		//LOOP QUE MONTA OS LINKS DA PAGINACAO

		$links= array();
		for( $i=$valorinicial; $i <= $quantidade_resultados; $i++ ){

			if( $i != $this->pagina_atual ){

				$links['link'][]= CAMINHO.$this->caminho.'/'.$this->CO->roteador->controlador.'/'.$this->CO->roteador->acao.$this->preparaParametros().'/paginacao/'.$i;
				$links['numeracao'][]= $i;

			}else{

				$links['link'][] = '';
				$links['numeracao'][]= $i;
				$links['numeracaoatual']= $i;

			}

		}


		if( $this->total_paginas > 1 ){

				
				//BOTOES DE NAVEGACAO
				if( $this->botoes_navegacao && $valorinicial > 1 ){

					$botoesnavegacao['primeira']= CAMINHO.$this->caminho.'/'.$this->CO->roteador->controlador.'/'.$this->CO->roteador->acao.$this->preparaParametros().'/paginacao/1';

					$botoesnavegacao['anterior']= CAMINHO.$this->caminho.'/'.$this->CO->roteador->controlador.'/'.$this->CO->roteador->acao.$this->preparaParametros().'/paginacao/'.( $this->pagina_atual-1 );
					

				}


				if( $this->botoes_navegacao && $this->total_paginas > $this->maximo_links && ( $this->total_paginas-$this->pagina_atual ) > floor( ( $this->maximo_links/2 ) ) ){
					

					$botoesnavegacao['ultima']= CAMINHO.$this->caminho.'/'.$this->CO->roteador->controlador.'/'.$this->CO->roteador->acao.$this->preparaParametros().'/paginacao/'.$this->total_paginas;

					$botoesnavegacao['proxima']= CAMINHO.$this->caminho.'/'.$this->CO->roteador->controlador.'/'.$this->CO->roteador->acao.$this->preparaParametros().'/paginacao/'.( $this->pagina_atual+1 );


					


				}

				//BOTOES DE NAVEGACAO

		}


		//RETORNA UMA STRING CONTENDO OS LINKS DA PAGINACAO

		return array( $links, $botoesnavegacao, $this->pagina_atual );

	}




	//-----------------------------------------------------------------------------------------------



	/*
	* Cria o resumo
	* Retorno string ( html )
	*/

	public function resumo(){

		$incremento= ( $this->total_registros!=0 )? 1 : 0 ;

		$inicio= ( ( $this->pagina_atual * $this->resultadospor_pagina ) - $this->resultadospor_pagina ) + $incremento;
		$fim= ( $this->pagina_atual*$this->resultadospor_pagina > $this->total_registros )? $this->total_registros : $this->pagina_atual*$this->resultadospor_pagina ;

		return  array( "inicio"=>$inicio, "fim"=>$fim, "total"=>$this->total_registros );

	}






	//-----------------------------------------------------------------------------------------------


	

	/*
	* Prepara os parametros
	*/

	private function preparaParametros(){

		
		unset( $this->CO->url->parametros['paginacao'] );

		$checa_parametros= ( count( $this->CO->url->parametros ) )?  true :  false ;

		if( $checa_parametros ){

			$parametros= array();

			foreach( $this->CO->url->parametros as $chave => $valor ){

				$parametros[]= $chave.'/'.$valor;

			}

			$parametros= '/'.implode('/',$parametros);

		}else{

			$parametros='';

		}

		return $parametros;

	}




	//-----------------------------------------------------------------------------------------------



	
	/*
	* Remove um parametro
	*/

	public function removeParametro( $parametro ){

		unset( $this->CO->url->parametros[$parametro] );

		array_values( $this->CO->url->parametros );

		return $this;

	}




	//-----------------------------------------------------------------------------------------------




	/*
	* Define o total de registros retornados na consulta
	*/

	public function defineTotalregistros( $linhas ){

		$this->total_registros= $linhas;

		$this->total_paginas= ceil( $this->total_registros/$this->resultadospor_pagina );

		return $this;

	}




	//-----------------------------------------------------------------------------------------------




	/*
	* Define o numero de resultados a serem exibidos por pagina
	*/

	public function defineResultadosporpagina( $itens, $limitepadrao=10 ){

		$this->resultadospor_pagina= ( is_numeric( $itens ) )? $itens : $limitepadrao ;//Define o limite

		return $this;

	}




	//-----------------------------------------------------------------------------------------------




	/*
	* Define o caminho adicional para paginacao
	*/

	public function defineCaminho( $caminho ){

		$this->caminho= $caminho;

		return $this;

	}



}


?>