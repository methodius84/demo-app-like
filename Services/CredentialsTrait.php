<?php

namespace App\Services;

use App\Models\Person;
use App\Models\YandexAccount;
use App\Services\Mailcow\MailboxService;
use Exception;
use Illuminate\Support\Facades\Log;

trait CredentialsTrait
{
    private static function cityPrefix($city): string
    {
        return match ($city) {
            'Екатеринбург' => '.ekb',
            'Ижевск' => '.izv',
            'Тюмень' => '.tmn',
            'Чебоксары' => '.chbk',
            'Уфа' => '.ufa',
            'Ростов-на-Дону' => '.rvnd',
            'Иваново' => '.ivn',
            'Архангельск' => '.arh',
            'Калининград' => '.kln',
            'Самара' => '.smr',
            'Пятигорск' => '.ptg',
            'Новороссийск' => '.nvr',
            'Омск' => '.omsk',
            'Краснодар' => '.krs',
            'Белгород' => '.blg',
            'Ярославль' => '.yrsl',
            'Барнаул' => '.brn',
            default => '',
        };
    }

    private function setNickname(Person $person) : string | null
    {
        $nickname = '';
        $name = mb_str_split($person->first_name, 1);
        for($i=0; $i <= array_key_last($name); $i++){
            try{
                $nameTransliteration = '';
                for($j = 0; $j<=$i; $j++){
                    $nameTransliteration .= $name[$j];
                }
                $nickname = $this->transliteration($nameTransliteration.'.'.$person->last_name);
                if(null!==YandexAccount::where('email', "$nickname@likebz.ru")->first()){
                    throw new Exception('Логин занят');
                }

                break;
            }
            catch(Exception $e){
                if($i == array_key_last($name)){
                    Log::channel('telegram')->error("Can't create email for the person $person->last_name: ");
                    return null;
                }

                continue;
            }
        }
        return $nickname;
    }

    private static function transliteration($value) : string
    {
        {
            $converter = array(
                'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
                'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
                'й' => 'i',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
                'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
                'у' => 'u',    'ф' => 'f',    'х' => 'kh',    'ц' => 'ts',    'ч' => 'ch',
                'ш' => 'sh',   'щ' => 'shch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
                'э' => 'e',    'ю' => 'iu',   'я' => 'ia',

                'А' => 'a',    'Б' => 'b',    'В' => 'v',    'Г' => 'g',    'Д' => 'd',
                'Е' => 'e',    'Ё' => 'e',    'Ж' => 'zh',   'З' => 'z',    'И' => 'i',
                'Й' => 'i',    'К' => 'k',    'Л' => 'l',    'М' => 'm',    'Н' => 'n',
                'О' => 'o',    'П' => 'p',    'Р' => 'r',    'С' => 's',    'Т' => 't',
                'У' => 'u',    'Ф' => 'f',    'Х' => 'kh',    'Ц' => 'ts',    'Ч' => 'ch',
                'Ш' => 'sh',   'Щ' => 'shch',  'Ь' => '',     'Ы' => 'y',    'Ъ' => '',
                'Э' => 'e',    'Ю' => 'iu',   'Я' => 'ia',
            );

            return strtr($value, $converter);
        }
    }

    public static function genPassword($length = 10)
    {
        $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
        $size = strlen($chars) - 1;
        $password = '';
        while($length--) {
            $password .= $chars[random_int(0, $size)];
        }
        return $password;
    }
}
