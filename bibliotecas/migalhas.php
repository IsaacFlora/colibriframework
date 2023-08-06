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
* Colibri Classe Arquivos
* Cria um link de navegação bradcrumbs ( links migalhas de pão )
*/

class CO_migalhas{
	
	//Propriedades públicas
	public $sessao= array( array( "Home","index","" ) );
	public $separador= '';

	private $elementopai= 'ol';//Define o tipo de elemento pai da estrutura do bradcrumbs
	private $classeelementoatual= 'atual';//Define a classe do elemento atual ( pagina atual )


	/*
	* Método que define o tipo de elemento pai da estrutura do bradcrumbs
	*/

	public function definetipoelemento( $tipo ){

		switch ($tipo) {
			case 'div':
					$this->elementopai='div';//Altera o tipo para div
				break;
		}

		return $this;

	}



	//--------------------------------------------------------------------------------------------------




	/*
	* Método que a classe do ultimo elemento do bradcrumbs ( elemento atual )
	*/

	public function defineclasselementoatual( $classe ){

		$this->classeelementoatual= $classe;

		return $this;

	}



	/*
	* Método que define uma sessão
	*/

	public function defineSessao( $sessao, $inicio=false ){

		if( is_array( $sessao ) ){

			if( $inicio ){ unset( $this->sessao ); }
			$this->sessao[]= array( $sessao[0], $sessao[1], $sessao[2] );

		}

		return $this;

	}



	//-----------------------------------------------------------------------------------------------



	/*
	* Metodo que cria e retorna o html do bradcrumbs
	* Retorno string ( html )
	*/

	public function menu( $class=null, $idelementopai=null ){

		$class=( is_null( $class ) )? 'breadcrumb' : $class ;
		$idelementopai=( is_null( $idelementopai ) )? '' : 'id="'.$idelementopai.'"' ;

		//Inicio da renderização do elemento pai do bradcrumbs
		$menu_html= '<'.$this->elementopai.'  class="'.$class.'" '.$idelementopai.' >';

		//Loop que cria o html da navegação
		$aux=1;
		foreach ( $this->sessao as $sessao ) {
			
			if( !empty( $sessao[1] ) ){
				$conteudo= '<a href="'.CAMINHO.'/'.$sessao[1].'" title="'.$sessao[0].'">'.$sessao[2].' '.$sessao[0].'</a>';
			}else{
				$conteudo= '<span class="'.$this->classeelementoatual.'" >'.$sessao[2].' '.$sessao[0].'</span>';
			}


			//Define se é uma ol ou outro elemento
			$menu_html.= ( $this->elementopai=='ol' )? '<li>'.$conteudo.'</li>'.$this->separador : $conteudo.$this->separador ;


		}
		
		//Fecha a renderização do elemento pai
		$menu_html .= '</'.$this->elementopai.'>';

		return $menu_html;

	}


}



?>