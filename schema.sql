create table if not exists times(
  id bigint(20) NOT NULL auto_increment,
  start datetime NOT NULL,
  end datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Yksityistunnit
create table if not exists enrollments(
  id bigint(20) NOT NULL auto_increment,
  start datetime NOT NULL,
  end datetime NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  phone varchar(255),
  comment varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=InnoDB;

create table if not exists auth(
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (username)
) ENGINE=InnoDB;

create table if not exists auth_token(
  token varchar(50) NOT NULL,
  valid_until datetime NOT NULL,
  PRIMARY KEY (token)
) ENGINE=InnoDB;


create table if not exists group_class(
  id bigint(20) NOT NULL auto_increment,
  start datetime NOT NULL,
  end datetime,
  day varchar(2) NOT NULL,
  start_time time NOT NULL,
  end_time time NOT NULL,
  name varchar(255) NOT NULL,
  max_size int NOT NULL,
  is_saturday_miniretreat bool NOT NULL,
  anchor varchar(255),
  PRIMARY KEY (id)
) ENGINE=InnoDB;

create index miniretreat_index on group_class(start, is_saturday_miniretreat);

create table if not exists cancelled_class(
  group_class_id bigint(20) NOT NULL,
  when date NOT NULL,
  reason varchar(255) NOT NULL,
  PRIMARY KEY (when, group_class_id),
  FOREIGN KEY (group_class_id) REFERENCES group_class(id) ON DELETE CASCADE
) ENGINE=InnoDB;

create table if not exists regular_client(
  email varchar(255) NOT NULL,
  group_class_id bigint(20) NOT NULL,
  PRIMARY KEY (email, group_class_id),
  FOREIGN KEY (group_class_id) REFERENCES group_class(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Ryhmätuntivaraukset
create table if not exists booking(
  email varchar(255) NOT NULL,
  group_class_id bigint(20) NOT NULL,
  when date NOT NULL,
  phone varchar(255),
  PRIMARY KEY (email, group_class_id, when),
  FOREIGN KEY (group_class_id) REFERENCES group_class(id) ON DELETE CASCADE
) ENGINE=InnoDB;

create table if not exists cancelled_regular(
  email varchar(255) NOT NULL,
  group_class_id bigint(20) NOT NULL,
  when date NOT NULL,
  PRIMARY KEY (email, group_class_id, when),
  FOREIGN KEY (group_class_id) REFERENCES group_class(id) ON DELETE CASCADE
) ENGINE=InnoDB;
