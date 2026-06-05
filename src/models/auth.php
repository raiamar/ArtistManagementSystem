<?php

require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../helper.php';

class Auth{

 public static function register(string $fname, string $lname, string $email, string $mobile, string $dob, string $gender, string $address,  string $password, string $cpassword, string $role = 'artist') : array
 {
    $errors = [];

    if(empty($fname) || !preg_match("/^[a-zA-Z\s]+$/", $fname))
        $errors['fname'] = 'Invalid first name.';

    if(empty($lname) || !preg_match("/^[a-zA-Z\s]+$/", $lname))
        $errors['lname'] = 'Invalid last name.';

    if(empty($address))
        $errors['address'] = 'Address is required.';

    if(!validateEmail($email))
        $errors['email'] = 'Invalid email format';

    $isEmailTaken = Database::fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if($isEmailTaken)
        $errors['email'] = 'Email already taken.';

    if($password !== $cpassword)
        $errors['cpassword'] = "Password do not match.";

    if(
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/',$password) ||
        !preg_match('/[a-z]/',$password) ||
        !preg_match('/[0-9]/',$password) ||
        !preg_match('/[^A-Za-z0-9]/',$password)
    ){
        $errors['password'] = 'Password must be at least 8 char with uppercase, lowercase, number & special character.';
    }
        
    if(empty($mobile) || !preg_match("/^(97|98)\d{8}$/", $mobile))
        $errors['mobile'] = "Invalid phone format";

    if(empty($gender))
        $errors['gender'] = "Gender is required";

    if(!empty($dob))
    {
        $dobDate = new DateTime($dob);
        $today = new DateTime();

        if($dobDate > $today)
        {
            $errors['dob'] = 'DOB cannot be future date.';
        }else{
            $age = $today->diff($dobDate)->y;

            if($age < 16)
                $errors['dob'] = 'You must  be at least 16 years to register';
        }
            
    }else{$errors['dob'] = 'DOB is required'; }

    if(!empty($errors))
        return ['success'=>false, 'errors'=>$errors];

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $adminUser = Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'super_admin'");
    $isFirstAdmin = $adminUser && (int)$adminUser['count'] === 0;

    $allowedRoles = $isFirstAdmin ? ['super_admin', 'artist_manager', 'artist'] : ['artist_manager', 'artist'];
    $role = in_array($role, $allowedRoles) ? $role : 'artist';

    Database::insert(
        "INSERT INTO users(first_name, last_name, email, password, phone, dob, address, gender, role) VALUES(?,?,?,?,?,?,?,?,?)",
        [$fname, $lname, $email, $hashedPassword, $mobile, $dob, $address, $gender, $role]
    );

    return ['success' => true];
 }

}