create table times(
  id bigint(20) NOT NULL auto_increment,
  start datetime NOT NULL,
  end datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

create table enrollments(
  id bigint(20) NOT NULL auto_increment,
  start datetime NOT NULL,
  end datetime NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  phone varchar(255),
  comment varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=InnoDB;

create table auth(
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (username)
) ENGINE=InnoDB;

create table auth_token(
  token varchar(50) NOT NULL,
  valid_until datetime NOT NULL,
  PRIMARY KEY (token)
) ENGINE=InnoDB;

insert into auth(username, password) values ('stefi', '$2y$10$2ZA9poID5hD0.qxqTwfy4euH5qQM0Nt2Y/p6fS4urw8/ySwbpdtiO');
