<?php

/*
* Classe de Benchmark
*/

class CO_bench {

	protected $start;
	protected $pause_time;

	/*  inicia o contador  */
	public function iniciar() {
		$this->start = $this->obter_segundos();
		$this->pause_time = 0;
	}

	/*  pausa o contador  */
	public function pausar() {
		$this->pause_time = $this->obter_segundos();
	}

	/*  reinicia o contador  */
	protected function reiniciar() {
		$this->start += ($this->obter_segundos() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*  retorna o tempo atual  */
	public function obter($decimals = 3) {
		return round(($this->obter_segundos() - $this->start),$decimals);
	}

	/*  retorna o tempo em segundos  */
	protected function obter_segundos() {
		list($usec,$sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

}

?>