## 安装步骤
<h1 align="center"> smscode support tengxin lexin yunpian </h1>
<p align="center"> :pencil: A tool used send smscode support 'tengxin' 'lexin' 'yunpian'.</p>

## Installing

```shell
$ composer require pianqifei/smscode 
```
# 
php artisan vendor:publish --provider="Pqf\Smscode\SmsServieProvider" 
发布sms配置文件 数据库文件 翻译文件(短信模板和签名)
# 
php artisan migrate 迁移数据表
# 
user Pqf\Smscode\Facades\SmsCode 门面方法
# 
Pqf\Smscode\Sms 具体发送逻辑

