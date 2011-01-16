--
-- Phoebius Framework v.1.2.0
-- Generated at 2011/01/16 16:08 for PgSql
--

CREATE TABLE "test_user"(
	"id" int4 NOT NULL,
	"name" character varying NOT NULL,
	"remote_addr_mac" character varying NOT NULL,
	"remote_addr_ip" character varying NOT NULL
);

CREATE TABLE "test_entry"(
	"id" int4 NOT NULL,
	"title" character varying NOT NULL,
	"author" int4 NOT NULL
);

CREATE TABLE "test_comment"(
	"id" int4 NOT NULL,
	"entry" int4 NOT NULL,
	"comment" character varying NOT NULL,
	"time" timestamp NOT NULL
);

ALTER TABLE "test_user" ADD PRIMARY KEY ("id");

ALTER TABLE "test_user" ADD CONSTRAINT "remoteAddr_uq" UNIQUE ("remote_addr_mac", "remote_addr_ip");

ALTER TABLE "test_user" ADD CONSTRAINT "mac_uq" UNIQUE ("remote_addr_mac");

ALTER TABLE "test_entry" ADD PRIMARY KEY ("id");

ALTER TABLE "test_entry" ADD CONSTRAINT "author_fk" FOREIGN KEY ("author") REFERENCES "test_user"("id") ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE "test_comment" ADD PRIMARY KEY ("id");

ALTER TABLE "test_comment" ADD CONSTRAINT "entry_fk" FOREIGN KEY ("entry") REFERENCES "test_entry"("id") ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE "test_comment" ADD INDEX "time_idx" ("time");

CREATE SEQUENCE "test_user_id_sq";

ALTER SEQUENCE "test_user_id_sq" OWNED BY "test_user"."id";

ALTER TABLE "test_user" ALTER COLUMN "id" SET DEFAULT nextval ( 'test_user_id_sq' );

CREATE SEQUENCE "test_entry_id_sq";

ALTER SEQUENCE "test_entry_id_sq" OWNED BY "test_entry"."id";

ALTER TABLE "test_entry" ALTER COLUMN "id" SET DEFAULT nextval ( 'test_entry_id_sq' );

ALTER TABLE "test_entry" ADD INDEX ("author");

CREATE SEQUENCE "test_comment_id_sq";

ALTER SEQUENCE "test_comment_id_sq" OWNED BY "test_comment"."id";

ALTER TABLE "test_comment" ALTER COLUMN "id" SET DEFAULT nextval ( 'test_comment_id_sq' );

ALTER TABLE "test_comment" ADD INDEX ("entry");