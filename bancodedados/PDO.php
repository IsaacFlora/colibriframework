<?php

class CO_PDO{



	public $linhas;//TOTAL DE LINHAS AFETADAS PELA CONSULTA

	public $tabela;//TABELA A SER USADA

	public $consulta_status;//STATUS DA CONSULTA REALIZADA

	public $conexao_status;//STATUS DA CONEXAO

	public $db= array();

	public $prefixo_tb;//Armazena o prefixo da tabela

	protected $select= array();//FATOR DE SELECAO DA CONSULTA

	protected $join=array();//JOIN DA CONSULTA

	protected $fetch;


	private $consultas =0;//QUANTIDADE DE REQUISICOES AO CRUD

	private $stmt= array();

	private $bd_caractere= "utf8";//Conjunto de caracteres do cliente


	//INSTANCIA O PDO E REALIZA A CONEXAO COM O DB

	public function conectar($tipobanco=null, $host=null, $banco=null, $usuariobanco=null, $senhabanco=null){

		
		try{

			if( !is_null($tipobanco) && !is_null($host) && !is_null($banco) && !is_null($usuariobanco) && !is_null($senhabanco) ){

				$this->db= new PDO( $tipobanco.':host='.$host.';dbname='.$banco, $usuariobanco, $senhabanco, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->bd_caractere ) );

			}else{

				$this->db= new PDO( TIPODEBANCO.':host='.HOST.';dbname='.BANCO, USUARIO, SENHA, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$this->bd_caractere ) );

			}

			$this->conexao_status= true;

		}catch ( PDOException $e ) {

			$this->conexao_status= false;

			if( ERR_CON ){

				echo $e->getMessage (); 

				//GRAVA LOG DE ERRO

				//$this->logs( 'CONEXAO COM O DB',$e->getMessage () ); 

			}

			

		}	

	

	}



	/*
	* Define o conjunto de caracteres do banco ex: utf8
	*/

	public function setbdCaractere( $bd_caractere ){

		$this->bd_caractere= $bd_caractere;

		return $this;

	}




	/*
	* Metodo que monta o select da consulta
	*/

	public function setselect( $select=null ){

		if( !is_null( $select ) ){
			$this->select[  $this->consultas ]=  trim( $select )  ;
		}

		return $this;

	}


	//DEFINE O TIPO DA BUSCA DO PDO COMO FETCH ( OBTEM A PROXIMA LINHA DE UM CONJUNTO DE RESULTADOS )

	public function setfetch(){

		$this->fetch= 'fetch';

		return $this;

	}

	

	//DEFINE O TIPO DA BUSCA DO PDO COMO FETCHALL ( RETORNA UM ARRAY COM TODAS AS LINHAS DE UM CONJUNTO DE RESULTADOS )

	public function setfetchAll(){

		$this->fetch= 'fetchAll';

		return $this;

	}

	


	//MONTA O JOIN

	public function setjoin( $tabela,$condicao,$tipo=null ){

		
		switch( $tipo ){//TIPOS DE JOIN

			case'left':

				$join='LEFT JOIN';//LEFT

			break;

			case'left outer':

				$join='LEFT OUTER JOIN';//LEFT

			break;

			case'right outer':

				$join='RIGHT OUTER JOIN';//LEFT

			break;

			default:

				$join='JOIN';//JOIN

			break;

		}

		

		if( empty( $this->join[  $this->consultas ] ) ){

			$this->join[  $this->consultas ]=  $join.' '.$this->prefixo_tb.$tabela.' ON '.$condicao  ;

		}else{

			$this->join[  $this->consultas ].=  ' '.$join.' '.$this->prefixo_tb.$tabela.' ON '.$condicao  ;

		}

		return $this;

	}



	//INICIA TRANSAÇÃO NO BANCO DE DADOS

	public function iniciaTransacao(){

		$this->db->beginTransaction();/* Inicia a transação */

	}





	//ENVIA A TRANSAÇÃO NO BANCO DE DADOS EXECUTANDO TODAS AS QUERYS ENVIADAS

	public function enviaTransacao(){

		$this->db->commit();/* Envia as transações perpetuando as ações */

	}




	//DESFAZ TODAS AS QUERYS EXECUTADAS NA TRANSAÇÃO

	public function desfazTransacao(){

		$this->db->rollBack();/* Desfaz todas as querys enviadas na transação */

	}




	//METODO QUE MONTA OS PARAMETROS DO WHERE

	private function montawhere( $where ){

		if( !is_null( $where ) && !empty ($where) ){

			$auxw=0;

			foreach($where as $w){

				if( count( $w )==4 ){

					$a_where[ $this->consultas ][] = $w[0]." ".$w[1]." :".str_replace(".", "", $w[0])."_w_".$auxw." ".$w[3];

				}else if ( count( $w==1 ) ) {

					$a_where[ $this->consultas ][] = $w[0];

				}

				$auxw++;

			}

			return "WHERE ".implode(" ",$a_where[ $this->consultas ]);

		}else{

			return" ";

		}

		
	}





	//METODO QUE VINCULA OS PARAMETROS DO WHERE

	private function vinculawhere( $where ){

		if( !is_null( $where ) && !empty( $where ) ){

			$auxw=0;

			foreach( $where as $w ){

				if( count( $w )==4 ){

					$this->stmt[ $this->consultas ]->bindValue(":".str_replace(".", "", $w[0])."_w_".$auxw, $w[2]);

				}

				$auxw++;

			}

		}

	}





	/*
	*************************** CRUD (CREATE,READ,UPDADE,DELETE) ****************************
	*/



	//METODO DE GRAVACAO DE DADOS
	public function gravar( Array $dados ){

		$auxdados=0;

		foreach($dados as $indices => $conteudo){

			$campos[]= $indices;

			$valores[]= ":".$indices."_d_".$auxdados;

			$auxdados++;

		}


		$valores=  implode(",",$valores);

		$campos= implode(", ",$campos);


		//MONTA A CONSULTA
		$query= " INSERT INTO `{$this->prefixo_tb}{$this->tabela}` ({$campos}) VALUES ({$valores}) ";

		//PREPARA A CONSULTA
		$stmt = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS
		$auxdados=0;
		foreach($dados as $indices => $conteudo){

			$conteudo= ( $conteudo==='null' || $conteudo==='NULL' )? null : $conteudo ;//Insere null

			$stmt->bindValue(":{$indices}_d_".$auxdados, $conteudo);

			$auxdados++;

		}

		//TENTA REALIZAR A ACAO
		try{
			
			//EXECUTA A CONSULTA
			if( !$stmt->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao gravar dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r($stmt->errorInfo()); }

			//GRAVA LOG DE ERRO
			$erros= $stmt->errorInfo();

			//$this->logs( 'CRUD GRAVAR',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

	}


	//METODO DE GRAVACAO DE DADOS EM SÉRIE
	public function gravar_grupo( Array $grupos ){


		//Get a list of column names to use in the SQL statement.
    	$campos = array_keys( $grupos[0] );
    	$campos = implode(", ",$campos);

		//Roda um loop para cada grupo
		$auxdados=0;
		foreach ( $grupos as $grupo ) {

			unset( $valores );//Destroy os valores
			
			//Roda um loop para montar os dados do grupo
			foreach( $grupo as $indices => $conteudo ){

				$valores[]= ":".$indices."_d_".$auxdados;

				$auxdados++;

			}

			$valoresf[]=  "( ".implode(",",$valores)." )";

		}

		$valoresf= implode(", ",$valoresf);

		//MONTA A CONSULTA
		$query= " INSERT INTO `{$this->prefixo_tb}{$this->tabela}` ({$campos}) VALUES $valoresf ";


		//PREPARA A CONSULTA
		$stmt = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS
		$auxdados=0;
		if( count( $grupos ) ){
			foreach ( $grupos as $grupo ) {
				
				foreach( $grupo as $indices => $conteudo){

					$conteudo= ( $conteudo==='null' || $conteudo==='NULL' )? null : $conteudo ;//Insere null

					$stmt->bindValue( ":{$indices}_d_".$auxdados, $conteudo );

					$auxdados++;

				}

			}
		}


		//TENTA REALIZAR A ACAO
		try{
			
			//EXECUTA A CONSULTA
			if( !$stmt->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao gravar dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r($stmt->errorInfo()); }

			//GRAVA LOG DE ERRO
			$erros= $stmt->errorInfo();

			//$this->logs( 'CRUD GRAVAR',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

	}



	//METODO DE LEITURA DE DADOS COM PREPARED STATEMENTS

	public function ler( $where=null, $limit=null, $offset=null, $orderby=null ){

		//MONTAGEM DO WHERE
		$f_where= $this->montawhere( $where );

		$orderby= (!is_null($orderby) && !empty($orderby) ? "ORDER BY {$orderby}": "");

		$limit= (!is_null($limit) && !empty($limit) ? "LIMIT {$limit}": "");

		$offset= (!is_null($offset) && !empty($offset) ? "OFFSET {$offset}": "");

		$select= ( empty( $this->select[  $this->consultas ] ) )? "*" : $this->select[  $this->consultas ] ;

		//MONTA A CONSULTA
		$query= " SELECT $select FROM `{$this->prefixo_tb}{$this->tabela}` ".$this->join[ $this->consultas ]." {$f_where} {$orderby} {$limit} {$offset} ";
		//echo $query.'<br>';
		//PREPARA A CONSULTA
		$this->stmt[ $this->consultas ] = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS
		$this->vinculawhere( $where );

		//TENTA REALIZAR A ACAO
		try{

			//EXECUTA A CONSULTA
			if( !$this->stmt[ $this->consultas ]->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao ler dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r( $this->stmt[ $this->consultas ]->errorInfo() ); }

			//GRAVA LOG DE ERRO

			$erros= $this->stmt[ $this->consultas ]->errorInfo();

			//$this->logs( 'CRUD LER',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}


		//BUSCAS PDO
		switch($this->fetch){

			case 'fetch':
				$resultados= $this->stmt[ $this->consultas ]->fetch(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

			case 'fetchAll':
				$resultados= $this->stmt[ $this->consultas ]->fetchAll(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

			default:
				$resultados= $this->stmt[ $this->consultas ]->fetchAll(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

		}

		$this->linhas= $this->stmt[ $this->consultas ]->rowCount($resultados);

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

		return $resultados;

	}


	//METODO DE LEITURA

	public function consulta( $consulta, array $parametros=null ){

		//PREPARA A CONSULTA
		$this->stmt[ $this->consultas ] = $this->db->prepare( $consulta );

		if( count($parametros) ){
			$aux= 1;
			foreach ( $parametros as $valores ) {

				$this->stmt[ $this->consultas ]->bindValue($aux, $valores);
				$aux++;
				
			}
		}
		

		//TENTA REALIZAR A ACAO
		try{

			//EXECUTA A CONSULTA
			if( !$this->stmt[ $this->consultas ]->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao ler dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r( $this->stmt[ $this->consultas ]->errorInfo() ); }

			//GRAVA LOG DE ERRO

			$erros= $this->stmt[ $this->consultas ]->errorInfo();

			//$this->logs( 'CRUD LER',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}


		//BUSCAS PDO
		switch($this->fetch){

			case 'fetch':
				$resultados= $this->stmt[ $this->consultas ]->fetch(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

			case 'fetchAll':
				$resultados= $this->stmt[ $this->consultas ]->fetchAll(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

			default:
				$resultados= $this->stmt[ $this->consultas ]->fetchAll(PDO::FETCH_ASSOC);//EXTRAI RESULTADOS FORMA ASSOCIATIVA
			break;

		}

		$this->linhas= $this->stmt[ $this->consultas ]->rowCount($resultados);

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

		return $resultados;

	}




	//METODO DE ATUALIZACAO DE DADOS

	public function atualizar( Array $dados, $where=null ){

		//MONTAGEM DO WHERE
		$f_where= $this->montawhere( $where );

		//MONTAGEM DOS CAMPOS
		$auxc=0;
		foreach($dados as $indices => $conteudo){

			//CRIA O INDICES DE CONTEUDO PARA A CONSULTA
			$a_campos[]= $indices." = :".$indices."_d_".$auxc;

			$auxc++;

		}

		$f_campos= implode(", ",$a_campos);

		//MONTA A CONSULTA
		$query= " UPDATE `{$this->prefixo_tb}{$this->tabela}` SET {$f_campos} {$f_where} ";

		

		//PREPARA A CONSULTA

		$this->stmt[ $this->consultas ] = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS DO WHERE
		$this->vinculawhere( $where );

		//VINCULA OS VALORES AOS PARÂMETROS DOS CAMPOS
		$auxc=0;
		foreach($dados as $indices => $conteudo){

			$conteudo= ( $conteudo==='null' || $conteudo==='NULL' )? null : $conteudo ;//Insere null

			$this->stmt[ $this->consultas ]->bindValue(":".$indices."_d_".$auxc."", $conteudo, PDO::PARAM_STR);//VINCULA VALOR

			$auxc++;

		}

		//TENTA REALIZAR A ACAO
		try{

			//EXECUTA A CONSULTA
			if( !$this->stmt[ $this->consultas ]->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao atualizar dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r( $this->stmt[ $this->consultas ]->errorInfo() ); }

			//GRAVA LOG DE ERRO
			$erros= $this->stmt[ $this->consultas ]->errorInfo();

			//$this->logs( 'CRUD ATUALIZAR',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD


	}



	//METODO DE ATUALIZACAO DE DADOS EM GRUPO

	public function atualizar_grupo( array $dados, array $where ){


		//MONTAGEM DOS CAMPOS
		$auxc=0;
		foreach($dados as $indices => $conteudo){

			//CRIA O INDICES DE CONTEUDO PARA A CONSULTA
			$a_campos[]= $indices." = :".$indices."_d_".$auxc;

			$auxc++;

		}


		$query = "UPDATE `{$this->prefixo_tb}{$this->tabela}` SET ";

		$indice_where= array_keys( $where );

		//RODA UM LOOP PARA CADA DADO/COLULA
		foreach ( $dados as $indice => $valor ) {

			unset( $coluna );

			 $coluna .= $indice." = CASE ".$indice_where[0];


			 	//EXTRAIMOS OS VALORS DE CADA LINHA REFERENTE A COLUNA
			 	$aux= 0;
			 	foreach ( $where[ $indice_where[0] ] as $chave ) {


			 		$coluna .= " WHEN ".$chave." THEN '".$a_campos[ $aux ]."' ";

			 		$aux++;
			 	}

			 $coluna.= " END";

			 $colunas[]= $coluna;
		}

		$query .= implode(', ', $colunas);

		$query .= " WHERE ".$indice_where[0]." IN ( ".implode(',', $where[ $indice_where[0] ] )." )";
		 

		//MONTA A CONSULTA
		//$query= " UPDATE `{$this->prefixo_tb}{$this->tabela}` SET {$f_campos} {$f_where} ";

		echo $query; exit();

		//PREPARA A CONSULTA

		$this->stmt[ $this->consultas ] = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS DO WHERE
		$this->vinculawhere( $where );

		//VINCULA OS VALORES AOS PARÂMETROS DOS CAMPOS
		$auxc=0;
		foreach($dados as $indices => $conteudo){

			$conteudo= ( $conteudo==='null' || $conteudo==='NULL' )? null : $conteudo ;//Insere null

			$this->stmt[ $this->consultas ]->bindValue(":".$indices."_d_".$auxc."", $conteudo, PDO::PARAM_STR);//VINCULA VALOR

			$auxc++;

		}

		//TENTA REALIZAR A ACAO
		try{

			//EXECUTA A CONSULTA
			if( !$this->stmt[ $this->consultas ]->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao atualizar dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r( $this->stmt[ $this->consultas ]->errorInfo() ); }

			//GRAVA LOG DE ERRO
			$erros= $this->stmt[ $this->consultas ]->errorInfo();

			//$this->logs( 'CRUD ATUALIZAR',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}

		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

	}




	//METODO DE EXCLUSAO DE DADOS
	public function excluir( $where=null ){

		//MONTAGEM DO WHERE
		$f_where= $this->montawhere( $where );

		//MONTA A CONSULTA
		$query= " DELETE FROM `{$this->prefixo_tb}{$this->tabela}` {$f_where} ";

		//PREPARA A CONSULTA
		$this->stmt[ $this->consultas ] = $this->db->prepare($query);

		//VINCULA OS VALORES AOS PARÂMETROS DO WHERE
		$this->vinculawhere( $where );

		//TENTA REALIZAR A ACAO
		try{

			//EXECUTA A CONSULTA
			if( !$this->stmt[ $this->consultas ]->execute() ){

				$this->consulta_status= false;


				if( ERR_DB ){
					throw new Exception("<span style='color:red;'>Falha ao excluir dados.</span><br>");
				}

			}else{

				$this->consulta_status= true;

			}

		}catch (Exception $e) {

			echo $e->getMessage();

			if(ERR_DB){ print_r( $this->stmt[ $this->consultas ]->errorInfo() ); }

			//GRAVA LOG DE ERRO
			$erros= $this->stmt[ $this->consultas ]->errorInfo();

			//$this->logs( 'CRUD EXCLUIR',$erros[0].PHP_EOL.$erros[1].PHP_EOL.$erros[2] );

		}


		$this->consultas++;//INCREMENTA A QUANTIDADE DE ACESSOS AO CRUD

	}

	

	/*
	*************************** CRUD (CREATE,READ,UPDADE,DELETE) ****************************
	*/


}



?>