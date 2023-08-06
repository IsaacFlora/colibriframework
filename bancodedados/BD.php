<?php

function &BD(){


	if ( ! file_exists( CAMINHOAPP.'config/bancodedados.php') ){

		exit( 'O arquivo de configuracao do banco bancodedados.php nao existe!' );

	}


	include( CAMINHOAPP.'config/bancodedados.php' );

	require_once( CAMINHOSIS.'bancodedados/PDO.php');

	$BD= new CO_PDO;

	$BD->prefixo_tb= $bd['prefixo'];//Define o prefixo das tabelas
	
	$BD->conectar( $bd['driver'], $bd['host'], $bd['banco'], $bd['usuario'], $bd['senha'] );

	return $BD;

}

?>