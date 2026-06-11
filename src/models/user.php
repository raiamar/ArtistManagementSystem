<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../helper.php';

class UserHandler
{
    public static function list(int $page, int $perPage = 10): array{
         return paginate('users','isActive = TRUE',[],$perPage,$page);
    }

    public static function create(array $data): array
    {
        $errors = self::validate($data, null, true);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        Database::insert(
            "INSERT INTO users(first_name, last_name, email, password, phone, dob, address, gender, role) VALUES(?,?,?,?,?,?,?,?,?)",
            [$data['fname'], $data['lname'], $data['email'], $hashedPassword, $data['mobile'], $data['dob'], $data['address'], $data['gender'], $data['role']]
        );

        return ['success' => true];
    }

    public static function update(int $id, array $data): array
    {
        $errors = self::validate($data, $id, false);

        if (!empty($errors))
            return ['success' => false, 'errors' => $errors];

        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            Database::query(
                "UPDATE users SET first_name=?, last_name=?, email=?, password=?, phone=?, dob=?, address=?, gender=?, role=? WHERE id=?",
                [$data['fname'], $data['lname'], $data['email'], $hashedPassword, $data['mobile'], $data['dob'], $data['address'], $data['gender'], $data['role'], $id]
            );
        } else {
            Database::query(
                "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, dob=?, address=?, gender=?, role=? WHERE id=?",
                [$data['fname'], $data['lname'], $data['email'], $data['mobile'], $data['dob'], $data['address'], $data['gender'], $data['role'], $id]
            );
        }

        return ['success' => true];
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