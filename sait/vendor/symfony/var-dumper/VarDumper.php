<?php
/**
 * Мінімальна заглушка Symfony VarDumper для тестування
 * Для повної роботи потрібно встановити через: composer install
 */

namespace Symfony\Component\VarDumper;

if (!function_exists('dump')) {
    function dump($var, ...$moreVars)
    {
        VarDumper::dump($var);
        
        foreach ($moreVars as $v) {
            VarDumper::dump($v);
        }
        
        if (1 < func_num_args()) {
            return func_get_args();
        }
        
        return $var;
    }
}

class VarDumper
{
    public static function dump($var)
    {
        echo '<pre style="background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto;">';
        var_dump($var);
        echo '</pre>';
    }
}



