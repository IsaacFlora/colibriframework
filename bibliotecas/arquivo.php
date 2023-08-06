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
* Colibri Classe Arquivos
* Realiza a manipulação de arquivos
*/

class CO_arquivo{
	
	
	protected $arquivo;//NOME DO ARQUIVO
	protected $extensao;//EXTENSAO DO ARQUIVO
	protected $tamanho;//TAMANHO DO ARQUIVO
	
	
	
	/*
	* Define a extensao e tamanho do arquivo
	*/

	public function montaArquivo( $arquivo ){
		
		$this->arquivo= $arquivo['name'];
		$ex_nome= explode( '.', $arquivo['name'] );
		$this->extensao= strtolower(end($ex_nome));
		$this->tamanho= $arquivo['size'];
		return $this;
	
	}



	//-----------------------------------------------------------------------------------------------
	
	
	/*
	* Valida a extensão de um arquivo
	*/

	public function validaExtensao( array $extensoes ){
			
		if( !in_array($this->extensao, $extensoes) && !empty($this->extensao) ){ 
		
			return false;
			
		}else{
		
			return true;
		
		}
	
	}



	//-----------------------------------------------------------------------------------------------
	
	
	
	
	/*
	* Valida o tamanho de um arquivo
	*/
	
	public function validaTamanho( $limite ){
		
		
			if( $this->tamanho > $limite ){
			
				return false;
				
			}else{
			
				return true;
			
			}
	
	
	}
	
	
	
	

}

?>