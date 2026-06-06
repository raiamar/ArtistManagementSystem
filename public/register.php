<?php

require_once __DIR__ . '/../src/models/auth.php';

$errors = [];
$old = $_POST;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Token is invalid.';
    } else {
        $result = Auth::register(
            $_POST['fname'] ?? '',
            $_POST['lname'] ?? '',
            $_POST['email'] ?? '',
            $_POST['mobile'] ?? '',
            $_POST['dob'] ?? '',
            $_POST['gender'] ?? '',
            $_POST['address'] ?? '',
            $_POST['password'] ?? '',
            $_POST['cpassword'] ?? '',
            $_POST['role'] ?? 'artist'
        );

        if ($result['success']) {
            $_SESSION['register_success_message'] = "Dear {$old['fname']} {$old['lname']}, you have successfully registered. Now you can login from here.";
            header('Location: login.php');
            exit;
        } else {
            $errors = $result['errors'];
        }
    }
}

$pageTitle = 'Register -' . APP_NAME;
require_once __DIR__ . '/../layout/header.php';
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 p-8">
        <h4 class="text-xl font-bold text-center text-gray-800 mb-8"><?= APP_NAME ?></h4>

        <?php if (!empty($errors['csrf'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= h($errors['csrf']) ?>
            </div>
        <?php endif; ?>

        <?php
        $adminUser = Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'super_admin'");
        $isFirstAdmin = $adminUser && (int)$adminUser['count'] === 0;
        ?>

        <form class="w-full" method="POST">
            <?= csrfField() ?>
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label for="fname" class="mb-2 text-slate-900 font-medium text-sm inline-block">First
                        Name</label>
                    <input type="text" id="fname" name="fname" value="<?= h($old['fname'] ?? '') ?>" placeholder="Amar" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['fname'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['fname']) ?></p>
                    <?php endif;  ?>
                </div>
                <div>
                    <label for="lname" class="mb-2 text-slate-900 font-medium text-sm inline-block">Last
                        Name</label>
                    <input type="text" id="lname" name="lname" value="<?= h($old['lname'] ?? '') ?>" placeholder="Rai" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['lname'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['lname']) ?></p>
                    <?php endif;  ?>
                </div>
                <div>
                    <label for="email"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Email</label>
                    <input type="email" id="email" name="email" value="<?= h($old['email'] ?? '') ?>" placeholder="amar@gmail.com" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['email'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['email']) ?></p>
                    <?php endif;  ?>
                </div>
                <div>
                    <label for="mobile"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" value="<?= h($old['mobile'] ?? '') ?>" placeholder="+977-9876543210" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['mobile'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['mobile']) ?></p>
                    <?php endif;  ?>
                </div>


                <div>
                    <label for="dob" class="mb-2 text-slate-900 font-medium text-sm inline-block">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?= h($old['dob'] ?? '') ?>" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['dob'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['dob']) ?></p>
                    <?php endif;  ?>
                </div>

                <div>
                    <label for="gender" class="mb-2 text-slate-900 font-medium text-sm inline-block">Gender</label>
                    <select id="gender" name="gender" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                        <!-- <option value="" disabled selected></option> -->
                        <?php $selectedGender = $old['gender'] ?? 'Select gender'; ?>
                        <option value="m" <?= $selectedGender === 'm' ? 'selected' : ''  ?>>Male</option>
                        <option value="f" <?= $selectedGender === 'f' ? 'selected' : ''  ?>>Female</option>
                        <option value="o" <?= $selectedGender === 'o' ? 'selected' : ''  ?>>Other</option>
                    </select>
                    <?php if (!empty($errors['gender'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['gender']) ?></p>
                    <?php endif;  ?>
                </div>

                <div>
                    <label for="address" class="mb-2 text-slate-900 font-medium text-sm inline-block">Address</label>
                    <input type="text" id="address" name="address" value="<?= h($old['address'] ?? '') ?>" placeholder="Lalitpur, Nepal" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                    <?php if (!empty($errors['address'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['address']) ?></p>
                    <?php endif;  ?>
                </div>

                <div>
                    <label for="role" class="mb-2 text-slate-900 font-medium text-sm inline-block">Register As</label>
                    <select id="role" name="role" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                        <?php $selectedRole = $old['role'] ?? 'artist'; ?>
                         <option value="super_admin" <?= $selectedRole === 'super_admin' ? 'selected' : ''  ?>>Admin</option>
                        <!-- 
                        <?php if ($isFirstAdmin): ?>
                            <option value="super_admin" <?= $selectedRole === 'super_admin' ? 'selected' : ''  ?>>Admin</option>
                        <?php endif; ?>
                        <option value="artist_manager" <?= $selectedRole === 'artist_manager' ? 'selected' : ''  ?>>Manager</option>
                        <option value="artist" <?= $selectedRole === 'artist' ? 'selected' : ''  ?>>Artist</option> -->
                    </select>
                </div>

                <div class="relative">
                    <label for="password"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Password</label>
                    <input type="password" id="password" name="password" value="<?= h($old['password'] ?? '') ?>" placeholder="••••••••" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />

                        <button type="button" data-target="password" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 mt-7">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                    <?php if (!empty($errors['password'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['password']) ?></p>
                    <?php endif;  ?>
                </div>
                <div class="relative">
                    <label for="cpassword"
                        class="mb-2 text-slate-900 font-medium text-sm inline-block">Confirm
                        Password</label>
                    <input type="password" id="cpassword" name="cpassword" value="<?= h($old['cpassword'] ?? '') ?>" placeholder="••••••••" required
                        class="bg-neutral-secondary-medium border border-default-medium px-3 py-2.5 text-sm text-slate-900 rounded-md bg-white w-full outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
                        <button type="button" data-target="cpassword" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 mt-7">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    <?php if (!empty($errors['cpassword'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?= h($errors['cpassword']) ?></p>
                    <?php endif;  ?>
                </div>

            </div>




            <div class="flex items-center justify-between mt-4">

                <p class="text-sm text-gray-600">
                    I have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
                </p>

                <button type="submit"
                    class="py-2 px-3.5 text-sm rounded-md font-semibold cursor-pointer tracking-wide text-white border border-blue-600 bg-blue-600 hover:bg-blue-700 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    Create an account</button>
            </div>
        </form>
    </div>
</div>

<?= require_once __DIR__ . '/../layout/footer.php' ?>