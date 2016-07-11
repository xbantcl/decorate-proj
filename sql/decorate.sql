/*
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_type` tinyint(4) NOT NULL COMMENT '用户类型(1-普通用户，2-商家, 3-装修公司)',
    `account` varchar(255) NOT NULL COMMENT '用户帐号',
    `nick_name` varchar(255) DEFAULT '' COMMENT '用户昵称',
    `avatar` varchar(255) DEFAULT '' COMMENT '用户头像',
    `password` varchar(255) NOT NULL COMMENT '用户密码',
    `salt` varchar(11) NOT NULL COMMENT '盐值',
    `cellphone`  varchar(15) DEFAULT '' COMMENT '手机号码',
    `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱',
    `invite_code` varchar(10) NOT NULL COMMENT '用户邀请码',
    `invited_code` varchar(10) NOT NULL COMMENT '邀请用户的邀请码',
    `sex` tinyint(1) DEFAULT 0 COMMENT '性别 0-未设置, 1-男, 2-女',
    `isPush` tinyint(1) DEFAULT 1 COMMENT '是否推送push',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    `reg_ip` varchar(15) DEFAULT '' COMMENT '注册ip',
    `sys_p` char(1) NOT NULL DEFAULT 'u' COMMENT '注册平台, i-Ios, a-Android, u-Unknown',
    `sys_d` varchar(64) DEFAULT '' COMMENT '设备号',
    `sys_m` varchar(15) DEFAULT '' COMMENT '用户手机类型',
    `sys_v` varchar(15) DEFAULT '' COMMENT '用户系统版本号',
    `cli_v` varchar(15) DEFAULT '' COMMENT '注册应用版本号',
    `cli_p` varchar(15) DEFAULT '' COMMENT '客户端平台: app, web',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='用户数据表';

DROP TABLE IF EXISTS `ord_user`;
CREATE TABLE `ord_user` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `dec_fund` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户装修基金, 单位分',
    `decorate_style` int DEFAULT 0 COMMENT '装修风格',
    `decorate_type` int DEFAULT 0 COMMENT '装修户型',
    `decorate_area` float DEFAULT 0 COMMENT '装修面积',
    `districts` varchar(1024) DEFAULT '' COMMENT '小区名称',
    `decorate_progress` tinyint(4) DEFAULT 1 COMMENT '装修进度, 1-装修准备中, 2-装修进场, 3-装修完成',
    `verify_status` tinyint(4) DEFAULT 1 COMMENT '认证状态, 1-手机通过认证, 2-手机未通过认证',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='普通用户数据表';

DROP TABLE IF EXISTS `seller`;
CREATE TABLE `seller` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `name` varchar(1024) DEFAULT '' COMMENT '商家名称',
    `verify_status` tinyint(4) DEFAULT 1 COMMENT '认证状态, 1-手机通过认证, 2-手机未通过认证',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='商家用户数据表';

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `shop_name` varchar(1024) DEFAULT '' COMMENT '商家名称',
    `location_longitude` varchar(25) DEFAULT '' COMMENT '用户注册经度',
    `location_latitude` varchar(25) DEFAULT '' COMMENT '用户注册纬度',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='商家用户数据表';

DROP TABLE IF EXISTS `boss`;
CREATE TABLE `boss` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `name` varchar(1024) DEFAULT '' COMMENT '商家名称',
    `location_longitude` varchar(25) DEFAULT '' COMMENT '用户注册经度',
    `location_latitude` varchar(25) DEFAULT '' COMMENT '用户注册纬度',
    `verify_status` tinyint(4) DEFAULT 1 COMMENT '认证状态, 1-手机通过认证, 2-手机未通过认证',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='公司用户数据表';

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `company_name` varchar(1024) DEFAULT '' COMMENT '商家名称',
    `location_longitude` varchar(25) DEFAULT '' COMMENT '用户注册经度',
    `location_latitude` varchar(25) DEFAULT '' COMMENT '用户注册纬度',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='公司用户数据表';

DROP TABLE IF EXISTS `designer`;
CREATE TABLE `designer` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `name` varchar(1024) DEFAULT '' COMMENT '设计师名称',
    `location_longitude` varchar(25) DEFAULT '' COMMENT '用户注册经度',
    `location_latitude` varchar(25) DEFAULT '' COMMENT '用户注册纬度',
    `verify_status` tinyint(4) DEFAULT 1 COMMENT '认证状态, 1-手机通过认证, 2-手机未通过认证',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='设计师数据表';

DROP TABLE IF EXISTS `worker`;
CREATE TABLE `worker` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `area_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '地区id',
    `work_type` varchar(35) DEFAULT '' COMMENT '工种',
    `experience` int DEFAULT 1 COMMENT '经验，工作年限',
    `star_level` int DEFAULT 5 COMMENT '星级，大众点评',
    `name` varchar(1024) DEFAULT '' COMMENT '小工名称',
    `verify_status` tinyint(4) DEFAULT 1 COMMENT '认证状态, 1-手机通过认证, 2-手机未通过认证',
    `insert_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='小工数据表';

DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `intr` varchar(255) NOT NULL DEFAULT '' COMMENT '图片描述',
    `url` varchar(1024) CHARACTER SET utf8mb4 NOT NULL DEFAULT '''''' COMMENT '原图云存储地址',
    `mark_url` varchar(1024) CHARACTER SET utf8mb4 NOT NULL DEFAULT '''''' COMMENT '水印图URL',
    `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
    `width` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '若为图片,表示宽度',
    `height` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '若为图片,表示高度',
    `duration` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '若为音视频,表示播放时长',
    `uuid` varchar(64) NOT NULL DEFAULT '' COMMENT '唯一标识位',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='资源表';

DROP TABLE IF EXISTS `diary`;
CREATE TABLE `diary` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `title` varchar(2048) NOT NULL COMMENT '日志标题',
    `decorate_progress` tinyint(4) DEFAULT 1 COMMENT '装修进度, 1-装修准备中, 2-装修进场, 3-装修完成',
    `label_id` int NOT NULL COMMENT '装修标签',
    `content` varchar(2048) NOT NULL COMMENT '日志内容',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='用户日志数据表';

DROP TABLE IF EXISTS `diary_file`;
CREATE TABLE `diary_file` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `diary_id` int(11) unsigned NOT NULL COMMENT '日志id',
    `file_id` int(11) unsigned NOT NULL COMMENT '日志图片id',
    `file_url` varchar(64) NOT NULL COMMENT '日志图片地址',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='日志图片数据表';

DROP TABLE IF EXISTS `diary_comment`;
CREATE TABLE `diary_comment` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `diary_id` int(11) unsigned NOT NULL COMMENT '日志id',
    `parent_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '父评论id',
    `target_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '被评论用户id',
    `content` varchar(2048) NOT NULL COMMENT '评论内容',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='日志评论数据表';

DROP TABLE IF EXISTS `diary_comment_file`;
CREATE TABLE `diary_comment_file` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `diary_comment_id` int(11) unsigned NOT NULL COMMENT '日志评论id',
    `file_id` int(11) unsigned NOT NULL COMMENT '日志图片id',
    `file_url` varchar(64) NOT NULL COMMENT '日志图片地址',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='日志评论图片数据表';

*/
DROP TABLE IF EXISTS `discuss`;
CREATE TABLE `discuss` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `label_id` varchar(32) DEFAULT '' COMMENT '装修标签',
    `content` varchar(2048) NOT NULL COMMENT '讨论内容',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='用户讨论数据表';

DROP TABLE IF EXISTS `discuss_file`;
CREATE TABLE `discuss_file` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `discuss_id` int(11) unsigned NOT NULL COMMENT '讨论id',
    `file_id` int(11) unsigned NOT NULL COMMENT '讨论图片id',
    `file_url` varchar(64) NOT NULL COMMENT '日志图片地址',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='讨论图片数据表';

DROP TABLE IF EXISTS `discuss_comment`;
CREATE TABLE `discuss_comment` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL COMMENT '用户id',
    `discuss_id` int(11) unsigned NOT NULL COMMENT '讨论id',
    `parent_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '父评论id',
    `target_uid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '被评论用户id',
    `content` varchar(2048) NOT NULL COMMENT '评论内容',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='日志评论数据表';

DROP TABLE IF EXISTS `discuss_comment_file`;
CREATE TABLE `discuss_comment_file` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `discuss_comment_id` int(11) unsigned NOT NULL COMMENT '讨论评论id',
    `file_id` int(11) unsigned NOT NULL COMMENT '讨论评论图片id',
    `file_url` varchar(64) NOT NULL COMMENT '讨论评论图片地址',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='讨论评论图片数据表';

DROP TABLE IF EXISTS `decorate_label`;
CREATE TABLE `decorate_label` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL COMMENT '标签名称',
    `parentId` int(11) unsigned NOT NULL COMMENT '讨论评论图片id',
    `insert_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='装修标签';
