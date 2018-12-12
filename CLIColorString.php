<?php

/**
 * Desc:
 * User: baagee
 * Date: 2018/8/27
 * Time: 下午2:28
 */

/*------------------------------------------
字体色     |     字背景色     |   颜色描述
------------------------------------------
30        |        40       |    黑色
31        |        41       |    红色
32        |        42       |    绿色
33        |        43       |    黃色
34        |        44       |    蓝色
35        |        45       |    紫红色
36        |        46       |    青蓝色
37        |        47       |    白色
-------------------------------------------
-------------------------------
显示方式     |      效果
-------------------------------
0           |     终端默认设置
1           |     高亮显示
4           |     使用下划线
7           |     反白显示
-------------------------------
-------------------------------*/

class CLIColorString
{
    private $map               = [
        '30' => '字体黑色',
        '31' => '字体红色',
        '32' => '字体绿色',
        '33' => '字体黄色',
        '34' => '字体蓝色',
        '35' => '字体紫红色',
        '36' => '字体青蓝色',
        '37' => '字体白色',
        '40' => '背景黑色',
        '41' => '背景红色',
        '42' => '背景绿色',
        '43' => '背景黄色',
        '44' => '背景蓝色',
        '45' => '背景紫红色',
        '46' => '背景青蓝色',
        '47' => '背景白色',
    ];
    private $foreground_colors = [
        30, 31, 32, 33, 34, 35, 36, 37
    ];
    private $background_colors = [
        40, 41, 42, 43, 44, 45, 46, 47
    ];
    private $show_style        = [
        'default'    => 0,
        'light'      => 1,
        'under_line' => 4,
        'reverse'    => 7
    ];

    public function __construct()
    {
        foreach ($this->show_style as $style) {
            foreach ($this->foreground_colors as $f_color) {
                foreach ($this->background_colors as $b_color) {
                    switch ($style) {
                        case 0:
                            $style_comment = '默认终端';
                            break;
                        case 1:
                            $style_comment = '高亮模式';
                            break;
                        case 4:
                            $style_comment = '下划线模式';
                            break;
                        case 7:
                            $style_comment = '反白模式';
                            break;
                    }
                    $str = $style_comment . ' ' . $this->map[$f_color] . ' ' . $this->map[$b_color] . '('.$style.';'.$f_color.';'.$b_color.'): ' . sprintf("\033[$style;$f_color;{$b_color}m Hello,world! \033[0m" . PHP_EOL);
                    echo $str;
                }
            }
        }
        // "\033[1;31;40m Hello,world! \033[0m"

//        $this->foreground_colors['black']        = '0;30';
//        $this->foreground_colors['dark_gray']    = '1;30';
//        $this->foreground_colors['blue']         = '0;34';
//        $this->foreground_colors['light_blue']   = '1;34';
//        $this->foreground_colors['green']        = '0;32';
//        $this->foreground_colors['light_green']  = '1;32';
//        $this->foreground_colors['cyan']         = '0;36';
//        $this->foreground_colors['light_cyan']   = '1;36';
//        $this->foreground_colors['red']          = '0;31';
//        $this->foreground_colors['light_red']    = '1;31';
//        $this->foreground_colors['purple']       = '0;35';
//        $this->foreground_colors['light_purple'] = '1;35';
//        $this->foreground_colors['brown']        = '0;33';
//        $this->foreground_colors['yellow']       = '1;33';
//        $this->foreground_colors['light_gray']   = '0;37';
//        $this->foreground_colors['white']        = '1;37';
//
//        $this->background_colors['black']      = '40';
//        $this->background_colors['red']        = '41';
//        $this->background_colors['green']      = '42';
//        $this->background_colors['yellow']     = '43';
//        $this->background_colors['blue']       = '44';
//        $this->background_colors['magenta']    = '45';
//        $this->background_colors['cyan']       = '46';
//        $this->background_colors['light_gray'] = '47';
    }

//    public function getColoredString($string, $foreground_color = null, $background_color = null)
//    {
//        $colored_string = "";
//        if (isset($this->foreground_colors[$foreground_color])) {
//            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
//        }
//        if (isset($this->background_colors[$background_color])) {
//            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
//        }
//        $colored_string .= $string . "\033[0m";
//        return $colored_string;
//    }
//
//    public function getForegroundColors()
//    {
//        return array_keys($this->foreground_colors);
//    }
//
//    public function getBackgroundColors()
//    {
//        return array_keys($this->background_colors);
//    }
}

$c = new CLIColorString();

//foreach ($c->getForegroundColors() as $f) {
//    echo $c->getColoredString('哈哈哈1234567890()*&^%$#@sgfudyhbwi', $f) . PHP_EOL;
//}