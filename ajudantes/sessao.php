<?php

//INICIA DADOS DE SESSAO
function iniciasessao( $tempo=null ){
	
	
	$inatividade= ( !is_null($tempo) )? $tempo*60 : 180*60 ;//DEFINE O TEMPO DE VIDA DA SESSAO
	
	
	if( !isset($_SESSION) ) {//SE A SESSAO NOA EXISTE CRIA A MESMA
		session_start();
	}


	//DESTROY CASO O TEMPO TENHA EXPIRADO
	if (isset($_SESSION['tempolimite'])) {

		$vida_sessao = time() - $_SESSION['tempolimite'];
		if ($vida_sessao > $inatividade) {
			
			session_start();
			session_destroy();
			session_start();
			
		}
	}
	//RENOVA O TEMPO DE LIMITE
	else{
		$_SESSION['tempolimite'] = time();
		
	}

}
	
	
	
	
//CRIA SESSAO
function criasessao( $nome, $valor ){

	$_SESSION[$nome]= $valor;
	
}
	
	
	
	
//ALTERA SESSAO
function alterasessao( $nome, $valor ){

	$_SESSION[$nome]= $valor;

}
	
	
	
	
//RETORNA UMA SESSAO
function selecionasessao( $nome ){

	return $_SESSION[$nome];

}
	
	
	
	
//EXCLUI SESSAO
function excluisessao( $nome ){

	unset( $_SESSION[$nome] );

}
	
	
	
	
//VERIFICA SE SESSAO EXISTE
function verificasessao($nome){

	return isset( $_SESSION[$nome] );

}

?>