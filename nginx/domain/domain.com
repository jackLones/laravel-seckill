server {
         listen 7081;

         error_log logs/domain-error.log error;
         #access_log logs/domain-access.log access;
         default_type text/plain;
         charset utf-8;

         #security token
         set $st "";

         #产品编号
         set $product_id "";

         #用户ID
         set_by_lua_file $user_id ../lua/set_common_var.lua;

         #添加预约资格
         location /api/reserve/(\d+)/users {
            limit_req zone=limit_by_user nodelay;
            proxy_pass http://backend;
            #设置返回的header，并将security token放在header中
            header_filter_by_lua_block{
               ngx.header["st"] = ngx.md5(ngx.var.user_id.."1")
               ngx.header["Access-Control-Expose-Headers"] = "st"

            }
         }

        include D:/project/Go/blitzSeckill/nginx/domain/public.com;

}
