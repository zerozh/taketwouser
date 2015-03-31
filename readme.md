# Laravel 5 多表用户支持

Laravel 自带的用户模块使用一个表保存用户信息，拓展性不足。本模块使用用户信息表和用户授权表共同组成，
可以方便地拓展各种登录/注册类型，手机、用户名或者是新浪微博等第三方登录。

本模块基于 Laravel 5 Service Provider。

## 安装

* composer 中添加 `zerozh/taketwouser` `dev-master` 分支并 update
* 添加 `Taketwo\Providers\AuthServiceProvider` 到 config/app.providers 中
* 修改 config/auth.driver 为 `taketwo`
