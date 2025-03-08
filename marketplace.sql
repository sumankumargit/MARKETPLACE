-- ========================
--  USERS TABLE
-- ========================
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'),
    phone VARCHAR(20) UNIQUE CHECK (phone ~ '^[0-9]{10,15}$'),
    password_hash TEXT NOT NULL,
    user_type VARCHAR(10) CHECK (user_type IN ('poster', 'bidder')), -- Define role
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
--  JOBS TABLE
-- ========================
CREATE TABLE jobs (
    job_id SERIAL PRIMARY KEY,
    poster_id INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT CHECK (LENGTH(description) <= 16384),
    requirements TEXT CHECK (LENGTH(requirements) <= 16384),
    expiration TIMESTAMP NOT NULL CHECK (expiration > CURRENT_TIMESTAMP),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_closed BOOLEAN DEFAULT FALSE -- Tracks if auction is closed
);

-- INDEX FOR FAST SEARCH
CREATE INDEX idx_jobs_expiration ON jobs (expiration);
CREATE INDEX idx_jobs_poster ON jobs (poster_id);

-- ========================
--  BIDS TABLE
-- ========================
CREATE TABLE bids (
    bid_id SERIAL PRIMARY KEY,
    job_id INT NOT NULL REFERENCES jobs(job_id) ON DELETE CASCADE,
    bidder_id INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    bid_amount DECIMAL(10,2) NOT NULL CHECK (bid_amount > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INDEX FOR FAST RETRIEVAL
CREATE INDEX idx_bids_job ON bids (job_id);
CREATE INDEX idx_bids_bidder ON bids (bidder_id);
CREATE INDEX idx_bids_amount ON bids (bid_amount);

-- ========================
--  JOB WINNERS TABLE
-- ========================
CREATE TABLE job_winners (
    job_id INT PRIMARY KEY REFERENCES jobs(job_id) ON DELETE CASCADE,
    winner_id INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    winning_bid DECIMAL(10,2) NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
-- FUNCTION TO CLOSE AUCTIONS & SELECT WINNERS
-- ========================
CREATE OR REPLACE FUNCTION close_expired_jobs()
RETURNS TRIGGER AS $$
BEGIN
    -- Close the job
    UPDATE jobs 
    SET is_closed = TRUE
    WHERE job_id = NEW.job_id;

    -- Select the lowest bid as the winner
    INSERT INTO job_winners (job_id, winner_id, winning_bid)
    SELECT job_id, bidder_id, bid_amount
    FROM bids
    WHERE job_id = NEW.job_id
    ORDER BY bid_amount ASC
    LIMIT 1;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ========================
-- TRIGGER TO AUTOMATICALLY CLOSE JOB WHEN EXPIRATION REACHED
-- ========================
CREATE TRIGGER trigger_close_jobs
AFTER UPDATE ON jobs
FOR EACH ROW
WHEN (NEW.expiration <= CURRENT_TIMESTAMP AND NEW.is_closed = FALSE)
EXECUTE FUNCTION close_expired_jobs();

-- ========================
-- FUNCTION TO PREVENT BIDDING ON CLOSED JOBS
-- ========================
CREATE OR REPLACE FUNCTION prevent_bidding_on_closed_jobs()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM jobs WHERE job_id = NEW.job_id AND is_closed = TRUE) THEN
        RAISE EXCEPTION 'Bidding is closed for this job';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ========================
-- TRIGGER TO BLOCK BIDDING ON CLOSED JOBS
-- ========================
CREATE TRIGGER trigger_prevent_bidding
BEFORE INSERT ON bids
FOR EACH ROW
EXECUTE FUNCTION prevent_bidding_on_closed_jobs();
