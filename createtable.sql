-- Table: "motor-list"
-- DROP TABLE "motor-list";
CREATE TABLE "motor-list"
(
  id serial NOT NULL,
  site character varying(100),
  siteid character varying(50),
  equipname character varying(50),
  model character varying(50),
  power numeric(50,2) DEFAULT 0,
  voltage character varying(50),
  insulate character varying(2),
  protect character varying(4),
  eplevel character varying(50),
  rpm integer DEFAULT 0,
  weight integer DEFAULT 0,
  puttype character varying(50),
  bearingf character varying(50),
  bearingb character varying(50),
  serial character varying(50),
  factory character varying(50),
  useful character varying(50),
  oil integer DEFAULT 0,
  permaintain integer DEFAULT 0,
  percheck integer DEFAULT 0,
  maintainer character varying(30),
  CONSTRAINT "motor-list_pkey" PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE "motor-list"
  OWNER TO webuser;

-- Index: "motor-list_siteid_idx"
-- DROP INDEX "motor-list_siteid_idx";
CREATE UNIQUE INDEX "motor-list_siteid_idx"
  ON "motor-list"
  USING btree
  (siteid COLLATE pg_catalog."default");


-- Table: sysdataauth
-- DROP TABLE sysdataauth;
CREATE TABLE sysdataauth
(
  id integer NOT NULL,
  uid character varying(20),
  "AuthSort" character varying(20),
  "AuthType" integer,
  CONSTRAINT bdataauth_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sysdataauth
  OWNER TO webuser;


-- Table: sysdataauthlist
-- DROP TABLE sysdataauthlist;
CREATE TABLE sysdataauthlist
(
  id integer NOT NULL,
  uid character varying(20),
  "AuthSort" character varying(20),
  "AuthDes" character varying(100),
  "AuthType" integer,
  CONSTRAINT bdataauthlist_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sysdataauthlist
  OWNER TO webuser;


-- Table: syslog
-- DROP TABLE syslog;
CREATE TABLE syslog
(
  id serial NOT NULL,
  buid integer,
  buser character varying(30),
  bdate date,
  btime timestamp without time zone,
  bip character varying(72),
  bclient character varying(20),
  baction character varying(20),
  bsucc integer,
  CONSTRAINT blog_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE syslog
  OWNER TO webuser;


-- Table: "syslog-action"
-- DROP TABLE "syslog-action";
CREATE TABLE "syslog-action"
(
  id integer NOT NULL,
  bid character varying(10) NOT NULL,
  bname character varying(50) NOT NULL,
  CONSTRAINT blog_action_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE "syslog-action"
  OWNER TO webuser;
-- Index: bid
-- DROP INDEX bid;
CREATE UNIQUE INDEX bid
  ON "syslog-action"
  USING btree
  (bid COLLATE pg_catalog."default");


-- Table: sysuser
-- DROP TABLE sysuser;
CREATE TABLE sysuser
(
  id integer NOT NULL,
  uid integer NOT NULL,
  uname character varying(20) NOT NULL,
  ename character varying(30),
  cname character varying(40),
  pass character(40),
  branch character varying(12),
  dep character varying(50),
  tel character varying(200),
  mb character varying(100),
  email character varying(30),
  standing character varying(10) NOT NULL,
  regtime timestamp without time zone,
  del integer NOT NULL,
  CONSTRAINT buser_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sysuser
  OWNER TO webuser;
-- Index: email
-- DROP INDEX email;
CREATE UNIQUE INDEX email
  ON sysuser
  USING btree
  (email COLLATE pg_catalog."default");
-- Index: uid
-- DROP INDEX uid;
CREATE UNIQUE INDEX uid
  ON sysuser
  USING btree
  (uid);
-- Index: uname
-- DROP INDEX uname;
CREATE UNIQUE INDEX uname
  ON sysuser
  USING btree
  (uname COLLATE pg_catalog."default");


-- Table: "motor-status"
-- DROP TABLE "motor-status";
CREATE TABLE "motor-status"
(
  id serial NOT NULL,
  bdate date,
  siteid character varying(50),
  status character varying(1) DEFAULT 0,
  uid integer,
  uptime timestamp without time zone,
  CONSTRAINT "motor-status_pkey" PRIMARY KEY (id),
  CONSTRAINT "motor-status_bdate_siteid_key" UNIQUE (bdate, siteid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE "motor-status"
  OWNER TO webuser;


-- Table: sysconf
-- DROP TABLE sysconf;
CREATE TABLE sysconf
(
  id serial NOT NULL,
  sysname character varying(20) NOT NULL,
  sysvalue character varying(30),
  uid smallint NOT NULL,
  uptime timestamp without time zone,
  CONSTRAINT sysconf_pkey PRIMARY KEY (id),
  CONSTRAINT sysconf_sysname_key UNIQUE (sysname)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE sysconf
  OWNER TO webuser;
