<?php



class CO_redirecionador{

	

	protected $parametrosurl= array();

	protected $CO;

	



	public function __construct(){

		$this->CO =& obter_instancia();

	}

	

 	

	//REDIRECIONA PARA PAGINA

	protected function ir( $url ){


		header("Location:".rtrim(CAMINHO, "/").'/'.$url);

	}

	

	

	

	public function obtercontroladorAtual(){

		

		return $this->CO->roteador->controlador;

	

	}

	

	

	public function obteracaoAtual(){

	

		return $this->CO->roteador->acao;

	

	}

	

	

	//RECUPERA E MONTA OS PARAMETROS DA URL

	protected function obterparametrosUrl(){

		

		$parametrosurl= "";

		

		

		//print_r($this->parametros); exit;

		

		

		$aux=1;

		foreach( $this->CO->url->parametros as $nome=>$valor ){

			

			$bar= ( count( $this->CO->url->parametros ) < $aux )? '/' : '' ;//BARRA NO FINAL

			

			$parametrosurl.= ( empty($parametrosurl) )?  $nome.'/'.$valor.$bar :  '/'.$nome.'/'.$valor.$bar ;

		

		$aux++;	

		}

		

		return $parametrosurl;

	

	}

	

	

	//REMOVE PARAMETRO

	public function removeParametro($parametro){

		

		unset( $this->CO->url->parametros[$parametro] );

		

		array_values( $this->CO->url->parametros );

		

		return $this;

		

	}

	

	

	//RECEBE OS PARAMETROS DA URL

	public function defineparametrosUrl( $nome, $valor ){

		

		$this->CO->url->parametros[$nome]= $valor;

		return $this;

	}

	



	//ENVIA PEDIDO PARA REDIRECIONAR PARA UM CONTROLADOR

	public function irparaControlador( $controlador ){

		

		$this->ir( $controlador.'/index/'.$this->obterparametrosUrl() );

	

	}

	

	//ENVIA PEDIDO PARA REDIRECIONAR PARA UMA ACAO

	public function irparaAcao( $acao ){

		

		$this->ir( $this->obtercontroladorAtual().'/'.$acao.'/'.$this->obterparametrosUrl() );

	

	}

	

	//ENVIA PEDIDO PARA REDIRECIONAR PARA UM CONTROLADOR E UMA ACAO

	public function irparacontroladorAcao( $controlador, $acao , $parametros=null ){

		$parametrosurl= $this->obterparametrosUrl();
		
		$barraparametros= ( !empty( $parametrosurl ) || !is_null( $parametros ) )? '/'  : '' ;

		

		$barraparametrosfuncao=( !empty( $parametrosurl ) && !is_null( $parametros )  )?  '/' : '' ;
		

		$this->ir( $controlador.'/'.$acao.$barraparametros.$this->obterparametrosUrl().$barraparametrosfuncao.$parametros );

		

	}

	

	//ENVIA PEDIDO PARA REDIRECIONAR PARA O INICIO

	public function irparaInicio(){

		

		$this->ir( 'index' );

		

	}

	

	

	//REDIRECIONA PARA UMA URL

	public function irparaUrl( $url ){

	

		header("Location: ".$url);

	

	}

	





}





?>