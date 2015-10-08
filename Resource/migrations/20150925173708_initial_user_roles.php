<?php

use Phinx\Migration\AbstractMigration;

class InitialUserRoles extends AbstractMigration
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
CREATE TABLE "roles" (
"id" BIGSERIAL,
"name" VARCHAR NOT NULL,
"title" VARCHAR,
"description" TEXT,
"created_at" TIMESTAMP,
"updated_at" TIMESTAMP,
CONSTRAINT "roles_name_unique" UNIQUE ("name"),
PRIMARY KEY ("id")
);

CREATE TABLE "user_roles" (
"user_id" BIGINT NOT NULL,
"role_id" BIGINT NOT NULL,
CONSTRAINT "user_roles_users_user_id_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
CONSTRAINT "user_roles_roles_role_id_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
PRIMARY KEY ("user_id", "role_id")
);

CREATE TABLE "resources" (
"id" BIGSERIAL,
"name" VARCHAR NOT NULL,
"title" VARCHAR,
"description" TEXT,
CONSTRAINT "resources_name_unique" UNIQUE ("name"),
PRIMARY KEY ("id")
);

CREATE TABLE "role_resources" (
"role_id" BIGINT NOT NULL,
"resource_id" BIGINT NOT NULL,
CONSTRAINT "role_resources_roles_role_id_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
CONSTRAINT "role_resources_resources_resource_id_id_foreign" FOREIGN KEY ("resource_id") REFERENCES "resources" ("id") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
PRIMARY KEY ("role_id", "resource_id")
);
        ');
    }
}
