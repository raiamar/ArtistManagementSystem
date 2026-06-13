<?php

require_once __DIR__ . '/../src/models/auth.php';

if(isLoggedIn())
    redirect('dashboard/index.php');

$pageTitle = 'Login -' . APP_NAME;
$successMessage = $_SESSION['register_success_message'] ?? null;
if ($successMessage)
    unset($_SESSION['register_success_message']);


$errors = [];
$old = $_POST;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Token is invalid.';
    } else {
        $result = Auth::login(
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
        );

        if ($result['success']) {
             $_SESSION['login_success_message'] = "Welcome ";

            redirect('dashboard/index.php');
        } else {
            $errors = $result['errors'];
        }
    }
}

require_once __DIR__ . '/../layout/header.php'
?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-8">
        <?php if ($successMessage): ?>
            <div id="alert-3" class="flex sm:items-center p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-green-200" role="alert">
                <svg class="w-4 h-4 shrink-0 mt-0.5 md:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div class="ms-2 text-sm ">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
                <button type="button" onclick="document.getElementById('alert-3').remove();" class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-green-200 focus:ring-2 focus:ring-green-300 inline-flex h-8 w-8" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['csrf'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= h($errors['csrf']) ?>
            </div>
        <?php endif; ?>

        <h4 class="text-xl font-bold text-center text-gray-800 mb-8"><?= APP_NAME ?></h4>
        <form class="max-w-sm mx-auto" method="POST">
            <?= csrfField() ?>
            <div class="mb-5">
                <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email</label>
                <input type="email" name="email" value="<?= h($old['email'] ?? '') ?>" id="email" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="amar@gmail.com" required />
            </div>
            <div class="mb-5 relative">
                <label for="password" class="block mb-2.5 text-sm font-medium text-heading">Password</label>
                <input type="password" id="password" name="password" value="<?= h($old['password'] ?? '') ?>" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="••••••••" required />
                <button type="button" data-target="password" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 mt-7">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <?php if (!empty($errors['email'])): ?>
                <p class="text-red-600 text-sm mt-1"><?= h($errors['email']) ?></p>
            <?php endif;  ?>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="text-white bg-blue-600 box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    Submit
                </button>

                <p class="text-sm text-gray-600">
                    Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?= require_once __DIR__ . '/../layout/footer.php' ?>