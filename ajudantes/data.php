<?php 

/*
* Colibri
*
* Framework de desenvolvimento PHP 5 ou superior
* Autores Time de desenvolvimento Organy
* VERSÃO 1.0
* |-framework
* 	|-ajudantes
*
*/


/*
* Colibri Função que converte data no formato d/m/Y para o formato Americano Y/m/d
* Retorno string
*/

function dataBrUs( $data ){

	$expressaodata='/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|[1][0-2])\/((19|20)[0-9]{2})$/';

	$expressaodatahora= '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|[1][0-2])\/((19|20)[0-9]{2})(\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|[1-5][0-9]):(0[0-9]|[1-5][0-9]))?$/';

	$data= str_replace('-', '/', $data);

	if( preg_match($expressaodata, $data) ){

		$dataus=explode('/',$data);

		return $dataus[2].'-'.$dataus[1].'-'.$dataus[0];

	}else if( preg_match( $expressaodatahora, $data ) ){

		$datahora=explode(' ',$data);
		$dataus= explode('/',$datahora[0]);
		$hora= $datahora[1];

		return $dataus[2].'-'.$dataus[1].'-'.$dataus[0].' '.$hora;

	}else{

		return null;

	}

}


/*
* Colibri função que converte uma data em formato americano Y/m/d para o formato d/m/Y
* Retorno string
*/

function dataUsBr( $data ){

	$expressaodata='/^((19|20)[0-9]{2})\/(0[1-9]|[1][0-2])\/(0[1-9]|[12][0-9]|3[01])$/';

	$expressaodatahora= '/^((19|20)[0-9]{2})\/(0[1-9]|[1][0-2])\/(0[1-9]|[12][0-9]|3[01])(\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|[1-5][0-9]):(0[0-9]|[1-5][0-9]))?$/';
	

	$data= str_replace('-', '/', $data);

	if( preg_match( $expressaodata, $data ) ){

		$dataus=explode('/',$data);

		return $dataus[2].'-'.$dataus[1].'-'.$dataus[0];

	}else if( preg_match( $expressaodatahora, $data ) ){

		$datahora=explode(' ',$data);
		$dataus= explode('/',$datahora[0]);
		$hora= $datahora[1];

		return $dataus[2].'-'.$dataus[1].'-'.$dataus[0].' '.$hora;

	}else{

		return null;

	}

}




/*
* Colibri função que retorna a data por extendo
*/

function dataExtenso( $data, $tipo ){

	$padrao= '/^((19|20)[0-9]{2})(\/|-)(0[1-9]|[1][0-2])(\/|-)(0[1-9]|[12][0-9]|3[01])$/';//REGEX DATA US

	$data= ( preg_match($padrao, $data) )?  dataUsBr( $data )  : $data  ;//DATA TERNARIA

	if( $tipo=='diasemana' ){

		$diasemana= date( "w", strtotime( $data ) );

		$dias_semana= array( "Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado" );

		return $dias_semana[ $diasemana ];

	}else if( $tipo=='mes' ){

		$mesano= date("n", strtotime($data));

		$mes_ano= array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		return $mes_ano[ $mesano-1 ];

	}


}




/*
* Colibri função que retorna a diferença entre datas em horas, minutos ou dias
* Retorno string
*/

function dataDiferenca( $datainicial, $datafinal ){

	$datafinal= ( empty($datafinal) )? date('Y/m/d H:i:s') : trim($datafinal) ;
	$datainicial = strtotime(trim($datainicial));
	$datafinal = strtotime($datafinal);
	$ndias = floor(($datafinal - $datainicial) / 86400);
	$nHoras   = floor(($datafinal - $datainicial) / 3600);
	$nMinutos = floor( ($datafinal - $datainicial) / 60  );
	$nMinutosrestantes = $nMinutos%60;

	return array('difhoras'=>$nHoras, 'difminutos'=>$nMinutos, 'difdias'=> $ndias, 'difminutosrestantes'=>$nMinutosrestantes);

}




//-----------------------------------------------------------------------------------------------



/*
* Colibri função que retorna a data somada
* Retorno string
*/

function somaData( $data, $dias, $horas=null, $minutos=null ){

	if( !is_null( $horas ) ){
		$datafinal= date('Y/m/d H:i:s', strtotime("+$horas hours",strtotime( $data )));
	}else if( !is_null( $minutos ) ){
		$datafinal= date('Y/m/d H:i:s', strtotime("+$minutos minutes",strtotime( $data )));
	}else{
		$datafinal= date('Y/m/d H:i:s', strtotime("+$dias days",strtotime( $data )));
	}
	

	return $datafinal;

}




//-----------------------------------------------------------------------------------------------




/*
* Colibri função que retorna a data subtraida
* Retorno string
*/

function subtraiData( $data, $dias ){

	$datafinal= date('Y/m/d H:i:s', strtotime("-$dias days",strtotime( $data )));

	return $datafinal;
	
}

