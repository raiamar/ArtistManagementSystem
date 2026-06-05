<?php

require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Login -' . APP_NAME;
require_once __DIR__ . '/../layout/header.php'
?>


<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 p-8">
        <h4 class="text-xl font-bold text-center text-gray-800 mb-8"><?= APP_NAME ?></h4>
        <form class="max-w-sm mx-auto">
            <div class="mb-5">
                <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email</label>
                <input type="email" id="email" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="name@flowbite.com" required />
            </div>
            <div class="mb-5">
                <label for="password" class="block mb-2.5 text-sm font-medium text-heading">Password</label>
                <input type="password" id="password" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body" placeholder="••••••••" required />
            </div>

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