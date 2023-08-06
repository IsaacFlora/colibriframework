<?php

	
//MONTA OS CAMPOS DO MENU SE BASEANDO NO XML
function Menu( $caminho, $idmenu, $classemenu=null ){
	
	
	$diretorio = $caminho;// pega o endereço do diretório
	
	$ponteiro  = opendir($diretorio);// monta os vetores com os itens encontrados na pasta
		
		
	//LE OS ARQUIVOS
	$i=0;
	while ($nome_itens = readdir($ponteiro)) {//Armazenamos os valores em arrays
		
		
	
		if ($nome_itens != "." && $nome_itens != "..") {
			
			
			//INTERPRETA O ARQUIVO XML E TRANSFORMA EM UM OBJETO 
			$xml = simplexml_load_file($caminho.$nome_itens);
			
			//GRAVAMOS O BASICO SOBRE O ARQUIVO COMO POSICAO DO MENU E NOME DO MESMO
			$arquivos[$i]= array('posicao'=>strval($xml->modulo->posicao), 'nome'=>$nome_itens);
			
		$i++;	
		}
	}
		
		
		
	//VERIFICA SE AO MENOS 1 ARQUIVO FOI ENCONTRADO
	if(count($arquivos)){

		sort($arquivos);//SORTEIA O ARRAY EM ORDEM CRESCENTE, ORGANIZANDO ASSIM O MENU


		//LOOP COM BASE NA QUANTIDADE DE ARQUIVOS ENCONTRADOS
		$i=0;
		foreach($arquivos as $arq) {

			//INTERPRETA O ARQUIVO XML E TRANSFORMA EM UM OBJETO 
			$xml = simplexml_load_file($caminho.$arq['nome']);

			//VERIFICA SE E UM MODULO VALIDO DE MENU E SE ESTA ATIVO
			if( $xml->modulo->tipo=='menu' && $xml->modulo->ativo=='true'){

				//GRAVA EM ARRAY OS DADOS DO MENU PRINCIPAL
				$nomes[$i]= strval($xml->modulo->menus->nome);
				$links[$i]= strval($xml->modulo->menus->link);
				$estilos[$i]= strval($xml->modulo->menus->estilo);
				$titulos[$i]= strval($xml->modulo->menus->titulo);
				$classes[$i]= strval($xml->modulo->menus->classes);//CLASSES
				$id[$i]= strval($xml->modulo->menus->id);//ID



				//MENU SECUNDARIO N1
				$b=0;
				foreach($xml->modulo->menus->submenus1 as $subs){


					$nomesub1[$i][$b]= strval($subs->nome);
					$linkssub1[$i][$b]= strval($subs->link);
					$titulossub1[$i][$b]= strval($subs->titulo);

					//MENU SECUNDARIO N2
					$c=0;
					foreach($subs->submenus2 as $subs2){


						$nomesub2[$i][$b][$c]= strval($subs2->nome);
						$linkssub2[$i][$b][$c]= strval($subs2->link);
						$titulossub2[$i][$b][$c]= strval($subs2->titulo);


						//MENU SECUNDARIO N3
						$d=0;
						foreach($subs2->submenus3 as $subs3){


							$nomesub3[$i][$b][$c][$d]= strval($subs3->nome);
							$linkssub3[$i][$b][$c][$d]= strval($subs3->link);
							$titulossub3[$i][$b][$c][$d]= strval($subs3->titulo);

							$d++;
						}


						$c++;
					}



					$b++;
				}


			}

			$i++;
		}


		//ARRAY COM OS DADPS DO MENU
		$campos= array( 
		'nomes' => $nomes, 
		'links' => $links, 
		'estilos' => $estilos, 
		'titulos' => $titulos, 
		'classes' => $classes,
		'id' => $id,
		'nomessub1' => $nomesub1, 'linkssub1' =>$linkssub1, 'titulossub1'=> $titulossub1, 
		'nomessub2' =>  $nomesub2, 'linkssub2' => $linkssub2, 'titulossub2' => $titulossub2, 
		'nomessub3' => $nomesub3, 'linkssub3' =>$linkssub3, 'titulossub3'=> $titulossub3 
		);


		/*
		*CRIA O MENU EM HTML
		*/
		$menumontado= '<ul id="'.$idmenu.'"  class="'.$classemenu.'"  >';



		if( count($campos['nomes']) >0){

			$aux=0;
			foreach($campos['nomes'] as $nome){

				$menumontado.='<li '; 

				//ESTILOS CSS
				if( !empty($campos['estilos'][$aux]) ){ 
					$menumontado.= ' style="'.$campos['estilos'][$aux].'" '; 
				}

				//CLASSES
				if( !empty($campos['classes'][$aux]) ){ 
					$menumontado.= ' class="'.$campos['classes'][$aux].'" '; 
				}

				//ID
				if( !empty($campos['id'][$aux]) ){ 
					$menumontado.= ' id="'.$campos['id'][$aux].'" '; 
				}

				$menumontado.='><span';

				if( count($campos['nomessub1'][$aux]) > 0 ){ 
					$menumontado.= ' class="sub_icone"'; 
				}

				$menumontado.='>';


				//LINK
				if( !empty( $campos['links'][$aux] ) && $campos['links'][$aux]!='nolink' ){

					$menumontado.='<a';


					if( !empty($campos['titulos'][$aux]) ){ 
						$menumontado.= ' title="'.$campos['titulos'][$aux].'"';
					}else{
						$menumontado.= ' title="'.$nome.'"';
					}

					$menumontado.= ' href="'.CAMINHO.'/'.$campos['links'][$aux].'" >';

				}

				$menumontado.=$nome;

				//LINK
				if( !empty( $campos['links'][$aux] ) && $campos['links'][$aux]!='nolink' ){

					$menumontado.='</a>';

				}

				$menumontado.='</span>';



				///////////////SUBMENUS NIVEL 1//////////////
				if( count($campos['nomessub1'][$aux]) > 0 ){

					$menumontado.='<ul class="sub1">';

					$sub1=0;
					foreach( $campos['nomessub1'][$aux] as $nome ){

						$menumontado.='<li><span';

						if( count($campos['nomessub2'][$aux][$sub1]) > 0 ){ 
							$menumontado.=' class="sub_icone"'; 
						}

						$menumontado.='><a'; 


						if( !empty($campos['titulossub1'][$aux][$sub1]) ){ 
							$menumontado.=' title="'.$campos['titulossub1'][$aux][$sub1].'"';
						}else{
							$menumontado.= ' title="'.$nome.'"';
						}

						$menumontado.=' href="'.CAMINHO.'/'.$campos['linkssub1'][$aux][$sub1].'" >'.$nome.'</a></span>';

						///////////////SUBMENUS NIVEL 2//////////////
						if( count($campos['nomessub2'][$aux][$sub1]) > 0 ){

							$menumontado.='<ul class="sub2">';

							$sub2=0;
							foreach( $campos['nomessub2'][$aux][$sub1] as $nomesub2 ){

								$menumontado.='<li><span';

								if( count($campos['nomessub3'][$aux][$sub1][$sub2]) > 0 ){ 
									$menumontado.=' class="sub_icone"'; 
								} 

								$menumontado.='><a';


								$menumontado.=' title="'.$nomesub2.'" href="'.CAMINHO.'/'.$campos['linkssub2'][$aux][$sub1][$sub2].'" >'.$nomesub2.'</a></span>';


								///////////////SUBMENUS NIVEL 3//////////////
								if( count($campos['nomessub3'][$aux][$sub1][$sub2]) > 0 ){

									$menumontado.='<ul class="sub3">';


									$sub3=0;
									foreach( $campos['nomessub3'][$aux][$sub1][$sub2] as $nomesub3 ){

										$menumontado.='<li><span><a  title="'.$nomesub3.'" href="'.CAMINHO.'/'.$campos['linkssub3'][$aux][$sub1][$sub2][$sub3].'" >'.$nomesub3.'</a></span></li>';

										$sub3++;

									} 


									$menumontado.='</ul>';


								}


								$menumontado.='</li>';

								$sub2++;

							} 

							$menumontado.='</ul>';

						}


						$menumontado.='</li>';

						$sub1++;
					} 

					$menumontado.='</ul>';

				}


				$menumontado.='</li>';


				$aux++;
			} 


		}else{

			$menumontado.='<li><a title="Não existem módulos" href="#"><span>Módulos vazios</span></a></li>';

		}


		$menumontado.='</ul>';


	}


	return $menumontado;

}
		
		

?>