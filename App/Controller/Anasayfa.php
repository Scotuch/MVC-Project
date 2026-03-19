<?php
defined('ERISIM') or exit('401 Unauthorized');

/*************************************************
 * Anasayfa Controller Modülü
 *
 * Author   : Scotuch
 * E-Mail   : samedcimen@hotmail.com
 * Web      : https://github.com/Scotuch
 * License  : MIT
 *
 *************************************************/
class Anasayfa_Controller extends Controller
{
    public function index()
    {
        Log::Bilgi(1, "Anasayfa_Controller::index çağrıldı", GELISTIRICI);
        $this->View('anasayfa');
    }
}
