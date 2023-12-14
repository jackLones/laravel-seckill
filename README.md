

## About laravel-seckill

使用Laravel开发的可扛百万流量的秒杀实战项目,带你从0到1设计一个高并发、高可用的秒杀系统，QPS最高可达到2万+。项目的重点如下:
- 技术选型：Laravel、Mysql、Redis、RabbitMQ、Lua、Nginx、OpenResty等
- 高可用建设：涉及到隔离、流控、削峰、限流、降级、热点和容灾等
- 库存和限购设计：在确保商品不超卖的前提下尽可能提高性能
- 性能调优：涉及Linux、Nginx、网络等方面，目的是让秒杀系
  统响应更快，用户体验更好


可用于生产环境的实战项目，欢迎star啊！

## 秒杀结构设计
### 服务设计
* 接入层服务
* 商品信息服务
* 秒杀信息服务
* 预扣服务
* 库存服务
* 订单服务

<image src="resources/images/服务层设计.png" width="400">

### 功能结构与系统架构的交互
<image src="resources/images/功能结构与系统架构图.png" width="800" height="600">

## 结合业务的整体架构
<image src="resources/images/结合业务的整体架构图.png" width="800" >

## 预约系统架构
<image src="resources/images/预约系统架构.png" width="600" height="400">

## 预扣系统


## 核心功能进度
*   [X] 流量控制、削峰、限流
*   [X] 异步队列
*   [ ] 系统隔离
*   [ ] 灰度发布
*   [ ] 服务降级
*   [ ] 动态扩容
*   [ ] 服务熔断
*   [ ] 分库分表
*   [ ] 服务注册与发现

## 先测试跑通核心下单功能
* 先redis执行： set stock:1515161 100
* mysql执行：
```
* INSERT INTO `seckill`.`t_activity` (`id`, `activity_name`, `product_id`, `activity_start_date`, `activity_end_date`, `limit_num`, `stock_num`, `status`, `activity_picture_url`, `activity_price`, `created_at`, `updated_at`, `is_enable`) VALUES (1, '11-03测试活动', 1515161, '2023-11-03 17:50:02', '2023-11-03 17:50:05', 1, 107, 1, NULL, 100.00, '0000-00-00 00:00:00', '2023-12-14 14:56:13', 1);
```
* 开始下单：你的url/api/order/submitData
* rabbitmq异步消费订单数据：php artisan queue:work rabbitmq

## 题外话
- 鉴于本人对laravel的Eloquent ORM 实在不感冒(吐槽一下，学习成本太高，还没啥用处)，所以用的很少



## License

The project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
