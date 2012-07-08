create table if not exists `user` (
	`id` int not null auto_increment,
	`name` varchar(50),
	`email` varchar(70),
	`username` varchar(50),
	`password` varchar(50),
	`permission_groups` enum ('basic'),
	primary key(`id`)
);
