# jwt-cas-server
一个基于 JWT 实现「单点登陆」的 [CAS，Central Authentication System](https://apereo.github.io/cas/4.2.x/planning/Architecture.html) 系统。

本项目依赖于 Laravel。

## 系统组成

- CAS Server （服务端，仅有一个）
- CAS Clients （客户端，多个）

用户只需在 Server 端登陆一次，获得 `token` 后便可用该令牌访问系统中的任意 Clients。

**[注意] 此项目为该系统的服务端实现，客户端请移步 https://github.com/uicosp/jwt-cas-client**

Server 端提供两个基本路由

-  POST serverdomain/jwt/login 用于登陆
-  POST serverdomain/jwt/user 用于获取用户信息

此外，本项目已集成第三方微信登陆

- POST serverdomain/jwt/login-via-wechat

## 安装

`composer require "uicosp/jwt-cas-server"`

## 配置

将 `Uicosp\JwtCasServer\CasServiceProvider::class` 添加到 `config/app.php` 的 `providers` 数组。

另外，本项目依赖 [typmon/jwt-auth](https://github.com/tymondesigns/jwt-auth) 和 [laravel-wecaht](https://github.com/overtrue/laravel-wechat)，所以还需添加各自的 service provider：

`Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class`
`Overtrue\LaravelWechat\ServiceProvider::class`
