-- permission groups table
-- one permission group to many permissions
create table if not exists `permission_group` (
	`id` int not null auto_increment,
	`name` varchar(20),
	primary key (`id`)
);

-- permmissions table
-- one permission to one permission group
create table if not exists `permission` (
	`id` int not null auto_increment,
	`permission_group_id` int not null,
	`name` varchar(20),
	`groups` enum ('basic'),
	primary key (`id`),
	foreign key (`permission_group_id`) references permission_group(`id`)
);
