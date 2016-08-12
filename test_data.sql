insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, is_course, anchor, highlight) values (now(), now(), 'mon', '16:45', '18:15', 'Joogarentoutuskurssi Oma Voima', 7, false, true, 'joogakurssi', 'Syyskuussa 2016');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'mon', '18:30', '20:00', 'Lempeän vahvistava dynaaminen jooga', 7, false, 'vahvistava');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'tue', '18:00', '19:30', 'Hidas lempeän vahvistava jooga', 7, false, 'hidas');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'wed', '18:30', '20:00', 'Syvärentouttava avaava jooga', 7, false, 'yin');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'thu', '16:45', '18:15', 'Lempeän vahvistava dynaaminen jooga', 7, false, 'vahvistava');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'thu', '18:30', '20:00', 'Syvärentouttava avaava jooga', 7, false, 'yin');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat, anchor) values (now(), now(), 'fri', '11:00', '12:30', 'Lempeän virtaava ja avaava jooga', 7, false, 'virtaava');

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat) values (now() + interval 2 week, now() + interval 2 week, 'sat', '10:00', '13:00', 'Oma Napa- Kehon ja Mielen Voimakeskus.', 7, true);

insert into group_class (display_start, start, day, start_time, end_time, name, max_size, is_saturday_miniretreat) values (now() + interval 8 week, now() + interval 8 week, 'sat', '11:00', '14:00', 'Naiseuden Ylistys Jooga: Lantio luovuuden lähteenä.', 7, true);

insert into regular_client(email, group_class_id) values ('joni@example.com', 1);
insert into regular_client(email, group_class_id) values ('joni2@example.com', 1);
insert into cancelled_regular(regular_client_id, group_class_id, when_date) values (1, 1, '2016-08-14');
insert into cancelled_regular(regular_client_id, group_class_id, when_date) values (2, 1, '2016-08-21');
insert into cancelled_class values (1, '2016-08-22', 'Syysloma');
