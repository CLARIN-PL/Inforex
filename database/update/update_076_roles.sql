/*
Changing the length of the role column in roles and users_roles tables.
 */
LOCK TABLES
roles WRITE,
users_roles WRITE;

ALTER TABLE users_roles
DROP FOREIGN KEY users_roles_ibfk_2;

ALTER TABLE users_roles MODIFY role varchar(32);

ALTER TABLE roles MODIFY role varchar(32);
ALTER TABLE users_roles
ADD CONSTRAINT users_roles_ibfk_2 FOREIGN KEY (role)
REFERENCES roles (role);

UNLOCK TABLES;

/*
Adding new roles
 */
INSERT INTO `roles` (`role`, `description`) VALUES ('editor_schema_events', 'Edit schema events.');
INSERT INTO `roles` (`role`, `description`) VALUES ('editor_schema_relations', 'Edit schema relations.');