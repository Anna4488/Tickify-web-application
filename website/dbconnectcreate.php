<?php
    try{
        $username = getenv("POSTGRES_USER");
        $password = getenv("POSTGRES_PASSWORD");
        $db_name = getenv("POSTGRES_DB");
        $port = 5432;
        $dsn = "pgsql:host=db;port=$port;dbname=$db_name";
        $db = new PDO($dsn, $username, $password);
    }catch(PDOException $e){
        echo "Connection failed";
    }

    $drop_all_tables = '
        DROP TABLE IF EXISTS line_up CASCADE;
        DROP TABLE IF EXISTS login CASCADE;
        DROP TABLE IF EXISTS payment_details CASCADE;
        DROP TABLE IF EXISTS ticket CASCADE;
        DROP TABLE IF EXISTS userRole CASCADE;
        DROP TABLE IF EXISTS accountaddress CASCADE;
        DROP TABLE IF EXISTS eventaddress CASCADE;
        DROP TABLE IF EXISTS events CASCADE;
        DROP TABLE IF EXISTS account CASCADE;
    ';
    $db->exec($drop_all_tables);
    echo 'Dropped all tables<br><br>';

    // Table creation
    try {
        $account = 'CREATE TABLE IF NOT EXISTS account(
                    email VARCHAR(50) PRIMARY KEY,          
                    userPassword VARCHAR(100),
                    firstName VARCHAR(15),
                    lastName VARCHAR(15),
                    birthdate DATE)';
        $db->exec($account);
        echo 'account created<br>';

        $userRole = 'CREATE TABLE IF NOT EXISTS userRole(
                    restriction VARCHAR(10),
                    accMail VARCHAR(50) PRIMARY KEY)';
        $db->exec($userRole);
        echo 'userRole created<br>';

        $accountaddress = 'CREATE TABLE IF NOT EXISTS accountaddress(
                    country varchar(30),                    
                    city varchar(30),
                    zipcode varchar(8),
                    street varchar(30),
                    housenum int,                                /* the housnumber of the account*/
                    accMail varchar(50) PRIMARY KEY)';       /* the email of the account the adress belong to*/
        echo 'account address created<br>';

        $events = 'CREATE TABLE IF NOT EXISTS events(
                    enum serial PRIMARY key,                /* the event number that is used to identify the enevent within the website */
                    dates date not null,                    /* the date on with the event starts */
                    names varchar(30) not null,                /* the name of the event*/
                    descriptions varchar(1000) not null,    /* a description of the event at contains at max 1000 characters*/
                    pegi int,                               /* the age restriction of the event*/
                    capacity int,                           /* the maximum amount of people that can atent the event*/
                    duration int,
                    accMail  varchar (50))';                     /* the time in day that the event lasts*/
        echo 'events created<br>';

        $eventaddress = 'CREATE TABLE IF NOT EXISTS eventaddress(
            country varchar(30),                    
            city varchar(30),
            zipcode varchar(8),
            street varchar(30),
            housenum int,                                /* the housnumber of the account*/
            enum int PRIMARY KEY)';       /* the email of the account the adress belong to*/
        echo 'event address created<br>';

        $ticket = 'CREATE TABLE IF NOT EXISTS ticket(
                    num SERIAL,
                    enum INT,
                    types VARCHAR(10), 
                    price INT,
                    accMail VARCHAR(50),
                    PRIMARY KEY (num, enum))';
        $db->exec($ticket);
        echo 'ticket created<br>';

        $payment_details = 'CREATE TABLE IF NOT EXISTS payment_details(
                    accMail varchar(50) PRIMARY KEY,
                    credentials varchar(60))';
        echo 'payment_details created<br>';
        echo '<br>Statements confirmed ' . "\u{1F44D}";
        echo '<br><br><br>';
    } catch (PDOException $e){
        echo '<br>Error creating SQL-Statements ' . "\u{1F615}";
        echo '<br><br><br>';
    }
//---------------------------------------------------------
    try {
        $db->exec($account);
        echo 'account table created<br>';
        $db->exec($userRole);
        echo 'userRole table created<br>';
        $db->exec($accountaddress);
        echo 'account address table created<br>';
        /*$db->exec($Line_up);
        echo 'Line_up table created<br>';*/
        $db->exec($events);
        echo 'events table created<br>';
        $db->exec($eventaddress);
        echo 'event address table created<br>';
        $db->exec($ticket);
        echo 'ticket table created<br>';
        $db->exec($payment_details);

        echo 'Tables created successfully<br><br>';

// Alter tables

        $alter_accountaddress = 'ALTER TABLE accountaddress
                    add constraint foreignKey_accountaddress
                    foreign key (accMail) references Account (email)';

        $db->exec($alter_accountaddress);
        echo 'altered account address<br>';
    //------------------------------------------------------------
        $alter_ticket_foreignKey1 = 'ALTER TABLE ticket
                    add constraint foreignKey_ticket1
                    foreign key (accMail) references Account (email)';

        $db->exec($alter_ticket_foreignKey1);
        echo 'altered ticket foreignKey1<br>';
    //------------------------------------------------------------
        $alter_ticket_foreignKey2 = 'ALTER TABLE ticket
                    add constraint foreignKey_ticket2
                    foreign key (enum) references events (enum)';

        $db->exec($alter_ticket_foreignKey2);
        echo 'altered ticket foreignKey2<br>';
    //------------------------------------------------------------    
        $alter_ticket_price = 'ALTER TABLE ticket
                    add constraint checkPricePositive_events
                    check (price>0)';

        $db->exec($alter_ticket_price);
        echo 'altered ticket price<br>';
    //------------------------------------------------------------
        $alter_payment_details = 'ALTER TABLE payment_details
                    add constraint foreignKey_paymentDetails
                    foreign key (accMail) references Account (email)';

        $db->exec($alter_payment_details);
        echo 'altered payment_details<br>';
    //------------------------------------------------------------
        /*$alter_line_up_foreignKey = 'ALTER TABLE line_up
                    add constraint foreignKey_lineUp
                    foreign key (accMail) references Account (email)';

        $db->exec($alter_line_up_foreignKey);
        echo 'altered line_up foreignKey<br>';
    //------------------------------------------------------------
        $alter_lineUp_hasArtists = 'ALTER TABLE line_up
                    add constraint hasArtist_event
                    check (exists(select 1 from line_up where lineup.enum = events.enum))';

        $db->exec($alter_lineUp_hasArtists);
        echo ' altered line_up hasArtists<br>';*/
    //------------------------------------------------------------
        $alter_event_pegi = 'ALTER TABLE events
                    add constraint checkPegiPositive_events
                    check (pegi >0)';

        $db->exec($alter_event_pegi);
        echo 'altered events pegi<br>';
    //------------------------------------------------------------
        $alter_event_capacity = 'ALTER TABLE events
                    add constraint checkCapacityPositive_events
                    check (capacity>0)';

        $db->exec($alter_event_capacity);
        echo 'altered events capacity<br>';
    //------------------------------------------------------------
        $alter_events_foreignKey = 'ALTER TABLE events
                    add constraint foreignKey_events
                    foreign key (accMail) references Account (email)';

        $db->exec($alter_events_foreignKey);
        echo 'altered event foreignKey<br>';
    //------------------------------------------------------------
        $alter_eventaddress = 'ALTER TABLE eventaddress
                    add constraint foreignKey_eventaddress
                    foreign key (enum) references events (enum)';

        $db->exec($alter_eventaddress);
        echo 'altered event address<br>';
    //------------------------------------------------------------
        $alter_account = 'ALTER TABLE account
                    add constraint uniqueEmail_account
                    unique(email)';

        $db->exec($alter_account);
        echo 'altered accountMail<br>';
        echo '<br>iterations confirmed ' . "\u{1F44D}<br><br><br>";
        
//---------------------------------------------------------
    } catch (PDOException $e) {
        echo '<br>Error altering tables ' . "\u{1F615}";
    }

    // Add data to tables
    try {
        /* Insert into account
        $db->exec("INSERT INTO account VALUES 
            ('john.doe@example.com', 'password123', 'John', 'Doe', '2000-05-20'),
            ('jane.smith@example.com', 'pass456', 'Jane', 'Smith', '1995-08-15')");
        echo 'Inserted into account<br>';
        the password is added only as a text*/

    //Account Table
    $psw = password_hash('12345678', PASSWORD_DEFAULT);
    $insertAccount = $db->prepare("INSERT INTO Account (email, userPassword, firstName, lastName, birthdate) 
                      VALUES('user1@example.com', :psw, 'John', 'Doe', '10.10.2020'),
                            ('user2@example.com', :psw, 'Julia', 'Roberts', '09.08.2000'), /*has no restriction to test login checks*/
                            ('user3@example.com', :psw, 'Peter', 'Smith', '11.11.2011'),
                            ('user4@example.com', :psw, 'Hannibal', 'Lecter', '12.06.1998'), /*has no event or ticket so delete this if need to try it out*/
                            ('user5@example.com', :psw, 'Lisa', 'Cuddy', '11.11.2011'),
                            ('admin1@example.com', :psw, 'Sarah', 'Forest', '05.06.2002');");
    $insertAccount->bindParam(':psw', $psw);
    $insertAccount->execute();
    echo 'Inserted data to account table<br>';

    //AccountAddress table
    $insertAccountAddress = "
        INSERT INTO accountaddress(country, city, zipcode, street, housenum, accmail) VALUES
        ('Belgium', 'Blankenberge', '8370', 'Zeedijk', 127, 'user1@example.com'),
        ('Germany', 'Bad Sooden-Allendorf', '37242', 'Hauptstrasse ', 334, 'user2@example.com'),
        ('Greece', 'Acharnes', '136 71', 'Landstrasse', 1, 'user3@example.com'),
        ('France', 'Grenoble', '38000', 'Rue Amiral Courbet', 1, 'user4@example.com'),
        ('Netherlands', 'Eindhoven', '5625 AA', 'Winkelcentrum Woensel', 15, 'user5@example.com'),
        ('Austria', 'Linz', '4020', 'Hauptplatz', 8, 'admin1@example.com');
        ";
    $db->exec($insertAccountAddress);
    echo 'Inserted data into event address<br>';

    //userRole Table
    $insertUserRole = "
        INSERT INTO userRole (restriction, accMail) VALUES
        ('customer', 'user1@example.com'),
        ('customer', 'user3@example.com'),
        ('customer', 'user4@example.com'),
        ('customer', 'user5@example.com'),
        ('admin', 'admin1@example.com');";
    $db->exec($insertUserRole);
    echo 'Inserted data into userRole table<br>';

    // Event Table
    $insertEvent = "
        INSERT INTO events (dates, names, descriptions, pegi, capacity, duration, accmail) VALUES
        ('2025-01-20', 'Harmony Haven', 'Harmony Haven is the ultimate 5-day musical journey, set in a picturesque lakeside venue. Featuring a diverse lineup of world-renowned artists from rock, pop, electronic, and indie genres, its a celebration of sound and culture. Attendees can enjoy camping, gourmet food trucks, interactive art installations, and wellness activities like yoga and meditation. With its blend of breathtaking landscapes and electrifying performances, Harmony Haven is the perfect escape for music lovers seeking both relaxation and excitement.', 18, 2, 3, 'user3@example.com'),
        ('2025-01-28', 'BeatFest', 'BeatFest is a thrilling 3-day urban music festival located in the heart of the city. Showcasing top-tier DJs, hip-hop artists, and indie bands, its a non-stop party for music enthusiasts. The festival offers multiple stages, food and drink vendors, immersive light shows, and pop-up art exhibits. BeatFest is known for its vibrant atmosphere and community spirit, making it the go-to destination for those looking to dance the night away and experience the best of urban music and culture.', 12, 300, 5, 'user3@example.com'),
        ('2025-08-11', 'Sound Festival', 'Sound Festival is an annual celebration of music and arts, bringing together diverse artists and audiences for a vibrant, multi-day experience. The festival features live performances, interactive workshops, and immersive installations, creating a dynamic atmosphere of creativity and community.', 16, 400, 3, 'user3@example.com'),
        ('2025-10-05', 'Luminara Festival', 'A celebration of light and color, Luminara Festival transforms the city into a glowing wonderland with dazzling light installations, lantern parades, and interactive art displays. Attendees can enjoy live music, food stalls, and workshops on creating their own luminous art.', 6, 200, 7, 'user5@example.com'),
        ('2025-03-21', 'Echoes of Earth', 'This eco-friendly festival focuses on sustainability and nature, featuring performances by artists who use natural instruments and sounds. Workshops on environmental conservation, organic food markets, and nature walks make it a holistic experience for all ages.', 12, 300, 2, 'user5@example.com'),
        ('2025-04-11', 'Fusion Fiesta', 'A vibrant celebration of cultural diversity, Fusion Fiesta showcases music, dance, and cuisine from around the world. Attendees can participate in dance workshops, taste international dishes, and enjoy performances that blend different cultural traditions into a harmonious fusion.', 6, 500, 6, 'user5@example.com'), 
        ('2025-06-24', 'Rhythm Oasis', 'Set in a desert landscape, Rhythm Oasis features a mix of world music and electronic beats, creating a unique fusion of sounds. The festival includes art installations and fire performances, adding to the mystical atmosphere.', 18, 2000, 5, 'user3@example.com');
    ";
    $db->exec($insertEvent);
    echo 'Inserted data into events table<br>';

    //Event address table
    $insertEventAddress = "
        INSERT INTO eventaddress (country, city, zipcode, street, housenum, enum) VALUES
        ('Netherlands', 'Assen', '9401 BJ', 'Gilbert St', 4, 1),
        ('United Kingdom', 'Hounslow', 'TW3 1QS', 'Groningerstraat', 59, 2),
        ('Germany', 'Bad Arolsen', '34454', 'Ostpreussenstrasse ', 66, 3),
        ('Finland', 'Helsinki', '00190', 'Suomenlinna', 9, 4),
        ('Italy', 'Padova', '35126', 'Via Pier Paolo Vergerio', 14, 5),
        ('Ireland', 'Dublin', 'D02 XW25', 'College Green', 12, 6),
        ('Belgium', 'Rumbeke', '8800', 'Regenbeekstraat', 36, 7);
    ";
    $db->exec($insertEventAddress);
    echo 'Inserted data into event address table<br>';

        /* Insert into events
        $db->exec("INSERT INTO events (enum,dates, names, descriptions, pegi, capacity, duration) VALUES 
            (1,'2024-10-01', 'Summer Fest', 'A fun summer music festival.', 18, 5000, 3),
            (2,'2024-11-15', 'Tech Expo', 'Explore the latest in tech.', 12, 2000, 2)");
        echo 'Inserted into events<br>';
        not working*/

    // Ticket Table
    $insertTicket = "
        INSERT INTO ticket (enum, types, price, accMail) VALUES
        ('1', 'VIP', 100, 'user1@example.com'),
        ('1', 'Regular', 50, 'user1@example.com'),
        ('6', 'VIP', 100, 'user1@example.com'),
        ('4', 'Regular', 50, 'user1@example.com'),
        ('2', 'VIP', 100, 'user2@example.com'),
        ('4', 'Regular', 50, 'user2@example.com'),
        ('3', 'Regular', 50, 'user2@example.com'),
        ('3', 'VIP', 100, 'user3@example.com');
    ";
    $db->exec($insertTicket);
    echo 'Inserted data into ticket table<br>';

        /* Insert into payment_details
        $db->exec("INSERT INTO payment_details VALUES 
            ('john.doe@example.com', 'VISA-1234-5678'),
            ('jane.smith@example.com', 'MasterCard-9876-5432')");
        echo 'Inserted into payment_details<br>';
        */

    } catch (PDOException $e) {
        echo '<br>Error inserting data: ' . $e->getMessage();
    }

    echo '<br>All data inserted successfully ' . "\u{1F44D}";
//-------------------------------------------------------------------------------------------------------------------------------
//Triggers for the website

    try {
        //maximum 10 tickets per event for one account
        $function10tickets = "
            CREATE OR REPLACE FUNCTION max_event_ticket()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (SELECT COUNT(*) FROM ticket WHERE accmail = NEW.accmail AND enum = NEW.enum) >= 10 THEN
                    RAISE EXCEPTION 'Ticket purchase limit exceeded for this account (max 10 tickets per event).';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ";

        $trigger10tickets = "
            CREATE OR REPLACE TRIGGER max_event_ticket
            BEFORE INSERT OR UPDATE ON ticket
            FOR EACH ROW EXECUTE function max_event_ticket();
       ";

       $db->exec($function10tickets);
       $db->exec($trigger10tickets);
       echo '<br>max_event_ticket trigger created<br>';

    } catch (PDOException $e) {
        echo '<br>Error creating triggers: ' . $e->getMessage();
    }

    //one account one email
    try {
        $functionemailcheck = "
            CREATE OR REPLACE FUNCTION individual_email()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (SELECT email FROM account where email = NEW.email) THEN
                    RAISE EXCEPTION 'Email already belongs to an account.';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ";

        $triggeremailcheck = "
            CREATE OR REPLACE TRIGGER individual_email
            BEFORE INSERT OR UPDATE ON account
            FOR EACH ROW EXECUTE FUNCTION individual_email();
        ";

       $db->exec($functionemailcheck);
       $db->exec($triggeremailcheck);
       echo '<br>individual_email trigger created<br>';

    } catch (PDOException $e) {
        echo '<br>Error creating triggers: ' . $e->getMessage();
    }

    //event capacity cant be negative
    try {
        $functioncapacity = "
            CREATE OR REPLACE FUNCTION positive_capacity()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (NEW.capacity < 0) THEN
                    RAISE EXCEPTION 'Capacity needs to be positive or zero';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ";

        $triggercapacity = "
            CREATE OR REPLACE TRIGGER positive_capacity
            BEFORE INSERT OR UPDATE ON events
            FOR EACH ROW EXECUTE FUNCTION positive_capacity();
        ";
        
        $db->exec($functioncapacity);
        $db->exec($triggercapacity);
        echo '<br>positive_capacity trigger created<br>';
        
    } catch (PDOException $e) {
        echo '<br>Error creating triggers: ' . $e->getMessage();
    }
?>
