<?php

/*
* Colibri
*
* Framework de desenvolvimento PHP 5 ou superior
* Autores Time de desenvolvimento Organy
* VERSÃO 1.1
*
*/



/*
* Colibri Classe de Imagem
* Realiza a manipulação de imagens
*/

class CO_imagem{

	protected $caminho;//CAMINHO DA IMAGEM
	protected $imagem;//NOME IMAGEM
	protected $imagemcriada;//IMAGEM CRIADA
	protected $extensao;//EXTENSAO DA IMAGEM
	protected $prefixothumb= 'thumb_';//PREFIXO DA MINIATURA DE IMAGEM

	

	/*
	* Define o caminho da imagem
	*/

	public function montaCaminho( $caminho ){

		$this->caminho= $caminho;

		return $this;

	}




	//-----------------------------------------------------------------------------------------------


	

	/*
	* Extrai e armazena a extensao de imagens
	* invoca o metodo de criação de imagens
	*/

	public function montaImagem( $imagem, $cria=true ){

		$this->imagem= $imagem;//NOME DA IMAGEM

		$expimg= explode( '.', $imagem );

		$this->extensao= strtolower(end( $expimg ));//EXTENSAO DA IMAGEM

		if( $cria ){
			$this->criaImagem();//CRIA IMAGEM
		}
		

		return $this;

	}





	//-----------------------------------------------------------------------------------------------

	

	

	/*
	* Define um prefixo opcional para thumbnails de imagens o padrão é 'thumb_'
	*/

	public function defineprefixoThumb( $prefixo ){

		$this->prefixothumb= $prefixo;

		return $this;

	}




	//-----------------------------------------------------------------------------------------------
	

	

	/*
	* Cria uma nova imagem
	*/

	protected function criaImagem( $nomedaimagem=null ){

		$nomedaimagem= ( is_null($nomedaimagem) )? $this->imagem : $nomedaimagem ;

		//DE ACORDO COM CADA EXTENSÃO EXECUTA UM BLOCO DE CÓDIGOS DIFERENTE	
		if ( $this->extensao == 'jpg' || $this->extensao == 'jpeg' ) {

			$this->imagemcriada= imagecreatefromjpeg( $this->caminho.$nomedaimagem  );

		} else if ( $this->extensao == 'png' ) {

			$this->imagemcriada= imagecreatefrompng( $this->caminho.$nomedaimagem  );

		// SE A VERSÃO DO GD INCLUIR SUPORTE A GIF MOSTRA...

		} else if ( $this->extensao == 'gif' ) {

			$this->imagemcriada= imagecreatefromgif( $this->caminho.$nomedaimagem );

		}


	}




	//-----------------------------------------------------------------------------------------------


	

	/*
	* Envia a imagem para o borwser ou arquivo
	*/

	protected function enviaImagem( $imagem, $nomedaimagem=null ){

		$nomedaimagem= ( is_null($nomedaimagem) )? $this->imagem : $nomedaimagem ;

		if ( $this->extensao == 'jpg' || $this->extensao == 'jpeg' ) {

			return ( imagejpeg($imagem, $this->caminho.$nomedaimagem, 100 ) )? true : false ;

		} else if ( $this->extensao == 'png' ) {

			return ( imagepng($imagem, $this->caminho.$nomedaimagem ) )? true : false ;

		// SE A VERSÃO DO GD INCLUIR SUPORTE A GIF MOSTRA...

		} else if ( $this->extensao == 'gif' ) {

			return ( imagegif($imagem, $this->caminho.$nomedaimagem, 100 ) )? true : false ;

		}

	

	}





	//-----------------------------------------------------------------------------------------------

	

	

	/*
	* Define valores em proporcao para largura e altura de uma imagem
	* Retorno array
	*/

	public function proporcaoImagem( $comprimento,$altura ){


		$largura_original= imagesx($this->imagemcriada); //COMPRIMENTO ORIGINAL DA IMAGEM
		$altura_original= imagesy($this->imagemcriada); //ALTURA ORIGINAL DA IMAGEM

		$escala= min($comprimento/$largura_original, $altura/$altura_original);//ESCALA DE PROPORCAO


		// SE A IMAGEM É MAIOR QUE O PERMITIDO, ENCOLHE ELA!
		$largura= ( $escala < 1 )? floor($escala * $largura_original) : $largura_original ;
		$altura= ( $escala < 1 )? floor($escala * $altura_original) : $altura_original ;

		return array( $largura,$altura );//RETORNA UM ARRAY COM LARGURA E ALTURA


	}





	//-----------------------------------------------------------------------------------------------	

	

	

	/* 
	* Redimensiona uma imagem para um novo tamanhos
	*/

	public function redimensionaImagem( $largura, $altura, $proporcao=null ){


		//SE A PROPORCAO ESTIVER ATIVA
		if( strtolower($proporcao)=='p'  ){

			$proporcoes= $this->proporcaoImagem( $largura,$altura );

			$largura = $proporcoes[0];
			$altura = $proporcoes[1];

		}else{

			$largura = $largura;
			$altura = $altura;

		}

		

		$largura_or= imagesx($this->imagemcriada); //COMPRIMENTO ORIGINAL DA IMAGEM
		$altura_or= imagesy($this->imagemcriada); //ALTURA ORIGINAL DA IMAGEM

		$nova_imagem = imagecreatetruecolor( $largura, $altura );//CRIA IMAGEM TRUE COLOR COM AS NOVAS PROPORCOES

		//SUPORTE A PNG TRANSPARENTE

		imagealphablending($nova_imagem, false);
		imagesavealpha($nova_imagem, true);

		//COPIA A IMAGEM PARA A NOVA IMAGEM

		imagecopyresampled( $nova_imagem, $this->imagemcriada, 0, 0, 0, 0, $largura, $altura, $largura_or, $altura_or );

		imagedestroy( $this->imagemcriada );//DESTROI A IMAGEM E LIBERA A MEMORIA
		

		//ENVIA A IMAGEM PARA PASTA OU BROWSER
		return $this->enviaImagem( $nova_imagem );
		

	}




	//-----------------------------------------------------------------------------------------------
	

	

	/*
	* Cria thumbnails de imagens
	*/

	public function thumbImagem( $largura, $altura, $proporcao=null ){

		//SE A PROPORCAO ESTIVER ATIVA
		if( strtolower($proporcao)=='p'  ){

			$proporcoes= $this->proporcaoImagem( $largura,$altura );

			$largura = $proporcoes[0];
			$altura = $proporcoes[1];

		}else{	

			$largura = $largura;
			$altura = $altura;

		}
		

		$largura_or= imagesx($this->imagemcriada); //COMPRIMENTO ORIGINAL DA IMAGEM
		$altura_or= imagesy($this->imagemcriada); //ALTURA ORIGINAL DA IMAGEM


		$thumb = imagecreatetruecolor( $largura, $altura );//CRIA IMAGEM THUMB

		//SUPORTE A PNG TRANSPARENTE
		imagealphablending($thumb, false);
		imagesavealpha($thumb, true);


		$nomethumb= explode('.',$this->imagem);
		$nomethumb= $this->prefixothumb.$nomethumb[0].'.'.$this->extensao;

		//COPIA A IMAGEM PARA A NOVA IMAGEM
		imagecopyresampled( $thumb, $this->imagemcriada, 0, 0, 0, 0, $largura, $altura, $largura_or, $altura_or );

		$this->enviaImagem( $thumb, $nomethumb );


	}





	//-----------------------------------------------------------------------------------------------
	

	

	/*
	* Insere uma imagem no centro de outra criando uma moldura
	*/

	public function emoldurarImagem( $largura, $altura, array $corfundo= array( 255,255,255 ) ){
		

		$largura_or= imagesx($this->imagemcriada); //COMPRIMENTO ORIGINAL DA IMAGEM
		$altura_or= imagesy($this->imagemcriada); //ALTURA ORIGINAL DA IMAGEM

		$imagem = imagecreatetruecolor( $largura, $altura );//Cria imagem

		//Aloca a cor para a imagem
		$backgroundColor = imagecolorallocate( $imagem, $corfundo[0], $corfundo[1], $corfundo[2] );

		imagefill( $imagem, 0, 0, $backgroundColor );//Realiza o preenchimento da imagem

		//SUPORTE A PNG TRANSPARENTE
		imagealphablending( $imagem, false );
		imagesavealpha( $imagem, true );

		//Centraliza a imagem no fundo
		$centro_largura= round( $largura/2-$largura_or/2 );
		$centro_altura= round( $altura/2-$altura_or/2 );

		//COPIA A IMAGEM PARA A NOVA IMAGEM
		imagecopyresampled( $imagem, $this->imagemcriada, $centro_largura , $centro_altura, 0, 0, $largura_or, $altura_or, $largura_or, $altura_or );

		$this->enviaImagem( $imagem, $this->imagem );


	}




	//-----------------------------------------------------------------------------------------------




	//-----------------------------------------------------------------------------------------------




	/*
	* Insere uma imagem no centro de outra criando uma moldura
	*/

	public function b64toImage( $img ){

		$img = str_replace( 'data:image/png;base64,', '', $img );//Remove strings do hash
		$img = str_replace( 'data:image/jpeg;base64,', '', $img );//Remove strings do hash
		$img = str_replace( 'data:image/jpg;base64,', '', $img );//Remove strings do hash
		$img = str_replace( 'data:image/gif;base64,', '', $img );//Remove strings do hash
		$img = str_replace( ' ', '+', $img );//Substitui espaço por +
		$data = base64_decode( $img );//Decodifica a string
		$data = imagecreatefromstring($data);//Cria um identificador da imagem a partir da string

		return $this->enviaImagem( $data );//Invoca o metodo que envia a umagem para o arquivo

	}




	//---------------------------------------------------------------------------------------------

	


	/*
	* Converte um arquivo de imagem para base 64
	*/

	public function imagemparab64( $imagem_nome ){

		$this->imagem= $imagem_nome;//NOME DA IMAGEM

		$expimg= explode( '.', $imagem_nome );

		$this->extensao= strtolower(end( $expimg ));//EXTENSAO DA IMAGEM

        $imgbinary = fread(fopen($this->caminho, "r"), filesize($this->caminho));

        return 'data:image/' . $this->extensao . ';base64,' . base64_encode($imgbinary);
    

	}
	



}
?>