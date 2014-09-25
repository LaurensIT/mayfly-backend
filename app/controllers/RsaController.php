<?php

class RsaController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
    
        /*
         * 
         */
    
        function getuid()
        {
		#return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		$n = rand(3,20);
		return substr(md5(time() . " - " . rand(0,1000000)),$n,$n+10);
        }

        // Todo: Rename this function.
	public function getsession()
	{
                if (Config::Get('database.connections.' . Config::Get('database.default') . 'driver') == 'mysql')
                {
                    $pregen = Pregen::orderBy(DB::raw('RAND()'))->limit(1)->first();
                } else {
                    $pregen = Pregen::orderBy(DB::raw('RANDOM()'))->limit(1)->first();    
                }
		$inuse = new Inuse();
		$inuse->uuid = $this->getuid();
		$inuse->private = $pregen->private;
		$inuse->valid_untill = date('Y-m-d H:i:s',time() + (86400 * Input::Get('validity')));

		$inuse->save();
			
		echo json_encode(array("uuid" => $inuse->uuid, "public" => $pregen->public, "password" => $pregen->password));
		$pregen->delete();
	}
        // Todo - Serious refactor here!
	public function recrypt()
	{
		$json = unserialize(urldecode(Input::Get('data')));
                $session = Inuse::Where('uuid','=',$json['session'])->first();
		if ($session == null)
			return "";

                $res = openssl_pkey_get_private($session->private,$json['password']);
                openssl_private_decrypt(hex2bin($json['encrypted']),$dec,$res,OPENSSL_PKCS1_PADDING);
                $session->delete();
		return $dec;
	}

	function base58_encode($num) {
	    $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	    $base_count = strlen($alphabet);
	    $encoded = '';
	 
	    while ($num >= $base_count) {
	        $div = $num / $base_count;
	        $mod = ($num - ($base_count * intval($div)));
	        $encoded = $alphabet[$mod] . $encoded;
	        $num = intval($div);
	    }
	 
	    if ($num) {
	        $encoded = $alphabet[$num] . $encoded;
	    }
	 
	    return $encoded;
	}
	 
	function base58_decode($num) {
	    $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	    $len = strlen($num);
	    $decoded = 0;
	    $multi = 1;
	 
	    for ($i = $len - 1; $i >= 0; $i--) {
	        $decoded += $multi * strpos($alphabet, $num[$i]);
	        $multi = $multi * strlen($alphabet);
	    }
	 
	    return $decoded;
	}

}
