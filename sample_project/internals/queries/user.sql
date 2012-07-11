create table if not exists `user` (
	`id` int not null auto_increment,
	`first_name` varchar(20),
	`last_name` varchar(20),
	`email` varchar(70),
	`username` varchar(50),
	`password` varchar(50),
	`permission_groups` enum ('basic'),
	primary key(`id`)
);
