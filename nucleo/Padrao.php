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
* Funções padrão do Framework
*/

/*
* Carrega classe informada
*/

if ( ! function_exists( 'carrega_classe' ) ) {

	function &carrega_classe( $classe, $dir='nucleo', $prefixo= 'CO_' ){

		static $classes= array();

		// Does the class exist?  If so, we're done...

		if ( isset( $_classes[$classe] ) ){

			return $_classes[$classe];

		}

		$nomeclasse = FALSE;

		foreach ( array( CAMINHOAPP,CAMINHOSIS ) as $caminho ) {

			if( file_exists( $caminho.$dir.'/'.$classe.'.php' ) ){

				$nomeclasse= $prefixo.$classe;

				require_once( $caminho.$dir.'/'.$classe.'.php' );

				break;

			}

		}

		if ( $nomeclasse === FALSE ){

			exit('Classe não localizada: '.$classe.'.php');

		}

		classes_carregadas( $classe );

		$classes[ $classe ] = new $nomeclasse;

		return $classes[ $classe ];

	}


}




//--------------------------------------------------------------------------------------




/*
* Registra as classes carregadas para controle
*/

if ( ! function_exists('classes_carregadas')){

	function &classes_carregadas( $classe = '' ){

		static $carregadas = array();

		if ($classe != ''){

			$carregadas[strtolower($classe)] = $classe;

		}

		return $carregadas;

	}

}




//--------------------------------------------------------------------------------------





/*
* Invoca a classe de Excessoes e grava log de erro
*/

function manipulador_excessoes( $errno, $errstr, $errfile, $errline ){

	$erro =& carrega_classe( 'Excessoes', 'nucleo' );

	$erro->gravalogErro( $errno, $errstr, $errfile, $errline );

}

?>