<?php
/**
* Colibri
*
* Framework de desenvolvimento PHP 5 ou superior
* @author Time de desenvolvimento Organy
* VERSÃO 1.0
*
*/



/**
* Define a versao do Colibri
*/
define( 'CO_VERSAO','1.0' );

/*
* Define a pasta da aplicacao
*/
if( realpath( $sistema ) !== FALSE && is_dir( $sistema ) ){
	define('CAMINHOSIS', rtrim( realpath( $sistema ),'/' ).'/' );
}else{
	exit( 'A pasta do sistema parece nao estar correta, por favor abra o arquivo index.php e cheque por essa configuracao.' );
}


/*
* Define a pasta da aplicacao
*/
if( realpath( $aplicacao ) !== FALSE && is_dir( $aplicacao ) ){
	define('CAMINHOAPP', rtrim( realpath( $aplicacao ),'/' ).'/');
}else{
	exit( 'A pasta de sua aplicacao parece nao estar correta, por favor abra o arquivo index.php e cheque por essa configuracao.' );
}


/*
*--------------------------------------------------------------------------------
* Carrega as constantes de configuração do framework
*--------------------------------------------------------------------------------
*/
require( CAMINHOAPP.'config/config.php' );


/*
 * ------------------------------------------------------
 *  Dedine o tempo de execução de scripts
 * ------------------------------------------------------
 */
if ( function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0 )
{
	@set_time_limit(300);
}


/*
*--------------------------------------------------------------------------------
* Carrega as funções padrao
*--------------------------------------------------------------------------------
*/
require( CAMINHOSIS.'nucleo/Padrao.php' );


/*
*--------------------------------------------------------------------------------
* Carrega a classe de url
*--------------------------------------------------------------------------------
*/
$URL =& carrega_classe( 'URL','nucleo' );


/*
*--------------------------------------------------------------------------------
* Carrega a classe do roteador
*--------------------------------------------------------------------------------
*/
$ROT =& carrega_classe( 'Roteador','nucleo' );


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


if( ERR_LOGS ){ set_error_handler( 'manipulador_excessoes' ); }


/*
*--------------------------------------------------------------------------------
* inclui a classe controlador
*--------------------------------------------------------------------------------
*/
require( CAMINHOSIS.'nucleo/Controlador.php' );



/*
*--------------------------------------------------------------------------------
* Retorna a instancia do super objeto controlador
*--------------------------------------------------------------------------------
*/
function &obter_instancia(){
	return CO_Controlador::obter_instancia();
}


/*
*--------------------------------------------------------------------------------
*Iclui o controlador secundario da aplicação
*--------------------------------------------------------------------------------
*/
$arquivo_controlador = CAMINHOAPP.CONTROLADORES.$ROT->controlador.'/'.$ROT->controlador.'Controlador.php';


try{


	if( file_exists( $arquivo_controlador ) ){
		
		require_once( $arquivo_controlador );//INCLUI O ARQUIVO CONTROLADOR


		//INSTANCIAMOS A MESMA
		$classe= $ROT->controlador.'Controlador';
		
		try{

			if( class_exists( $classe ) ){
				
				$co_classe= new $classe;//INSTANCIA A CLASSE DO CONTROLADOR
				

				//SE O METODO EXISTE CHAMAMOS O MESMO
				$metodo= $ROT->acao.'Acao';
				
				try{

					if( method_exists( $classe,$metodo ) ){
						
						
						if(method_exists($classe,'inicializacao')){//CHAMA O INICIALIZADOR DO CONTROLADOR
							$co_classe->inicializacao();
						}
						
						$co_classe->$metodo();//EXECUTA O METODO
						
						
					}else{
						throw new Exception("Metodo '$metodo' nao existe na classe '$classe'<br>");
					}

				}catch (Exception $e) {
					echo $e->getMessage();
				}


			}else{
				throw new Exception("Classe '$classe' nao existe no arquivo '$arquivo_controlador'<br>");
			}
		}catch (Exception $e) {
			echo $e->getMessage();
		}


	}else{
		throw new Exception('Arquivo controlador '.$arquivo_controlador.' nao encontrado<br>');
	}
	
}catch (Exception $e) {
	echo $e->getMessage();
}



?>