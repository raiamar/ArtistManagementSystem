<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../helper.php';

class ArtistHandler
{
    public static function list(int $page, int $perPage = 10): array
    {
        $page = $page ?? max(1, (int)($_GET['p'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetchOne("SELECT COUNT(*) as total FROM artists")['total'];
        $lastPage = max(1, (int) ceil($total / $perPage));

        $data = Database::fetchAll(
            "SELECT u.id AS user_id, u.first_name, u.last_name, u.email, u.address, u.phone, u.dob, u.gender, 
            a.first_release_year, a.id AS artist_id, a.no_of_album_released, a.created_at 
            FROM artists a JOIN users u ON a.user_id = u.id WHERE u.isActive = TRUE ORDER BY a.created_at DESC LIMIT ? OFFSET ?",
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

    public static function exportCsv(): void
    {
        $artists = Database::fetchAll(
            "SELECT u.first_name AS 'first name', u.last_name AS 'last name', u.email, u.address, u.phone, 
            DATE_FORMAT(u.dob, '%Y-%m-%d') AS dob, 
            CASE u.gender
                WHEN 'm' THEN 'Male'
                WHEN 'f' THEN 'Female'
                WHEN 'o' THEN 'Other'
                ELSE 'N/A'
            END AS gender,
            CASE WHEN a.first_release_year IS NOT NULL THEN a.first_release_year ELSE 'N/A' END AS 'first release',
            COALESCE(a.no_of_album_released,0) AS 'total album released'
            FROM artists a JOIN users u ON a.user_id = u.id ORDER BY u.first_name ASC"
        );
        csvExport($artists, 'artists_export_' . date('Y-m-d_H-i-s') . '.csv');
    }

    public static function exportSampleCsv(): void
    {
        $headers = ['first name', 'last name', 'email', 'password', 'phone', 'dob', 'gender', 'address', 'first release year', 'total albums released'];
        $sample = [
            ['John', 'Doe', 'john@example.com', 'Nepal@123', '9812345678', '2000-01-15', 'm', 'Kathmandu', '2020', '3'],
        ];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="artist_import_sample.csv"');
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);
        foreach ($sample as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public static function importCsv(array $file): array
    {
        $tmpPath = $file['tmp_name'];
        $allowedMime = [
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'text/plain',
        ];

        if (!empty($file['name']) && in_array($file['type'], $allowedMime)) {
            if (is_uploaded_file($tmpPath)) {
                $csvFile = fopen($tmpPath, 'r');
                $headers = fgetcsv($csvFile);

                if (!$headers) {
                    fclose($csvFile);
                    return ['success' => false, 'message' => 'Empty or invalid CSV file.'];
                }

                $errors = [];
                $pdo = Database::getInstance();

                try {
                    $pdo->beginTransaction();
                    $rowNumber = 1;
                    $created = 0;
                    $updated = 0;

                    while (($line = fgetcsv($csvFile)) !== false) {
                        $rowNumber++;
                        $email = $line[2];

                        if (empty($email)) {
                            $errors[] = "Row $rowNumber: email is required.";
                            continue;
                        }

                        $existingUser = Database::fetchOne(
                            "SELECT u.id, a.id AS artist_id FROM users u LEFT JOIN artists a ON a.user_id = u.id WHERE u.email = ? AND u.isActive = TRUE",
                            [$email]
                        );

                        $artistData = [
                            'fname' => $line[0] ?? '',
                            'lname' => $line[1] ?? '',
                            'email' => $email,
                            'mobile' => $line[4] ?? '',
                            'dob' => $line[5],
                            'address' => $line[7] ?? '',
                            'gender' => self::normalizeGender($line[6]),
                            'password' => $line[3] ?? '',
                            'cpassword' => $line[3] ?? '',
                            'first_release_year' => $line[8],
                            'no_of_album_released' => $line[9],
                        ];

                        // echo"<pre>";
                        // print_r($artistData);
                        // die;

                        $validationErrors = self::validate(
                            $artistData,
                            $existingUser ? (int)$existingUser['id'] : null,
                            !$existingUser
                        );

                        if (!empty($validationErrors)) {
                            $errors[] = "Row $rowNumber: " . implode('; ', $validationErrors);
                            continue;
                        }


                        if ($existingUser) {
                            Database::query(
                                "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, dob=?, address=?, gender=? WHERE id=?",
                                [$artistData['fname'], $artistData['lname'], $artistData['email'], $artistData['mobile'], $artistData['dob'], $artistData['address'], $artistData['gender'], $existingUser['id']]
                            );
                            Database::query(
                                "UPDATE artists SET first_release_year=?, no_of_album_released=? WHERE user_id=?",
                                [$artistData['first_release_year'], $artistData['no_of_album_released'], $existingUser['id']]
                            );
                            $updated++;
                        } else {
                            $hashedPassword = password_hash($artistData['password'], PASSWORD_BCRYPT);
                            $userId = Database::insert(
                                "INSERT INTO users(first_name, last_name, email, password, phone, dob, address, gender, role) VALUES(?,?,?,?,?,?,?,?,?)",
                                [$artistData['fname'], $artistData['lname'], $artistData['email'], $hashedPassword, $artistData['mobile'], $artistData['dob'], $artistData['address'], $artistData['gender'], 'artist']
                            );
                            Database::insert(
                                "INSERT INTO artists(user_id, first_release_year, no_of_album_released) VALUES(?,?,?)",
                                [$userId, $artistData['first_release_year'], $artistData['no_of_album_released']]
                            );
                            $created++;
                        }
                    }

                    fclose($csvFile);

                    if (!empty($errors)) {
                        $pdo->rollBack();
                        return [
                            'success' => false,
                            'message' => 'Import failed due to validation errors. No changes were saved.',
                            'errors' => $errors,
                        ];
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    if (isset($csvFile) && is_resource($csvFile))
                        fclose($csvFile);

                    error_log($e->__toString(), 3, LOG_FILE);
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Import failed: exception'];
                }
            }
        }

        return [
            'success' => true,
            'message' => "Import completed.",
        ];
    }


    private static function normalizeGender(string $gender): string
    {
        $map = [
            'male' => 'm',
            'female' => 'f',
            'other' => 'o',
            'm' => 'm',
            'f' => 'f',
            'o' => 'o',
        ];
        return $map[strtolower(trim($gender))] ?? 'o';
    }
}
