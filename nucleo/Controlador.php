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
* Colibri Classe de Controle da Aplicação
*/
class CO_Controlador{

	/*
	* Armazena a instancia das classes carregadas
	*/
	private static $instancia;




	/*
	* Construtor, cria a instancia do super objeto e inicializa o auto-carregamento
	*/

	public function __construct(){
		
		self::$instancia =& $this;


		//ATRIBUI TODOS OS OBJETOS DE CLASSE QUE FORAM INSTANCIADOS
		//NA INICIALIZACAO ( Colibri.php ) PARA VARIAVEIS DE CLASSE LOCAIS
		foreach ( classes_carregadas() as $var => $classse ){
			$this->$var =& carrega_classe( $classse,'nucleo' );
		}
		

		$this->carregar =& carrega_classe( 'Carregador', 'nucleo' );

		$this->carregar->inicializar();

	}


	//--------------------------------------------------------------------------------------


	/*
	* Recupera a instancia do superobjeto
	* Retorno objeto
	*/
	public static function &obter_instancia(){
		return self::$instancia;
	}


}

?>