### PHP异步任务一种思路


#### 目录结构


```
.
├── AsyncTask.php //异步任务类
├── README.md
├── script // 异步任务脚本目录
│   └── test_1.php 
├── task_map_config.php //生成的异步任务map
└── test.php //测试 demo
```

#### 运行test.php结果

```
start
bool(false)
string(74) " --name=hahha哈哈哈 --age=123 --aaa=jkhgf9876 --bbb=bbbbb --ccc=ccccccc"
end

脚本开始运行
string(106) "接收到的参数：{"name":"hahha哈哈哈","age":"123","aaa":"jkhgf9876","bbb":"bbbbb","ccc":"ccccccc"}"
name=>hahha哈哈哈
age=>123
aaa=>jkhgf9876
bbb=>bbbbb
ccc=>ccccccc
脚本执行结束,运行时间:5.0003340244293
Process finished with exit code 0
```