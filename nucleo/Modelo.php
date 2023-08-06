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
* Modelo da aplicação
*/

class CO_Modelo{
	

	/*
	 * __get
	 *
	 * Permite aos modelos acessar todas as classes instanciadas no CO
	 * com a mesma sintaxe que os controladores
	 *
	 */
	function __get( $chave ){

		$CO =& obter_instancia();
		return $CO->$chave;
		
	}


}


?>