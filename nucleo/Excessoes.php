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
* Colibri Classe de manipulação de excessoes
*/
class CO_Excessoes{

	/*
	* Grava os logs de erros
	*/
	public function gravalogErro( $errno, $errstr, $errfile, $errline ){

		$stringerro= "Nivel do erro ( $errno ) - $errstr em $errfile na linha $errline\n";
		
		$caminho= CAMINHOAPP.'logserro';//CAMINHO DOS LOGS
		
		if( $errno!=8 ){
			
			//GRAVA LOG DE ERRO
			if( !is_dir($caminho) ){ mkdir($caminho, 0777); }//CHECA SE O DIR EXISTE E CRIAR DIR
			$fp = fopen($caminho."/".time().".txt", "w");// ABRE O ARQUIVO SOMENTE PARA ESCRITA COM PONTEIRO NO FIM ARQUIVO
			$escreve = fwrite($fp, date('d/m/Y H:i:s').PHP_EOL.$_SERVER['HTTP_REFERER'].PHP_EOL."$errno".PHP_EOL.$stringerro.PHP_EOL );//ESCREVE NO ARQUIVO
			fclose($fp);//FECHA O ARQUIVO
			//GRAVA LOG DE ERRO
		
		}
		
		
		// se retornar TRUE não faz o tratamento padrão do erro no PHP
		return TRUE;

	}




}


?>