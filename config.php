function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // PostgreSQL DSN with SSL
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Set timezone
            $pdo->exec("SET TIME ZONE 'UTC'");
            
        } catch(PDOException $e) {
            // Log error and show detailed message
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}
