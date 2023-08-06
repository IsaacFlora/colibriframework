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
* Colibri Função apaga pasta, subpastas e todos os seus arquivos
*/

function exdirs( $diretorio ){
 
    if ( $dd = opendir( $diretorio ) ) {
        while ( false !== ( $arquivo = readdir($dd) ) ) {

            if( $arquivo != "." && $arquivo != ".." ){
                $caminho = "$diretorio/$arquivo";
                if( is_dir($caminho) ){
                    exdirs($caminho);
                }elseif( is_file($caminho) ){
                    unlink($caminho);
                }
            }
        }

        closedir( $dd );
    }

    rmdir( $diretorio );
}



/*
* Colibri Função apaga todos os seus arquivos de uma pasta
*/

function exarquivos( $diretorio ){

    if ( is_dir( $diretorio ) ) {

        $diretorio = dir( $diretorio );

        while( $arquivo = $diretorio->read() ){

            if( ( $arquivo != '.' ) && ( $arquivo != '..' ) ){
                unlink( $pasta.$arquivo );
            }

        }

        $diretorio->close();

    }

}