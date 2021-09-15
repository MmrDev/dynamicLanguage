<?php


namespace MmrDev\DynamicLanguage\mainClasses;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Helper
{
    protected static function getLangName(string $file): array
    {
        $result = [];
        $directories = File::directories($file);
        foreach ($directories as $directory) {
            $item = explode('/', $directory);
            $result[] = $item[array_key_last($item)];
        }
        return $result;
    }

    protected static function getLangFiles(string $file): array
    {
        $result = [];
        $files = File::allFiles($file);
        foreach ($files as $item) {
            $result[] = explode('.', $item->getFilename())[0];
        }
        return $result;
    }

    protected static function removeKey(array $array, string $key): array
    {
        Arr::forget($array, $key);
        return $array;
    }

    protected static function replaceKey(array $array, string $key, string $string): array
    {
        \Arr::set($array, $key, $string);
        return $array;
    }

    protected static function getString(array $array): string
    {
        $str = '<?php' . PHP_EOL . PHP_EOL;
        $str .= 'return ' . PHP_EOL;

        $newArray = json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $data = str_replace(',]', ']', $newArray);
        $data = str_replace('}', ']', $data);
        $data = str_replace('{', '[' , $data);
        $data = str_replace('":', "' => ", $data);
        $data = str_replace(',', ',' , $data);
        $data = str_replace('"', "'", $data);

        return $str . $data . ';';
    }

    protected static function write(string $file, string $str)
    {
        File::put($file, $str);
    }

    protected static function getLangFileArray(string $file)
    {
        try {
            $content = file_get_contents($file);
            $data = str_replace('<?php', '', $content);
            $data = str_replace('return', '', $data);
            $data = str_replace(';', '', $data);
            $regex = '\/([\s\S]*?)\/[\r\n]+';
            $data = preg_replace("/$regex/", '', $data);
            $data = preg_replace("/\/\/.*/", '', $data);
            $data = preg_replace("/\r|\n/", "", $data);
            $data = str_replace(',]', ']', $data);
            $data = str_replace(']', '}', $data);
            $data = str_replace('[', '{', $data);
            $data = str_replace('=>', ':', $data);
            $data = str_replace("'", '"', $data);

            $data = json_decode($data, true);
            $result = [];
            foreach ($data as $key => $item) {
                $result[$key] = $item;
            }
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
