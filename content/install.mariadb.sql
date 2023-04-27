create table /*tableprefix*/user (
  id int not null,
  deleted smallint(1) not null default 0,
  email varchar(255) not null,
  password varchar(255),
  primary key (id)
);


create table /*tableprefix*/node (
  id int not null auto_increment,
  type char(50) not null,
  created_by int,
  created_at timestamp not null default current_timestamp,
  updated_by int,
  updated_at timestamp null,

  primary key(id),
  foreign key (created_by) references /*tableprefix*/user(id),
  foreign key (updated_by) references /*tableprefix*/user(id)
);


create table /*table_prefix*/setting (
  id int not null, 
  deleted smallint(1) not null default 0,
  user_id int,

  name varchar(255) not null,
  value mediumtext,

  primary key (id),
  foreign key (user_id) references /*table_prefix*/user(id)
);

create table /*table_prefix*/role (
  id int not null, 
  deleted smallint(1) not null default 0,

  primary key (id)
);

create table /*table_prefix*/role_text (
  id int not null,
  text_id int not null,
  locale char(7) not null,

  name varchar(255) not null,

  primary key (id),
  foreign key (text_id) references /*table_prefix*/role(id),
  index (text_id, locale)
);


create table /*table_prefix*/permission (
  id int not null, 
  deleted smallint(1) not null default 0,

  primary key (id)
);

create table /*table_prefix*/permission_text (
  id int not null,
  text_id int not null,
  locale char(7) not null,

  name varchar(255) not null,

  primary key (id),
  foreign key (text_id) references /*table_prefix*/permission(id),
  index (text_id, locale)
);

create table /*table_prefix*/user_role (
  user_id int not null,
  role_id int not null,

  primary key (user_id, role_id),
  foreign key (user_id) references /*table_prefix*/user(id),
  foreign key (role_id) references /*table_prefix*/role(id)
);

create table /*table_prefix*/user_permission (
  user_id int not null,
  permission_id int not null,

  primary key (user_id, permission_id),
  foreign key (user_id) references /*table_prefix*/user(id),
  foreign key (permission_id) references /*table_prefix*/permission(id)
);

create table /*table_prefix*/role_permission (
  role_id int not null,
  permission_id int not null,

  primary key (role_id, permission_id),
  foreign key (role_id) references /*table_prefix*/role(id),
  foreign key (permission_id) references /*table_prefix*/permission(id)
);



create table /*table_prefix*/plugin (
	id int not null, 
  deleted smallint(1) not null default 0,

	name char(50) not null,
	active tinyint(1) not null,

	primary key (id)
);


create table /*table_prefix*/media (
  id int not null, 
  deleted smallint(1) not null default 0,

  mime_type varchar(255) not null,
  dir varchar(255) not null,
  file varchar(255) not null,
  file_updated_at timestamp null,
  width int,
  height int,

  primary key (id)
);


create table /*table_prefix*/category (
  id int not null, 
  deleted smallint(1) not null default 0,

  parent_id int,
  media_id int,

  primary key (id),
  foreign key (parent_id) references /*table_prefix*/category(id),
  foreign key (media_id) references /*table_prefix*/media(id)
);
create table /*table_prefix*/category_text (
  id int not null,
  text_id int not null,
  locale char(7) not null,

  name varchar(255) not null,

  primary key (id),
  foreign key (text_id) references /*table_prefix*/category(id),
  index (text_id, locale)
);


create table /*table_prefix*/tag (
  id int not null, 
  deleted smallint(1) not null default 0,

  primary key (id)
);
create table /*table_prefix*/tag_text (
  id int not null,
  text_id int not null,
  locale char(7) not null,

  name varchar(255) not null,

  primary key (id),
  foreign key (text_id) references /*table_prefix*/tag(id),
  index (text_id, locale)
);


create table /*table_prefix*/post (
  id int not null,

  status int not null default 0,
  media_id int,
  category_id int,

  primary key (id),
  foreign key (category_id) references /*table_prefix*/category(id)
);
create table /*table_prefix*/post_text (
  id int not null,
  text_id int not null,
  locale char(7) not null,

  title varchar(255) not null,
  description text not null,
  content text not null,

  primary key (id),
  foreign key (text_id) references /*table_prefix*/post(id),
  index (text_id, locale)
);
create table /*table_prefix*/post_tag (
  post_id int not null,
  tag_id int not null,

  primary key (post_id, tag_id),
  foreign key (post_id) references /*table_prefix*/post(id),
  foreign key (tag_id) references /*table_prefix*/tag(id)
);

create table /*table_prefix*/post_archive (
  version int not null,
  id int not null,

  status int not null default 0,
  media_id int,
  category_id int,

  primary key (version, id),
  foreign key (category_id) references /*table_prefix*/category(id)
);
create table /*table_prefix*/post_archive_text (
  version int not null,
  id int not null,
  text_id int not null,
  locale char(7) not null,

  title varchar(255) not null,
  description text not null,
  content text not null,

  primary key (version, id),
  foreign key (text_id) references /*table_prefix*/post_archive(id),
  index (text_id, locale)
);
create table /*table_prefix*/post_archive_tag (
  version int not null,
  post_id int not null,
  tag_id int not null,

  primary key (version, post_id, tag_id),
  foreign key (post_id) references /*table_prefix*/post_archive(id),
  foreign key (tag_id) references /*table_prefix*/tag(id)
);