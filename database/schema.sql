CREATE TABLE users (
	id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name VARCHAR(120) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL,
	created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
	id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name VARCHAR(120) NOT NULL,
	description TEXT,
	price_per_night NUMERIC(12, 2) NOT NULL CHECK (price_per_night >= 0),
	capacity INTEGER NOT NULL CHECK (capacity > 0),
	created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
	id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	user_id BIGINT NOT NULL REFERENCES users(id),
	room_id BIGINT NOT NULL REFERENCES rooms(id),
	check_in DATE NOT NULL,
	check_out DATE NOT NULL,
	guests INTEGER NOT NULL CHECK (guests > 0),
	status VARCHAR(20) NOT NULL DEFAULT 'pending'
		CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed')),
	created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CHECK (check_out > check_in)
);

CREATE INDEX bookings_user_id_idx ON bookings(user_id);
CREATE INDEX bookings_room_dates_idx ON bookings(room_id, check_in, check_out);
