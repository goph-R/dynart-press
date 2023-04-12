create table `user` (
  id int not null auto_increment,
  email not null varchar(255) not null,
  password not null varchar(255) not null,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by int not null,
  updated_at timestamp null,
  updated_by int null,
);

create table media (
  id int not null auto_increment,
  width int not null,
  height int not null,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by int not null,
  updated_at timestamp null,
  updated_by int null,
  path_updated_at timestamp null,
  dir VARCHAR(255) NOT NULL,
  path varchar(255) not null,
  title varchar(255) not null,
  primary key (id),
  FOREIGN KEY (created_by) REFERENCES `user`(id),
  FOREIGN KEY (updated_by) REFERENCES `user`(id)
);

