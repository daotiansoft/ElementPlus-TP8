# element-ui-admin-tp6

通用后台管理系统：ThinkPHP 6 提供接口，Vue 3 + Element Plus 提供管理界面，另附一个 Python 签名客户端用于开放接口对接。

界面改造自 [vue-element-admin](https://github.com/PanJiaChen/vue-element-admin) 的设计思路。

## 目录结构

```
element-ui-admin-tp6/
├── api/                    后端（ThinkPHP 6，多应用：admin / common / user / task / openapi）
│   ├── app/                控制器、服务、验证器、模型
│   ├── config/             框架与应用配置（cors.php、jwt.php 等）
│   ├── db/migrations/      增量 SQL（建表结构以 dbdata.sql 为准）
│   ├── dbdata.sql          完整表结构 + 基础数据
│   └── .example.env        环境变量模板
├── manage/                 前端（Vue 3 + Vite + Element Plus + Pinia）
│   ├── src/api/            接口模块（基于 createApi 工厂）
│   ├── src/components/     FormDialog / CrudPagination / Tinymce / Upload
│   ├── src/composables/    useTableList（列表页通用逻辑）、useBalanceTypes 等
│   └── src/views/          页面
├── openapi/                开放接口的 Python 客户端（appid + sign 签名）
└── .github/workflows/ci.yml
```

## 环境要求

| 组件    | 版本                                |
| ------- | ----------------------------------- |
| PHP     | 7.4+（`composer.json` 要求 >= 7.1） |
| MySQL   | 5.7+（表与列均为 utf8mb4）          |
| Redis   | 缓存驱动默认使用 redis              |
| Node.js | ^20.19 或 >=22.12（Vite 8 要求）    |
| Python  | 3.9+（仅 openapi 客户端需要）       |

## 快速开始

### 1. 后端

```bash
cd api
composer install

# 环境变量：务必配置数据库、JWT 密钥与跨域白名单
cp .example.env .env

# 建库（字符集 utf8mb4）并导入结构与基础数据
mysql -uroot -p -e "CREATE DATABASE dbdata DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -uroot -p dbdata < dbdata.sql

# 如库是早期导入的，按需执行增量脚本
mysql -uroot -p dbdata < db/migrations/2026-10-15-index-optimization.sql
```

把站点根目录指向 `api/public`。本地开发习惯用 `admin.test.com`，可在 hosts 中加：

```
127.0.0.1  admin.test.com
```

初始账号来自 `dbdata.sql` 种子数据：`admin / 123456`，**首次登录后请立即修改密码**。

### 2. 前端

```bash
cd manage
npm install          # 同时会为当前仓库配置 pre-commit 钩子
# 按需修改 .env.development 中的 VITE_API_BASE_URL（默认 http://admin.test.com）
npm run dev          # 开发
npm run build        # 打包到 manage/dist
```

### 3. openapi 客户端（可选）

```bash
cd openapi
python -m pip install -r requirements.txt
cp .env.example .env   # 填写 OPENAPI_BASE_URL / OPENAPI_APPID / OPENAPI_SECRET
```

> `dbdata.sql` 里 `dt_app_list.secret` 是 `CHANGE_ME` 占位值。请在后台「账号管理 → API管理」重新生成密钥，
> 再把同样的值写入 `openapi/.env`。`.env` 已被 gitignore，请勿把真实密钥提交到仓库。

## 环境变量

### api/.env

| 变量                 | 说明                                                                                              |
| -------------------- | ------------------------------------------------------------------------------------------------- |
| `APP_DEBUG`          | 调试开关。生产环境必须为 `false`，否则会把异常细节返回给前端                                      |
| `DATABASE.*`         | 数据库连接，`CHARSET` 保持 `utf8mb4`                                                              |
| `CORS.ALLOW_ORIGINS` | 允许跨域的来源，多个用英文逗号分隔。留空表示回显请求 Origin（仅适合本地联调）；生产必须配置白名单 |
| `JWT.SECRET`         | token 签名密钥，生产必须配置固定随机串；留空会自动生成并落盘到 `runtime/jwt_key`                  |

### manage/.env.*

| 变量                | 说明                                                                         |
| ------------------- | ---------------------------------------------------------------------------- |
| `VITE_API_BASE_URL` | 接口地址。开发为 `http://admin.test.com`；打包生产环境建议留空走同源反向代理 |
| `VITE_APP_TITLE`    | 浏览器标签页标题                                                             |

## 接口约定

所有接口统一返回信封，并按语义返回 HTTP 状态码：

```json
{ "code": 1, "msg": "获取成功", "data": {} }
```

| 场景                | HTTP | code     |
| ------------------- | ---- | -------- |
| 成功                | 200  | `1`      |
| 未登录 / 登录态失效 | 401  | `-10001` |
| 无权限              | 403  | `-10002` |
| 参数校验或业务错误  | 422  | `-1`     |
| 系统异常            | 500  | `-1`     |

前端 `src/utils/request.js` 已统一处理：`code === 1` 时直接剥离出 `data`，`-10001` 会清理登录态并回到登录页，
其余情况弹出后端返回的具体原因。

鉴权方式有两种：

- 后台接口：请求头 `Api-Token`（JWT）
- 开放接口（`/task/api/*` 等）：`appid + time + sign` 签名，见 `openapi/`

权限按「权限节点 → 角色 → 账号」三级控制，路径形如 `admin/member/items`，
新增接口后需在后台「账号管理 → 权限节点」登记并授权给角色，否则会返回 403。

## 开发约定

### 代码风格

```bash
cd manage
npm run lint          # ESLint
npm run format        # Prettier 写入
npm run format:check  # Prettier 校验
```

`npm install` 会执行 `scripts/setup-git-hooks.mjs`，把 `core.hooksPath` 指向 `manage/.husky`，
提交前由 lint-staged 对暂存文件跑 ESLint + Prettier（详见该脚本内注释，因为本仓库 `package.json` 不在 git 根目录，无法直接使用 husky）。
类 Unix 系统需要钩子文件保留可执行权限。

编辑器跳转与别名提示依赖 `manage/jsconfig.json` 中的 `@/*` 等路径映射。

`vite.config.mjs` 的 `optimizeDeps.include` 登记了 Element Plus 按需样式的全部入口
（启动时扫描 `node_modules/element-plus/es/components/*/style` 生成）。这些深层导入由
unplugin-vue-components 在编译期注入，esbuild 预扫描看不到，不登记就会在开发时反复打印
`optimized dependencies changed. reloading` 并整页刷新，因此不要删除这段配置。

### 复用组件

| 能力          | 用法                                                                                                                                                                                                                             |
| ------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 新增/编辑弹窗 | `components/FormDialog/index.vue`：一份 `fields` 配置驱动；`:model` 传初始值、`@submit(data)` 回传编辑结果（组件内部持有副本，父组件无需逐字段双向绑定），`disabledInEdit: true` 的字段在编辑态禁用，`hidden` 支持函数式条件显示 |
| 列表页逻辑    | `composables/useTableList.js`：统一查询表单、分页、批量删除，页面只需提供 `fetchList` 与 `deleteApi`                                                                                                                             |
| 交易类型      | `composables/useBalanceTypes.js`：拉取后端 `types` 接口，选项唯一来源是后端的 `BalanceService::$typeMap`                                                                                                                         |
| 分页条        | `components/CrudPagination/index.vue`                                                                                                                                                                                            |
| 接口模块      | `utils/baseApi.js` 的 `createApi(前缀, 扩展方法)`，无需再写 class                                                                                                                                                                |

### 数据与安全注意事项

- 时间字段为整型时间戳，模型层不做自动时间戳，由业务代码写入。
- 余额以「分」为单位存储，展示时用 `utils/format.js` 的 `formatFen` 换算成元。
- 余额流水的交易类型不入库，唯一数据源是 `app/common/service/BalanceService.php` 的静态 map
  `BalanceService::$typeMap`（键用 `BalanceTypeEnum` 的常量），由 `types` 接口下发、由
  `BalanceService::isValidType()` 在写入时校验；新增类型只改这份 map，无需配置文件和数据迁移。
- 余额流水不允许物理删除，修正金额请新增一笔冲正记录。
- 删除角色/账号/权限节点时会先做引用校验（例如角色仍被账号使用时拒绝删除），并在事务内级联清理关联数据。
  账号的数据引用校验按钩子组织（`app/admin/service/UserDeleteGuard.php`）：内置「有余额」「有流水」两条规则，
  需要新增判断时用 `UserDeleteGuard::register(闭包)` 追加，闭包返回非空字符串即中断删除。
- 项目为纯 HTTP 接口，已移除 Websocket（`think-worker` 依赖、`config/worker*.php` 与对应的 server 类）。
- 修改密码会使该账号此前签发的 token 立即失效；登录密码连续错误会临时锁定账号若干分钟。
- 提交前请确认没有把 `.env`、密钥、真实数据库导出提交进版本库。

## 许可

`api/` 使用 ThinkPHP 框架，遵循 Apache-2.0（见 `api/LICENSE.txt`）。
