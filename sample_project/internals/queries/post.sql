create table if not exists `post` (
	`id` int not null auto_increment,
	`subject` varchar(25),
	`body` varchar(100),
	primary key (`id`)
);

create table if not exists `comment` (
	`id` int not null auto_increment,
	`post_id` int not null,
	`comment` varchar(50),
	foreign key (`post_id`) references post(`id`),
	primary key (`id`)
);


-- data
insert into `post` values
(1, "first post", "post post post...."),
(2, "second post", "post post post...."),
(3, "third post", "post post post...."),
(4, "fourth post", "post post post....");

insert into `comment` values
(1, 1, "my comment...."),
(2, 1, "my comment...."),
(3, 1, "my comment...."),
(4, 1, "my comment....");
