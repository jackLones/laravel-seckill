

## About laravel-seckill

使用Laravel开发的可扛百万流量的秒杀实战项目,带你从0到1设计一个高并发、高可用的秒杀系统，QPS最高可达到2万+。项目的重点如下:
- 技术选型：Laravel、Mysql、Redis、RabbitMQ、Lua、Nginx、OpenResty等
- 高可用建设：涉及到隔离、流控、削峰、限流、降级、热点和容灾等
- 库存和限购设计：在确保商品不超卖的前提下尽可能提高性能
- 性能调优：涉及Linux、Nginx、网络等方面，目的是让秒杀系
  统响应更快，用户体验更好


可用于生产环境的实战项目，欢迎star啊！

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


## 题外话
- 鉴于本人对laravel的Eloquent ORM 实在不感冒(吐槽一下，学习成本太高，还没啥用处)，所以用的很少



## License

The project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
