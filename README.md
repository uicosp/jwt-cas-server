# 一个基于 JWT 实现「单点登陆」的 [CAS，Central Authentication System](https://apereo.github.io/cas/4.2.x/planning/Architecture.html) 系统

## 系统组成

- CAS Server （服务端，仅有一个）
- CAS Clients （客户端，多个）

用户只需在 Server 端登陆一次，获得 `token` 后便可用该令牌访问系统中的任意 Clients。

**[注意] 此项目为该系统的服务端实现，客户端请移步 https://github.com/uicosp/jwt-cas-client**

Server 端 提供两个路由

-  POST serverdomain/jwt/login 用于登陆
-  POST serverdomain/jwt/user 用于获取用户信息
