CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    github_id INTEGER UNIQUE NULL,
    username VARCHAR(50) UNIQUE NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NULL,
    avatar VARCHAR(255) UNIQUE NULL,
    ip VARCHAR(255) NULL,
    token VARCHAR(255) UNIQUE NULL, 
    state VARCHAR(255) UNIQUE NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TYPE valid_position AS ENUM ('left', 'right');

CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    sender_id INT REFERENCES users(id),
    receiver_id INT REFERENCES users(id),
    content TEXT NOT NULL,
    position valid_position NOT NULL DEFAULT 'left',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS participants (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create or replace function NotifyOnDataChange()
   returns trigger 
   language plpgsql
as
$body$
declare 
    data json;
    notification json;
    m_mess text;
    m_sender_id INT;
    rec record;
begin
    -- rec = NEW;
    -- PERFORM pg_notify('datachange', row_to_json(rec));
    -- RETURN NEW;
    rec = new;
    select content,sender_id
    into m_mess, m_sender_id
    from messages 
    where id = rec.id;
    
    data = row_to_json(rec);
    notification = json_build_object('data', data);
    perform pg_notify('datachange', notification::text);
    return rec;
end;
$body$;

CREATE TRIGGER "OnDataChange"
  AFTER INSERT
  ON "messages"
  FOR EACH ROW
  EXECUTE PROCEDURE NotifyOnDataChange();