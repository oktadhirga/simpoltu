<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Konversi {
    function eja($n) {

        $dasar = array(1=>'SATU','DUA','TIGA','EMPAT','LIMA','ENAM','TUJUH','DELAPAN','SEMBILAN');
        $angka = array(1000000000,1000000,1000,100,10,1);
        $satuan = array('MILYAR','JUTA','RIBU','RATUS','PULUH','');
        $str ="";
        if ($n == 0) {
          $str = "nol";
        }
        $i=0;
        while($n!=0){
            $count = (int)($n/$angka[$i]);
            if($count>=10) {
              $str .= $this->eja($count). " ".$satuan[$i]." ";
            } else if($count > 0 && $count < 10) {
              $str .= $dasar[$count] . " ".$satuan[$i]." ";
            }
            $n -= $angka[$i] * $count;
            $i++;
        }
        $str = preg_replace("/SATU PULUH (\w+)/i","\\1 BELAS",$str);
        $str = preg_replace("/SATU (RIBU|RATUS|PULUH|BELAS)/i","SE\\1",$str);
        return $str;
    }
}

/*Author:Okz */
/* End of file Konversi.php */
/* Location: ./application/libraries/Konversi.php */
