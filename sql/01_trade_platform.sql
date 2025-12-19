/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2025/12/16 12:58:38                          */
/*==============================================================*/

drop table if exists cart16;

drop table if exists cart_product;

drop table if exists category16;

drop table if exists cmplain16;

drop table if exists order16;

drop table if exists process16;

drop table if exists product16;

drop table if exists userInfo16;

drop table if exists violation16;

/*==============================================================*/
/* Table: cart16                                                */
/*==============================================================*/
CREATE TABLE cart16 (
    cid VARCHAR(50) NOT NULL,
    uid VARCHAR(50) NOT NULL,
    cdate DATETIME,
    status SMALLINT DEFAULT 0, -- 0为未支付，1为已支付
    PRIMARY KEY (cid)
);

/*==============================================================*/
/* Table: cart_product16                                        */
/*==============================================================*/
create table cart_product16
(
   pid                  varchar(50) not null,
   cid                  varchar(50) not null,
   quantity             smallint,
   primary key (pid, cid)
);

/*==============================================================*/
/* Table: category16                                            */
/*==============================================================*/
create table category16
(
   category_id          varchar(50) not null,
   category_name        varchar(50) not null,
   primary key (category_id)
);

/*==============================================================*/
/* Table: cmplain16                                             */
/*==============================================================*/
create table cmplain16
(
   oid                  varchar(50) not null,
   uid                  varchar(50) not null,
   complain_id          varchar(50) not null,
   pro_uid              varchar(50),
   pro_oid              varchar(50),
   process_id           varchar(50),
   pid                  varchar(50) not null,
   cdate                datetime default now(), -- 投诉时间
   reason               varchar(200),
   status               smallint not null default 0, -- 0为待处理，1为已处理
   primary key (oid, uid, complain_id)
);

/*==============================================================*/
/* Table: order16                                               */
/*==============================================================*/
create table order16
(
   oid                  varchar(50) not null,
   cmp_uid              varchar(50),
   complain_id          varchar(50),
   pid                  varchar(50) not null,
   uid                  varchar(50) not null,
   cid                  varchar(50),
   tdate                datetime default now(), -- 订单时间
   amount               decimal(10,2),
   status               smallint not null default 0, -- 0为未支付，1为已支付
   primary key (oid, uid)
);

/*==============================================================*/
/* Table: process16                                             */
/*==============================================================*/
create table process16
(
   oid                  varchar(50) not null,
   process_id           varchar(50) not null,
   complain_id          varchar(50) not null,
   uid                  varchar(50) not null,
   use_uid              varchar(50),
   process_date         datetime default now(), -- 处理时间
   result               varchar(200),
   primary key (oid, process_id, complain_id, uid)
);

/*==============================================================*/
/* Table: product16                                             */
/*==============================================================*/
create table product16
(
   pid                  varchar(50) not null,
   pname                varchar(50) not null,
   category_id          varchar(50) not null,
   oid                  varchar(50),
   uid                  varchar(50),
   pyear                smallint,
   usedmonth            smallint,
   price                decimal(10,2),
   contact              varchar(50) not null,
   status               smallint not null default 1, -- 1为上架，0为下架
   primary key (pid)
);

/*==============================================================*/
/* Table: userInfo16                                            */
/*==============================================================*/
create table userInfo16
(
   uid                  varchar(50) not null,
   name                 varchar(50) not null,
   pw_hash              char(64) not null,
   role                 smallint not null default 0, -- 0为用户，1为管理员
   status               smallint not null default 1, -- 0为禁用，1为启用
   primary key (uid)
);

/*==============================================================*/
/* Table: violation16                                           */
/*==============================================================*/
create table violation16
(
   violation_id         varchar(50) not null,
   uid                  varchar(50) not null,
   reason               varchar(200),
   vdate                datetime default now(),
   primary key (violation_id, uid)
);

/*==============================================================*/
/* Table: favorite16                                            */
/*==============================================================*/
create table favorite16
(
   uid                  varchar(50) not null,
   pid                  varchar(50) not null,
   fdate                datetime default now(),
   primary key (uid, pid)
);

alter table cart_product add constraint FK_cart_product foreign key (pid)
      references product16 (pid) on delete restrict on update restrict;

alter table cart_product add constraint FK_cart_product2 foreign key (cid)
      references cart16 (cid) on delete restrict on update restrict;

alter table cmplain16 add constraint FK_complain_process foreign key (pro_oid, process_id, complain_id, pro_uid)
      references process16 (oid, process_id, complain_id, uid) on delete restrict on update restrict;

alter table cmplain16 add constraint FK_order_complain2 foreign key (oid, uid)
      references order16 (oid, uid) on delete restrict on update restrict;

alter table order16 add constraint FK_order_cart foreign key (cid)
      references cart16 (cid) on delete restrict on update restrict;

-- alter table order16 add constraint FK_order_complain foreign key (oid, cmp_uid, complain_id)
--       references cmplain16 (oid, uid, complain_id) on delete restrict on update restrict;

alter table order16 add constraint FK_order_product foreign key (pid)
      references product16 (pid) on delete restrict on update restrict;

alter table order16 add constraint FK_user_product foreign key (uid)
      references userInfo16 (uid) on delete restrict on update restrict;

alter table process16 add constraint FK_admin_process foreign key (use_uid)
      references userInfo16 (uid) on delete restrict on update restrict;

alter table process16 add constraint FK_complain_process2 foreign key (oid, uid, complain_id)
      references cmplain16 (oid, uid, complain_id) on delete restrict on update restrict;

alter table product16 add constraint FK_category_product foreign key (category_id)
      references category16 (category_id) on delete restrict on update restrict;

-- alter table product16 add constraint FK_order_product2 foreign key (oid, uid)
--       references order16 (oid, uid) on delete restrict on update restrict;

alter table violation16 add constraint FK_user_volation foreign key (uid)
      references userInfo16 (uid) on delete restrict on update restrict;

