<?php

use Phinx\Migration\AbstractMigration;

/**
 * Initial Users Migration
 */
class InitialUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $this->execute('
CREATE TABLE "users" (
"id" BIGSERIAL,
"username" VARCHAR NOT NULL,
"email" VARCHAR NOT NULL,
"password" VARCHAR NOT NULL,
"status" INTEGER NOT NULL DEFAULT 0,
"created_at" TIMESTAMP,
"updated_at" TIMESTAMP,
CONSTRAINT "users_username_unique" UNIQUE ("username"),
CONSTRAINT "users_email_unique" UNIQUE ("email"),
PRIMARY KEY ("id")
);
CREATE INDEX "users_created_at_index" ON "users" ("created_at");
CREATE INDEX "users_updated_at_index" ON "users" ("updated_at");
COMMENT ON COLUMN "users"."status" IS \'inactive, active, banned\';

CREATE TABLE "user_details" (
"id" BIGSERIAL NOT NULL,
"user_id" BIGINT NOT NULL,
"key" VARCHAR NOT NULL,
"value" TEXT,
CONSTRAINT "user_details_users_user_id_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
CONSTRAINT "user_details_user_id_key_unique" UNIQUE ("user_id", "key")
);
');
    }
}
