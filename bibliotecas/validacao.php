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
* Colibri Classe Validacao
* Esta classe permite a validação de dados através de métodos pré-definidos
*
*/

class CO_validacao{


		public $el_pai;//IDENTIFICACAO DO ELEMENTO PAI DOS CAMPOS VALIDADOS
		public $cor;//COR DE FORMATACAO DOS INPUTS
		protected $idsinput= array();//IDS DOS INPUTS A SEREM FORMATADOS
		protected $aviso;//STRING QUE CARREGA A MENSAGEM DE ERRO
		protected $avisoporid= array();//ARRAY QUE CARREGA AS MENSAGENS DE ERRO, CUJO OS INDICES SÃO OS IDS DOS ELEMENTOS
		protected $estiloaviso;//STRING QUE CARREGA O ESTILO PARA FORMATACAO DO ERRO

		//CARREGA UM VALOR BOOLEANO QUE INDICA O STATUS DA VALIDAÇÃO, FALSE QUANDO EXISTE UM CAMPO QUE NÃO PASSOU
		protected $statusvalidacao= true;


		//Carrega dados
		private $obrigatorio, $nome, $tipo, $valor, $valor2, $maxlength, $minlength, $idobj, $stringerro, $regex;



		/*
		* Monta a string de erro de validação e array com ids dos elementos validados
		*/

		private function montastring( $coderro, $stringerro=null ){

			//Altera o status da validação para falso, indicando que existem campos que não passaram na validação
			$this->statusvalidacao= false;
			

			//Criamos as frases de erro
			$fraseerro[0]= "inválido!";
			$fraseerro[1]= "é obrigatório!";
			$fraseerro[2]= "maior que o permitido!";
			$fraseerro[3]= "menor que o permitido!";
			$fraseerro[4]= "apenas números são permitidos!";
			$fraseerro[5]= "apenas letras são permitidas!";
			$fraseerro[6]= "caracteres especiais não são permitidos!";
			$fraseerro[7]= "os valores não conferem!";
			$fraseerro[8]= "As senhas não conferem!";
			$fraseerro[9]= "A data deve ter um formato válido!";
			$fraseerro[10]= "A data informada é inválida!";
			$fraseerro[11]= "Tefefone inválido!";
			$fraseerro[12]= "Url inválida!";
			$fraseerro['personalizada']= $stringerro;
			

			//Monta a string de aviso e armazena no parametro aviso
			if(empty($this->aviso)){

				$this->aviso = '<p class="texto_validacao"><strong>'.$this->nome.':</strong> '.$fraseerro[$coderro].'</p>';

			}else{

				$this->aviso .= '<p class="texto_validacao"><strong>'.$this->nome.':</strong> '.$fraseerro[$coderro].'</p>';

			}



			//Monta os arrays com os avisos de erro, e armazena o parametro avisoporid
			$this->avisoporid[$this->idobj] = array('<p class="texto_validacao"><strong>'.$this->nome.':</strong> '.$fraseerro[$coderro].'</p>', $this->nome.': '.$fraseerro[$coderro]);

			

			//Preenche o array com os inputs
			if ( is_array( $this->idobj ) ) {

				foreach ( $this->idobj as $obj ) {
					$this->idsinput[] = $obj;
				}

			}else{

				$this->idsinput[] = $this->idobj;

			}
		

		}




		//---------------------------------------------------------------------------------------------
		

		

		/*
		* Cria o estilo css do aviso, que será usado para formatar os campos inválidos
		*/

		private function montaestiloaviso(){

			$strininputs="";
			
			$elementopai= (!empty($this->el_pai) && $this->el_pai!='' )? $this->el_pai : 'form' ;
			$corinputs= (empty($this->corinputs))? '#D90000' : $this->corinputs ;

			foreach($this->idsinput as $inp){

				if(empty($strininputs)){

					$strininputs= $elementopai." #".$inp;

				}else{

					$strininputs.= ", ".$elementopai." #".$inp;

				}

			}


			$this->estiloaviso= '<style type="text/css"> '.$strininputs.'{ border:'.$corinputs.' solid 1px !important; }</style>';

		}



		//---------------------------------------------------------------------------------------------
		



		/*
		* Adiciona um estilo de aviso personalizado
		* Retorno array
		*/

		public function adiciona( $rotulo, $stringerro, $idobj=null ){

			//DEFINE O ID DO ELEMENTO ATUAL QUE ESTA SENDO VALIDADO
			if ( !is_null( $idobj ) ) { $this->idobj= $idobj; }

			$this->nome= $rotulo;//DEFINE O ROTULO DE EXIBIÇÃO
			$this->montastring( 'personalizada', $stringerro );
			$this->montaestiloaviso();//MONTA ESTILO AVISO

			//Retorna aviso, estilo css e array dos inputs validados
			return array( $this->aviso,$this->estiloaviso,$this->idsinput );

		}




		//-----------------------------------------------------------------------------------------------




		/*
		*
		*/

		private function validaregex(){

			if( !preg_match("/".$this->regex."/", $this->valor ) ) {

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring( 'personalizada', $this->stringerro );

			}

		}




		//-----------------------------------------------------------------------------------------------




		/*
		* Metodo que valida url
		*/

		private function validaurl(){

			if( !filter_var( $this->valor, FILTER_VALIDATE_URL ) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength ) {

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring( 12 );

			}

		}




		//-----------------------------------------------------------------------------------------------


		

		/*
		* Valida campos vazios
		*/

		private function validavazio(){

			if( (empty($this->valor) || $this->valor=='' ) && $this->obrigatorio && $this->valor!==0 && $this->valor!=='0' ){//Vazio		

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(1);

			}

		}




		//-----------------------------------------------------------------------------------------------
		



		/*
		* Valida campos maiores que o permitido
		*/

		private function validamaior(){

			if(strlen($this->valor) > $this->maxlength){//Maior que o permitido					

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(2);

			}
		

		}




		//-----------------------------------------------------------------------------------------------

		


		/*
		* Valida campos menores que o permitido
		*/

		private function validamenor(){

			if(strlen($this->valor) < $this->minlength && !empty($this->valor)){//Menor que o permitido

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(3);	

			}

		

		}


		

		//-----------------------------------------------------------------------------------------------




		/*
		* Valida e-mail
		*/

		private function validaemail(){

			if(!filter_var($this->valor, FILTER_VALIDATE_EMAIL) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength){

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(0);

			}

		}




		//-----------------------------------------------------------------------------------------------

		

		/*
		* Valida telefone
		*/

		private function validatelefone(){

			//Padrao ER para validacao
			$padrao='/^(\(\s{0,2}[0-9]{2,3}\s{0,2}\)||([0-9]{2}))(\s{0,2}-?\s{0,2})?([0-9]{4,5}-?[0-9]{4})$/';

			if(!preg_match($padrao, $this->valor) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength){

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(11);

			}

		}




		//-----------------------------------------------------------------------------------------------


		

		/*
		* Valida numeros ( apenas numeros )
		*/



		private function validanumero(){

			if( !filter_var($this->valor, FILTER_VALIDATE_INT) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength ){		

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(4);

			}

		}





		//-----------------------------------------------------------------------------------------------


		

		/*
		* Valida data
		*/

		private function validadata(){

			//Padrao ER para validacao
			$padrao='/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|[1][0-2])\/((19|20)[0-9]{2})$/';

			//Utilizamos a função nativa preg_match para realizar a comparação
			if(!preg_match($padrao, $this->valor) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength){

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(9);

			}else if(preg_match($padrao, $this->valor) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength){
				

				$data= explode('/',$this->valor);

				if(!checkdate($data[1],$data[0],$data[2])){

					//Montamos a classe e frase de erro usando o metodo montastring
					$this->montastring(10);

				}


			}

		

		}





		//-----------------------------------------------------------------------------------------------
		

		

		/*
		* Valida data e hora
		*/

		private function validadatahora(){

			//Padrao ER para validacao
			$padrao='/^(0[1-9]|[12][0-9]|3[01])((\/)(0[1-9]|[1][0-2])(\/)|(-)(0[1-9]|[1][0-2])(-))((19|20)[0-9]{2})([ ]{1})[0-2]{1}[0-39]{1}(:)[0-5]{1}[0-9]{1}(:)[0-5]{1}[0-9]{1}$/';

			//Utilizamos a função nativa preg_match para realizar a comparação 
			if(!preg_match($padrao, $this->valor) && !empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength){

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(10);

			}
		

		}
		



		//-----------------------------------------------------------------------------------------------
		

		

		/*
		* Valida comparação de senhas
		*/

		private function validasenha(){

			if(!empty($this->valor) && strlen($this->valor) <= $this->maxlength && strlen($this->valor) >= $this->minlength && !empty($this->valor2) && strlen($this->valor2) <= $this->maxlength && strlen($this->valor2) >= $this->minlength && $this->valor != $this->valor2 ){

				//Montamos a classe e frase de erro usando o metodo montastring
				$this->montastring(8);

			}

		

		}



		//-----------------------------------------------------------------------------------------------
		

		

		/*
		* Limpa a validação
		*/

		public function limpa(){

			unset( $this->idsinput );//Destroy o array
			unset( $this->avisoporid );//Destroy o array
			$this->estiloaviso="";
			$this->fraseerro="";
			$this->aviso="";
			$this->statusvalidacao= true;

		}





		//-----------------------------------------------------------------------------------------------
		



		/*
		* Valida os campos
		*/

		public function valida( array $campos=null ){

			/*
			FUNCIONAMENTO DO METODO
			O método espera receber 8 parâmetros, sendo o 1º,2º,3º e 4º parêmetros obrigatórios, os demais parâmetros restantes podem ser passadados como uma string vazia '' caso não pretenda usá-los
			

			Paremetro 1
			Aceita os valores numericos 0 e 1, quando setado como 0 valida os campos somente quando nao sao vazios ao contrário quando configurado como 1 força um campo a ser preenchido e valida o mesmo

			Parametro 2
			Nome do campo para tratamento
			Ex: Nome, E-mail, Tel: etc...

			Parametro 3
			Tipos de campos aceitos:
			string: Para texto, nomes, numeros etc...
			email: Para e-mails
			numero: Para campos onde só será permitido a insersão de numeros
			senha: Para confirmar/comparar senha etc...
			data: Para data
			datahora: Para data e hora
			url: Para urls

			Parametro 4
			Valor a ser validado

			Parametro 5
			Valor opcional para comparacao

			Paremetro 6 e 7
			Será o valor representado para tamanho máximo e mínimo de caracteres no campo.

			Paremetro 8
			Será o 'name' do input caso queira a formatacao de aviso no campo
		

			*/

				

				

			//Roda para cada campo a ser validado
			if ( count( $campos ) ) {
			
				foreach($campos as $campos){

					//Carregando os valores de parametro
					$this->obrigatorio= $campos[0];
					$this->nome= trim( $campos[1] );
					$this->tipo= trim( $campos[2] );
					$this->valor= trim( $campos[3] );
					$this->valor2= trim( $campos[4] );
					$this->maxlength= (!empty($campos[5]) && $campos[5]!='' && $campos[5]!=0)? $campos[5] : 200 ;
					$this->minlength= (!empty($campos[6]) && $campos[6]!='' && $campos[6]!=0)? $campos[6] : 2 ;
					$this->idobj= trim( $campos[7] );
					$this->stringerro= trim( str_replace(array('\r\n', '\r'), '', $campos[8] ) );
					$this->regex= trim( $campos[9] );
						

					//Valida os campos mesmo o valor estando em branco (em casos de campos obrigatórios).
					if( $this->obrigatorio==1 ){
									

						$this->validavazio();

						if( empty( $this->regex ) ){

							$this->validamaior();
							$this->validamenor();
						}
									

						//Validamos o e-mail
						if($this->tipo=='email'){ $this->validaemail(); }
						
						//Validamos o telefone
						if($this->tipo=='telefone'){ $this->validatelefone(); }

						//Validamos numeros
						if($this->tipo=='numero'){ $this->validanumero(); }

						//Validamos data no formato d/m/Y
						if($this->tipo=='data'){ $this->validadata(); }

						//Validamos data no formato d/m/Y
						if($this->tipo=='datahora'){ $this->validadatahora(); }

						//Validamos a senha
						if($this->tipo=='senha'){ $this->validasenha(); }

						//Validamos a regex
						if($this->tipo=='regex'){ $this->validaregex(); }

						//Validamos a url
						if($this->tipo=='url'){ $this->validaurl(); }

					}

						

					//Valida os campos somente se o valor nao estiver em branco
					if( $this->obrigatorio==0 && !empty($this->valor) ){

						if( empty( $this->regex ) ){
							$this->validamaior();
							$this->validamenor();
						}

									

						//Validamos o e-mail
						if($this->tipo=='email'){ $this->validaemail(); }

						//Validamos telefone
						if($this->tipo=='telefone'){ $this->validatelefone(); }

						//Validamos numeros
						if($this->tipo=='numero'){ $this->validanumero(); }

						//Validamos data no formato d/m/Y
						if($this->tipo=='data'){ $this->validadata(); }

						//Validamos data no formato d/m/Y
						if($this->tipo=='datahora'){ $this->validadatahora(); }

						//Validamos a senha
						if($this->tipo=='senha'){ $this->validasenha(); }

						//Validamos a regex
						if($this->tipo=='regex'){ $this->validaregex(); }

						//Validamos a url
						if($this->tipo=='url'){ $this->validaurl(); }

					}

				}

			}



			//MONTA ESTILO AVISO
			$this->montaestiloaviso();

			//Retorna string de validacao, estilo css aviso, array dos inputs validados
			return array( $this->statusvalidacao, $this->aviso, $this->avisoporid, $this->estiloaviso, $this->idsinput );
		

		}
	

}
?>