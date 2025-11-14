<?php
// GONATO-Personalized-Fashion-Guidance-/Model/User.php
require_once __DIR__ . '/../Core/Database.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Register a new user
     * 
     * @param string $firstname
     * @param string $lastname
     * @param string $username
     * @param string $email
     * @param array $addressData Array containing address fields
     * @param string $contact_num
     * @param string $password
     * @return bool
     */
    public function register($firstname, $lastname, $username, $email, $addressData, $contact_num, $password) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Convert address array to JSON string
        $addressJson = json_encode($addressData, JSON_UNESCAPED_UNICODE);
        
        // Prepare SQL statement
        $stmt = $this->conn->prepare("
            INSERT INTO users (FIRST_NAME, LAST_NAME, USERNAME, EMAIL, ADDRESS, CONTACTS, PASSWORD, CREATED_AT)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        // Bind parameters
        $stmt->bind_param("sssssss", 
            $firstname, 
            $lastname, 
            $username, 
            $email, 
            $addressJson,  // Store as JSON
            $contact_num, 
            $hashedPassword
        );
        
        // Execute and return result
        return $stmt->execute();
    }

    /**
     * Get user address as array
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserAddress($userId) {
        $stmt = $this->conn->prepare("SELECT ADDRESS FROM users WHERE USER_ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Decode JSON address back to array
            return json_decode($row['ADDRESS'], true);
        }
        
        return null;
    }

    /**
     * Update user address
     * 
     * @param int $userId
     * @param array $addressData
     * @return bool
     */
    public function updateAddress($userId, $addressData) {
        $addressJson = json_encode($addressData, JSON_UNESCAPED_UNICODE);
        
        $stmt = $this->conn->prepare("UPDATE users SET ADDRESS = ? WHERE USER_ID = ?");
        $stmt->bind_param("si", $addressJson, $userId);
        
        return $stmt->execute();
    }

    /**
     * Get formatted address string for display
     * 
     * @param int $userId
     * @return string
     */
    public function getFormattedAddress($userId) {
        $address = $this->getUserAddress($userId);
        
        if (!$address) {
            return 'No address on file';
        }
        
        // Build formatted address string
        $parts = [];
        
        if (!empty($address['street_address'])) {
            $parts[] = $address['street_address'];
        }
        
        if (!empty($address['apartment'])) {
            $parts[] = $address['apartment'];
        }
        
        if (!empty($address['barangay'])) {
            $parts[] = 'Brgy. ' . $address['barangay'];
        }
        
        if (!empty($address['city'])) {
            $parts[] = $address['city'];
        }
        
        if (!empty($address['province'])) {
            $parts[] = $address['province'];
        }
        
        if (!empty($address['postal_code'])) {
            $parts[] = $address['postal_code'];
        }
        
        return implode(', ', $parts);
    }
}
?>