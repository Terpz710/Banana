-- #!sqlite

-- #{ table
    -- #{ cooldowns
        CREATE TABLE IF NOT EXISTS cooldowns (
            username TEXT NOT NULL PRIMARY KEY,
            last_claim INTEGER NOT NULL
        );
    -- #}
-- #}

-- #{ cooldowns
    -- #{ set
        -- # :username string
        -- # :last_claim int
        INSERT INTO cooldowns (username, last_claim)
        VALUES (:username, :last_claim)
        ON CONFLICT(username) DO UPDATE SET last_claim = excluded.last_claim;
    -- #}

    -- #{ get
        -- # :username string
        SELECT last_claim FROM cooldowns WHERE username = :username;
    -- #}

    -- #{ remove
        -- # :username string
        DELETE FROM cooldowns WHERE username = :username;
    -- #}

    -- #{ cleanup
        -- # :time int
        DELETE FROM cooldowns WHERE last_claim <= :time;
    -- #}
-- #}