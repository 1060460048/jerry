#用redis作为数据库实现微博系统
这个版本中已经把粉丝主页中显示自己的微博及关注者发布信息的获取方式改成了拉，用redis维护了一个有序集合，因为有序集合这种redis的存储方式，可以更方便的按照数据的所在位置（即先后入库存在于redis时间横轴上的坐标位置）来拉取信息，并且限制了数据的展示条数，记录某次拉取数据更新到的时间点，则下次数据进行拉取的时候，不必对已经拉取过的时间点前的数据进行拉取，只要以那个时间点为边界，拉取最新的数据展示出来即可以。当到达我们设置的这阀值，就会有数据会从有序集合的右侧使用redis内置的rpoplpush这种方式踢出并记录到一个一个全局的global:store中，而我们创建的mysql存数据脚本目的就是用来监测这个全局的global:store中数据量并把达标数据写入到mysql里面；<br />
每个人微博的前1000条数据存于redis,更旧的数据存于mysql数据库
思路：每个人1000条以前的，都推到global:store中，用定时任务取global:store中的前1000条，入数据库。<br />
感谢php领域燕十八老师的
#本人qq：1060460048
