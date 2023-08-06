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
* Colibri Classe Autenticacao
* Realiza o controle de acesso do uauário
*/

class CO_autenticacao{

	//PROPRIEDADES PROTEGIDAS

	protected

	$CO,
	$redirecionadorAjudante,
	$nomeTabela,
	$colunaUsuario,
	$regrasAdicionais,
	$colunaSenha,
	$usuario,
	$senha,
	$url=null,
	$controladorLogin='index',
	$acaoLogin='index',
	$controladorLogout='index',
	$acaoLogout='index',
	$nomeSessao='Usuarioautenticado'//NOME QUE SERA ATRIBUIDO A SESSAO DE AUTENTICACAO

	;

	



	/*
	* Construtor
	* Recupera instancia super objeto, se conecta ao banco de dados e 
	* carrega ajudantes e blibliotecas necessarias
	*/

	public function __construct(){

		$this->CO =& obter_instancia();

		$this->CO->carregar->ajudante( array('sessao') );

		iniciasessao();

		$this->CO->carregar->biblioteca( 'redirecionador' );

		return $this;

	}




	//-----------------------------------------------------------------------------------------------




	/*
	* Define nome da tabela a ser usada
	*/

	public function montaNometabela( $tabela ){

		$this->nomeTabela= $tabela;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------

	


	/*
	* Define coluna do usuario
	*/

	public function montaColunausuario( $coluna ){

		$this->colunaUsuario= $coluna;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------



	/*
	* Define coluna da senha
	*/

	public function montaColunasenha( $coluna ){

		$this->colunaSenha= $coluna;
		return $this;

	}



	//-----------------------------------------------------------------------------------------------

	

	/*
	* Define regras adicionais
	*/


	public function montaRegras( array $regras ){

		$this->regrasAdicionais= $regras;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------

	

	
	/*
	* Define usuario
	*/

	public function montaUsuario( $usuario ){

		$this->usuario= $usuario;
		return $this;

	}



	//-----------------------------------------------------------------------------------------------



	/*
	* Define senha
	*/


	public function montaSenha( $senha ){

		$this->senha= $senha;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------

	

	/*
	* Define parametros para url
	*/

	public function montaparametrosUrl( $nome, $valor ){

		$this->CO->redirecionador->defineparametrosUrl($nome, $valor);
		return $this;

	}




	//-----------------------------------------------------------------------------------------------

	

	/*
	* Define controlador e acao para redirecionamento apos o login
	*/

	public function montaLogincontroladoracao( $controlador, $acao ){

		$this->controladorLogin= $controlador;
		$this->acaoLogin= $acao;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------

	
	

	/*
	* Define controlador e acao para redirecionamento apos o login
	*/

	public function montaUrlerrologin( $url ){

		$this->url= $url;
		return $this;

	}





	//-----------------------------------------------------------------------------------------------



	/*
	* Define a url de redirecionamento em caso de logout
	*/

	public function montaLogouturl( $url ){

		$this->url= $url;
		return $this;

	}




	//-----------------------------------------------------------------------------------------------



	/*
	* Define controlador e acao para redirecionamento em caso de logout
	*/

	public function montaLogoutcontroladoracao( $controlador, $acao ){

		$this->controladorLogout= $controlador;
		$this->acaoLogout= $acao;
		return $this;

	}



	//-----------------------------------------------------------------------------------------------

	

	/*
	* Define nome da sessao
	*/

	public function montaNomesessao( $nome ){

		$this->nomeSessao= $nome;
		return $this;

	}

	


	//-----------------------------------------------------------------------------------------------

	

	/*
	* Realiza o login
	*/

	public function login( $lembrarlogin=false, $login, $ajax=true ){

		$this->CO->carregar->bancodedados();

		$this->CO->bd->tabela= $this->nomeTabela;

		$where[]= array( $this->colunaUsuario,'=',$this->usuario,'AND' );

		//DEFINE AS REGRAS ADICIONAIS
		if( count( $this->regrasAdicionais ) > 0 && is_array( $this->regrasAdicionais ) ){

			foreach( $this->regrasAdicionais as $regras ){

				$where[]= $regras;//MONTA AS REGRAS ADICIONAIS array('coluna','operador','valor','continuador')

			}

		}


		$where[]= array($this->colunaSenha,'=',$this->senha,'');

		$sql= $this->CO->bd->setfetch()->ler($where, '1');

		if( $this->CO->bd->linhas>0 ){

			
			criasessao( md5( $this->nomeSessao ), true );
			criasessao( $this->nomeSessao.'_Usuariodados', $sql );
			criasessao( $this->nomeSessao.'_Datalogin', date("Y/m/d H:i:s") );

			$this->CO->redirecionador->irparacontroladorAcao( $this->controladorLogin, $this->acaoLogin );

		}else{

			$this->CO->redirecionador->irparaUrl( $this->url );

		}

	}

	

	//-----------------------------------------------------------------------------------------------

	

	/*
	* Realiza o logout
	*/
	public function logout(){

		excluisessao( md5( $this->nomeSessao ) );
		excluisessao( $this->nomeSessao."_Usuariodados" );
		/* session_destroy(); Foi desabilitado pois ao deslogar da central do cliente deslogava do painel */

		if( !verificasessao( md5( $this->nomeSessao ) ) && !verificasessao( $this->nomeSessao."_Usuariodados" ) ){

			if( is_null( $this->url ) ){

				$this->CO->redirecionador->irparacontroladorAcao( $this->controladorLogout, $this->acaoLogout );

			}else{

				$this->CO->redirecionador->irparaUrl( $this->url );

			}

		}

		return $this;

	}



	//-----------------------------------------------------------------------------------------------

	

	/*
	* Checa se o usuario esta logado
	*/

	public function checaLogin( $condicao ){

		switch($condicao){

			case "boleano":

				if( !verificasessao( md5( $this->nomeSessao ) ) ){

					return false;

				}else{

					return true;

				}

			break;

			case "redirecionar":

				if( !verificasessao( md5( $this->nomeSessao ) ) ){

					if( is_null( $this->url ) ){

						$this->CO->redirecionador->irparacontroladorAcao( $this->controladorLogin,$this->acaoLogin );

					}else{

						$this->CO->redirecionador->irparaUrl( $this->url );

					}

				}

			break;

			case "parar":

				if( !verificasessao( md5( $this->nomeSessao ) ) ){

					exit;

				}

			break;

		}

	}




	//-----------------------------------------------------------------------------------------------

	

	/*
	* Retorna os dados do usuario
	*/

	public function dadosUsuario( $coluna ){

		$sessao= selecionasessao( $this->nomeSessao."_Usuariodados");
		return $sessao[$coluna];

	}

}

?>