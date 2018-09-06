<?php
/**
 * Desc:
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/9/6
 * Time: 上午11:20
 */

//需要扩展：
//      0：要安装yaml 扩展 首先需要先安装libyaml的源码包，这个libyaml是安装php的yaml扩展的前置条件，我们到下面这个网址可以下载到最新版本，https://pyyaml.org/wiki/LibYAML
//    1：http://pecl.php.net/package/yaml
$config=yaml_parse_file('app.yaml');
var_dump($config);