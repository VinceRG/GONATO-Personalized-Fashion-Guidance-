<?php 
// controllers/featureControl.php

class FeaturesController {

    public function index() {

         // ---------------------------------------
        // 🔐 2. Prevent page from being cached
        //     - Stops Back Button from showing protected pages
        // ---------------------------------------
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: 0");

        // ---------------------------------------
        // 🔐 3. SESSION GUARD
        // ---------------------------------------
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            // user not logged in → redirect to login
            header("Location: index.php?page=login");
            exit;
        }

        // ---------------------------------------
        // 🔐 4. Optional: Validate IP + User-Agent (extra protection)
        // ---------------------------------------
        if (isset($_SESSION['ip_address'], $_SESSION['user_agent'])) {

            $current_ip  = $_SERVER['REMOTE_ADDR'] ?? '';
            $current_ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';

            if ($_SESSION['ip_address'] !== $current_ip ||
                $_SESSION['user_agent'] !== $current_ua) {

                // Mismatch → force logout (possible session hijack)
                session_unset();
                session_destroy();
                header("Location: index.php?page=login");
                exit;
            }
        }

        // ---------------------------------------
        // 🔐 5. Optional: Idle timeout (15 mins)
        // ---------------------------------------
        if (isset($_SESSION['last_activity']) 
            && time() - $_SESSION['last_activity'] > 900) {

            session_unset();
            session_destroy();
            header("Location: index.php?page=login&timeout=1");
            exit;
        }

        // Refresh last activity timestamp
        $_SESSION['last_activity'] = time();

        // ---------------------------------------
        // 6. Load Model + View
        // ---------------------------------------
        require_once __DIR__ . '/../Model/features.php';
        require_once __DIR__ . '/../View/system_features.php';
    }
}

?>
