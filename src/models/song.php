<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../helper.php';

class SongHandler
{
    public static function list(int $artistId, int $page, int $perPage = 10): array
    {
        $page = $page ?? max(1, (int)($_GET['p'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetchOne("SELECT COUNT(*) as total FROM musics WHERE artist_id = ?", [$artistId])['total'];
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = Database::fetchAll(
            "SELECT m.* FROM musics m WHERE m.artist_id = ? ORDER BY m.created_at DESC LIMIT? OFFSET ?",
            [$artistId, $perPage, $offset]
        );

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'total' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $lastPage,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
        ];
    }

    public static function listAll(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetchOne("SELECT COUNT(*) as total FROM musics")['total'];
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = Database::fetchAll(
            "SELECT m.*, u.first_name, u.last_name
             FROM musics m
             JOIN artists a ON m.artist_id = a.id
             JOIN users u ON a.user_id = u.id
             ORDER BY m.created_at DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'total' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $lastPage,
            'prev_page' => $page - 1,
            'next_page' => $page + 1,
        ];
    }

    public static function create(array $data): array
    {
        $errors = self::validate($data, null, true);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $pdo = Database::getInstance();

        try {

            $pdo->beginTransaction();

            $role = 'artist';
            $userId = Database::insert(
                "INSERT INTO users(first_name, last_name, email, password, phone, dob, address, gender, role) VALUES(?,?,?,?,?,?,?,?,?)",
                [$data['fname'], $data['lname'], $data['email'], $hashedPassword, $data['mobile'], $data['dob'], $data['address'], $data['gender'], $role]
            );

            Database::insert(
                "INSERT INTO artists(user_id, first_release_year, no_of_album_released) VALUES(?,?,?)",
                [$userId, $data['first_release_year'] ?? null, $data['no_of_album_released'] ?? null]
            );

            $pdo->commit();
        } catch (Exception $e) {
            error_log(
                $e->__toString(),
                3,
                LOG_FILE
            );
            $pdo->rollBack();
            return ['success' => false, 'errors' => ['general' => 'Failed to create artist.']];
        }

        return ['success' => true];
    }

    public static function update(int $id, array $data): array
    {
        $errors = self::validate($data, (int)($data['user_id'] ?? 0) ?: null, false);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();
            $userId = $data['user_id'];
            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
                Database::query(
                    "UPDATE users SET first_name=?, last_name=?, email=?, password=?, phone=?, dob=?, address=?, gender=? WHERE id=?",
                    [$data['fname'], $data['lname'], $data['email'], $hashedPassword, $data['mobile'], $data['dob'], $data['address'], $data['gender'], $userId]
                );
            } else {
                Database::query(
                    "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, dob=?, address=?, gender=? WHERE id=?",
                    [$data['fname'], $data['lname'], $data['email'], $data['mobile'], $data['dob'], $data['address'], $data['gender'], $userId]
                );
            }

            Database::query(
                "UPDATE artists SET first_release_year=?, no_of_album_released=? WHERE id=?",
                [$data['first_release_year'] ?? null, $data['no_of_album_released'] ?? null, $id]
            );

            $pdo->commit();
        } catch (Exception $e) {
            error_log(
                $e->__toString(),
                3,
                LOG_FILE
            );
            $pdo->rollBack();
            return ['success' => false, 'errors' => ['general' => 'Failed to update artist.']];
        }

        return ['success' => true];
    }


    public static function delete(int $id): array
    {
        $artist = Database::fetchOne("SELECT a.id, a.user_id FROM artists a WHERE id = ?", [$id]);

        if (!$artist)
            return ['success' => false, 'message' => 'Artist not found.'];

        $musicCount = Database::fetchOne("SELECT COUNT(*) as count FROM musics WHERE artist_id = ?", [$id]);
        if ((int)$musicCount['count'] > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete artist with music(s) under their profile.'
            ];
        }

        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();
            Database::query("DELETE FROM artists WHERE id = ?", [$id]);
            Database::query("UPDATE users SET isActive = FALSE WHERE id = ?", [$artist['user_id']]);
            $pdo->commit();
        } catch (Exception $e) {
            error_log($e->__toString(), 3, LOG_FILE);
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Failed to delete artist.'];
        }

        return [
            'success' => true,
            'message' => 'User deleted successfully.'
        ];
    }

    public static function getArtistIdForUser(int $userId): ?int
    {
        $artist = Database::fetchOne("SELECT id FROM artists WHERE user_id = ?", [$userId]);
        return $artist ? (int) $artist['id'] : null;
    }

    private static function validate(array $data, ?int $excludeId = null, bool $requirePassword = false): array
    {
        $errors = [];

        if (empty($data['fname']) || !preg_match("/^[a-zA-Z\s]+$/", $data['fname']))
            $errors['fname'] = 'Invalid first name.';

        if (empty($data['lname']) || !preg_match("/^[a-zA-Z\s]+$/", $data['lname']))
            $errors['lname'] = 'Invalid last name.';

        if (empty($data['address']))
            $errors['address'] = 'Address is required.';

        if (!validateEmail($data['email']))
            $errors['email'] = 'Invalid email format';

        $emailCheckSql = "SELECT id FROM users WHERE email = ? AND isActive = TRUE";
        $emailCheckParams = [$data['email']];
        if ($excludeId !== null) {
            $emailCheckSql .= " AND id != ?";
            $emailCheckParams[] = $excludeId;
        }
        $isEmailTaken = Database::fetchOne($emailCheckSql, $emailCheckParams);
        if ($isEmailTaken)
            $errors['email'] = 'Email already taken.';

        if (empty($data['mobile']) || !preg_match("/^(97|98)\d{8}$/", $data['mobile']))
            $errors['mobile'] = "Invalid phone format";

        if (empty($data['gender']))
            $errors['gender'] = "Gender is required";

        if (!empty($data['dob'])) {
            $dobDate = new DateTime($data['dob']);
            $today = new DateTime();

            if ($dobDate > $today)
                $errors['dob'] = 'DOB cannot be future date.';
            else {
                $age = $today->diff($dobDate)->y;
                if ($age < 16)
                    $errors['dob'] = 'You must be at least 16 years to register';
            }
        } else {
            $errors['dob'] = 'DOB is required';
        }

        if (!empty($data['first_release_year'])) {
            $year = (int)$data['first_release_year'];
            $currentYear = (int)date('Y');
            if ($year < 1900 || $year > $currentYear)
                $errors['first_release_year'] = "Year must be between 1900 and $currentYear.";
        }

        if (isset($data['no_of_album_released']) && $data['no_of_album_released'] !== '' && $data['no_of_album_released'] !== null) {
            if (!is_numeric($data['no_of_album_released']) || (int)$data['no_of_album_released'] < 0)
                $errors['no_of_album_released'] = 'Number of albums must be a non-negative number.';
        }

        if (!empty($data['password'])) {
            if (($data['password'] ?? '') !== ($data['cpassword'] ?? ''))
                $errors['cpassword'] = "Passwords do not match.";

            if (
                strlen($data['password']) < 8 ||
                !preg_match('/[A-Z]/', $data['password']) ||
                !preg_match('/[a-z]/', $data['password']) ||
                !preg_match('/[0-9]/', $data['password']) ||
                !preg_match('/[^A-Za-z0-9]/', $data['password'])
            ) {
                $errors['password'] = 'Password must be at least 8 char with uppercase, lowercase, number & special character.';
            }
        } elseif ($requirePassword) {
            $errors['password'] = 'Password is required.';
        }

        return $errors;
    }
}
