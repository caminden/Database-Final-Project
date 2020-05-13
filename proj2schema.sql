drop table users cascade constraints;
drop table myclientsession cascade constraints;
drop table myclient cascade constraints;
drop table students cascade constraints;
drop table section cascade constraints;
drop table address cascade constraints;
drop table enrolledIn cascade constraints;

create table myclient (
	clientid varchar2(8) primary key,
	password varchar2(12)
);

create table myclientsession (
	sessionid varchar2(32) primary key,
	clientid varchar2(8),
	sessiondate date,
	foreign key (clientid) references myclient
);

create table users (
	userID number(10) primary key,
	clientid varchar2(8),
	aFlag number(1),
	sFlag number(1),
	foreign key (clientid) references myclient
);

create table address (
        addressCode number(12) primary key,
        city varchar(12),
        state varchar(2),
        zip number(5)
);

create table students (
	Sid varchar2(8) primary key,
	userID number(10),
	fname varchar(10),
	lname varchar(10),
	age number(2),
	studentTypeFlag number(1),
	statusFlag number(1),
	addressCode number(12),
	gpa number(2, 1),
	foreign key (userID) references users,
	foreign key (addressCode) references address
);

create table section (
	crn number(5) primary key,
	sectionId varchar2(8), 
	title varchar(15),
	semester varchar(2),
	year number(4),
	credits number(1),
	seats number(2),
	description varchar(64),
	time varchar2(15),
	prereq number(5),
	foreign key (prereq) references section (crn)
);

create table enrolledIn (
	Sid varchar2(8),
	crn number(5),
	grade number(2, 1),
	primary key (Sid, crn),
	foreign key (Sid) references students,
	foreign key (crn) references section
);

create or replace view crnCount as
	select count(*) crncount, Sid
	from enrolledIn e1 join section s1 on e1.crn=s1.crn
	where year > 2019
	group by Sid;

CREATE OR REPLACE VIEW enrolled as
	select count(*) enrolledCount, e1.crn
	from enrolledIn e1 join section s1 on e1.crn=s1.crn
	group by e1.crn;


create or replace procedure Enroll (a in varchar2, z in varchar, y in number, x out varchar2) as
         maxSeats number;
         seatsLeft number;
         seatsTaken number;
         deadline varchar2(12) := z; 
begin 
	if to_date(deadline, 'dd-mm-yyyy') > sysdate then
                select seats into maxSeats from section where crn=y for update;
                select count(*) into seatsTaken from enrolledIn where crn=y;
                if seatsTaken = null then seatsTaken := 0;
                end if;
                if maxSeats-seatsTaken > 0 then
                        insert into enrolledIn values(a, y, 0.0);
                        commit;
			x := 'Enroll Success';
                else
                        x := 'No seats left';
                        rollback;
                end if;
        else
	x := 'Past date';
        dbms_output.put_line('Past date');
        end if;
end;
/

create or replace procedure GPA as
        cursor c1 is select Sid from students where Sid in (select Sid from enrolledIn);
        stuID students.Sid%type;
        cursor c2(id students.Sid%type) is
                select grade, credits from enrolledIn e1 join section s1 on e1.crn=s1.crn
                where Sid = id and year < 2020
                or (Sid = id and year = 2020 and semester = 'Sp');
        outGrade number := 0;
        temp number;
        temp2 number;
        creditTotal number := 0;
        newgpa number;
        oldgpa number;
begin
        open c1;
        loop
                exit when c1%NOTFOUND;
                fetch c1 into stuID;
                open c2(stuID);
                loop
                        exit when c2%NOTFOUND;
                        fetch c2 into temp, temp2;
                        outGrade := outGrade + temp * temp2;
                        creditTotal := creditTotal + temp2;
                end loop;
                if creditTotal > 0 then
                newgpa := outGrade/creditTotal;
                else
                newgpa := 0.0;
                end if;
                select gpa into oldgpa from students where Sid = stuID for update;
                update students set gpa = newgpa where Sid = stuID;
                commit;
                close c2;
                creditTotal := 0;
                outGrade := 0;
        end loop;
        close c1;
end;
/

create or replace trigger probation
after update of gpa on students
for each row
declare
        stuId students.Sid%TYPE;
        newGrade students.gpa%TYPE;

begin
        dbms_output.put_line('Trigger called');
        stuId := :new.Sid;
	newGrade := :new.gpa;
	if newGrade >= 2 then
                dbms_output.put_line('Okay');
        end if;
        if newGrade < 2 then
        	dbms_output.put_line('not okay');
	end if;
end;
/

insert into section values(25323, 'CMS1002', 'Programming1', 'Sp', 2018, 3, 23, 'Basics of programming', 'MWF1-2', null);
insert into section values(20568, 'CMS3034', 'DataStruct', 'Fa', 2019, 3, 24, 'Datastructure design', 'MW1:45-3', null);
insert into section values(20991, 'CMS4003', 'Database', 'Sp', 2020, 3, 27, 'Database sytstem management', 'MWF4-5', 20568);
insert into section values(23472, 'MA4101', 'Linear Alg', 'Fa', 2020, 3, 1, 'Matrix algebra and manipulation', 'TTh7:30-8:45', null);
insert into section values(24083, 'CMS4865', 'NwSecurity', 'Sp', 2021, 3, 16, 'Network and internet security', 'TTh2:15-3:30', 20991);
insert into section values(12052, 'GE1250', 'FYStudy', 'Su', 2020, 2, 30, 'First year Study', 'MWF9-10', null);

insert into myclient values ('chase', 'gq050');
insert into users values(456, 'chase', 1, 1);
insert into address values(3502, 'Marlow', 'OK', 73055);
insert into students values('cm204699', 456, 'Chase', 'Minden', 21, 0, 1, 3502, 0);

insert into myclient values('test', 'test');
insert into users values(456852, 'test', 0, 1);
insert into address values(7321, 'City', 'ST', 74321);
insert into students values('tc230563', 456852, 'Test', 'Case', 22, 0, 1, 7321, 0);

insert into enrolledIn values('cm204699', 25323, 4.0);
insert into enrolledIn values('cm204699', 20991, 3.9);
insert into enrolledIn values('cm204699', 20568, 3.6);
insert into enrolledIn values('tc230563', 20991, 3.8);

insert into myclient values('a', 'a');
insert into users values(123, 'a', 1, 0);

commit;
