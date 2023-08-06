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
* Colibri Classe Chave
* Esta classe permite a geração de chaves numericas e alfanumericas aleatorias 
*
*/

class CO_Chave{

	/**
	* Valida a existencia da chave na tabela e coluna definidas
	* @return inteiro
	*/

	private function validabd( $chave, $tabela, $coluna ){

		$CO =& obter_instancia();//Recupera a instancia do superobjeto

		$CO->carregar->bancodedados();

		$CO->bd->tabela= $tabela;

		$where[]= array( $coluna,'=',$chave,'');

		$CO->bd->ler( $where );

		return $CO->bd->linhas;

	}





	//-----------------------------------------------------------------------------------------------





	/*
	* Gera a chave com base nos dados informados
	* Retorno string
	*/

	public function geraChave( $tamanho, $tipo='', $bd='' ){

		switch($tipo){

			case "numerico":

				$hash= "7534691820";

			break;

			case "senha":

				$hash= "0a^b-1c*d2_ef_3g*h4!i_j5!kl^6mn?7o@p8*qr9s=t_uv^xw!yz";

			break;

			default:

				$hash= "0ab1cd2ef3gh4ij5kl6mn7op8qr9stuvxwyz";

			break;

		

		}


		do{

			$chave="";

			$c = strlen($hash)-1; // strlen conta o nº de caracteres da variável $numeros

			for( $x=0; $x < $tamanho; $x++ ){ // A função mt_rand() tem objetivo de gerar um valor aleatório

				$rand = mt_rand( 0,$c );//Escolhe aleatoriamente um caractere do hash

				if ( $hash[$rand]==0 && $x==0 ){ 
					$x--; 
				}else{
					$chave .= $hash[$rand];//Inclui o caractere selecionado á formacao
				}

			}

			if( is_array( $bd ) ){

				$comparadorloop= $this->validabd( $chave,$bd[0],$bd[1] );

			}else{

				$comparadorloop= 0;

			}


		}while( $comparadorloop );

		return $chave;


	}



}

?>