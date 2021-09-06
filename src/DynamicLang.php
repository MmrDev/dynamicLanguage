<?php


namespace App\packages\mmrdev\dynamicLanguage\src;


use App\packages\mmrdev\dynamicLanguage\src\mainClasses\Helper;
use Illuminate\Support\Facades\File;

class DynamicLang extends Helper
{

    public static function getLanguages(): array
    {
        try {
            $file = base_path('resources/lang/');
            return self::getLangName($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getFiles(string $lang): array
    {
        try {
            $file = base_path('resources/lang/' . $lang);
            return self::getLangFiles($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getData(string $fileName, string $language)
    {
        try {
            $file = base_path('resources/lang/' . $language . '/' . $fileName . '.php');
            return self::getLangFileArray($file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getByKey(string $fileName, string $key, string $language = null): array
    {
        try {
            $arrayData = [];
            if (is_null($language)) {
                foreach (self::getLangName(base_path('resources/lang')) as $item) {
                    $arrayData[$item] = self::getLangFileArray(base_path('resources/lang/' . $item . '/' . $fileName . '.php'));
                }
            } else {
                $file = base_path('resources/lang/' . $language . '/' . $fileName . '.php');
                $arrayData[$language] = self::getLangFileArray($file);
            }

            $result = [];
            foreach ($arrayData as $lang => $array) {
                if (array_key_exists($key, $array)) {
                    $result[$lang] = [
                        'key' => $key,
                        'value' => $array[$key]
                    ];
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function modifyKey(string $fileName, string $language, string $key, string $type = 'add' | 'remove' | 'replace', string $string = null): bool
    {
        try {
            $file = base_path('resources/lang/' . $language . '/' . $fileName . '.php');
            $langArray = self::getLangFileArray($file);

            switch ($type) {
                case 'add':
                case 'replace':
                    $newArray = self::replaceKey($langArray, $key, $string);
                    break;
                case 'remove':
                    $newArray = self::removeKey($langArray, $key);
                    break;
                default:
                    $newArray = [];
                    break;
            }

            $newString = self::getString($newArray);
            self::write($file, $newString);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


}
