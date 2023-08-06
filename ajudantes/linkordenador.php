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
* Colibri Função de links de ordenaçãos
* Monta os links de ordenação
* Retorno array
*/

function montaOrdenadores( array $ordenadores, $urlprincipal ){

	$CO =& obter_instancia();

	//SE EXISTE A BUSCA INCLUI NOS PARAMETROS
	if( isset( $_POST['busca'] ) && !empty( $_POST['busca'] ) ){ $CO->url->incluiParametro( 'busca', $_POST['busca'] ); }

	$parametros= $CO->url->parametros;//RECUPERA OS PARAMETROS EXISTENTES

	//REMOVE ORDENADOR E TIPOORDENACAO PARA EVIDAR DUPLICIDADES NA URL
	unset( $parametros["ordenador"] );
	unset( $parametros["tipoordenacao"] );

	$stringparametros= '' ;//STRING PARAMETROS INICIA VAZIA

	//SE EXISTEM MONTAMOS OS PARAMETROS
	if( count( $parametros ) ){

		foreach( $parametros as $chavepar => $valorpar ){
			$stringparametros[]= $chavepar.'/'.$valorpar;
		}

		$stringparametros= implode('/',$stringparametros).'/';

	}

	$ord_atual= $CO->url->obterParametro('ordenador');//RECUPERA ORDENADOR ATUAL

	$tipo_atual= $CO->url->obterParametro('tipoordenacao');//RECUPERA TIPO DE ORDENADOR ATUAL

	foreach( $ordenadores as $ordenador ){

			$rotulo= trim( $ordenador[0] );//FORMATA ROTULO

			$id= trim( mb_strtolower( $ordenador[1],'UTF-8' ) );//FORMATA ID

			$tipoordenacao= ( $ord_atual==$id && $tipo_atual=='asc' )? 'desc' : 'asc' ;

			$classatual= ( $ord_atual==$id )? 'ordenaratual'.$tipo_atual : '' ;

			$linksordenacao[$ordenador[1]]= '<a title="'.strip_tags( $rotulo ).'" name="'.$id.'" href="'.$urlprincipal.'/'.$CO->roteador->controlador.'/'.$CO->roteador->acao.'/'.$stringparametros.'ordenador/'.$id.'/tipoordenacao/'.$tipoordenacao.'" class="ordenar '.$classatual.'">'.$rotulo.'</a>';

	}

	return $linksordenacao;

}

?>